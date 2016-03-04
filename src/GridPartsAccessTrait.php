<?php
namespace ViewComponents\Grids;

use ViewComponents\ViewComponents\Base\ComponentInterface;
use ViewComponents\ViewComponents\Base\Compound\PartInterface;
use ViewComponents\ViewComponents\Base\ContainerComponentInterface;
use ViewComponents\ViewComponents\Base\ViewComponentInterface;
use ViewComponents\ViewComponents\Component\Html\Tag;
use ViewComponents\ViewComponents\Component\Part;

/**
 * Trait GridPartsAccessTrait
 *
 * @todo replace Grid::constant to static::constant if it would work
 */
trait GridPartsAccessTrait
{
    /**
     * @param $id
     * @param bool $extractView
     * @return null|PartInterface|ViewComponentInterface|Part
     */
    abstract public function getComponent($id, $extractView = true);

    /**
     * @param ComponentInterface|PartInterface $component
     * @param string|null $id
     * @param string|null $defaultParent
     * @return $this
     */
    abstract public function setComponent(ComponentInterface $component, $id = null, $defaultParent = null);

    /**
     * @return Tag
     */
    public function getTable()
    {
        return $this->getComponent(Grid::TABLE_ID);
    }

    /**
     * @param ComponentInterface $component
     * @return $this
     */
    public function setTable(ComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TABLE_ID, Grid::FORM_ID);
    }

    /**
     * @return Tag
     */
    public function getTableHeading()
    {
        return $this->getComponent(Grid::TABLE_HEADING_ID);
    }

    /**
     * @param ComponentInterface $component
     * @return $this
     */
    public function setTableHeading(ComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TABLE_HEADING_ID, Grid::TABLE_ID);
    }

    /**
     * @return Tag
     */
    public function getTableBody()
    {
        return $this->getComponent(Grid::TABLE_BODY_ID);
    }

    /**
     * @param ComponentInterface $component
     * @return $this
     */
    public function setTableBody(ComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TABLE_BODY_ID, Grid::TABLE_ID);
    }

    /**
     * @return Tag
     */
    public function getTableFooter()
    {
        return $this->getComponent(Grid::TABLE_FOOTER_ID);
    }

    /**
     * @param ComponentInterface $component
     * @return $this
     */
    public function setTableFooter(ComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TABLE_FOOTER_ID, Grid::TABLE_ID);
    }

    /**
     * @return Tag
     */
    public function getTileRow()
    {
        return $this->getComponent(Grid::TITLE_ROW_ID);
    }

    /**
     * @param ComponentInterface $component
     * @return $this
     */
    public function setTitleRow(ComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TITLE_ROW_ID, Grid::TABLE_HEADING_ID);
    }

    /**
     * @return ContainerComponentInterface
     */
    public function getControlRow()
    {
        return $this->getComponent(Grid::CONTROL_ROW_ID);
    }

    /**
     * @param ComponentInterface $component
     * @return $this
     */
    public function setControlRow(ComponentInterface $component)
    {
        return $this->setComponent($component, Grid::CONTROL_ROW_ID, Grid::TABLE_HEADING_ID);
    }
}
