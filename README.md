# web-agent-emulator
Эмулятор API для работы с WebAgent для получения фото с камеры.

## Запуск

```bash
git clone https://github.com/ZVanoZ/web-agent-emulator.git
cd web-agent-emulator
docker-compose up
```
Открываем в браузере две вкладки
1. http://localhost:8022/phpinfo.php
Это эмулятор серверной части. Смотрим настройки PHP.
2. http://localhost:8080/
Это эмулятор клиентской части.
Меняем оции на форме, жмем кнопку "Get photo" и смотрим результат в консоли браузера.
Смотрим какие запросы выполняются, какие заголовки отправляются и принимаются.
