$(document).ready(function() {
    var addNewReceiverModal = $("#addNewReceiverModal");

    var newReceiverNameDiv = $("#newReceiverNameDiv");
    var newReceiverName = $("#newReceiverName");

    var newReceiverRoomNumberDiv = $("#newReceiverRoomNumberDiv");
    var newReceiverRoomNumber = $("#newReceiverRoomNumber");

    var newReceiverLabel = $("#newReceiverLabel");

    var submitNewReceiver = $("#submitNewReceiver");

    var referer = "";
    var select2 = false;

    addNewReceiverModal.on("show.bs.modal", function(e) {
        var button = $(e.relatedTarget);
        referer = button.data('referer');
        select2 = button.data('select2');
    });

    addNewReceiverModal.on("shown.bs.modal", function() {
        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($("#packageModal").hasClass("in")) {
            addNewReceiverModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        }

        clearErrors();
        newReceiverName.focus();
    });

    addNewReceiverModal.on("hidden.bs.modal", function() {
        clearFields();
        clearErrors();
    });

    submitNewReceiver.on("click", function() {
        // Remove all errors from prior to checking for errors
        clearErrors();

        // Get the new receiver name
        var receiverName = newReceiverName.val();
        // Get the new receiver room number
        var receiverRoomNumber = newReceiverRoomNumber.val();

        // If the new receiver name and/or new receiver room number are null or empty, display error
        if ((receiverName == null) || ((receiverName.replace(/\s/g, "")) == '')) {
            addError("name", "Enter a name for the new Receiver");
            newReceiverName.focus();
        } else if ((receiverRoomNumber == null) || ((receiverRoomNumber.replace(/\s/g, '')) == '')) {
            addError("roomNumber", "Enter a room number for the new Receiver");
            newReceiverRoomNumber.focus();
        } else if (((receiverName.replace(/\s/g, "")) == '') && ((receiverRoomNumber.replace(/\s/g, '')) == '')) {
            addError("all", "Enter a name and a room number for the new Receiver");
            newReceiverName.focus();
        } else {
            // Submit the newReceiver information
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
                        newReceiverRoomNumber.focus();
                    } else {
                        addError("name", results['message']);
                        newReceiverName.focus();
                    }

                } else if (results['result'] == 'success') {
                    // Display a noty notification towards the bottom telling the user that the new receiver information was submitted successfully
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
                        var option = new Option(results['object'][0]['name'] + " | " + results['object'][0]['deliveryRoom'], results['object'][0]['id']);

                        $("#select2-Receiver").html(option).trigger("change");
                    }

                    // Close the modal
                    addNewReceiverModal.modal('hide');
                } else {
                    alert("Error with creating new Receiver");
                }
            });
        }
    });

    function addError(error, label) {
        newReceiverLabel.removeClass("label-primary");
        newReceiverLabel.addClass("label-danger");
        newReceiverLabel.text(label);

        if (error == "name") {
            newReceiverNameDiv.addClass("has-error");
        } else if (error == "roomNumber") {
            newReceiverRoomNumberDiv.addClass("has-error");
        } else {
            newReceiverNameDiv.addClass("has-error");
            newReceiverRoomNumberDiv.addClass("has-error");
        }

    }

    function clearErrors(){
        // Clear all errors
        newReceiverLabel.removeClass("label-danger");
        newReceiverLabel.addClass("label-primary");
        newReceiverLabel.text("New Receiver name must be less than 50 characters and room number less than 6");

        newReceiverNameDiv.removeClass("has-error");
        newReceiverRoomNumberDiv.removeClass("has-error");
    }

    function clearFields() {
        newReceiverName.val("");
        newReceiverRoomNumber.val("");
    }
});