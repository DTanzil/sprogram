/*
 * simple script that toggles the visability of page sections and some icons
 * creating a basic expandable/collapsable type thing
 *
 * TODO: Implement this basassery:
 * https://developers.google.com/web/updates/2017/03/performant-expand-and-collapse
 */

$(document).ready(function() {
	var addressees = [];
	var templates = [];

	getTemplates();

	$('span.glyphicon-expand').click(expand);

	//$('button.venueApprove').click(updateApproval);
	//$('button.venueDeny').click(updateApproval);
	$('button.VenueOperatorAction').click(updateVenueApproval);
	$('button.SponsorAction').click(updateSponsorApproval);
	$('button.CommitteeAction').click(updateCommiteeApproval);

	$('button.resend').click(resendEmail);
	$('button#createNote').click(createNote);
	$('button#expire').click(expireApp);
	$('button#inactivate').click(inactivateApp);

	$('#addressee').select2({
		placeholder: "recipient...",
		data: addressees,
		allowClear: true
	});

	$('#sendReminder').click(sendReminder);


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

	function updateVenueApproval(event) {
		var btn = $(this);
		var apprID = btn.parent('div.info-box').attr('value');
		var decision = btn.attr('value');

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

	function updateSponsorApproval(event) {
		var btn = $(this);
		var apprID = btn.parent('div.info-box').attr('value');
		var decision = btn.attr('value');
		var appID = $('#appID').text();

		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/sponsorDecision',
			method: 'post',
			data:
			{
				approvalID: apprID,
				decision: decision,
				appID: appID
			},
			success: success,
			error: error
		});

	}

	function updateCommiteeApproval(event) {
		var btn = $(this);
		//var apprID = btn.parent('div.info-box').attr('value');
		var decision = btn.attr('value');
		var appID = $('#appID').text();

		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/committeeDecision',
			method: 'post',
			data:
			{
				//approvalID: apprID,
				decision: decision,
				appID: appID
			},
			success: function(data) {
				console.log(data);
			},
			success: success,
			error: error
		});

	}

	function resendEmail(event) {
		var $tr = $(this).parents('tr');
		var rec = $tr.children('td.emailRec').attr('value');
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

	function getTemplates() {
		var appID = $('#appID').text();
		console.log(appID);
		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/getTemplates',
			method: 'post',
			data:
			{
				appID: appID
			},
			success: function(data) {
				data = JSON.parse(data);
				templates = data.map(function(i) {
					return {id: i['EmailTemplateID'], text: i['EmailTemplateName']};
				});

				$('#template').select2({
					placeholder: "template...",
					data: templates,
					allowClear: true
				});
			},
			error: error
		})
	}

	function sendReminder(event) {
		var rec = $('#addressee').val();
		var template = $('#template').val();
		var appID = $('#appID').text();
		console.log(appID);

		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/sendReminder',
			method: 'post',
			data:
			{
				rec: rec,
				template: template,
				appID: appID
			},
			success: success,
			error: error

		});
	}

	function createNote(event) {
		var appID = $('#appID').text();
		var noteText = $('textarea#noteText').val();

		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/createNote',
			method: 'post',
			data:
			{
				appID: appID,
				noteText: noteText
			},
			success: success,
			error: error
		});
	}

	function expireApp(event) {
		var appID = $('#appID').text();
		var expReason = $('textarea#expReason').val();

		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/expireApp',
			method: 'post',
			data:
			{
				appID: appID,
				expReason: expReason
			},
			success: success,
			error: error
		});
	}

	function inactivateApp(event) {
		var appID = $('#appID').text();
		var inactiveReason = $('textarea#inactiveReason').val();

		$.ajax({
			url: 'http://localhost/sprogram-app/app/applications/inactivate',
			method: 'post',
			data:
			{
				appID: appID,
				inactiveReason: inactiveReason
			},
			success: success,
			error: error
		});
	}

	function success(data) {
		var btns = $('button[value=' + data['ApprovalID'] + ']');
		var box = btns.parent();
		btns.remove();

		box.find('h4.appr-status').text('Status: ' + data['Descision']);
		box.find('h4.appr-date').text('Approval Process Ended ' + data['ApprovalEndDate']);
		//$('btn[value="' + data + '"]').
		//
		location.reload();
	}

	function error(error) {
		console.error(error);
	}
});