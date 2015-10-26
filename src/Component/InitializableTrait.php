<?php
namespace Presentation\Grids\Component;

use Nayjest\Tree\NodeCollection;
use Presentation\Grids\Grid;

trait InitializableTrait
{

    private $initialized = false;

    abstract protected function initializeInternal($grid);

    /** @var Grid */
    protected $grid;

    /**
     * @return NodeCollection
     */
    abstract protected function children();

    final public function initialize($grid)
    {
        if ($this->initialized) {
            return;
        }
        $this->grid = $grid;
        $this->enableDeferredInitialization($grid);
        $this->initializeInternal($grid);
        $this->initialized = true;
    }

    final protected function enableDeferredInitialization($grid)
    {
        $collection = $this->children();
        if (!$collection->isWritable()) {
            $collection = $this->collection;
        }
        $collection->onItemAdd(function($item) use($grid) {
            if ($item instanceof InitializableInterface) {
                $item->initialize($grid);
            }
        });
    }

    final public function isInitialized()
    {
        return $this->initialized;
    }
}
