<?php

namespace AngelosKanatsos\XM\PHP\core;

use DateTime, Exception;

class SearchCriteriaController
{
    protected $apiConsumer;
    protected $companies;
    protected $searchCriteria;
    protected $isValidSearchCriteria;
    protected $queryString;

    public function __construct(SearchCriteria $searchCriteria, AbstractApiConsumer $apiConsumer)
    {
        $this->apiConsumer = $apiConsumer;
        $this->searchCriteria = $searchCriteria;
        $this->isValidSearchCriteria = true;
        $this->validateSearchCriteria();
    }

    public function validateSearchCriteria(): void
    {
        // Check server side if symbol exists.
        $this->companies = json_decode($this->apiConsumer->loadData());
        $foundSymbol = false;
        foreach ($this->companies as $company) {
            if (strcasecmp($company->Symbol, $this->searchCriteria->companySymbol) == 0) {
                $foundSymbol = true;
            }
        }
        if (!$foundSymbol) {
            $messages['company-symbol'] = 'Not valid symbol';           
            $this->isValidSearchCriteria = false;
        } 
        // Check required fields 
        foreach ($this->searchCriteria as $key => $value) {
            if (empty($value)) {
                $messages[$key] = "is required";
                $this->isValidSearchCriteria = false;
            } else if ($key === 'startDate' || $key === 'endDate') {
                $messages[$key] = ($this->validateSqlDate($value) === 1 ? '' : 'is not valid format');
            }
        }

        // Check valid date interval
        if (!(empty($this->searchCriteria->startDate || empty($this->searchCriteria->endDate))) && $this->validateDateInterval()) {
        }
    }

    public function getCompanies(): array
    {
        return $this->companies;
    }

    public function isValidSearchCriteria(): bool
    {
        return $this->isValidSearchCriteria;
    }

    public function getHistoricalQuotesQueryString(): string
    {
        return "{$this->searchCriteria->companySymbol}.csv?order=asc&" .
            "start_date={$this->searchCriteria->startDate}&" .
            "end_date=" . $this->searchCriteria->endDate;
    }

    private function validateSqlDate(string $date)
    {
        return preg_match('/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/', $date);
    }

    private function validateDateInterval(): bool
    {
        $startDate = new DateTime($this->searchCriteria->startDate);
        $endDate = new DateTime($this->searchCriteria->endDate);
        $interval = $startDate->diff($endDate);
        if ($interval->days < 0) {
            $this->isValidSearchCriteria = false;
            $messages['date-interval'] = 'is not valid';            
            return false;
        }
        return true;
    }
}
