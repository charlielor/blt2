$(document).ready(function() {
    var trackingNumberInput = $('#trackingNumberInput');

    window.packageObject = new Package();

    // DataTable for today's packages for the receiving page
    var dataTableReceiving = $('#datatable-Receiving').DataTable({
        'ajax': {
            'url': 'getPackagesForDate',
            'data': {
                'date': 'now'
            },
            'dataSrc': 'object'
        },
        'columns': [
            {'data': 'trackingNumber'},
            {'data': 'vendor.name'},
            {'data': 'shipper.name'},
            {'data': 'receiver.name'},
            {'data': 'numberOfPackages'},
            {'data': 'userWhoReceived'},
            {
                'data': 'packingSlips[]',
                'render': function(data) {
                    // Create links for all packing slips
                    var packingSlipLinks = 'None';
                    if (data.length != 0) {
                        packingSlipLinks = "";
                        $.each(data, function(index) {
                            packingSlipLinks += '<a href="download/' + data[index]['downloadLink'] + '">' + data[index]['extension'].toUpperCase() + '</a> ';
                        });
                    }

                    return packingSlipLinks;
                }
            },
            {
                'data': 'dateReceived.timestamp',
                'render': function(data) {
                    // Get the date and times it by 1000
                    var dateFromPackage = new Date(data * 1000);

                    // Get the month and add 1 to it (zero-base)
                    var month = (dateFromPackage.getMonth() + 1);
                    // If the month is less than 10 then append a 0 to the month
                    month = month < 10 ? '0' + month : month;

                    // Get the date
                    var date = dateFromPackage.getDate();
                    // If the date is less than 10 then append a 0 to the month
                    date = date < 10 ? '0' + date : date;

                    // Return date
                    return (month + '-' + date + '-' + dateFromPackage.getFullYear());
                }
            }
        ]
    });

    var selectShipperModal = $('#selectShipperModal');
    var shipperSpan = $('#shipperSpan');
    var shipperSpanInForm = $('#shipperSpanInForm');

    var emptyTrackingNumberModal = $("#emptyTrackingNumberModal");
    var packageAlreadyExistsModal = $("#packageAlreadyExistsModal");

    selectShipperModal.on("show.bs.modal", function() {
        // Show a spinner gif while the page is retrieving a list of enabled shippers
        $('#spinner').show();

        // Remove all previous shipper buttons
        $('.shipperRow').remove();

        // Do an AJAX call to the server to get a list of enabled shippers and append them to the dialog
        $.getJSON('getEnabledShippers', function(data) {
            var retrievedShippers = data['object'];
            var listOfShippers = [];

            $.each(retrievedShippers, function(index) {
                listOfShippers.push('<div class="row shipperRow"><div class="col-md-12"><button type="button" id="' + retrievedShippers[index]['id'] + '" class="btn btn-default btn-lg btn-block text-center shipperSelected">' + retrievedShippers[index]['name'] + '</button></div></div>');
            });

            listOfShippers.push('<div class="row shipperRow"><div class="col-md-12"><button type="button" id="addANewShipper" class="btn btn-default btn-lg btn-block text-center" data-toggle="modal" data-target="#addNewShipperModal" data-referer="selectAShipper" data-select2=false>Add New Shipper</button></div></div>');

            // Hide the spinner.gif
            $('#spinner').hide();

            // Append the list of shipper
            $("#shippers").append(listOfShippers);
        });
    });

    emptyTrackingNumberModal.on("hidden.bs.modal", function() {
        trackingNumberInput.val("");
        trackingNumberInput.focus();
    });

    selectShipperModal.modal({
        backdrop: "static",
        keyboard: false
    });

    selectShipperModal.on('click', ".shipperSelected", function() {
        var selectedShipper = $(this).text();

        shipperSpan.empty();
        shipperSpanInForm.empty();
        shipperSpan.append(selectedShipper);
        shipperSpanInForm.append(selectedShipper);

        shipperSpan.attr('value', $(this).attr('id'));

        selectShipperModal.modal('hide');

        trackingNumberInput.focus();
    });

    selectShipperModal.on('click', '#addANewShipper', function() {
        selectShipperModal.modal('hide');
    });

    // When the user clicks on the select shipper button, show shipper
    $('#shipper').click(function() {
        selectShipperModal.modal({
            backdrop: "static"
        });
    });

    //////////////////// Page Functions ////////////////////

    // When the user presses the enter key on the keyboard, proceed with "Enter in Details".
    $(document).keypress(function(e) {
        if (e.keyCode == 13) {
            if (!($(".modal").hasClass("in"))) {
                $("#enterInDetails").click();
            } else if ($("#packageModal").hasClass("in")) {
            }
        }
    });

    $('#clear').click(function() {
        clearAndFocusTrackingNumberField();
    });

    // The "Enter in Details" button on the page.
    $('#enterInDetails').click(function() {
        // Get the tracking number with no spaces
        var trackingNumber = trackingNumberInput.val().replace(/\s/g, "");

        // Check to see if it's empty
        if (trackingNumber.length == 0) {
            emptyTrackingNumberModal.modal('show');
        } else {
            $.get('getPackageGivenTrackingNumber', {trackingNumber: trackingNumber})
                .done(function(data) {
                    var results = JSON && JSON.parse(data) || $.parseJSON(data);

                    if (results['result'] == 'success') {
                        if (results['object'] != null) {
                            window.packageObject = results['object'];

                            window.packageObject['isNew'] = false;

                            $("#existingPackage").text(packageObject['trackingNumber'] + " already exists");
                            packageAlreadyExistsModal.modal("show");


                        } else {
                            window.packageObject = new Package(trackingNumber);

                            $("#packageModal").modal({
                                backdrop: "static"
                            });
                        }

                    } else { // TODO - Error in querying for package

                    }

                });
        }
    });

    /**
     * Clear the tracking number input field and focus it
     */
    function clearAndFocusTrackingNumberField() {
        // Focus on trackingNumberInput
        trackingNumberInput.val('');
        trackingNumberInput.focus();
    }

});