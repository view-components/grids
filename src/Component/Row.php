<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Component\CompoundComponent;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Grids\Grid;

/**
 * Grid row with automatically generated columns based on grid columns configuration.
 */
class Row extends CompoundComponent
{
    public function __construct()
    {
        parent::__construct(
            [
                'tr' => []
            ]
            , [
            'tr' => new Tag('tr')
        ]);
    }

    /**
     * @return Tag
     */
    protected function getTr()
    {
        return $this->getComponent('tr');
    }

    /**
     * @param string $columnName
     * @return Tag
     */
    public function getCell($columnName)
    {
        $column = $this->getComponent('column-' . $columnName);
        return $column ?: $this->createColumn($columnName);
    }

    protected function createColumn($name)
    {
        $name = 'column-' . $name;
        $this->appendComponent('tr', $name, $column = new Tag('td'));
        return $column;

    }

    private function updateColumns()
    {
        /** @var Grid $grid */
        $grid = $this->parents()->findByType(Grid::class);
        if (!$grid) {
            return;
        }
        $i = 1;
        foreach ($grid->getColumns() as $column) {
            $this->getCell($column->getName())->setSortPosition($i);
            $i++;
        }
    }

    public function render()
    {
        $this->updateColumns();
        return parent::render();
    }
}