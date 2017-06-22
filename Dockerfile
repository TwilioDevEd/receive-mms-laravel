FROM php:5.6-cli

RUN apt-get update && apt-get install -y \
    git \
    openssl \
    zip \
    unzip \
    zlib1g-dev \
    inotify-tools \
    && apt-get clean

RUN docker-php-ext-install pdo mbstring zip
ENV USERNAME dev

RUN groupadd --gid 1000 $USERNAME \
  && useradd --uid 1000 --gid $USERNAME --shell /bin/bash --create-home $USERNAME

ENV HOME /home/$USERNAME

# Install ngrok (latest official stable from https://ngrok.com/download).
RUN curl -o ./ngrok.zip https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-amd64.zip && \
    set -x \
    && unzip -o ./ngrok.zip -d /bin \
    && rm -f ./ngrok.zip

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -o /usr/bin/phpunit-watch \
    https://gist.githubusercontent.com/ngyuki/7786721/raw/1ca6a93a95d868218bac1fc92321b2911fd13e44/phpunit-watch && \
    chmod +x /usr/bin/phpunit-watch

RUN chown -R $USERNAME $HOME

USER $USERNAME

WORKDIR $HOME

RUN composer global require "laravel/installer" "friendsofphp/php-cs-fixer"


USER root

ENV PATH="$HOME/.composer/vendor/bin:$HOME/src/vendor/bin:${PATH}"

COPY . ./src

WORKDIR $HOME/src

RUN composer install

RUN touch ./database/database.sqlite && \
    php artisan migrate --force

RUN cp .env-example .env

RUN phpunit

ENV PORT 8000

EXPOSE $PORT

CMD ./start.sh
