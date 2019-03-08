FROM php:7.1-apache

#ARG REVISION
ENV APACHE_DOCUMENT_ROOT /app/public
WORKDIR /app

ADD . ./
RUN apt-get update
RUN apt-get -y update && apt-get install -y libmcrypt-dev git-core libpq-dev gettext-base locales libicu-dev libxslt-dev libssl1.0-dev 
RUN docker-php-ext-install pdo_pgsql pdo_mysql mcrypt intl xsl 
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer 
RUN composer install --no-dev --no-scripts 
RUN apt-get clean  && rm -rf /var/lib/apt/lists/* 
RUN echo "es_AR.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen
RUN printf "date.timezone=America/Argentina/Buenos_Aires\nupload_max_filesize = 20M\npost_max_size = 20M\n" >> /usr/local/etc/php/php.ini
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite
EXPOSE 80
ADD docker/my_entrypoint /usr/local/bin/my_entrypoint
ENTRYPOINT ["my_entrypoint"]
CMD ["/usr/local/bin/apache2-foreground"]
