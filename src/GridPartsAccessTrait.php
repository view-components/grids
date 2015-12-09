<?php
namespace Presentation\Grids;

use Presentation\Framework\Base\ComponentInterface;

trait GridPartsAccessTrait
{
    /**
     * @param string $componentName
     * @return ComponentInterface|null
     */
    abstract public function getComponent($componentName);

    /**
     * @param string $name
     * @param ComponentInterface|null $component
     * @return Grid
     */
    abstract public function setComponent($name, ComponentInterface $component = null);

    abstract public function setRecordView(ComponentInterface $component);

    abstract public function getRecordView();

    public function getTable()
    {
        return $this->getComponent('table');
    }

    public function setTable(ComponentInterface $table)
    {
        return $this->setComponent('table', $table);
    }

    public function getTableHeading()
    {
        return $this->getComponent('table_heading');
    }

    public function setTableHeading(ComponentInterface $table)
    {
        return $this->setComponent('table_heading', $table);
    }

    public function getTitleRow()
    {
        return $this->getComponent('title_row');
    }

    public function setTitleRow(ComponentInterface $table)
    {
        return $this->setComponent('title_row', $table);
    }

    public function getControlRow()
    {
        return $this->getComponent('control_row');
    }

    public function setControlRow(ComponentInterface $table)
    {
        return $this->setComponent('control_row', $table);
    }

    public function getTableBody()
    {
        return $this->getComponent('table_body');
    }

    public function setTableBody(ComponentInterface $table)
    {
        return $this->setComponent('table_body', $table);
    }

    public function getTableFooter()
    {
        return $this->getComponent('table_footer');
    }

    public function setTableFooter(ComponentInterface $table)
    {
        return $this->setComponent('table_footer', $table);
    }


    /**
     * @return ComponentInterface|null
     */
    public function getTableRow()
    {
        return $this->getRecordView();
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTableRow(ComponentInterface $component = null)
    {
        return $this->setRecordView($component);
    }
}
