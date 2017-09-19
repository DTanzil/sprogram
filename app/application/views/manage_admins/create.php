<?php echo validation_errors(); ?>

 	<?php echo form_open('Manage_admins/create'); ?>
 		<div class="row">

 		<h2>Contact Information</h2>
 		<div class="col-lg-6">
	 		<label for="fname">First Name</label>
			<input class="form-control"id="fname" type="text" name="UserFname" required/>
		</div>
 		<div class="col-lg-6">
	 		<label for="lname">Last Name</label>
			<input class="form-control" id="lname" type="text" name="UserLname" required/>
		</div>
		<div class="col-lg-4">
			<label for="netid">UW NetID</label>
			<input class="form-control" id="netid" type="text" name="NetID" required/>
		</div>
		<div class="col-lg-4">
			<label for="email">Email</label>
			<input class="form-control" id="email" type="email" name="UserEmail" required/>
 		</div>
 		<div class="col-lg-4">
			<label for="phone">Phone</label>
			<input class="form-control" id="phone" type="tel" name="UserPhone" />
		</div>
 		</div>
		
 		<h2>Affiliation Information</h2>
 		<div class="row">
	 		<div class="col-lg-6">
	 			<label for="affiliation">Dept/Org Name</label>
				<input class="form-control" id="affiliation" type="text" name="AffiliationName" required/>
	 		</div>
	 		<div class="col-lg-6">
	 			<label for="title">Admin's Title</label>
				<input class="form-control" id="title" type="text" name="UserTitle" required/>
	 		</div>
	 		<div class="col-lg-3">

	 			<label for="mailbox">UW Mailbox # (if applicable)</label>
				<input class="form-control" id="mailbox" type="number" name="UWBox" />

	 		</div>
 		</div>
 		<h2>Privileges</h2>
 		<p>Check all that apply</p>
 		<div class="row">
 		<div class="col-lg-6">
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" value="Admin">Admin</label>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" value="VenueOperator">Venue Operator</label>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" value="Sponsor">Sponsor</label>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" value="Committee">Committee</label>
	 		<label class="checkbox-inline"><input type="checkbox" name="privileges[]" value="Viewer">Viewer</label>
 		</div>
 		</div>

 		<div class="row">
 		<div class="col-lg-6">
 			<p>Admin Status</p>	
 			<label class="radio-inline"><input type="radio" name="AdminIsActive" value="1" required/> Approved</label>
 			<label class="radio-inline"><input type="radio" name="AdminIsActive" value="0" /> Disapproved</label>
 		</div>
 		</div>

	 		<div class="input-group">
	 		<input class="btn btn-default" type="reset" name="" value="Reset">
			<input class="btn btn-success" type="submit" name="submit" value="Submit">
			</div>

	</form>


<?php

?>