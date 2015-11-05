<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Component\CompoundComponent;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Grids\Grid;

/**
 * Grid row with automatically generated columns based on grid columns configuration.
 */
class Row extends CompoundComponent implements InitializableInterface
{
    use InitializableTrait;

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
        return $this->components()->get('tr');
    }

    /**
     * @param string $columnName
     * @return Tag
     */
    public function getCell($columnName)
    {
        $column = $this->components()->get('column-' . $columnName);
        return $column ?: $this->createColumn($columnName);
    }

    protected function createColumn($name)
    {
        $tree = $this->getTreeConfig();
        $name = 'column-' . $name;
        if (!array_key_exists($name, $tree['tr'])) {
            $tree['tr'][$name] = [];
        }
        $column = new Tag('td');
        $this->components()->set($name, $column);
        $this->setTreeConfig($tree);
        return $column;

    }

    protected function createCells()
    {
        $i = 1;
        foreach ($this->grid->getColumns() as $column) {
            $this->getCell($column->getName())->setSortPosition($i);
            $i++;
        }
    }

    /**
     * @param Grid $grid
     */
    protected function initializeInternal(Grid $grid)
    {
        $this->createCells();
    }
}