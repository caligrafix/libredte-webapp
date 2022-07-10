FROM debian:buster

WORKDIR /opt/libredte-webapp

RUN apt-get update && apt-get -y install  \
	screen mailutils mutt git apache2 openssl php \
	php-pear php-gd mercurial curl php-curl php-imap php-pgsql \
	php-memcached php-mbstring php-soap php-zip zip php-gmp php-bcmath \
	postgresql-client ifstat dnsutils ca-certificates wget
RUN apt-get -y autoremove --purge && apt-get autoclean && apt-get clean


# Debe ser de largo 16 caracteres
ENV LIBREDTE_PASSWORD_USER=""

# Debe ser de largo 32 caracteres
ENV LIBREDTE_PASSWORD_DB=""

# Habilitar AllowOverride All para /var/www en /etc/apache2/apache2.conf
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride all/' /etc/apache2/apache2.conf

# En /etc/php/7.3/apache2/php.ini modificar las sesiones para usar Memcache
RUN sed -i 's/session.save_handler = files/session.save_handler = memcached/' /etc/php/7.3/apache2/php.ini
RUN sed -i 's/;session.save_path = "\/var\/lib\/php\/sessions"/session.save_path = "libredte-webapp-memcached:11211"/' /etc/php/7.3/apache2/php.ini

# Habilitar módulos de Apache
RUN a2enmod rewrite php7.3

# Instalar bibliotecas de PEAR
RUN pear channel-update pear.php.net && \
	pear install Mail Mail_mime Net_SMTP

# Instalar Composer
RUN wget https://getcomposer.org/composer.phar && \
	chmod +x ./composer.phar && \
	mv ./composer.phar /usr/bin/composer

# Instalar framework SowerPHP
RUN mkdir /usr/share/sowerphp && \
	git clone -b 21.10.0 https://github.com/SowerPHP/sowerphp.git /usr/share/sowerphp && \
	cd /usr/share/sowerphp && \
	composer install

# Instalar dependencias con coposer
COPY ./website/composer.* ./website/
RUN cd website && composer install

# Instalar Aplicación Web de LibreDTE
COPY ./ ./

# Cambiar el directorio raíz de Apache
RUN rm -rf /var/www/html && \
	ln -s $(pwd) /var/www/html

# Copiar archivos para configuraciones:
RUN cd website/Config && \
	cp core-dist.php core.php &&  \
    cp routes-dist.php routes.php

# Crear carpetas usadas por LibreDTE
RUN mkdir -p /home/libredte/www/htdocs/data/static/contribuyentes && \
	chmod 777 /home/libredte/www/htdocs/data/static/contribuyentes && \
	mkdir /home/libredte/www/htdocs/data/static/emision_masiva_pdf && \
	chmod 777 /home/libredte/www/htdocs/data/static/emision_masiva_pdf && \
	mkdir /home/libredte/www/htdocs/tmp && \
	chmod 777 /home/libredte/www/htdocs/tmp


ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid
ENV APACHE_SERVER_NAME localhost

EXPOSE 80
CMD ["/usr/sbin/apache2ctl", "-DFOREGROUND"]

