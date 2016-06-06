$(document).ready(function() {
    $("#errorModal").on("shown.bs.modal", function() {
        $(".modal").forEach(function() {
            $("#errorModal").css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        });
    });
});