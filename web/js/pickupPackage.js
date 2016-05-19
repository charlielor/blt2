$(document).ready(function() {
    var pickupPackageModal = $("#pickupPackageModal");
    var pickupPackageDiv = $("#pickupPackageDiv");

    var pickupTrackingNumber = $("#pickupTrackingNumber");

    var pickupPackageLabel = $("#pickupPackageLabel");
    var submitPackageTrackingNumber = $("#submitPackageTrackingNumber");

    var pickupPackageResultsModal = $("#pickupPackageResultsModal");
    var userWhoPickedUpDiv = $("#userWhoPickedUpDiv");
    var userWhoAuthorizedDiv = $("#userWhoAuthorizedDiv");

    var userWhoPickedUp = $("#userWhoPickedUp");
    var userWhoAuthorized = $("#userWhoAuthorized");

    var submitPackagePickupInformation = $("#submitPackagePickupInformation");

    var dataTablePickUp = $('#datatable-PickupResults').DataTable({
        'searching': false,
        'ordering': false,
        'paging': false,
        'info': false,
        'dataSrc': '',
        'columns': [
            {
                'data': null,
                'render': function() {
                    return '<button type="button" class="btn btn-default btn-sm deleteRowInPickUpTable">Delete</button>';
                }
            },
            {'data': 'trackingNumber'},
            {'data': 'vendor.name'},
            {'data': 'shipper.name'},
            {'data': 'receiver.name'},
            {
                'data': 'dateReceived.timestamp',
                'render': function(data) {
                    var dateFromPackage = new Date(data * 1000);

                    var month = (dateFromPackage.getMonth() + 1);
                    month = month < 10 ? '0' + month : month;

                    var date = dateFromPackage.getDate();
                    date = date < 10 ? '0' + date : date;

                    return (month + '-' + date + '-' + dateFromPackage.getFullYear());
                }
            },
            {'data': 'numberOfPackages'}
        ]
    });

    // When the user clicks on the pick up button, open the dialog box with the barcode input textbox
    $("#pickup").click(function() {
        pickupPackageModal.modal('show');
    });

    pickupPackageModal.on("shown.bs.modal", function() {
        pickupTrackingNumber.focus();
    });

    submitPackageTrackingNumber.on("click", function() {
        // Clear any errors
        clearErrors("input");

        // Get the pickup tracking number text
        var ptn = pickupTrackingNumber.val();

        // If the barcode is not empty, submit the barcode to the server and get the package
        if ((ptn.replace(/\s/g,"")) == "") {
            // If the barcode is empty, then display error, clear and focus input box
            addError("input", "Tracking number can not be empty");
            pickupTrackingNumber.focus();
        } else {
            // Get the packages base on scanned barcode
            $.ajax({
                    type: "GET",
                    url: "package/search",
                    data: { 'term': ptn }
                })
                .done(function(results) {
                    // If the results come back with an error, display display error
                    if (results['result'] == 'error') {
                        addError("input", results['message']);
                        pickupTrackingNumber.focus();
                    }  else {
                        // Add the results to a datatable
                        for (var i = 0; i < results['object'].length; i++) {
                            dataTablePickUp.row.add(results['object'][i]).draw();
                        }

                        // If there are more than one package, enable the 'delete' button to allow the user to delete rows
                        enableDisableDeleteButtonsForPickUp();

                        // Close the dialog box
                        pickupPackageModal.modal('hide');

                        // Open the pickup results dialog box
                        pickupPackageResultsModal.modal('show');
                    }
                })
                .fail(function() {
                    addError("input", 'There was a connection error; please try again');
                });
        }
    });

    pickupPackageResultsModal.on("shown.bs.modal", function() {
        userWhoPickedUp.focus();
    });

    submitPackagePickupInformation.on("click", function() {
        if (dataTablePickUp.rows().data().length > 1) {
            addError("table", "Please find the package being picked up and delete the rest");
        } else {
            clearErrors("result");

            // Get the user who is picking up the package
            var uwpu = userWhoPickedUp.val();

            // Get the user who is authorizing the pick up
            var uwa = userWhoAuthorized.val();

            // Get the tracking number for that package
            var trackingNumberInPickUp = dataTablePickUp.row(0).data()['trackingNumber'];
            // Get the number of packages for the tracking number
            var numberOfPackages = dataTablePickUp.row(0).data()['numberOfPackages'];

            // If the user who is picking up the package's textbox is not empty or null, then update the server
            // that the package has been picked up.
            if (((uwpu.replace(/\s/g, "")) == "") || (uwa == null)) {
                addError("result", "Please enter in the first and last name of the person who is picking up package");
                userWhoPickedUp.focus();
            } else {
                clearErrors("result");

                // Set up the parameters
                var dataBeingSent = {
                    userWhoPickedUp: uwpu,
                    userWhoAuthorized: uwa,
                    pickedUp: 1
                };

                // Send the AJAX request
                $.ajax({
                        type: "PUT",
                        url: "package/" + trackingNumberInPickUp + "/update",
                        data: dataBeingSent
                    })
                    .done(function(results) {
                        var n = null;

                        // If the results come back successful, create a noty to let the user know that it has been successfully updated on the server
                        // Else let the user know that it was unsuccessful or that there was a connection failure
                        if (results['result'] == 'success') {
                            // Close the pickup results dialog
                            pickupPackageResultsModal.modal('hide');

                            n = noty({
                                layout: "bottom",
                                theme: "bootstrapTheme",
                                type: "success",
                                text: results['message'],
                                maxVisible: 2,
                                timeout: 2000,
                                killer: true,
                                buttons: false
                            });

                            // If there are more than one package for this tracking number, then alert the user
                            if (numberOfPackages > 1) {
                                alert("There are " + numberOfPackages + " packages for this tracking number");
                            }

                        } else {
                            addError("result", results["message"]);
                            userWhoPickedUp.focus();
                        }
                    })
                    .fail(function() {
                        addError("result", 'There was a connection error; please try again');
                    });
            }
        }
    });

    // For one of those rare cases where there's a long tracking number and a number of stored tracking numbers match
    // the long tracking number. The response from the server will be a table with an extra column with a delete button.
    // This will allow the user to see which package it is exactly and delete the others.
    $('#datatable-PickupResults tbody').on("click", ".deleteRowInPickUpTable", function() {
        dataTablePickUp.row($(this).parents('tr')).remove().draw();
        enableDisableDeleteButtonsForPickUp();
    });

    /**
     * Enable or disable delete buttons for pickup tables
     */
    function enableDisableDeleteButtonsForPickUp() {
        // Get all the rows with the class 'deleteRowInPickUpTable'
        var numberOfDeleteButtonsInPickUpTable = $('.deleteRowInPickUpTable');

        // If there are more than one rows, enable the 'delete' button to allow the row to be deleted from datatable
        // Else disable the button
        if (numberOfDeleteButtonsInPickUpTable.length > 1) {
            for (var i = 0; i < numberOfDeleteButtonsInPickUpTable.length; i++) {
                $(numberOfDeleteButtonsInPickUpTable[i]).removeAttr('disabled');
            }
        } else {
            $(numberOfDeleteButtonsInPickUpTable[0]).attr('disabled', 'disabled');
        }
    }

    function addError(error, label) {
        pickupPackageLabel.removeClass("label-primary");
        pickupPackageLabel.addClass("label-danger");
        pickupPackageLabel.text(label);

        if (error == "input") {
            pickupPackageDiv.addClass("has-error");
        } else if (error == "table") {
            $("#pickupResultsDiv").addClass("has-error");
        }

    }

    function clearErrors(error){
        // Clear all errors
        pickupPackageLabel.removeClass("label-danger");
        pickupPackageLabel.addClass("label-primary");
        pickupPackageLabel.text("Package must not have been delivered or picked up");

        pickupPackageDiv.removeClass("has-error");
        $("#pickupResultsDiv").removeClass("has-error");
    }
});

