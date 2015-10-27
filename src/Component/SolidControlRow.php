<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Control\ControlInterface;
use Presentation\Framework\Control\PaginationControl;
use Presentation\Grids\Grid;

class SolidControlRow extends SolidRow
{
    public function render()
    {
        $controls = $this->grid->controls()->filter(
            function (ControlInterface $control) {
                return !$control instanceof PaginationControl;
            }
        );
        if ($controls->isEmpty()) {
            return '';
        } else {
            return parent::render();
        }
    }

    /**
     * @param Grid $grid
     */
    protected function initializeInternal(Grid $grid)
    {
        parent::initializeInternal($grid);
        $submitButton = $grid->components()->getSubmitButton();
        foreach($grid->controls() as $control) {
            if ($control->getView()->parent()) {
                continue;
            }
            $this->children()->add($control->getView());
        }
        if ($submitButton) {
            $this->addChild($submitButton);
        }
    }
}
