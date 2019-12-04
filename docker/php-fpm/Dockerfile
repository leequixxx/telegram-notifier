FROM php:7.4.0-fpm-alpine3.10

LABEL maintainer="Misha Vorobeychikov <mail@leequixxx.space>"

RUN apk update \
    && apk upgrade \
    && apk --update add composer

ENV ACCESS_KEY=""
ENV TELEGRAM_BOT_TOKEN=""
ENV TELEGRAM_CHAT_IDS=""
ENV LOGGING_LEVEL=200

WORKDIR /app
VOLUME /app
