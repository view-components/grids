# Grids

Data grids library for PHP

Project status: pre-alpha.

This project is a successor of [nayjest/grids](https://github.com/Nayjest/Grids) library for Laravel.

Now Grids is framework-agnostic in sense of application framework.
It's based on PHP UI framework called [Presentation Framework](https://github.com/presentation-framework/presentation-framework) (that's actually also in pre-alpha status).

Bindings for app. frameworks like Laravel, Symfony, Yii, etc. will be available later.

## Demo Application

1) Clone this repository
```bash
git clone https://github.com/presentation-framework/grids.git
cd grids
```
2) Install [Composer](https://getcomposer.org/) if it's not installed

3) Install Composer dependencies
 ```bash
 composer install
 ```

4) Create .env file
```bash
cp tests/.env.example tests/.env

```
5)  Start built-in php web server
```bash
php -S localhost:9000 tests/webapp/index.php
```
Do not close terminal.

6) Now open [http://localhost:9000/](http://localhost:9000/) in browser

## Testing

#### Overview

The package bundled with phpunit tests and web-application for integration/acceptance tests using codeception.

#### Running Unit Tests

Just execute phpunit from package folder.

```bash
phpunit
```
Package dependencies must be installed via composer (just run composer install).


## License

© 2015—2016 Vitalii Stepanenko

Licensed under the MIT License.

Please see [License File](LICENSE) for more information.
