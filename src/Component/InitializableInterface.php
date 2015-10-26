<?php
namespace Presentation\Grids\Component;

use Presentation\Grids\Grid;

interface InitializableInterface
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
