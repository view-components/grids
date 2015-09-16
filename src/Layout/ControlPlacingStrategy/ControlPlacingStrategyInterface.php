<?php

namespace Presentation\Grids\Layout\ControlPlacingStrategy;

use Presentation\Grids\GridConfig;

interface ControlPlacingStrategyInterface
{

    public function placeControls(GridConfig $config);
}
