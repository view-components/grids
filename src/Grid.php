<?php
namespace Presentation\Grids;

use Presentation\Framework\Component\ManagedList\Control\PaginationControl;
use Presentation\Framework\Component\ManagedList\ManagedList;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Component\InitializableInterface;

class Grid extends ManagedList
{
    /** @var  InputSource */
    protected $inputSource;

    protected $currentRow;

    /** @var ColumnCollection|Column[] */
    protected $columnCollection;

    public function __construct($dataProvider = null, array $columns = [])
    {
        parent::__construct($dataProvider);
        $this->columnCollection = new ColumnCollection($this);
        $this->setColumns($columns);
    }

    public function getDefaultTreeConfig()
    {
        return [
            'container' => [
                'form' => [
                    'table' => [
                        'table_heading' => [
                            'title_row' => [
                            ],
                            'control_row_hider' => [
                                'control_row' => [
                                    'submit_button' => []
                                ]
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
        $this->components()->getRepeater()->setCallback([$this, 'setCurrentRow']);
        return parent::buildTree();
    }
}
