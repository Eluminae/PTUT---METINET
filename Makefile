nginx_container_name = work-competition-nginx
php_container_name = work-competition-php
mysql_container_name =  work-competition-mysql

.PHONY: pac mod bash composer-update dump command

pac:
	docker exec -it $(php_container_name) php app/console $(cmd)

bash:
	docker exec -it $(php_container_name) bash

composer-add-github-token:
	docker exec -it $(php_container_name) composer config --global github-oauth.github.com $(token)

composer-update:
	docker exec -it $(php_container_name) composer update

dump:
	docker exec -i $(mysql_container_name) bash -c "mysqldump -p'operation-manager' -u operation-manager operation-manager" > operation-manager.sql

command:
	docker exec -it $(php_container_name) $(cmd)

default: pac
