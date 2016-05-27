$(document).ready(function() {
    /*
     Allows for remote data AJAX searches within the database. For Shipper.
     */
    $("#select2-Shipper").select2({
        theme: "bootstrap",
        minimumInputLength: 1,
        placeholder: "Search for a Shipper",
        width: "off",
        ajax: {
            url: 'shipper/like',
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
                    var shipper = data['object'];

                    $.each(shipper, function(index) {
                        results.push({
                            id: shipper[index]['id'],
                            text: shipper[index]['name']
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