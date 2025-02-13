FROM php:8.2-fpm

RUN docker-php-ext-install pdo pdo_mysql


RUN apt-get update

# Install useful tools
RUN apt-get -y install apt-utils nano wget dialog vim

# Install important libraries
RUN echo "\e[1;33mInstall important libraries\e[0m"
RUN apt-get -y install --fix-missing \
    apt-utils \
    build-essential \
    git \
    curl \
    openssl \
    libcurl4 \
    libcurl4-openssl-dev \
    zlib1g-dev \
    libzip-dev \
    zip \
    libbz2-dev \
    locales \
    libmcrypt-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev

RUN echo "\e[1;33mInstall important docker dependencies\e[0m"
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    openssl \
    wget \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    xfonts-75dpi \
    xfonts-base \
    fontconfig \
    libxrender1 \
    zip \
    unzip \
    nano \
    gzip \
    sudo


# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath zip mysqli \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

