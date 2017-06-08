nginx_container_name = work-competition-nginx
php_container_name = work-competition-php
mysql_container_name =  work-competition-mysql

.PHONY: pac mod bash composer-update dump command

pbc:
	docker exec -it $(php_container_name) php bin/console $(cmd)

bash:
	docker exec -it $(php_container_name) bash

composer-add-github-token:
	docker exec -it $(php_container_name) composer config --global github-oauth.github.com $(token)

composer-install:
	docker exec -i $(php_container_name) composer install

composer-update:
	docker exec -it $(php_container_name) composer update

mysql-import: $(path)
	docker exec -i $(mysql_container_name) bash -c "mysql -pwork-competition -uwork-competition work-competition" < $(path)

mysql-dump:
	docker exec -i $(mysql_container_name) bash -c "mysqldump -p'work-competition' -u work-competition work-competition" > work-competition.sql

command:
	docker exec -it $(php_container_name) $(cmd)

default: pac
