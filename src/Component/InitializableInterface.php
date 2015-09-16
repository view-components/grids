<?php
namespace Presentation\Grids\Component;

use Presentation\Grids\Grid;

interface InitializableInterface
{
    public function initialize(Grid $grid);

    public function isInitialized();
}
