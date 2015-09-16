<?php

namespace Presentation\Grids\Layout\ControlPlacingStrategy;

use Presentation\Framework\Component\Html\Tag;
use Presentation\Grids\GridConfig;

class ControlPlacingHelper
{
    public static function getNotPlacedControlViews(GridConfig $config)
    {
        $controls = $config->getControls();
        if ($controls->isEmpty()) {
            return [];
        }

        $res = [];
        foreach ($controls->getViews() as $view) {
            if (!$view->parent()) {
                $res[] = $view;
            }
        }
        return $res;
    }
}
