<?php

namespace Presentation\Grids\Component;

use Nayjest\Collection\Extended\ObjectCollection;
use Presentation\Grids\Grid;

trait GridComponentTrait
{
    /**
     * @return ObjectCollection
     */
    abstract protected function parents();

    /**
     * @return \Presentation\Grids\GridConfig
     */
    public function getGridConfig()
    {
        /** @var Grid $grid */
        $grid = $this->parents()->findByType(Grid::class);
        return $grid->getConfig();
    }
}
