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

    private $isUpdateRunning = false;

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
//
//    protected function addColumnToGrid(Column $column)
//    {
//        $grid = $this->grid;
//        $titleComponentName = 'column_' . $column->getName() . '_title';
//        $grid->appendComponent(
//                'title_row',
//                $titleComponentName,
//                $column->getTitleCell()
//        );
//        $dataCellComponentName = 'column_' . $column->getName() . '_data_cell';
//        $grid->appendComponent(
//                'title_row',
//                $dataCellComponentName,
//                $column->getDataCell()
//        );
//    }
    /**
     * Updates grid structure with columns.
     */
    public function updateGridInternal()
    {
        if ($this->isUpdateRunning) {
            return;
        }
        $this->isUpdateRunning = true;
        $grid = $this->grid;
        /** @var Column[] $columns */
        $columns = $this->items();
        foreach($columns as $column) {
            $titleComponentName = 'column_' . $column->getName() . '_title';
            if ($grid->hasComponent($titleComponentName)) {
                $grid->setComponent($titleComponentName, $column->getTitleCell());
            } else {
                $grid->appendComponent(
                    'title_row',
                    $titleComponentName,
                    $column->getTitleCell()
                );
            }

            $dataCellComponentName = 'column_' . $column->getName() . '_data_cell';
            if ($grid->hasComponent($dataCellComponentName)) {
                $grid->setComponent($dataCellComponentName, $column->getDataCell());
            } else {
                $grid->appendComponent(
                    'record_view',
                    $dataCellComponentName,
                    $column->getDataCell()
                );
            }
        }
        $this->isUpdateRunning = false;
    }
}
