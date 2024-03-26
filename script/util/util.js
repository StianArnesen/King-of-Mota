function secondsToTime(n)
{
	n = Math.abs(n);
    if(n < 60){
    	return "Just now";
    }
    if(n < 60*60){
    	return Math.floor(n/60) + " minutes ago";
    }
    if(n < (60*60*24)){
    	return Math.floor(n/(60*60)) + " hours ago";
    }
    return Math.floor(n/(60*60*24)) + " days ago";
}

function secondsToTimeV1(n)
{
	n = Math.abs(n);
    var hours = Math.floor(n/60/60),
        minutes = Math.floor((n - (hours * 60 * 60))/60),
        seconds = Math.round(n - (hours * 60 * 60) - (minutes * 60));
    return hours + ':' + ((minutes < 10) ? '0' + minutes : minutes) + ':' + ((seconds < 10) ? '0' + seconds : seconds);
}


Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };