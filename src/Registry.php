<?php

namespace Presentation\Grids;

use Closure;
use Nayjest\Collection\Extended\Registry as BaseRegistry;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\RepeaterInterface;
use Presentation\Framework\Component\Repeater;
use Presentation\Framework\Component\Text;
use Presentation\Grids\Component\TCell;
use Presentation\Grids\Component\Tr;

class Registry extends BaseRegistry
{

    public $defaults = [];

    public function get($name)
    {
        $item = parent::get($name);
        if ($item && $item instanceof Closure) {
            $this->items()[$name] = $item = $item();
        }
        return $item;
    }


    public function toArray()
    {
        foreach($this->items() as &$item) {
            if ($item && $item instanceof Closure) {
                $item = $item();
            }
        }
        return parent::toArray();
    }
    /**
     * @return null|ComponentInterface
     */
    public function getContainer()
    {
        return $this->get('container');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setContainer(ComponentInterface $component = null)
    {
        return $this->set('container', $component);
    }

    /**
     * @return null|ComponentInterface
     */
    public function getForm()
    {
        return $this->get('form');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setForm(ComponentInterface $component = null)
    {
        return $this->set('form', $component);
    }


    /**
     * @return null|ComponentInterface
     */
    public function getTable()
    {
        return $this->get('table');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTable(ComponentInterface $component = null)
    {
        return $this->set('table', $component);
    }

    /**
     * @return null|ComponentInterface
     */
    public function getTableHeading()
    {
        return $this->get('table_heading');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTableHeading(ComponentInterface $component = null)
    {
        return $this->set('table_heading', $component);
    }

    /**
     * @return null|ComponentInterface
     */
    public function getTableBody()
    {
        return $this->get('table_body');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTableBody(ComponentInterface $component = null)
    {
        return $this->set('table_body', $component);
    }

    /**
     * @return null|ComponentInterface
     */
    public function getTableFooter()
    {
        return $this->get('table_footer');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTableFooter(ComponentInterface $component = null)
    {
        return $this->set('table_footer', $component);
    }

    /**
     * @return Tr
     */
    public function getTitleRow()
    {
        return $this->get('title_row');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTitleRow(ComponentInterface $component = null)
    {
        return $this->set('title_row', $component);
    }

    /**
     * @return RepeaterInterface
     */
    public function getHeadingColumnRepeater()
    {
        return $this->get('heading_column_repeater');
    }

    /**
     * @param RepeaterInterface|null $component
     * @return $this
     */
    public function setHeadingColumnRepeater(RepeaterInterface $component = null)
    {
        return $this->set('heading_column_repeater', $component);
    }


    /**
     * @return ComponentInterface
     */
    public function getTitleCell()
    {
        return $this->get('title_cell');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTitleCell(ComponentInterface $component = null)
    {
        return $this->set('title_cell', $component);
    }


    /**
     * @return Repeater
     */
    public function getDataRowRepeater()
    {
        return $this->get('data_row_repeater');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setDataRowRepeater(ComponentInterface $component = null)
    {
        return $this->set('data_row_repeater', $component);
    }


    /**
     * @return Tr
     */
    public function getTableRow()
    {
        return $this->get('table_row');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTableRow(ComponentInterface $component = null)
    {
        return $this->set('table_row', $component);
    }


    /**
     * @return Repeater
     */
    public function getBodyColumnRepeater()
    {
        return $this->get('body_column_repeater');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setBodyColumnRepeater(ComponentInterface $component = null)
    {
        return $this->set('body_column_repeater', $component);
    }

    /**
     * @return TCell
     */
    public function getDataCell()
    {
        return $this->get('data_cell');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setDataCell(ComponentInterface $component = null)
    {
        return $this->set('data_cell', $component);
    }

    /**
     * @return Text
     */
    public function getTitleText()
    {
        return $this->get('title_text');
    }

    /**
     * @param Text $component
     * @return $this
     */
    public function setTitleText(Text $component)
    {
        return $this->set('title_text', $component);
    }

    /**
     * @return Text
     */
    public function getDataText()
    {
        return $this->get('data_text');
    }

    /**
     * @param Text $component
     * @return $this
     */
    public function setDataText(Text $component)
    {
        return $this->set('data_text', $component);
    }

    /**
     * @return null|ComponentInterface
     */
    public function getControlRow()
    {
        return $this->get('control_row');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setControlRow(ComponentInterface $component = null)
    {
        return $this->set('control_row', $component);
    }

    #---------------------------------------------------------------------
    #  NOT PLACED
    #---------------------------------------------------------------------

    /**
     * @return null|ComponentInterface
     */
    public function getSubmitButton()
    {
        return $this->get('submit_button');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setSubmitButton(ComponentInterface $component = null)
    {
        return $this->set('submit_button', $component);
    }
}
