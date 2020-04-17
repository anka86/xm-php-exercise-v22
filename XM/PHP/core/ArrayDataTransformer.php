<?php

namespace AngelosKanatsos\XM\PHP\Core;

/**
 * This class is used for manipulating multiple arrays
 */
class ArrayDataTransformer
{
    protected $desiredArrays;
    protected $data;
    protected $desiredColumns;

    public function __construct(array $data, array $desiredColumns)
    {
        $this->data = $data;
        $this->desiredColumns = array_map('strtoupper', $desiredColumns);
        $this->desiredArrays = [];
    }

    /**
     * This method combines one-to-one array values
     * in order to used in highchart series. [Date, Open] and 
     * [Date, Close] values.
     *
     * @return array|null
     */
    public function getDateSeriesForHighChart(): ?array
    {
        $desiredArrays = $this->getDesiredColumns(false);
        $dateSeries = $desiredArrays["Date"] ?? [];
        $openSeries = $desiredArrays["Open"] ?? [];
        $closeSeries = $desiredArrays["Close"] ?? [];

        $dateWithOpenSeries = $dateWithCloseSeries = [];
        $arrayKeys = array_keys($dateSeries);
        $lastArrayKey = array_pop($arrayKeys);
        if (isset($dateSeries[0])) {
            foreach ($dateSeries as $key => $value) {
                $formattedDate = $value . "T00:00:00.000Z";
                $openValue = $openSeries[$key] ?? 0;
                $closeValue = $closeSeries[$key] ?? 0;
                if ($key === $lastArrayKey && ($openValue === 0 || $closeValue === 0)) {
                    break;
                }
                $dateWithOpenSeries[] = array($formattedDate, floatval($openValue));
                $dateWithCloseSeries[] = array($formattedDate, floatval($closeValue));
            }
        }
        return array("Open" => $dateWithOpenSeries, "Close" => $dateWithCloseSeries);
    }

    /**
     * This method is used to create arrays from csv column titles.
     * 
     * @param boolean $shiftFirstElement
     * @return array|null
     */
    public function getDesiredColumns(bool $shiftFirstElement = true): ?array
    {
        $desiredKeys = $this->getColumnKeys();
        foreach ($desiredKeys as $key) {
            $row = array_column($this->data, $key);
            // Remove column title from the array
            if ($shiftFirstElement) {
                array_shift($row);
                $this->desiredArrays[] = $row;
            } else {
                $title = array_shift($row);
                $this->desiredArrays[$title] = $row;
            }
        }
        return $this->desiredArrays;
    }

    /**
     * This method is used to collect the keys that map the desirable values.
     *
     * @return array
     */
    private function getColumnKeys(): array
    {
        $desiredKeys = [];
        if (isset($this->data[0]) && is_array($this->data[0])) {
            foreach ($this->data[0] as $key => $columnValue) {
                if (in_array(strtoupper($columnValue), $this->desiredColumns)) {
                    $desiredKeys[] = $key;
                }
            }
        }
        return $desiredKeys;
    }
}
