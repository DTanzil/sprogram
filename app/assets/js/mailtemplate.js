function populateActions(parentId){
	$.getJSON('<?php echo base_url();?>index.php/admin/edit_mail_templates/getAllActions', function(j){
	    $.each(j, function () {
		    $checkbox = $('<input></input>').attr("type", "checkbox").attr("name", "actions").attr("value", this.id).attr('id', "action-"+this.id);
		    $checkbox.change(function(){
			    newVars = new Array();
			    $.ajax({
				    dataType: 'json',
				    url: '<?php echo base_url();?>index.php/admin/edit_mail_templates/getVariablesForAction',
				    data: {action : $(this).attr('value')},
				    async:false, 
				    success: function(d){
			    		$.each(d, function() {
				    		if(d.variables != null)
				    			newVars = d.variables.split(',');
			       		});
				    }
		    	});
		    	if($(this).attr('checked') === 'checked'){
			    	variables[parseInt($(this).attr('value'))-1] = newVars;
		    	} else {
			    	variables[parseInt($(this).attr('value'))-1] = undefined;
		    	}
		    	populateVariables();
		    });
		    $label = $('<label></label>').html(this.name).attr("for", $checkbox.attr("id"));
		    $label.prepend($checkbox);
		    $("#"+parentId).append($label);
	    });
	});
}
function populateVariables(){
	$("#variables").empty();
	$.each(keepDuplicates(variables), function (index, value) {
		$("#variables").append($("<div class='panel three columns variable'></div>").html(value));
	});
}
Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};
function keepDuplicates(arr) {
	var copy = $.extend(true, [], arr);
	var firstIndex = -1;
	for(var i=0;i<copy.length;++i){
		if(typeof copy[i] != 'undefined'){
			firstIndex = i;
			break;
		}
	}
	if(firstIndex != -1){
		for(var i=0;i<copy[firstIndex].length;++i){
			for(var j=0;j<copy.length;++j){
				if(typeof copy[j] != 'undefined'){
					index = copy[j].indexOf(copy[firstIndex][i]);
					if(index == -1) {
						copy[firstIndex].splice(i,1);
						i--;
						break;
					}
				}
			}
		}
	}
	return copy[firstIndex];
}
function setActions(id){
	$("input[name=actions]").each(function(i, v){
		$(v).removeAttr('checked');
	});
	$.getJSON('<?php echo base_url();?>index.php/admin/edit_mail_templates/getActionsForTemplate', {template : id}, function(j){
	    $.each(j, function () {
		    $("#action-"+this.idAction).attr('checked', 'checked');
	    });
	});
}