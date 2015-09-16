<?php
namespace Presentation\Grids;

use Nayjest\Tree\NodeTrait;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\ComponentTrait;
use Presentation\Framework\Rendering\ViewTrait;
use Presentation\Grids\Component\InitializableInterface;

class Grid implements ComponentInterface
{
    use NodeTrait {
        NodeTrait::initializeCollection as private origInitializeCollection;
    }
    use ViewTrait;
    use ComponentTrait;

    /** @var GridConfig */
    protected $config;

    private $isOperationsApplied = false;

    public function __construct(GridConfig $config)
    {
        $this->config = $config;
        $defaults = new ConfigDefaults();
        $defaults->apply($config);
    }

    protected function initializeCollection(array $items)
    {
        $this->origInitializeCollection($items);
        $this->initializeComponents();
    }

    protected function initializeComponent(ComponentInterface $component)
    {
        if ($component instanceof InitializableInterface) {
            $component->initialize($this);
        }
        if ($component->children()->isWritable()) {
            $component->children()->onItemAdd(function($item) {
                $this->initializeComponent($item);
            });
        }
    }

    protected function initializeComponents()
    {
        foreach($this->getChildrenRecursive() as $component) {
            $this->initializeComponent($component);
        }
    }

    /**
     * @return GridConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    protected function defaultChildren()
    {
        $this->applyOperations();
        return $this->makeComponents();
    }

    protected function makeComponents()
    {
        $composer = new GridComposer();
        return $composer->compose($this->config);
    }

    public function applyOperations()
    {
        if (!$this->isOperationsApplied) {
            $this->config->getControls()->applyOperations(
                $this->config->getDataProvider()
            );
            $this->isOperationsApplied = true;
        }
    }
}
