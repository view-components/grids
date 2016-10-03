<?php
namespace ViewComponents\Grids\WebApp;

use DateTime;
use ViewComponents\Grids\Component\AjaxDetailsRow;
use ViewComponents\Grids\Component\ColumnSortingControl;
use ViewComponents\Grids\Component\DetailsRow;
use ViewComponents\Grids\Component\TableCaption;
use ViewComponents\TestingHelpers\Application\Http\DefaultLayoutTrait;
use ViewComponents\TestingHelpers\Application\Http\TimingTrait;
use ViewComponents\ViewComponents\Component\Container;
use ViewComponents\ViewComponents\Component\Control\FilterControl;
use ViewComponents\ViewComponents\Component\Control\PageSizeSelectControl;
use ViewComponents\ViewComponents\Component\Control\PaginationControl;
use ViewComponents\ViewComponents\Component\DataView;
use ViewComponents\ViewComponents\Component\Debug\SymfonyVarDump;
use ViewComponents\ViewComponents\Component\Html\Tag;
use ViewComponents\ViewComponents\Component\ManagedList\ResetButton;
use ViewComponents\ViewComponents\Component\Part;
use ViewComponents\ViewComponents\Component\TemplateView;
use ViewComponents\ViewComponents\Customization\CssFrameworks\BootstrapStyling;
use ViewComponents\ViewComponents\Customization\CssFrameworks\FoundationStyling;
use ViewComponents\ViewComponents\Customization\CssFrameworks\SemanticUIStyling;
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

class Controller
{
    use DefaultLayoutTrait;
    use TimingTrait;

    private $defaultCss;

    public function __construct()
    {
        $this
            ->layout()
            ->section('head')
            ->addChild($this->defaultCss = new TemplateView('test/default_css'));
    }

    protected function getUsersData()
    {
        return include(TESTING_HELPERS_DIR . '/resources/fixtures/users.php');
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
        return $this->page('', 'Home');
    }

