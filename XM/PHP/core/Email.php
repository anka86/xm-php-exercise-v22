<?php

namespace AngelosKanatsos\XM\PHP\Core;

class Email
{
    protected $subject;
    protected $addressFrom;
    protected $addressTo;
    protected $content;

    public function __construct(string $subject, string $addressFrom, string $addressTo, string $content)
    {
        $this->subject = $subject;
        $this->addressFrom = $addressFrom;
        $this->addressTo = $addressTo;
        $this->content = $content;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getAddressFrom(): string
    {
        return $this->addressFrom;
    }

    public function getAddressTo(): string
    {
        return $this->addressTo;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
