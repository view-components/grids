<?php

namespace Presentation\Grids;

use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\RepeaterInterface;
use Presentation\Framework\Control\ControlCollection;
use Presentation\Framework\Control\ControlInterface;
use Presentation\Framework\Data\DataAcceptorInterface;
use Presentation\Framework\Data\DataProviderInterface;
use Presentation\Framework\Input\InputSource;
use Presentation\Grids\Layout\ControlPlacingStrategy\ControlPlacingStrategyInterface;

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

    /** @var ComponentInterface */
    protected $submitButton;

    /** @var ComponentInterface */
    protected $tRow;

    /** @var ComponentInterface */
    protected $tCell;

    /** @var ComponentInterface */
    protected $table;

    /** @var Column[] */
    protected $columns = [];

    /** @var ControlCollection */
    protected $controls;

    /** @var  DataProviderInterface */
    protected $dataProvider;

    /** @var  RepeaterInterface */
    protected $rowRepeater;

    /** @var  RepeaterInterface */
    protected $columnRepeater;

    /** @var  InputSource */
    protected $inputSource;

    /** @var  ControlPlacingStrategyInterface */
    protected $controlPlacingStrategy;

    /**
     * @return ComponentInterface|null
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ComponentInterface|null $container
     * @return $this
     */
    public function setContainer(ComponentInterface $container = null)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return ComponentInterface|null
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param ComponentInterface|null $form
     * @return $this
     */
    public function setForm(ComponentInterface $form = null)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return ComponentInterface|null
     */
    public function getTHead()
    {
        return $this->tHead;
    }

    /**
     * @param ComponentInterface|null $tHead
     * @return $this
     */
    public function setTHead(ComponentInterface $tHead = null)
    {
        $this->tHead = $tHead;
        return $this;
    }

    /**
     * @return ComponentInterface|null
     */
    public function getTFoot()
    {
        return $this->tFoot;
    }

    /**
     * @param ComponentInterface|null $tFoot
     * @return $this
     */
    public function setTFoot(ComponentInterface $tFoot = null)
    {
        $this->tFoot = $tFoot;
        return $this;
    }

    /**
     * @return ComponentInterface|null
     */
    public function getTBody()
    {
        return $this->tBody;
    }

    /**
     * @param ComponentInterface|null $tBody
     * @return $this
     */
    public function setTBody(ComponentInterface $tBody = null)
    {
        $this->tBody = $tBody;
        return $this;
    }

    /**
     * @return DataProviderInterface|null
     */
    public function getDataProvider()
    {
        return $this->dataProvider;
    }

    /**
     * @param DataProviderInterface|null $dataProvider
     * @return $this
     */
    public function setDataProvider(DataProviderInterface $dataProvider = null)
    {
        $this->dataProvider = $dataProvider;
        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
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
     * @return ComponentInterface|null
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param ComponentInterface|null $table
     * @return $this
     */
    public function setTable(ComponentInterface $table = null)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return RepeaterInterface|null
     */
    public function getRowRepeater()
    {
        return $this->rowRepeater;
    }

    /**
     * @param RepeaterInterface|null $rowRepeater
     * @return $this
     */
    public function setRowRepeater(RepeaterInterface $rowRepeater = null)
    {
        $this->rowRepeater = $rowRepeater;
        return $this;
    }

    /**
     * @return ComponentInterface|DataAcceptorInterface|null
     */
    public function getTRow()
    {
        return $this->tRow;
    }

    /**
     * @param ComponentInterface|null $tRow
     * @return $this
     */
    public function setTRow(ComponentInterface $tRow = null)
    {
        $this->tRow = $tRow;
        return $this;
    }

    /**
     * @return RepeaterInterface|null
     */
    public function getColumnRepeater()
    {
        return $this->columnRepeater;
    }

    /**
     * @param RepeaterInterface|null $columnRepeater
     * @return $this
     */
    public function setColumnRepeater(RepeaterInterface $columnRepeater = null)
    {
        $this->columnRepeater = $columnRepeater;
        return $this;
    }

    /**
     * @return ComponentInterface|null
     */
    public function getTCell()
    {
        return $this->tCell;
    }

    /**
     * @param ComponentInterface|null $tCell
     * @return $this
     */
    public function setTCell(ComponentInterface $tCell = null)
    {
        $this->tCell = $tCell;
        return $this;
    }

    /**
     * @return InputSource|null
     */
    public function getInputSource()
    {
        return $this->inputSource;
    }

    /**
     * @param InputSource|null $inputSource
     * @return $this
     */
    public function setInputSource(InputSource $inputSource = null)
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    /**
     * @return ControlPlacingStrategyInterface|null
     */
    public function getControlPlacingStrategy()
    {
        return $this->controlPlacingStrategy;
    }

    /**
     * @param ControlPlacingStrategyInterface|null $controlPlacingStrategy
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
