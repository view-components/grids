<?php

namespace Presentation\Grids;

use Nayjest\Manipulator\Manipulator;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Component\Text;

/**
 * Grid column.
 */
class Column
{
    /**
     * Column name.
     *
     * @var string
     */
    protected $name;

    /**
     * Text label that will be rendered in table header.
     *
     * @var string|null
     */
    protected $label;

    /** @var  ComponentInterface */
    protected $dataCell;

    /** @var  ComponentInterface */
    protected $titleCell;

    /** @var  Grid|null */
    protected $grid;

    /** @var ComponentInterface */
    protected $titleView;

    /** @var ComponentInterface */
    protected $dataView;

    /** @var  string|null */
    protected $dataFieldName;

    /** @var  callable|null */
    protected $valueCalculator;

    /** @var  callable|null */
    protected $valueFormatter;

    /**
     * Constructor.
     *
     * @param string|null $columnName column unique name for internal usage
     * @param string|null $label column label
     */
    public function __construct($columnName, $label = null)
    {
        $this->setName($columnName);
        $this->setLabel($label);
        $this->titleView = (new Text())
            ->setValue([$this, 'getLabel']);

        $this->dataView = (new Text())
            ->setValue([$this, 'getCurrentValueFormatted']);
    }

    /**
     * @return ComponentInterface
     */
    public function getDataCell()
    {
        if ($this->dataCell === null) {
            $this->setDataCell(
                new Tag('td')
            );
        }
        return $this->dataCell;
    }

    /**
     * Returns formatted value of current data cell.
     *
     * @return string
     */
    public function getCurrentValueFormatted()
    {
        return $this->formatValue($this->getCurrentValue());
    }

    /**
     * Formats value extracted from data row.
     *
     * @param $value
     * @return string
     */
    public function formatValue($value)
    {
        $formatter = $this->getValueFormatter();
        return (string)($formatter ? call_user_func($formatter, $value): $value);
    }

    /**
     * Returns current data cell value.
     *
     * @return mixed
     */
    public function getCurrentValue()
    {
        $func = $this->getValueCalculator();
        if ($func !== null) {
            return call_user_func($func, $this->grid->getCurrentRow());
        } else {
            return Manipulator::getValue($this->grid->getCurrentRow(), $this->getDataFieldName());
        }
    }

    /**
     * @return Text
     */
    public function getDataView()
    {
        return $this->dataView;
    }

    /**
     * @return Text
     */
    public function getTitleView()
    {
        return $this->titleView;
    }

    /**
     * @param string|null $dataFieldName
     * @return $this
     */
    public function setDataFieldName($dataFieldName)
    {
        $this->dataFieldName = $dataFieldName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDataFieldName()
    {
        return $this->dataFieldName ?: $this->getName();
    }

    /**
     * @return callable|null
     */
    public function getValueCalculator()
    {
        return $this->valueCalculator;
    }

    /**
     * @param $valueCalculator
     * @return $this
     */
    public function setValueCalculator(callable $valueCalculator = null)
    {
        $this->valueCalculator = $valueCalculator;
        return $this;
    }

    /**
     * @param callable|null $valueFormatter
     * @return Column
     */
    public function setValueFormatter($valueFormatter)
    {
        $this->valueFormatter = $valueFormatter;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getValueFormatter()
    {
        return $this->valueFormatter;
    }

    protected function updateGrid()
    {
        if ($this->grid) {
            $this->grid->getColumns()->updateGridInternal();
        }
    }

    public function setDataCell(ComponentInterface $cell)
    {
        $this->dataCell = $cell;
        $this->dataCell->children()->add($this->dataView, true);
        $this->updateGrid();
        return $this;
    }

    /**
     * @return ComponentInterface
     */
    public function getTitleCell()
    {
        if ($this->titleCell === null) {
            $this->setTitleCell(new Tag('th'));
        }
        return $this->titleCell;
    }

    public function setTitleCell(ComponentInterface $cell)
    {
        $this->titleCell = $cell;
        $this->titleCell->children()->add($this->titleView, true);
        $this->updateGrid();
        return $this;
    }

    /**
     * @param Grid $grid
     */
    public function setGridInternal(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Returns column name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets column name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        // @todo remove column with old name (?)
        $this->updateGrid();
        return $this;
    }

    /**
     * Returns text label that will be rendered in table header.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label ?: ucwords(str_replace(array('-', '_', '.'), ' ', $this->name));
    }

    /**
     * Sets text label that will be rendered in table header.
     *
     * @param string|null $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
}