    public function demo1()
    {
        $this->prepareTiming();

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
        $this->prepareTiming();

        $provider = $this->getDataProvider();
        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('role'),
            new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET)),
            new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET))
        ]);
        $grid->getComponent('table_heading')->addChild(
            new SolidRow([new DataView('additional row')])
        );
        return $this->page($grid->render(), 'Filters');
    }

    public function demo3()
    {
        $this->prepareTiming();

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
        return $this->page($grid->render(), 'Filters placed under column headers');
    }


    public function demo3b()
    {
        $this->prepareTiming();

        $provider = $this->getDataProvider();
        $grid = new Grid($provider, [
            new Column('id'),
            new Column('name'),
            new Column('role'),
            new Column('action'),
            new Part(
                new Tag('tr',[], [
                    (new Tag('td')),
                    (new Tag('td'))->addChild(new FilterControl('name', FilterOperation::OPERATOR_EQ, new InputOption('name', $_GET))),
                    (new Tag('td'))->addChild(new FilterControl('role', FilterOperation::OPERATOR_EQ, new InputOption('role', $_GET))),
                    (new Tag('td'))->addChild(new Tag('input', ['type' => 'submit'])),
                ]),
                'control_row2',
                'table_heading'
            ),
        ]);
        return $this->page($grid->render(), 'Filters placed under column headers: alternative implementation');
    }

    /**
     * Pagination
     *
     * @return string
     */
    public function demo4()
    {
        $this->prepareTiming();

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
        $this->prepareTiming();

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
        $this->prepareTiming();

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
                'age' => PageTotalsRow::OPERATION_AVG,
                'random_number' => PageTotalsRow::OPERATION_SUM,
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
        $this->prepareTiming();

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
        $this->prepareTiming();

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
        $this->prepareTiming();

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
        $this->prepareTiming();

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

    protected function prepareDemo11Grid()
    {
        $input = new InputSource($_GET);
        $grid = new Grid($provider = $this->getDataProvider(),
            [
                new TableCaption('Demo 11: Customization. Table Caption'),
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
                (new Column('income'))->setValueFormatter(function ($value) {
                    if (!class_exists('\NumberFormatter')) {
                        return '$' . $value;
                    }
                    static $numberFormatter;
                    if ($numberFormatter === null) {
                        $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
                        $numberFormatter->setPattern(
                            '<span style="color:green">¤</span>#,##0.00;-<span style="color:red">¤</span>#,##0.00'
                        );
                    }
                    return $numberFormatter->format($value);
                }),
                new FilterControl('name', FilterOperation::OPERATOR_EQ, $input('name')),
                (new FilterControl('role', FilterOperation::OPERATOR_EQ, $input('role')))->setView(
                    new TemplateView('select', [
                        'options' => [
                            '' => 'All Roles',
                            'User' => 'Users',
                            'Manager' => 'Managers',
                            'Admin' => 'Admins',
                        ]
                    ])
                ),
                new PageSizeSelectControl($input('ps', 10), [5, 10, 20, 50, 100]),
                new CsvExport($input('csv')),
                new ResetButton(),
                new PageTotalsRow([
                    'id' => function () {
                        return 'Page totals';
                    },
                    'age' => PageTotalsRow::OPERATION_AVG,
                    'income' => PageTotalsRow::OPERATION_SUM,
                ]),
                new PaginationControl($input('page', 1), 10, $provider),
                new ColumnSortingControl('id', new InputOption('sort', $_GET)),
                new ColumnSortingControl('birthday', new InputOption('sort', $_GET))
            ]
        );

        $grid->getTileRow()->detach()->attachTo($grid->getTableHeading());
        $grid->attachTo($this->layout());
    }

    public function demo11_1()
    {
        $this->prepareTiming();
        $this->prepareDemo11Grid();
        BootstrapStyling::applyTo($this->layout());
        $this->defaultCss->detach();
        return $this->page(null, 'Bootstrap Styling');
    }

    public function demo11_2()
    {
        $this->prepareTiming();
        $this->prepareDemo11Grid();
        FoundationStyling::applyTo($this->layout());
        $this->defaultCss->detach();
        return $this->page(null, 'Foundation Styling');
    }

    public function demo11_3()
    {
        $this->prepareTiming();
        $this->prepareDemo11Grid();
        SemanticUIStyling::applyTo($this->layout());
        $this->defaultCss->detach();
        return $this->page(null, 'Semantic UI Styling');
    }

    public function demo12()
    {
        $this->prepareTiming();

        $grid = new Grid($provider = $this->getDataProvider(),
            [
                new Column('id'),
                new Column('name'),
                new TableCaption('Caption')
            ]
        );
        $grid->attachTo($this->layout());

        $styling = new BootstrapStyling();
        $styling->apply($this->layout());
        $this->defaultCss->detach();

        return $this->page(null, 'Grid Caption');
    }


    public function demo13()
    {
        $this->prepareTiming();

        $grid = new Grid($provider = $this->getDataProvider(),
            [
                new Column('id'),
                new Column('name'),
                new DetailsRow(new SymfonyVarDump())
            ]
        );
        $grid->attachTo($this->layout());

        $styling = new BootstrapStyling();
        $styling->apply($this->layout());
        $this->defaultCss->detach();
        return $this->page(null, 'DetailsRow (Click table rows to check)');
    }

    public function demo14()
    {
        $this->prepareTiming();

        if (isset($_GET['details'])) {
            // mark resources as already included
            $manager = Services::resourceManager();
            $manager->js('jquery');
            $manager->js('bootstrap');
            $manager->css('bootstrap');
            return $this->demo14Details();
        }
        $grid = new Grid($provider = $this->getDataProvider(),
            [
                new Column('id'),
                new Column('name'),
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
                new AjaxDetailsRow(function ($row) {
                    return "/index.php/demo14?details=1&id=" . $row->id;
                }),
                new PageTotalsRow([
                    'id' => PageTotalsRow::OPERATION_IGNORE,
                    'age' => PageTotalsRow::OPERATION_AVG
                ])
            ]
        );
        $grid->attachTo($this->layout());

        $styling = new BootstrapStyling();
        $styling->apply($this->layout());
        $this->defaultCss->detach();
        return $this->page(null, 'AjaxDetailsRow (Click table rows to check)');
    }

    protected function demo14Details()
    {
        $provider = $this->getDataProvider();
        $provider->operations()->add(new FilterOperation('id', FilterOperation::OPERATOR_EQ, $_GET['id']));
        foreach($provider as $item) {
            $view = new TemplateView('data_view/table', get_object_vars($item));
            BootstrapStyling::applyTo($view);
            $layout = new DataView(function () use ($view) {
                $out = $view->render();
                return "<div class='panel panel-default'>
                <div class='panel-body'>$out</div>
            </div>";
            });
            return $layout->render();
        }
    }
}