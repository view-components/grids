<?php
namespace ViewComponents\Grids\Component;

use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Base\ComponentInterface;
use ViewComponents\ViewComponents\Base\Control\ControlInterface;
use ViewComponents\ViewComponents\Common\UriFunctions;
use ViewComponents\ViewComponents\Component\Part;
use ViewComponents\ViewComponents\Component\TemplateView;
use ViewComponents\ViewComponents\Data\Operation\DummyOperation;
use ViewComponents\ViewComponents\Data\Operation\SortOperation;
use ViewComponents\ViewComponents\Input\InputOption;

/**
 * ColumnSortingControl adds buttons for sorting grid data by specified column.
 * It's automatically placed after title of target column.
 */
class ColumnSortingControl extends Part implements ControlInterface
{
    const DELIMITER = '-dir-';
    /**
     * @var string
     */
    protected $columnId;
    /**
     * @var InputOption
     */
    private $inputOption;

    public function isManualFormSubmitRequired()
    {
        return false;
    }

    /**
     * Returns sorting direction for attached column if specified in input.
     *
     * @return string|null 'asc', 'desc' or null
     */
    protected function getDirection()
    {
        if (!$this->inputOption->hasValue()) {
            return null;
        }
        list($columnName, $direction) = explode(static::DELIMITER, $this->inputOption->getValue());
        if ($columnName !== $this->columnId) {
            return null;
        }
        return $direction;
    }

    /**
     * @param string $columnId
     * @param InputOption $input
     * @param ComponentInterface|null $view
     */
    public function __construct($columnId, InputOption $input, ComponentInterface $view = null)
    {
        $this->columnId = $columnId;
        $this->inputOption = $input;
        parent::__construct($view, "column-$columnId-sorting", "column-{$columnId}-title-cell");
    }

    /**
     * Creates operation for data provider.
     *
     * @return DummyOperation|SortOperation
     */
    public function getOperation()
    {
        $direction = $this->getDirection();
        if ($direction === null) {
            return new DummyOperation();
        }
        if ($this->root !== null) {
            /** @var Grid $root */
            $root = $this->root;
            $fieldName = $root->getColumn($this->columnId)->getDataFieldName();
        } else {
            $fieldName = $this->columnId;
        }
        return new SortOperation($fieldName, $direction);
    }

    protected function makeLinks()
    {
        $asc = UriFunctions::modifyQuery(
            null,
            [$this->inputOption->getKey() => $this->columnId . static::DELIMITER . SortOperation::ASC]
        );
        $desc = UriFunctions::modifyQuery(
            null,
            [$this->inputOption->getKey() => $this->columnId . static::DELIMITER . SortOperation::DESC]
        );
        $asc = UriFunctions::replaceFragment($asc, '');
        $desc = UriFunctions::replaceFragment($desc, '');
        return compact('asc', 'desc');
    }

    /**
     * Renders component.
     *
     * @return string
     */
    public function render()
    {
        if ($this->getView() === null) {
            $this->setView(new TemplateView('controls/column_sorting'));
        }
        $this->getView()->mergeData([
            'order' => $this->getDirection(),
            'links' => $this->makeLinks()
        ]);
        return parent::render();
    }
}
