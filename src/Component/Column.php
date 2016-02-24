<?php

namespace ViewComponents\Grids\Component;

use mp;
use Nayjest\Tree\ChildNodeTrait;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Base\ComponentInterface;
use ViewComponents\ViewComponents\Base\Compound\PartInterface;
use ViewComponents\ViewComponents\Base\Compound\PartTrait;
use ViewComponents\ViewComponents\Base\ContainerComponentInterface;
use ViewComponents\ViewComponents\Base\DataViewComponentInterface;
use ViewComponents\ViewComponents\Component\Compound;
use ViewComponents\ViewComponents\Component\Html\Tag;
use ViewComponents\ViewComponents\Component\DataView;
use ViewComponents\ViewComponents\Component\Part;

/**
 * Grid column.
 */
class Column implements PartInterface
{
    use PartTrait {
        PartTrait::attachToCompound as attachToCompoundInternal;
    }
    use ChildNodeTrait;

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

    /** @var ComponentInterface */
    protected $titleView;

    /** @var ComponentInterface */
    protected $dataView;

    /** @var  Part|null */
    protected $titleCellPart;

    /** @var  Part|null */
    protected $dataCellPart;

    /** @var  Grid|null */
    protected $grid;

    /** @var  string|null */
    protected $dataFieldName;

    /** @var  callable|null */
    protected $valueCalculator;

    /** @var  callable|null */
    protected $valueFormatter;

    /**
     * Constructor.
     *
     * @param string|null $columnId column unique name for internal usage
     * @param string|null $label column label
     */
    public function __construct($columnId, $label = null)
    {
        $this->setDestinationParentId(Compound::ROOT_ID);
        $this->setId($columnId);
        $this->setLabel($label);
        $this->titleView = new DataView([$this, 'getLabel']);
        $this->dataView = new DataView([$this, 'getCurrentValueFormatted']);
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
        return (string)($formatter ? call_user_func($formatter, $value, $this->grid->getCurrentRow()) : $value);
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
            return mp\getValue($this->grid->getCurrentRow(), $this->getDataFieldName());
        }
    }

    /**
     * @return DataViewComponentInterface
     */
    public function getDataView()
    {
        return $this->dataView;
    }

    /**
     * @return DataViewComponentInterface
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
        return $this->dataFieldName ?: $this->getId();
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

    public function setDataCell(ContainerComponentInterface $cell)
    {
        $this->dataCell = $cell;
        $this->dataCell->children()->add($this->dataView, true);
        if ($this->dataCellPart !== null) {
            $this->dataCellPart->setView($cell);
        }
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

    public function setTitleCell(ContainerComponentInterface $cell)
    {
        $this->titleCell = $cell;
        $this->titleCell->children()->add($this->titleView, true);
        if ($this->titleCellPart !== null) {
            $this->titleCellPart->setView($cell);
        }
        return $this;
    }

    /**
     * Returns text label that will be rendered in table header.
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->label === null) {
            $this->label = ucwords(str_replace(array('-', '_', '.'), ' ', $this->id));
        }
        return $this->label;
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

    public function attachToCompound(Compound $root)
    {
        $this->attachToCompoundInternal($root);
        $this->grid = $root;
        $parts = $root->getComponents();
        $titleCellPart = $this->getTitleCellPart();
        if (!$parts->contains($titleCellPart)) {
            $parts->add($titleCellPart);
        }
        $dataCellPart = $this->getDataCellPart();
        if (!$parts->contains($dataCellPart)) {
            $parts->add($dataCellPart);
        }
    }

    /**
     * @return Part
     */
    protected function getDataCellPart()
    {
        if ($this->dataCellPart === null) {
            $this->dataCellPart = new Part(
                $this->getDataCell(),
                'column-' . $this->getId() . '-data-cell',
                'record_view'
            );
        }
        return $this->dataCellPart;
    }

    /**
     * @return Part
     */
    protected function getTitleCellPart()
    {
        if ($this->titleCellPart === null) {
            $this->titleCellPart = new Part(
                $this->titleCellPart = $this->getTitleCell(),
                'column-' . $this->getId() . '-title-cell',
                'title_row'
            );
        }
        return $this->titleCellPart;
    }
}
