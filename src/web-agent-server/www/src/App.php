<?php


namespace WebAgentServer;

use ZVanoZ\BaseApiServer\{ActionInterface,
    Monolog\PdoHandler,
    Request,
    RequestInterface,
    Response,
    RouterInterface,
    TranslateHandlerInterface
};
use Monolog\Logger;
use PDO;
use Psr\Log\LoggerInterface;
use ZVanoZ\BaseApiServer\Headers;

class App
    extends \ZVanoZ\BaseApiServer\App
{
    protected array $allowedApiVersions = [1, 2, 3];

    protected function createRouter(): RouterInterface
    {
        return new Router($this);
    }

    public function createTranslateHandler(): TranslateHandlerInterface
    {
        return new TranslateHandler();
    }

    public function createLogger(): LoggerInterface
    {
        $appName = $this->getAppName();
        $result = new Logger($appName);

        $pdo = $this->getDb();
        $pdoHandler = new PdoHandler($pdo);
        $result->pushHandler($pdoHandler);

        return $result;
    }

    public function getXhrHeaders(): Headers
    {
        $result = parent::getXhrHeaders();
        $origin = $this->getRequest()->getOrigin();
        $result->set('Access-Control-Allow-Origin', $origin);
        return $result;
    }

    public function isOriginAllow(): bool
    {
        // @TODO: add check origin here
        return true;
    }

    protected function getDb(): PDO
    {
        static $result = null;
        if (is_null($result)) {
            //$dsn = 'sqlite::memory:';
            $dbPath = __DIR__ . '/../data/logs/log.sqlite';
            $dsn = 'sqlite:' . $dbPath;
            $result = new PDO(
                $dsn,
                null,
                null,
                array(
                    PDO::ATTR_PERSISTENT => true
                )
            );
        }
        return $result;
    }

}