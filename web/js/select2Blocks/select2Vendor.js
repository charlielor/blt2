$(document).ready(function() {
    /*
     Allows for remote data AJAX searches within the database. For Vendor.
     */
    $("#select2-Vendor").select2({
        theme: "bootstrap",
        minimumInputLength: 1,
        placeholder: "Search for a Vendor",
        width: "off",
        ajax: {
            url: 'vendors/like',
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
                    var vendors = data['object'];

                    $.each(vendors, function(index) {
                        if (vendors[index]['enabled'] === true) {
                            results.push({
                                id: vendors[index]['id'],
                                text: vendors[index]['name']
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