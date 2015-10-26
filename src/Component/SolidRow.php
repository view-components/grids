<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Component\CompoundContainer;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Grids\Grid;

class SolidRow extends CompoundContainer implements InitializableInterface
{
    use InitializableTrait;

    protected $rowTag;
    protected $cellTag;

    public function __construct($components = [], $cellTagName = 'td', $columnsCount = null)
    {
        parent::__construct(
            ['row'=>['cell']],
            [
                'row' => new Tag('tr', ['data-role'=> 'solid-row']),
                'cell' => new Tag($cellTagName, ['colspan' => $columnsCount], $components)
            ],
            'cell'
        );
    }

    /**
     * @return Tag
     */
    public function getCellTag()
    {
        return $this->components()->get('cell');
    }

    /**
     * @return Tag
     */
    public function getRowTag()
    {
        return $this->components()->get('row');
    }

    protected function provideColspanAttribute()
    {
        /** @var Tag $cell */
        $cell = $this->getCellTag();
        if ($cell->getAttribute('colspan') === null) {
            $cell->setAttribute('colspan', count($this->grid->getColumns()));
        }
    }

    protected function initializeInternal(Grid $grid)
    {
        $this->provideColspanAttribute();
    }
}
