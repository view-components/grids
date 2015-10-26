<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Component\CompoundComponent;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Control\ControlInterface;
use Presentation\Framework\Control\FilterControl;
use Presentation\Framework\Control\PaginationControl;
use Presentation\Grids\Grid;

class ControlRowWithColumns extends CompoundComponent implements InitializableInterface
{
    use InitializableTrait;

    public function __construct()
    {
        parent::__construct(
            [
                'row' => []
            ]
            , [
            'row' => new Tag('tr')
        ]);
    }

    public function render()
    {
        /** @var Tag $row2 */
        $row2 = $this->components()->get('row2');
        if ($this->grid->controls()->isEmpty() && (!$row2 || $row2->children()->isEmpty())) {
            return '';
        } else {
            return parent::render();
        }
    }

    /**
     * @return Tag
     */
    protected function getRow()
    {
        return $this->components()->get('row');
    }

    /**
     * @return SolidRow
     */
    protected function requireSecondRow()
    {
        $row = $this->components()->get('row2');
        if (!$row) {
            $this->components()->set('row2', $row = new SolidRow());
            $tree = $this->getTreeConfig();
            $tree['row2'] = [];
            $this->setTreeConfig($tree);
        }
        return $row;
    }

    /**
     * @param string $name
     * @return Tag|null
     */
    protected function getColumn($name)
    {
        return $this->components()->get('column-' . $name);
    }

    protected function createColumns()
    {
        $tree = $this->getTreeConfig();

        foreach ($this->grid->getColumns() as $column) {
            $name = 'column-' . $column->getName();
            $tree['row'][$name] = [];
            $this->components()->set($name, new Tag('td'));
        }
        $this->setTreeConfig($tree);
    }

    /**
     * @param Grid $grid
     */
    protected function initializeInternal(Grid $grid)
    {
        $this->createColumns();

        $notPlaced = [];
        foreach ($grid->controls() as $control) {
            if ($control->getView()->parent()) {
                continue;
            }
            $fieldName = $this->resolveControlColumn($control);
            if ($column = $this->getColumn($fieldName)) {
                $column->addChild($control->getView());
            } else {
                $notPlaced[] = $control->getView();
            }
        }
        $submitButton = $grid->components()->getSubmitButton();
        if ($submitButton) {
            $notPlaced[] = $submitButton;
        }
        if (count($notPlaced) > 0) {
            $this->requireSecondRow()->setChildren($notPlaced);
        }
    }

    protected function resolveControlColumn(ControlInterface $control)
    {
        if ($control instanceof FilterControl || method_exists($control, 'getField')) {
            return $control->getField();
        }
        return null;
    }
}
