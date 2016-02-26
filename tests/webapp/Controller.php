<?php
namespace ViewComponents\Grids\WebApp;

use DateTime;

use ViewComponents\Grids\Component\ColumnSortingControl;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PageSizeSelectControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Component\DataView;
use ViewComponents\ViewComponents\Component\Html\Tag;
use ViewComponents\ViewComponents\Component\Part;
use ViewComponents\ViewComponents\Customization\Bootstrap\BootstrapStyling;
use ViewComponents\ViewComponents\Input\InputOption;
use ViewComponents\ViewComponents\Data\ArrayDataProvider;
use ViewComponents\ViewComponents\Data\DbTableDataProvider;
use ViewComponents\ViewComponents\Data\Operation\FilterOperation;
use ViewComponents\ViewComponents\Input\InputSource;
use ViewComponents\Grids\Component\Column;
use ViewComponents\Grids\Component\CsvExport;
use ViewComponents\Grids\Component\PageTotalsRow;
use ViewComponents\Grids\Component\SolidRow;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Service\Services;

class Controller extends AbstractController
{
    public $disableStandardCss = false;

    protected function getUsersData()
    {
        return include(TESTING_HELPERS_DIR . '/fixtures/users.php');
    }

    protected function getDataProvider($operations = [])
    {
        return (isset($_GET['use-db']) && $_GET['use-db'])
            ? new DbTableDataProvider(
                \ViewComponents\TestingHelpers\dbConnection(),
                'test_users',
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
        $out .= '<h1>Grids Demo App</h1><h2>Index Page</h2>';

        return $out;
    }

    public function demo1()
    {
        $provider = $this->getDataProvider();
        $grid = new Grid($provider, [
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
        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('role'),
            new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET)),
            new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET))
        ]);

