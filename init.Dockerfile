# This is the Dockerfile for the init container (for Kubernetes)
# that initializes the database if needed

FROM debian:buster

WORKDIR /opt/libredte-webapp-init

RUN apt-get update && apt-get -y install git postgresql-client
RUN apt-get -y autoremove --purge && apt-get autoclean && apt-get clean

# Download framework SowerPHP (needed for database init scripts)
RUN mkdir /usr/share/sowerphp && \
	git clone -b 21.10.0 https://github.com/SowerPHP/sowerphp.git /usr/share/sowerphp

# Copy database init files
COPY ./website/Module/ ./website/Module/
COPY ./init-db.sh ./

# Init database when container is executed
CMD ["./init-db.sh"]
