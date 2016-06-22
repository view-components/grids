<?php

namespace ViewComponents\Grids;

use Nayjest\Collection\Extended\ObjectCollectionReadInterface;
use RuntimeException;
use ViewComponents\Grids\Component\Column;
use ViewComponents\ViewComponents\Base\Compound\PartInterface;
use ViewComponents\ViewComponents\Component\Html\Tag;
use ViewComponents\ViewComponents\Component\ManagedList;
use ViewComponents\ViewComponents\Component\ManagedList\RecordView;
use ViewComponents\ViewComponents\Component\Part;
use ViewComponents\Grids\Component\SolidRow;

/**
 * Grid component.
 */
class Grid extends ManagedList
{
    use GridPartsAccessTrait;

    const TABLE_ID = 'table';
    const TABLE_HEADING_ID = 'table_heading';
    const TABLE_FOOTER_ID = 'table_footer';
    const TABLE_BODY_ID = 'table_body';
    const TITLE_ROW_ID = 'title_row';
    const CONTROL_ROW_ID = 'control_row';

    /** @var  mixed|null */
    protected $currentRow;

    /**
     * Returns column collection (readonly).
     *
     * @return Column[]|ObjectCollectionReadInterface
     */
    public function getColumns()
    {
        return $this->getComponents()->filterByType(Column::class);
    }

    /**
     * Returns column with specified id,
     * throws exception if grid does not have specified column.
     *
     * @param string $id
     * @return Column
     */
    public function getColumn($id)
    {
        $column = $this->getComponent($id);
        if (!$column) {
            throw new RuntimeException("Column '$id' is not defined.");
        }
        if (!$column instanceof Column) {
            throw new RuntimeException("'$id' is not a column.");
        }
        return $column;
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
     * @see \ViewComponents\ViewComponents\Component\ManagedList::makeDataInjector
     *
     * @return callable
     */
    protected function makeDataInjector()
    {
        return [$this, 'setCurrentRow'];
    }

    /**
     * Creates and returns default compound components.
     *
     * @return PartInterface[]
     */
    protected function makeDefaultComponents()
    {
        $components = parent::makeDefaultComponents();
        unset($components[ManagedList::RECORD_VIEW_ID]);
        $components[ManagedList::CONTROL_CONTAINER_ID]->setDestinationParentId(static::CONTROL_ROW_ID);
        $components[ManagedList::LIST_CONTAINER_ID]->setDestinationParentId(static::TABLE_BODY_ID);
        $components[ManagedList::SUBMIT_BUTTON_ID]->setDestinationParentId(static::CONTROL_ROW_ID);
        return array_merge(
            $components,
            [
                static::TABLE_ID => new Part(
                    new Tag('table'),
                    static::TABLE_ID,
                    static::FORM_ID
                ),
                static::TABLE_HEADING_ID => new Part(
                    new Tag('thead'),
                    static::TABLE_HEADING_ID,
                    static::TABLE_ID
                ),
                static::TITLE_ROW_ID => new Part(
                    new Tag('tr'),
                    static::TITLE_ROW_ID,
                    static::TABLE_HEADING_ID
                ),
                static::CONTROL_ROW_ID => new Part(
                    new SolidRow(),
                    static::CONTROL_ROW_ID,
                    static::TABLE_HEADING_ID
                ),
                static::TABLE_BODY_ID => new Part(new Tag('tbody'), static::TABLE_BODY_ID, static::TABLE_ID),
                static::RECORD_VIEW_ID => new RecordView(new Tag('tr')),
                static::TABLE_FOOTER_ID => new Part(new Tag('tfoot'), static::TABLE_FOOTER_ID, static::TABLE_ID),
            ]
        );
    }

    /**
     * Prepares component for rendering.
     */
    protected function prepare()
    {
        parent::prepare();
        $this->hideControlRowIfEmpty();
    }

    protected function hideControlRowIfEmpty()
    {
        $row = $this->getControlRow();
        $container = $this->getControlContainer();
        if (!$row
            || !$container
            || !$container->children()->isEmpty()
            || ($row->children()->count() > 2) // submit button + control_container
        ) {
            return;
        }
        /** @var Part $rowPart */
        $rowPart = $this->getComponent(static::CONTROL_ROW_ID, false);
        $rowPart->setView(null);
    }
}
