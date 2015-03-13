#!/bin/bash
PWD=`pwd`
STDIR="$(dirname "$PWD")"
CONFIGDIR="dev"

LOOP=CONTINUE
until [ $LOOP = END ]; do
    read -p " - Please specify environment [loc][dev][stage][prod] : " env
    case $env in
        "LOC" | "loc") echo " -- Local environment: setting Local server configurations."
            CONFIGDIR="local";
            echo "Updating /etc/hosts for local environment";
            sudo cp /etc/hosts /etc/hosts.original
            echo -e "127.0.0.1\localhost.ewms.com" >> /etc/hosts;
            LOOP=END;;
        "DEV" | "dev") echo " -- Development environment: setting development server configurations."
            CONFIGDIR="dev";
            LOOP=END;;
        "STAGE" | "stage") echo " -- Staging environment: setting staging server configurations."
            CONFIGDIR="stage";
            LOOP=END;;
        "PROD" | "prod") echo " -- Production environment: setting production server configurations."
            CONFIGDIR="prod";
            LOOP=END;;
        * ) echo " -- Invalid option."
            LOOP=CONTINUE;;
    esac
done

sudo chmod -R a+rX $STDIR
sudo chmod a+rx ~

#cache sudo credentials
[ "$UID" -eq 0 ] || exec sudo bash "$0" "$@"
sudo chmod -R a+rX $STDIR
sudo chmod a+rx ~

echo "Updating yum...";
sudo yum -y update

echo "*** Installing System Requirements ***";

echo "Installing Dependencies (i.e. curl, memcached, postfix, etc..) ";
sudo yum  -y install curl postfix

echo "Installing MySQL";
sudo yum install mysql mysql-server
echo "Make MySQL start automatically";
chkconfig --levels 235 mysqld on
echo "Starting MySQL";
sudo /etc/init.d/mysqld start
echo "Set password for the MySQL root account"
sudo /usr/bin/mysql_secure_installation

echo "Installing Apache";
sudo yum -y install httpd httpd-manual mod_ssl
echo "Make MySQL start automatically";
chkconfig --levels 235 httpd on
echo "Starting MySQL";
sudo /etc/init.d/httpd start

echo "Installing PHP";
sudo yum -y install php php-pear gcc
sudo yum -y install php-cli php-pdo

echo "Setup complete.";
echo "Done.";