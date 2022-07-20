FROM php:8.1.0-apache

#------------------------------------------------------------------------------
# subversion - для "php-composer"
# git - для "php-composer"
# zip - без этой библиотеки обновления библиотек через composer виснет и прерывается с ошибкой через 300 секунд
# libpng-dev - для установки PHP-расширения gd
# zlib1g-dev - для установки PHP-расширения gd
# libzip-dev - для установки PHP-расширения zip
# libjpeg-turbo-dev либо libjpeg62-turbo-dev - для поддержки jpeg в gd
RUN apt update && \
    DEBIAN_FRONTEND=noninteractive apt install -y \
        subversion \
        git \
        zip \
        libzip-dev \
        libpng-dev \
        zlib1g-dev

## Install PHP composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#
## Расширение XDebug
#RUN pecl install xdebug-3.1.1 \
#    && ln -svf /app/configs/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# gd - для генерации картинок
# zip - ?для генерации jpeg
#RUN  docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
#    && docker-php-ext-install gd zip

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    gd \
    xdebug-^3.1 \
    @composer-2

#------------------------------------------------------------------------------
#-- Копирование дополнительных файлов и линковка

WORKDIR '/app'

COPY volume/run.sh /app/run.sh
COPY volume/configs /app/configs
COPY volume/scripts  /app/scripts
COPY volume/www /app/www
RUN rm -rfv /var/www \
    && ln -svfn /app/www /var/www
RUN chown www-data:www-data /app
RUN ln -svf /app/configs/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

CMD ["bash", "/app/run.sh"]

RUN a2enmod rewrite

#------------------------------------------------------------------------------
# Выводим в консоль информацию об сгенерированном окружении
#----

RUN composer -V
RUN php -v \
RUN php -m \
RUN ls -l /app

#------------------------------------------------------------------------------