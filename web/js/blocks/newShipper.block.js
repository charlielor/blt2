$(document).ready(function() {
    var addNewShipperModal = $("#addNewShipperModal");

    var newShipperNameDiv = $("#newShipperNameDiv");
    var newShipperName = $('#newShipperName');

    var newShipperLabel = $("#newShipperLabel");

    var submitNewShipper = $("#submitNewShipper");

    var referer = "";
    var select2 = false;

    addNewShipperModal.on("show.bs.modal", function(e) {
        var button = $(e.relatedTarget);
        referer = button.data('referer');
        select2 = button.data('select2');
    });

    addNewShipperModal.on("shown.bs.modal", function() {
        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($("#packageModal").hasClass("in")) {
            addNewShipperModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        }

        clearErrors();
        newShipperName.focus();
    });

    addNewShipperModal.on("hidden.bs.modal", function() {
        clearFields();
        clearErrors();

        if (referer == "selectAShipper") {
            $("#selectShipperModal").modal("show");
        }
    });

    submitNewShipper.on("click", function() {
        // Clear all errors
        clearErrors();

        // Get the newShipperName value
        var newShipper = newShipperName.val();

        // If newShipperName value is empty, null or more than 15 characters, display error
        if (newShipper === null || newShipper.replace(/\s/g, "") == "") {
            addError("New Shipper name can not be empty");
            newShipperName.focus();
        } else {
            // Submit new shipper information
            $.post("shippers/new",
                {
                    name: newShipper
                }
            ).fail(function() {
                n = noty({
                    layout: "top",
                    theme: "bootstrapTheme",
                    type: "error",
                    text: "Connection error; please try again",
                    maxVisible: 2,
                    timeout: 2000,
                    killer: true,
                    buttons: false
                });
            }).done(function(results) {
                // If there's an error, display the error
                if (results['result'] == 'error') {
                    addError(results['message']);
                } else if (results['result'] == 'success') {
                    // Display a noty notification towards the bottom telling the user that the new vendor information was submitted successfully
                    n = noty({
                        layout: "top",
                        theme: "bootstrapTheme",
                        type: "success",
                        text: results['message'],
                        maxVisible: 2,
                        timeout: 2000,
                        killer: true,
                        buttons: false
                    });

                    // Add the newly created vendor to the select2 input box
                    if (select2) {
                        var option = new Option(results['object'][0]['name'], results['object'][0]['id']);

                        $("#select2-Shipper").html(option).trigger("change");
                    }

                    // Close the modal
                    addNewShipperModal.modal('hide');

                } else {
                    alert("Error with creating new Shipper");
                }
            });
        }
    });

    function addError(label) {
        newShipperLabel.removeClass("label-primary");
        newShipperLabel.addClass("label-danger");
        newShipperLabel.text(label);

        newShipperNameDiv.addClass("has-error");
    }

    function clearErrors() {
        // Clear all errors
        newShipperLabel.removeClass("label-danger");
        newShipperLabel.addClass("label-primary");
        newShipperLabel.text("New Shipper name must be unique");

        newShipperNameDiv.removeClass("has-error");
    }

    function clearFields() {
        newShipperName.val("");
    }

});