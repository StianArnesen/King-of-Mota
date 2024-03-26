function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d3})+(?!\d))/g, ",");
}

function fixPriceLabel(className){
	var items_list = document.getElementsByClassName(className);

	for(var i = 0; i < items_list.length; i++){
	    items_list[i].innerHTML = numberWithCommas(items_list[i].innerHTML);
	}	
}

function fixPriceLabelId(id){
	var item = document.getElementById(id);
	console.log("price fix!");
	item.innerHTML = numberWithCommas(item.innerHTML);
}


