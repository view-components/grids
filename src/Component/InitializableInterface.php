<?php
namespace Presentation\Grids\Component;

use Presentation\Grids\Grid;

interface InitializableInterface1
{
    /**
     * @param Grid $grid
     */
    public function initialize(Grid $grid);

    /**
     * @return bool
     */
    public function isInitialized();
}
