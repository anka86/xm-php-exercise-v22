<?php

namespace AngelosKanatsos\XM\PHP\Core;

class CsvApiConsumer extends AbstractApiConsumer
{
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function loadData()
    {
        $context = stream_context_create($this->options);
        $data = @file_get_contents($this->url, false, $context);
        $fullTable = [];
        if ($data) {
            $fullTable = array_map("str_getcsv", explode("\n", $data));     
        } else {
            $messages['Historical Quotes'] = 'Historical quotes not found with these criteria.';            
        }
        return $fullTable;
    }
}
