-- ----------------------------------------------------------------------------
-- SQL: suma1_ddl.sql
-- Note: Database schema for twitter search engine (tweeze)
-- Last modified: 06-27-13
-- ----------------------------------------------------------------------------

-- ----------------------------------------------------------------------------
-- DBMS
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
-- PREREQUISITES  
-- ----------------------------------------------------------------------------

-- Create database
create database if not exists suma1 default character set uft8 
collate utf8_unicode_ci;

-- Grant privileges
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' 
with grant option;

-- Use database
use suma1;

-- Temporarly disable constraint checks
set foreign_key_checks=0;

-- ----------------------------------------------------------------------------
-- DDL 
-- ----------------------------------------------------------------------------

-- Drop/Create table 'suma1.twz_hub'
drop table if exists suma1.twz_hub;
create table if not exists suma1.twz_hub (
id bigint(20) unsigned not null auto_increment,
tweet_id bigint(20) unsigned not null,
index tweet_id_idx (tweet_id),
primary key (id),
foreign key (tweet_id) references suma1.wut_tweets (id) 
on update cascade on delete cascade
) engine=innodb default charset utf8 collate utf8_unicode_ci 
comment='suma1.twz_hub';

-- Drop/Create table 'suma1.twz_urls'
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
index idx_idx (idx),
index expanded_url_idx (expanded_url(255)),
primary key (id),
foreign key (idx) references suma1.twz_urlmap (urls_idx) 
on update cascade on delete cascade
) engine=InnoDB default charset utf8 collate utf8_unicode_ci
comment='suma1.twz_urls';

-- Drop/Create table 'suma1.twz_urlmap'
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
primary key (id),
foreign key (hub_id) references suma1.twz_hub(id) 
on update cascade on delete cascade
) engine=InnoDB default charset utf8 collate utf8_unicode_ci
comment='suma1.twz_urlmap';

-- Drop/Create table 'suma1.twz_urls_final'
drop table if exists suma1.twz_urls_final;
create table if not exists suma1.twz_urls_final (
id bigint(20) unsigned not null auto_increment,
idx bigint(20) unsigned,
url text default null,
index idx_idx (idx),
index url_idx (url(255)),
primary key (id),
foreign key (idx) references suma1.twz_urlmap (urls_final_idx) 
on update cascade on delete cascade
) engine=innodb default charset utf8 collate utf8_unicode_ci
comment='suma1.twz_urls_final';

-- Drop/Create table 'suma1.twz_documents'
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
count_words int(11) default null,
index count_words_idx(count_words),
index urls_final_id_idx (urls_final_id),
primary key (id),
foreign key (urls_final_id) references suma1.twz_urls_final(id) 
on update cascade on delete cascade
) engine=innodb default charset utf8 collate utf8_unicode_ci
comment='suma1.twz_documents';

-- Drop/Create table 'suma1.twz_words'
drop table if exists suma1.twz_words;
create table if not exists suma1.twz_words (
id bigint(20) unsigned not null auto_increment,
word varchar(255) default null,
count_doc int(11) default null,
count_all bigint(20) unsigned default null,
idf float not null default 0,
index word_idx(word),
primary key (id, word)
) engine=innodb default charset utf8 collate utf8_unicode_ci
comment='suma1.twz_words';

-- Drop/Create table 'suma1.twz_wordmap'
drop table if exists suma1.twz_wordmap;
create table if not exists suma1.twz_wordmap (
words_id bigint(20) unsigned default null,
documents_id bigint(20) unsigned default null,
word_count smallint(11) unsigned not null,
title boolean default null,
wdf float not null default 0,
index words_id_idx(words_id),
index documents_id_idx(documents_id),
index wdf_idx(wdf),
foreign key (words_id) references suma1.twz_words(id) 
on update cascade on delete cascade,
foreign key (documents_id) references suma1.twz_documents(id) 
on update cascade on delete cascade
) engine=innodb default charset utf8 collate utf8_unicode_ci
comment='suma1.twz_wordmap';

-- ----------------------------------------------------------------------------
-- FINAL  
-- ----------------------------------------------------------------------------

-- Enable constraint checks
set foreign_key_checks=1;

-- EOF