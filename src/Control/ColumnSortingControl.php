<?php
namespace Presentation\Grids\Control;

use League\Url\Query;
use League\Url\Url;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\ViewAggregate;
use Presentation\Framework\Component\ManagedList\Control\ControlInterface;
use Presentation\Framework\Data\DataAcceptorInterface;
use Presentation\Framework\Data\Operation\DummyOperation;
use Presentation\Framework\Data\Operation\SortOperation;
use Presentation\Framework\Input\InputOption;

class ColumnSortingControl extends ViewAggregate implements ControlInterface
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
        parent::__construct($view);
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

    /**
     * @return ComponentInterface|DataAcceptorInterface
     */
    public function getView()
    {
        return parent::getView();
    }

    /**
     * Build links for sorting based on current URL's.
     *
     * @return array resulting array contains 'asc' and 'desc' keys with corresponding URL's for sorting.
     */
    protected function makeLinks()
    {
        $url = Url::createFromServer($_SERVER);
        $asc = $url->mergeQuery(
            Query::createFromArray([$this->inputOption->getKey() => $this->fieldName . static::DELIMITER . SortOperation::ASC])
        );
        $desc = $url->mergeQuery(
            Query::createFromArray([$this->inputOption->getKey() => $this->fieldName . static::DELIMITER . SortOperation::DESC])
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
        $this->getView()->setData([
            'order' => $this->getDirection(),
            'links' => $this->makeLinks()
        ]);
        return parent::render();
    }
}
