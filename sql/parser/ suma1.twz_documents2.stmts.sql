# TWEEZE
# MYSQL DATABASE “suma1”
# > suma1.twz_documents_words_wordmap.stmts.sql

# PREQUISITE:

# A. create database suma1:

create database if not exists suma1 default character set uft8;

# B. grant privileges:
grant all on suma1.* to 'suma1'@'localhost' identified by 'suma1' with grant option;

# C. set database to suma1:
use suma1;

# D. import tables/data:
# mysqladmin -u root -p suma1 < /SQL_DUMP.SQL

# E. disable constraint checks
# set FOREIGN_KEY_CHECKS=0;

# CREATE TABLES:

# 1. drop/create table suma1.twz_documents:

drop table if exists suma1.twz_documents;
create table if not exists suma1.twz_documents (
id bigint(20) unsigned not null auto_increment,
urls_id bigint(20) unsigned,
content text default null,
identifier varchar(255) default null,
parsed boolean default false,
parse_date datetime default null,
index urls_id_idx (urls_id),
primary key (id),
foreign key (urls_id) references suma1.twz_urls(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# 2. drop/create table suma1.twz_words:

drop table if exists suma1.twz_words;
create table if not exists suma1.twz_words (
id bigint(20) unsigned not null auto_increment,
word varchar(255) default null,
idf int(11) default null,
count int(11) default null,
primary key (id),
foreign key (urls_id) references suma1.twz_urls(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;

# 3. drop/create table suma1.twz_wordmap:

drop table if exists suma1.twz_wordmap;
create table if not exists suma1.twz_wordmap (
documents_id bigint(20) unsigned,
words_id bigint(20) unsigned,
hashtag boolean default false,
foreign key (documents_id) references suma1.twz_documents(id) on update cascade on delete cascade,
foreign key (words_id) references suma1.twz_words(id) on update cascade on delete cascade
) engine=InnoDB default charset utf8;
