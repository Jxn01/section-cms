# Section CMS — local development image (PHP + Apache)
FROM php:8.3-apache

# PDO MySQL driver + mod_rewrite for clean URLs / .htaccess
RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
