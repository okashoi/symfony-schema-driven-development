FROM php:8.5.1-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && \
	apt-get install --no-install-recommends -y \
		acl \
		file \
		gettext \
		git \
		libicu-dev \
		libpq-dev \
		libzip-dev \
		unzip && \
	docker-php-ext-install \
		intl \
		pdo \
		pdo_pgsql \
		zip && \
  pecl install apcu && \
  docker-php-ext-enable apcu && \
	rm -rf /var/lib/apt/lists/* && \
	apt-get clean

COPY --from=composer:2.9.3 /usr/bin/composer /usr/bin/composer

COPY ./etc/php/php.ini /usr/local/etc/php/php.ini

COPY ./etc/apache2/sites-available/my-issue-tracker.conf /etc/apache2/sites-available/my-issue-tracker.conf
RUN a2enmod rewrite && \
	a2dissite 000-default && \
	a2ensite my-issue-tracker

WORKDIR /var/www/html
