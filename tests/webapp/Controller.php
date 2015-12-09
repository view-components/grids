<?php
namespace Presentation\Grids\Demo;

use DateTime;
use Presentation\Framework\Component\ManagedList\Control\FilterControl;
use Presentation\Framework\Component\ManagedList\Control\PageSizeSelectControl;
use Presentation\Framework\Component\ManagedList\Control\PaginationControl;
use Presentation\Framework\Component\Text;
use Presentation\Framework\Customization\Bootstrap\BootstrapStyling;
use Presentation\Framework\Input\InputOption;
use Presentation\Framework\Data\ArrayDataProvider;
use Presentation\Framework\Data\DbTableDataProvider;
use Presentation\Framework\Data\Operation\FilterOperation;
use Presentation\Framework\Input\InputSource;
use Presentation\Framework\Rendering\SimpleRenderer;
use Presentation\Framework\Resource\AliasRegistry;
use Presentation\Framework\Resource\IncludedResourcesRegistry;
use Presentation\Framework\Resource\ResourceManager;
use Presentation\Grids\Column;
use Presentation\Grids\Component\CsvExport;
use Presentation\Grids\Component\PageTotalsRow;
use Presentation\Grids\Component\Row;
use Presentation\Grids\Component\SolidRow;
use Presentation\Grids\Control\ColumnSortingControl;
use Presentation\Grids\Control\ColumnSortingView;
use Presentation\Grids\Grid;

class Controller extends AbstractController
{
    public $disableStandardCss = false;

    protected function getUsersData()
    {
        return include(dirname(__DIR__) . '/fixtures/users.php');
    }

    protected function getRenderer()
    {
        return new SimpleRenderer([
            __DIR__ . '/resources/views',
            dirname(dirname(__DIR__)) . '/resources/views'
        ]);
    }

    protected function getResourceManager()
    {
        return new ResourceManager(
            new AliasRegistry([
                'jquery' => '//code.jquery.com/jquery-2.1.4.min.js'
            ]),
            new AliasRegistry(),
            new IncludedResourcesRegistry()
        );
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
        $grid = (new Grid)
            ->setDataProvider($provider)
            ->setColumns([
                new Column('id'),
                new Column('name'),
                new Column('role'),
            ]);
        $grid->getControlContainer()->addChildren([
            new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET)),
            new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET))
        ]);
        $grid->getComponent('table_heading')->addChild(
            (new SolidRow([new Text('additional row')]))->setSortPosition(2)
        );
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
                new Column('action')
            ]);
        $grid->appendComponent('table_heading', 'control_row2', $row= new Row());

        $nameFilter = new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET));
        $roleFilter = new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET));
        $row->getCell('name')->addChild($nameFilter);
        $row->getCell('role')->addChild($roleFilter);
        $submitButton = $grid->getSubmitButton();
        $grid->removeComponent('submit_button');
        $grid->removeComponent('control_row');
        $row->getCell('action')->addChild($submitButton);

        $nameFilter->getView(true)->setLabel('');
        $roleFilter->getView(true)->setLabel('');

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

        $grid = new Grid(
            $provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
            ],
            [
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
                new FilterControl('name', FilterOperation::OPERATOR_EQ, $input->option('name')),
                new FilterControl('birthday', FilterOperation::OPERATOR_EQ, $input->option('birthday')),
                new PaginationControl($input->option('page', 1), 5, $provider)
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
            ],
            [
                $p = new PaginationControl($input->option('g1_page', 1), 5, $provider)
            ]
        );
        $grid1->appendComponent('table_footer', 'footer_row', new SolidRow);
        $grid1->moveComponent($p->getComponentName(), 'footer_row');

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
            ->attachTo($grid2->getTableFooter());

        # Solution C
        $provider = $this->getDataProvider();
        $grid3 = new Grid(
            $provider,
            [
                new Column('id'),
                new Column('name'),
                new Column('role'),
                new Column('birthday'),
            ]
        );
        # SolidRow will be accessible via $grid->getComponent('footer_row')
        # It would be convenient if you are planning to work with that component later
        $grid3->appendComponent(
            'table_footer',
            'footer_row',
            (new SolidRow)
                ->addChild(
                    new PaginationControl($input->option('g3_page', 1), 5, $provider)
                )
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
                ->setValueFormatter(function($val) {return "$val years";})
            ,
            (new Column('random_number'))
                ->setValueCalculator(function() {return rand(1,100);})
        ]);

        $grid->getComponent('table_footer')->addChildren([
            new PageTotalsRow([
                'id' => function() {return 'Totals:';},
                'age' => PageTotalsRow::OPERATION_AVG
            ]),
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
        $grid = new Grid($provider,
            [
            new Column('id'),
            new Column('name'),
            new Column('role'),
            new Column('birthday'),
        ],
            [new PaginationControl(new InputOption('p', $_GET, 1), 5, $provider)]
        );

        foreach ($grid->getColumns() as $column) {
            $column->getTitleCell()->addChild(
                new ColumnSortingControl(
                    $column->getName(),
                    new InputOption('sort', $_GET),
                    new ColumnSortingView($this->getRenderer())
                )
            );
        }
        return $this->page($grid, 'column sorting');
    }

    public function demo8()
    {
        $provider = $this->getDataProvider();
        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('role'),
        ]);
        $grid->getControlRow()->addChild(new CsvExport(new InputOption('csv',$_GET)));
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
            ],
            [
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
                new PaginationControl($input->option('page', 1), 5, $provider),
                new CsvExport($input->option('csv')),
            ]
        );
        return $this->page($grid->render(), 'CSV Export & Filter');
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
            ],
            [
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input->option('role')),
                new PageSizeSelectControl($input->option('ps', 4), [2,4,10,100]),
                new CsvExport($input->option('csv')),
                new PaginationControl($input->option('page', 1), 5, $provider),

            ]
        );
        //$grid->addChild(new PaginationControl($input->option('page', 1), 5, $provider));
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
            ],
            [
                new FilterControl('role', FilterOperation::OPERATOR_EQ, $input('role')),
                new PageSizeSelectControl($input('ps', 4), [2,4,10,100]),
                new CsvExport($input('csv')),
                new PaginationControl($input('page', 1), 5, $provider),
            ]
        );

        foreach ($grid->getColumns() as $column) {
            $column->getTitleCell()->addChild(
                new ColumnSortingControl(
                    $column->getName(),
                    new InputOption('sort', $_GET),
                    new ColumnSortingView($this->getRenderer())
                )
            );
        }

        $styling = new BootstrapStyling($this->getResourceManager());
        $styling->apply($grid);
        $this->disableStandardCss = true;
        return $this->page($grid->render(), 'BootstrapStyling');
    }
}