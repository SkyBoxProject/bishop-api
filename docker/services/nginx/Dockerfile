FROM nginx:1.19.3

RUN apt-get update

COPY nginx.conf /etc/nginx

VOLUME ["/var/www", "/etc/nginx/conf.d", "/var/log/nginx"]

WORKDIR /var/www

CMD ["/usr/sbin/nginx"]
