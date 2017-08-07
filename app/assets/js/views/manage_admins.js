$(document).ready(function() {
	$('.delete').click(function(event) {
		$('button.delete').prop('disabled', true);

		var thisButton = $(this)[0];
		var thisTr = $(thisButton).parents('tbody tr');
		var thisAdminId = $(thisButton).attr('value');
		var warning = 
		$('<tr></tr>').append('<td colspan="7"><div class="alert alert-danger" style="height:60px; margin:0">' +
				'<strong>Danger:</strong> This operation is not reversable. <a class="btn btn-xs btn-info hideButton">Nevermind</a>' +
														   '<a class="btn btn-xs btn-danger" href="' + window.location.href + '/delete/' + thisAdminId + '">Delete</a>' +
		'</div></td>');
		warning.insertAfter(thisTr);

		$('.hideButton').click(function() {
			$('button.delete').prop('disabled', false);
			$(this).parents('tr').remove();
		});
	});
});