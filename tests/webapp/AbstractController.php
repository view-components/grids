<?php
namespace ViewComponents\Grids\WebApp;

use ViewComponents\ViewComponents\Base\ComponentInterface;
use ReflectionClass;
use ReflectionMethod;

abstract class AbstractController
{
    private $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    protected function render($tpl, array $data = [])
    {
        $data['startTime'] = $this->startTime;
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

    protected function page($content, $title = '')
    {
        if ($content instanceof ComponentInterface) {
            $content = $content->render();
        }
        return $this->render('layout', compact('content', 'title'));
    }
}