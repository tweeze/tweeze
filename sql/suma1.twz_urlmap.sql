# TWEEZE
# MYSQL DATABASE “suma1”
# suma1.twz_hub.sql

# CREATE DATABASE
create database if not exists suma1 default character set uft8;

# SET PRIVILEGES
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' with grant option;

# SET CURRENT DATABASE
use suma1;

# DROP TABLE suma1.twz_urlmap
drop table if exists suma1.twz_urlmap;

# CREATE TABLE suma1.twz_urlmap
create table if not exists suma1.twz_urlmap (
id bigint(20) unsigned,
display_url text not null,
expanded_url text default null,
truncated_url text not null,
url text not null,
status_code tinyint(10) unsigned default null,
content_type varchar(255) default null,
resolved boolean default false,
valid boolean default false,
resolve_date datetime default null,
index id_idx (id),
foreign key (id) references suma1.twz_hub(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# VERIFY TABLE suma1.twz_urlmap
describe suma1.twz_urlmap;

# INSERT VALUES IN TABLE suma1.twz_urlmap
# Inserts fields (tweet_id, display_url, truncated_url, url) into table suma1.twz_urlmap
# Values of (expanded_url) will be inserted into (truncated_url)
insert into suma1.twz_urlmap (tweet_id, display_url, truncated_url, url) select tweet_id, display_url, expanded_url, url from suma1.wut_urls;