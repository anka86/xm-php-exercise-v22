<?php

namespace AngelosKanatsos\XM\PHP\Core; 

abstract class AbstractApiConsumer
{
    protected $url;
    protected $curl;
    protected $data;
    protected $options;

    public function __construct(string $url)    
    {
        $this->url = $url;        
    }

    public function getData(): array
    {
        return $this->data;
    }

    abstract public function loadData();
    abstract public function setOptions(array $options): void;
}
