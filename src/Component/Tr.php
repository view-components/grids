<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Base\Html\AbstractTag;
use Presentation\Framework\Data\DataAcceptorInterface;

class Tr extends AbstractTag implements DataAcceptorInterface
{
    protected $rowData;
    protected $tCell;

    public function __construct()
    {
        parent::__construct(['data-role'=>'grid-row']);
    }

    public function getTagName()
    {
        return 'tr';
    }

    public function setData($data)
    {
        $this->setRowData($data);
    }

    /**
     * @return TCell
     */
    public function getTCell()
    {
        return $this->tCell;
    }

    /**
     * @param TCell $tCell
     * @return $this
     */
    public function setTCell($tCell)
    {
        $this->tCell = $tCell;
        return $this;
    }

    public function render()
    {
        $this->getTCell()->setRowData($this->rowData);
        return parent::render();
    }

    /**
     * @param mixed $rowData
     */
    public function setRowData($rowData)
    {
        $this->rowData = $rowData;
    }
}
