FROM nginx:stable-alpine

RUN addgroup -g 1000 mythicalframework && adduser -G mythicalframework -g mythicalframework -s /bin/sh -D mythicalframework

ADD ./docker/nginx/nginx.conf /etc/nginx/
ADD ./docker/nginx/default.conf /etc/nginx/conf.d/

RUN mkdir -p /var/www/html

RUN chown mythicalframework:mythicalframework /var/www/html