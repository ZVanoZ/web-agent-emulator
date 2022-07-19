FROM php:8.1.0-apache

#------------------------------------------------------------------------------
# subversion - для "php-composer"
# git - для "php-composer"
# zip - ???
RUN apt update && \
    DEBIAN_FRONTEND=noninteractive apt install -y \
        subversion \
        git \
        zip

# Install PHP composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Расширение XDebug
RUN pecl install xdebug-3.1.1 \
    && ln -svf /app/configs/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

#------------------------------------------------------------------------------
#-- Копирование дополнительных файлов и линковка

WORKDIR '/app'

COPY volume/run.sh /app/run.sh
COPY volume/configs /app/configs
COPY volume/scripts  /app/scripts
COPY volume/www /app/www
RUN rm -rfv /var/www \
    && ln -svfn /app/www /var/www

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