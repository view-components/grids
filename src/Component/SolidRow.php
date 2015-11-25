<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Component\CompoundContainer;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Grids\Grid;

/**
 * Table row containing one cell with colspan attribute equal to grid's columns count.
 *
 * This component hides it's internal structure from accessing via children() method and provides direct access to
 * it's "cell" component children.
 *
 */
class SolidRow extends CompoundContainer
{
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
        parent::__construct(
            ['row_tag' => ['cell_tag' => []]],
            [
                'row_tag' => new Tag('tr', ['data-role' => 'solid-row']),
                'cell_tag' => new Tag($cellTagName, ['colspan' => $columnsCount], $components)
            ],
            'cell_tag'
        );
    }

    /**
     * @return Tag
     */
    public function getCellTag()
    {
        return $this->getComponent('cell_tag');
    }

    /**
     * @return Tag
     */
    public function getRowTag()
    {
        return $this->getComponent('row_tag');
    }

    private function provideColspanAttribute()
    {
        /** @var Tag $cell */
        $cell = $this->getCellTag();
        if (
            ($cell->getAttribute('colspan') === null)
            && ($grid = $this->parents()->findByType(Grid::class))
        ) {
            $cell->setAttribute('colspan', count($grid->getColumns()));
        }
    }

    public function render()
    {
        $this->provideColspanAttribute();
        return parent::render();
    }
}
