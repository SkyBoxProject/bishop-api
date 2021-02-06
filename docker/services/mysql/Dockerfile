FROM mysql:8.0

EXPOSE 3306

ADD ./mysql.cnf  /etc/mysql/conf.d/custom.cnf
RUN chmod 644 /etc/mysql/conf.d/custom.cnf

#RUN mysqld --initialize

CMD ["mysqld"]
