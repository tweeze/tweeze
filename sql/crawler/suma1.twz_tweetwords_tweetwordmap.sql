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

-- Drop/Create table 'suma1.twz_tweetwords'
drop table if exists suma1.twz_tweetwords;
create table if not exists suma1.twz_tweetwords (
id bigint(20) unsigned not null auto_increment,
tweetword varchar(255) default null,
primary key (id, tweetword)
) engine=innodb default charset utf8 collate utf8_unicode_ci
comment='suma1.twz_tweetwords';

-- Drop/Create table 'suma1.twz_tweetwordmap'
drop table if exists suma1.twz_tweetwordmap;
create table if not exists suma1.twz_tweetwordmap (
tweetwords_id bigint(20) unsigned default null,
tweet_id bigint(20) unsigned default null,
primary key (tweetwords_id,tweet_id),
foreign key (tweetwords_id) references suma1.twz_tweetwords(id) 
on update cascade on delete cascade,
foreign key (tweet_id) references suma1.twz_hub(tweet_id) 
on update cascade on delete cascade
) engine=innodb default charset utf8 collate utf8_unicode_ci
comment='suma1.twz_tweetwordmap';

# F. enable constraint checks
set foreign_key_checks=1;