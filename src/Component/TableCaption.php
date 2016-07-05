<?php

namespace ViewComponents\Grids\Component;

use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Base\Compound\PartInterface;
use ViewComponents\ViewComponents\Base\Compound\PartTrait;
use ViewComponents\ViewComponents\Component\Compound;
use ViewComponents\ViewComponents\Component\DataView;
use ViewComponents\ViewComponents\Component\Html\Tag;

/**
 * This class is a component for rendering grid caption using 'caption' tag.
 */
class TableCaption extends Tag implements PartInterface
{
    use PartTrait {
        PartTrait::attachToCompound as private attachToCompoundInternal;
    }

    const ID = 'caption';

    /** @var DataView  */
    private $text;

    /**
     * TableCaption constructor.
     *
     * @param null|string $text
     * @param array $attributes caption tag attributes
     */
    public function __construct($text, $attributes = [])
    {
        parent::__construct('caption', $attributes);
        $this->setDestinationParentId(Grid::TABLE_ID);
        $this->setId(static::ID);
        $this->addChild($this->text = new DataView($text));
    }

    /**
     * This method is overridden in TableCaption class for placing caption to beginning
     * of parent container instead of default appending (default value of $prepend argument changed).
     *
     * @param Compound $root
     * @param bool $prepend
     */
    public function attachToCompound(Compound $root, $prepend = true)
    {
        $this->attachToCompoundInternal($root, $prepend);
    }

    /**
     * Returns caption text.
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Sets caption text.
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text->setData($text);
    }
}
