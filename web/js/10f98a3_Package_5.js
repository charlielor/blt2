// Global Namespace
var Package = Package || function(trackingNumber) {
        this.trackingNumber = trackingNumber;

        this.isNew = true;

        this.shipper = {
            "id": "",
            "name": ""
        };

        this.vendor = {
            "id": "",
            "name": ""
        };

        this.receiver = {
            "id": "",
            "name": "",
            "deliveryRoom": ""
        };

        this.numberOfPackages = 1;

        this.packingSlips = [];
    };

Package.prototype.valdiatePackage = function() {
    return ((this.trackingNumber != null) && (this.isNew != null) && (this.shipper.id != "")
    && (this.shipper.name != "") && (this.vendor.id != "") && (this.vendor.name != "")
    && (this.receiver.id != "") && (this.receiver.name != "")
    && (this.receiver.deliveryRoom != "") && (this.numberOfPackages > 0));
};