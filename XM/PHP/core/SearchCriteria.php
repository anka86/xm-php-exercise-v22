<?php 

namespace AngelosKanatsos\XM\PHP\Core;

class SearchCriteria
{
    public $companySymbol;
    public $startDate;
    public $endDate;
    public $email;

    public function __construct()
    {   
        $this->companySymbol = isset($_POST['company-symbol']) ? filter_var($_POST['company-symbol'], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP) : null;
        $this->startDate = isset($_POST['start-date']) ? filter_var($_POST['start-date'], FILTER_SANITIZE_STRING) : null;
        $this->endDate = isset($_POST['end-date']) ? filter_var($_POST['end-date'], FILTER_SANITIZE_STRING) : null;
        $this->email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : null;
    }

    public function getCompanySymbol(): string
    {
        return $this->companySymbol;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}