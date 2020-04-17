<?php

namespace AngelosKanatsos\XM\PHP\Core;

interface ReporterInterface
{
    public function print(array $array): void;
    public function format(string $message, string $type): string;    
}