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

                                var dateReceived = new Date(Date.parse(pack.dateReceived));

                                var month = (dateReceived.getMonth() + 1);
                                month = month < 10 ? '0' + month : month;

                                var date = dateReceived.getDate();
                                date = date < 10 ? '0' + date : date;

                                dateReceived =  (month + '-' + date + '-' + dateReceived.getFullYear());

                                if (pack.delivered == true) {
                                    var dateDelivered = new Date(Date.parse(pack.dateDelivered));

                                    var month = (dateDelivered.getMonth() + 1);
                                    month = month < 10 ? '0' + month : month;

                                    var date = dateDelivered.getDate();
                                    date = date < 10 ? '0' + date : date;

                                    dateDelivered =  (month + '-' + date + '-' + dateDelivered.getFullYear());

                                    packageTableBody.append(
                                        '<tr>' +
                                        '<td>' + pack.trackingNumber + '</td>' +
                                        '<td>' + pack.vendor.name + '</td>' +
                                        '<td>' + pack.vendor.name + '</td>' +
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
                                    var datePickedUp = new Date(Date.parse(pack.datePickedUp));

                                    var month = (datePickedUp.getMonth() + 1);
                                    month = month < 10 ? '0' + month : month;

                                    var date = datePickedUp.getDate();
                                    date = date < 10 ? '0' + date : date;

                                    datePickedUp =  (month + '-' + date + '-' + datePickedUp.getFullYear());

                                    packageTableBody.append(
                                        '<tr>' +
                                        '<td>' + pack.trackingNumber + '</td>' +
                                        '<td>' + pack.vendor.name + '</td>' +
                                        '<td>' + pack.vendor.name + '</td>' +
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
                                        '<td>' + pack.vendor.name + '</td>' +
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
                                        '<td><button type="button" data-id="'+ vendor.id +'" data-action="disable" class="vendor btn btn-sm btn-danger">Disable</button></td>' +
                                        '<td><button type="button" id="editVendor" data-vendor-id="' + vendor.id + '" data-toggle="modal" data-target="#vendorModal" data-referer="edit" data-select2=false data-maintenance=true data-vendor-name="' + vendor.name  + '" class="btn btn-sm btn-primary">Edit</button></td>' +
                                        '</tr>'
                                    )
                                } else {
                                    vendorTableBody.append(
                                        '<tr>' +
                                        '<td>' + vendor.id + '</td>' +
                                        '<td>' + vendor.name + '</td>' +
                                        '<td><button type="button" data-id="'+ vendor.id +'" data-action="enable" class="vendor btn btn-sm btn-success">Enable</button></td>' +
                                        '<td><button type="button" id="editVendor" data-vendor-id="' + vendor.id + '" data-toggle="modal" data-target="#vendorModal" data-referer="edit" data-select2=false data-maintenance=true data-vendor-name="' + vendor.name  + '" class="btn btn-sm btn-primary">Edit</button></td>' +
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
        // Get the button that launched the modal
        var receiver = $(this);

        // Get the clicked button's id
        var id = receiver.data('id');
        // Get the clicked button's value
        var action = receiver.data('action');

        if (id !== "" && id !== undefined && id !== null && action.replace(/\s/g) !== "" && action !== undefined && action !== null) {
            // Make the AJAX call to the server to switch the on or off the receiver
            $.ajax({
                type: "PUT",
                url: "receivers/" + id + "/" + action.toLowerCase()
            })
                .done(function(results) {
                    // If the results are successful, change the color and text within the button to reflect the change
                    if (results['result'] == 'success') {
                        if (action ==='enable') {
                            receiver.text("Disable");
                            receiver.removeClass("btn-success");
                            receiver.addClass("btn-danger");
                            receiver.data('action', 'disable');
                        } else {
                            receiver.text("Enable");
                            receiver.removeClass("btn-danger");
                            receiver.addClass("btn-success");
                            receiver.data('action', 'enable')
                        }
                    }
                })
                .fail(function() {
                    // Display an alert saying that there was an issue with the AJAX call
                    $("#errorModalText").text("There was an connection error; please try again");
                    $("#errorModal").modal('show');
                });
        } else {
            // Display an alert saying that there was an issue getting data from button
            $("#errorModalText").text("Unable to determine Receiver's ID and/or action");
            $("#errorModal").modal('show');
        }

    });

    // When the user click a button within the shipper table, enable or disable the shipper
    $('.shipper').click(function() {
        // Get the button that launched the modal
        var shipper = $(this);

        // Get the clicked button's id
        var id = shipper.data('id');
        // Get the clicked button's value
        var action = shipper.data('action');

        if (id !== "" && id !== undefined && id !== null && action.replace(/\s/g) !== "" && action !== undefined && action !== null) {
            // Make the AJAX call to the server to switch the on or off the shipper
            $.ajax({
                type: "PUT",
                url: "shippers/" + id + "/" + action.toLowerCase()
            })
                .done(function(results) {
                    // If the results are successful, change the color and text within the button to reflect the change
                    if (results['result'] == 'success') {
                        if (action ==='enable') {
                            shipper.text("Disable");
                            shipper.removeClass("btn-success");
                            shipper.addClass("btn-danger");
                            shipper.data('action', 'disable');
                        } else {
                            shipper.text("Enable");
                            shipper.removeClass("btn-danger");
                            shipper.addClass("btn-success");
                            shipper.data('action', 'enable')
                        }
                    }
                })
                .fail(function() {
                    // Display an alert saying that there was an issue with the AJAX call
                    $("#errorModalText").text("There was an connection error; please try again");
                    $("#errorModal").modal('show');
                });
        } else {
            // Display an alert saying that there was an issue getting data from button
            $("#errorModalText").text("Unable to determine Shipper's ID and/or action");
            $("#errorModal").modal('show');
        }

    });

    // When the user clicks a button within the vendor table, enable or disable the vendor
    $(document).on("click", ".vendor", function() {
        // Get the button that launched the modal
        var vendor = $(this);

        // Get the clicked button's id
        var id = vendor.data('id');
        // Get the clicked button's value
        var action = vendor.data('action');

        if (id !== "" && id !== undefined && id !== null && action.replace(/\s/g) !== "" && action !== undefined && action !== null) {
            // Make the AJAX call to the server to switch the on or off the vendor
            $.ajax({
                type: "PUT",
                url: "vendors/" + id + "/" + action.toLowerCase()
            })
                .done(function(results) {
                    // If the results are successful, change the color and text within the button to reflect the change
                    if (results['result'] == 'success') {
                        if (action ==='enable') {
                            vendor.text("Disable");
                            vendor.removeClass("btn-success");
                            vendor.addClass("btn-danger");
                            vendor.data('action', 'disable');
                        } else {
                            vendor.text("Enable");
                            vendor.removeClass("btn-danger");
                            vendor.addClass("btn-success");
                            vendor.data('action', 'enable')
                        }
                    }
                })
                .fail(function() {
                    // Display an alert saying that there was an issue with the AJAX call
                    $("#errorModalText").text("There was an connection error; please try again");
                    $("#errorModal").modal('show');
                });
        } else {
            // Display an alert saying that there was an issue getting data from button
            $("#errorModalText").text("Unable to determine Vendor's ID and/or action");
            $("#errorModal").modal('show');
        }
    });
});