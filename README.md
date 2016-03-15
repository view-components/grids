# Grids

[![Release](https://img.shields.io/packagist/v/view-components/view-components.svg)](https://packagist.org/packages/view-components/grids)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/view-components/grids/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/view-components/grids/?branch=master)
[![Build Status](https://travis-ci.org/view-components/grids.svg?branch=master)](https://travis-ci.org/view-components/grids)
[![Code Coverage](https://scrutinizer-ci.com/g/view-components/grids/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/view-components/grids/?branch=master)


**Data grids for PHP**

Project status: **alpha** *since 2016-02-24*

This project is a successor of [nayjest/grids](https://github.com/Nayjest/Grids).

It's framwork-agnostic in sense of both backend and frontend frameworks, i. e.:
 * You can use it with any PHP Framework. Integration packages are also available.
 * Don't worry about fitting markup for your favorite CSS framework. Just describe grid with all required components and then apply customization

### Bindings / Integrations

 Area | Framework | Component | Package | Status
 --- | --- | --- | --- | ---
 Backend | Laravel(Eloquent) | Eloquent Data Provider | [view-components/eloquent-data-processing](https://github.com/view-components/eloquent-data-processing) | Stable
 Backend | Laravel(Blade) | Blade Renderer |  | Planned
 Backend | Symfony(Twig) | Twig Renderer |  | Planned
 Backend | Zend Framework 2/3 | * |  | Planned
 Backend | Yii 2 | * |  | Planned
 Backend | Doctrine(DBAL) | Doctrine(DBAL) Data Provider | [view-components/doctrine-dbal-data-processing](https://github.com/view-components/doctrine-dbal-data-processing) | In Progress
  Backend | Any | PHP Array Data Provider | Bundled | Ready, Unstable 
  Backend | Any | PDO Data Provider | Bundled | Ready, Unstable 
  Frontend | Twitter Bootstrap | Bootstrap View Customization | Bundled | Ready, Unstable 
  Frontend | Foundation by ZURB | Foundation View Customization | Bundled | In Progress
  Frontend | Semantic UI | Semantic UI View Customization |  | Planned
  Frontend | Yahoo Pure | Yahoo Pure View Customization |  | Planned
  Frontend | UIKit | UIKit View Customization |  | Planned
  

## Requirements

* PHP 5.5+ (hhvm & php7 are supported)
* Some components requires [ext_intl](http://php.net/manual/en/book.intl.php) ([bundled with PHP](http://php.net/manual/en/intl.installation.php) as of PHP 5.3.0)
* ext_curl required for running package tests

## Installation

The recommended way of installing the component is through [Composer](https://getcomposer.org).

Run following command:

```bash
composer require view-components/grids
```
## Usage

Basic example:
```php
$provider = new DbTableDataProvider($pdoConnection, 'my_table');
$input = $input = new InputSource($_GET);

// create grid
$grid = new Grid(
    $provider,
    // all components is optional, you can specify only columns
    [
        new TableCaption('My Grid')
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

## Testing

#### Overview

The package bundled with phpunit tests and web-application for integration/acceptance tests.

#### Running Tests

1) Clone this repository and navigate to created folder

2) Run installation

```
composer install
```

3) Run tests

```
composer test
```


#### Running demo application

1) Clone this repository and navigate to created folder

2) Run composer installation

```
composer install
```

3) Run web-server

```
composer serve
```

4) Open [http://localhost:8000](http://localhost:8000) in browser

## License

© 2015—2016 Vitalii Stepanenko

Licensed under the MIT License.

Please see [License File](LICENSE) for more information.
