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
        return $this->setComponent($component, 'control_container', 'control_row');
    }

    public function setSubmitButton(ComponentInterface $component)
    {
        return $this->setComponent($component, 'submit_button', 'control_row');
    }

    public function setListContainer(ComponentInterface $component)
    {
        return $this->setComponent($component, 'list_container', 'table_body');
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
            new Part(new Tag('div'), 'container', 'root'),
            new Part(new Tag('form'), 'form', 'container'),

            new Part(new Tag('table'), 'table', 'form'),
            new Part(new Tag('thead'), 'table_heading', 'table'),
            new Part(new Tag('tr'), 'title_row', 'table_heading'),
            new Part(new SolidRow(), 'control_row', 'table_heading'),
            new Part(new Tag('span'), 'control_container', 'control_row'),
            new Part(new Tag('input', ['type' => 'submit']), 'submit_button', 'control_row'),
            new Part(new Tag('tbody'), 'table_body', 'table'),
            new Part(new Container(), 'list_container', 'table_body'),
            new Part(new CollectionView(), 'collection_view', 'list_container'),
            new RecordView(new Tag('tr')),
            new Part(new Tag('tfoot'), 'table_footer', 'table'),
        ];
    }
}
