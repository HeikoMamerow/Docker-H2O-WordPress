# Load latest phpfpm image
FROM php:fpm

# Add an enable mysql extension to php7
# Source: https://github.com/docker-library/php/issues/391#issuecomment-346590029
RUN apt-get update && docker-php-ext-install mysqli && docker-php-ext-enable mysqli