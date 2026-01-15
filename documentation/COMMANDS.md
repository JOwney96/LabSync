# Project Commands Cheat Sheet

Use this reference for common tasks when developing LabSync.

## Launching the Project
Start the project:
```
    composer run dev
```

## Database Management
Run the migrations, which update the database schema:
```
    php artisan migrate
```

Reset the database, **WARNING**: this will delete all data:
```
    php artisan migrate:fresh
```

Seed the database with dummy data, **Note:** this requires seeders to be created:
```
    php artisan db:seed
```

## Creating New Files (Artisan)
Don't create files manually! Artisan can generate all the boilerplate for you:

Create a controller:
```
    php artisan make:controller ControllerName
```

Create a model and a database migration for the model:
```
    php artisan make:model ModelName -m
```

Create a seeder:
```
    php artisan make:seeder SeederName
```

## Troubleshooting
If the app is crashing or acting weird, try these first:

Clear application cache:
```
    php artisan optimize:clear
```

Install missing php packages – Run this if you get a "Class not found" error:
```
    composer install
```

Install missing node packages – Run this if `npm run dev` fails
```
    npm install
```
