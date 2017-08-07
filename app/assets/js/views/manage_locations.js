$(document).ready(function() {
	var basepath = 'http://localhost/';
	var options = [];

	$.ajax({
		method: 'GET',
		url: basepath + 'sprogram-app/app/Manage_locations/getOperators',
		success: populateOperators,
		error: function(xhr) {
			console.error(xhr);
		}
	});

	$('.admin-add').select2({
		placeholder: "Add an admin",
		data: options,
		allowClear: true
	});

	$('.glyphicon-minus-sign').click(function(event) {
		$(this).parents('button').remove();
	});

	$('.glyphicon-plus-sign').click(function(event) {
		
			var adminID = $('.admin-add').val();
			var locationID = $('.admin-add').attr('id');
			var adminInfo = $('.admin-add:selected').innerText;

			var data = $('.admin-add').select2('data')[0].text;
			if(data != '') {
				addOperator(adminID, data);
			}
			
			
	});

	// $('form').submit(function() {
	// 	var buttons = $('button.list-group-item');
	// 	var admins = [];
	// 	buttons.each(function(i, d) {
	// 		admins.push(d.id);
	// 	});

	// 		var location = $('.admin-add').attr('id');



	// 	$.ajax({
	// 		method: 'POST',
	// 		url: basepath + 'sprogram-app/app/Manage_locations/addOperatorToRoom',
	// 		data: {
	// 			admins: admins,
	// 			locationID: location
	// 		},
	// 		success: function(data) { console.log(data) },
	// 		error: function(xhr) {
	// 			console.log(xhr);
	// 		}
	// 	});
	// });

	function populateOperators(data) {
		$.each(data, function(i, d) {
			options.push({id: d.UserID, text: d.UserFname+' '+d.UserLname+' - '+d.NetID});
		});

		$('.admin-add').select2({
			placeholder: "Add an admin",
			data: options
		});
	}

	function addOperator(userID, text) {
		var btn = $('<button id="'+userID+'" type="button" class="list-group-item"><span class="glyphicon glyphicon-minus-sign"></span>' + text + '<input type="hidden" name="admins[]" value="' + userID + '" /></button>');
		console.log(btn);
		btn.insertBefore($('.admin-add').parents('li'));

		$('.glyphicon-minus-sign').click(function(event) {
			$(this).parents('button').remove();
		});
	}
});
