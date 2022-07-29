<?php


namespace ZVanoZ\BaseApiServer;

use Psr\Log\LoggerInterface;

abstract class App
    implements AppInterface
{
    public const APP_NAME = 'ZVanoZ\BaseApiServer';
    public const APP_VERSION = '0.0.1';
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

    public function isApiVersionAllow(
        string|int $apiVersion
    ): bool
    {
        if (empty($apiVersion)) {
            return false;
        }
        $apiVersion = intval($apiVersion);
        $allowApiVersions = $this->app->getAllowApiVersions();
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
            $this->response = new \ZVanoZ\BaseApiServer\Response\JsonResponse([
                'success' => false,
                'message' => 'Error, during initialize of an application',
                'error' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'stack' => $e->getTrace()
                ]
            ], 500);
            $this->response->send();
            return;
        }
        try {
            $logLineContext = [
                'uri' => $this->getRequest()->getUri(),
                'origin' => $this->getRequest()->getOrigin(),
            ];
            $this->getLogger()->info('start request', $logLineContext);
            $this->action = $this->router->route();
            $this->response = $this->action->execute($this);
            $xhrHeaders = $this->getXhrHeaders();
            $this->response->getHeaders()->setFromHeaders($xhrHeaders);
            $rawContent = ob_get_contents();
            ob_end_clean();
            $this->response->send();
        } catch (\Throwable $e) {
            $this->response = new \ZVanoZ\BaseApiServer\Response\JsonResponse([
                'success' => false,
                'message' => 'Error, during initialize of an application',
                'error' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'stack' => $e->getTrace()
                ]
            ], 500);
            $this->response->send();
            return;
        }
    }

    public function init(): void
    {
        $this->initPhp();
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

    protected function createRequest(): RequestInterface
    {
        return new Request();
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

    public function exceptionHandler(\Throwable $e)
    {
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
    }

    public function errorHandler(
        $errno,
        $errstr,
        $errfile = NULL,
        $errline = 0,
        $errcontext = []
    )
    {
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

}