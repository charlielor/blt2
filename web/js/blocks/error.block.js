$(document).ready(function() {
    // Get error modal
    var errorModal = $("#errorModal");

    // If the Receiver modal is shown, move the modal "up" if there's already another modal up (by editing the z-index)
    errorModal.on("shown.bs.modal", function() {
        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($(".modal").hasClass("in")) {
            errorModal.css("z-index", parseInt($(".modal").css("z-index")) + 30);
        }
    });

});