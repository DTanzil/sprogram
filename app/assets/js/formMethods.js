function toggle(hook, element, elementRule){ //toggle show/hide for elements using checkbox as hook
	$(hook).change(function(){
		if( $(this).attr("checked") == "checked" ){
			 if($.isArray(element)){//if an array of elements is to be toggled
				 $.each(element, function(index, value){
					 $(value).css("display", "block");
				 });
			 }
			 else{
			 	$(element).css("display","block");
			 }
			 if(arguments.length == 3){ //if a rule element is passed. 
				 $(elementRule).rules("add",{required : true});
			 }
		} else{
			if($.isArray(element)){//if an array of elements is to be toggled
				 $.each(element, function(index, value){
					 $(value).css("display", "none");
				 });
			 }
			else{
				$(element).css("display", "none");
			}
			if(arguments.length == 3){
			 	$(elementRule).rules("remove","required");
			}
		}
	});
}
function applyMasks(){
	$(".phone").mask('(111)111-1111');
	$(".money").mask('000,000,000,000,000.00', {reverse: true});
}
function nextStep(form){
	if(validate_form()){ 
		step++;
		if($("#step"+step).length > 0){
			$("#step"+step).css("display", "block");
		}
		else{
			$.ajax({
				url: 'https://depts.washington.edu/sprogram/wordpress/wp-content/themes/uw-theme/api/api.php',
				type: 'POST',
				data: {"step" : step, 'form' : form},
				success: function(data){
					$("#form").append(data);
					applyMasks();
				},
				failure: function(data){
					//if last step, post to update script. 
				}
			});
		}
		$("#step"+(step-1)).css("display", "none");
	}
}
function previousStep(){
	step--;
	$("#step"+step).css("display","block");
	$("#step"+(step+1)).css("display", "none");

}