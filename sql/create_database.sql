CREATE DATABASE aomagento DEFAULT CHARSET utf8 COLLATE utf8_unicode_ci;
CREATE USER 'aomagento'@'localhost' IDENTIFIED BY 'aoamgento';
GRANT ALL PRIVILEGES ON aomagento.* TO 'aomagento'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;