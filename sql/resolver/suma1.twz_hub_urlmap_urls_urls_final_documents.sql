-- ----------------------------------------------------------------------------
-- SQL suma1.db_schema.sql
-- Project: tweeze
-- Description: Database schema for twitter search engine
-- ----------------------------------------------------------------------------

-- ----------------------------------------------------------------------------
-- DB-SETTINGS: Adjust settings to match underlying hardware:
-- ----------------------------------------------------------------------------
/*
set global have_query_cache=1;
set global query_cache_size=41984;
set global query_cache_type=1;
set global log_slow_queries=1;
set global long_query_time=5;
set global max_heap_table_size=67108864;
set global tmp_table_size=67108864;
*/

-- ----------------------------------------------------------------------------
-- PREREQUISITES:  
-- ----------------------------------------------------------------------------

-- A. Create database suma1:
create database if not exists suma1 default character set uft8 
collate utf8_unicode_ci;

-- B. Grant privileges to user 'suma1':
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' 
with grant option;

-- C. Set current database to 'suma1':
use suma1;

-- D. Temporarly disable constraint checks:
set foreign_key_checks=0;


# SQL STATEMENTS:

-- 1. Drop/Create table 'suma1.twz_hub':
drop table if exists suma1.twz_hub;
create table if not exists suma1.twz_hub (
id bigint(20) unsigned not null auto_increment,
tweet_id bigint(20) unsigned not null,
index tweet_id_idx (tweet_id),
primary key (id),
foreign key (tweet_id) references suma1.wut_tweets (id) 
on update cascade on delete cascade
) engine=innodb default charset utf8 collate utf8_unicode_ci comment='suma1.twz_hub';

# 2. Drop/Create table 'suma1.twz_urls':

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
index valid_idx (valid),
index id_idx (id),
index idx_idx (idx),
index expanded_url_idx (expanded_url(255)),
primary key (id),
foreign key (idx) references suma1.twz_urlmap (urls_idx) 
on update cascade on delete cascade
) engine=InnoDB default charset utf8 collate utf8_unicode_ci;

# 3. Drop/Create table 'suma1.twz_urlmap':

drop table if exists suma1.twz_urlmap;
create table if not exists suma1.twz_urlmap (
id bigint(20) unsigned not null auto_increment,
urls_idx bigint(20) unsigned,
urls_final_idx bigint(20) unsigned,
hub_id bigint(20) unsigned,
tweet_id BIGINT(20) unsigned,
display_url text default null,
truncated_url text default null,
expanded_url text default null,
url text default null,
index urls_idx_idx (urls_idx),
index urls_final_idx_idx (urls_final_idx),
index hub_id_idx (hub_id),
index truncated_url_idx (truncated_url(255)),
index expanded_url_idx (expanded_url(255)),
index id_idx (id),
primary key (id),
foreign key (hub_id) references suma1.twz_hub(id) 
on update cascade on delete cascade
) engine=InnoDB default charset utf8 collate utf8_unicode_ci;

# 4. Drop/Create table 'suma1.twz_urls_final':

drop table if exists suma1.twz_urls_final;
create table if not exists suma1.twz_urls_final (
id bigint(20) unsigned not null auto_increment,
idx bigint(20) unsigned,
url text default null,
index id_idx (id),
index idx_idx (idx),
index url_idx (url(255)),
primary key (id),
foreign key (idx) references suma1.twz_urlmap (urls_final_idx) 
on update cascade on delete cascade
) engine=InnoDB default charset utf8 collate utf8_unicode_ci;

# 5. Populate field 'suma1.twz_hub.tweet_id' 
# -> 'suma1.wut_tweets.id' (99617 records):

insert into suma1.twz_hub (tweet_id) 
select id 
from suma1.wut_tweets 
order by suma1.wut_tweets.id;

# 6. Populate table 'suma1.twz_urlmap' -> 'suma1.wut_urls' (37460 records):
# Note: 'suma1.wut_urls.expanded_url' -> 'suma1.twz_urlmap.truncated_url'!

insert into suma1.twz_urlmap (tweet_id, display_url, truncated_url, url)
select suma1.wut_urls.tweet_id, suma1.wut_urls.display_url, 
suma1.wut_urls.expanded_url, suma1.wut_urls.url 
from suma1.wut_urls 
order by suma1.wut_urls.tweet_id;

# 7. Update field 'suma1.twz_urlmap.urls_idx':
# A. (24457 records)

