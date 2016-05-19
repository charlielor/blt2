$(document).ready(function() {
    var packageModal = $("#packageModal");

    var select2Shipper = $("#select2-Shipper");
    var select2Vendor = $("#select2-Vendor");
    var select2Receiver = $("#select2-Receiver");

    var numberOfPackages = $('#numberOfPackages');

    var uploadFiles = $("#uploadFiles");
    var deletedPackingSlips = [];

    var listOfExistingPackingSlips = $("#listOfExistingPackingSlips");

    var n = null;

    // Set the dropdownParent for all select2 on this page to the package modal
    $.fn.select2.defaults.set("dropdownParent", $("#packageModal"));

    packageModal.on("show.bs.modal", function() {
        // When the dialogForm opens, check to see there's an existing packageObject.
        // If so then show the shipper select2 div, create a new packageObject and fill the information
        if (window.packageObject.isNew == false) {
            // Display the shipper select2
            $(".existingPackage").show();

            // Hide the currently selected shipper
            $(".newPackage").hide();

            // Set the tracking number
            $("#packageTrackingNumber").text(window.packageObject.trackingNumber);

            // Fill in the select2 inputs
            $("#select2-Shipper").select2('data', {
                id: window.packageObject.shipper.id,
                text: window.packageObject.shipper.name
            });

            $("#select2-Vendor").select2('data', {
                id: window.packageObject.vendor.id,
                text: window.packageObject.vendor.name
            });

            $("#select2-Receiver").select2('data', {
                id: window.packageObject.receiver.id,
                text: window.packageObject.receiver.name  + ' | ' + window.packageObject.receiver.deliveryRoom
            });

            // Set the number of packageObjects
            numberOfPackages.val(window.packageObject.numberOfPackages);

            // If the packageObject has packing slips, set up preview links
            if (window.packageObject.packingSlips.length > 0) {

                window.packageObject.packingSlips.forEach(function (element, index, array) {

                    listOfExistingPackingSlips.append(
                        '<div id="' + element['id']+ '" class="form-control-static input-group col-md-3">' +
                        '<a class="btn btn-default btn-xs" data-id="'+ element['id'] + '" role="button" href="preview/' + element['downloadLink']  + '" target="_blank">Link</a>' +
                        '<div class="input-group-btn">' +
                        '<button type="button" data-id="' + element['id'] + '" class="btn btn-danger btn-xs deleteExistingPackingSlip">X</button>' +
                        '</div>' +
                        '</div>'
                    );
                });
            }

        } else { // Package being edited is a new package
            // Hide the shipper select2 input
            $(".existingPackage").hide();

            // Show the currently selected shipper
            $(".newPackage").show();

            // Get the tracking number
            var trackingNumber = $("#trackingNumberInput").val();

            var shipperSpan = $("#shipperSpan");

            // Create a new packageObject
            window.packageObject = new Package(trackingNumber);

            $("#packageShipper").text(shipperSpan.text());

            // Set the tracking number in form
            $("#packageTrackingNumber").text(window.packageObject.trackingNumber);
        }
    });

    packageModal.on("shown.bs.modal", function() {
        if (window.packageObject['isNew'] == true) {
            select2Vendor.focus();
        } else if (window.packageObject.isNew == false) {
            select2Shipper.focus();
        }
    });

    packageModal.on("hidden.bs.modal", function() {
        clearForm();
        $("#trackingNumberInput").val("");
        $("#trackingNumberInput").focus();
    });

    $("#submitNewPackage").on("click", function() {
        // Get the elements
        var shipperSpan = $("#shipperSpan");
        var shipperSelector = $("#select2-Shipper");
        var vendorSelector = $("#select2-Vendor");
        var receiverSelector = $("#select2-Receiver");

        var shipper, vendor, receiver;

        // Remove all errors
        //removeFormErrors();

        // Check to see if select2 shipper/vendor/receiver is filled
        if (window.packageObject.isNew == false) {
            var existingPackageObject = window.packageObject;

            // Create a new receivedPackage object and fill in information
            window.packageObject = new Package(existingPackageObject['trackingNumber']);
            window.packageObject.isNew = false;
            window.packageObject.shipper = existingPackageObject['shipper'];
            window.packageObject.receiver = existingPackageObject['receiver'];
            window.packageObject.vendor = existingPackageObject['vendor'];
            window.packageObject.numberOfPackages = existingPackageObject['numberOfPackages'];
            window.packageObject.deletedPackingSlips = deletedPackingSlips;

            if (shipperSelector.val() == null) {
                // If selectShipper is empty
                addError("shipper", "");
            } else {
                shipper = {
                    "id": shipperSelector.val(),
                    "name": shipperSelector.text()
                };
            }
        } else {
            shipper = {
                "id": parseInt(shipperSpan.attr('value')),
                "name": shipperSpan.text()
            };
        }

        window.packageObject.shipper = shipper;

        if ((vendorSelector.val() === null)) {
            addError("vendor", "");

        } else if (receiverSelector.val() === null) {
            addError("receiver", "");
        } else {
            vendor = {
                "id": vendorSelector.val(),
                "name": vendorSelector.text()
            };

            receiver = {
                "id": receiverSelector.val(),
                "name": receiverSelector.text().split("|")[0].trim(),
                "deliveryRoom": receiverSelector.text().split("|")[1].trim()
            };

            window.packageObject.vendor = vendor;
            window.packageObject.receiver = receiver;

        }

        // Set the number of packageObjects
        window.packageObject.numberOfPackages = parseInt($("#numberOfPackages").val());

        // If the form is valid
        if (window.packageObject.valdiatePackage()) {
            // Push all pictures to the packageObject object
            var picturesTaken = document.getElementsByClassName("thumbnail");

            for (var i = 0; i < picturesTaken.length; i++) {
                window.packageObject.packingSlips.push(picturesTaken[i].src);
            }

            // Get the selected uploaded files into it's own form
            var formData = new FormData(document.getElementById('uploadFiles'));

            // Change the packageObject object into JSOn
            var packageObjectJSON = JSON.stringify(window.packageObject);

            // Append the packageObject JSON to the form
            formData.append('packageObject', packageObjectJSON);

            // Check for packing slips and if there are none, ask if it's okay to submit packageObject with no packing slips
            var okayOnPackingSlips = true;

            if (window.packageObject.packingSlips.length == 0) {
                var emptyPackingSlips = false;
                var packingSlips = $("#attachedPackingSlips");
                var numberOfAttachedPackingSlips = packingSlips[0]['files'].length;

                if (numberOfAttachedPackingSlips < 1) {
                    emptyPackingSlips = true;
                }

                if (emptyPackingSlips) {
                    var confirmNoPackingSlip = confirm("There are no packing slips attached. Is that okay?");
                    if (!confirmNoPackingSlip) {
                        okayOnPackingSlips = false;
                    }
                }
            }

            // If okay no packing slips
            if (okayOnPackingSlips) {
                // Upload form VIA AJAX POST
                $.ajax({
                        url: 'submitPackageInformation',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false
                    })
                    .done(function (results) {
                        // If the result is an error, display the error and close the form as the form has already been submitted
                        if (results['result'] == 'error') {
                            n = noty({
                                layout: "bottom",
                                theme: "bootstrapTheme",
                                type: "error",
                                text: results['message'],
                                maxVisible: 2,
                                timeout: 2000,
                                killer: true,
                                buttons: false
                            });


                        } else {
                            if ((results['result'] == 'success') && (results['object'] !== null)) {
                                if (results['message'] == 'Successfully submitted package') {
                                    // Add a row to the current table with the last uploaded packageObject information
                                    $('#datatable-Receiving').DataTable().row.add(results['object']).draw();

                                    // Display a noty notification towards the bottom telling the user that the packageObject information was submitted successfully
                                    n = noty({
                                        layout: "bottom",
                                        theme: "bootstrapTheme",
                                        type: "success",
                                        text: "Package information sent successfully!",
                                        maxVisible: 2,
                                        timeout: 2000,
                                        killer: true,
                                        buttons: false
                                    });

                                    // Close the form
                                    packageModal.modal("hide");

                                } else if (results['message'] == "Successfully updated package") {
                                    // Display a noty notification towards the bottom telling the user that the packageObject information was submitted successfully
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

                                    // Depending on the returned object, either update the dataTable or ignore
                                    var trackingNumberUpdated = results["object"]["trackingNumber"];

                                    // Get the row index the row is on if any
                                    // NOTICE: The DataTable constructor used here is "Hungarian" casing to use
                                    // legacy plugins created for 1.9 and lower
                                    var row = $('#datatable-Receiving').dataTable().fnFindCellRowIndexes(trackingNumberUpdated, 0);

                                    if (row.length != 0) {
                                        $('#datatable-Receiving').DataTable().row(row).remove().row.add(results["object"]).draw();
                                    }

                                    // Close the form
                                    packageModal.modal("hide");
                                } else {
                                    // TODO

                                    console.log("Was successful at doing something...");
                                }
                            } else {
                                n = noty({
                                    layout: "bottom",
                                    theme: "bootstrapTheme",
                                    type: "error",
                                    text: "Error in loading packageObject data into table",
                                    maxVisible: 2,
                                    timeout: 2000,
                                    killer: true,
                                    buttons: false
                                });
                            }

                        }
                    })
                    .fail(function () {
                        // Display a noty telling the user that there was an issue submitting the packageObject information
                        n = noty({
                            layout: "bottom",
                            theme: "bootstrapTheme",
                            type: "error",
                            text: "Package information NOT sent successfully!",
                            maxVisible: 2,
                            timeout: 2000,
                            killer: true,
                            buttons: false
                        });
                    });
            }
        }
    });

    // When the user clicks on the "-" next to the number of packageObjects text input box, decrease the number in the text box by one
    $("#minusAPackage").click(function() {
        var numberOfPackages = document.getElementById("numberOfPackages");
        var numValue = parseInt(numberOfPackages.value, 10);
        if (numValue > 1) {
            numberOfPackages.value = numValue - 1;
        }
    });


    // When the user clicks on the "+" next to the number of packageObjects text input box, increase the number in the text box by one
    $("#addAPackage").click(function() {
        var numberOfPackages = document.getElementById("numberOfPackages");
        var numValue = parseInt(numberOfPackages.value, 10);
        numberOfPackages.value = numValue + 1;
    });

    /*
     * Remove the selected input type file. If it's the only one, then just remove the input element and add a new one.
     */
    $(document).on("click", "#clearAttachedPackingSlips", function() {
        $("#attachedPackingSlips").val("");
    });

    /*
     * Delete existing packing slip
     */
    $(document).on("click", ".deleteExistingPackingSlip", function(e) {
        var idOfPackingSlip = $(this).data('id');

        $("#" + idOfPackingSlip).remove();

        deletedPackingSlips.push(idOfPackingSlip);
    });

    /**
     * Clear the form
     */
    function clearForm() {
        // Remove any highlighted errors
        removeFormErrors();

        // Reset select2 hidden inputs
        select2Shipper.val(null).trigger("change");
        select2Vendor.val(null).trigger("change");
        select2Receiver.val(null).trigger("change");

        // Set the number of packageObjects to 1
        numberOfPackages.val(1);

        // Remove all existing packing slips from the previous packageObject, if any
        $("#listOfExistingPackingSlips").empty();

        // Clear imput type file
        $("#attachedPackingSlips").val("");

        // Remove all images
        $('.thumbnail').remove();
        $("#image").src = '';
        $("#thumbnailsDiv").empty();

        packageObject = null;
    }

    // If the up/down arrow keys are pressed, add or subtract the number of packageObjects
    $(document).keyup(function(e) {
        if (!($(".select2-input").is(":visible"))) {
            if (e.keyCode == 38) {
                $("#addAPackage").click();
            } else if (e.keyCode == 40) {
                $("#minusAPackage").click();
            }
        }
    });

    /**
     * Remove all form errors
     */
    function removeFormErrors() {
        $('#packageVendorDiv').removeClass('has-error');
        $('#packageReceiverDiv').removeClass('has-error');
        $('#packageShipperDiv').removeClass('has-error');
    }

    /**
     * When the user clicks on a thumbnail, ask the user to see if he/she wants to delete this image. If so, delete the
     * thumbnail and the hidden image.
     */
    $(document).on("click", ".thumbnail", function() {
        var confirmDelete = confirm("Are you sure you want to delete this image?");
        if (confirmDelete) {
            this.remove();
            var allThumbnails = document.getElementsByClassName("thumbnail").length;
            if (allThumbnails == 0) {
                $("#thumbnails").css("display", "none");
            }
        }

    });

    function addError(error, label) {
        if (error == "vendor") {
            $('#packageVendorDiv').addClass('has-error');
        } else if (error == "receiver") {
            $('#packageReceiverDiv').addClass('error');
        } else if (error == "shipper") {
            $('#packageShipperDiv').addClass('error');
        }

    }
});