FROM php:7.3.4-cli
COPY swoole-src-master.tar.gz /build/swoole.tar.gz
COPY composer.phar /usr/local/bin/composer
RUN echo deb http://mirrors.aliyun.com/debian stretch main contrib non-free > /etc/apt/sources.list && \
    echo deb http://mirrors.aliyun.com/debian stretch-proposed-updates main contrib non-free >> /etc/apt/sources.list && \
    echo deb http://mirrors.aliyun.com/debian stretch-updates main contrib non-free >> /etc/apt/sources.list && \
    echo deb-src http://mirrors.aliyun.com/debian stretch main contrib non-free >> /etc/apt/sources.list && \
    echo deb-src http://mirrors.aliyun.com/debian stretch-proposed-updates main contrib non-free >> /etc/apt/sources.list && \
    echo deb-src http://mirrors.aliyun.com/debian stretch-updates main contrib non-free >> /etc/apt/sources.list && \
    echo deb http://mirrors.aliyun.com/debian-security/ stretch/updates main non-free contrib >> /etc/apt/sources.list && \
    echo deb-src http://mirrors.aliyun.com/debian-security/ stretch/updates main non-free contrib >> /etc/apt/sources.list && \
    rm -rf /etc/apt/sources.list.d/* && \

    apt-get update && \
    apt-get install -y libssl-dev && \
    apt-get install -y libnghttp2-dev && \
    apt-get install -y libhiredis-dev && \
    apt-get install -y libzip-dev && \
    apt-get install -y git && \
    apt-get install -y zip && \
    apt-get autoclean && \
    apt-get autoremove && \

    docker-php-ext-install pdo_mysql && \
    docker-php-ext-install sockets && \
    cd /build && \
    rm -rf ./swoole-src && \
    tar zxvf ./swoole.tar.gz && \
    mv swoole-src* swoole-src && \
    cd swoole-src && \
    phpize && \
    ./configure \
        --enable-openssl  \
        --enable-http2  \
        --enable-async-redis \
        --enable-mysqlnd  \
        --enable-sockets  && \
    make clean && make && make install && \
    echo extension=swoole.so > /usr/local/etc/php/conf.d/swoole.ini && \
    rm -rf /build && \
    chmod +x /usr/local/bin/composer && \
    composer config -g repo.packagist composer https://packagist.laravel-china.org

