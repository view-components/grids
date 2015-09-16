<?php

namespace Presentation\Grids\Layout\ControlPlacingStrategy;

use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Control\PaginationControl;
use Presentation\Grids\Component\SolidRow;
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

    public static function placePagination(GridConfig $config)
    {
        $pagination = $config->getControls()->findByType(PaginationControl::class);
        /** @var PaginationControl $pagination */
        if ($pagination && !$pagination->getView()->parent()) {
            $config
                ->getTFoot()
                ->addChild(
                    new SolidRow([$pagination->getView()])
                );
        }
    }
}
