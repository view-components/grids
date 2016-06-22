Grids Cookbook
==============
## Table of contents

@todo

# Displaying links, images, etc. inside table cells

`Column::setValueFormatter(callable $valueFormatter)` allows to specify function for formatting data before printing.

$valueFormatter will accept cell value extracted from data row as first argument.

Custom value formatter can be used to render links, image tags, etc.

See [Column class](https://github.com/view-components/grids/blob/master/src/Component/Column.php).

Example:
```php
$grid = new Grid($provider, [
    new Column('id'),
    new Column('name'),
    (new Column('img_file', 'Image'))->setValueFormatter(function($value) {
        return "<img src = '/images/$value'/>";
    }),
]);
```

Sometimes it can be useful to access to data row when rendering cell content.
For example: you want to render html links in table, but URIs stored in one column and titles in another.
For this purpose $valueFormatter accepts also entire data row in second argument.

```php
Example:
$column->setValueFormatter(function($name, $row) {
    return "<a href='/user-profile/{$row->id}'>{$name}</a>";
});
```


# Modifying table cells

It's possible to access data cell & title cell components from column.
`Data cell component` renders 'TD' tags for associated column in each row and `title cell component` renders 'TH' tag in table header for associated column.
By default there are instances of [Tag](https://github.com/view-components/view-components/blob/master/src/Component/Html/Tag.php) component,
but they can be replaced to any component implementing [ContainerComponentInterface](https://github.com/view-components/view-components/blob/master/src/Base/ContainerComponentInterface.php). 

Example 1: Using Tag::setAttribute() for adding class to table cell
```php
$column->getDataCell()->setAttribute('class', 'my-class');
```

Example 2: Replacing title cell

```php
$column->setTitleCell(new Tag('th', ['class' => 'my-table-header']));
```

Example 3: Making clickable TD with link
```php
$columns = [
    'id' => new Column('id'),
    'name' => new Column('name')
];
$grid = new Grid($provider, $columns);
$columns['name']
    ->setValueFormatter(function ($name, $row) {
        return "<a href='/user-profile/{$row->id}'>{$name}</a>";
    })
    ->getDataCell()
    ->setAttribute('onclick', 'window.location = $(this).find(\'a\').attr(\'href\'));')
    ->setAttribute('style', 'cursor:pointer');
```
