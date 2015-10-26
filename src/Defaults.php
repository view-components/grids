<?php

namespace Presentation\Grids;

use Presentation\Framework\Component\HideIfNoChildren;
use Presentation\Framework\Component\Html\Div;
use Presentation\Framework\Component\Html\Form;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Component\Repeater;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Component\SolidControlRow;
use Presentation\Grids\Component\SolidRow;
use Presentation\Grids\Component\TCell;

class Defaults
{
    public function tree()
    {
        return [
            'container' => [
                'form' => [
                    'table' => [
                        'table_heading' => [
                            'title_row' => [
                                'heading_column_repeater' => [
                                    'title_cell' => []
                                ]
                            ],
                            'control_row' => []
                        ],
                        'table_body' => [
                            'data_row_repeater' => [
                                'table_row' => [
                                    'body_column_repeater' => [
                                        'data_cell' => []
                                    ]
                                ]
                            ]
                        ],
                        'table_footer' => [
                            'hide_if_no_pagination' => [
                                'pagination_container' => []
                            ]
                        ]

                    ]
                ]
            ]
        ];
    }

    public function inputSource()
    {
        return new InputSource($_GET);
    }

    public function getComponents()
    {
        return [
            'hide_if_no_pagination' => function() {
                return new HideIfNoChildren();
            },
            'pagination_container' => function() {
                return new SolidRow();
            },
            'container' => function() {
                return new Div(['data-role' => 'grid-container']);
            },
            'form' => function() {
                return new Form();
            },
            'table' => function() {
                return new Tag('table');
            },
            'table_heading' => function() {
                return new Tag('thead');
            },
            'table_body' => function() {
                return new Tag('tbody');
            },
            'table_footer' => function() {
                return new Tag('tfoot');
            },
            'table_row' => function() {
                return new Tr();
            },
            'title_row' => function() {
                return new Tag('tr');
            },
            'heading_column_repeater' => function() {
                return new Repeater();
            },
            'title_cell' => function() {
                return new Tag('th');
            },
            'data_row_repeater' => function() {
                return new Repeater();
            },
            'body_column_repeater' => function() {
                return new Repeater();
            },
            'data_cell' => function() {
                return new TCell();
            },
            'control_row' => function() {
                return new SolidControlRow();
            },
            'submit_button' => function() {
                return new Tag('input', ['type' => 'submit']);
            }
        ];
    }
}
