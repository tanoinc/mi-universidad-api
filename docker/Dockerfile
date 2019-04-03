FROM php:7.1-apache

ARG REVISION
ENV APACHE_DOCUMENT_ROOT /app/public
WORKDIR /app

ADD . ./
RUN apt-get -y update && apt-get install -y libmcrypt-dev git-core libpq-dev zip unzip gettext-base locales libicu-dev libxslt-dev libssl1.0-dev &&\
docker-php-ext-install pdo_pgsql pdo_mysql mcrypt intl xsl &&\
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer &&\
composer install --no-dev --no-scripts &&\
composer clear-cache &&\
apt-get clean  && rm -rf /var/lib/apt/lists/* &&\
echo "es_AR.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen &&\
printf "date.timezone=America/Argentina/Buenos_Aires\nupload_max_filesize = 20M\npost_max_size = 20M\n" >> /usr/local/etc/php/php.ini &&\
sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf &&\
sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf &&\
a2enmod rewrite &&\
echo $REVISION > REVISION
EXPOSE 80
ADD docker/my_entrypoint /usr/local/bin/my_entrypoint
ENTRYPOINT ["my_entrypoint"]
CMD ["/usr/local/bin/apache2-foreground"]
