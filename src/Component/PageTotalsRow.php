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
    use ComponentTrait {
        ComponentTrait::render as private renderInternal;
    }
    use ViewTrait;
    use InitializableTrait;

    protected $totalData;

    protected $cellObserver;

    public function render()
    {
        // clone totalData to avoid pushing data to totals one more time
        $this->grid->setCurrentRow(clone $this->totalData);
        $this->grid->components()->getTableRow()->attachTo($this);
        return $this->renderInternal();
    }

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
    protected function initializeInternal(Grid $grid)
    {
        // @todo will not work if replace TR after adding PageTotalsRow
        $grid->components()->getTableRow()->onRender(function(){
            foreach($this->grid->getColumns() as $column) {
                $this->pushData($column->getDataFieldName(), $column->extractValueFromCurrentRow());
            }
        });
    }
}
