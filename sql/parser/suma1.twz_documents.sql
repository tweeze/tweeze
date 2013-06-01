# TWEEZE
# MYSQL DATABASE “suma1”
# > suma1.twz_documents.stmts.sql

# PREQUISITE:

# A. create database suma1:

create database if not exists suma1 default character set uft8;

# B. grant privileges:
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' with grant option;

# C. set database to suma1:
use suma1;

# D. disable constraint checks
set FOREIGN_KEY_CHECKS=0;

# E. import tables/data:
# mysqladmin -u root -p suma1 < /SQL_DUMP.SQL

# CREATE TABLES:

# 1. drop/create table suma1.twz_documents:

drop table if exists suma1.twz_documents;
create table if not exists suma1.twz_documents (
id bigint(20) unsigned not null auto_increment,
urls_final_id bigint(20) unsigned,
parsed boolean default false,
parse_date datetime default null,
content mediumtext default null,
meta_description text default null,
meta_keyword text default null,
language varchar(255) default null,
index urls_final_id_idx (urls_final_id),
primary key (id),
foreign key (urls_final_id) references suma1.twz_urls_final(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# F. enable constraint checks
set FOREIGN_KEY_CHECKS=1;