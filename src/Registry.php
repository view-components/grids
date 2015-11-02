<?php

namespace Presentation\Grids;

use Closure;
use Nayjest\Collection\Extended\Registry as BaseRegistry;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Component\Repeater;

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
     * @return ComponentInterface|null
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
     * @return ComponentInterface|null
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
     * @return ComponentInterface|null
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
     * @return ComponentInterface|null
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
     * @return ComponentInterface|null
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
     * @return Repeater|null
     */
    public function getRepeater()
    {
        return $this->get('repeater');
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setRepeater(ComponentInterface $component = null)
    {
        return $this->set('repeater', $component);
    }


    /**
     * @return ComponentInterface|null
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
     * @return ComponentInterface|null
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
    #  NOT PLACED TO TREE
    #---------------------------------------------------------------------

    /**
     * @return ComponentInterface|null
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
