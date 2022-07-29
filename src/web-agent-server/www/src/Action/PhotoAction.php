<?php

namespace WebAgentServer\Action;

use GdImage;
use ZVanoZ\BaseApiServer\Action\Http404Action;
use ZVanoZ\BaseApiServer\AppInterface;
use ZVanoZ\BaseApiServer\Response\JsonResponse;
use ZVanoZ\BaseApiServer\ResponseInterface;

class PhotoAction
    extends \ZVanoZ\BaseApiServer\Action
{
    public function execute(
        AppInterface $app
    ): ResponseInterface
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
        $text = $app->getTranslateHandler()
            ->translateByArrayCopy('Generated at: ', [
                'uk' => 'Створено: ',
            ]);
        imagestring($gdImage, 5, 5, 5, $text, $textColor);
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
            $result = (new Http404Action())
                ->setTranslateMessageKey('Image is empty')
                ->execute($app);
            return $result;
        }
        $img = base64_encode($img);
        $result = new JsonResponse([
            'success' => true,
            'result' => [
                'data' => $img,
                'mimetype' => 'image/jpeg',
                'encoding' => 'base64'
            ]
        ]);
        return $result;
    }
}