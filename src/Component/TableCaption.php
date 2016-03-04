<?php

namespace ViewComponents\Grids\Component;

use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Base\Compound\PartInterface;
use ViewComponents\ViewComponents\Base\Compound\PartTrait;
use ViewComponents\ViewComponents\Component\Compound;
use ViewComponents\ViewComponents\Component\DataView;
use ViewComponents\ViewComponents\Component\Html\Tag;

class TableCaption extends Tag implements PartInterface
{
    use PartTrait {
        PartTrait::attachToCompound as private attachToCompoundInternal;
    }

    const ID = 'caption';

    /** @var DataView  */
    private $text;

    public function __construct($text, $attributes = [])
    {
        parent::__construct('caption', $attributes);
        $this->setDestinationParentId(Grid::TABLE_ID);
        $this->setId(static::ID);
        $this->addChild($this->text = new DataView($text));
    }

    public function attachToCompound(Compound $root, $prepend = true)
    {
        $this->attachToCompoundInternal($root, $prepend);
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text->setData($text);
    }
}
