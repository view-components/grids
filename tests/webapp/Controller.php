<?php
namespace Presentation\Grids\Demo;

use Presentation\Framework\Control\PaginationControl;
use Presentation\Framework\Input\InputOption;
use Presentation\Framework\Control\FilterControl;
use Presentation\Framework\Data\ArrayDataProvider;
use Presentation\Framework\Data\DbTableDataProvider;
use Presentation\Framework\Data\Operation\FilterOperation;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Column;
use Presentation\Grids\Grid;
use Presentation\Grids\GridConfig;
use Presentation\Grids\Layout\ControlPlacingStrategy\ColumnsStrategy;

class Controller extends AbstractController
{
    protected function getUsersData()
    {
        return include(dirname(__DIR__) . '/fixtures/users.php');
    }

    protected function getDataProvider($operations = [])
    {
        return (isset($_GET['use-db']) && $_GET['use-db'])
            ? new DbTableDataProvider(
                db_connection(),
                'users',
                $operations
            )
            : new ArrayDataProvider(
                $this->getUsersData(),
                $operations
            );
    }

    public function index()
    {
        $out = '';
        $out .= $this->renderMenu();
        $out .= '<h1>Presentation/Grids Test Application</h1><h2>Index Page</h2>';

        return $out;
    }

    /**
     * Basic usage of Repeater component.
     *
     * @return string
     */
    public function demo1()
    {
        $provider = $this->getDataProvider();
        $cfg = new GridConfig();
        $cfg
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
            ]);

        $grid = new Grid($cfg);
        return $this->page($grid);
    }

    /**
     * Demo1 extended by HtmlBuilder usage.
     *
     * @return string
     */
    public function demo2()
    {
        $provider = $this->getDataProvider();
        $cfg = new GridConfig();
        $cfg
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
            ]);
        $cfg->setControls([
            new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET)),
            new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET))
        ]);
        $grid = new Grid($cfg);
        return $this->page($grid);
    }

    /**
     * Array Data Provider with sorting.
     *
     * @return string
     */
    public function demo3()
    {
        $provider = $this->getDataProvider();
        $cfg = new GridConfig();
        $cfg
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
            ]);
        $cfg->setControls([
            new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET)),
            new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET))
        ]);
        $cfg->setControlPlacingStrategy(new ColumnsStrategy());
        $grid = new Grid($cfg);
        return $this->page($grid);
    }

    /**
     * Array Data Provider with sorting.
     *
     * @return string
     */
    public function demo4()
    {
        $provider = $this->getDataProvider();
        $cfg = new GridConfig();
        $input = new InputSource($_GET);
        $cfg->setInputSource($input);
        $cfg
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
            ]);
        $cfg->setControls([
            new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
            new FilterControl('name', FilterOperation::OPERATOR_EQ, $input->option('name')),
            new FilterControl('birthday', FilterOperation::OPERATOR_EQ, $input->option('birthday')),
            new PaginationControl($input->option('page'), 10, $provider)
        ]);
        $cfg->setControlPlacingStrategy(new ColumnsStrategy());
        $grid = new Grid($cfg);
        return $this->page($grid);
    }
}