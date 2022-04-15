FROM thecodingmachine/php:8.1-v4-fpm

RUN echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | sudo tee /etc/apt/sources.list.d/symfony-cli.list \
    && sudo apt update \
    && sudo apt install symfony-cli -y
