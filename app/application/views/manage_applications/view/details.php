<h1><b><?=$details['EventName']?></b></h1>
<h2><b><?= ($details['Status'] == 'expired' || $details['Status'] == 'denied' || $details['Status'] == 'approved' || $details['Status'] == 'cancelled') ? $details['Status'] : 'Awaiting ' . $details['Status'] . ' approval'?></b></h2>
<div class="alert alert-warning">

<!-- TODO: make this look nicer -->
<!-- <div class="row"> -->
	<!-- <div class="col-xs-1"> -->
		<span class="glyphicon glyphicon-exclamation-sign"></span>
	<!-- </div> -->
	<!-- <div class="col-xs-11">  -->
		You are the sponsor for this application which requires one or more approvals. Details regarding this application can be found below.
	<!-- </div> -->
<!-- </div> -->
</div>

<div id="actionItems">

<?= print_r($this->approval->getOpenApprovalsForUser($details['ApplicationID'], $this->authorize->getNetid())) ?>

	 <?php foreach($this->approval->getOpenApprovalsForUser($details['ApplicationID'], $this->authorize->getNetid()) as $openAppr) { ?>
		<div class="row">
		<div class="col-md-8">
		<h3>{Role} Approval</h3>
		<div class="info-box">
			<h4>Sponsor Name</h4>
			<p>email@email.email</p>
			<p>You are the {role} for this application. Choose whether to approve or deny it:</p>
			<button class="btn btn-success">Approve</button>
			<button class="btn btn-danger">Deny</button>

			<h4>Status:</h4>
			<h4>Category:</h4>
			<h4>Approval Process Started {date}</h4>
		</div> 
		</div> 
		</div>
	<?php } ?>

</div>

<div id="app-details">
	<h2><span class="glyphicon glyphicon-expand"></span>Application Details</h2>
	<div class="collapse">
	<div class="row">
	<div class="col-md-8">
	<h3>Event Details</h3>
	<div class="info-box">
		<h4><?=$details['EventName']?></h4>
		<div class="row">
			<p><?=$details['EventStartDate']?></p>

			<?php 
			$splitDesc = splitDesc($details['EventDesc']);
			foreach($splitDesc as $chunk) {
				?> <p class="col-xs-6"><?=$chunk?></p> <?php
			}
			?>
		</div>

		<h4>Administrative Details</h4>
		<div class="row">
			<div class="col-xs-6">
				<dl>
					<dt>Permit Type:</dt>
						<!-- TODO: This might be permit/app type -->
						<dd><?=$details['Category']?></dd>
					<dt>Sponsor Assigned:</dt>
						<dd><?= $users['Sponsor'][0]['UserFname'] . ' ' . $users['Sponsor'][0]['UserLname']?></dd>
					<dt>Donations Accepted:</dt>
						<dd><?= $details['HasDonations'] == 1 ? 'Yes' : 'No' ?></dd>
					<dt>Parking:</dt>
						<dd>Prepaid - <?= $details['ComuterServicesParking'] == 1 ? 'Yes' : 'No' ?></dd>
						<dd>Gatehouse - <?= $details['GatehouseParking'] == 1 ? 'Yes' : 'No' ?></dd>
						<dd>Bus Parking - <?= $details['BusParking'] == 1 ? 'Yes' : 'No' ?></dd>
				</dl>
			</div>
			<div class="col-xs-6">
			<dl>
				<dt>Political Status:</dt>
					<dd><?= $details['IsPolitical'] == 1 ? 'Yes' : 'No' ?></dd>
				<dt>Alcohol:</dt>
					<dd><?= $details['Alcohol'] == 1 ? 'Yes' : 'No' ?></dd>
				<dt>Registration/Admission Fees:</dt>
					<dd><?= (is_numeric($details['RegFeeAmount']) && $details['RegFeeAmount'] > 0) ? '$' . $details['RegFeeAmount'] : 'No' ?></dd>
				<dt>Amplified Sound:</dt>
					<dd><?= $details['AmpSoundDesc'] == '' ? 'No' : $details['AmpSoundDesc']?></dd>
			</dl>
			</div>
		</div>

		<h4>Attendees</h4>
		<div class="row">
			<div class="col-xs-6">
				<dl>
					<dt>Prominant Attendees:</dt>
						<dd><?= $details['ProminantAttendees'] ?></dd>
				</dl>
			</div>
			<div class="col-xs-6">
			<dl>
				<dt>Open to Off-Campus or Public:</dt>
					<dd><?= $details['IsPublic'] == 1 ? 'Yes' : 'No' ?></dd>
			</dl>
			</div>
		</div>

		<h4>Status: <?= $details['Status'] ?></h4>
		<h4>Category: <?= $details['ApplicationTypeName'] ?></h4>
		<h4>Submitted <?= $details['EventStartDate'] ?></h4>
	</div> <!-- end info-box -->
	</div>
	</div>

	<!-- Applicant details -->
	<div class="row">
	<div class="col-md-8">
	<h3>Applicant</h3>
	<div class="info-box">
		<h4><?= "{$users['Applicant'][0]['UserFname']} {$users['Applicant'][0]['UserLname']}" ?></h4>
		<div class="row">
			<div class="col-xs-6">
				<p><?= $users['Applicant'][0]['UserEmail'] ?></p>
				<p><?= $users['Applicant'][0]['UserPhone'] ?></p>
			</div>
			<div class="col-xs-6">
				<p>UW Mailbox: <?= $users['Applicant'][0]['UWBox'] ?></p>
			</div>
		</div>

		<h4>Organization</h4>
		<div class="row">
			<div class="col-xs-6">
				<p><?= $users['Applicant'][0]['AffiliationName'] ?></p>
				<p><?= $users['Applicant'][0]['Street'] ?></p>
				<p><?= $users['Applicant'][0]['City'] ?>, <?= $users['Applicant'][0]['StateProvince'] ?> <?= $users['Applicant'][0]['Zip'] ?></p>
			</div>
		</div>

		<h4>Affiliation: <?= $users['Applicant'][0]['AffiliationTypeName'] ?></h4>
		<h4>View Key: <?= $details['ViewKey'] ?></h4>
	</div> <!-- end info-box -->
	</div>
	</div> <!-- end row -->
	</div><!--  end expand/collapse -->
