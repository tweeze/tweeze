# SQL
# 
# DB: MySQL
# Database: suma1
# File: suma1.twz_results.sql

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

# 1. Drop/Create table 'suma1.twz_results':

drop table if exists suma1.twz_results;
create table if not exists suma1.twz_results (
id bigint(20) unsigned not null auto_increment,
tweet_id bigint(20) unsigned,
urls_final_id bigint(20) unsigned,
urls_final_url text,
value int(11),
primary key (id)
) engine=InnoDB default charset utf8;

# F. enable constraint checks
set foreign_key_checks=1;