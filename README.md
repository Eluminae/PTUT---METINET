PTUT-Metinet
============

Rules
-
- Please rebase on dev branch before pull request
- Merge dev into master when all code have been tested

## Docker commands

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
