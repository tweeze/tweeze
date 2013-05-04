# TWEEZE
# MYSQL DATABASE “suma1”
# > suma1.twz_document.stmts.sql

# PREQUISITE:

# A. create database suma1:

create database if not exists suma1 default character set uft8;

# B. grant privileges:
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' with grant option;

# C. set database to suma1:
use suma1;

# D. import tables/data:
# mysqladmin -u root -p suma1 < /SQL_DUMP.SQL

# E. disable constraint checks
# set FOREIGN_KEY_CHECKS=0;

# CREATE TABLES:

# 1. drop/create table suma1.twz_document:

drop table if exists suma1.twz_document;
create table if not exists suma1.twz_document (
id bigint(20) unsigned not null auto_increment,
url_id bigint(20) unsigned,
index url_id_idx (url_id),
content text default null,
parsed boolean default false,
parse_date datetime default null,
primary key (id),
foreign key (url_id) references suma1.twz_urls(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;