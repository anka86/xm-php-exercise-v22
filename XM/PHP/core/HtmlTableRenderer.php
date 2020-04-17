<?php

namespace AngelosKanatsos\XM\PHP\Core;

/**
 * This class is used to build the HTML datatable using the data
 * from the public csv api.
 */
class HtmlTableRenderer
{
    protected $data;    
    protected $htmlTableId;
    protected $htmlTable;

    public function __construct(string $htmlTableId, ?array $data)
    {
        $this->data = $data;
        $this->htmlTableId = $htmlTableId;
    }

    public function __toString()
    {
        return $this->renderHtmlTable();
    }

    /**
     * This function builds the final html table
     *
     * @return string
     */
    public function renderHtmlTable(): string
    {
        $this->htmlTable = '<table class="table" id="' . $this->htmlTableId . '">';
        // Not only headers
        if (isset($this->data["Date"]) && !empty($this->data["Date"][0])) {
            // column titles
            $arrayKeys =  array_keys($this->data);
            $this->htmlTable .= $this->formatTableHeader(array_keys($this->data));
            // Remove Date title
            array_shift($arrayKeys);
            // Build the table rows from columns 
            foreach ($this->data["Date"] as $key => $value) {
                $row = [$value];
                foreach ($arrayKeys as $title) {                    
                    $row[] = $this->data[$title][$key] ?? null;                    
                }
                $this->htmlTable .= $this->formatTableRow($row);
            }
        }
        $this->htmlTable .= "</table>";
        return $this->htmlTable;
    }

    /**
     * This function build the header rows of the table
     *
     * @param array $titles
     * @return string
     */
    private function formatTableHeader(array $titles): string
    {
        // Called once
        $row = '<thead class="thead-dark"><tr><th scope="col">#</th>';
        foreach ($titles as $key => $column) {
            $row .= ('<th scope="col">' . $column . '</th>');
        }
        $row .= "</tr></thead>";
        return $row;
    }

    /**
     * This function builds the body rows of the table.
     * I am using a counter for the first element as an extra
     * feature
     *
     * @param array $dataRow
     * @return string
     */
    private function formatTableRow(array $dataRow): string
    {
        $row = "<tr>";
        $foundCol = false;
        static $counter = 1;
        $row .= ('<td>' . $counter . '</td>');
        foreach ($dataRow as $key => $column) {
            if ($column !== null) {
                $row .= ('<td>' . $column . '</td>');
                $foundCol = true;
            }
        }
        $counter++;
        $row .= "</tr>";
        return ($foundCol ? $row : '');
    }
}
