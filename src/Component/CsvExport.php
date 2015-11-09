<?php

namespace Presentation\Grids\Component;

use League\Url\Query;
use League\Url\Url;
use Presentation\Framework\Base\ComponentInterface;
use Presentation\Framework\Base\ViewAggregate;
use Presentation\Framework\Component\Html\Tag;
use Presentation\Framework\Component\Text;
use Presentation\Framework\Data\DataProviderInterface;
use Presentation\Framework\Data\Operation\PaginateOperation;
use Presentation\Framework\Input\InputOption;
use Presentation\Grids\Grid;


class CsvExport extends ViewAggregate implements  InitializableInterface
{
    use InitializableTrait;

    private $fileName = 'data.csv';
    private $csvDelimiter = ';';
    private $exitFunction;
    /**
     * @var InputOption
     */
    private $inputOption;

    public function __construct(InputOption $inputOption = null, ComponentInterface $controlView = null)
    {
        $this->inputOption = $inputOption;
        parent::__construct($controlView);
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

    protected function initializeInternal(Grid $grid)
    {
        $grid->onRender(function() {
            if ($this->inputOption->hasValue())
            $this->renderCsv();
        });
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

    public function getExportUrl()
    {
        $baseUrl = Url::createFromServer($_SERVER);
        $url = $baseUrl->mergeQuery(
            Query::createFromArray([$this->inputOption->getKey() => 1])
        );
        return $url;
    }

    protected function makeDefaultView()
    {
        $href = $this->getExportUrl();
        return new Tag('button', [
            'type' => 'button',
            'onclick' => "window.location='$href'; return false;"
        ], [new Text('CSV Export')]);
    }
}
