<?php
namespace Presentation\Grids\Component;

use Nayjest\Tree\NodeCollection;
use Presentation\Grids\Grid;

trait InitializableTrait1
{
    private $initialized = false;

    abstract protected function initializeInternal(Grid $grid);

    /** @var Grid */
    protected $grid;

    final public function initialize(Grid $grid)
    {
        if ($this->initialized) {
            return;
        }
        $this->grid = $grid;
        $this->initializeInternal($grid);
        $this->initialized = true;
    }

    final public function isInitialized()
    {
        return $this->initialized;
    }
//
//    public function render()
//    {
//        if ($grid = $this->parents()->findByType(Grid::class)) {
//            $this->initialize($grid);
//        }
//        return parent::render();
//    }
}
