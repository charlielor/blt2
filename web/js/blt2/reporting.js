$(document).ready(function() {
    var request = $("#request");
    var token1 = $("#token1");

    var dateBeginButton = $("#dateBeginButton");
    var dateEndButton = $("#dateEndButton");

    var dateBeginModal = $("#dateBeginModal");
    var dateEndModal = $("#dateEndModal");
    var dateBegin = $("#dateBegin");
    var dateEnd = $("#dateEnd");

    var shipperSelect = $(".shipperSelect");
    var select2Shipper = $("#select2-Shipper");

    var vendorSelect = $(".vendorSelect");
    var select2Vendor = $("#select2-Vendor");

    var receiverSelect = $(".receiverSelect");
    var select2Receiver = $("#select2-Receiver");

    var userSelect = $(".userSelect");
    var select2User = $("#select2-ShibbolethUser");

    var submitReportRequest = $("#submitReportRequest");

    var spinnerGraph = $("#spinnerGraph");

    // Get current date
    var currentDate = new Date();

    var requestQuery = {
        "request": null,
        "tokenId": null,
        "dateBegin": null,
        "dateEnd": null,
        "type": null
    };

    var requestTable = {
        "request": null,
        "tokenId": null,
        "dateBegin": null,
        "dateEnd": null,
        "type": null
    };

    var graphQueryResults = $("#graphQueryResults");
    var queryResultsText = $("#queryResultsText");

    var reportResultsGraph = $("#reportResultsGraph");
    var reportResultsChart = null;

    var tableQueryResults = $("#tableQueryResults");
    var reportResultsTableBody = $("#reportResultsTableBody");

    function showSelect2Input() {
        // Clear all select2 inputs
        select2Receiver.val(null).trigger("change");
        select2Shipper.val(null).trigger("change");
        select2Vendor.val(null).trigger("change");
        select2User.val(null).trigger("change");
        
        // Get the entity requested
        var requestSplit = request.val().split("-");
        // Always the last element
        var entityRequested = requestSplit[requestSplit.length - 1];

        // Depending on which request type is selected, show/enable the respective select2 input
        switch (entityRequested) {
            case "receiver":
                receiverSelect.show();
                select2Receiver.prop("disabled", false);

                shipperSelect.hide();
                vendorSelect.hide();
                userSelect.hide();
                break;
            case "shipper":
                shipperSelect.show();
                select2Shipper.prop("disabled", false);

                receiverSelect.hide();
                vendorSelect.hide();
                userSelect.hide();
                break;
            case "vendor":
                vendorSelect.show();
                select2Vendor.prop("disabled", false);

                receiverSelect.hide();
                shipperSelect.hide();
                userSelect.hide();
                break;
            case "user":
                userSelect.show();
                select2User.prop("disabled", false);

                receiverSelect.hide();
                shipperSelect.hide();
                vendorSelect.hide();
                break;
            default:
                select2Receiver.prop("disabled", true);
                select2Shipper.prop("disabled", true);
                select2Vendor.prop("disabled", true);
                select2User.prop("disabled", true);
                requestQuery["tokenId"] = null;
                break;
        }
    }

    function getListOfUsers() {
        $.get('users', function(response) {
            if (response['result'] == 'success') {
                if (response['object'].length != 0) {
                    token1.empty();
                    for (var i = 0; i < response['object'].length; i++) {
                        var option = '<option value=' + response['object'][i]['username'] + '>' + response['object'][i]['username'] + '</option>';

                        select2User.append(option);
                    }
                }
            } else {
                alert("Fail to update page");
            }

        });
    }

    function clearTable() {
        reportResultsTableBody.empty();
    }

    function clearGraph() {
        if (reportResultsChart != null) {
            reportResultsChart.destroy();
        }
    }

    function clearGraphAndTable() {
        clearGraph();
        clearTable();
        spinnerGraph.hide();
        graphQueryResults.hide();
        tableQueryResults.hide();

        submitReportRequest.text("Go!");
        submitReportRequest.removeAttr("disabled");

        $("#graphToTable").text("Graph to Table");
        $("#graphToTable").removeAttr("disabled");
    }

    $("#tableToCSV").click(function() {
        var requestDownload = requestTable;

        requestDownload["type"] = "d-csv";

        window.location.href = "reporting/query?" + "request=" + requestDownload["request"] + "&tokenId=" + requestDownload["tokenId"] + "&dateBegin=" + requestDownload["dateBegin"] + "&dateEnd=" + requestDownload["dateEnd"] + "&type=" + requestDownload["type"];
    });

    $(request).on("change", request, function() {
        showSelect2Input();
        //getEntities();
    });


    dateBegin.datepicker({
        defaultDate: currentDate,
        maxDate: currentDate,
        onSelect: function(selectedDate) {
            // Change the button to the date to the selected date
            dateBeginButton.text(selectedDate);

            dateBeginModal.modal("hide");
        }
    });

    dateEnd.datepicker({
        defaultDate: currentDate,
        maxDate: currentDate,
        onSelect: function(selectedDate) {
            // Change the button to the date to the selected date
            dateEndButton.text(selectedDate);

            dateEndModal.modal("hide");
        }
    });

    dateBeginButton.click(function() {
        dateBeginModal.modal("show");
    });

    dateEndButton.click(function() {
        dateEndModal.modal("show");
    });


    submitReportRequest.on("click", function() {
        // Get the entity requested
        var requestSplit = request.val().split("-");
        // Always the last element
        var entityRequested = requestSplit[requestSplit.length - 1];

        // Token
        var tokenId = null;
        var tokenName = null;

        switch (entityRequested) {
            case "receiver":
                tokenId = select2Receiver.val();
                tokenName = select2Receiver.text();
                break;
            case "shipper":
                tokenId = select2Shipper.val();
                tokenName = select2Shipper.text();
                break;
            case "vendor":
                tokenId = select2Vendor.val();
                tokenName = select2Vendor.text();
                break;
            case "user":
                tokenId = select2User.val();
                tokenName = select2User.text();
                break;
            default:
                break;
        }

        if ((tokenId === "" || tokenId === null) && entityRequested !== "none") {
            $("#emptyTokenModal").modal('show');
            // Must have selecting from a null select2
            switch (entityRequested) {
                case "receiver":
                    select2Receiver.focus();
                    break;
                case "shipper":
                    select2Shipper.focus();
                    break;
                case "vendor":
                    select2Vendor.focus();
                    break;
                case "user":
                    select2User.focus();
                    break;
                default:
                    break;
            }
            return;
        } else {
            // Clear all select2 inputs
            select2Receiver.val(null).trigger("change");
            select2Shipper.val(null).trigger("change");
            select2Vendor.val(null).trigger("change");
            select2User.val(null).trigger("change");
        }


        // Make sure dates are in chronological order
        if (dateBegin.datepicker("getDate") > dateEnd.datepicker("getDate")) {
            $("#dateRangeErrorModal").modal('show');
            return;
        }

        requestQuery = {
            "request": request.val(),
            "tokenId": tokenId,
            "dateBegin": dateBegin.datepicker("getDate").toDateString(),
            "dateEnd": dateEnd.datepicker("getDate").toDateString(),
            "type": 'g-graph'
        };

        clearGraphAndTable();

        spinnerGraph.show();

        submitReportRequest.text("Fetching...");
        submitReportRequest.attr("disabled", "true");

        $.get("reporting/query", requestQuery, function(response) {
            spinnerGraph.hide();

            submitReportRequest.text("Go!");
            submitReportRequest.removeAttr("disabled");

            if (response["result"] == "success") {
                var requestedQuery = response['requestedQuery'].split("-");

                if (response['object'].length > 0) {

                    graphQueryResults.show();

                    var dates = response['object'];
                    var reportResultsDates = [];
                    var reportResultsCount = [];

                    //if (dates.length > 15) {
                    //    reportResultsGraph.css('width', '1000');
                    //    reportResultsGraph.css('height', '500px');
                    //} else if (dates.length > 15) {
                    //    reportResultsGraph.css('width', '1000px');
                    //    reportResultsGraph.css('height', '500px');
                    //} else if (dates.length > 10) {
                    //    reportResultsGraph.css('width', '800px');
                    //    reportResultsGraph.css('height', '400px');
                    //} else if (dates.length > 5) {
                    //    reportResultsGraph.css('width', '600px');
                    //    reportResultsGraph.css('height', '300px');
                    //} else {
                    //    reportResultsGraph.css('width', '600px');
                    //    reportResultsGraph.css('height', '300px');
                    //}

                    for (var i = 0; i < dates.length; i++) {
                        if (dates[i] != null) {
                            reportResultsDates.push(dates[i]['d']);
                            reportResultsCount.push(dates[i]['numOfPackages']);
                        }
                    }

                    var reportResultsData = {
                        labels: reportResultsDates,
                        datasets: [
                            {
                                label: "Packages " + tokenName + " received",
                                fillColor: "rgba(151,187,205,0.5)",
                                strokeColor: "rgba(151,187,205,0.8)",
                                highlightFill: "rgba(151,187,205,0.75)",
                                highlightStroke: "rgba(151,187,205,1)",
                                data: reportResultsCount
                            }
                        ]
                    };

                    var pSDCTX = reportResultsGraph.get(0).getContext("2d");

                    reportResultsChart = new Chart(pSDCTX).Bar(reportResultsData, {
                        barShowStroke: false
                    });
                } else {
                    $("#emptySetModal").modal('show');
                }
            } else {
                $("#queryDatabaseErrorModal").modal('show');
            }

            spinnerGraph.hide();

            submitReportRequest.text("Go!");
            submitReportRequest.removeAttr("disabled");

        }).fail(function() {
            $("#queryDatabaseErrorModal").modal('show');

            spinnerGraph.hide();

            submitReportRequest.text("Go!");
            submitReportRequest.removeAttr("disabled");
        });

    });

    $("#reportResultsGraph, #graphToTable").click(function(e) {

        var valid = false;
        var btn = false;

        // Figure out what was clicked
        if (this.id == "graphToTable") {

            requestTable = {
                "request": requestQuery["request"],
                "tokenId": requestQuery["tokenId"],
                "dateBegin": requestQuery["dateBegin"],
                "dateEnd": requestQuery["dateEnd"],
                "type": 't-table'
            };

            valid = true;
            btn = true;
            $("#graphToTable").text("Generating...");
            $("#graphToTable").attr("disabled", "true");

        } else if (this.id == "reportResultsGraph") {
            var barClicked = reportResultsChart.getBarsAtEvent(e);

            if (barClicked.length != 0) {
                if (barClicked[0]["label"].split(' - ').length > 1) {
                    requestTable = {
                        "request": requestQuery["request"],
                        "tokenId": requestQuery["tokenId"],
                        "dateBegin": barClicked[0]["label"].split(' - ')[0],
                        "dateEnd": barClicked[0]["label"].split(' - ')[1],
                        "type": 't-table'
                    };
                } else if (barClicked[0]["label"].split("/").length == 2) {
                    var month = barClicked[0]["label"].split('/')[0];
                    var year =barClicked[0]["label"].split('/')[1];

                    var firstDayOfMonth = new Date(year, Number(month) - 1);
                    var lastDayOfMonth = new Date(year, Number(month), 0);

                    requestTable = {
                        "request": requestQuery["request"],
                        "tokenId": requestQuery["tokenId"],
                        "dateBegin": firstDayOfMonth,
                        "dateEnd": lastDayOfMonth,
                        "type": 't-table'
                    };
                } else {
                    requestTable = {
                        "request": requestQuery["request"],
                        "tokenId": requestQuery["tokenId"],
                        "dateBegin": barClicked[0]["label"],
                        "dateEnd": barClicked[0]["label"],
                        "type": 't-table'
                    };
                }


                valid = true;
            }

        }

        if (valid) {

            tableQueryResults.hide();

            clearTable();

            $.get("reporting/query", requestTable, function(response) {

                var packagesFromServer = null;

                if (response['result'] == 'success') {
                    clearTable();

                    packagesFromServer = response['object'];

                    $.each(packagesFromServer, function(index, element) {

                        // Create string-friendly dateReceived
                        var dateReceived = new Date(element["dateReceived"]["timestamp"] * 1000);
                        var month = (dateReceived.getMonth() + 1);
                        month = month < 10 ? '0' + month : month;
                        var date = dateReceived.getDate();
                        date = date < 10 ? '0' + date : date;
                        var dateReceivedString = month + '-' + date + '-' + dateReceived.getFullYear();

                        if (element["pickedUp"] === true) {
                            // Create string-friendly dateDelivered
                            var datePickedUp = new Date(element["datePickedUp"]["timestamp"] * 1000);
                            month = (datePickedUp.getMonth() + 1);
                            month = month < 10 ? '0' + month : month;
                            date = datePickedUp.getDate();
                            date = date < 10 ? '0' + date : date;
                            var datePickedUpString = month + '-' + date + '-' + datePickedUp.getFullYear();
                            reportResultsTableBody.append(
                                "<tr>" +
                                "<td>" + element["trackingNumber"] + "</td>" +
                                "<td>" + element["vendorName"] + "</td>" +
                                "<td>" + element["shipperName"] + "</td>" +
                                "<td>" + element["receiverName"] + "</td>" +
                                "<td>" + element["numberOfPackages"] + "</td>" +
                                "<td>" + element["userWhoReceived"] + "</td>" +
                                "<td>" + dateReceivedString + "</td>" +
                                "<td colspan='3' class='text-center'> Package picked up </td>" +
                                "<td> YES </td>" +
                                "<td>" + datePickedUpString + "</td>" +
                                "<td>" + element["userWhoPickedUp"] + "</td>" +
                                "<td>" + element["userWhoAuthorizedPickUp"] + "</td>" +
                                "</tr>"
                            );
                        } else if (element["delivered"] === true) {
                            // Create string-friendly dateDelivered
                            var dateDelivered = new Date(element["dateDelivered"]["timestamp"] * 1000);
                            month = (dateDelivered.getMonth() + 1);
                            month = month < 10 ? '0' + month : month;
                            date = dateDelivered.getDate();
                            date = date < 10 ? '0' + date : date;
                            var dateDeliveredString = month + '-' + date + '-' + dateDelivered.getFullYear();

                            reportResultsTableBody.append(
                                "<tr>" +
                                "<td>" + element["trackingNumber"] + "</td>" +
                                "<td>" + element["vendorName"] + "</td>" +
                                "<td>" + element["shipperName"] + "</td>" +
                                "<td>" + element["receiverName"] + "</td>" +
                                "<td>" + element["numberOfPackages"] + "</td>" +
                                "<td>" + element["userWhoReceived"] + "</td>" +
                                "<td>" + dateReceivedString + "</td>" +
                                "<td> YES </td>" +
                                "<td>" + dateDeliveredString + "</td>" +
                                "<td>" + element["userWhoDelivered"] + "</td>" +
                                "<td colspan='4' class='text-center'> Package delivered </td>" +
                                "</tr>"
                            );
                        } else {
                            reportResultsTableBody.append(
                                "<tr class='danger'>" +
                                "<td>" + element["trackingNumber"] + "</td>" +
                                "<td>" + element["vendorName"] + "</td>" +
                                "<td>" + element["shipperName"] + "</td>" +
                                "<td>" + element["receiverName"] + "</td>" +
                                "<td>" + element["numberOfPackages"] + "</td>" +
                                "<td>" + element["userWhoReceived"] + "</td>" +
                                "<td>" + dateReceivedString + "</td>" +
                                "<td colspan='3' class='text-center'> Package NOT delivered </td>" +
                                "<td colspan='4' class='text-center'> Package NOT picked up </td>" +
                                "</tr>"
                            );
                        }
                    });

                    tableQueryResults.show();
                } else {
                    $("#queryDatabaseErrorModal").modal('show');
                }

                if (btn == true) {
                    $("#graphToTable").text("Graph to Table");
                    $("#graphToTable").removeAttr("disabled");
                }
            }).fail(function() {
                $("#queryDatabaseErrorModal").modal('show');

                if (btn == true) {
                    $("#graphToTable").text("Graph to Table");
                    $("#graphToTable").removeAttr("disabled");
                }
            });

        }

    });

    ////////////////////// On Page Load //////////////////////

    showSelect2Input();
    getListOfUsers();
});