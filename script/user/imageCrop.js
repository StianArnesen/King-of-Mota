
/*
    Image crop info    
*/

var offsetX;
var offsetY;

var width;
var height;
// Jquery -> Starting

const save_btn = "#btn_save_cropping";

var cropper;

$(document).ready(init);

function init(){
    
}

function initImageCrop(){
    /*var cropperOptions = {
        cropUrl:'path_to_your_image_cropping_file.php',
        loadPicture:'http://kingofmota.com/img/uploads/583ee5d63014376718cd719838ccc982c2bb0b8aafcd7.jpg'
    }
    var cropperHeader = new Croppic('yourId', cropperOptions);
    */

    var image = document.querySelector('.img-container > img');

    var minAspectRatio = 1;
    var maxAspectRatio = 1;
    
    cropper = new Cropper(image, {
        ready: function () {
            var cropper = this.cropper;
            var containerData = cropper.getContainerData();
            var cropBoxData = cropper.getCropBoxData();
            var aspectRatio = cropBoxData.width / cropBoxData.height;
            var newCropBoxWidth;

            if (aspectRatio < minAspectRatio || aspectRatio > maxAspectRatio) {
                newCropBoxWidth = cropBoxData.height * ((minAspectRatio + maxAspectRatio) / 2);

                cropper.setCropBoxData({
                    left: (containerData.width - newCropBoxWidth) / 2,
                    width: newCropBoxWidth
                });
            }
        },
        cropmove: function () {
            var cropper = this.cropper;
            var cropBoxData = cropper.getCropBoxData();
            var aspectRatio = cropBoxData.width / cropBoxData.height;

            if (aspectRatio < minAspectRatio) {
                cropper.setCropBoxData({
                    width: cropBoxData.height * minAspectRatio
                });
            } else if (aspectRatio > maxAspectRatio) {
                cropper.setCropBoxData({
                    width: cropBoxData.height * maxAspectRatio
                });
            }
        }
    });
    
    showSaveButton();
}

function showSaveButton(){
    $(save_btn).show(0);
}

function saveCropping(){
    
    // http://kingofmota.com
    //$.post("user/PublicUser.php", {'set_image_crop': data});
    
    console.log(cropper.getData(false));
}

