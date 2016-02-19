FROM php:7.0-apache
MAINTAINER Sylvain PRAS <sylvain.pras@jetpulp.fr>


RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
        libcurl4-gnutls-dev git zlib1g-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        jpegoptim \
        msmtp \
    && docker-php-ext-install iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-install mysqli curl zip pdo_mysql gd mbstring opcache

#install redis
#RUN yes | pecl install redis \
#    && echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

# install xdebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

# install blackfire
RUN curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/56 \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp \
    && mv /tmp/blackfire-*.so `php -r "echo ini_get('extension_dir');"`/blackfire.so \
    && echo "extension=blackfire.so\nblackfire.agent_socket=\${BLACKFIRE_PORT}" > $PHP_INI_DIR/conf.d/blackfire.ini

# copy the msmtp config file in the container and set his owner and permissions as the www-data will use it properly
# msmtp will send email through mailhog container
COPY msmtprc /usr/local/etc/msmtprc
RUN chown www-data.www-data /usr/local/etc/msmtprc \
    && chmod 600 /usr/local/etc/msmtprc

COPY apache2-foreground /usr/local/bin/
RUN chmod ug+x /usr/local/bin/apache2-foreground

EXPOSE 80

