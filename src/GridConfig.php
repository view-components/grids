<?php

namespace Presentation\Grids;

use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\RepeaterInterface;
use Presentation\Framework\Component\Html\Div;
use Presentation\Framework\Component\Html\Form;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Component\Repeater;
use Presentation\Framework\Control\ControlCollection;
use Presentation\Framework\Control\ControlInterface;
use Presentation\Framework\Data\DataProviderInterface;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Component\TCell;
use Presentation\Grids\Component\Tr;
use Presentation\Grids\Layout\ControlPlacingStrategy\ControlPlacingStrategyInterface;
use Presentation\Grids\Layout\ControlPlacingStrategy\SolidRowStrategy;

class GridConfig
{
    /** @var ComponentInterface */
    protected $container;

    /** @var ComponentInterface */
    protected $form;

    /** @var ComponentInterface */
    protected $tHead;

    /** @var ComponentInterface */
    protected $tFoot;

    /** @var ComponentInterface */
    protected $tBody;

    protected $submitButton;

    /** @var ComponentInterface */
    protected $tRow;

    protected $tCell;

    /** @var ComponentInterface */
    protected $table;

    protected $columns = [];

    protected $controls;

    /** @var  DataProviderInterface */
    protected $dataProvider;

    /** @var  RepeaterInterface */
    protected $rowRepeater;

    /** @var  RepeaterInterface */
    protected $columnRepeater;

    protected $inputSource;

    protected $controlPlacingStrategy;

    /**
     * @return ComponentInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ComponentInterface $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return ComponentInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param ComponentInterface $form
     * @return $this
     */
    public function setForm($form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return ComponentInterface
     */
    public function getTHead()
    {
        return $this->tHead;
    }

    /**
     * @param ComponentInterface $tHead
     * @return $this
     */
    public function setTHead($tHead)
    {
        $this->tHead = $tHead;
        return $this;
    }

    /**
     * @return ComponentInterface
     */
    public function getTFoot()
    {
        return $this->tFoot;
    }

    /**
     * @param ComponentInterface $tFoot
     * @return $this
     */
    public function setTFoot($tFoot)
    {
        $this->tFoot = $tFoot;
        return $this;
    }

    /**
     * @return ComponentInterface
     */
    public function getTBody()
    {
        return $this->tBody;
    }

    /**
     * @param ComponentInterface $tBody
     * @return $this
     */
    public function setTBody($tBody)
    {
        $this->tBody = $tBody;
        return $this;
    }

    /**
     * @return DataProviderInterface
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @param DataProviderInterface $dataProvider
     * @return $this
     */
    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param mixed $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @return ControlInterface[]|ControlCollection
     */
    public function getControls()
    {
        if ($this->controls === null) {
            $this->controls = new ControlCollection();
        }
        return $this->controls;
    }

    /**
     * @param array $controls
     * @return $this
     */
    public function setControls($controls)
    {
        $this->getControls()->set($controls);
        return $this;
    }

    /**
     * @return ComponentInterface
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param ComponentInterface $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return RepeaterInterface
     */
    public function getRowRepeater()
    {
        return $this->rowRepeater;
    }

    /**
     * @param RepeaterInterface $rowRepeater
     * @return $this
     */
    public function setRowRepeater($rowRepeater)
    {
        $this->rowRepeater = $rowRepeater;
        return $this;
    }

    /**
     * @return ComponentInterface
     */
    public function getTRow()
    {
        return $this->tRow;
    }

    /**
     * @param ComponentInterface $tRow
     * @return $this
     */
    public function setTRow($tRow)
    {
        $this->tRow = $tRow;
        return $this;
    }

    /**
     * @return RepeaterInterface
     */
    public function getColumnRepeater()
    {
        return $this->columnRepeater;
    }

    /**
     * @param RepeaterInterface $columnRepeater
     * @return $this
     */
    public function setColumnRepeater($columnRepeater)
    {
        $this->columnRepeater = $columnRepeater;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTCell()
    {
        return $this->tCell;
    }

    /**
     * @param mixed $tCell
     * @return $this
     */
    public function setTCell($tCell)
    {
        $this->tCell = $tCell;
        return $this;
    }

    public function initializeDefaults()
    {
        if (!$this->form) {
            $this->form = new Form();
        }

        if (!$this->container) {
            $this->container = new Div(['data-role'=> 'grid-container']);
        }

        if(!$this->columnRepeater) {
            $this->columnRepeater = new Repeater();
        }

        if(!$this->rowRepeater) {
            $this->rowRepeater = new Repeater();
        }

        if(!$this->tRow) {
            $this->tRow = new Tr();
        }

        if (!$this->table) {
            $this->table = new Tag('table');
        }

        if (!$this->tHead) {
            $this->tHead = new Tag('tHead');
        }

        if (!$this->tBody) {
            $this->tBody = new Tag('tbody');
        }

        if (!$this->tFoot) {
            $this->tFoot = new Tag('tFoot');
        }

        if (!$this->tCell) {
            $this->tCell = new TCell();
        }

        if (!$this->controlPlacingStrategy) {
            $this->controlPlacingStrategy = new SolidRowStrategy();
        }

        if (!$this->submitButton && !$this->getControls()->isEmpty()) {
            $this->submitButton = new Tag(
                'input',
                [
                    'type' => 'submit',
                    'data-role' => 'grid-submit'
                ]
            );
        }
    }

    /**
     * @return InputSource
     */
    public function getInputSource()
    {
        return $this->inputSource;
    }

    /**
     * @param InputSource $inputSource
     * @return $this
     */
    public function setInputSource(InputSource $inputSource = null)
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    /**
     * @return ControlPlacingStrategyInterface
     */
    public function getControlPlacingStrategy()
    {
        return $this->controlPlacingStrategy;
    }

    /**
     * @param mixed $controlPlacingStrategy
     * @return $this
     */
    public function setControlPlacingStrategy(ControlPlacingStrategyInterface $controlPlacingStrategy = null)
    {
        $this->controlPlacingStrategy = $controlPlacingStrategy;
        return $this;
    }

    /**
     * @return ComponentInterface|null
     */
    public function getSubmitButton()
    {
        return $this->submitButton;
    }

    public function setSubmitButton(ComponentInterface $submitButton = null)
    {
        $this->submitButton = $submitButton;
        return $this;
    }
}