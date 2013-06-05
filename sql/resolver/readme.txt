# readme.txt

mysql> set foreign_key_checks=0;

# import CLI:
> mysqladmin -u root -p suma1 < /suma1.twz_hub.dump.sql
> mysqladmin -u root -p suma1 < /suma1.twz_urls.dump.sql
> mysqladmin -u root -p suma1 < /suma1.twz_urls_final.dump.sql
> mysqladmin -u root -p suma1 < /suma1.twz_urlmap.dump.sql

OR

# import phpmyadmin:
> suma1.twz_hub.dump.sql
> suma1.twz_urls.dump.sql
> suma1.twz_urls_final.dump.sql
> suma1.twz_urlmap.dump.sql

mysql> set foreign_key_checks=1;