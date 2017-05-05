PTUT-Metinet
============

Rules
-
- Please rebase on dev branch before pull request
- Merge dev into master when all code have been tested

## Docker install commands

Require docker and docker-compose.
```bash
$ docker-compose up -d
```

Install dependencies inside the docker container.
```bash
$ make composer-install
```

The mysql server host access from inside the php container is 'mysql'.
The defaults access are :
User     : work-competition
Password : work-competition
Database : work-competition

To setup database
```bash
$ make pbc cmd="doctrine:schema:update --force"
```

To import default admin user
```bash
$ make mysql-import path=dumps/initial-admin-user.sql
```

To add hosts
```bash
$ sudo bash -c "echo 127.0.0.1 dev.work-competition.fr dev.adminer.work-competition.fr >> /etc/hosts"
```

To change permissions
```bash
$ sudo chown $USER:www-data -R ./ && sudo chmod 775 -R ./
```
