# SQL
# 
# DB: MySQL
# Database: suma1
# File: suma1.twz_words_wordmap.sql

# PREREQUISITES:  

# A. Create database suma1:
create database if not exists suma1 default character set uft8;

# B. Grant privileges to user 'suma1':
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' with grant option;

# C. Set current database to 'suma1':
use suma1;

# D. Temporarly disable constraint checks:
set foreign_key_checks=0;

# E. Import tables/data:
# > mysqladmin -u root -p suma1 < /%SQLDUMP%.SQL

# SQL STATEMENTS:

# 1. Drop/Create table 'suma1.twz_words':

drop table if exists suma1.twz_words;
create table if not exists suma1.twz_words (
id bigint(20) unsigned not null auto_increment,
word varchar(255) default null,
count_doc int(11) default null,
count_all bigint(20) unsigned default null,
idf float default null,
index id_idx(id),
index word_idx(word),
primary key (id, word)
) engine=InnoDB default charset utf8;

# 2. Drop/Create table 'suma1.twz_wordmap':

drop table if exists suma1.twz_wordmap;
create table if not exists suma1.twz_wordmap (
words_id bigint(20) unsigned default null,
documents_id bigint(20) unsigned default null,
word_count smallint(11) unsigned not null,
title boolean default null,
index words_id_idx(words_id),
index documents_id_idx(documents_id),
foreign key (words_id) references suma1.twz_words(id) on update cascade on delete cascade,
foreign key (documents_id) references suma1.twz_documents(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# F. enable constraint checks
set foreign_key_checks=1;