

//      HTML element id constants

const ID_FORM           = "#feedback-form";

const ID_FORM_TITLE     = "#val-title";
const ID_FORM_DATA      = "#val-data";
const ID_FORM_CATEGORY  = "#val-category";

const ID_BUTTON_SUBMIT  = "#submit-feedback";

/*
 *   HTML TEMPLATES
 * */

var html_success_template = "<div class='feedback-success info-view'>";
    html_success_template += "<div class='center view-title'>Your feedback was sent successfully!</div>";
    html_success_template += "<div class='center'>Thank you supporting me and my game! <3 All the love from me to you <3</div>";
    html_success_template += "</div>";
    



//      SCRIPT START
$(document).ready(feedbackStart);

function feedbackStart() {
    
}

function insertNewFeedback(){
    
    var title       = $(ID_FORM_TITLE).val();
    var data        = $(ID_FORM_DATA).val();
    var category    = $(ID_FORM_CATEGORY).val();
    
    
    $.post("feedback/controller/feedbackController.php", {insert_feedback: 1, feedback_title: title, feedback_data: data, feedback_category: category}, function(response)
    {
        var result = JSON.parse(response);
        var status = result['STATUS'];
        
        switch (status)
        {
            case "OK":
                showSuccessMessage();
                break;
            case "FAILED":
                confirm("I hate myself for saying this but... \N Something might have failed...");
                break;
            default:
                confirm("I hate myself for saying this but... \N Something might have failed...");
                break;
        }
    });
}

function showSuccessMessage(){
    $(ID_BUTTON_SUBMIT).hide(0);
    $(ID_FORM).html(html_success_template);
}
