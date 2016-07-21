$(document).ready(function() {
    var receiverModal = $("#receiverModal");

    var receiverNameDiv = $("#receiverNameDiv");
    var receiverNameText = $("#receiverName");

    var receiverRoomNumberDiv = $("#receiverRoomNumberDiv");
    var receiverRoomNumberText = $("#receiverRoomNumber");

    var receiverLabel = $("#receiverLabel");

    var submitReceiver = $("#submitReceiver");

    var referer = "";
    var select2 = false;
    var id = "";

    var existingReceiverName = "";
    var existingReceiverRoomNumber = "";

    receiverModal.on("show.bs.modal", function(e) {
        var button = $(e.relatedTarget);
        referer = button.data('referer');
        select2 = button.data('select2');
        id = button.data('receiver-id');

        if (referer === "new") {
            $("#receiverTitle").text("Add a new Receiver");
        } else if (referer === "edit") {
            $("#receiverTitle").text("Update Receiver");
            $("#submitReceiver").text("Update");
            existingReceiverName = button.data('receiver-name');
            existingReceiverRoomNumber = button.data('receiver-delivery-room');

            receiverNameText.val(existingReceiverName);
            receiverRoomNumberText.val(existingReceiverRoomNumber);
        }
    });

    receiverModal.on("shown.bs.modal", function() {
        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($("#packageModal").hasClass("in")) {
            receiverModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        }

        clearErrors();
        receiverNameText.focus();
    });

    receiverModal.on("hidden.bs.modal", function() {
        clearFields();
        clearErrors();
    });

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
            } else if (referer === 'edit') {
                if (id === null || id === undefined) {
                    displayError("Cannot retrieve ID of Receiver");
                } else {
                    var updateReceiver = {};

                    if (receiverName !== existingReceiverName) {
                        updateReceiver['name'] = receiverName;
                    }

                    if (receiverRoomNumber != existingReceiverRoomNumber) {
                        updateReceiver['deliveryRoom'] = receiverRoomNumber;
                    }

                    if (updateReceiver. != 0) {
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

    var addError = function(error, label) {
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

    };

    var clearErrors = function() {
        // Clear all errors
        receiverLabel.removeClass("label-danger");
        receiverLabel.text("New Receiver name must be unique");

        receiverNameDiv.removeClass("has-error");
        receiverRoomNumberDiv.removeClass("has-error");
    };

    var clearFields = function() {
        receiverNameText.val("");
        receiverRoomNumberText.val("");
    };
    
    var displaySuccess = function(message) {
        // Display a noty
        n = noty({
            layout: "top",
            theme: "bootstrapTheme",
            type: "success",
            text: message,
            maxVisible: 1,
            timeout: 2000,
            killer: true,
            buttons: false
        });
    };
    
    var displayError = function(error) {
        n = noty({
            layout: "top",
            theme: "bootstrapTheme",
            type: "error",
            text: error,
            maxVisible: 1,
            timeout: 2000,
            killer: true,
            buttons: false
        });
    };
});