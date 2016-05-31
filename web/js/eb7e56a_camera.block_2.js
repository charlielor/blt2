$(document).ready(function() {
    var video = document.getElementById("video");
    var image = document.getElementById("image");
    var canvas = document.getElementById("canvas");

    var imageConfirmModal = $("#imageConfirmModal");
    var confirmPicture = $("#confirmPicture");

    var cameraModal = $("#cameraModal");

    var cameraWidth = 1600;
    var cameraHeight = 1200;
    var zoom = .55;

    var referer = "";

    cameraModal.on("show.bs.modal", function(e) {
        referer = $(e.relatedTarget).data('referer');

        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($("#packageModal").hasClass("in")) {
            cameraModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        }
    });

    imageConfirmModal.on("show.bs.modal", function(e) {
        // If new package modal is shown, increase the z-index so that this modal is on top of the new package modal
        if ($("#packageModal").hasClass("in")) {
            imageConfirmModal.css("z-index", parseInt($("#packageModal").css("z-index")) + 30);
        }
    });

    confirmPicture.on("click", function() {
        // Append the picture to the form VIA hidden input tag with the value containing the base64 src of the image
        var allHiddenImageTags = document.getElementsByClassName("thumbnail");
        var allHiddenImageTagsLength = allHiddenImageTags.length;
        var imageThumbnail = document.getElementById("imageThumbnail");

        console.log(allHiddenImageTags);

        // Create a thumbnail of the image so that the user can see what's being uploaded
        createThumbnail(allHiddenImageTagsLength, image.src);
    });

    cameraModal.on("shown.bs.modal", function() {
        camera.startCamera(function success(){
            $(video).one("click", function() {
                camera.takePicture(function() {
                    image.width = image.width * zoom;
                    image.height = image.height * zoom;

                    cameraModal.modal("hide");
                    imageConfirmModal.modal("show");

                });
            });

            console.log(camera.getOptions());
        }, function error() {
            alert("Unable to start camera!");
            cameraModal.modal("hide");
        });
    });

    cameraModal.on("hidden.bs.modal", function() {
        if (camera.on == true) {
            camera.stopCamera(function() {
                console.log('Successfully stopped');
            });
        }
    });

    // Set the navigator.getUserMedia to the appropriate browser
    navigator.getUserMedia = navigator.getUserMedia||navigator.webkitGetUserMedia||
        navigator.mozGetUserMedia||navigator.msGetUserMedia;


    // Set the window.URL object to the appropriate browser
    window.URL=window.URL||window.webkitURL||window.mozURL||window.msURL;

    // If the browser supports getUserMedia, create the camera object
    if (navigator.getUserMedia) {
        var camera = new Camera(cameraWidth, cameraHeight, video, canvas, image, zoom);
    }

    function createThumbnail(thumbnailNumber, src) {
        var thumbnailsDiv = document.getElementById("thumbnailsDiv");

        var newImageThumbnailId = "imageThumbnail_" + thumbnailNumber;

        $(thumbnailsDiv).append('<div class="form-control-static col-md-8 col-md-offset-4"><img name="imageThumbnail" src="' + src + '" id="' + newImageThumbnailId + '" class="img-thumbnail thumbnail" /></div>');

    }
});