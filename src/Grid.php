<?php

namespace ViewComponents\Grids;

use Nayjest\Collection\Extended\ObjectCollectionReadInterface;
use RuntimeException;
use ViewComponents\Grids\Component\Column;
use ViewComponents\ViewComponents\Base\ComponentInterface;
use ViewComponents\ViewComponents\Component\CollectionView;
use ViewComponents\ViewComponents\Component\Container;
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
    const TABLE_ID = 'table';
    const TABLE_HEADING_ID = 'table_heading';
    const TABLE_FOOTER_ID = 'table_footer';
    const TABLE_BODY_ID = 'table_body';
    const TITLE_ROW_ID = 'title_row';
    const CONTROL_ROW_ID = 'control_row';

    use GridPartsAccessTrait;

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

    public function setControlContainer(ComponentInterface $component)
    {
        return $this->setComponent($component, static::CONTROL_CONTAINER_ID, static::CONTROL_ROW_ID);
    }

    public function setSubmitButton(ComponentInterface $component)
    {
        return $this->setComponent($component, static::SUBMIT_BUTTON_ID, static::CONTROL_ROW_ID);
    }

    public function setListContainer(ComponentInterface $component)
    {
        return $this->setComponent($component, static::LIST_CONTAINER_ID, static::TABLE_BODY_ID);
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

    protected function makeDataInjector()
    {
        return [$this, 'setCurrentRow'];
    }

    protected function makeDefaultComponents()
    {
        return [
            new Part(new Tag('div'), static::CONTAINER_ID, static::ROOT_ID), // inherited
            new Part(new Tag('form'), static::FORM_ID, static::CONTAINER_ID), // inherited
            new Part(new Tag('table'), static::TABLE_ID, static::FORM_ID),
            new Part(new Tag('thead'), static::TABLE_HEADING_ID, static::TABLE_ID),
            new Part(new Tag('tr'), static::TITLE_ROW_ID, static::TABLE_HEADING_ID),
            new Part(new SolidRow(), static::CONTROL_ROW_ID, static::TABLE_HEADING_ID),
            new Part(new Tag('span'), static::CONTROL_CONTAINER_ID, static::CONTROL_ROW_ID), // inherited
            new Part(new Tag('input', ['type' => 'submit']), static::SUBMIT_BUTTON_ID, static::CONTROL_ROW_ID), // inherited
            new Part(new Tag('tbody'), static::TABLE_BODY_ID, static::TABLE_ID),
            new Part(new Container(), static::LIST_CONTAINER_ID, static::TABLE_BODY_ID), // inherited
            new Part(new CollectionView(), static::COLLECTION_VIEW_ID, static::LIST_CONTAINER_ID),  // inherited
            new RecordView(new Tag('tr')), // inherited
            new Part(new Tag('tfoot'), static::TABLE_FOOTER_ID, static::TABLE_ID),
        ];
    }
}
