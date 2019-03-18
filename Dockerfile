FROM php:7.3-fpm-alpine

EXPOSE 80 443
ENTRYPOINT ["/bin/sh","/usr/src/app/run.sh"]

RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app
ENV MORGUE_ENVIRONMENT docker

RUN apk add nginx --update-cache && docker-php-ext-install pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

COPY . /usr/src/app
RUN ln -sf /usr/src/app/nginx/site.conf /etc/nginx/nginx.conf \
	&& composer update
