<?php

namespace Presentation\Grids\Component;

use Nayjest\Tree\NodeTrait;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\ComponentTrait;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Rendering\ViewTrait;

class SolidRow implements ComponentInterface
{

    use NodeTrait {
        NodeTrait::children as realChildren;
    }
    use ComponentTrait;
    use ViewTrait;
    use GridComponentTrait;

    private $rowTag;
    private $cellTag;

    public function __construct($components = null, $cellTagName = 'td', $columnsCount = null)
    {
        $this->cellTag = new Tag($cellTagName, ['colspan' => $columnsCount]);
        $this->rowTag = new Tag('tr', [], [$this->cellTag]);
        $this->initializeCollection([$this->rowTag]);
        if ($components) {
            $this->children()->set($components);
        }

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

    public function children()
    {
        return $this->getCellTag()->children();
    }

    protected function provideColspanAttribute()
    {
        if ($this->cellTag->getAttribute('colspan') === null) {
            $this->cellTag->setAttribute('colspan', count($this->getGridConfig()->getColumns()));
        }
    }
    public function render()
    {
        $this->provideColspanAttribute();
        return $this->beforeRender()->notify() . $this->getRowTag()->render();
    }

}