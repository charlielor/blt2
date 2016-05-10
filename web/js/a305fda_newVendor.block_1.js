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
            $.post("addNewVendor",
                {
                    name: vendorName
                }
            ).fail(function() {
                    // If connection error, display error
                    addError('There was an connection error; please try again');
                    newVendorName.focus();
                }
            ).done(function(data) {
                // Parse through JSON data and return array
                var results = JSON && JSON.parse(data) || $.parseJSON(data);

                // If error, append error
                if (results['result'] == 'error') {
                    addError(results['message']);
                    newVendorName.focus();
                } else if (results['result'] == 'success') {
                    // Display a noty notification towards the bottom telling the user that the new vendor information was submitted successfully
                    n = noty({
                        layout: "bottom",
                        theme: "bootstrap",
                        type: "success",
                        text: "New Vendor successfully created!",
                        maxVisible: 2,
                        timeout: 2000,
                        killer: true,
                        buttons: false
                    });

                    // Add the newly created vendor to the select2 input box
                    if (select2) {
                        var putInSelect2 = {
                            'id': results['object']['id'],
                            'text': results['object']['name'] + " | " + results['object']['deliveryRoom']
                        };

                        $("#select2-Vendor").select2('data', putInSelect2);
                    }

                    // Close the modal
                    addNewVendorModal.modal("hide");
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
