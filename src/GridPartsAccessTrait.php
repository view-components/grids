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
     * Returns component that renders 'table' HTML tag.
     *
     * @return Tag
     */
    public function getTable()
    {
        return $this->getComponent(Grid::TABLE_ID);
    }

    /**
     * Sets component for rendering 'table' tag.
     *
     * @param ContainerComponentInterface $component
     * @return $this
     */
    public function setTable(ContainerComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TABLE_ID, Grid::FORM_ID);
    }

    /**
     * Returns component that renders 'thead' HTML tag.
     *
     * @return Tag
     */
    public function getTableHeading()
    {
        return $this->getComponent(Grid::TABLE_HEADING_ID);
    }

    /**
     * @param ViewComponentInterface $component
     * @return $this
     */
    public function setTableHeading(ViewComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TABLE_HEADING_ID, Grid::TABLE_ID);
    }

    /**
     * Returns component that renders 'tbody' HTML tag.
     *
     * @return Tag
     */
    public function getTableBody()
    {
        return $this->getComponent(Grid::TABLE_BODY_ID);
    }

    /**
     * @param ViewComponentInterface $component
     * @return $this
     */
    public function setTableBody(ViewComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TABLE_BODY_ID, Grid::TABLE_ID);
    }

    /**
     * Returns component that renders 'tfoot' HTML tag.
     *
     * @return Tag
     */
    public function getTableFooter()
    {
        return $this->getComponent(Grid::TABLE_FOOTER_ID);
    }

    /**
     * @param ViewComponentInterface $component
     * @return $this
     */
    public function setTableFooter(ViewComponentInterface $component)
    {
        return $this->setComponent($component, Grid::TABLE_FOOTER_ID, Grid::TABLE_ID);
    }

    /**
     * Returns component that renders 'tr' HTML tag containing column titles.
     * @return Tag
     */
    public function getTileRow()
    {
        return $this->getComponent(Grid::TITLE_ROW_ID);
    }

    /**
     * @param ViewComponentInterface $component
     * @return $this
     */
    public function setTitleRow(ViewComponentInterface $component)
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
     * @param ContainerComponentInterface $component
     * @return $this
     */
    public function setControlRow(ContainerComponentInterface $component)
    {
        return $this->setComponent($component, Grid::CONTROL_ROW_ID, Grid::TABLE_HEADING_ID);
    }

    /**
     * @param ContainerComponentInterface $component
     * @return $this
     */
    public function setControlContainer(ContainerComponentInterface $component)
    {
        return $this->setComponent($component, Grid::CONTROL_CONTAINER_ID, Grid::CONTROL_ROW_ID);
    }

    /**
     * @param ViewComponentInterface $component
     * @return $this
     */
    public function setSubmitButton(ViewComponentInterface $component)
    {
        return $this->setComponent($component, Grid::SUBMIT_BUTTON_ID, Grid::CONTROL_ROW_ID);
    }

    /**
     * @param ContainerComponentInterface $component
     * @return $this
     */
    public function setListContainer(ContainerComponentInterface $component)
    {
        return $this->setComponent($component, Grid::LIST_CONTAINER_ID, Grid::TABLE_BODY_ID);
    }
}
