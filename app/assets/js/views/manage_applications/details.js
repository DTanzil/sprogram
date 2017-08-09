/*
 * simple script that toggles the visability of page sections and some icons
 * creating a basic expandable/collapsable type thing
 *
 * TODO: Implement this basassery:
 * https://developers.google.com/web/updates/2017/03/performant-expand-and-collapse
 */

$(document).ready(function() {
	$('span.glyphicon-expand').click(expand);

	$('button.venueApprove').click(approveVenue);

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

	function approveVenue(event) {
		console.log('click');
		
		var btn = $(this);
		var venueID = btn.val();
		var userRoleID = btn.siblings('h4').val();

		console.log(venueID, userRoleID);

	}
});