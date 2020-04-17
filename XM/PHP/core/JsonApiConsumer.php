<?php

namespace AngelosKanatsos\XM\PHP\Core;

use Exception;

class JsonApiConsumer extends AbstractApiConsumer
{
    public function __construct(string $url)
    {
        parent::__construct($url);
        $this->curl = curl_init();
    }

    public function setOptions(array $options): void
    {                
        foreach ($options as $option => $value) {
            curl_setopt($this->curl, $option, $value);
        }   
        $this->options = $options;     
    }

    public function loadData(): string
    {
        if(count($this->options) == 0) {
            throw new Exception("You have to set the CURL OPTIONS.");
        }
        $data = curl_exec($this->curl);
        if (curl_errno($this->curl)) {
            throw new Exception(curl_error($this->curl));
        }
        return $data;
    }
}
