<?php

namespace ViewComponents\Grids\Component;

use Closure;
use LogicException;
use Nayjest\Tree\ChildNodeTrait;
use ViewComponents\ViewComponents\Base\Compound\PartInterface;
use ViewComponents\ViewComponents\Base\Compound\PartTrait;
use ViewComponents\ViewComponents\Base\ViewComponentInterface;
use ViewComponents\ViewComponents\Component\CollectionView;
use ViewComponents\ViewComponents\Component\Compound;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Rendering\ViewTrait;

/**
 * Class PageTotalsRow
 */
class PageTotalsRow implements PartInterface, ViewComponentInterface
{
    use PartTrait {
        PartTrait::attachToCompound as attachToCompoundInternal;
    }
    use ChildNodeTrait;
    use ViewTrait;

    const OPERATION_SUM = 'sum';
    const OPERATION_AVG = 'avg';
    const OPERATION_COUNT = 'count';
    const OPERATION_IGNORE = null;

    protected $valuePrefixes = [
        self::OPERATION_SUM => 'Σ',
        self::OPERATION_AVG => 'Avg.',
        self::OPERATION_COUNT => 'Count:'
    ];

    protected $operations;

    protected $totalData;

    protected $cellObserver;

    protected $rowsProcessed = 0;

    private $dataCollectingCallback;

    private $stopDataCollecting = false;

    /**
     * @var string
     */
    private $defaultOperation;

    /**
     * PageTotalsRow constructor.
     *
     * Operations passed to first argument ($operations) can contain values
     * of PageTotalsRow::OPERATIN_* constants or Closure or null.
     * If $operations has no key for column, default operation will be used.
     *
     * @param array $operations keys are field names and values are operations, operations
     * @param string $defaultOperation
     */
    public function __construct(array $operations = [], $defaultOperation = self::OPERATION_SUM)
    {
        $this->id = 'page_totals_row';
        $this->destinationParentId = 'list_container';
        $this->operations = $operations;
        $this->dataCollectingCallback = function () {
            if ($this->stopDataCollecting) {
                return;
            }
            $this->rowsProcessed++;
            /** @var Grid $grid */
            $grid = $this->root;
            foreach ($grid->getColumns() as $column) {
                $this->pushData($column->getId(), $column->getCurrentValue());
            }
        };

        $this->defaultOperation = $defaultOperation;
    }

    public function attachToCompound(Compound $root, $prepend = false)
    {
        $this->attachToCompoundInternal($root, $prepend);
        /** @var CollectionView $collectionView */
        $collectionView = $root->getComponent('collection_view');
        $collectionView->setDataInjector(function ($dataRow, $collectionView) use ($root) {
            call_user_func([$root, 'setCurrentRow'], $dataRow, $collectionView);
            call_user_func($this->dataCollectingCallback);
        });
    }

    /**
     * @return array
     */
    public function getValuePrefixes()
    {
        return $this->valuePrefixes;
    }

    public function setValuePrefixes(array $valuePrefixes)
    {
        $this->valuePrefixes = $valuePrefixes;
        return $this;
    }

    public function render()
    {
        /** @var Grid $grid */
        $grid = $this->root;
        $this->stopDataCollecting = true;
        $tr = $grid->getRecordView();

        // set total_row as current grid row
        $lastRow = $grid->getCurrentRow();
        $grid->setCurrentRow($this->totalData);

        // modify columns
        $valueCalculators = [];
        $valueFormatters = [];
        foreach ($grid->getColumns() as $column) {
            $valueCalculators[$column->getId()] = $column->getValueCalculator();
            $valueFormatters[$column->getId()] = $prevFormatter = $column->getValueFormatter();
            $column->setValueCalculator(null);
            $column->setValueFormatter(function ($value) use ($prevFormatter, $column) {
                if ($prevFormatter) {
                    $value = call_user_func($prevFormatter, $value);
                }
                $operation = $this->getOperation($column->getId());
                if ($value !== null && is_string($operation) && array_key_exists($operation, $this->valuePrefixes)) {
                    $value = $this->valuePrefixes[$operation] . '&nbsp;' . $value;
                }
                return $value;
            });
        }

        $output = $tr->render();

        // restore column value calculators & formatters
        foreach ($grid->getColumns() as $column) {
            $column->setValueCalculator($valueCalculators[$column->getId()]);
            $column->setValueFormatter($valueFormatters[$column->getId()]);
        }
        // restore last data row
        $grid->setCurrentRow($lastRow);
        return $output;
    }

    protected function pushData($field, $value)
    {
        if ($this->totalData === null) {
            $this->totalData = new \stdClass();
        }
        if (!is_numeric($value)) {
            return;
        }
        if (!property_exists($this->totalData, $field)) {
            $this->totalData->$field = 0;
        }
        $operation = $this->getOperation($field);
        switch ($operation) {
            case self::OPERATION_SUM:
                $this->totalData->$field += $value;
                break;
            case self::OPERATION_COUNT:
                $this->totalData->$field = $this->rowsProcessed;
                break;
            case self::OPERATION_AVG:
                $sumFiled = "{$field}_sum_for_totals";
                if (!property_exists($this->totalData, $sumFiled)) {
                    $this->totalData->$sumFiled = 0;
                }
                $this->totalData->$sumFiled += $value;
                $this->totalData->$field = round(
                    $this->totalData->$sumFiled / $this->rowsProcessed,
                    2
                );
                break;
            case self::OPERATION_IGNORE:
                $this->totalData->$field = null;
                break;
            default:
                if ($operation instanceof Closure) {
                    $this->totalData->$field = $operation($this->totalData->$field, $value);
                    break;
                }
                throw new LogicException(
                    'PageTotalsRow: Unknown aggregation operation.'
                );
        }
    }

    /**
     * @param string $columnName
     * @return string|Closure|null
     */
    protected function getOperation($columnName)
    {
        return array_key_exists($columnName, $this->operations)
            ? $this->operations[$columnName]
            : $this->defaultOperation;
    }
}
