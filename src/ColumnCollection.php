<?php

namespace Presentation\Grids;

use Nayjest\Collection\Extended\ObjectCollection;

class ColumnCollection extends ObjectCollection
{
    protected $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @param Column $item
     * @param bool|false $prepend
     * @return $this
     */
    public function add($item, $prepend = false)
    {
        parent::add($item, $prepend);
        $item->setGridInternal($this->grid);
        $this->updateGridInternal();
        return $this;
    }

    public function updateGridInternal()
    {
        $grid = $this->grid;
        /** @var Column[] $columns */
        $columns = $this->items();
        foreach($columns as $column) {
            $grid->compose('title_row', 'column_' . $column->getName() . '_title', $column->getTitleCell());
            $grid->compose('record_view', 'column_' . $column->getName() . '_data_cell', $column->getDataCell());
        }
    }
}
