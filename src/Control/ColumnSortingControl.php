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
     * @var
     */
    protected $columnName;
    /**
     * @var InputOption
     */
    private $inputOption;

    protected function getDirection()
    {
        if (!$this->inputOption->hasValue()) {
            return null;
        }
        list($columnName, $direction) = explode(static::DELIMITER, $this->inputOption->getValue());
        if ($columnName !== $this->columnName) {
            return null;
        }
        return $direction;
    }

    /**
     * @param string $columnName
     * @param InputOption $input
     * @param ComponentInterface|null $view
     */
    public function __construct($columnName, InputOption $input, ComponentInterface $view = null)
    {
        $this->columnName = $columnName;
        $this->inputOption = $input;
        parent::__construct($view);
    }

    public function getOperation()
    {
        $direction = $this->getDirection();
        if ($direction === null) {
            return new DummyOperation();
        }
        return new SortOperation($this->columnName, $direction);
    }

    /**
     * @return ComponentInterface|DataAcceptorInterface
     */
    public function getView()
    {
        return parent::getView();
    }

    protected function getLinks()
    {
        $url = Url::createFromServer($_SERVER);
        $asc = $url->mergeQuery(
            Query::createFromArray([$this->inputOption->getKey() => $this->columnName . static::DELIMITER . SortOperation::ASC])
        );
        $desc = $url->mergeQuery(
            Query::createFromArray([$this->inputOption->getKey() => $this->columnName . static::DELIMITER . SortOperation::DESC])
        );
        return compact('asc','desc');
    }

    public function render()
    {
        $this->getView()->setData([
            'order' => $this->getDirection(),
            'links' => $this->getLinks()
        ]);
        return parent::render();
    }


}