<?php
namespace ViewComponents\Grids\Component;

use ViewComponents\ViewComponents\Base\ComponentInterface;
use ViewComponents\ViewComponents\Base\Control\ControlInterface;
use ViewComponents\ViewComponents\Component\Part;
use League\Uri\Schemes\Http as HttpUri;
use ViewComponents\ViewComponents\Component\TemplateView;
use ViewComponents\ViewComponents\Data\Operation\DummyOperation;
use ViewComponents\ViewComponents\Data\Operation\SortOperation;
use ViewComponents\ViewComponents\Input\InputOption;

class ColumnSortingControl extends Part implements ControlInterface
{
    const DELIMITER = '-dir-';
    /**
     * @var string
     */
    protected $fieldName;
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
        if ($columnName !== $this->fieldName) {
            return null;
        }
        return $direction;
    }

    /**
     * @param string $fieldName
     * @param InputOption $input
     * @param ComponentInterface|null $view
     */
    public function __construct($fieldName, InputOption $input, ComponentInterface $view = null)
    {
        $this->fieldName = $fieldName;
        $this->inputOption = $input;
        parent::__construct($view, "column-$fieldName-sorting", "column-{$fieldName}-title-cell");
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
        return new SortOperation($this->fieldName, $direction);
    }

    protected function makeLinks()
    {
        $url = HttpUri::createFromServer($_SERVER);

        $asc = (string)$url->withQuery(
            (string)$url->query->merge(
                http_build_query(
                    [$this->inputOption->getKey() => $this->fieldName . static::DELIMITER . SortOperation::ASC]
                )
            )
        );
        $desc = (string)$url->withQuery(
            (string)$url->query->merge(
                http_build_query(
                    [$this->inputOption->getKey() => $this->fieldName . static::DELIMITER . SortOperation::DESC]
                )
            )
        );
        return compact('asc','desc');
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
        $this->getView()->setData([
            'order' => $this->getDirection(),
            'links' => $this->makeLinks()
        ]);
        return parent::render();
    }
}
