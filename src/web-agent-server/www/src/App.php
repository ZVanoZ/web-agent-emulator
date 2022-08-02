<?php


namespace WebAgentServer;

use ZVanoZ\BaseApiServer\{
    Monolog\Handler\Pdo\Sqlite3Handler,
    RouterInterface,
    TranslateHandlerInterface
};
use Monolog\Formatter\JsonFormatter;
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
        $pdoHandler = new Sqlite3Handler($pdo);
        $pdoHandler->setFormatter(new JsonFormatter());
        $result->pushHandler($pdoHandler);

        return $result;
    }

    public function getXhrHeaders(): Headers
    {
        $result = parent::getXhrHeaders();
        $origin = $this->getRequest()->getOrigin();
        if($this->isOriginAllow()){
            $result->set('Access-Control-Allow-Origin', $origin);
        } else {
            $result->remove('Access-Control-Allow-Origin');
        }
        return $result;
    }

    public function isOriginAllow(): bool
    {
        if ('OPTIONS' === $this->getRequest()->getMethod() ){
            return true;
        }
        $debugHeaderValue = $this->getRequest()->getHeaders()->get('X-DEBUG-IS-ALLOW-ORIGIN');
        if($debugHeaderValue === 'true'){
            return true;
        }
        return false;
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
                    PDO::ATTR_PERSISTENT => false
                )
            );
        }
        return $result;
    }

}