<?php

namespace ViewComponents\Grids\Component;

use RuntimeException;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Base\Compound\PartInterface;
use ViewComponents\ViewComponents\Base\Compound\PartTrait;
use ViewComponents\ViewComponents\Base\DataViewComponentInterface;
use ViewComponents\ViewComponents\Base\Html\TagInterface;
use ViewComponents\ViewComponents\Component\Compound;
use ViewComponents\ViewComponents\Component\DataView;
use ViewComponents\ViewComponents\Resource\ResourceManager;
use ViewComponents\ViewComponents\Service\Services;

/**
 * This component adds hidden rows that are shown when clicking on grid row.
 */
class DetailsRow extends SolidRow implements PartInterface
{
    use PartTrait {
        PartTrait::attachToCompound as private attachToCompoundInternal;
    }

    const ID = 'details_row';

    protected $view;
    /** @var ResourceManager */
    private $resourceManager;
    private $jquery;

    /**
     * DetailsRow constructor.
     *
     * @param DataViewComponentInterface $view details row content, will be initialized by data row
     * @param ResourceManager|null $resourceManager
     */
    public function __construct(DataViewComponentInterface $view, ResourceManager $resourceManager = null)
    {
        parent::__construct();
        $this->getRowTag()
            ->setAttribute('style', 'display:none;')
            ->setAttribute('data-details-row', '1');
        $this->addChild($this->view = $view);
        $this->setDestinationParentId(Grid::COLLECTION_VIEW_ID);
        $this->setId('details_row');
        $this->resourceManager = $resourceManager ?: Services::resourceManager();
        $this->jquery = $this->resourceManager->js('jquery');
    }

    public function render()
    {
        $this->view->setData($this->getGrid()->getCurrentRow());
        return parent::render();
    }

    /**
     * @return null|Grid
     */
    protected function getGrid()
    {
        return $this->root;
    }

    public function attachToCompound(Compound $root, $prepend = false)
    {
        $isAlreadyAttached = $this->root !== null;
        $this->attachToCompoundInternal($root, $prepend);
        if ($isAlreadyAttached) {
            return;
        }
        $tr = $this->getGrid()->getRecordView();
        if (!$tr instanceof TagInterface) {
            throw new RuntimeException(
                "Details row works only with record_view components implementing TagInterface"
            );
        }
        $tr->setAttribute('data-row-with-details', 1);
        $this->getGrid()->children()
            ->add($this->jquery, 1)
            ->add($this->getScript());
        // fix zebra styled tables
        $this->parent()->addChild(new DataView('<tr style="display: none"></tr>'));
    }

    protected function getScript()
    {
        $source = $this->getScriptSource();
        return new DataView("<script>jQuery(function(){ $source });</script>");
    }

    protected function getScriptSource()
    {
        return '
            jQuery(\'tr[data-row-with-details="1"]\').click(function() {
                jQuery(this).next().toggle(\'slow\');
            });
        ';
    }
}
