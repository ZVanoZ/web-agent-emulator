<?php


namespace WebAgentServer;

class TranslateHandler
    extends \ZVanoZ\BaseApiServer\TranslateHandler
{
    protected string $targetLang='en';
    protected array $allowLangs = [
        'en',
        'uk'
    ];
    protected array $translates = [
        'http-400' => [
            'en' => 'Bad Request',
            'uk' => 'Помилка у запиті.'
        ],
        'http-403' => [
            'en' => 'Forbidden',
            'uk' => 'У доступі відмовлено'
        ],
        'http-404' => [
            'en' => 'Page not found',
            'uk' => 'Сторінку не знайдено'
        ],
        'Origin not allowed' => [
            'uk' => 'Забороненв обробка запитів з вашого домена'
        ],
        'Image is empty' => [
            'uk' => 'Зображення не існує'
        ],
        'Api version not sipported' => [
            'uk' => 'Версія API не підтримується'
        ]
    ];

}