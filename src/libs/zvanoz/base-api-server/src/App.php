<?php


namespace ZVanoZ\BaseApiServer;


use ZVanoZ\BaseApiServer\Headers;

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

    function checkApiVersion(): bool
    {
        // TODO: Implement checkApiVersion() method.
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
            $this->action = $this->router->route();
            $this->response = $this->action->execute($this);
        } catch (\Throwable $e) {
            $this->response = new \ZVanoZ\BaseApiServer\Response\JsonResponse([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'stack' => $e->getTrace()
                ]
            ]);
        }
        $xhrHeaders = $this->getXhrHeaders();
        $this->response->getHeaders()->setFromHeaders($xhrHeaders);
        $rawContent = ob_get_contents();
        ob_end_clean();
        $this->response->send();
    }

    public function init(): void
    {
        $this->setPhpHandlers();
        $this->createRequest();
        $this->createTranslateHandler();
        $this->createRouter();
    }

    protected function setPhpHandlers()
    {
        set_exception_handler([&$this, 'exceptionHandler']);
        set_error_handler([$this, 'errorHandler'], E_ALL);
        register_shutdown_function([$this, 'shutdownFunction']);
    }

    protected function createRequest()
    {
        $this->request = new Request();
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    abstract protected function createTranslateHandler(): void;

    abstract protected function createRouter(): void;

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