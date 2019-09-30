FROM php:7.2-alpine
WORKDIR '/systemcheck'
RUN mkdir /archive
RUN apk update && apk add --no-cache bash curl git gnupg patchutils wget unzip zip
RUN echo "[global]" > /usr/local/etc/php/conf.d/php.ini
RUN echo "memory_limit=-1" >> /usr/local/etc/php/conf.d/php.ini
RUN echo "phar.readonly=Off" >> /usr/local/etc/php/conf.d/php.ini
RUN echo "log_errors=On" >> /usr/local/etc/php/conf.d/php.ini
RUN echo "error_reporting=E_ALL" >> /usr/local/etc/php/conf.d/php.ini
RUN echo "display_errors=1" >> /usr/local/etc/php/conf.d/php.ini
RUN echo "error_log=/dev/stderr" >> /usr/local/etc/php/conf.d/php.ini
RUN echo "date.timezone=Europe/Berlin" >> /usr/local/etc/php/conf.d/php.ini
RUN cd /root \
    && wget https://composer.github.io/installer.sig -O - -q | tr -d '\n' > installer.sig \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === file_get_contents('installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php'); unlink('installer.sig');"
RUN chmod a+x /root/composer.phar && ln -s /root/composer.phar /usr/local/bin/composer
#ENTRYPOINT ["bash"]