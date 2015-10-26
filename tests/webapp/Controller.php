<?php
namespace Presentation\Grids\Demo;

use Presentation\Framework\Component\Text;
use Presentation\Framework\Control\PaginationControl;
use Presentation\Framework\Input\InputOption;
use Presentation\Framework\Control\FilterControl;
use Presentation\Framework\Data\ArrayDataProvider;
use Presentation\Framework\Data\DbTableDataProvider;
use Presentation\Framework\Data\Operation\FilterOperation;
use Presentation\Grids\Column;
use Presentation\Grids\Component\ControlRowWithColumns;
use Presentation\Grids\Component\PageTotalsRow;
use Presentation\Grids\Component\SolidRow;
use Presentation\Grids\Grid;
use Presentation\Grids\GridConfig;

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
        return $this->page($grid, 'Basic Grid');
    }

    public function demo1b()
    {
        $provider = $this->getDataProvider();
        $grid = (new Grid())
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
            ]);

        return $this->page($grid->render(), 'Basic Grid');
    }

    /**
     *
     *
     * @return string
     */
    public function demo2()
    {
        $provider = $this->getDataProvider();
        $grid = (new Grid)
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
            ])
            ->setControls([
                new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET)),
                new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET))
        ]);
        (new SolidRow([new Text('additional row')]))
            ->attachTo($grid->components()->getTableHeading())
            ->setSortPosition(2);
        return $this->page($grid->render(), 'Filters');
    }

    public function demo3()
    {
        $provider = $this->getDataProvider();
        $grid = (new Grid())
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
            ])
            ->setControls([
                new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET)),
                new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET))
            ]);
        $grid->components()->setControlRow(new ControlRowWithColumns());
        return $this->page($grid->render(), 'Filters placed under column headers');
    }

    /**
     * Pagination
     *
     * @return string
     */
    public function demo4()
    {
        $provider = $this->getDataProvider();
        $grid = new Grid();
        $input = $grid->getInputSource();
        $grid
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
            ])
            ->setControls([
            new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
            new FilterControl('name', FilterOperation::OPERATOR_EQ, $input->option('name')),
            new FilterControl('birthday', FilterOperation::OPERATOR_EQ, $input->option('birthday')),
            $p = new PaginationControl($input->option('page', 1), 5, $provider)
        ]);
        $grid->components()->setControlRow(new ControlRowWithColumns());
        return $this->page($grid, 'Pagination');
    }

    /**
     * Totals
     *
     * @return string
     */
    public function demo5()
    {
        $provider = $this->getDataProvider();
        $grid = new Grid();
        $input = $grid->getInputSource();
        $grid
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
            ])
            ->setControls([
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
                new FilterControl('name', FilterOperation::OPERATOR_EQ, $input->option('name')),
                new FilterControl('birthday', FilterOperation::OPERATOR_EQ, $input->option('birthday')),
                new PaginationControl($input->option('page', 1), 5, $provider)
            ]);
        $grid->components()->getTableFooter()->addChild(new PageTotalsRow());
        return $this->page($grid, 'Page Totals');
    }
}