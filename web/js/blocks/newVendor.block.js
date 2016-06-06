$(document).ready(function() {
    var addNewVendorModal = $("#addNewVendorModal");

    var newVendorNameDiv = $("#newVendorNameDiv");
    var newVendorName = $("#newVendorName");

    var newVendorLabel = $("#newVendorLabel");

    var submitNewVendor = $("#submitNewVendor");

    var referer = "";
    var select2 = false;

    addNewVendorModal.on("show.bs.modal", function(e) {
        var button = $(e.relatedTarget);
        referer = button.data('referer');
        select2 = button.data('select2');
    });

    addNewVendorModal.on("shown.bs.modal", function() {
        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($("#packageModal").hasClass("in")) {
            addNewVendorModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        }

        clearErrors();
        newVendorName.focus();
    });

    addNewVendorModal.on("hidden.bs.modal", function() {
        clearFields();
        clearErrors();
    });

    submitNewVendor.on("click", function() {
        // Clear all errors
        clearErrors();

        // Get the vendor name
        var vendorName = newVendorName.val();

        // If the vendor name is null or empty spaces or empty, display error
        if ((vendorName == null) || ((vendorName.replace(/\s/g, "")) == "")) {
            addError("Enter in name for the new Vendor");
            newVendorName.focus();
        } else {
            // Submit new vendor
            $.post("vendors/new",
                {
                    name: vendorName
                }
            ).fail(function() {
                    // If connection error, display error
                    addError('There was an connection error; please try again');
                    newVendorName.focus();
                }
            ).done(function(results) {
                // If error, append error
                if (results['result'] == 'error') {
                    addError(results['message']);
                    newVendorName.focus();
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

                        $("#select2-Vendor").html(option).trigger("change");
                    }

                    // Close the modal
                    addNewVendorModal.modal("hide");
                } else {
                    alert("Error with creating new Vendor");
                }
            });
        }
    });

    function addError(label) {
        newVendorLabel.removeClass("label-primary");
        newVendorLabel.addClass("label-danger");
        newVendorLabel.text(label);

        newVendorNameDiv.addClass("has-error");
    }

    function clearErrors() {
        // Clear all errors
        newVendorLabel.removeClass("label-danger");
        newVendorLabel.addClass("label-primary");
        newVendorLabel.text("New Vendor Name must be less than 50 characters");

        newVendorNameDiv.removeClass("has-error");
    }

    function clearFields() {
        newVendorName.val("");
    }
});
