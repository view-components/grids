<?php
namespace Presentation\Grids;

use Presentation\Framework\Base\RepeaterInterface;
use Presentation\Framework\Component\CompoundComponent;
use Presentation\Framework\Control\ControlCollection;
use Presentation\Framework\Control\ControlInterface;
use Presentation\Framework\Control\PaginationControl;
use Presentation\Framework\Data\DataProviderInterface;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Component\InitializableInterface;

class Grid extends CompoundComponent
{
    private $isOperationsApplied = false;

    /** @var ControlCollection */
    protected $controlCollection;

    /** @var  DataProviderInterface */
    protected $dataProvider;

    /** @var  InputSource */
    protected $inputSource;

    protected $defaults;

    protected $currentRow;

    /** @var ColumnCollection|Column[]  */
    protected $columnCollection;

    public function __construct()
    {
        $this->controlCollection = new ControlCollection();
        $this->columnCollection = new ColumnCollection($this);
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
     * @return mixed
     */
    public function getCurrentRow()
    {
        return $this->currentRow;
    }

    /**
     * @param mixed $currentRow
     */
    public function setCurrentRow($currentRow)
    {
        $this->currentRow = $currentRow;
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
     * @return Column[]|ColumnCollection
     */
    public function getColumns()
    {
        return $this->columnCollection;
    }

    /**
     * @param Column[] $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columnCollection->set($columns);
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
        $this->placePagination();
        $this->columnCollection->updateGridInternal();
        $components->getRepeater()
            ->setIterator($this->dataProvider)
            ->setCallback(function(RepeaterInterface $repeater, $dataRow){
                $this->setCurrentRow($dataRow);
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
