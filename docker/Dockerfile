FROM ubuntu:16.04

RUN apt-get update && apt-get install -y \
        apache2 php libapache2-mod-php

RUN apt-get update && apt-get install -y \
        git make g++ \
        libboost-date-time-dev libboost-program-options-dev \
        texlive texlive-lang-german

WORKDIR /etc/apache2/sites-available
COPY site.conf site.conf
RUN a2dissite 000-default.conf && \
    a2ensite site.conf

RUN chown www-data:www-data /var/www
USER www-data
WORKDIR /var/www

RUN git clone https://github.com/Kruecke/Putzplan.git

USER root
EXPOSE 80
CMD /usr/sbin/apache2ctl -D FOREGROUND
