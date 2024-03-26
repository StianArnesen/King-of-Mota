const DialogType = Object.freeze({
    DIALOG_TYPE_CONFIRM     : 0,
    DIALOG_TYPE_OK          : 1,
    DIALOG_TYPE_ERROR       : 2
});

let DIALOG_WINDOW_BACKGROUND_ID     = "#top-banner-overlay-view";
let DIALOG_TITLE_ID                 = "#overlay-msg-title";
let DIALOG_WINDOW_ID                = "#top-banner-overlay-data";
let DIALOG_MESSAGE_ID               = "#overlay-msg-text";
let DIALOG_ACTION_CONFIRM_BUTTON    = "#overlay-action-button";
let DIALOG_ACTION_CANCEL_BUTTON     = "#overlay-button-cancel";
let DIALOG_MESSAGE_ERROR_CODE       = "#overlay-msg-error-code";




const fadeSpeed = 200; // In ms

class GameDialog
{
    /**
     * @param {DialogType} type The dialog type.
     * @param {string} title The title of the dialog window.
     * @param {string} text The title of the dialog window.
     * @param {Function} confirmActionCallback The function to run if the confirm button is pressed in the dialog window.
     * 
    */
    constructor(type, title, text, error_code = null, confirmActionCallback = null){
        switch (type)
        {
            case DialogType.DIALOG_TYPE_CONFIRM:
                this.showConfirmationDialog(title, text, confirmActionCallback);
                break;
            case DialogType.DIALOG_TYPE_ERROR:
                this.showErrorDialog(title, text, error_code);
                break;
            default:
                alert("The GameDialog.class was given an invalid DialogType as argument! ");
        }
    }
    hideGameDialog(){
        $(DIALOG_WINDOW_BACKGROUND_ID).fadeOut(fadeSpeed);
        $(DIALOG_WINDOW_ID).fadeOut(fadeSpeed);
    }
    showGameDialog(){
        $(DIALOG_WINDOW_BACKGROUND_ID).fadeIn(fadeSpeed);
        $(DIALOG_WINDOW_ID).fadeIn(fadeSpeed);
    }
    showErrorDialog(title, text, error_code){
        $(DIALOG_TITLE_ID).html(title);
        $(DIALOG_MESSAGE_ID).html(text);
        $(DIALOG_MESSAGE_ERROR_CODE).html("Error code: " + error_code);
        $(DIALOG_MESSAGE_ERROR_CODE).show();

        $(DIALOG_ACTION_CONFIRM_BUTTON).html("Ok");
        $(DIALOG_ACTION_CONFIRM_BUTTON).unbind('click').bind('click', this.hideGameDialog);
        
        $(DIALOG_ACTION_CANCEL_BUTTON).hide();
        
        this.showGameDialog();
    }
    showConfirmationDialog(title, text, confirmActionCallback){
        $(DIALOG_TITLE_ID).html(title);
        $(DIALOG_MESSAGE_ID).html(text);
        
        $(DIALOG_ACTION_CONFIRM_BUTTON).html("Yes");
        $(DIALOG_ACTION_CONFIRM_BUTTON).unbind('click').bind('click', (confirmActionCallback !== null)? confirmActionCallback : null);
        
        $(DIALOG_ACTION_CANCEL_BUTTON).html("No");
        $(DIALOG_ACTION_CANCEL_BUTTON).show();
        $(DIALOG_MESSAGE_ERROR_CODE).hide();
        $(DIALOG_ACTION_CANCEL_BUTTON).on('click', function(){
            hideGameDialog();
        });
        

        this.showGameDialog();
    }
    setHtmlPropertiesOfDialogWindow(title, text, confirmActionCallback){

    }
}