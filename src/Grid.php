<?php

namespace Presentation\Grids;

use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Component\ManagedList\Control\ControlInterface;
use Presentation\Framework\Component\ManagedList\ManagedList;
use Presentation\Framework\Data\DataProviderInterface;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Component\InitializableInterface;
use Traversable;

/**
 * Grid component.
 */
class Grid extends ManagedList
{
    /** @var  InputSource */
    protected $inputSource;

    /** @var  mixed|null */
    protected $currentRow;

    /** @var ColumnCollection|Column[] */
    protected $columnCollection;

    /**
     * Grid constructor.
     *
     * @param DataProviderInterface|null $dataProvider
     * @param Column[] $columns empty by default
     */
    public function __construct($dataProvider = null, array $columns = [])
    {
        parent::__construct($dataProvider);
        $this->columnCollection = new ColumnCollection($this);
        $this->setColumns($columns);
    }

    /**
     * Returns default grid structure.
     *
     * @return array
     */
    public function getDefaultTreeConfig()
    {
        return [
            'container' => [
                'form' => [
                    'table' => [
                        'table_heading' => [
                            'title_row' => [
                            ],
                            'control_row' => [
                                'submit_button' => []
                            ]

                        ],
                        'table_body' => [
                            'repeater' => [
                                'record_view' => [
                                ]
                            ]
                        ],
                        'table_footer' => [
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Returns current grid row, used internally for grid rendering.
     *
     * @internal
     * @return mixed
     */
    public function getCurrentRow()
    {
        return $this->currentRow;
    }

    /**
     * Sets current grid row for rendering, used internally for grid rendering.
     *
     * @internal
     * @param mixed $currentRow
     * @return $this
     */
    public function setCurrentRow($currentRow)
    {
        $this->currentRow = $currentRow;
        return $this;
    }

    /**
     * Returns registry of compound components.
     *
     * @return Registry|ComponentInterface[]
     */
    public function components()
    {
        return parent::components();
    }

    /**
     * Returns collection of grid columns.
     *
     * @return Column[]|ColumnCollection
     */
    public function getColumns()
    {
        return $this->columnCollection;
    }

    /**
     * Sets grid columns
     *
     * @param Column[]|Traversable $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columnCollection->set($columns);
        return $this;
    }

    /**
     * Creates components registry.
     *
     * @param array $components
     * @return Registry
     */
    protected function makeComponentRegistry(array $components = [])
    {
        return new Registry($components, $this);
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

    protected function buildTree()
    {
        $tree = parent::buildTree();
        $this->components()->getRepeater()->setCallback([$this, 'setCurrentRow']);
        $this->manageControlRow();
        return $tree;

    }

    /**
     * Hides submit button if control row doesn't contains controls.
     * Then hides control row if there is no visible components.
     */
    protected function manageControlRow()
    {
        $controlRow = $this->components()->getControlRow();
        if ($controlRow) {
            $submitButton = $this->components()->getSubmitButton();
            $children = $controlRow->getChildrenRecursive();
            if ($submitButton && $children->filterByType(ControlInterface::class)->isEmpty()) {
                $submitButton->hide();
            }
            if ($children->filterByProperty('visible', true, true)->isEmpty()) {
                $controlRow->hide();
            }
        }
    }
}
