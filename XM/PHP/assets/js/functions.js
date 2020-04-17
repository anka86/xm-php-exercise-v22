// Check if a url is valid
function isValidUrl(url) {
    var urlPattern = new RegExp('^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.?)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // ip address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port
        '(\\?[;&amp;a-z\\d%_.~+=-]*)?' + // query string
        '(\\#[-a-z\\d_]*)?$', 'i');
    return urlPattern.test(url);
}

// Get api json data
function loadJsonData(apiUrl, handleData) {
    if (isValidUrl(apiUrl))
        $.ajax({
            url: apiUrl,
            contentType: "application/json",
            dataType: 'json',
            success: function (data) {
                handleData(data);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.responseText + "\n" + xhr.status + "\n" + thrownError);
            }
        });
    else {
        console.log("Invalid URL");
    }
}

// Validate company symbol
function isValidCompanyProperty(availableCompanies, propertyName, value) {
    return Boolean(Array.isArray(availableCompanies) && availableCompanies.findIndex(x => x[propertyName].toUpperCase() === value.toUpperCase()) >= 0);
}

// Update form field error message
function updateErrorMessage(id, message) {
    let mainFormSubmitBtn = $("#submit-main-form");
    if (typeof message !== undefined && message !== null && message !== '') {
        mainFormSubmitBtn.prop("disabled", true);
    } else {
        mainFormSubmitBtn.prop("disabled", false);
    }
    $("#" + id + "-message").text(message);
}

function getHighChart(parsedData) {
    $('#highchart-container').highcharts({
        title: {
            text: parsedData.columns[1] + '&' + parsedData.columns[2]
        },
        xAxis: {
            type: 'datetime',
            labels: {
                format: '{value:%Y-%m-%d}',
            }
        },
        series: [
            {
                name: parsedData.columns[1],
                data: parsedData.highchart.Open.map(function (point) {
                    return [
                        new Date(point[0]).getTime(),
                        point[1]
                    ];
                })
            },
            {
                name: parsedData.columns[2],
                data: parsedData.highchart.Close.map(function (point) {
                    return [
                        new Date(point[0]).getTime(),
                        point[1]
                    ];
                })
            }]
    });
}
