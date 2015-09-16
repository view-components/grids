<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Base\Html\AbstractTag;
use Presentation\Framework\Component\Text;
use Presentation\Framework\Data\DataAcceptorInterface;
use Presentation\Grids\Column;

class TCell extends AbstractTag implements DataAcceptorInterface
{
    /** @var  Column */
    protected $currentColumn;
    protected $text;
    protected $rowData;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(['data-role'=>'grid-cell']);
        $this->text = new Text();
        $this->text->attachTo($this);
    }

    /**
     * @return string
     */
    public function getTagName()
    {
        return 'td';
    }

    /**
     * @param $column
     * @return $this
     */
    public function setData($column)
    {
        $this->setCurrentColumn($column);
        return $this;
    }

    public function extractData()
    {
        $row = $this->rowData;
        $columnName = $this->currentColumn->getName();
        return property_exists($row, $columnName)?$row->{$columnName}:'?';
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->text->setValue($this->extractData());
        return parent::render();
    }

    /**
     * @param $currentColumn
     * @return $this
     */
    public function setCurrentColumn(Column $currentColumn = null)
    {
        $this->currentColumn = $currentColumn;
        return $this;
    }

    /**
     * @param $rowData
     * @return $this
     */
    public function setRowData($rowData)
    {
        $this->rowData = $rowData;
        return $this;
    }

    /**
     * @return Column|null
     */
    public function getCurrentColumn()
    {
        return $this->currentColumn;
    }
}
