<?php

namespace Presentation\Grids;


use Presentation\Framework\Component\Html\Div;
use Presentation\Framework\Component\Html\Form;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Component\Repeater;
use Presentation\Grids\Component\TCell;
use Presentation\Grids\Component\Tr;
use Presentation\Grids\Layout\ControlPlacingStrategy\SolidRowStrategy;

class ConfigDefaults
{
    public function apply(GridConfig $config)
    {
        if (!$config->getForm()) {
            $config->setForm(new Form());
        }

        if (!$config->getContainer()) {
            $config->setContainer(new Div(['data-role'=> 'grid-container']));
        }

        if(!$config->getColumnRepeater()) {
            $config->setColumnRepeater(new Repeater());
        }

        if(!$config->getRowRepeater()) {
            $config->setRowRepeater(new Repeater());
        }

        if(!$config->getTRow()) {
            $config->setTRow(new Tr);
        }

        if (!$config->getTable()) {
            $config->setTable(new Tag('table'));
        }

        if (!$config->getTHead()) {
            $config->setTHead(new Tag('tHead'));
        }

        if (!$config->getTBody()) {
            $config->setTBody(new Tag('tbody'));
        }

        if (!$config->getTFoot()) {
            $config->setTFoot(new Tag('tfoot'));
        }

        if (!$config->getTCell()) {
            $config->setTCell(new TCell());
        }

        if (!$config->getControlPlacingStrategy()) {
            $config->setControlPlacingStrategy(new SolidRowStrategy());
        }

        if (!$config->getSubmitButton() && !$config->getControls()->isEmpty()) {
            $config->setSubmitButton(new Tag(
                'input',
                [
                    'type' => 'submit',
                    'data-role' => 'grid-submit'
                ]
            ));
        }
    }
}