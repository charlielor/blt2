$(document).ready(function() {
    // Get the modal for the Receiver
    var receiverModal = $("#receiverModal");

    // Get the div for Receiver's name div
    var receiverNameDiv = $("#receiverNameDiv");
    // Get the div for Receiver's name input text box
    var receiverNameText = $("#receiverName");

    // Get the div for Receiver's room number div
    var receiverRoomNumberDiv = $("#receiverRoomNumberDiv");
    // Get the div for Receiver's room number input text box
    var receiverRoomNumberText = $("#receiverRoomNumber");

    // Get the label for the Receiver modal (for errors)
    var receiverLabel = $("#receiverLabel");

    // Get the submit Receiver button
    var submitReceiver = $("#submitReceiver");

    // Set the referer (with data-referer)
    var referer = "";
    // Set the select2 variable (if true then put results in select2 box)
    var select2 = false;
    // Set the maintenance variable
    var maintenance = false;
    // Set the id (with data-id)
    var id = "";
    // Set up the button from which the modal was launched from
    var button = null;

    // Initialize the existingReceiverName and existingReceiverRoomNumber
    var existingReceiverName = "";
    var existingReceiverRoomNumber = "";

    // If the Receiver modal is to show, set the referer, select2, id and if the modal is being used to update an existing Receiver, set those too
    receiverModal.on("show.bs.modal", function(e) {
        // Get the button that launched the modal
        button = $(e.relatedTarget);

        // Set the referer/select2/id
        referer = button.data('referer');
        select2 = button.data('select2');
        maintenance = button.data('maintenance');
        id = button.data('receiver-id');

        if (referer === "new") {
            $("#receiverTitle").text("Add a new Receiver");
        } else if (referer === "edit") {
            $("#receiverTitle").text("Update Receiver");
            submitReceiver.text("Update");
            existingReceiverName = button.data('receiver-name');
            existingReceiverRoomNumber = button.data('receiver-delivery-room');

            receiverNameText.val(existingReceiverName);
            receiverRoomNumberText.val(existingReceiverRoomNumber);
        }
    });

    // If the Receiver modal is shown, move the modal "up" if there's already another modal up (by editing the z-index)
    receiverModal.on("shown.bs.modal", function() {
        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($("#packageModal").hasClass("in")) {
            receiverModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        }

        // Clear all errors
        clearErrors();
        // Focus on the name input textbox
        receiverNameText.focus();
    });

    // When the Receiver modal is hidden, clear error and fields
    receiverModal.on("hidden.bs.modal", function() {
        clearFields();
        clearErrors();
    });

    // When the submit Receiver button is clicked
    submitReceiver.on("click", function() {
        // Remove all errors from prior to checking for errors
        clearErrors();

        // Get the new receiver name
        var receiverName = receiverNameText.val();
        // Get the new receiver room number
        var receiverRoomNumber = receiverRoomNumberText.val();

        // If the new receiver name and/or new receiver room number are null or empty, display error
        if ((receiverName === null) || ((receiverName.replace(/\s/g, "")) == '')) {
            addError("name", "Enter a name for the new Receiver");
            receiverNameText.focus();
        } else if ((receiverRoomNumber == null) || ((receiverRoomNumber.replace(/\s/g, '')) == '')) {
            addError("roomNumber", "Enter a room number for the new Receiver");
            receiverRoomNumberText.focus();
        } else if (((receiverName.replace(/\s/g, "")) == '') && ((receiverRoomNumber.replace(/\s/g, '')) == '')) {
            addError("all", "Enter a name and a room number for the new Receiver");
            receiverNameText.focus();
        } else {
            // If the referer says that the Receiver is new, submit new Receiver
            if (referer === 'new') {
                // Submit the receiver information
                $.post("receivers/new",
                    {
                        name: receiverName,
                        deliveryRoom: receiverRoomNumber
                    }
                ).fail(function() {
                        // If connection error, display error
                        addError("none", 'There was a connection error; please try again');
                    }
                ).done(function(results) {
                    // If error, display error
                    if (results['result'] == 'error') {
                        if (results['message'].indexOf('Room number') == 0) {
                            addError("roomNumber", results['message']);
                            receiverRoomNumberText.focus();
                        } else {
                            addError("name", results['message']);
                            receiverNameText.focus();
                        }

                    } else if (results['result'] == 'success') {
                        displaySuccess(results['message']);

                        // Add the newly created vendor to the select2 input box
                        if (select2) {
                            var option = new Option(results['object'][0]['name'] + " | " + results['object'][0]['deliveryRoom'], results['object'][0]['id']);

                            $("#select2-Receiver").html(option).trigger("change");
                        }

                        // Close the modal
                        receiverModal.modal('hide');
                    } else {
                        displayError("Error with creating new Receiver");
                    }
                });
            } else if (referer === 'edit') { // If the referer says it is updating an existing Receiver, update it
                // If id is null or undefined, then display error
                if (id === null || id === undefined) {
                    displayError("Cannot retrieve ID of Receiver");
                } else {
                    // Create an object that will be sent to the server for updating the Receiver
                    var updateReceiver = {};

                    // If the name of the Receiver has changed, add it to the update object
                    if (receiverName !== existingReceiverName) {
                        updateReceiver['name'] = receiverName;
                    }

                    // If the room number for the Receiver has changed, add it to the update object
                    if (receiverRoomNumber != existingReceiverRoomNumber) {
                        updateReceiver['deliveryRoom'] = receiverRoomNumber;
                    }

                    // If the update object has at least one property (that means that the Recevier object has been updated), update the Receiver
                    if (Object.getOwnPropertyNames(updateReceiver).length != 0) {
                        $.ajax({
                            type: "PUT",
                            url: "receivers/" + id + "/update",
                            data: updateReceiver
                        })
                            .done(function(results) {
                                // If error, display error
                                if (results['result'] == 'error') {
                                    addError("name", results['message']);
                                    receiverNameText.focus();
                                } else if (results['result'] == 'success') {
                                    displaySuccess(results['message']);

                                    // Add the updated Receiver to the select2 input box
                                    if (select2) {
                                        var option = new Option(results['object'][0]['name'] + " | " + results['object'][0]['deliveryRoom'], results['object'][0]['id']);

                                        $("#select2-Receiver").html(option).trigger("change");
                                    }

                                    // Update table if from maintenance
                                    if (maintenance) {
                                        $(button.parent().parent().children()[1]).text(results['object'][0]['name']);
                                        $(button.parent().parent().children()[2]).text(results['object'][0]['deliveryRoom']);

                                        button.data('receiver-name', results['object'][0]['name']);
                                        button.data('receiver-delivery-room', results['object'][0]['deliveryRoom']);
                                    }

                                    // Close the modal
                                    receiverModal.modal('hide');
                                } else {
                                    displayError("Error with creating new Receiver");
                                }
                            })
                            .fail(function() {
                                // If connection error, display error
                                addError("none", 'There was a connection error; please try again');
                            });
                    } else {
                        // If connection error, display error
                        addError("none", 'There was nothing to be updated');
                    }

                }
            } else {
                displayError("Unable to determine referer");
            }
        }
    });

    // Add error text and CSS
    function addError(error, label) {
        receiverLabel.removeClass("label-primary");
        receiverLabel.addClass("label-danger");
        receiverLabel.text(label);

        if (error == "name") {
            receiverNameDiv.addClass("has-error");
        } else if (error == "roomNumber") {
            receiverRoomNumberDiv.addClass("has-error");
        } else {
            receiverNameDiv.addClass("has-error");
            receiverRoomNumberDiv.addClass("has-error");
        }

    }

    // Clear errors and CSS
    function clearErrors() {
        // Clear all errors
        receiverLabel.removeClass("label-danger");
        receiverLabel.text("New Receiver name must be unique");

        receiverNameDiv.removeClass("has-error");
        receiverRoomNumberDiv.removeClass("has-error");
    }

    // Clear the input text fields
    function clearFields() {
        receiverNameText.val("");
        receiverRoomNumberText.val("");
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
    
    // Show a fail noty
    function displayError(message) {
        var n = noty({
            layout: "top",
            theme: "bootstrapTheme",
            type: "error",
            text: message,
            maxVisible: 1,
            timeout: 2000,
            killer: true,
            buttons: false
        });
    }
});