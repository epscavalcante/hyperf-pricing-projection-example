FROM hyperf/hyperf:8.4-alpine-v3.21-swoole-slim

ARG user=application
ARG uid=1000

# Cria usuário customizado
RUN adduser -D -u $uid $user \
    && addgroup $user www-data \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

# Instala o Composer
RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

WORKDIR /var/www

COPY ../.. /var/www

RUN chown -R $user:$user /var/www

USER $user

EXPOSE 9501

CMD [ "tail", "-f", "/dev/null" ]
