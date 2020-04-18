<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'autoloader.inc.php';
require_once __DIR__  . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.inc.php';

use AngelosKanatsos\XM\PHP\core\{
    ArrayDataTransformer,
    JsonApiConsumer,
    SearchCriteria,
    SearchCriteriaController,
    CsvApiConsumer,
    HtmlTableRenderer,
    Email,
    EmailSender,
    JsonReporter
};


global $messages;
$messages = [];
if (isset($_POST['service_type'])) {
    $searchCriteria = new SearchCriteria;
    $jsonReporter = new JsonReporter;
    switch ($_POST['service_type']) {
        case 'load_historical_quotes':
            if (!isset($_POST['table_id'])) {
                $jsonReporter->errorJSON("Be careful. You have to set table id attribute value.");                
            }                      
            $jsonApiConsumer = new JsonApiConsumer(COMPANIES_URL);
            $options = [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => COMPANIES_URL,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,                
                CURLOPT_VERBOSE => 0
            ];
            $jsonApiConsumer->setOptions($options);
            // Server side company symbol validation 
            $searchCriteriaController = new SearchCriteriaController($searchCriteria, $jsonApiConsumer);

            // If search critiria are valid, fetch historical quotes
            if ($searchCriteriaController->isValidSearchCriteria()) {
                $csvApiConsumer = new CsvApiConsumer(COMPANIES_HISTORICAL_QUOTES_BASE_URL . $searchCriteriaController->getHistoricalQuotesQueryString());
                $options = array(
                    'https' => array(
                        'method' => "GET",
                        'header' => "Accept-language: en\r\n"
                    )
                );
                $csvApiConsumer->setOptions($options);
                $csvData = $csvApiConsumer->loadData();
                $arrayDataTransformer = new ArrayDataTransformer($csvData, ["Date", "Open", "High", "Low", "Close", "Volume"]);
                $htmlTable = new HtmlTableRenderer($_POST['table_id'], $arrayDataTransformer->getDesiredColumns(false));

                // Get highchart's data
                $desiredColumns = ["Date", "Open", "Close"];
                $highChartColumns = new ArrayDataTransformer($csvData,  $desiredColumns);
                // $temp = $highChartColumns->getDateSeriesForHighChart();
                echo json_encode(array("html" => $htmlTable->renderHtmlTable(), "columns" => $desiredColumns, "highchart" => $highChartColumns->getDateSeriesForHighChart()));
            } else if(!empty($messages)) {
                $jsonReporter->errorJSON(implode(" | ", $messages));
            }
            break;
        case 'send_email':
            // Send email
            $email = new Email($searchCriteria->getCompanySymbol(), 'xm.test_aggeloskan@yahoo.com', $searchCriteria->getEmail(), 'From ' . $searchCriteria->getStartDate() . ' to ' . $searchCriteria->getEndDate());
            $sender = new EmailSender($email, $jsonReporter);
            $sender->sendEmail();
            break;
    }
} else {
    $jsonReporter->errorJSON('Please set service type.');
}
