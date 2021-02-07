FROM php:7.4-fpm

ARG USER_ID
ARG GROUP_ID
ARG XDEBUG_ON=true
ARG XDEBUG_MODE="debug"
ARG XDEBUG_PORT=9003
ARG XDEBUG_IDEKEY="PHPSTORM"
ARG XDEBUG_REMOTE_HOST=192.168.201.65

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" && \
    apt-get update && apt-get install -y \
    libmagickwand-dev \
    git \
	libssl-dev \
	libpq-dev \
	libonig-dev \
	libzip-dev \
	nano \
	procps \
	unzip \
	default-mysql-client

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions imagick

RUN docker-php-ext-install opcache && \
    docker-php-ext-install zip && \
    docker-php-ext-install bz2 && \
    docker-php-ext-install calendar && \
    docker-php-ext-install gd && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install soap && \
    docker-php-ext-install sockets && \
    docker-php-ext-install exif && \
    docker-php-ext-install sysvsem && \
    docker-php-ext-install gettext

RUN if [ ${XDEBUG_ON} = true ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.mode=${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_port=${XDEBUG_PORT}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.log=/var/www/html/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.client_host=${XDEBUG_REMOTE_HOST}" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.idekey=${XDEBUG_IDEKEY}" >> /usr/local/etc/php/conf.d/xdebug.ini \
;fi

# Clear package lists
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN echo log_errors=On > /usr/local/etc/php/php.ini

VOLUME ["/var/www"]

WORKDIR /var/www/public

# Permissions
RUN chown -R root:www-data /var/www/public
RUN chmod u+rwx,g+rx,o+rx /var/www/public
RUN find /var/www/public -type d -exec chmod u+rwx,g+rx,o+rx {} +
RUN find /var/www/public -type f -exec chmod u+rw,g+rw,o+r {} +

EXPOSE 9000

CMD ["php-fpm"]
