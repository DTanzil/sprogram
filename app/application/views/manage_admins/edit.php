<?php
	if(isset($netid)) {
		if(isset($deletedAt) && $deletedAt != null) {
	?>
	<div class="alert alert-danger">
	  <strong>Warning!</strong> Records show an Administrator with netid "<?= $netid ?>" was deleted from the database on
	  <?= $deletedAt ?> . To re-instate this user, confirm the information below and click 'Submit'
	</div>
	<?php } ?>
	<div class="alert alert-warning">
	  <strong>Warning!</strong> An administrator with netid "<?= $netid ?>" already exists. The information you entered
	  in the previous form has been populated here. Please review the information below before clicking 'Submit'
	</div>
	<?php
	}

	echo form_open("Manage_admins/edit/{$adminID}"); ?>
	 		<div class="row">

	 		<h2>Contact Information</h2>
	 		<div class="col-lg-6">
		 		<label for="fname">First Name</label>
				<input class="form-control" id="fname" type="text" name="UserFname" value=<?= '"' . $adminInfo['UserFname'] . '"' ?> required />
			</div>
	 		<div class="col-lg-6">
		 		<label for="lname">Last Name</label>
				<input class="form-control" id="lname" type="text" name="UserLname" value=<?= '"' . $adminInfo['UserLname'] . '"' ?> required />
			</div>
			<div class="col-lg-4">
				<label for="netid">UW NetID</label>
				<input class="form-control" id="netid" type="text" name="NetID" value=<?= '"' . $adminInfo['NetID'] . '"' ?> required disabled />
			</div>
			<div class="col-lg-4">
				<label for="email">Email</label>
				<input class="form-control" id="email" type="email" name="UserEmail" value=<?= '"' . $adminInfo['UserEmail'] . '"' ?> required />
	 		</div>
	 		<div class="col-lg-4">
				<label for="phone">Phone</label>
				<input class="form-control" id="phone" type="tel" name="UserPhone" value=<?= '"' . $adminInfo['UserPhone'] . '"' ?> />
			</div>
	 		</div>
			
	 		<h2>Affiliation Information</h2>
	 		<div class="row">
		 		<div class="col-lg-6">
		 			<label for="affiliation">Dept/Org Name</label>
					<input class="form-control" id="affiliation" type="text" name="AffiliationName" value=<?= '"' . $adminInfo['AffiliationName'] . '"' ?> required/>
		 		</div>
		 		<div class="col-lg-6">
		 			<label for="title">Admin's Title</label>
					<input class="form-control" id="title" type="text" name="UserTitle" value=<?= '"' . $adminInfo['UserTitle'] . '"' ?> required />
		 		</div>
		 		<div class="col-lg-3">

		 			<label for="mailbox">UW Mailbox # (if applicable)</label>
					<input class="form-control" id="mailbox" type="number" name="UWBox" value=<?= '"' . $adminInfo['UWBox'] . '"' ?> />

		 		</div>
	 		</div>
	 		<h2>Privileges and Settings</h2>
	 		<p>Admin Status</p>
	 		<label class="radio-inline"><input type="radio" name="AdminIsActive"
	 		
	 		<?php echo isset($adminInfo['AdminIsActive']) && $adminInfo['AdminIsActive'] == true ? 'checked ' : ''; ?> value="1" required>Active</label>
	 		<label class="radio-inline"><input type="radio" name="AdminIsActive" 
	 		
	 		<?php echo isset($adminInfo['AdminIsActive']) && $adminInfo['AdminIsActive'] == false ? 'checked ' : ''; ?> value="0">Inactive</label>
	 		<p>Check all that apply</p>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" <?php echo in_array('Admin', $adminInfo['privileges']) != false ? 'checked ' : ''; ?> value="Admin">Admin</label>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" <?php echo in_array('VenueOperator', $adminInfo['privileges']) != false ? 'checked ' : ''; ?>value="VenueOperator">Venue Operator</label>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" <?php echo in_array('Sponsor', $adminInfo['privileges']) != false ? 'checked ' : ''; ?> value="Sponsor">Sponsor</label>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" <?php echo in_array('Committee', $adminInfo['privileges']) != false ? 'checked ' : ''; ?> value="Committee">Committee</label>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" <?php echo in_array('Viewer', $adminInfo['privileges']) != false ? 'checked ' : ''; ?> value="Viewer">Viewer</label>


		 		<div class="input-group">
		 		<input class="btn btn-default" type="reset" name="" value="Reset">
				<input class="btn btn-success" type="submit" name="submit" value="Submit">
				</div>

		</form>