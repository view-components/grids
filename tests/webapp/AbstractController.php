<?php
namespace Presentation\Grids\Demo;

use ReflectionClass;
use ReflectionMethod;

abstract class AbstractController
{
     /**
     * @return \ReflectionMethod[]
     */
    protected function getActions()
    {
        $class = new ReflectionClass($this);
        return $class->getMethods(ReflectionMethod::IS_PUBLIC);

    }

    protected function render($tpl, array $data = [])
    {
        extract($data);
        ob_start();
        $resourcesDir = __DIR__ . '/resources';
        include "$resourcesDir/views/$tpl.php";
        return ob_get_clean();
    }


    protected function renderMenu()
    {
        return $this->render('menu/menu');
    }

    protected function page($content)
    {
        return $this->render('layout', compact('content'));
    }
}