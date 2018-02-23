FROM php:7.0-fpm


RUN apt-get update

RUN apt install -y libicu-dev
RUN docker-php-ext-install -j$(nproc) intl


RUN apt-get install -y --force-yes g++ zlib1g-dev libicu-dev
RUN apt-get install -y --force-yes \
		libmysqlclient-dev  \
		libxml2-dev \
		screen \
		vim \
		nodejs npm 
		
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN docker-php-ext-install -j$(nproc) mysqli pdo pdo_mysql mbstring soap zip \
    && docker-php-ext-enable mysqli pdo pdo_mysql mbstring soap zip intl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
	    && composer --version
		
RUN rm /etc/localtime \
		    && ln -s /usr/share/zoneinfo/Europe/Moscow /etc/localtime \
		    && "date"
			
RUN pecl config-set php_ini /usr/local/etc/php
		
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
		
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
		&& echo "xdebug.default_enable = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
		&& echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "xdebug.remote_host=docker.for.mac.host.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
	    && echo "xdebug.profiler_enable_trigger=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
		&& echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
		
CMD ["php-fpm"]