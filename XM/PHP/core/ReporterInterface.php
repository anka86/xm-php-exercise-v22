<?php

namespace AngelosKanatsos\XM\PHP\core;

interface ReporterInterface
{
    public function print(array $array): void;
    public function format(string $message, string $type): string;    
}