<?php

namespace Presentation\Grids\Component;

use Nayjest\Tree\NodeTrait;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\ComponentTrait;
use Presentation\Framework\Event\CallbackObserver;
use Presentation\Framework\Rendering\ViewTrait;
use Presentation\Grids\Grid;

class PageTotalsRow implements ComponentInterface, InitializableInterface
{
    use NodeTrait;
    use ComponentTrait;
    use ViewTrait;
    use InitializableTrait;

    protected $totalData;

    protected $cellObserver;

    protected function pushData($field, $value)
    {
        if ($this->totalData === null) {
            $this->totalData = new \stdClass();
        }
        if (!is_numeric($value)) {
            return;
        }
        if (property_exists($this->totalData, $field)) {
            $this->totalData->$field += $value;
        } else {
            $this->totalData->$field = $value;
        }
    }

    /**
     * @param Grid $grid
     */
    protected function initializeInternal($grid)
    {

        $components = $grid->components();
        // Observer will collect data from table cells.
        $this->cellObserver = new CallbackObserver(function (TCell $cell) {
            $value = $cell->extractData();
            $field = $cell->getCurrentColumn()->getName();
            $this->pushData($field, $value);
        });
        $components->getDataCell()->beforeRender()->attach($this->cellObserver);

        // Before rendering totals row:
        // 1. Stop collecting data from cells
        // 2. Push collected data to table row and attach it to PageTotalsRow component for rendering.
        $this->beforeRender()->attachCallback(function () use ($components) {
            $components->getDataCell()->beforeRender()->detach($this->cellObserver);
            $components->getTableRow()
                ->setData($this->totalData)
                ->attachTo($this);
        });
    }
}
