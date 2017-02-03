 ![Logo](https://raw.githubusercontent.com/view-components/logo/master/view-components-logo-without-text-42.png) Grids
=====

[![Release](https://img.shields.io/packagist/v/view-components/grids.svg)](https://packagist.org/packages/view-components/grids)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/view-components/grids/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/view-components/grids/?branch=master)
[![Build Status](https://travis-ci.org/view-components/grids.svg?branch=master)](https://travis-ci.org/view-components/grids)
[![Code Coverage](https://scrutinizer-ci.com/g/view-components/grids/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/view-components/grids/?branch=master)

### `Flexible Framework-agnostic Data Grids for PHP`

##### This project is a successor of [nayjest/grids](https://github.com/Nayjest/Grids) (Data Grids Framework for Laravel).

This package is framwork-agnostic in sense of both backend and frontend frameworks, i.e.:
 * You can use it with any PHP framework or without it. Integration packages will help you to use facilities of popular PHP frameworks.
 * Don't worry about fitting markup to your favorite CSS framework. Just describe grid structure and add one of available customizations that will apply framework-specific modifications to markup.

Project status: **beta** *since 2016-03-31*

**Have questions? [Ask in issue-tracker](https://github.com/view-components/grids/issues/new)**.

**Screenshot:**

![screenshot](https://i.gyazo.com/be64c5acebb7982bea9fb6fdff5586e2.png)

## Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Installing into Existing Project](#installing-into-existing-project)
  - [Installing as Stand-alone Project](#installing-as-stand-alone-project)
- [Integrations](#integrations)
- [Usage](#usage)
- [Demo Application](#demo-application)
  - [Working Demo Deployed to Heroku](#working-demo-deployed-to-heroku)
  - [Running Demo Application Locally](#running-demo-application-locally)
- [Documentation](#documentation)
- [Testing](#testing)
- [Contributing](#contributing)
- [Security](#security)
- [License](#license)

## Features
* Flexible component architecture
* Can be used with wide variety of data sources (php array, PDO database connection, Laravel ActiveRecord or QueryBuilder, Doctrine DBAL Query builder, etc)
* Themes / Customizations / Styling
* Can be used with any PHP framework, has integration packages for popular frameworks.
* Don't worry about fitting markup for your favorite CSS framework. Just describe grid structure and components and then apply customization. This package is framework-agnostic in sense of frontend frameworks and bundled with themes for Twitter Bootstrap, Foundation, Semantic UI
* Alot of components ready to use: filters, sorting, totals, row details, pagination, CSV export 
* User-friendly for developers (documentation in progress)


## Requirements

* PHP 5.5+ (hhvm & php7 are supported)

## Installation

### Installing into Existing Project

The recommended way of installing the component is through [Composer](https://getcomposer.org).

Run following command from your project folder:

```bash
composer require view-components/grids
```
#### Add-ons

If you use Laravel, install also [eloquent-data-processing](https://github.com/view-components/eloquent-data-processing) package.

```bash
composer require view-components/eloquent-data-processing
```
This will give possibility to use Eloquent models and query builder instances as data source for grid.

For Doctrine users, there is [doctrine-data-processing](https://github.com/view-components/doctrine-data-processing) package available.

```bash
composer require view-components/doctrine-data-processing
```

### Installing as Stand-alone Project

For running tests and demo-application bundled with this package on your system you need to install it as stand-alone project.

```
composer create-project view-components/grids
```

This is the equivalent of doing a git clone followed by a "composer install" of the vendors.
Composer will automatically run 'post-create-project-cmd' command and that will call interactive installation.

If you want to use default settings and run it silently, just add `--no-interaction` option.

If you already cloned this repository, or you want to reinstall the package, navigate to the package folder and run `composer create-project` command without specifying package name.

If you are sure that you don't need to reinstall composer dependencies, you can execute only bundled installer: `composer run post-create-project-cmd`

This kind of installation has additional requirements:
* ext-curl
* ext-pdo_sqlite

## Integrations

 Area | Framework | Component | Package | Status
 --- | --- | --- | --- | ---
 Backend | Laravel(Eloquent) | Eloquent Data Provider | [view-components/eloquent-data-processing](https://github.com/view-components/eloquent-data-processing) | Ready, Stable
 Backend | Laravel(Blade) | Blade Renderer |  | Planned
 Backend | Symfony(Twig) | Twig Renderer |  | Planned
 Backend | Zend Framework 2/3 | * |  | Planned
 Backend | Yii 2 | * |  | Planned
 Backend | Doctrine(DBAL) | Doctrine(DBAL) Data Provider | [view-components/doctrine-dbal-processing](https://github.com/view-components/doctrine-data-processing) | Ready, Beta
  Backend | Any | PHP Array Data Provider | Bundled | Ready, Unstable 
  Backend | Any | PDO Data Provider | Bundled | Ready, Unstable 
  Frontend | Twitter Bootstrap | Bootstrap View Customization | Bundled | Ready, Beta 
  Frontend | Foundation by ZURB | Foundation View Customization | Bundled | Ready, Unstable
  Frontend | Semantic UI | Semantic UI View Customization | Bundled | Ready, Unstable
  Frontend | Yahoo Pure | Yahoo Pure View Customization |  | Planned
  Frontend | UIKit | UIKit View Customization |  | Planned

## Usage

Basic example:
```php
$provider = new DbTableDataProvider($pdoConnection, 'my_table');
$input = new InputSource($_GET);

// create grid
$grid = new Grid(
    $provider,
    // all components are optional, you can specify only columns
    [
        new TableCaption('My Grid'),
        new Column('id'),
        new Column('name'),
        new Column('role'),
        new Column('birthday'),
        (new Column('age'))
            ->setValueCalculator(function ($row) {
                return DateTime
                    ::createFromFormat('Y-m-d', $row->birthday)
                    ->diff(new DateTime('now'))
                    ->y;
            })
            ->setValueFormatter(function ($val) {
                return "$val years";
            })
        ,
        new DetailsRow(new SymfonyVarDump()), // when clicking on data rows, details will be shown
        new PaginationControl($input->option('page', 1), 5), // 1 - default page, 5 -- page size
        new PageSizeSelectControl($input->option('page_size', 5), [2, 5, 10]), // allows to select page size
        new ColumnSortingControl('id', $input->option('sort')),
        new ColumnSortingControl('birthday', $input->option('sort')),
        new FilterControl('name', FilterOperation::OPERATOR_LIKE, $input->option('name')),
        new CsvExport($input->option('csv')), // yep, that's so simple, you have CSV export now
        new PageTotalsRow([
            'id' => PageTotalsRow::OPERATION_IGNORE,
            'age' => PageTotalsRow::OPERATION_AVG
        ])
    ]
);

// now you can render it:
echo $grid->render();
// or even this way:
echo $grid;

//  but also you can add some styling:
$customization = new BootstrapStyling();
$customization->apply($grid);
echo $grid;
```
## Demo Application

This package bundled with demo-application.

Source code of demos available [here](https://github.com/view-components/grids/blob/master/tests/webapp/Controller.php)

### Working Demo Deployed to Heroku

Travis CI automatically deploys web-application bundled with this package to Heroku.

Here you can see working demos: <http://vc-grids.herokuapp.com/>

*First run may be slow because Heroku shutting down workers when there is no traffic and starts it again on first visit*

### Running Demo Application Locally

To run it locally, you must install this package as stand-alone project with dev-dependencies.

Then, run web-server from the package directory with the following command:

```
composer serve
```
This command uses web-server bundled with PHP.

Now, open [http://localhost:8000](http://localhost:8000) in the browser (for Windows users it will be opened automatically after starting web-server).

## Documentation
* [Grids Cookbook](https://github.com/view-components/grids/blob/master/doc/cookbook.md)
* [ViewComponents / Components Overview](https://github.com/view-components/view-components/blob/master/doc/components.md)
* [ViewComponents / Cookbook](https://github.com/view-components/view-components/blob/master/doc/cookbook.md)


## Testing

This application bundled with unit and acceptance tests created with PHPUnit.

To run tests locally, you must install this package as stand-alone project with dev-dependencies.

Command for running unit and acceptance tests:

```bash
composer test
```

Command for checking code style:

```bash
composer cs
```

## Contributing

Please see [Contributing Guidelines](contributing.md) and [Code of Conduct](code_of_conduct.md) for details.

## Security

If you discover any security related issues, please email mail@vitaliy.in instead of using the issue tracker.

## License

© 2015—2016 Vitalii Stepanenko

Licensed under the MIT License.

Please see [License File](LICENSE) for more information.