</div>

<div id="locations">
	<h2><span class="glyphicon glyphicon-expand"></span>Locations</h2>

	<div class="collapse">
	<!-- Venues -->
	<h3>Event Venues</h3>

	<?php foreach($venues as $venue) { ?>

	<div class="row">
	<div class="col-md-8">
	<div class="info-box">
		<h4><?= $venue['RoomAbbr'] ?></h4>
		<p><?= $venue['EventStartDate'] . ' - ' .  $venue['EventEndDate']?></p>
		<p><?= 'Operated by: ' . $venue['UserFname'] . ' ' . $venue['UserLname'] ?></p>

		<p>You are the Venue Operator for this venue. Choose whether to approve or deny this event at this location:</p>
		<button class="btn btn-success">Approve</button>
		<button class="btn btn-danger">Deny</button>

		<h4>Status: <?= $venue['Descision'] ?></h4>
		<h4>Category: <?= $details['ApplicationTypeName'] ?></h4>
		<h4>Approval Process Started <?= $venue['ApprovalStartDate'] ?></h4>
	</div> <!-- end info-box -->
	</div>
	</div>

	<?php } ?>

	</div> <!-- end collapse/expand -->
</div>

<div id="sponsor">
	<h2><span class="glyphicon glyphicon-expand"></span>Sponsorship</h2>

	<div class="collapse">
	<div class="row">
	<div class="col-md-8">
	<div class="info-box">
		<h4><?= $users['Sponsor'][0]['UserFname'] . ' ' . $users['Sponsor'][0]['UserLname'] ?></h4>
		<p><?= $users['Sponsor'][0]['UserEmail'] ?></p>
		<p><?= $approvals['sponsor'][0]['Descision'] ?></p>

		<h4>Status: <?= $approvals['sponsor'][0]['Descision'] ?></h4>
		<h4>Category: <?= $details['ApplicationTypeName'] ?></h4>
		<h4>Approval Process Started <?= $approvals['sponsor'][0]['ApprovalStartDate'] ?></h4>
	</div> <!-- end info-box -->
	</div>
	</div>
	</div> <!-- end collapse/expand -->
</div>

<div id="committee">
	<h2><span class="glyphicon glyphicon-expand"></span>Committee</h2>

	<div class="collapse">
	<div class="row">
	<div class="col-md-8">
	<div class="info-box">
		<!-- TODO: figure out whether or not to create all approvals when an app is submitted -->
		<?php $committee = sizeof($approvals['committee']) != 0 ? true : false; ?>
		<h4><?= $users['Committee'][0]['UserFname'] . ' ' . $users['Committee'][0]['UserLname'] ?></h4>
		<p><?= $users['Committee'][0]['UserEmail'] ?></p>
		<p><?= $committee ? $approvals['committee'][0]['Descision'] : '' ?></p>

		<h4>Status: <?= $committee ? $approvals['committee'][0]['Descision'] : '' ?></h4>
		<h4>Category: <?= $details['ApplicationTypeName'] ?></h4>
		<h4>Approval Process Started <?= $committee ? $approvals['committee'][0]['ApprovalStartDate'] : '' ?></h4>
	</div> <!-- end info-box -->
	</div>
	</div>
	</div> <!-- end expand/collapse -->
