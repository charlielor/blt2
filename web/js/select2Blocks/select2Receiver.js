$(document).ready(function() {
    /*
     Allows for remote data AJAX searches within the database. For Receiver.
     */
    $("#select2-Receiver").select2({
        theme: "bootstrap",
        minimumInputLength: 1,
        placeholder: "Search for a Receiver",
        width: "off",
        ajax: {
            url: 'receivers/like',
            delay: 250,
            data: function(params) {
                var query = {
                    term: params.term
                };

                return query;
            },
            processResults: function (data) {
                var results = [];

                if (data['object'] !== null) {
                    var receiver = data['object'];

                    $.each(receiver, function(index) {
                        if (receiver[index]['enabled'] === true) {
                            results.push({
                                id: receiver[index]['id'],
                                text: receiver[index]['name'] + " | " + receiver[index]['deliveryRoom']
                            })
                        }
                    });
                }

                return {
                    results: results
                };
            }
        }
    });
});