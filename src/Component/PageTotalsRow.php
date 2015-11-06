<?php

namespace Presentation\Grids\Component;

use Closure;
use LogicException;
use Nayjest\Tree\NodeTrait;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\ComponentTrait;
use Presentation\Framework\Rendering\ViewTrait;
use Presentation\Grids\Grid;

class PageTotalsRow implements ComponentInterface, InitializableInterface
{
    use NodeTrait;
    use ComponentTrait {
        ComponentTrait::render as private renderInternal;
    }
    use ViewTrait;
    use InitializableTrait;

    const OPERATION_SUM = 'sum';
    const OPERATION_AVG = 'avg';
    const OPERATION_COUNT = 'count';
    const OPERATION_IGNORE = 'ignore';

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
    /**
     * @var string
     */
    private $defaultOperation;

    public function __construct(array $operations = [], $defaultOperation = self::OPERATION_SUM)
    {
        $this->operations = $operations;
        $this->dataCollectingCallback = function () {
            $this->rowsProcessed++;
            foreach ($this->grid->getColumns() as $column) {
                $this->pushData($column->getName(), $column->getCurrentValue());
            }
        };

        $this->defaultOperation = $defaultOperation;
    }

    public function render()
    {
        $grid = $this->grid;
        $tr = $grid->components()->getTableRow();
        $tr->removeListener('render', $this->dataCollectingCallback);
        $lastRow = $grid->getCurrentRow();
        $grid->setCurrentRow($this->totalData);
        $trParent = $tr->parent();
        $tr->attachTo($this);

        $valueCalculators = [];
        $valueFormatters = [];
        foreach ($this->grid->getColumns() as $column) {
            $valueCalculators[$column->getName()] = $column->getValueCalculator();
            $valueFormatters[$column->getName()] = $prevFormatter = $column->getValueFormatter();
            $column->setValueCalculator(null);
            $column->setValueFormatter(function($value) use ($prevFormatter, $column) {
                if ($prevFormatter) {
                    $value = call_user_func($prevFormatter, $value);
                }
                $operation = $this->getOperation($column->getName());
                if ($value !== null && is_string($operation) && array_key_exists($operation, $this->valuePrefixes)) {
                    $value = $this->valuePrefixes[$operation] . '&nbsp;' . $value;
                }
                return $value;
            });
        }

        $output = $this->renderInternal();

        // restore column value calculators & formatters
        foreach ($this->grid->getColumns() as $column) {
            $column->setValueCalculator($valueCalculators[$column->getName()]);
            $column->setValueFormatter($valueFormatters[$column->getName()]);
        }

        // restore last data row
        $grid->setCurrentRow($lastRow);
        // restore TR parent
        $tr->attachTo($trParent);

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
        switch($operation) {
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

    protected function getOperation($columnName)
    {
        return array_key_exists($columnName, $this->operations)
            ? $this->operations[$columnName]
            : $this->defaultOperation;
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

    /**
     * @param Grid $grid
     */
    protected function initializeInternal(Grid $grid)
    {
        // attach event handler to TR inside grid.onRender to guarantee that TR will not be changed.
        $grid->onRender(function (Grid $grid) {
            $grid->components()->getTableRow()->onRender($this->dataCollectingCallback);
        });
    }
}
