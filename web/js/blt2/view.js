$(document).ready(function() {
    // Get current date
    var currentDate = new Date();

    // Variable shortcuts for dialogs
    var datepickerModal = $("#datepickerModal");
    var selectDateButton = $('#selectDateButton');

    // Variable shortcuts for the 'Go to Today button'
    var goToTodayButton = $('#goToToday');

    // An AJAX DataTable for the page with columns defined
    var dataTable = $('#dataTable').DataTable({
        dom: "<'row'<'col-sm-6 hidden-xs'l><'col-sm-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5 hidden-xs'i><'col-sm-7 hidden-xs'p>>" +
        "<'row'<'col-sm-12 text-center'B>>",
        buttons: [
            'csv', 'pdf'
        ],
        autoWidth: false,
        responsive: true,
        ajax: {
            url: 'packages',
            data: {
                dateBegin: "now",
                dateEnd: "now"
            },
            dataSrc: 'object'
        },
        columns: [
            {data: 'trackingNumber'},
            {data: 'vendor.name'},
            {data: 'shipper.name'},
            {data: 'receiver.name'},
            {data: 'numberOfPackages'},
            {data: 'userWhoReceived'},
            {
                data: 'packingSlips',
                render: function(data) {
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
                data: 'dateReceived',
                render: function(data) {
                    // There is no check for data because for each package, there has to be a date received
                    var selectedDate = new Date(Date.parse(data));

                    var month = (selectedDate.getMonth() + 1);
                    month = month < 10 ? '0' + month : month;

                    var date = selectedDate.getDate();
                    date = date < 10 ? '0' + date : date;

                    return (month + '-' + date + '-' + selectedDate.getFullYear());
                }
            },
            {
                data: 'dateDelivered',
                render: function(data) {
                    // If data, display the date otherwise display 'Not Delivered'
                    if (data) {
                        // Create a new date object and format it for the column
                        var selectedDate = new Date(Date.parse(data));

                        var month = (selectedDate.getMonth() + 1);
                        month = month < 10 ? '0' + month : month;

                        var date = selectedDate.getDate();
                        date = date < 10 ? '0' + date : date;

                        return (month + '-' + date + '-' + selectedDate.getFullYear());
                    } else {
                        return 'Not Delivered';
                    }

                }
            },
            {
                data: 'datePickedUp',
                // If data, display the date otherwise display 'Not Picked Up'
                render: function(data) {
                    if (data) {
                        // Create a new date object and format it for the column
                        var selectedDate = new Date(Date.parse(data));

                        var month = (selectedDate.getMonth() + 1);
                        month = month < 10 ? '0' + month : month;

                        var date = selectedDate.getDate();
                        date = date < 10 ? '0' + date : date;

                        return (month + '-' + date + '-' + selectedDate.getFullYear());
                    } else {
                        return 'Not Picked Up'
                    }
                }
            }
        ]
    });

    datepickerModal.on("shown.bs.modal", function() {
        $('#datepicker').datepicker();
    });

    // Settings for the jQuery datepicker with a max date to prevent users from selecting future dates (no reason to)
    $('#datepicker').datepicker({
        defaultDate: new Date(),
        maxDate: new Date(),
        onSelect: function(selectedDate) {
            var dateSelected = new Date(selectedDate).toDateString();
            var date = {
                dateBegin: dateSelected,
                dateEnd: dateSelected
            };

            // When the user selects a date, send a request to the server to get data for that date
            dataTable.clear().draw();

            $.get('packages', date, function(response) {
                if (response['result'] == 'success') {
                    // For however long the array is from the JSON parse, add the data to the DataTable
                    for (var i = 0; i < response['object'].length; i++) {
                        dataTable.row.add(response['object'][i]).draw();
                    }
                }
            });

            // Close the modal enclosing the datepicker
            datepickerModal.modal('hide');

            selectDateButton.text(selectedDate);

            hideOrShowGoToTodayButton();
        }
    });

    // When the datePickerButton is clicked, show the datePicker dialog
    selectDateButton.click(function() {
        datepickerModal.modal('show');
    });


    // If the goToToday button is clicked, clear the current table and update it with new data
    // Will only appear if the date on the current page is different from the actual current date
    goToTodayButton.on("click", function() {
        var date = {
            dateBegin: "now",
            dateEnd: "now"
        };

        dataTable.clear().draw();

        $.get('/packages', date, function(response) {
            if (response['result'] == 'success') {
                for (var i = 0; i < response['object'].length; i++) {
                    dataTable.row.add(response['object'][i]).draw();
                }
            }
        });

        var month = (currentDate.getMonth() + 1);
        month = month < 10 ? '0' + month : month;

        var day = currentDate.getDate();
        day = day < 10 ? '0' + day : day;

        selectDateButton.text(month + '/' + day + '/' + currentDate.getFullYear());

        hideOrShowGoToTodayButton();
    });

    /**
     * If the selected date isn't the current date, display the 'Go to Today' button
     */
    function hideOrShowGoToTodayButton() {
        // Get the date from the date picker button
        var dateOnDatePickerButton = new Date(selectDateButton.text());
        console.log(selectDateButton.text());
        console.log(currentDate);

        // If it's not the current date, show the 'Go to Today' button, else hide the button
        if (!((currentDate.getFullYear() === dateOnDatePickerButton.getFullYear()) &&
            (currentDate.getMonth() === dateOnDatePickerButton.getMonth()) &&
            (currentDate.getDate() === dateOnDatePickerButton.getDate()))) {
            goToTodayButton.css('display', 'inline-block');
        } else {
            goToTodayButton.css('display', 'none');
        }
    }

    /*
     Refreshes the datatable every 30 seconds if the date on the datatable is the current date
     */
    setInterval(function() {
        var dateOnDatePickerButton = new Date(selectDateButton.text());

        if (((currentDate.getFullYear() === dateOnDatePickerButton.getFullYear()) &&
            (currentDate.getMonth() === dateOnDatePickerButton.getMonth()) &&
            (currentDate.getDate() === dateOnDatePickerButton.getDate()))) {
            dataTable.ajax.reload();
        }

    }, 30000);

});