//        $grid->getControlContainer()->addChildren([
//            //new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET)),
//            new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET))
//        ]);

        $grid->getComponent('table_heading')->addChild(
            new SolidRow([new DataView('additional row')])
        );
        return $this->page($grid->render(), 'Filters');
    }

    public function demo3()
    {
        $provider = $this->getDataProvider();
        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('role'),
            new Column('action'),
            new Part(new Tag('tr'), 'control_row2', 'table_heading'),
            new Part(new Tag('td'), 'id-c-row', 'control_row2'),
            new Part(new Tag('td'), 'name-c-row', 'control_row2'),
            new Part(new Tag('td'), 'role-c-row', 'control_row2'),
            new Part(new Tag('td'), 'action-c-row', 'control_row2'),
            (new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET)))
                ->setDestinationParentId('name-c-row'),
            (new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET)))
                ->setDestinationParentId('role-c-row'),
            new Part(new Tag('input', ['type' => 'submit']), 'submit_button', 'action-c-row')
        ]);
        $grid->getComponents()->remove($grid->getComponent('control_container', false));
        $grid->getComponents()->remove($grid->getComponent('control_row', false));
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
        $input = new InputSource($_GET);

        $grid = new Grid($provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
                new FilterControl('name', FilterOperation::OPERATOR_EQ, $input->option('name')),
                new FilterControl('birthday', FilterOperation::OPERATOR_EQ, $input->option('birthday')),
                new PaginationControl($input->option('page', 1), 5)
            ]
        );


        return $this->page($grid, 'Pagination');
    }

    /**
     * Pagination in table footer, multiple grids
     *
     * @return string
     */
    public function demo5()
    {
        $input = new InputSource($_GET);
        # Solution A
        # Attach pagination to grid, then move it to another part of tree using Tree API
        $provider = $this->getDataProvider();
        $grid1 = new Grid(
            $provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
                new Part(new SolidRow, 'footer_row', 'table_footer'),
                (new PaginationControl($input->option('g1_page', 1), 5))
                    ->setDestinationParentId('footer_row')
            ]
        );

        # Solution B
        $provider = $this->getDataProvider();
        $grid2 = new Grid(
            $provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
            ]
        );
        (new SolidRow)
            ->addChild(
                new PaginationControl($input->option('g2_page', 1), 5, $provider)
            )
            ->attachTo($grid2->getComponent('table_footer'));

        # Solution C
        $provider = $this->getDataProvider();
        $grid3 = new Grid(
            $provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
                new Part(
                    (new SolidRow)->addChild(
                        new PaginationControl($input->option('g3_page', 1), 5, $provider)
                    ),
                    'footer_row',
                    'table_footer'
                ),
            ]
        );

        return $this->page("$grid1 <hr> $grid2 <hr> $grid3", ' Pagination in table footer, multiple grids');
    }

    /**
     * Column: custom value + PageTotalsRow
     *
     * @return string
     */
    public function demo6()
    {
        $provider = $this->getDataProvider();
        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('role'),
            new Column('birthday'),
            (new Column('age'))
                ->setValueCalculator(function ($row) {
                    return DateTime
                        ::createFromFormat('Y-m-d', $row->birthday)
                        ->diff(new DateTime('now'))
                        ->y;

                })
                ->setValueFormatter(function ($val) {
                    return "$val years";
                })
            ,
            (new Column('random_number'))
                ->setValueCalculator(function ($row) {
                    if (empty($row->random_number)) {
                        $row->random_number = rand(1, 100);
                    }
                    return $row->random_number;
                }),
            new PageTotalsRow([
                'id' => function () {
                    return 'Totals:';
                },
                'age' => PageTotalsRow::OPERATION_AVG
            ])
        ]);

        $grid->getComponent('table_footer')->addChildren([
            new SolidRow([new PaginationControl(new InputOption('p', $_GET, 1), 5, $provider)])
        ]);

        return $this->page($grid, 'Column: custom value + PageTotalsRow');
    }

    /**
     * Column sorting
     *
     * @return string
     */
    public function demo7()
    {
        $provider = $this->getDataProvider();
        $sortingInput = new InputOption('sort', $_GET);
        $grid = new Grid($provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
                new PaginationControl(new InputOption('p', $_GET, 1), 5),
                new ColumnSortingControl('id', $sortingInput),
                new ColumnSortingControl('birthday', $sortingInput),
                new ColumnSortingControl('name', $sortingInput),
                new ColumnSortingControl('role', $sortingInput)
            ]
        );
        return $this->page($grid, 'column sorting');
    }

    public function demo8()
    {
        $provider = $this->getDataProvider();
        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('role'),
            new CsvExport(new InputOption('csv', $_GET))
        ]);
        return $this->page($grid->render(), 'CSV Export');
    }

    public function demo9()
    {
        $provider = $this->getDataProvider();
        $input = new InputSource($_GET);
        $grid = new Grid($provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
                new PaginationControl($input->option('page', 1), 5, $provider),
                new CsvExport($input->option('csv')),
            ]
        );
        return $this->page($grid->render(), 'CSV Export & Filter & Pagination');
    }

    public function demo10()
    {
        $provider = $this->getDataProvider();
        $input = new InputSource($_GET);
        $grid = new Grid($provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
                new PageSizeSelectControl($input->option('ps', 4), [2, 4, 10, 100]),
                new CsvExport($input->option('csv')),
                new PaginationControl($input->option('page', 1), 5, $provider),
            ]
        );
        return $this->page($grid->render(), 'PageSizeSelectControl');
    }

    public function demo11()
    {
        $input = new InputSource($_GET);
        $grid = new Grid($provider = $this->getDataProvider(),
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input('role')),
                new PageSizeSelectControl($input('ps', 4), [2, 4, 10, 100]),
                new CsvExport($input('csv')),
                new PaginationControl($input('page', 1), 5, $provider),
                new ColumnSortingControl('id', new InputOption('sort', $_GET))
            ]
        );

        $styling = new BootstrapStyling();
        $styling->apply($grid);
        $this->disableStandardCss = true;
        return $this->page($grid->render(), 'BootstrapStyling');
    }
}