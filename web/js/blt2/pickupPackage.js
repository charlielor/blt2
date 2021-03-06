$(document).ready(function() {
    var pickupPackageModal = $("#pickupPackageModal");
    var pickupPackageDiv = $("#pickupPackageDiv");

    var pickupTrackingNumber = $("#pickupTrackingNumber");

    var pickupPackageLabel = $("#pickupPackageLabel");
    var submitPackageTrackingNumber = $("#submitPackageTrackingNumber");

    var pickupPackageResultsModal = $("#pickupPackageResultsModal");
    var pickupPackageResultsLabel = $("#pickupPackageResultsLabel");

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
            {'data': 'trackingNumber'},
            {'data': 'vendor.name'},
            {'data': 'shipper.name'},
            {'data': 'receiver.name'},
            {
                'data': 'dateReceived',
                'render': function(data) {
                    var dateFromPackage = new Date(Date.parse(data));

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
        pickupPackageModal.modal({
            backdrop: "static"
        });
    });

    pickupPackageModal.on("shown.bs.modal", function() {
        pickupTrackingNumber.focus();
    });

    pickupPackageModal.on("hide.bs.modal", function() {
        clearErrors();
        pickupTrackingNumber.val("");
    });

    pickupPackageResultsModal.on("hide.bs.modal", function() {
        clearErrors();
        dataTablePickUp.clear().draw();
        userWhoPickedUp.val("");
    });

    submitPackageTrackingNumber.on("click", function() {
        // Get the pickup tracking number text
        var ptn = pickupTrackingNumber.val();

        // If the barcode is not empty, submit the barcode to the server and get the package
        if ((ptn.replace(/\s/g,"")).length === 0) {
            // If the barcode is empty, then display error, clear and focus input box
            addError("input", "Tracking number can not be empty");
            pickupTrackingNumber.focus();
        } else {
            // Get the packages base on scanned barcode
            $.ajax({
                    type: "GET",
                    url: "packages/search",
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

                        // Close the dialog box
                        pickupPackageModal.modal('hide');

                        // Open the pickup results dialog box
                        pickupPackageResultsModal.modal({
                            backdrop: "static"
                        });
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
            // Get the user who is picking up the package
            var uwpu = userWhoPickedUp.val();

            // Get the tracking number for that package
            var trackingNumberInPickUp = dataTablePickUp.row(0).data()['trackingNumber'];
            // Get the number of packages for the tracking number
            var numberOfPackages = dataTablePickUp.row(0).data()['numberOfPackages'];

            // If the user who is picking up the package's textbox is not empty or null, then update the server
            // that the package has been picked up.
            if ((uwpu.replace(/\s/g, "")).length === 0) {
                addError("table", "Please enter in the first and last name of the person who is picking up package");
                userWhoPickedUp.focus();
            } else {
                // Set up the parameters
                var dataBeingSent = {
                    userWhoPickedUp: uwpu
                };

                // Send the AJAX request
                $.ajax({
                        type: "PUT",
                        url: "packages/" + trackingNumberInPickUp + "/pickup",
                        data: dataBeingSent
                    })
                    .done(function(results) {
                        var n = null;

                        // If the results come back successful, create a noty to let the user know that it has been successfully updated on the server
                        // Else let the user know that it was unsuccessful or that there was a connection failure
                        if (results['result'] == 'success') {
                            clearErrors();

                            // Close the pickup results dialog
                            pickupPackageResultsModal.modal('hide');

                            var pickedUpPackage = results['object'];

                            // If there are more than one package, alert the user that there are more more than one package
                            if (pickedUpPackage.numberOfPackages > 1) {
                                $("#moreThanOnePackages").text('There are ' + pickedUpPackage.numberOfPackages + ' packages for ' + pickedUpPackage.trackingNumber);

                                $("#moreThanOnePackagesModal").modal("show");

                                $("#moreThanOnePackagesModal").on("hide.bs.modal", function() {
                                    displaySuccess(results['message']);
                                });

                            } else {
                                displaySuccess(results['message']);
                            }

                        } else {
                            addError("table", results["message"]);
                            userWhoPickedUp.focus();
                        }
                    })
                    .fail(function() {
                        addError("table", 'There was a connection error; please try again');
                    });
            }
        }
    });

    function addError(error, label) {
        if (error == "input") {
            pickupPackageLabel.removeClass("label-primary");
            pickupPackageLabel.addClass("label-danger");
            pickupPackageLabel.text(label);
            
            pickupPackageDiv.addClass("has-error");
        } else if (error == "table") {
            pickupPackageResultsLabel.removeClass("label-primary");
            pickupPackageResultsLabel.addClass("label-danger");
            pickupPackageResultsLabel.text(label);
            
            $("#userWhoPickedUpDiv").addClass("has-error");
        }

    }

    function displaySuccess(message) {
        n = noty({
            layout: "top",
            theme: "bootstrapTheme",
            type: "success",
            text: message,
            maxVisible: 2,
            timeout: 2000,
            killer: true,
            buttons: false
        });
    }

    function clearErrors() {
        // Clear all errors
        pickupPackageLabel.removeClass("label-danger");
        pickupPackageLabel.addClass("label-primary");
        pickupPackageLabel.text("Package must not have been delivered or picked up");

        pickupPackageResultsLabel.removeClass("label-danger");
        pickupPackageResultsLabel.addClass("label-primary");
        pickupPackageResultsLabel.text("Type the first and last name of the person who is picking up package");

        pickupPackageDiv.removeClass("has-error");
        $("#userWhoPickedUpDiv").removeClass("has-error");
    }
});

