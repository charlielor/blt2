$(document).ready(function() {
    // Get the modal for the Vendor
    var vendorModal = $("#vendorModal");

    // Get the div for Vendor's name div
    var vendorNameDiv = $("#vendorNameDiv");
    // Get the div for Vendor's name input text box
    var vendorNameText = $("#vendorName");

    // Get the label for the Vendor modal (for errors)
    var vendorLabel = $("#vendorLabel");

    // Get the submit Vendor button
    var submitVendor = $("#submitVendor");

    // Set the referer (with data-referer)
    var referer = "";
    // Set the select2 variable (if true then put the results in select2 box)
    var select2 = false;
    // Set the maintenance variable
    var maintenance = false;
    // Set the id (with data=id)
    var id = "";
    // Set up the button from which the modal was launched from
    var button = null;

    // Initialize the existingVendorName
    var existingVendorName = "";

    // If the Vendor modal is to show, set the referer, select2, id and if the modal is used to update an existing Vendor, set those too
    vendorModal.on("show.bs.modal", function(e) {
        // Get the button that launched the modal
        button = $(e.relatedTarget);

        // Set the referer/select2/id
        referer = button.data('referer');
        select2 = button.data('select2');
        maintenance = button.data('maintenance');
        id = button.data('vendor-id');

        if (referer === "new") {
            $("#vendorTitle").text("Add a new Vendor");
        } else if (referer === "edit") {
            $("#vendorTitle").text("Update Vendor");
            submitVendor.text("Update");
            existingVendorName = button.data('vendor-name');

            vendorNameText.val(existingVendorName);
        }
    });

    // If the Vendor modal is shown, move the modal "up" if there's already another modal up (by editing the z-index)
    vendorModal.on("shown.bs.modal", function() {
        // If referer or ID is not filled with data from button, close modal and display error
        if (referer === "") {
            vendorModal.modal("hide");
            $("#errorModalText").text("Error in retrieving the referer");
            $("#errorModal").modal("show");
        } else if (id === "") {
            vendorModal.modal("hide");
            $("#errorModalText").text("Error in retrieving the ID");
            $("#errorModal").modal("show");
        } else {
            // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
            if ($("#packageModal").hasClass("in")) {
                vendorModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
            }

            // Clear all errors
            clearErrors();
            // Focus on the name input textbox
            vendorNameText.focus();
        }
    });

    // When the Vendor modal is hidden, clear error and field
    vendorModal.on("hidden.bs.modal", function() {
        clearFields();
        clearErrors();
    });

    // When the submit Vendor button is clicked
    submitVendor.on("click", function() {
        // Clear all errors
        clearErrors();

        // Get the vendor name
        var vendorName = vendorNameText.val();

        // If the vendor name is null or empty spaces or empty, display error
        if ((vendorName === null) || ((vendorName.replace(/\s/g, "")) == "")) {
            addError("Enter in name for the new Vendor");
            vendorNameText.focus();
        } else {
            if (referer === "new") {
                // Submit new vendor
                $.post("vendors/new",
                    {
                        name: vendorName
                    }
                ).fail(function() {
                        // If connection error, display error
                        addError('There was an connection error; please try again');
                        vendorNameText.focus();
                    }
                ).done(function(results) {
                    // If error, append error
                    if (results['result'] == 'error') {
                        addError(results['message']);
                        vendorNameText.focus();
                    } else if (results['result'] == 'success') {
                        displaySuccess(results['message']);

                        // Add the newly created vendor to the select2 input box
                        if (select2) {
                            var option = new Option(results['object'][0]['name'], results['object'][0]['id']);

                            $("#select2-Vendor").html(option).trigger("change");
                        }

                        // Close the modal
                        vendorModal.modal("hide");
                    } else {
                        addError("Error with creating new Vendor");
                    }
                });
            } else if (referer === "edit") { // If the referer says it is updating an existing Vendor, update it
                    // Create an object that will be sent to the server for updating the Vendor
                    var updateVendor = {};

                    // If the vendor name has changed, add it to the update object
                    if (vendorName !== existingVendorName) {
                        updateVendor['name'] = vendorName;
                    }

                    // If the update object has at least one property, update it
                    if (Object.getOwnPropertyNames(updateVendor).length != 0) {
                        $.ajax({
                            type: "PUT",
                            url: "vendors/" + id + "/update",
                            data: updateVendor
                        })
                            .done(function(results) {
                                // If error, display error
                                if (results['result'] == 'error') {
                                    addError(results['message']);
                                    vendorNameText.focus();
                                } else if (results['result'] == 'success') {
                                    displaySuccess(results['message']);

                                    // Add the updated Vendor to the select2 input box
                                    if (select2) {
                                        var option = new Option(results['object'][0]['name'], results['object'][0]['id']);
                                        $("#select2-Vendor").html(option).trigger("change");
                                    }

                                    // Update table if from maintenance
                                    if (maintenance) {
                                        $(button.parent().parent().children()[1]).text(results['object'][0]['name']);
                                        button.data('vendor-name', results['object'][0]['name']);
                                    }

                                    // Close the modal
                                    vendorModal.modal('hide');
                                } else {
                                    addError("Error with creating new Vendor");
                                }
                            })
                            .fail(function() {
                                // If connection error, display error
                                addError('There was a connection error; please try again');
                                vendorNameText.focus();
                            });
                    } else {
                        // If connection error, display error
                        addError('There was nothing to be updated');
                        vendorNameText.focus();
                    }
            } else {
                addError("Unable to determine referer");
                vendorNameText.focus();
            }
        }
    });

    // Add error text and CSS
    function addError(label) {
        vendorLabel.removeClass("label-primary");
        vendorLabel.addClass("label-danger");
        vendorLabel.text(label);

        vendorNameDiv.addClass("has-error");
    }

    // Clear errors and CSS
    function clearErrors() {
        // Clear all errors
        vendorLabel.removeClass("label-danger");
        vendorLabel.addClass("label-primary");
        vendorLabel.text("Vendor name must be unique");

        vendorNameDiv.removeClass("has-error");
    }

    // Clear the input text fields
    function clearFields() {
        vendorNameText.val("");
    }

    // Show a successful noty
    function displaySuccess(message) {
        // Display a noty
        var n = noty({
            layout: "top",
            theme: "bootstrapTheme",
            type: "success",
            text: message,
            maxVisible: 1,
            timeout: 2000,
            killer: true,
            buttons: false
        });
    }
});
