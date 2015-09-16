<?php

namespace Presentation\Grids\Layout\ControlPlacingStrategy;

use Presentation\Grids\Component\SolidRow;
use Presentation\Grids\GridConfig;

class SolidRowStrategy implements ControlPlacingStrategyInterface
{
    public function placeControls(GridConfig $config, $forceNewRow = false)
    {
        $views = ControlPlacingHelper::getNotPlacedControlViews($config);
        if (!$forceNewRow && count($views) === 0) {
            return;
        }
        $row = new SolidRow($views);
        $submit = $config->getSubmitButton();
        if ($submit && !$submit->parent()) {
            $submit->attachTo($row);
        }
        $config->getTHead()->addChild($row);
    }
}
