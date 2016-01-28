<?php

namespace Presentation\Grids\Control;

use Presentation\Framework\Component\TemplateView;
use Presentation\Framework\Rendering\RendererInterface;

class ColumnSortingView extends TemplateView
{
    public function __construct(RendererInterface $renderer = null)
    {
        parent::__construct('controls/column_sorting', [], $renderer);
    }
}