update suma1.twz_urlmap 
inner join (
select truncated_url, MIN(id) as firstid
from suma1.twz_urlmap
group by truncated_url
having count(truncated_url) > 1) 
dup on suma1.twz_urlmap.truncated_url = dup.truncated_url
set suma1.twz_urlmap.urls_idx=dup.firstid;

# B. (13003 records)

update suma1.twz_urlmap 
inner join (
select truncated_url, MIN(id) as firstid
from suma1.twz_urlmap
group by truncated_url)
dup on suma1.twz_urlmap.truncated_url = dup.truncated_url
set suma1.twz_urlmap.urls_idx=dup.firstid;

# 8. Update field 'suma1.twz_urlmap.hub_id' 
# -> 'suma1.twz_hub.id' (37460 records):

lock tables suma1.twz_urlmap write;
update suma1.twz_urlmap join suma1.twz_hub 
on suma1.twz_hub.tweet_id=suma1.twz_urlmap.tweet_id
set suma1.twz_urlmap.hub_id=suma1.twz_hub.id;
unlock tables;

# 9. Populate table 'suma1.twz_urls' -> 'suma1.twz_urlmap' (17791 records):

insert into suma1.twz_urls (idx, display_url, truncated_url, url)
select suma1.twz_urlmap.urls_idx, suma1.twz_urlmap.display_url, 
suma1.twz_urlmap.truncated_url, 
suma1.twz_urlmap.url from suma1.twz_urlmap 
group by suma1.twz_urlmap.urls_idx 
order by suma1.twz_urlmap.urls_idx;

# Note: Run URLResolver or import sql dump!
# E. Import tables/data:
# > mysqladmin -u root -p suma1 < /%SQLDUMP%.SQL

# 10. Update field 'suma1.twz_urlmap.expanded_url' 
# -> 'suma1.twz_urlmap.expanded_url' (37460 records):

lock tables suma1.twz_urlmap write, suma1.twz_urls write;
update suma1.twz_urlmap, suma1.twz_urls
set suma1.twz_urlmap.expanded_url=suma1.twz_urls.expanded_url
where suma1.twz_urlmap.urls_idx=suma1.twz_urls.idx;
unlock tables;

# 11. Update field 'suma1.twz_urlmap.urls_final_idx':
# A. (27412 records)

update suma1.twz_urlmap 
inner join (
select expanded_url, MIN(id) as firstid
from suma1.twz_urlmap
group by expanded_url
having count(expanded_url) > 1) 
dup on twz_urlmap.expanded_url = dup.expanded_url
set suma1.twz_urlmap.urls_final_idx=dup.firstid;

# B. (10048 records)

update suma1.twz_urlmap 
inner join (
select expanded_url, MIN(id) as firstid
from suma1.twz_urlmap
group by expanded_url)
dup on suma1.twz_urlmap.expanded_url = dup.expanded_url
set suma1.twz_urlmap.urls_final_idx=dup.firstid;

# 12. Populate table 'suma1.twz_urls_final' 
# -> 'suma1.twz_urlmap' (12371 records):

insert into suma1.twz_urls_final (url)
select suma1.twz_urls.expanded_url
from suma1.twz_urls where valid=1 
group by suma1.twz_urls.expanded_url 
order by suma1.twz_urls.id;

# 13. Update field 'suma1.twz_urls_final.idx'
# -> 'suma1.twz_urlmap.urls_final_idx' (12371 records):

lock tables suma1.twz_urlmap write, suma1.twz_urls_final write;
update suma1.twz_urlmap, suma1.twz_urls_final
set suma1.twz_urls_final.idx=suma1.twz_urlmap.urls_final_idx
where suma1.twz_urls_final.url=suma1.twz_urlmap.expanded_url;
unlock tables;

# 14. Drop fields from table 'suma1.twz_urlmap' except for 'urls_idx', 
# 'urls_final_idx' and 'hub_id' (37460 records):

alter table suma1.twz_urlmap drop id, drop tweet_id, drop display_url, 
drop truncated_url, drop url, drop expanded_url;

# 15. Drop/Create table 'suma1.twz_documents':

drop table if exists suma1.twz_documents;
create table if not exists suma1.twz_documents (
id bigint(20) unsigned not null auto_increment,
urls_final_id bigint(20) unsigned,
parsed boolean default false,
parse_date datetime default null,
content mediumtext default null,
meta_description text default null,
meta_keyword text default null,
language_description varchar(255) default null,
index urls_final_id_idx (urls_final_id),
primary key (id),
foreign key (urls_final_id) references suma1.twz_urls_final(id) 
on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# F. Enable constraint checks
set foreign_key_checks=1;