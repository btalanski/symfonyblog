#Symfony 4 blog
This is just a simple CRUD app developed using Symfony 4 as a study to learn the basics about the Symfony framework. 

This should not be taken seriously for now but I intend to keep adding more functionalities to it as I continue to explore the framework.


###To do list

- [ ] Make installation user-friendly
- [ ] Improve post management
- [ ] Improve templating logic to allow custom themes
- [ ] Improve media management
- [ ] Allow post comments
- [ ] Add tagging system
- [ ] Add AMP support
- [ ] Dynamic menu system
- [x] Set SASS variables using webpack


##Useful commands

###Doctrine

```
# update migration
php bin/console make:migration

# run migration
php bin/console doctrine:migrations:migrate

# create database
php bin/console doctrine:database:create

#update database schema
php bin/console doctrine:schema:update --force

# drop database
php bin/console doctrine:schema:update --force
```