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
id bigint(20) unsigned not null auto_increment,
hub_id bigint(20) unsigned,
display_url text default null,
expanded_url text default null,
truncated_url text default null,
url text default null,
status_code tinyint(10) unsigned default null,
content_type varchar(255) default null,
resolved boolean default false,
valid boolean default false,
resolve_date datetime default null,
index hub_id_idx (hub_id),
primary key (id),
foreign key (hub_id) references suma1.twz_hub(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# VERIFY TABLE suma1.twz_urlmap
describe suma1.twz_urlmap;

# Insert all tweets with link/url into suma1.twz_urlmap (18614 of 42150)
insert into suma1.twz_urlmap (hub_id, display_url, expanded_url, url)
select suma1.twz_hub.id, suma1.wut_urls.display_url, suma1.wut_urls.expanded_url, suma1.wut_urls.url 
from suma1.twz_hub, suma1.wut_urls where suma1.twz_hub.tweet_id = suma1.wut_urls.tweet_id;