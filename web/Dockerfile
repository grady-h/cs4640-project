FROM php:8.1-apache

RUN echo "Setting up PHP"

RUN rm -rf /var/lib/apt/lists/* \
    && apt-get clean \
    && apt-get update -o Acquire::CompressionTypes::Order::=gz \
    && apt-get install -y --no-install-recommends \
       libpq-dev git libzip-dev libcgi-pm-perl xsltproc \
    && rm -rf /var/lib/apt/lists/*


RUN apt-get update && apt-get install -y libpq-dev git libzip-dev libcgi-pm-perl xsltproc
RUN docker-php-ext-install pgsql pdo pdo_pgsql zip 

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./php-ini/php.prod.ini /usr/local/etc/php/php.ini

RUN a2enmod cgi
RUN service apache2 restart
