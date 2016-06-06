$(document).ready(function() {
    $("#spinnerPackage").hide();
    $("#spinnerVendor").hide();

    var error = "";

    $("#emptyStringModal").on("hidden.bs.modal", function() {
        if (error == "package") {
            $("#searchPackageText").val('');
            $("#searchPackageText").focus();
        } else if (error == "vendor") {
            $("#searchVendorText").val('');
            $("#searchVendorText").focus();
        }
    });

    $("#searchPackageButton").on("click", function() {
        if ($("#searchPackageText").val().replace(/\s/g, "").length == 0) {
            error = "package";
            $("#emptyStringModal").modal('show');
        } else {
            $("#searchPackageTable").hide();
            $("#spinnerPackage").show();
            var packageTableBody = $("#searchPackageTableBody");
            packageTableBody.empty();

            $.ajax({
                    type: "GET",
                    url: "packages/like",
                    data: {
                        term: $("#searchPackageText").val()
                    }
                })
                .done(function(results) {

                    if (results['result'] == 'success') {
                        $("#searchPackageText").val('');

                        if (results['object'].length === 0) {
                            $("#emptySetModal").modal('show');
                        } else {
                            $.each(results['object'], function(i) {
                                var pack = results['object'][i];
                                var downloadLinks = "";

                                if (pack['packingSlips'].length !== 0) {
                                    $.each(pack['packingSlips'], function(j) {
                                        downloadLinks += '<a href="preview/' + pack['packingSlips'][j].downloadLink + '" target="_blank">' + pack['packingSlips'][j].extension.toUpperCase() + '</a> ';
                                    });
                                } else {
                                    downloadLinks = "None";
                                }

                                var dateReceived = new Date(pack.dateReceived.timestamp * 1000);

                                var month = (dateReceived.getMonth() + 1);
                                month = month < 10 ? '0' + month : month;

                                var date = dateReceived.getDate();
                                date = date < 10 ? '0' + date : date;

                                dateReceived =  (month + '-' + date + '-' + dateReceived.getFullYear());

                                if (pack.delivered == true) {
                                    var dateDelivered = new Date(pack.dateDelivered.timestamp * 1000);

                                    var month = (dateDelivered.getMonth() + 1);
                                    month = month < 10 ? '0' + month : month;

                                    var date = dateDelivered.getDate();
                                    date = date < 10 ? '0' + date : date;

                                    dateDelivered =  (month + '-' + date + '-' + dateDelivered.getFullYear());

                                    packageTableBody.append(
                                        '<tr>' +
                                        '<td>' + pack.trackingNumber + '</td>' +
                                        '<td>' + pack.vendor.name + '</td>' +
                                        '<td>' + pack.shipper.name + '</td>' +
                                        '<td>' + pack.receiver.name + '</td>' +
                                        '<td>' + pack.numberOfPackages + '</td>' +
                                        '<td>' + pack.userWhoReceived + '</td>' +
                                        '<td>' + downloadLinks + '</td>' +
                                        '<td>' + dateReceived + '</td>' +
                                        '<td>' + dateDelivered + '</td>' +
                                        '<td>' + pack.userWhoDelivered + '</td>' +
                                        "<td colspan='2' class='text-center'>Package delivered</td>" +
                                        '</tr>'
                                    );
                                } else if (pack.pickedUp == true) {
                                    var datePickedUp = new Date(pack.datePickedUp.timestamp * 1000);

                                    var month = (datePickedUp.getMonth() + 1);
                                    month = month < 10 ? '0' + month : month;

                                    var date = datePickedUp.getDate();
                                    date = date < 10 ? '0' + date : date;

                                    datePickedUp =  (month + '-' + date + '-' + datePickedUp.getFullYear());

                                    packageTableBody.append(
                                        '<tr>' +
                                        '<td>' + pack.trackingNumber + '</td>' +
                                        '<td>' + pack.vendor.name + '</td>' +
                                        '<td>' + pack.shipper.name + '</td>' +
                                        '<td>' + pack.receiver.name + '</td>' +
                                        '<td>' + pack.numberOfPackages + '</td>' +
                                        '<td>' + pack.userWhoReceived + '</td>' +
                                        '<td>' + downloadLinks + '</td>' +
                                        '<td>' + dateReceived + '</td>' +
                                        "<td colspan='2' class='text-center'>Package picked up</td>" +
                                        '<td>' + datePickedUp + '</td>' +
                                        '<td>' + pack.userWhoPickedUp + '</td>' +
                                        '</tr>'
                                    );

                                } else {
                                    packageTableBody.append(
                                        "<tr class='danger'>" +
                                        '<td>' + pack.trackingNumber + '</td>' +
                                        '<td>' + pack.vendor.name + '</td>' +
                                        '<td>' + pack.shipper.name + '</td>' +
                                        '<td>' + pack.receiver.name + '</td>' +
                                        '<td>' + pack.numberOfPackages + '</td>' +
                                        '<td>' + pack.userWhoReceived + '</td>' +
                                        '<td>' + downloadLinks + '</td>' +
                                        '<td>' + dateReceived + '</td>' +
                                        "<td colspan='2' class='text-center'>Package NOT delivered</td>" +
                                        "<td colspan='2' class='text-center'>Package NOT picked up</td>" +
                                        '</tr>'
                                    );
                                }


                                $("#searchPackageTable").show();
                            });

                        }

                        $("#spinnerPackage").hide();
                    } else {
                        $("#spinnerPackage").hide();
                        $("#emptySetModal").modal('show');
                    }

                })
                .fail(function() {
                    $("#spinnerPackage").hide();
                    $("#queryDatabaseErrorModal").modal('show');
                });
        }

    });

    $("#searchVendorButton").on("click", function() {
        if ($("#searchVendorText").val().replace(/\s/g, "").length == 0) {
            error = "vendor";
            $("#emptyStringModal").modal('show');
        } else {
            $("#searchVendorTable").hide();
            $("#spinnerVendor").show();
            var vendorTableBody = $("#searchVendorTableBody");
            vendorTableBody.empty();

            $.ajax({
                    type: "GET",
                    url: "vendors/like",
                    data: {
                        term: $("#searchVendorText").val()
                    }
                })
                .done(function(results) {
                    if (results['result'] == 'success') {
                        $("#searchVendorText").val('');

                        if (results['object'].length === 0) {
                            $("#emptySetModal").modal('show');
                        } else {
                            $.each(results['object'], function(i) {
                                var vendor = results['object'][i];

                                if (vendor.enabled == 1) {
                                    vendorTableBody.append(
                                        '<tr>' +
                                        '<td>' + vendor.id + '</td>' +
                                        '<td>' + vendor.name + '</td>' +
                                        '<td><button type="button" id="'+ vendor.id +'" class="vendor btn btn-sm btn-danger">Disable</button></td>' +
                                        '</tr>'
                                    )
                                } else {
                                    vendorTableBody.append(
                                        '<tr>' +
                                        '<td>' + vendor.id + '</td>' +
                                        '<td>' + vendor.name + '</td>' +
                                        '<td><button type="button" id="'+ vendor.id +'" class="vendor btn btn-sm btn-success">Enable</button></td>' +
                                        '</tr>'
                                    )
                                }


                            });

                            $("#searchVendorTable").show();
                        }

                        $("#spinnerVendor").hide();

                    } else {
                        $("#spinnerVendor").hide();
                        $("#queryDatabaseErrorModal").modal('show');
                    }
                })
                .fail(function() {
                    $("#spinnerVendor").hide();
                    $("#queryDatabaseErrorModal").modal('show');
                });
        }
    });

    // When the user click a button within the receiver table, enable or disable the receiver
    $('.receiver').click(function() {
        // Get the clicked button's id
        var id = this.getAttribute('id');
        // Get the clicked button's value
        var action = $(this).text();
        // Get the clicked button
        var button = this;
        // Make the AJAX call to the server to switch the on or off the receiver
        $.ajax({
                type: "PUT",
                url: "receivers/" + id + "/" + action.toLowerCase()
            })
            .done(function(results) {
                // If the results are successful, change the color and text within the button to reflect the change
                if (results['result'] == 'success') {
                    if (action == 'Enable') {
                        $(button).text("Disable");
                        $(button).removeClass("btn-success");
                        $(button).addClass("btn-danger");
                    } else {
                        $(button).text("Enable");
                        $(button).removeClass("btn-danger");
                        $(button).addClass("btn-success");
                    }
                }
            })
            .fail(function() {
                // Display an alert saying that there was an issue with the AJAX call
                alert('There was an connection error; please try again');
            });
    });

    // When the user click a button within the shipper table, enable or disable the shipper
    $('.shipper').click(function() {
        // Get the clicked button's id
        var id = this.getAttribute('id');
        // Get the clicked button's value
        var action = $(this).text();
        // Get the clicked button
        var button = this;
        // Make the AJAX call to the server to switch the on or off the shipper
        $.ajax({
                type: "PUT",
                url: "shippers/" + id + "/" + action.toLowerCase()
            })
            .done(function(results) {
                // If the results are successful, change the color and text within the button to reflect the change
                if (results['result'] == 'success') {
                    if (action == 'Enable') {
                        $(button).text("Disable");
                        $(button).removeClass("btn-success");
                        $(button).addClass("btn-danger");
                    } else {
                        $(button).text("Enable");
                        $(button).removeClass("btn-danger");
                        $(button).addClass("btn-success");
                    }
                }
            })
            .fail(function() {
                // Display an alert saying that there was an issue with the AJAX call
                alert('There was an connection error; please try again');
            });
    });

    // When the user clicks a button within the vendor table, enable or disable the vendor
    $(document).on("click", ".vendor", function() {
        // Get the clicked button's id
        var id = this.getAttribute('id');
        // Get the clicked button's value
        var action = $(this).text();
        // Get the clicked button
        var button = this;
        // Make the AJAX call to the server to switch the on or off the vendor
        $.ajax({
                type: "PUT",
                url: "vendors/" + id + "/" + action.toLowerCase()
            })
            .done(function(results) {
                // If the results are successful, change the color and text within the button to reflect the change
                if (results['result'] == 'success') {
                    if (action == 'Enable') {
                        $(button).text("Disable");
                        $(button).removeClass("btn-success");
                        $(button).addClass("btn-danger");
                    } else {
                        $(button).text("Enable");
                        $(button).removeClass("btn-danger");
                        $(button).addClass("btn-success");
                    }
                }
            })
            .fail(function() {
                // Display an alert saying that there was an issue with the AJAX call
                alert('There was an connection error; please try again');
            });
    });
});