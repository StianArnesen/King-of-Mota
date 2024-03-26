

const _HTML_ERR_NAME 		= "#err-name";

const _HTML_ERR_PASS 		= "#err-pass";
const _HTML_ERR_PASS_RETYPE = "#err-pass-retype";

const _HTML_ERR_EMAIL 		= "#err-mail";



const _HTML_INPUT_NAME		= "#input-username";
const _HTML_INPUT_PASS		= "#input-pass";
const _HTML_INPUT_PASS_RE	= "#input-pass-retype";
const _HTML_INPUT_MAIL		= "#input-mail";

const _HTML_INPUT_SUBMIT	= "#input-submit";

const pass_length_min		= 7;
const pass_length_max		= 25;


var valid_name 		= false;
var valid_pass 		= false;
var valid_pass_re 	= false;


$(document).ready(main);


function main()
{
	initEventListeners();

}
function initEventListeners()
{

    var name;
    var pass;
    var pass_re;
    
	$(_HTML_INPUT_NAME).on('input', function(e) {
        name = $(this).val();
		validateUsername(name);
        updateStatus();
	});
	$(_HTML_INPUT_PASS).on('input', function(e) {
    	pass = $(this).val();
        if(validatePasswordLength(pass)){
            if($(this).hasClass("invalid")){
                $(this).removeClass("invalid");
            }
            $(this).addClass("valid");
            valid_pass = true;
		}
		else 	 
		{
            valid_pass = false;
			if($(this).hasClass("valid")){
                $(this).removeClass("valid");
			}
            $(this).addClass("invalid");
		}
        validatePasswords(pass, pass_re);
        updateStatus();
    });
    $(_HTML_INPUT_PASS_RE).on('input', function(e) {
        pass_re = $(this).val();
        if(validatePasswords(pass, pass_re)){
            valid_pass_re = true;
            if($(this).hasClass("invalid")){
                $(this).removeClass("invalid");
            }
            $(this).addClass("valid");
		}
		else
		{
            valid_pass_re = false;
            if($(this).hasClass("valid")){
                $(this).removeClass("valid");
            }
            $(this).addClass("invalid");
		}
        updateStatus();
    });
}

function updateStatus(){
    if(valid_name && valid_pass && valid_pass_re){
    	submitEnable();
	}
	else
	{
        submitDisable();
	}
}

function validatePasswords(pass, pass_re){
	if(pass === pass_re){
        if(pass.length >= pass_length_min && pass.length <= pass_length_max){
            if(pass_re.length >= pass_length_min && pass_re.length <= pass_length_max){
				return true;
            }
        }
	}
	return false;
}

function validatePasswordLength(pass){
	return (pass.length >= pass_length_min && pass.length <= pass_length_max);
}


function validateUsername(username)
{
	var HTML;
	if(username.length < 5 || username.length > 20)
	{
		HTML = "<div class='red'>Username must be between 5 and 20 characters long.</div>";
        $(_HTML_ERR_NAME).html(HTML);
        valid_name = false;
        if(! $(_HTML_INPUT_NAME).hasClass("invalid"))
        {
            if($(_HTML_INPUT_NAME).hasClass("valid")){
                $(_HTML_INPUT_NAME).removeClass("valid");
            }
            $(_HTML_INPUT_NAME).addClass("invalid");
        }
        return;
	}
	$.post("utils/new_user/RegisterController.php", {V_UNAME: username}, function(data){

		var result = JSON.parse(data);

		var ERROR = result['ERR'];

		if(ERROR)
		{
			switch(ERROR)
			{
				case "INVALID_CHAR":
					HTML = "<div class='red'>Username can only contain the letters a-z, A-Z and numbers 0-9 </div>";
					break;

				case "IN_USE":
					HTML = "<div class='red'>Sorry. This username is already in use.</div>";
					
					break;

				case "INVALID_LENGTH":
					HTML = "<div class='red'>Username must be between 5 and 20 characters long.</div>";
					break;

				default:
					HTML = "<div class='icon-valid'></div>";
					break;
			}
			if(! $(_HTML_INPUT_NAME).hasClass("invalid"))
			{
                if($(_HTML_INPUT_NAME).hasClass("valid")){
                    $(_HTML_INPUT_NAME).removeClass("valid");
                }
				$(_HTML_INPUT_NAME).addClass("invalid");	
			}
            valid_name = false;
		}
		else
		{
            valid_name = true;
			if(! $(_HTML_INPUT_NAME).hasClass("valid"))
			{
				if($(_HTML_INPUT_NAME).hasClass("invalid")){
                    $(_HTML_INPUT_NAME).removeClass("invalid");
				}
				$(_HTML_INPUT_NAME).addClass("valid");	
			}
			
			HTML = "<div class='green'>Valid</div>";
		}

		$(_HTML_ERR_NAME).html(HTML);
		
	});
}

function submitEnable()
{
    $(_HTML_INPUT_SUBMIT).prop("disabled", false);
}
function submitDisable()
{
    $(_HTML_INPUT_SUBMIT).prop("disabled", true);
}