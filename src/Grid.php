<?php
namespace Presentation\Grids;

use Presentation\Framework\Component\CompoundComponent;
use Presentation\Framework\Component\Text;
use Presentation\Framework\Control\ControlCollection;
use Presentation\Framework\Control\ControlInterface;
use Presentation\Framework\Control\PaginationControl;
use Presentation\Framework\Data\DataProviderInterface;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Component\InitializableInterface;
use Presentation\Grids\Layout\ControlPlacingStrategy\ControlPlacingStrategyInterface;

class Grid extends CompoundComponent
{
    private $isOperationsApplied = false;

    /** @var Column[] */
    protected $columns = [];

    /** @var ControlCollection */
    protected $controlCollection;

    /** @var  DataProviderInterface */
    protected $dataProvider;

    /** @var  InputSource */
    protected $inputSource;

    protected $defaults;

    public function __construct()
    {
        $this->controlCollection = new ControlCollection();
        // @todo instantiate defaults only by request (not in constructor)
        parent::__construct($this->getDefaults()->tree(), $this->getDefaults()->getComponents());

    }

    protected function getDefaults()
    {
        if (!$this->defaults) {
            $this->defaults = new Defaults();
        }
        return $this->defaults;
    }

    /**
     *
     * @return Registry
     */
    public function components()
    {
        return parent::components();
    }

    /**
     * @return DataProviderInterface|null
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @param DataProviderInterface|null $dataProvider
     * @return $this
     */
    public function setDataProvider(DataProviderInterface $dataProvider = null)
    {
        $this->dataProvider = $dataProvider;
        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @return ControlInterface[]|ControlCollection
     */
    public function controls()
    {
        return $this->controlCollection;
    }

    public function setControls($controls)
    {
        $this->controls()->set($controls);
        return $this;
    }


    /**
     * @return InputSource|null
     */
    public function getInputSource()
    {
        if ($this->inputSource === null) {
            $this->inputSource = $this->getDefaults()->inputSource();
        }
        return $this->inputSource;
    }

    /**
     * @param InputSource|null $inputSource
     * @return $this
     */
    public function setInputSource(InputSource $inputSource = null)
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    protected function makeComponentRegistry(array $components = [])
    {
        return new Registry($components);
    }

    protected function initializeCollection(array $items)
    {
        parent::initializeCollection($items);
        $this->initializeComponents();
    }

    protected function initializeComponents()
    {
        foreach ($this->getChildrenRecursive() as $component) {
            if ($component instanceof InitializableInterface) {
                $component->initialize($this);
            }
        }
    }

    protected function placePagination()
    {
        /** @var PaginationControl $pagination */
        $pagination = $this->controls()->filterByType(PaginationControl::class)->first();
        if ($pagination && !$pagination->getView()->parent()) {
            $container = $this->components()->get('pagination_container');
            if ($container) {
                $pagination->getView()->attachTo($container);
            }
        }
    }
    protected function buildTree()
    {
        $this->applyOperations();
        $components = $this->components();
        $components->getTableRow()->setTCell($components->getDataCell());
        //$components->getTitleRow()->setTCell($components->getTitleCell());
        $components->getDataRowRepeater()->setIterator($this->dataProvider);
        $components->getBodyColumnRepeater()->setIterator($this->getColumns());
        $components->getHeadingColumnRepeater()->setIterator($this->getColumns());
        $this->placePagination();


        $components->getHeadingColumnRepeater()->setCallback(function($repeater, Column $column) use ($components) {
            $components->getTitleCell()->setChildren([new Text($column->getLabel())]);
        });

        return parent::buildTree();
    }

    public function applyOperations()
    {
        if (!$this->isOperationsApplied) {
            $this->controlCollection->applyOperations($this->dataProvider);
            $this->isOperationsApplied = true;
        }
    }
}
