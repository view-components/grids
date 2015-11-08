<?php

namespace Presentation\Grids\Component;

use Presentation\Framework\Base\ViewAggregate;
use Presentation\Framework\Data\DataProviderInterface;
use Presentation\Framework\Data\Operation\PaginateOperation;
use Presentation\Grids\Grid;


class CsvExport extends ViewAggregate implements  InitializableInterface
{
    use InitializableTrait;

    private $fileName = 'data.csv';
    private $csvDelimiter = ';';
    private $exitFunction;

    /**
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

    protected function initializeInternal(Grid $grid)
    {
        $grid->onRender([$this, 'renderCsv']);
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
        $file = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'. $this->getFileName() .'"');
        header('Pragma: no-cache');
        set_time_limit(0);

        $provider = $this->grid->getDataProvider();
        $this->renderHeadingRow($file);
        $this->removePagination($provider);
        foreach($provider as $row) {
            $output = [];
            $this->grid->setCurrentRow($row);
            foreach ($this->grid->getColumns() as $column) {
                    $output[] = $this->escapeString($column->getCurrentValueFormatted());

            }
            fputcsv($file, $output, $this->getCsvDelimiter());
        }
        fclose($file);
        $this->finishApplication();
    }

    protected function escapeString($str)
    {
        return str_replace('"', '\'', strip_tags(html_entity_decode($str)));
    }

    /**
     * @param resource $file
     */
    protected function renderHeadingRow($file)
    {
        $output = [];
        foreach ($this->grid->getColumns() as $column) {
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
}
