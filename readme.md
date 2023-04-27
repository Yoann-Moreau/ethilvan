# Ethil Van website

Ethil Van's website is a Symfony 6.1 project

## Installation

Create a `.env.local` file inside the root directory with the following information customized to your needs:
```ini
APP_ENV=dev
APP_SECRET=app_secret

DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?charset=utf8"

MJ_APIKEY_PUBLIC=public_key_here
MJ_APIKEY_PRIVATE=secret_key_here
SENDER_EMAIL="email@email.email"
ADMIN_EMAIL="email@email.email"
ACCOUNT_ACTIVATION_TEMPLATE_ID=template_id_here
```

Run `composer install` inside the root directory, this will create a vendor directory with the dependencies.

Run `php bin/console doctrine:database:create` to create the database.

Run `php bin/console doctrine:migrations:migrate` to create the tables.

## Contributing

### Code style
Please respect the following rules:
* Indent all files with tabs (execpt for the yaml files which are indented with 2 spaces)
* One line break at the end of all files
* No trailing whitespace, including on empty lines
* Separate functions with 2 line breaks
* No more than 2 line breaks consecutively
* All function names must be in camelCase

### PHP
* Variables names must be in snake_case

### Javascript
* Variables names must be in camelCase

### HTML & mustache
* Class, id and input names must be in kebab-case

### Stylesheets
This project uses Less as the CSS preprocessor.

To compile, in PHPStorm, use the following arguments for the Less file watcher:

**Arguments:** `$FileDir$\$FileName$ $ContentRoot$\public\assets\css\$FileNameWithoutExtension$.css`

**Output path to refresh:** `$ContentRoot$\public\assets\css\$FileNameWithoutExtension$.css`
