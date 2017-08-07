Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function equalizeHeights(targets){
	var highest = $(targets[0]).height();
	
	$.each(targets, function(i, ele){
		var height = $(ele).height();
		if(height > highest){
			highest = height;	
		}
	});
	
	var padding = $(targets[0]).outerHeight() - $(targets[0]).innerHeight();
	
	$(targets).innerHeight = highest + padding;
		
}

function showMessage(message, target){
		
}

function showSuccess(message, target, destination){
	target.empty();
	showAlert(message, target, destination, "success", true);
}

function showError(message, target, destination){
	target.empty();
	showAlert(message, target, destination, "alert", true);
}

function showAlert(message, target, destination, alertClass, temp){
	
	var div = $("<div/>").addClass("alert-box").addClass(alertClass).text(message).hide().appendTo(target);
	
	switch(alertClass){
		case 'alert':
			div.fadeIn(400, 'swing').delay(4000).fadeOut(400, 'swing');
			break;
		default:
			div.fadeIn(400, 'swing').delay(2000).fadeOut(400, 'swing');
			break;	
	}
	
	if(destination){
		div.text(message + " Redirecting...");
	
		setTimeout(function(){
			window.location.href = destination;
		}, 1100);
	}	
}

function ajaxWrapper(msg, msgTarget, ajaxBody){
	showSuccess(msg, msgTarget);
	if(!flag)
		ajaxBody();
	
}

function postRequest(url, data, msg, msgTarget){
	
}
