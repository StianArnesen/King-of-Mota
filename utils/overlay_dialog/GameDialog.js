
const DIALOG_TYPE_WARN = 0;


$(document).ready(initGameDialog);

function initGameDialog()
{

}


function showGameDialog(title, type, desc)
{
	var HTML = getGameDialogLayout(title, type, desc);
	
	$("#dialog-view").html(HTML);

	$("#static-game-dialog").fadeIn(300);
	
	$("#dialog-view").fadeIn(300);
}
function closeGameDialog()
{
	$("#static-game-dialog").fadeOut(300);
}

function getGameDialogLayout(title, type, desc)
{	
	var HTML;

	HTML = '<div id="static-game-dialog" class="game-dialog">';
		HTML += '<div class="static-game-dialog-border">';
			HTML += '<div class="static-game-dialog-title">';
				HTML += title;
			HTML += '</div>';		// dialog-title
		HTML += '</div>';			// dialog-border

		HTML += '<div clas="static-game-dialog-desc">';
			HTML += desc;
		HTML += '</div>';			// dialog-desc

		HTML += '<div clas="static-game-dialog-options">';
			
			if(type == DIALOG_TYPE_WARN)
			{
				HTML += '<div class="static-game-dialog-button" onclick="closeGameDialog()">';
					HTML += 'Close';
				HTML += '</div>';
			}

		HTML += '</div>';			// dialog-options
		

	HTML += '</div>';				// dialog

	return HTML;
}