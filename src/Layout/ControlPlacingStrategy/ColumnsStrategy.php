<?php

namespace Presentation\Grids\Layout\ControlPlacingStrategy;

use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Component\ControlView\FilterControlView;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Control\ControlCollection;
use Presentation\Framework\Control\ControlInterface;
use Presentation\Framework\Control\FilterControl;
use Presentation\Grids\Column;
use Presentation\Grids\GridConfig;

class ColumnsStrategy implements ControlPlacingStrategyInterface
{

    public function placeControls(GridConfig $config)
    {
        $controls = $config->getControls();
        if ($controls->isEmpty()) {
            return;
        }

        $columns = $config->getColumns();

        $row = new Tag('tr');
        $config->getTHead()->addChild($row);

        $controlsGrouped = $this->resolveControlColumns($columns, $controls);
        $cells = $this->createCells($columns);
        foreach ($cells as $columnName => $cell) {
            $cell->attachTo($row);
            /** @var ControlInterface $control */
            foreach ($controlsGrouped[$columnName] as $control) {
                $view = $control->getView();
                $view->attachTo($cell);
                $this->removeLabel($view);

            }
        }

        $isSolidRowRequired = false;
        if (!empty($controlsGrouped[null])) {
            // has not placed some controls
            $isSolidRowRequired = true;
        }

        /** @var Column $lastColumn */
        $lastColumn = end($columns);
        $lastGroup = $controlsGrouped[$lastColumn->getName()];
        $submit = $config->getSubmitButton();
        if ($submit && !$submit->parent() && count($lastGroup) === 0) {
            $lastCell = end($cells);
            $submit->attachTo($lastCell);
        } else {
            // has not placed submit btn
            $isSolidRowRequired = true;
        }

        if ($isSolidRowRequired) {
            $strategy = new SolidRowStrategy();
            $strategy->placeControls($config, true);
        }
    }

    /**
     * @param Column[] $columns
     * @return array|ComponentInterface[] (column_name => ComponentInterface)
     */
    protected function createCells($columns)
    {
        $cells = [];
        foreach ($columns as $column) {
            $cell = new Tag('th');
            $cell->setComponentName('controls_row_' . $column->getName() . '_cell');
            $cells[$column->getName()] = $cell;
        }
        return $cells;
    }

    protected function resolveControlColumn(ControlInterface $control)
    {

        if ($control instanceof FilterControl || method_exists($control, 'getOperation')) {
            return $control->getField();
        }
        return null;
    }

    /**
     * @param Column[] $columns
     * @param ControlCollection|ControlInterface[] $controls
     * @return array|ControlInterface[] (column_name => ControlInterface)
     */
    protected function resolveControlColumns($columns, ControlCollection $controls)
    {
        $res = [
            null => []
        ];

        foreach ($columns as $column) {
            $res[$column->getName()] = [];
        }
        foreach ($controls as $control) {
            $columnName = $this->resolveControlColumn($control);
            if (array_key_exists($columnName, $res)) {
                $res[$columnName][] = $control;
            } else {
                if (!$control->getView()->parent()) {
                    $res[null][] = $control;
                }
            }
        }
        return $res;
    }

    /**
     * @param ComponentInterface $controlView
     */
    protected function removeLabel($controlView)
    {
        if ($controlView instanceof FilterControlView) {
            $label = $controlView
                ->children()
                ->findByProperty('tagName', 'label', true);
            if ($label) {
                $label->detach();
            }
        }
    }
}