<?php


namespace ZVanoZ\BaseApiServer;

use PHPUnit\Event\Code\Throwable;
use Psr\Log\LoggerInterface;
use ZVanoZ\BaseApiServer\Monolog\Context\ErrorContext;
use ZVanoZ\BaseApiServer\Monolog\Context\JournalContext;

abstract class App
    implements AppInterface
{
    public const APP_NAME = 'ZVanoZ\BaseApiServer';
    public const APP_VERSION = '0.0.1';
    protected string $traceId;
    protected array $allowedApiVersions;
    protected RouterInterface $router;
    protected RequestInterface $request;
    protected ResponseInterface $response;
    protected ActionInterface $action;
    protected TranslateHandlerInterface $translateHandler;
    protected ?LoggerInterface $logger;

    public function getAppName(): string
    {
        return static::APP_NAME;
    }

    public function getAppVersion(): string
    {
        return static::APP_VERSION;
    }

    public function setOptions(Options $options): self
    {
        if ($options->getRouter() instanceof RouterInterface) {
            $this->router = $options->getRouter();
        }
        return $this;
    }

    public function getAllowApiVersions(): array
    {
        return $this->allowedApiVersions;
    }

    public function getTranslateHandler(): TranslateHandlerInterface
    {
        return $this->translateHandler;
    }

    /**
     * @param {string|int|null} $apiVersion
     * @return bool
     * @see AppInterface::isApiVersionAllow()
     */
    public function isApiVersionAllow(
        $apiVersion
    ): bool
    {
        if (empty($apiVersion)) {
            return false;
        }
        $apiVersion = intval($apiVersion);
        $allowApiVersions = $this->getAllowApiVersions();
        if (!in_array($apiVersion, $allowApiVersions)) {
            return false;
        }
        return true;
    }

    public function run(
        ?Options $options = null
    ): void
    {
        ob_start();
        if ($options instanceof Options) {
            $this->setOptions($options);
        }
        try {
            $this->init();
        } catch (\Throwable $e) {
            $this->sendApplicationError($e);
            return;
        }
        try {
            $logLineContext = new JournalContext();
            $logLineContext->traceId = $this->getTraceId();

            $tmpLogContext = clone $logLineContext;
            $tmpLogContext->message = [
                'method' => $this->getRequest()->getMethod(),
                'uri' => $this->getRequest()->getUri(),
                'origin' => $this->getRequest()->getOrigin(),
                'headers' => $this->getRequest()->getHeaders()->getListRef()
            ];
            $tmpLogContext = $tmpLogContext->toArray();
            $this->getLogger()->info('Request', $tmpLogContext);
            unset($tmpLogContext);

            $this->action = $this->router->route();
            $this->response = $this->action->execute($this);
            $xhrHeaders = $this->getXhrHeaders();
            $this->response->getHeaders()->setFromHeaders($xhrHeaders);
            $rawContent = ob_get_contents();
            if (ob_get_length()) {
                ob_end_clean();
            }
            $this->response->send();

            $tmpLogContext = clone $logLineContext;
            $tmpLogContext->message = [
                'code' => $this->response->getHttpCode(),
                'headers' => $this->response->getHeaders()->getListRef(),
                'body' => $this->response->getBody()
            ];
            $tmpLogContext = $tmpLogContext->toArray();
            $this->getLogger()->info('Response', $tmpLogContext);
            unset($tmpLogContext);
        } catch (\Throwable $e) {
            $this->sendApplicationError($e);
            return;
        }
    }

    public function init(): void
    {
        $this->initPhp();
        $this->traceId = $this->createTraceId();
        $this->logger = $this->createLogger();
        $this->request = $this->createRequest();
        $this->translateHandler = $this->createTranslateHandler();
        $this->router = $this->createRouter();
    }

    protected function initPhp()
    {
        set_exception_handler([&$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler'], E_ALL);
        register_shutdown_function([$this, 'shutdownFunction']);
    }

    public function getTraceId(): ?string
    {
        return $this->traceId;
    }

    protected function createTraceId(): string
    {
        $result = uniqid();
        return $result;
    }

    protected function createRequest(): RequestInterface
    {
        $result = new Request();
        $result->getHeaders()->appendFromRawRequest();
        return $result;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    abstract protected function createLogger(): LoggerInterface;

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    abstract protected function createTranslateHandler(): TranslateHandlerInterface;

    abstract protected function createRouter(): RouterInterface;

    public function getXhrHeaders(): Headers
    {
        $result = (new Headers())
            ->set('Access-Control-Allow-Origin', '*')
            ->set('Access-Control-Allow-Method', '*')
            ->set('Access-Control-Allow-Headers', '*');
        return $result;
    }

    /**
     * Метод предназначен для переопроеделения потолмками класса
     * @return bool
     */
    public function isOriginAllow(): bool
    {
        return true;
    }

    public function exceptionHandler(
        \Throwable $e
    )
    {
        try {
            $stack = $e->getTrace();
            //$stack = json_encode($stack, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $errCode = $e->getCode();
            $errMessage = $e->getMessage();
            $errFile = $e->getFile();
            $errLine = $e->getLine();
            $errStr = $e->getMessage();
            $this->getLogger()->error(
                $errMessage
            );
        } catch (\Throwable $e) {
        }
        $this->sendApplicationError($e);
    }

    public function errorHandler(
        $errno,
        $errstr,
        $errfile = NULL,
        $errline = 0,
        $errcontext = []
    )
    {
        $data = [
            'err_no' => $errno,
            'err_message' => $errstr,
            'err_file' => $errfile,
            'err_line' => $errline,
            'err_context' => $errcontext
        ];
        //var_dump($data);
        $e = (new ModuleError(__METHOD__))->addContext('data', $data);
        $this->sendApplicationError($e);
    }

    public function shutdownFunction()
    {
        $error = error_get_last();
        if (!is_array($error)) {
            return;
        }
        try {
            $errType = NULL;
            $errCode = NULL;
            $errMessage = NULL;
            $errFile = NULL;
            $errLine = NULL;
            $errContext = NULL;
            if (array_key_exists('type', $error)) {
                $errType = @$error['type'];
                $errFile = @$error['file'];
                $errLine = @$error['line'];
                $errMessage = @$error['message'];
            } elseif (array_key_exists('ERR_NO', $error)) {
                $errCode = @$error['ERR_NO'];
                $errMessage = @$error['ERR_STR'];
                $errFile = @$error['ERR_FILE'];
                $errLine = @$error['ERR_LINE'];
            }
        } catch (\Throwable $err) {
        }
    }

    protected function sendApplicationError(
        Throwable|\Error|\Exception $e
    )
    {
        static $isDone=false;
        if($isDone){
           return;
        }
        $isDone = true;
        $rawContent = ob_get_contents();
        if (ob_get_length()) {
            ob_end_clean();
        }
        $data = [
            'success' => false,
            'message' => 'Application Error',
            'error' => [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'stack' => $e->getTrace()
            ],
            'rawContent' => $rawContent
        ];
        if ($this->getLogger() instanceof LoggerInterface) {
            try {
                $context = new ErrorContext();
                $context->traceId = $this->getTraceId();
                $context->errMessage = $e->getMessage();
                $context->errCode = $e->getCode();
                $context->errLine = $e->getLine();
                $context->errFile = $e->getFile();
                $context->context = [
                    'stack' => $e->getTrace(),
                    'prevError' => $e->getPrevious(),
                ];
                $this->getLogger()->error(__METHOD__, $context->toArray());
            } catch (\Throwable $e) {
                $data['logError'] = $e;
            }
        }
        try {
            $this->response = new \ZVanoZ\BaseApiServer\Response\JsonResponse($data, 500);
            $this->response->getHeaders()->setFromHeaders($this->getXhrHeaders());
            $this->response->send();
        } catch (\Throwable $e) {
            var_dump($e);
        }
        die();
    }
}