# enuygun_case
Wingie EnUygun Case

Project uses sqlite database in default

INSTALLATION & USAGE

Install Required Packages
```
composer install
```

Run For Migrations
```
php bin/console doctrine:migrations:migrate
```

Run For Seeding Process For Developer Records
```
php bin/console app:seed-developer
```

Run For Fetching Task Records From Providers
```
php bin/console app:store-tasks
```

Open Homepage For Showing Task Records
http:127.0.0.1:8000/

Click "Plan Information" Button To See Information About Planning