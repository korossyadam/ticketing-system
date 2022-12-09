#!/usr/bin/env bash

#### Import script args ####

timezone=$(echo "$1")
domain=$(echo "$2")

#### Bash helpers ####

function info {
  echo " "
  echo "--> $1"
  echo " "
}

#### Provision script ####

info "Provision-script user: `whoami`"

info "Configure locales"
echo 'hu_HU.UTF-8 UTF-8' >> /etc/locale.gen
locale-gen

info "Configure timezone"
timedatectl set-timezone $timezone

info "Update OS software"
DEBIAN_FRONTEND=noninteractive apt-get -yq update
DEBIAN_FRONTEND=noninteractive apt-get -yq -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" upgrade

info "Install additional software"
apt-get install -y vim ssh screen sudo less ntp ntpdate lsof rsync mc whois htop sysstat bzip2 tcpdump dstat dnsutils telnet
apt-get install -y apache2
apt-get install -y php7.4 php7.4-cli php7.4-common php7.4-curl php7.4-gd php7.4-intl php7.4-mbstring php7.4-pgsql php7.4-soap php7.4-xml php7.4-zip php7.4-gnupg php7.4-mail php7.4-memcached php7.4-xdebug phpunit
apt-get install -y postgresql-13

info "Edit PostgreSQL config file (pg_hba.conf)"
sed -i "s/# DO NOT DISABLE!/local all all trust\n# DO NOT DISABLE!/" /etc/postgresql/13/main/pg_hba.conf
service postgresql restart

info "Create SQL database"
echo "CREATE USER main_user WITH PASSWORD 'asdasd';" | psql -U postgres
echo "CREATE DATABASE main_database OWNER main_user ENCODING 'UTF8' LC_COLLATE = 'hu_HU.UTF-8' LC_CTYPE = 'hu_HU.UTF-8' TEMPLATE = template0;" | psql -U postgres

update-alternatives --set editor /usr/bin/vim.basic

info "Configure Apache2"
a2enmod rewrite
service apache2 restart
info "Enabling site configuration"
ln -s /var/www/html/ticketing-system-template/vagrant/apache/app.conf /etc/apache2/sites-enabled/0-$domain.conf
