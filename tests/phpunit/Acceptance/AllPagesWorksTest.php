<?php
namespace ViewComponents\Grids\Test\Acceptance;

use ViewComponents\Grids\WebApp\Controller;
use ViewComponents\TestingHelpers\Application\Http\EasyRouting;
use ViewComponents\TestingHelpers\Test\Acceptance\AbstractAcceptanceTest;

class AllPagesWorksTest extends AbstractAcceptanceTest
{
    public function testAllPages()
    {
        $this->assertPagesWorks(EasyRouting::getUris(Controller::class));
    }
}