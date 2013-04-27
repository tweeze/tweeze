# TWEEZE
# MYSQL DATABASE “tweeze”

# CREATE DATABASE
create database if not exists tweezy default character set latin1;

# SET PRIVILEGES
grant all on tweeze.* to 'tweeze'@'localhost' identified by 'password' with grant option;
grant select on tweeze.* to 'user'@'localhost' identified by 'password' with grant option;

# SET CURRENT DATABASE
use tweeze;

# CREATE TABLES
# INNODB ENGINE SUPPORTS FOREIGN KEY CONSTRAINTS

# Sample table A!!!
create table if not exists words (
id int(10) not null auto_increment,
name varchar(255) not null default "",
primary key (id)
) engine=InnoDB default charset latin1;

# Sample table B!!!
create table if not exists relate (
id int(2) not null auto_increment,
name varchar(255) not null default "",
words_id int(10) not null,
index something_id_idx (words_id),
primary key (id),
foreign key (words_id) references words(id) on update cascade on delete cascade
) engine=InnoDB default charset latin1;

