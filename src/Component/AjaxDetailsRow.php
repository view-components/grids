<?php

namespace ViewComponents\Grids\Component;

use ViewComponents\ViewComponents\Component\DataView;
use ViewComponents\ViewComponents\Resource\ResourceManager;

/**
 * This component adds hidden rows after each existing table row
 * and 'onclick' handlers that loads inner html of associated details row and displays it on click.
 *
 * AjaxDetailsRow includes jQuery on page if it was not included via view-components resource manager before.
 * If jQuery included to your page not via resource manager, it's possible to tell resource manager to ignore it,
 * see link below for instructions.
 * @link https://github.com/view-components/view-components/blob/master/doc/cookbook.md
 */
class AjaxDetailsRow extends DetailsRow
{
    protected $urlGenerator;

    /**
     * AjaxDetailsRow constructor.
     *
     * @param callable $urlGenerator
     * @param ResourceManager|null $resourceManager
     */
    public function __construct(callable $urlGenerator, ResourceManager $resourceManager = null)
    {
        parent::__construct(new DataView(null, function ($url) {
            return "<div data-details-container='1' data-details-url='$url'></div>";
        }), $resourceManager);
        $this->setUrlGenerator($urlGenerator);
    }

    /**
     * @return callable
     */
    public function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * @param callable $urlGenerator
     * @return $this
     */
    public function setUrlGenerator(callable $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        return $this;
    }

    /**
     * Renders component and returns output.
     *
     * @return string
     */
    public function render()
    {
        $this->view->setData(call_user_func($this->getUrlGenerator(), $this->getGrid()->getCurrentRow()));
        return SolidRow::render();
    }

    protected function getScriptSource()
    {
        return parent::getScriptSource() . '
            jQuery(\'tr[data-row-with-details="1"]\').click(function() {
                var $detailsRow = jQuery(this).next();
                var $detailsContainer;
                if (!$detailsRow.data(\'loaded\')) {
                    $detailsRow.data(\'loaded\', 1);
                    $detailsContainer = $detailsRow.find(\'[data-details-container="1"]\');
                    $detailsContainer.load($detailsContainer.data("details-url"));
                }
            });
        ';
    }
}
