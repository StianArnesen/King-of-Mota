

/*
*   Global time inputs ID
* */

const TIME_HRS = "#grow-time-hrs";
const TIME_MIN = "#grow-time-min";
const TIME_SEC = "#grow-time-sec";

const TIME_FINAL_SEC = "#grow-time-final";


$(document).ready(main);


function main()
{
    initImagePreviewOnUploadEvent();
    initGrowTimeChangeListener();

}

function initGrowTimeChangeListener() {
    $('.time-input').change(function(){

        var totalSeconds = 0;

        var hrs =  parseInt($(TIME_HRS).val());
        var min =  parseInt($(TIME_MIN).val());
        var sec =  parseInt($(TIME_SEC).val());

        totalSeconds += hrs * 60 * 60;
        totalSeconds += min * 60;
        totalSeconds += sec;

        $(TIME_FINAL_SEC).val(totalSeconds);
    });
}

function initImagePreviewOnUploadEvent()
{
    $("#item-image-upload-file").change(function(){
        readURL(this);
    });
}

function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#item-image-preview').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

