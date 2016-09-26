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
use ViewComponents\ViewComponents\Component\ManagedList;
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

    const ID = 'page_totals_row';

    const OPERATION_SUM = 'sum';
    const OPERATION_AVG = 'avg';
    const OPERATION_COUNT = 'count';
    const OPERATION_IGNORE = 'ignore';

    protected $valuePrefixes = [
        self::OPERATION_SUM => 'Σ',
        self::OPERATION_AVG => 'Avg.',
        self::OPERATION_COUNT => 'Count:'
    ];

    /**
     * Keys are column id's and values are operations (PageTotalsRow::OPERATION_* constants  or closures).
     *
     * @var string[]|array
     */
    protected $operations;

    protected $totalData;

    protected $cellObserver;

    protected $rowsProcessed = 0;

    private $isTotalsCalculationFinished = false;

    /**
     * @var string
     */
    private $defaultOperation;

    /**
     * PageTotalsRow constructor.
     *
     * Operations passed to first argument ($operations) may contain values
     * of PageTotalsRow::OPERATIN_* constants or Closure or null. Keys must be equal to target column id's
     * If $operations has no value for column, default operation will be used for that column.
     *
     * Closure passed to operations can accept
     * accumulated value in first argument and current row value in second argument.
     *
     * @param array|string[] $operations (optional) keys are column id's and values are operations
     *                                   (see PageTotalsRow::OPERATION_* constants) or closures.
     * @param string|null $defaultOperation operation that will be used for column
     *                                      if operation isn't specified for this column in first argument.
     */
    public function __construct(array $operations = [], $defaultOperation = null)
    {
        $this->id = static::ID;
        $this->destinationParentId = ManagedList::LIST_CONTAINER_ID;
        $this->operations = $operations;
        if ($defaultOperation === null) {
            $defaultOperation = empty($operations) ? self::OPERATION_SUM : self::OPERATION_IGNORE;
        }
        $this->defaultOperation = $defaultOperation;
    }

    /**
     * @param Compound|Grid $root
     * @param bool $prepend
     */
    public function attachToCompound(Compound $root, $prepend = false)
    {
        $this->attachToCompoundInternal($root, $prepend);
        $this->replaceGridDataInjector($root);
    }

    /**
     * Returns string prefixes for data printed in totals row for different operations.
     *
     * Keys are operations and values are prefixes.
     *
     * @return string[]
     */
    public function getValuePrefixes()
    {
        return $this->valuePrefixes;
    }

    /**
     * Sets string prefixes for data printed in totals row for different operations.
     *
     * @param string[] $valuePrefixes keys are operations and values are prefixes.
     * @return $this
     */
    public function setValuePrefixes(array $valuePrefixes)
    {
        $this->valuePrefixes = $valuePrefixes;
        return $this;
    }

    /**
     * Renders tag and returns output.
     *
     * @return string
     */
    public function render()
    {
        /** @var Grid $grid */
        $grid = $this->root;
        $this->isTotalsCalculationFinished = true;
        $tr = $grid->getRecordView();
        // set total_row as current grid row
        $lastRow = $grid->getCurrentRow();
        $grid->setCurrentRow($this->totalData);

        // modify columns, prepare it for rendering totals row
        $valueCalculators = [];
        $valueFormatters = [];
        foreach ($grid->getColumns() as $column) {
            $valueCalculators[$column->getId()] = $column->getValueCalculator();
            $valueFormatters[$column->getId()] = $prevFormatter = $column->getValueFormatter();
            $column->setValueCalculator(null);
            $column->setValueFormatter(function ($value) use ($prevFormatter, $column) {
                $operation = $this->getOperation($column->getId());
                if ($prevFormatter && !($operation === static::OPERATION_IGNORE || $operation instanceof Closure)) {
                    $value = call_user_func($prevFormatter, $value);
                }
                // Add value prefix if specified for operation
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
        if (!property_exists($this->totalData, $field)) {
            $this->totalData->$field = 0;
        }
        $operation = $this->getOperation($field);
        switch ($operation) {
            case self::OPERATION_SUM:
                if (!is_numeric($value)) {
                    return;
                }
                $this->totalData->$field += $value;
                break;
            case self::OPERATION_COUNT:
                $this->totalData->$field = $this->rowsProcessed;
                break;
            case self::OPERATION_AVG:
                $sumField = "{$field}_sum_for_totals";
                if (!property_exists($this->totalData, $sumField)) {
                    $this->totalData->$sumField = 0;
                }
                if (is_numeric($value)) {
                    $this->totalData->$sumField += $value;
                }
                $this->totalData->$field = round(
                    $this->totalData->$sumField / $this->rowsProcessed,
                    2
                );
                break;
            case self::OPERATION_IGNORE:
                $this->totalData->$field = null;
                break;
            default:
                if ($operation instanceof Closure) {
                    if (!property_exists($this->totalData, $field)) {
                        $this->totalData->$field = 0;
                    }
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

    protected function processCurrentRow()
    {
        if ($this->isTotalsCalculationFinished) {
            return;
        }
        $this->rowsProcessed++;
        /** @var Grid $grid */
        $grid = $this->root;
        foreach ($grid->getColumns() as $column) {
            $this->pushData($column->getId(), $column->getCurrentValue());
        }
    }

    protected function replaceGridDataInjector(Grid $grid)
    {
        /** @var CollectionView $collectionView */
        $grid->getCollectionView()->setDataInjector(function ($dataRow) use ($grid) {
            $grid->setCurrentRow($dataRow);
            $this->processCurrentRow();
        });
    }
}
