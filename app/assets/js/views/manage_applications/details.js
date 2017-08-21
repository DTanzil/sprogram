/*
 * simple script that toggles the visability of page sections and some icons
 * creating a basic expandable/collapsable type thing
 *
 * TODO: Implement this basassery:
 * https://developers.google.com/web/updates/2017/03/performant-expand-and-collapse
 */

$(document).ready(function() {
	$('span.glyphicon-expand').click(expand);

	$('button.venueApprove').click(updateApproval);
	$('button.venueDeny').click(updateApproval);
	$('button.resend').click(resendEmail);

	function collapse(event) {
		var $el = $(this);

		var expandable = $el.parent().siblings('div.expand');
		expandable.removeClass('expand');
		expandable.addClass('collapse');
		$el.removeClass('glyphicon-collapse-down');
		$el.addClass('glyphicon-expand');

		$el.click(expand);
	}

	function expand(event) {
		var $el = $(this);

		var expandable = $el.parent().siblings('div.collapse');
		expandable.removeClass('collapse');
		expandable.addClass('expand');
		$el.removeClass('glyphicon-expand');
		$el.addClass('glyphicon-collapse-down');

		$el.click(collapse);
	}

	function updateApproval(event) {


		var btn = $(this);
		var apprID = btn.val();
		var decision = btn.hasClass('venueApprove') ? 'approved' : 'denied';

		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/venueDecision',
			method: 'post',
			data:
			{
				approvalID: apprID,
				decision: decision
			},
			success: success,
			error: error
		});

	}

	function resendEmail(event) {
		var $tr = $(this).parents('tr');
		var rec = $tr.children('td.emailRec').text();
		var template = $tr.children('td.emailTemplate').attr('value');
		var appID = $('p#appID').text();

		console.log(template);
		console.log(rec);

		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/sendEmail',
			method: 'post',
			data:
			{
				rec: rec,
				template: template,
				appID: appID
			},
			success: success,
			error: error

		})
	}

	function success(data) {
		var btns = $('button[value=' + data['ApprovalID'] + ']');
		var box = btns.parent();
		btns.remove();

		box.find('h4.appr-status').text('Status: ' + data['Descision']);
		box.find('h4.appr-date').text('Approval Process Ended ' + data['ApprovalEndDate']);
		//$('btn[value="' + data + '"]').
	}

	function error(error) {
		console.error(error);
	}
});