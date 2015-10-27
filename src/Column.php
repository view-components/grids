<?php

namespace Presentation\Grids;

use Presentation\Framework\Control\ControlCollection;
use Traversable;

class Column
{
    /**
     * Field name.
     *
     * @var string
     */
    protected $name;

    /**
     * Text label that will be rendered in table header.
     *
     * @var string
     */
    protected $label;

    protected $controls;

//    protected $headerView;

    /**
     * Constructor.
     *
     * @param string|null $name column unique name for internal usage
     * @param string|null $label column label
     */
    public function __construct($name = null, $label = null)
    {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($label !== null) {
            $this->setLabel($label);
        }
    }


    /**
     * Returns column name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets column name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns text label that will be rendered in table header.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label ? : ucwords(str_replace(array('-', '_', '.'), ' ', $this->name));
    }

    /**
     * Sets text label that will be rendered in table header.
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @todo remove this
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return ControlCollection
     */
    public function getControls()
    {
        if ($this->controls === null) {
            $this->controls = new ControlCollection();
        }
        return $this->controls;
    }

    /**
     * @param array|Traversable $controls
     * @return $this
     */
    public function setControls($controls)
    {
        $this->getControls()->set($controls);
        return $this;
    }
//
//    /**
//     * @return ComponentInterface|null
//     */
//    public function getHeaderView()
//    {
//        return $this->headerView;
//    }
//
//    /**
//     * @param mixed $headerView
//     * @return $this
//     */
//    public function setHeaderView($headerView)
//    {
//        $this->headerView = $headerView;
//        return $this;
//    }

}