<?php

namespace Presentation\Grids;

use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\RepeaterInterface;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Component\ManagedList\ManagedList;
use Presentation\Framework\Data\DataProviderInterface;
use Presentation\Framework\Initialization\InitializerTrait;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Component\SolidRow;
use Traversable;

/**
 * Grid component.
 */
class Grid extends ManagedList
{
    use GridPartsAccessTrait;
    use InitializerTrait;

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
     * @param ComponentInterface[] $components empty by default
     */
    public function __construct($dataProvider = null, array $columns = [], array $components = [])
    {
        parent::__construct($dataProvider, null, $components);
        $this->columnCollection = new ColumnCollection($this);
        $this->setColumns($columns);
    }

    /**
     * Returns default grid structure.
     *
     * @return array
     */
    protected function makeDefaultHierarchy()
    {
        return [
            'container' => [
                'title' => [],
                'form' => [
                    'table' => [
                        'table_heading' => [
                            'title_row' => [
                            ],
                            'control_row' => [
                                'control_container' => [],
                                'submit_button' => [],
                            ]

                        ],
                        'table_body' => [
                            'list_container' => [
                                'repeater' => [
                                    'record_view' => [
                                    ]
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

    protected function makeDefaultComponents()
    {
        $components =  array_merge(
            parent::makeDefaultComponents(),
            [
                'record_view' => new Tag('tr'), //override
                'table' => new Tag('table'),
                'table_heading' => new Tag('thead'),
                'table_body' => new Tag('tbody'),
                'table_footer' => new Tag('tfoot'),
                'title_row' => new Tag('tr'),
                'control_row' => new SolidRow(),
            ]
        );
        /** @var RepeaterInterface $repeater */
        $repeater = $components['repeater'];
        $repeater->setCallback([$this, 'setCurrentRow']);
        return $components;
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

    protected function initializeCollection(array $items)
    {
        parent::initializeCollection($items);
        $this->startInitialization();
    }

    protected function prepare()
    {
        parent::prepare();
        $this->getRepeater()->setCallback([$this, 'setCurrentRow']);
    }
}
