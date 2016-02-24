# Grids

Data grids library for PHP

Project status: **alpha** *since 2016-02-24*

This project is a successor of [nayjest/grids](https://github.com/Nayjest/Grids) library for Laravel.

Now Grids is framework-agnostic.

Bindings for app. frameworks:
* Laravel: alpha
* Symfony 2/3: planned
* Yii 2: planned
* ZF 2: planned

## Demo Application

1) Clone this repository
```bash
git clone https://github.com/view-components/grids.git
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
