// Global Namespace
var Camera = Camera || function(minWidth, minHeight, video, canvas, image, scale) {
        this.video = video;

        this.scale = scale;

        this.canvas = canvas;

        this.image = image;

        this.resolution = {
            "mandatory": {
                "minWidth": minWidth,
                "minHeight": minHeight
            }
        };

        // Setting up the mediaStream so that when the user closes the video, the stream ends
        this.mediaStream = null;

        // Video rotation
        this.videoRotation = 0;

        // Video rotated recently?
        this.rotated = 0;

        // Camera status
        this.on = false;

        // Start Camera
        this.startCamera = function(success, error) {
            var self = this;

            navigator.getUserMedia(
                {
                    audio: false,
                    video: self.resolution
                },
                function successCallBack(stream) {
                    var retryCount = 0;
                    var retryLimit = 50;

                    self.mediaStream = stream;

                    self.video.src = (window.URL && window.URL.createObjectURL(stream));

                    self.video.play();

                    self.video.onplaying = function(e) {
                        var videoWidth = this.videoWidth;
                        var videoHeight = this.videoHeight;
                        self.video.width = videoWidth * self.scale;
                        self.video.height = videoHeight * self.scale;

                        if (!videoWidth || !videoHeight) {
                            if (retryCount < retryLimit) {
                                retryCount++;
                                window.setTimeout(function () {
                                    self.video.pause();
                                    self.video.play();
                                }, 100);
                            }

                        } else if (videoWidth && videoHeight) {
                            self.image.height = self.video.videoHeight;
                            self.image.width = self.video.videoWidth;

                            self.canvas.width = videoWidth;
                            self.canvas.height = videoHeight;

                            self.on = true;

                            success();
                        } else {
                            console.log("An error has occurred: Can't retrieve video width and height");

                            error();
                        }
                    };
                }, function errorCallback(e) {
                    error();
                }
            );
        };

        // Stop Camera
        this.stopCamera = function(success, error) {
            var self = this;

            // Comment out for Chrome 47
            // self.mediaStream.stop();
            self.mediaStream = null;
            self.video.pause();

            if ((self.mediaStream == null) && (self.video.paused)) {
                self.on = false;
                self.video.removeAttribute("style");

                success();
            } else {
                console.log("Error: Could not pause/stop camera");
                error();
            }
        };

        // Take a picture
        this.takePicture = function(callback) {
            var self = this;

            // Get the frame from the video
            var ctx = self.canvas.getContext('2d');

            if (self.rotated == 1) {
                // Base on the rotation
                switch (self.videoRotation) {
                    case -90:
                    case 270:
                        ctx.translate(0, self.canvas.height);
                        self.image.height = self.video.videoWidth;
                        self.image.width = self.video.videoHeight;
                        break;
                    case 90:
                    case -270:
                        ctx.translate(self.canvas.width, 0);
                        self.image.height = self.video.videoWidth;
                        self.image.width = self.video.videoHeight;
                        break;
                    case 180:
                    case -180:
                        ctx.translate(self.canvas.width, self.canvas.height);
                        self.image.height = self.video.videoHeight;
                        self.image.width = self.video.videoWidth;
                        break;
                    case 0:
                        self.image.height = self.video.videoHeight;
                        self.image.width = self.video.videoWidth;
                        break;
                    default:
                        console.log('Error: Can not translate ctx');
                        break;
                }

                self.rotated = 0;
                ctx.rotate(self.videoRotation * (Math.PI/180));
            }

            // Draw the image to the canvas
            ctx.drawImage(self.video, 0, 0);

            // Set the source
            self.image.src = self.canvas.toDataURL('image/png', 1.0);

            callback();
        };

        // Rotate the video/canvas left
        this.rotateLeft = function() {
            var self = this;
            self.videoRotation -= 90;

            if (self.videoRotation < -270) {
                self.videoRotation = 0;
            }

            // Switch base on video rotation
            switch (self.videoRotation) {
                case 90:
                case -90:
                    self.canvas.height = self.video.videoWidth;
                    self.canvas.width = self.video.videoHeight;

                    self.video.height = self.video.videoWidth * self.scale;
                    break;
                case 180:
                case -180:
                    self.canvas.height = self.video.videoHeight;
                    self.canvas.width = self.video.videoWidth;

                    self.video.height = self.video.videoHeight * self.scale;
                    break;
                case 270:
                case -270:
                    self.canvas.height = self.video.videoWidth;
                    self.canvas.width = self.video.videoHeight;

                    self.video.height = self.video.videoWidth * self.scale;
                    break;
                case 0:
                    self.canvas.height = self.video.videoHeight;
                    self.canvas.width = self.video.videoWidth;

                    self.video.height = self.video.videoHeight * self.scale;
                    break;
                default:
                    console.log('Error: Could not rotate canvas');
                    break;
            }

            self.rotated = 1;
            self.video.style.transform = ' rotate(' + this.videoRotation + 'deg)';
        };

        // Rotate the video/canvas right
        this.rotateRight = function() {
            var self = this;
            self.videoRotation += 90;

            if (self.videoRotation > 270) {
                self.videoRotation = 0;
            }

            // Switch base on video rotation
            switch (self.videoRotation) {
                case 90:
                case -90:
                    self.canvas.height = self.video.videoWidth;
                    self.canvas.width = self.video.videoHeight;

                    self.video.height = self.video.videoWidth * self.scale;
                    break;
                case 180:
                case -180:
                    self.canvas.height = self.video.videoHeight;
                    self.canvas.width = self.video.videoWidth;

                    self.video.height = self.video.videoHeight * self.scale;
                    break;
                case 270:
                case -270:
                    self.canvas.height = self.video.videoWidth;
                    self.canvas.width = self.video.videoHeight;

                    self.video.height = self.video.videoWidth * self.scale;
                    break;
                case 0:
                    self.canvas.height = self.video.videoHeight;
                    self.canvas.width = self.video.videoWidth;

                    self.video.height = self.video.videoHeight * self.scale;
                    break;
                default:
                    console.log('Error: Could not rotate canvas');
                    break;
            }

            self.rotated = 1;
            self.video.style.transform = ' rotate(' + this.videoRotation + 'deg)';
        };

        // Get camera options
        this.getOptions = function() {
            var self = this;
            return JSON.stringify(self);
        };

    };