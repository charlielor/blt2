$(document).ready(function() {
    /*
     Allows for remote data AJAX searches within the database. For Receiver.
     */
    $("#select2-Receiver").select2({
        theme: "bootstrap",
        minimumInputLength: 1,
        placeholder: "Search for a Receiver",
        width: "auto",
        ajax: {
            url: 'receiver/like',
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
                        results.push({
                            id: receiver[index]['id'],
                            text: receiver[index]['name']
                        })
                    });
                }

                return {
                    results: results
                };
            }
        }
    });
});