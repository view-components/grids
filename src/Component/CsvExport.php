<?php

namespace ViewComponents\Grids\Component;

use ViewComponents\ViewComponents\Base\ViewComponentInterface;
use ViewComponents\ViewComponents\Common\UriFunctions;
use ViewComponents\ViewComponents\Component\Compound;
use ViewComponents\ViewComponents\Component\DataView;
use ViewComponents\ViewComponents\Component\Html\Tag;
use ViewComponents\ViewComponents\Component\Part;
use ViewComponents\ViewComponents\Data\DataProviderInterface;
use ViewComponents\ViewComponents\Data\Operation\PaginateOperation;
use ViewComponents\ViewComponents\Input\InputOption;
use ViewComponents\Grids\Grid;

class CsvExport extends Part
{

    private $fileName = 'data.csv';
    private $csvDelimiter = ';';
    private $exitFunction;

    /**
     * @var InputOption
     */
    private $inputOption;

    public function __construct(InputOption $inputOption = null, ViewComponentInterface $controlView = null)
    {
        $this->inputOption = $inputOption;
        parent::__construct($controlView ?: $this->makeDefaultView(), 'csv_export', 'control_container');
    }

    /**
     * @param InputOption $inputOption
     * @return CsvExport
     */
    public function setInputOption($inputOption)
    {
        $this->inputOption = $inputOption;
        return $this;
    }

    /**
     * @return InputOption
     */
    public function getInputOption()
    {
        return $this->inputOption;
    }

    /**
     * Sets exit function.
     *
     * You may specify custom exit function to finish application cleanly.
     * 'exit' will be called after rendering CSV if no exit function specified.
     *
     * @param callable|null $exitFunction
     * @return $this
     */
    public function setExitFunction($exitFunction)
    {
        $this->exitFunction = $exitFunction;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getExitFunction()
    {
        return $this->exitFunction;
    }

    /**
     * @return string
     */
    public function getCsvDelimiter()
    {
        return $this->csvDelimiter;
    }

    /**
     * @param string $csvDelimiter
     * @return $this
     */
    public function setCsvDelimiter($csvDelimiter)
    {
        $this->csvDelimiter = $csvDelimiter;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setFileName($name)
    {
        $this->fileName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    public function attachToCompound(Compound $root, $prepend = false)
    {
        // prepend component that will render results
        $root->children()->add(new DataView(function () {
            if ($this->inputOption->hasValue()) {
                $this->renderCsv();
            }
        }), true);
        parent::attachToCompound($root, $prepend);
    }

    protected function removePagination(DataProviderInterface $provider)
    {
        $pagination = $provider->operations()->findByType(PaginateOperation::class);
        if ($pagination) {
            $provider->operations()->remove($pagination);
        }
    }

    protected function renderCsv()
    {
        ob_clean(); // removes previous output from buffer if grid was rendered inside view
        $file = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $this->getFileName() . '"');
        header('Pragma: no-cache');
        set_time_limit(0);
        /** @var Grid $grid */
        $grid = $this->root;
        $provider = $grid->getDataProvider();
        $this->renderHeadingRow($file);
        $this->removePagination($provider);
        foreach ($provider as $row) {
            $output = [];
            $grid->setCurrentRow($row);
            foreach ($grid->getColumns() as $column) {
                $output[] = $this->escapeString($column->getCurrentValueFormatted());
            }
            fputcsv($file, $output, $this->getCsvDelimiter());
        }
        fclose($file);
        $this->finishApplication();
    }

    protected function escapeString($str)
    {
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        $str = str_replace('"', '\'', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        $str = trim($str);
        return $str;
    }

    /**
     * @param resource $file
     */
    protected function renderHeadingRow($file)
    {
        $output = [];
        /** @var Grid $grid */
        $grid = $this->root;
        foreach ($grid->getColumns() as $column) {
            $output[] = $this->escapeString($column->getLabel());
        }
        fputcsv($file, $output, $this->getCsvDelimiter());
    }

    protected function finishApplication()
    {
        if (!$this->getExitFunction()) {
            exit;
        }
        call_user_func($this->getExitFunction());
    }

    public function getExportUrl()
    {
        return UriFunctions::modifyQuery(null, [$this->inputOption->getKey() => 1]);
    }

    protected function makeDefaultView()
    {
        $href = $this->getExportUrl();
        return new Tag(
            'button',
            [
                // required to avoid emitting 'click' on pressing enter
                'type' => 'button',
                'onclick' => "window.location='$href'; return false;",
                'style' => 'margin:2px;'
            ],
            [new DataView('CSV Export')]
        );
    }
}
