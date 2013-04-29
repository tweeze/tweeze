# TWEEZE
# MYSQL DATABASE “suma1”
# suma1.twz_hub.sql

# CREATE DATABASE
create database if not exists suma1 default character set uft8;

# SET PRIVILEGES
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' with grant option;

# SET CURRENT DATABASE
use suma1;

# DROP TABLE suma1.twz_hub
drop table if exists suma1.twz_hub;

# CREATE TABLE suma1.twz_hub
create table if not exists suma1.twz_hub (
id bigint(20) unsigned not null auto_increment,
tweet_id bigint(20) unsigned,
index tweet_id_idx (tweet_id),
primary key (id),
foreign key (tweet_id) references suma1.wut_tweets(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# VERIFY TABLE suma1.twz_hub
describe suma1.twz_hub;

# INSERT VALUES IN TABLE suma1.twz_hub
# Inserts fields (tweet_id) into table suma1.twz_hub
insert into suma1.twz_hub (tweet_id) select id from suma1.wut_tweets;