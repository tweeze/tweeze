# TWEEZE
# MYSQL DATABASE “suma1”
# > suma1.twz_hub_urlmap_urls.stmts.sql

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

# 1. drop/create table suma1.twz_hub:

drop table if exists suma1.twz_hub;
create table if not exists suma1.twz_hub (
id bigint(20) unsigned not null auto_increment,
tweet_id bigint(20) unsigned,
index tweet_id_idx (tweet_id),
primary key (id),
foreign key (tweet_id) references suma1.wut_tweets(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# 2. drop/create table suma1.twz_urls:

drop table if exists suma1.twz_urls;
create table if not exists suma1.twz_urls (
id bigint(20) unsigned not null auto_increment,
idx bigint(20) unsigned,
display_url text default null,
expanded_url text default null,
truncated_url text default null,
url text default null,
status_code int(10) unsigned default null,
content_type varchar(255) default null,
resolved boolean default false,
valid boolean default false,
resolve_date datetime default null,
primary key (id),
foreign key (idx) references suma1.twz_urlmap (urls_idx) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# 3. drop/create table suma1.twz_urlmap:

drop table if exists suma1.twz_urlmap;
create table if not exists suma1.twz_urlmap (
id bigint(20) unsigned not null auto_increment,
urls_idx bigint(20) unsigned,
hub_id bigint(20) unsigned,
tweet_id BIGINT(20) unsigned,
display_url text default null,
truncated_url text default null,
url text default null,
index urls_idx_idx (urls_idx),
index hub_id_idx (hub_id),
primary key (id),
foreign key (hub_id) references suma1.twz_hub(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# 4. drop/create table suma1.twz_urls_final:

drop table if exists suma1.twz_urls_final;
create table if not exists suma1.twz_urls_final (
id bigint(20) unsigned not null auto_increment,
idx bigint(20) unsigned,
expanded_url text default null,
primary key (id),
foreign key (idx) references suma1.twz_urls (idx) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# POPULATE TABLES

# 5. populate table suma1.twz_hub (99617 records):

insert into suma1.twz_hub (tweet_id) select id from suma1.wut_tweets order by suma1.wut_tweets.id;

# 6. populate table suma1.twz_urlmap (37460 records):

insert into suma1.twz_urlmap (tweet_id, display_url, truncated_url,url)
select suma1.wut_urls.tweet_id, suma1.wut_urls.display_url, suma1.wut_urls.expanded_url, suma1.wut_urls.url 
from suma1.wut_urls order by suma1.wut_urls.tweet_id;

# 7a. update "urls_idx" in suma1.twz_urlmap (24457 records):

update suma1.twz_urlmap 
INNER JOIN (
    SELECT truncated_url, MIN(id) as firstid
    FROM suma1.twz_urlmap
    GROUP BY truncated_url
    HAVING count(truncated_url) > 1
) dup ON twz_urlmap.truncated_url = dup.truncated_url
set suma1.twz_urlmap.urls_idx=dup.firstid;

# 7b. update "url_id" in suma1.twz_urlmap (13003 records):

update suma1.twz_urlmap 
JOIN (
	select truncated_url, MIN(id) as firstid
	from suma1.twz_urlmap
	group by truncated_url
) q
ON 	suma1.twz_urlmap.truncated_url = q.truncated_url
set suma1.twz_urlmap.urls_idx=q.firstid;

# 8. update "hub_id" in suma1.twz_urlmap (37460 records):

update suma1.twz_urlmap JOIN twz_hub ON twz_hub.tweet_id=twz_urlmap.tweet_id
set suma1.twz_urlmap.hub_id=twz_hub.id;

# 9. populate suma1.twz_urls from suma1.twz_urlmap (17791 records):

insert into suma1.twz_urls (idx, display_url, truncated_url, url)
select suma1.twz_urlmap.urls_idx, suma1.twz_urlmap.display_url, suma1.twz_urlmap.truncated_url, 
suma1.twz_urlmap.url from suma1.twz_urlmap group by suma1.twz_urlmap.urls_idx order by suma1.twz_urlmap.urls_idx;

# RUN RESOLVER! REDO UPDATE-PROCEDURE FOR FINAL URLS

# 10. drop colums from suma1.twz_urlmap except for "urls_idx", "hub_id":

alter table suma1.twz_urlmap drop id, drop tweet_id, drop display_url, drop truncated_url, drop url;

# F. enable constraint checks
set FOREIGN_KEY_CHECKS=1;