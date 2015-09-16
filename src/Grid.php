<?php
namespace Presentation\Grids;

use Nayjest\Tree\NodeTrait;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\ComponentTrait;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Component\Repeater;
use Presentation\Framework\Component\Text;
use Presentation\Framework\Rendering\ViewTrait;
use Presentation\Grids\Component\Tr;

class Grid implements ComponentInterface
{
    use NodeTrait;
    use ViewTrait;
    use ComponentTrait;

    /** @var GridConfig */
    protected $config;

    private $isOperationsApplied = false;

    public function __construct(GridConfig $config)
    {
        $this->config = $config;
        $defaults = new ConfigDefaults();
        $defaults->apply($config);
    }

    /**
     * @return GridConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    protected function defaultChildren()
    {
        $this->applyOperations();
        return $this->makeComponents();
    }

    protected function makeComponents()
    {
        $config = $this->config;
        $container = $config->getContainer();

        $form = $config->getForm();
        $form->attachTo($container);

        $table = $config->getTable();
        $table->attachTo($form);

        $tHead = $config->getTHead();
        $tHead->attachTo($table);

        $labelTextComponent = new Text;
        $tHead->addChild(
            new Tag('tr', null, [new Repeater(
                $config->getColumns(),
                [new Tag('th', null, [$labelTextComponent])],
                function ($repeater, Column $column) use ($labelTextComponent) {
                    $labelTextComponent->setValue($column->getLabel());
                }
            )])
        );

        $config->getControlPlacingStrategy()->placeControls($config);

        $tBody = $config->getTBody();
        $tBody->attachTo($table);

        $tFoot = $config->getTFoot();
        $tFoot->attachTo($table);

        $rowRepeater = $config->getRowRepeater();
        $rowRepeater->setIterator($config->getDataProvider());
        $rowRepeater->attachTo($tBody);

        /** @var Tr $row */
        $row = $config->getTRow();
        $row->attachTo($rowRepeater);

        $columnRepeater = $config->getColumnRepeater();
        $columnRepeater->setIterator($config->getColumns());
        $columnRepeater->attachTo($row);

        $cell = $config->getTCell();
        $cell->attachTo($columnRepeater);
        $row->setTCell($cell);

        return [$container];
    }

    public function applyOperations()
    {
        if (!$this->isOperationsApplied) {
            $this->config->getControls()->applyOperations(
                $this->config->getDataProvider()
            );
            $this->isOperationsApplied = true;
        }
    }
}