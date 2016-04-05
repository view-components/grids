<?php

namespace ViewComponents\Grids\Test;

use PHPUnit_Framework_TestCase;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Grid;
use ViewComponents\TestingHelpers\Test\DefaultFixture;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Customization\CssFrameworks\FoundationStyling;
use ViewComponents\ViewComponents\Customization\CssFrameworks\SemanticUIStyling;
use ViewComponents\ViewComponents\Data\ArrayDataProvider;

class GridTest extends PHPUnit_Framework_TestCase
{
    /** @var  Grid */
    private $grid;

    protected function makeGrid()
    {
        return $this->grid =  new Grid(
            new ArrayDataProvider(DefaultFixture::getArray()),
            [
                new Column('id'),
                new Column('role')
            ]
        );
    }

    protected function checkOutput()
    {
        $out = strtolower($this->grid->render());
        self::assertTrue(substr_count($out, 'tr') >= DefaultFixture::getTotalCount() * 2);
        self::assertTrue(substr_count($out, 'td') >= DefaultFixture::getTotalCount() * 2 * 2);
        self::assertContains('form', $out);
    }
    public function test()
    {
        $this->makeGrid();
        $this->checkOutput();
    }

    public function testStyling()
    {
        BootstrapStyling::applyTo($this->makeGrid());
        $this->checkOutput();
        FoundationStyling::applyTo($this->makeGrid());
        $this->checkOutput();
        SemanticUIStyling::applyTo($this->makeGrid());
        $this->checkOutput();
    }
}