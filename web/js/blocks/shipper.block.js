$(document).ready(function() {
    // Get the modal for the Shipper
    var shipperModal = $("#shipperModal");

    // Get the div for Shipper's name div
    var shipperNameDiv = $("#shipperNameDiv");
    // Get the div for Shipper's name input text box
    var shipperNameText = $("#shipperName");

    // Get the label for the Shipper modal (for errors)
    var shipperLabel = $("#shipperLabel");

    // Get the submit Shipper button
    var submitShipper = $("#submitShipper");

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

    // Initialize the existingShipperName
    var existingShipperName = "";

    // If the Shipper modal is to show, set the referer, select2, id and if the modal is used to update an existing Shipper, set those too
    shipperModal.on("show.bs.modal", function(e) {
        // Get the button that launched the modal
        button = $(e.relatedTarget);

        // Set the referer/select2/id
        referer = button.data('referer');
        select2 = button.data('select2');
        maintenance = button.data('maintenance');
        id = button.data('shipper-id');

        if (referer === "new") {
            $("#shipperTitle").text("Add a new Shipper");
        } else if (referer === "edit") {
            $("#shipperTitle").text("Update Shipper");
            submitShipper.text("Update");
            existingShipperName = button.data('shipper-name');

            shipperNameText.val(existingShipperName);
        }
    });

    // If the Shipper modal is shown, move the modal "up" if there's alrady anothe rmodal up (by editing the z-index)
    shipperModal.on("shown.bs.modal", function() {
        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($("#packageModal").hasClass("in")) {
            shipperModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        }

        // Clear all errors
        clearErrors();
        // Focus on the name input textbox
        shipperNameText.focus();
    });

    // When the Shipper modal is hidden, clear error and field
    shipperModal.on("hidden.bs.modal", function() {
        clearFields();
        clearErrors();
    });

    // When the submit Shipper button is clicked
    submitShipper.on("click", function() {
        // Clear all errors
        clearErrors();

        // Get the shipper name
        var shipperName = shipperNameText.val();

        // If the shipper name is null or empty spaces or empty, display error
        if ((shipperName === null) || ((shipperName.replace(/\s/g, "")) == "")) {
            addError("Enter in name for the new Shipper");
            shipperNameText.focus();
        } else {
            if (referer === "new") {
                // Submit new shipper
                $.post("shippers/new",
                    {
                        name: shipperName
                    }
                ).fail(function() {
                        // If connection error, display error
                        addError('There was an connection error; please try again');
                        shipperNameText.focus();
                    }
                ).done(function(results) {
                    // If error, append error
                    if (results['result'] == 'error') {
                        addError(results['message']);
                        shipperNameText.focus();
                    } else if (results['result'] == 'success') {
                        displaySuccess(results['message']);

                        // Add the newly created shipper to the select2 input box
                        if (select2) {
                            var option = new Option(results['object'][0]['name'], results['object'][0]['id']);

                            $("#select2-Shipper").html(option).trigger("change");
                        }

                        // Close the modal
                        shipperModal.modal("hide");
                    } else {
                        addError("Error with creating new Shipper");
                    }
                });
            } else if (referer === "edit") { // If the referer says it is updating an existing Shipper, update it
                    // Create an object that will be sent to the server for updating the Shipper
                    var updateShipper = {};

                    // If the shipper name has changed, add it to the update object
                    if (shipperName !== existingShipperName) {
                        updateShipper['name'] = shipperName;
                    }

                    // If the update object has at least one property, update it
                    if (Object.getOwnPropertyNames(updateShipper).length != 0) {
                        $.ajax({
                            type: "PUT",
                            url: "shippers/" + id + "/update",
                            data: updateShipper
                        })
                            .done(function(results) {
                                // If error, display error
                                if (results['result'] == 'error') {
                                    addError(results['message']);
                                    shipperNameText.focus();
                                } else if (results['result'] == 'success') {
                                    displaySuccess(results['message']);

                                    // Add the updated Shipper to the select2 input box
                                    if (select2) {
                                        var option = new Option(results['object'][0]['name'], results['object'][0]['id']);

                                        $("#select2-Shipper").html(option).trigger("change");
                                    }

                                    // Update table if from maintenance
                                    if (maintenance) {
                                        $(button.parent().parent().children()[1]).text(results['object'][0]['name']);
                                        button.data('shipper-name', results['object'][0]['name']);
                                    }

                                    // Close the modal
                                    shipperModal.modal('hide');
                                } else {
                                    addError("Error with creating new Shipper");
                                    shipperNameText.focus();
                                }
                            })
                            .fail(function() {
                                // If connection error, display error
                                addError('There was a connection error; please try again');
                                shipperNameText.focus();
                            });
                    } else {
                        // If connection error, display error
                        addError('There was nothing to be updated');
                        shipperNameText.focus();
                    }
            } else {
                addError("Unable to determine referer");
                shipperNameText.focus();
            }
        }
    });

    // Add error text and CSS
    function addError(label) {
        shipperLabel.removeClass("label-primary");
        shipperLabel.addClass("label-danger");
        shipperLabel.text(label);

        shipperNameDiv.addClass("has-error");
    }

    // Clear errors and CSS
    function clearErrors() {
        // Clear all errors
        shipperLabel.removeClass("label-danger");
        shipperLabel.addClass("label-primary");
        shipperLabel.text("Shipper name must be unique");

        shipperNameDiv.removeClass("has-error");
    }

    // Clear the input text fields
    function clearFields() {
        shipperNameText.val("");
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
