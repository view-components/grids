<?php

namespace ViewComponents\Grids\Component;

use ViewComponents\ViewComponents\Base\ComponentInterface;
use ViewComponents\ViewComponents\Component\Container;
use ViewComponents\ViewComponents\Component\Html\Tag;
use ViewComponents\Grids\Grid;

/**
 * Table row containing one cell with colspan attribute equal to grid's columns count.
 *
 * This component hides it's internal structure from accessing via children() method and provides direct access to
 * it's "cell" component children.
 *
 */
class SolidRow extends Container
{
    private $cellTag;
    private $rowTag;

    /**
     * SolidRow constructor.
     *
     * If columns count argument is specified, SolidRow will use it instead of real grid's columns count.
     *
     * @param ComponentInterface[] $components
     * @param string $cellTagName
     * @param int|null $columnsCount
     */
    public function __construct($components = [], $cellTagName = 'td', $columnsCount = null)
    {
        $this->cellTag = new Tag(
            $cellTagName,
            $columnsCount ? ['colspan' => $columnsCount] : [],
            $components
        );
        $this->rowTag = new Tag('tr', [], [$this->cellTag]);

        parent::__construct([$this->rowTag]);
    }

    /**
     * @return Tag
     */
    public function getCellTag()
    {
        return $this->cellTag;
    }

    /**
     * @return Tag
     */
    public function getRowTag()
    {
        return $this->rowTag;
    }

    /**
     * Renders component and returns output.
     *
     * @return string
     */
    public function render()
    {
        $this->provideColspanAttribute();
        return $this->rowTag->render();
    }

    public function children()
    {
        return $this->cellTag->children();
    }

    private function provideColspanAttribute()
    {
        if ($this->cellTag->getAttribute('colspan') !== null) {
            return;
        }
        $grid = $this->parents()->findByType(Grid::class);
        if (!$grid) {
            return;
        }
        $this->cellTag->setAttribute('colspan', count($grid->getColumns()));
    }
}
