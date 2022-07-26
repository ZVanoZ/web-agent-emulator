<?php


namespace WebAgentServer;


class App
{
    const API_VERSION = 3;
    const API_VERSION_ALLOW = [1, 2, 3];
    const DEFAULT_LANG = 'en';
    const ALLOW_LANG = ['en', 'uk'];

    protected string $url;
    protected string $method;
    protected string $origin;

    public function run()
    {
        $this->url = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->origin = @$_SERVER['HTTP_ORIGIN'];

        if (!$this->checkOrigin()) {
            $this->sendJson([
                'success' => false,
                'message' => $this->translate([
                    'en' => 'Origin not allowed',
                    'uk' => 'Забороненв обробка запитів з вашого домена'
                ]),
            ], 403);
            return null;
        }
        $this->setXhrHeaders();
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return null;
        }
        if (!$this->checkApiVersion()) {
            $this->sendJson([
                'success' => false,
                'message' => $this->translate([
                    'en' => 'Api version not sipported',
                    'uk' => 'Версія API не підтримується'
                ]),
                'allowVersions' => static::API_VERSION_ALLOW
            ], 400);
            return null;
        }
        if ($this->method === 'GET') {
            if (strtolower($this->url) === '/photo') {
                $this->actionPhoto();
                return null;
            }
        }
        $this->action404();
    }

    protected function setXhrHeaders()
    {
        //header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Origin: ' . $this->origin);
        header('Access-Control-Allow-Method: OPTIONS, GET, POST');
        header('Access-Control-Allow-Headers: *');
    }

    protected function checkApiVersion(): bool
    {
        $apiVersion = $this->getHeader('X-API-VERSION');
        if (empty($apiVersion)) {
            return false;
        }
        $apiVersion = intval($apiVersion);
        if (!in_array($apiVersion, static::API_VERSION_ALLOW)) {
            return false;
        }
        return true;
    }

    protected function checkOrigin(): bool
    {
        // @TODO: add check origin here
        return true;
    }

    protected function actionPhoto(): void
    {
        $imgWidth = 320;
        $imgHeight = 240;
        $imgFormat = 'jpeg';
        /**
         * @var GdImage $gdImage
         */
        $gdImage = imagecreate($imgWidth, $imgHeight);
        $backgroundColor = imagecolorallocate($gdImage, 146, 133, 133);
        $textColor = imagecolorallocate($gdImage, 0, 0, 255);
        imagestring($gdImage, 5, 5, 5,
            $this->translate([
                'en' => 'Generated at: ',
                'uk' => 'Створено: ',
            ]), $textColor
        );
        imagestring($gdImage, 5, 5, 25, (new \DateTime())->format('Y-n-d h:i:s'), $textColor);

        //imagepng($handler);
        @ob_clean();
        @ob_start();
        if ($imgFormat == 'jpeg') {
            imagejpeg($gdImage);
        } elseif ($imgFormat == 'png') {
            imagepng($gdImage);
        }
        imagedestroy($gdImage);
        $img = ob_get_contents();
        @ob_clean();
        if (empty($img)) {
            $this->sendJson([
                'success' => false,
                'message' => $this->translate([
                    'en' => 'Image is empty',
                    'uk' => 'Зображення не існує',
                ])
            ], 400);
        }
        $img = base64_encode($img);
        $this->sendJson([
            'success' => true,
            'result' => [
                'data' => $img,
                'mimetype' => 'image/jpeg',
                'encoding' => 'base64' //
            ]
        ], 200);
    }

    protected function action404(): void
    {
        $this->sendJson([
            'success' => false,
            'message' => $this->translate([
                'en' => 'Page not found',
                'uk' => 'Сторінку не знайдено'
            ])
        ], 404);
    }

    protected function sendJson(
        array $data,
        ?int $code = 200
    ): bool
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        return true;
    }

    protected function translate(array $values): string
    {
        $lang = $this->getHeader('Accept-Language');
        if (!in_array($lang, static::ALLOW_LANG)) {
            $lang = static::DEFAULT_LANG;
        }
        $result = @$values[$lang];
        if (!is_string($result)) {
            $result = '';
        }
        return $result;
    }

    protected function getHeader(string $searchName, $defaultValue = null)
    {
        $searchName = strtolower($searchName);
        $headers = getallheaders();
        foreach ($headers as $headerName => $headerValue) {
            if ($searchName === strtolower($headerName)) {
                return $headerValue;
            }
        }
        return $defaultValue;
    }
}