</div>

<?php if($this->authorize->hasRole('Admin')) { ?>
<div id="admin">
	<h2><span class="glyphicon glyphicon-expand"></span>Admin Functions</h2>

	<div class="collapse">

	<div>
	<h3><span class="glyphicon glyphicon-expand"></span>Email Records</h3>
	<div class="collapse">
	<div class="row">
	<div class="col-md-8">
		<table class="table table-default">
			<thead>
			</thead>
			<tbody>

				<?php foreach($emails as $email) { ?>
				<tr>
					<td><?= $email['EmailTemplateName'] ?></td>
					<td><?= $email['UserEmail'] ?></td>
					<td><?= $email['EmailRecordDate'] ?></td>
					<td><button class="btn btn-success btn-xs">Resend</button></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	</div>
	</div> <!-- end collapse/expand -->
	</div>

	<div>
	<h3><span class="glyphicon glyphicon-expand"></span>Send Reminder Email</h3>
	<div class="collapse">
	<div class="row">
	<div class="col-md-8">
		<label for="addressee">Addressee<input id="addressee" class="form-control" type="text" /></label>
		<label for="template">Template<input id="template" class="form-control" type="text" /></label>
		<button class="btn btn-success btn-sm">Send</button>
	</div>
	</div>
	</div> <!-- end collapse/expand -->
	</div>

	<div>
	<h3><span class="glyphicon glyphicon-expand"></span>Notes</h3>
	<div class="collapse">
	<div class="row">
	<div class="col-md-8">
		<hr>

		<?php foreach($notes as $note) { ?>
		<div>
			<p><?= $note['NoteDate'] ?></p>
			<p><?= $note['NoteText'] ?></p>
			<hr>
		</div>
		<?php } ?>


			<div style="vertical-align: bottom; display: inline-block; width: 88%">
			<textarea class="form-control" rows="8" maxlength="500" placeholder="New Note..."></textarea>
			</div>
			<div style="vertical-align: bottom; display: inline-block; width: 10%; margin-left: 5px;">
			<button class="btn btn-success btn-sm">Save</button>
			</div>
			

	</div>
	</div>
	</div> <!-- end collapse/expand -->
	</div>

	<div>
	<h3><span class="glyphicon glyphicon-expand"></span>Expire Application</h3>
	<div class="collapse">
	<div class="row">
	<div class="col-md-8">
		<div style="vertical-align: bottom; display: inline-block; width: 88%">
		<textarea class="form-control" rows="8" maxlength="500" placeholder="Reason for expiriation..."></textarea>
		</div>
		<div style="vertical-align: bottom; display: inline-block; width: 10%; margin-left: 5px;">
		<button class="btn btn-success btn-sm">Expire</button>
		</div>
	</div>
	</div>
	</div> <!-- end collapse/expand -->
	</div>

	<div>
	<h3><span class="glyphicon glyphicon-expand"></span>Inactivate Application</h3>
	<div class="collapse">
	<div class="row">
	<div class="col-md-8">
		<div style="vertical-align: bottom; display: inline-block; width: 88%">
		<textarea class="form-control" rows="8" maxlength="500" placeholder="Reason for inactivation..."></textarea>
		</div>
		<div style="vertical-align: bottom; display: inline-block; width: 10%; margin-left: 5px;">
		<button class="btn btn-success btn-sm">Inactivate</button>
		</div>
	</div>
	</div>
	</div> <!-- end collapse/expand -->
	</div>

	</div> <!-- end collapse/expand -->
</div>
<?php } ?>

<pre>
<?= print_r($details); ?>
</pre>
<pre>
<?= print_r($users); ?>
</pre>
<pre>
<?= print_r($approvals); ?>
</pre>

<?php
# TODO: maybe move these to the controller, you lazy git...

# If the Event Desc is greater than 250 characters, split it so we can make two
# Columns.
# 	$desc string The Event description to potentially split in half
function splitDesc($desc) {
	if(strlen($desc) > 250) {
		$chunks[] = substr($desc, 0, strlen($desc) / 2);
		$chunks[] = substr($desc, strlen($desc) / 2 + 1);
	} else {
		$chunks[] = $desc;
	}
	return $chunks;
}

function findRole($netid) {

}
?>