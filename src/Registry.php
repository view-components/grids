<?php

namespace Presentation\Grids;

use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Component\ManagedList\Registry as BaseRegistry;
use Presentation\Grids\Component\SolidRow;

class Registry extends BaseRegistry
{
    /**
     * Fills registry with required components if it's absent.
     */
    public function useDefaults()
    {

        $this->has('record_view') || $this->setRecordView(new Tag('tr'));
        $this->has('table') || $this->setTable(new Tag('table'));
        $this->has('table_heading') || $this->setTableHeading(new Tag('thead'));
        $this->has('table_body') || $this->setTableBody(new Tag('tbody'));
        $this->has('table_footer') || $this->setTableFooter(new Tag('tfoot'));
        $this->has('title_row') || $this->setTitleRow(new Tag('tr'));
        $this->has('control_row') || $this->setControlRow(new SolidRow());
        parent::useDefaults();
    }

    /**
     * Sets 'form' component.
     *
     * This method is overriden,
     * becouse registry for gris does not requires specific
     * controls reattaching logic of managed list's registry
     *
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setForm(ComponentInterface $component)
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
     * @return ComponentInterface|null
     */
    public function getTableRow()
    {
        return $this->getRecordView();
    }

    /**
     * @param ComponentInterface|null $component
     * @return $this
     */
    public function setTableRow(ComponentInterface $component = null)
    {
        return $this->setRecordView($component);
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
}
