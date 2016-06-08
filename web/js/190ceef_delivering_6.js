$(document).ready(function() {
    // Variables
    var moreThanOnePackageModal = $("#moreThanOnePackageModal");

    // Get the barcode text box
    var barcodeTextBox = $("#barcodeTextBox");

    // Get the receiver span
    var receiverSpan = $('#receiverSpan');

    // Receiver variable
    var receiver = null;

    // Array of packages
    var undeliveredPackages = [];

    // Get the ASCII value for ENTER
    var ENTER = 13;

    // noty
    var n = null;

    // When the moreThanOnePackageModal closes, focus on textbox input
    $("#moreThanOnePackageModal").on("hidden.bs.modal", function() {
        clearAndFocus();
    });

    $("#moreThanOnePackagesModal").on("hidden.bs.modal", function() {
        clearAndFocus();
    });

    // Today's date
    var dateFromToday = new Date();

    // Global variable to allow different settings of DataTables for mobile and desktop
    // DataTable for the main table on delivering
    var dataTableDelivering = $('#datatable-Delivering').DataTable({
        paging: false,
        searching: false,
        autoWidth: false,
        responsive: true,
        columns: [
            {data: 'trackingNumber'},
            {data: 'vendor.name'},
            {data: 'receiver.name'},
            {data: 'numberOfPackages'},
            {
                data: 'dateReceived',
                'render': function(data) {
                    var dateFromPackage = new Date(Date.parse(data));

                    var month = (dateFromPackage.getMonth() + 1);
                    month = month < 10 ? '0' + month : month;

                    var date = dateFromPackage.getDate();
                    date = date < 10 ? '0' + date : date;

                    return (dateFromPackage.getFullYear() + '/' + month + '/' + date);

                }
            }
        ],
        columnDefs: [
            { "visible": false, "targets": 4}
        ],
        drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;

            api.column(4, {page:'current'} ).data().each( function ( group, i ) {
                var dateFromPackage = new Date(Date.parse(group));
                var day = null;

                if ((dateFromPackage.getFullYear() === dateFromToday.getFullYear()) && (dateFromPackage.getMonth() === dateFromToday.getMonth()) && (dateFromPackage.getDate() === dateFromToday.getDate())) {
                    day = 'Today';
                } else {
                    var monthFromPackage = (dateFromPackage.getMonth() + 1) < 10 ? '0' + (dateFromPackage.getMonth() + 1) : (dateFromPackage.getMonth() + 1);
                    var dayFromPackage = dateFromPackage.getDate() < 10 ? '0' + dateFromPackage.getDate() : dateFromPackage.getDate();
                    day = dateFromPackage.getFullYear() + '/' + monthFromPackage + '/' + dayFromPackage;
                }

                if (last !== day) {
                    $(rows).eq( i ).before(
                        '<tr class="group" style="background-color: #d3d3d3; font-weight: bold;"><td colspan="4">'+ day +'</td></tr>'
                    );
                }

                last = day;
            } );
        }
    });

    // Global variable to allow different settings of DataTables for mobile and desktop
    // DataTable for when there are more than one package for the given tracking number
    var dataTableMoreThanOnePackage = $('#datatable-MoreThanOnePackage').DataTable({
        autoWidth: false,
        responsive: true,
        columns: [
            {
                data: null,
                'render': function() {
                    return '<button type="button" class="btn btn-default btn-sm submitPackage">Submit</button>';
                }
            },
            {data: 'trackingNumber'},
            {data: 'vendor.name'},
            {data: 'numberOfPackages'},
            {
                data: 'dateReceived',
                'render': function(data) {
                    var dateFromPackage = new Date(Date.parse(data));

                    var month = (dateFromPackage.getMonth() + 1);
                    month = month < 10 ? '0' + month : month;

                    var date = dateFromPackage.getDate();
                    date = date < 10 ? '0' + date : date;

                    return (month + '-' + date + '-' + dateFromPackage.getFullYear());
                }
            }
        ]
    });

    //////////////////// Page Functions ////////////////////

    moreThanOnePackageModal.on("hide.bs.modal", function() {
        window.dataTableMoreThanOnePackage.clear().draw();
    });

    /*
     When the user selects a tracking number that pretains to the package he/she is delivering and submit the package information.
     */
    $('#datatable-MoreThanOnePackage tbody').on("click", ".submitPackage", function() {
        // Get the receivedPackage object
        var selectedPackage = dataTableMoreThanOnePackage.row($(this).parents('tr')).data();

        submitPackageInformation(selectedPackage);

        moreThanOnePackageModal.modal("hide");

        clearAndFocus();

    });

    // When a key is pressed on the delivering page
    $(document).keypress(function(e) {
        // If the key pressed was ENTER
        if (e.keyCode == ENTER) {
            validateBarcode();
        }

    });

    // Focus on the barcode textbox when the page loads
    barcodeTextBox.focus();

    //////////////////// Helper Functions ////////////////////

    /**
     * When the user scans in a barcode, interpret what the barcode means.

     * If it's not a part of the undeliveredPackages array, then it's either a receiver or invalid. Submit the barcode to see if it's a receiver.
     * If so, then return all of the undelivered packages for that receiver. If not, display error.

     * If it's part of the undeliveredPackages array, then it's a tracking number for the receiver. Submit the receiver and the barcode
     * to have it updated.
     */
    function interpretBarcode() {
        var barcode = barcodeTextBox.val();

        var indexOfPackage = $.inArray(barcode, undeliveredPackages);

        // If similarTrackingNumbers array is 0, then the barcode could be a receiver instead
        // Try to get the receiver's undelievered packages
        if (indexOfPackage == -1) {

            $.ajax({
                type: "GET",
                url: "receivers/packages",
                data: {
                    name: barcode
                }
            })
                . done(function(results) {
                    // If the results come back with an error, display a noty with the error
                    if (results['result'] == 'error') {
                        // Display a noty letting the user know what the error is
                        displayError(results['message']);

                    } else if (results["object"].length != 0) {
                        clearPageData();
                        receiver = barcode;

                        if (results["object"][0]['receiver']['deliveryRoom']) {
                            receiverSpan.text(receiver + ' | ' + results["object"][0]['receiver']['deliveryRoom']);
                        } else {
                            receiverSpan.text(receiver);
                        }

                        for (var i = 0; i < results["object"].length; i++) {
                            undeliveredPackages.push(results["object"][i].trackingNumber);
                            dataTableDelivering.row.add(results["object"][i]).draw();
                        }
                    } else {
                        displaySuccess("No packages for " + barcode);
                    }

                    clearAndFocus();
                })
                . fail(function() {
                    displayError("Connection error! Please try again");
                });


        } else if (indexOfPackage != -1) {
            submitPackageInformation(undeliveredPackages[indexOfPackage]);
        } else if (undeliveredPackages.length >= 1) {
            for (var j = 0; j < undeliveredPackages.length; j++) {
                dataTableMoreThanOnePackage.row.add(undeliveredPackages[j]).draw();
            }

            moreThanOnePackageModal.modal("show");
        }

    }


    /**
     * When the user scans in a barcode, validate it to make sure that it's not empty or just spaces
     */
    function validateBarcode() {
        // Remove errors
        barcodeTextBox.removeClass('error');

        // Get the value of the barcode textbox
        var barcodeTextBoxValue = barcodeTextBox.val();

        // If the barcode is not empty or does not contain just spaces
        if ((barcodeTextBoxValue == null) || (barcodeTextBoxValue.replace(/\s/g, "") == "")) {
            displayError("Please scan in a valid barcode");
            barcodeTextBox.addClass('error');
        } else {
            interpretBarcode();
        }
    }

    /**
     * Submit the package information the server. If it's successful, remove it from the table and display
     * a successful noty.
     *
     * @param packageInformation - Package information from the undelivered package array
     */
    function submitPackageInformation(trackingNumber) {

        if (trackingNumber)
            $.ajax({
                type: "PUT",
                url: "packages/" + trackingNumber + "/deliver",
                data: {
                    barcode: trackingNumber,
                    receiver: receiver
                }
            })
                .done(function(results) {
                    var n = null;

                    // If the results is an error, display a noty letting the user know that there was an error
                    // Else remove the submitted package information from there current array and display a noty to let the user know that the package has been successfully updated in database
                    if (results['result'] === 'error') {
                        displayError(results['message']);

                        clearAndFocus();
                    } else if (results['result'] === 'success') {
                        var deliveredPackage = results['object'];

                        // Get the number of packages for the package
                        var numberOfPackages = deliveredPackage.numberOfPackages;
                        // If there are more than one package, alert the user that there are more more than one package
                        if (numberOfPackages > 1) {
                            $("#moreThanOnePackages").text('There are ' + numberOfPackages + ' packages for ' + deliveredPackage.trackingNumber);

                            $("#moreThanOnePackagesModal").modal("show");

                            $("#moreThanOnePackagesModal").on("hide.bs.modal", function() {
                                displaySuccess("Successfully marked as delivered");
                            });

                        } else {
                            displaySuccess("Successfully marked as delivered!");
                        }

                        // Remove from array and from table
                        for (var i = 0; i < undeliveredPackages.length; i++) {
                            if (trackingNumber === undeliveredPackages[i]) {
                                undeliveredPackages.splice(i, 1);
                                dataTableDelivering.row(i).remove().draw();
                                break;
                            }
                        }

                        // Remove receiver text if the undelivered packages array is empty
                        if (undeliveredPackages.length === 0) {
                            receiverSpan.text('');
                            undeliveredPackages = [];
                            dataTableDelivering.clear().draw();
                            dataTableMoreThanOnePackage.clear().draw();
                        }

                        deliveredPackage = null;
                    } else {
                        displayError('Error in submitting package information');
                    }
                })
                .fail(function() {
                    displayError("Connection error! Please try again");
                });

        clearAndFocus();
    }

    /**
     * Clear the textbox and focus on it
     */
    function clearAndFocus() {
        barcodeTextBox.val("");
        barcodeTextBox.focus();
    }

    /**
     * Clear the page of current data
     */
    function clearPageData() {
        receiver = null;
        undeliveredPackages = [];
        receiverSpan.text('');
        barcodeTextBox.removeClass('error');
        dataTableDelivering.clear().draw();
    }

    // Order by the grouping
    $('#datatable-Delivering tbody').on( 'click', 'tr.group', function () {
        var currentOrder = dataTableDelivering.order()[0];
        if ( currentOrder[0] === 4 && currentOrder[1] === 'asc' ) {
            dataTableDelivering.order( [ 4, 'desc' ] ).draw();
        }
        else {
            dataTableDelivering.order( [ 4, 'asc' ] ).draw();
        }
    } );

    function displaySuccess(message) {
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
    }

    function displayError(error) {
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
    }
});