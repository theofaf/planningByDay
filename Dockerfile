FROM alpine
COPY ./ ./
RUN apk add --no-cache bash curl php-phar php-mbstring php-openssl php-ctype php-iconv php-xml php-tokenizer php-session php-dom php-curl php-pdo php-pdo_mysql
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer update
RUN composer dump-autoload
EXPOSE 8000
CMD ["sh", "start.sh"]