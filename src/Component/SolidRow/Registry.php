<?php

namespace Presentation\Grids\Component\SolidRow;

use Nayjest\Collection\Extended\Registry as BaseRegistry;
use Presentation\Framework\Component\Html\Tag;

class Registry extends BaseRegistry
{
    /**
     * @return Tag
     */
    public function getCell()
    {
        return $this->get('cell');
    }

    public function getRow()
    {
        return $this->get('row');
    }
}