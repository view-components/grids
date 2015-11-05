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
class SolidRow extends CompoundContainer implements InitializableInterface
{
    use InitializableTrait;

    protected $rowTag;
    protected $cellTag;

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
