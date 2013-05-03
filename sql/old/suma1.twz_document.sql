# TWEEZE
# MYSQL DATABASE “suma1”
# suma1.twz_document.sql

# CREATE DATABASE
create database if not exists suma1 default character set uft8;

# SET PRIVILEGES
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' with grant option;

# SET CURRENT DATABASE
use suma1;

# DROP TABLE suma1.twz_document
drop table if exists suma1.twz_document;

# CREATE TABLE suma1.twz_document
create table if not exists suma1.twz_document (
id bigint(20) unsigned not null auto_increment,
hub_id bigint(20) unsigned,
content text default null,
index hub_id_idx (hub_id),
primary key (id),
foreign key (hub_id) references suma1.twz_hub(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# VERIFY TABLE suma1.twz_document
describe suma1.twz_document;