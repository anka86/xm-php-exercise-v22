<?php

use AngelosKanatsos\XM\PHP\core\MessagesReporter;

$incDocBase = __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR;
require_once $incDocBase . 'autoloader.inc.php';
require_once __DIR__  . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'settings.inc.php';
$reporter = new MessagesReporter();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>PHP Exercise v22</title>
  <?php require_once $incDocBase . 'resources.inc.php'; ?>
</head>

<body>
  <div class="m-2">
    <?php $reporter->print($messages); ?>
    <!-- Email result -->
    <div class='p-3 mb-2 text-center bg-success text-dark font-weight-bold email-result'></div>
  </div>
  <p>
    <h1 class="text-center">Angelos Kanatsos</h1>
    <h2 class="text-center">- PHP Exercise v22 -</h2>
  </p>
  <div class="container-fluid">
    <form id="main-form">
      <div class="form-group">
        <label for="company-symbol">Company Symbol<span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="company-symbol" id="company-symbol" placeholder="Loading company symbols..." required>
        <div id="company-symbol-message" class="text-danger"></div>
      </div>
      <div class="form-group">
        <label for="start-date">Start Date<span class="text-danger">*</span></label>
        <input required class="form-control" type="text" value="" name="start-date" id="start-date" placeholder="YYYY-mm-dd" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" required>
        <div id="start-date-message" class="text-danger"></div>
      </div>
      <div class="form-group">
        <label for="end-date">End Date<span class="text-danger">*</span></label>
        <input required class="form-control" type="text" value="" name="end-date" id="end-date" placeholder="YYYY-mm-dd" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])">
        <div id="end-date-message" class="text-danger"></div>
      </div>
      <div class="form-group">
        <label for="email-address">Email address<span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="email-address" name="email" required>
        <div id="email-address-message" class="text-danger"></div>
      </div>
      <div class="form-group row float-right">
        <div class="col-sm-12">
          <button id="submit-main-form" type="submit" class="btn btn-primary btn-lg btn-block">Submit</button>
        </div>
      </div>
      <div class="form-group row float-left">
        <div class="col-sm-12">
          <button id="reset-main-form" type="button" class="btn btn-primary btn-lg btn-block">Reset</button>
        </div>
      </div>
    </form>

    <!-- No data found -->
    <div class='p-2 mb-2 text-center bg-warning text-dark font-weight-bold data-not-found'>No data found!</div>
    <!-- Datatable -->
    <div class="container-fluid" id="historical-quotes-container"></div>
    <div class="d-flex justify-content-center">
      <div class="spinner-border text-dark" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
    <!-- Highchart for historical quotes -->
    <div class="container-fluid" id="highchart-container"></div>
  </div>

  <script>
    $(function() {
      let localServicesUrl = '<?php echo LOCAL_SERVICES_URL; ?>';
      let companiesAPI = '<?php echo COMPANIES_URL; ?>';
      let mainFormSubmitBtn = $("#submit-main-form");
      let notFoundDiv = $(".data-not-found");
      let spinner = $("div.spinner-border");
      let emailResult = $("div.email-result");

      // Load companys' historical quotes
      $("#main-form").on("submit", function(event) {
        event.preventDefault();

        // UX        
        mainFormSubmitBtn.prop("disabled", true);
        spinner.show();
        notFoundDiv.hide();
        emailResult.hide();
        $("#historical-quotes-container,#highchart-container").text('');

        // Ajax call
        let parameters = $(this).serialize() + '&service_type=load_historical_quotes&table_id=historical-quotes';
        $.post(localServicesUrl, parameters)
          .done(function(data) {
            let parsedData = JSON.parse(data)
            // HIGHCHART & TABLE DATA            
            if (parsedData.highchart.Open !== null && parsedData.highchart.Open.length !== 0) {
              $("#historical-quotes-container").html(parsedData.html);
              console.log(parsedData);
              getHighChart(parsedData);
              notFoundDiv.hide();
            } else {
              notFoundDiv.show();
            }
            // UX
            mainFormSubmitBtn.prop("disabled", false);
            spinner.hide();
          });

        // Send asychronously email
        $.post(localServicesUrl, $(this).serialize() + '&service_type=send_email')
          .done(function(data) {           
            emailResult.text(data).show();
          });
      });


      // Load companies
      let availableCompanies = [];
      loadJsonData(companiesAPI, function(data) {
        availableCompanies = data;
        // In case the data has not already been loaded and the input field has data
        $("#company-symbol").keyup();
        if (availableCompanies.length > 1) {
          $("#company-symbol").prop("placeholder", "Eg. " + availableCompanies[0].Symbol + ", " + availableCompanies[1].Symbol);
        }
      });

      // Reset main form
      $("#reset-main-form").on("click", function() {
        $("#main-form").find("input[type=text],input[type=email]").val("");
      });

      // Check if company symbol exists
      $("#company-symbol").on("keyup", function() {
        let message = '';
        $(this).val($(this).val().toUpperCase());
        if (!isValidCompanyProperty(availableCompanies, "Symbol", $(this).val()) && $(this).val() !== '') {
          message = "Not valid symbol. Keep typing...";
        }
        updateErrorMessage(this.id, message);
      });

      // Set datepicker and validate range between start and end dates
      var dates = $("#start-date,#end-date").datepicker({
        defaultDate: "-1",
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        altFormat: "yyyy-mm-dd",
        showAnim: "clip",
        firstDay: 0,
        yearRange: '1980:' + new Date().getFullYear(),
        onSelect: function(selectedDate) {
          var reversedLimit = this.id == "start-date" ? "maxDate" : "minDate";
          var option = this.id == "start-date" ? "minDate" : "maxDate",
            instance = $(this).data("datepicker"),
            date = $.datepicker.parseDate(
              instance.settings.dateFormat ||
              $.datepicker._defaults.dateFormat,
              selectedDate, instance.settings);
          dates.not(this).datepicker("option", option, date);
          // max date interval
          var maxDatePeriod = "1200";
          var dateLimit = new Date(new Date(date).setMonth(date.getMonth() + maxDatePeriod));
          if (reversedLimit === "minDate") {
            dateLimit = new Date(new Date(date).setMonth(date.getMonth() - maxDatePeriod));
          } else if (new Date() < dateLimit) {
            dateLimit = new Date();
          }
          dates.not(this).datepicker("option", reversedLimit, dateLimit);
        }
      });
    });
  </script>
</body>

</html>