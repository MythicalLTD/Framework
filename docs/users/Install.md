# Installation

This documentation works only for the official framework and some fork's may modify
this steps so always make sure you are using the documentation the developer told you to use and not our's because the dev may added something new or modified the commands in a way that those docs will be wrong!

## Hardware Requirements

| Component        | Supported | Minimum   | Notes                         |
| ---------------- | --------- | --------- | ----------------------------- |
| CPU (Cores)      | ✅        | 2         |                               |
| RAM (GB)         | ✅        | 4         |                               |
| Storage          | ✅        | 12GB      | SSD recommended               |
| Network          | ✅        | 100(MB/s) | Internet connection needed    |
| Operating System | ✅        | N/A       | [Linux](#system-requirements) |
| GPU              | ❌        | N/A       | No reason for one             |

Those are just recommended specs but this should run everywhere where a php server can run!

## System Requirements

| Operating System       | Version | Supported | Notes                                                    |
| ---------------------- | ------- | --------- | -------------------------------------------------------- |
| [Ubuntu](OS/ubuntu.md) | >=20.04 | ✅        | This will require you to use additional repo's           |
| CentOS (Alma/Rocky)    | -       | ❌        | It can run but no support or how to install!             |
| [Debian](OS/deb.md)    | >=10    | ✅        | This will require you to use additional repo's           |
| CPanel                 | X       | ✅        | Requires to use our prebuilt vendors                     |
| Plesk                  | X       | ✅        | Requires to use our prebuilt vendors                     |
| HestiaCP (Vesta)       | X       | ✅        | Requires to use our prebuilt vendors                     |
| CloudPanel             | X       | ✅        | Requires to use our prebuilt image                       |
| DirectAdmin            | X       | ✅        | Requires to use our prebuilt vendors                     |
| WebHosting Panels      | X       | ✅        | Requires to use our prebuilt vendors and to support php! |

If you are using a webhosting panel your only way to install the framework is via the [GUI](#web) interface.

Welcome to our installation documents for MythicalFrameWork <3

When you are installing you have to options to install it!

- [GUI (WebInterface)](#web)
- [CLI (Commands)](#cli)

## CLI

Welcome to the installation script for CLI users!

This will show you how to install the framework with the cli commands!

First please make sure to follow our os setup instructions!

- [Ubuntu](OS/ubuntu.md)
- [Debian](OS/deb.md)

Now that you got the stuff related for the os installed here are the commands that will work both on linux and on debian!

### Installing Composer

Composer is a dependency manager for PHP that allows us to ship everything you'll need code wise to operate the framework. You'll need composer installed before continuing in this process.

```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### Setting up the right timezone

Some servers by default do not have the right timezone setup so you have to do it manually!
Make sure to replace `Europe/Vienna` to your timezone!

```bash
sudo timedatectl set-timezone "Europe/Vienna"
```

### Download Files

The first step in this process is to create the folder where the framework will live and then move ourselves into that newly created folder. Below is an example of how to perform this operation.

```bash
cd /var/www
git clone https://github.com/MythicalLTD/Framework.git framework
cd /var/www/framework
```

Once it is downloaded you'll need to set the correct permissions on the core/ and tmp/ directories. These directories allow us to store files as well as keep a speedy cache available to reduce load times.

```bash
chown -R www-data:www-data /var/www/framework/*
```

### Install vendors

After you've downloaded all of the files you will need to download the core components of the framework. To do this, simply run the commands below and follow any prompts.

```bash
composer install --no-dev --optimize-autoloader
```

### MySQL Setup

#### Setting up MySQL on the server

In the case if you use mariadb you may need to update some settings for our framework to work at the best speeds and to behave good!

##### Configuring MySQL

MySQL by default does not use optimized settings and the right time and date!

```bash
echo "[mysqld]" >> /etc/mysql/my.cnf
echo "default_time_zone = SYSTEM" >> /etc/mysql/my.cnf
```

##### Fixing MariaDB

In the last versions of `MariaDB` they decided to update how the data is being stored!
In this case you need to set it back to default how it was before!

```bash
sudo sed -i '/^#collation-server/a collation-server = utf8mb4_general_ci' /etc/mysql/mariadb.conf.d/50-server.cnf
sudo sed -i '/^character-set-server/s/^/#/g' /etc/mysql/mariadb.conf.d/50-server.cnf
sudo sed -i '/^#character-set-server/a character-set-server = utf8mb4' /etc/mysql/mariadb.conf.d/50-server.cnf

sudo sed -i '/^character-set-collations/s/^/#/g' /etc/mysql/mariadb.conf.d/50-server.cnf
sudo sed -i '/^#character-set-collations/a character-set-collations = utf8mb4' /etc/mysql/mariadb.conf.d/50-server.cnf
```

#### Allowing remote connections to database!

This is just if you want to allow remote connections from other hosts to this database!

It is not required but it is nice to have!

```bash
echo "bind-address = '0.0.0.0'" >> /etc/mysql/my.cnf
sudo sed -i 's/^bind-address.*$/bind-address = 0.0.0.0/' /etc/mysql/mariadb.conf.d/50-server.cnf
```

Now that all of the files have been downloaded we need to configure some core aspects of the framework. You will need a database setup and a user with the correct permissions created for that database before continuing any further.

If you want to allow remote access for this user make sure to replace `127.0.0.1` to `%` in the next commands!

```sql
mysql -u root -p

CREATE USER 'framework'@'127.0.0.1' IDENTIFIED BY 'yourPassword';
CREATE DATABASE framework;
GRANT ALL PRIVILEGES ON framework.* TO 'framework'@'127.0.0.1' WITH GRANT OPTION;
exit;
```

### Redis Setup

We use redis for caches and to store caches and you may need to configure some things for it!
This is not required but it will help you!

```bash
sudo sed -i 's/^bind 127.0.0.1 -::1$/bind 0.0.0.0 -::1/' /etc/redis/redis.conf
sudo sed -i 's/^protected-mode yes$/protected-mode yes/' /etc/redis/redis.conf
```

Now that you have done that we have to start redis and enable it

```bash
systemctl start redis
systemctl enable redis --now
```

After that you may want to create a new user for redis!

```bash
redis-cli
ACL SETUSER "framework" on >"mypassword" allcommands on
exit
```

### PHP Setup

If you think php is slow you may want to run those commands to boost it
```bash
sed -i 's/max_execution_time = 30/max_execution_time = 240/' /etc/php/8.3/fpm/php.ini
sed -i 's/display_errors = .*/display_errors = Off/' /etc/php/8.3/fpm/php.ini
sed -i '/^;zend_extension=opcache/s/^;//' /etc/php/8.3/fpm/php.ini
sed -i 's/^opcache.enable=.*/opcache.enable=1/' /etc/php/8.3/fpm/php.ini
sed -i 's/^;opcache.enable=.*/opcache.enable=1/' /etc/php/8.3/fpm/php.ini
sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/8.3/fpm/php.ini
sed -i 's/^post_max_size = .*/post_max_size = 64M/' /etc/php/8.3/fpm/php.ini
sed -i 's/^zlib.output_compression = .*/zlib.output_compression = On/' /etc/php/8.3/fpm/php.ini
sed -i 's/^zlib.output_compression_level = .*/zlib.output_compression_level = 5/' /etc/php/8.3/fpm/php.ini
sed -i 's/^;realpath_cache_size = .*/realpath_cache_size = 16M/' /etc/php/8.3/fpm/php.ini
sed -i 's/^;realpath_cache_ttl = .*/realpath_cache_ttl = 240/' /etc/php/8.3/fpm/php.ini

sed -i 's/^;session.save_handler = .*/session.save_handler = files/' /etc/php/8.3/fpm/php.ini
sed -i 's|^;session.save_path = .*|session.save_path = /var/lib/php/sessions|' /etc/php/8.3/fpm/php.ini
sed -i 's/^session.cache_limiter = .*/session.cache_limiter = public/' /etc/php/8.3/fpm/php.ini
sed -i 's/^session.cache_expire = .*/session.cache_expire = 240/' /etc/php/8.3/fpm/php.ini
```

Next are 2 commands that you have to modify in order to work

Please modify `2G` to the amount of GB you want to give php to use!

And timezone set it to your timezone!
```bash
sed -i 's/memory_limit = 128M/memory_limit = 2G/' /etc/php/8.2/fpm/php.ini
sed -i 's/;date.timezone =/date.timezone = 'Europe/Vienna' /etc/php/8.2/fpm/php.ini
```

This should boost your php performance by `x10`!!

Now that we are done with the server side let's get back to MythicalFramework

### Setting up the framework

First go to the frameowrk directory

```bash
cd /var/www/framework
```

Now you have to run some commands to set the framework up

```bash
# This will setup the connection to the MySQL database
php framework dbconfigure
# This will build all tables into the database 
php framework dbmigrate
# This will generate the settings in the database
php framework configmigrate
# This will create a unic encryption key to encrypt the user data in the database
php framework newkey
```

### Setting up the firewall
Now you may be using `ufw` or `iptables` so let's configure it the right way!

##### For ufw
```bash
ufw allow 80 # HTTP
ufw allow 443 # HTTPS
ufw allow 3306 # MySQL
ufw allow 22 # SSH
```

##### For iptables
First let's install the iptables package to make sure our rules stay saved!
```bash
apt install iptables-persistent -y
```
Now let's set the firewall up and save it!
```bash
iptables -A INPUT -p tcp --dport 80 -j ACCEPT # HTTP
iptables -A INPUT -p tcp --dport 443 -j ACCEPT # HTTPS
iptables -A INPUT -p tcp --dport 22 -j ACCEPT # SSH
iptables -A INPUT -p tcp --dport 3306 -j ACCEPT # MySQL
iptables -A INPUT -p tcp --dport 6379 -j ACCEPT # Redis-CLI

# Block forward so no one can use your domain to login inside SSH
iptables -A FORWARD -p tcp --dport 80 -j ACCEPT # HTTP
iptables -A FORWARD -p tcp --dport 443 -j ACCEPT # HTTPS
iptables -A FORWARD -p tcp --dport 22 -j DROP # SSH
iptables -A FORWARD -p tcp --dport 3306 -j DROP # MySQL
iptables -A FORWARD -p tcp --dport 6379 -j DROP # Redis-CLI


iptables-save > /etc/iptables/rules.v4
```

##### No firewall (NOT RECOMMENDED)

This is not recommended but a lot of ppl do it!
#### If you want to disable ufw
```bash
ufw disable
```

#### If you want to disable iptables
First let's install the iptables package to make sure our rules stay saved!
```bash
apt install iptables-persistent -y
sudo iptables -P INPUT ACCEPT
sudo iptables -P FORWARD ACCEPT
sudo iptables -P OUTPUT ACCEPT
sudo iptables -t nat -F
sudo iptables -t mangle -F
sudo iptables -F
sudo iptables -X
iptables-save > /etc/iptables/rules.v4
```

### WebServer
Now that we are done with this let's setup a webserver to run the framework!

For this you can use one of those tutorials

- [Apache2](WEBSERVERS/apache2.md)
- [Nginx](WEBSERVERS/nginx.md)

After you have your webserver running you are done <3!!!


## WEB (WebHosting Panels/Servers)
