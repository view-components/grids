<?php

namespace Presentation\Grids;

use Nayjest\Collection\Extended\ObjectCollection;

/**
 * Collection of grid columns.
 */
class ColumnCollection extends ObjectCollection
{
    /** @var Grid  */
    protected $grid;

    /**
     * ColumnCollection constructor.
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Adds column to collection.
     *
     * This method is overriden to update grid structure after adding new column.
     *
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

    /**
     * Updates grid structure with columns.
     */
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
