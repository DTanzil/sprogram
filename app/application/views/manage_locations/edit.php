<?php
	echo form_open("Manage_locations/edit/{$locationInfo['RoomID']}"); ?>
	 		<div class="row">

	 		<h2>Location Information</h2>
	 		<div class="col-lg-6">
		 		<label for="fname">Location Full Name</label>
				<input class="form-control" id="fname" type="text" name="RoomName" value=<?= '"' . $locationInfo['RoomName'] . '"' ?> required />
			</div>
	 		<div class="col-lg-6">
		 		<label for="lname">Location Abbreviation</label>
				<input class="form-control" id="lname" type="text" name="RoomAbbr" value=<?= '"' . $locationInfo['RoomAbbr'] . '"' ?> required />
			</div>
			<div class="col-lg-6">
				<label for="comment">Description</label>
  				<textarea class="form-control" rows="5" maxlength=500 id="comment" value=<?= '"' . $locationInfo['RoomDesc'] . '"' ?> ></textarea>
			</div>
			<div class="col-lg-6">
				<label for="email">Building</label>
				<input list="buildings" class="form-control" id="email" type="text" name="BuildingName" value=<?= '"' . $locationInfo['BuildingName'] . '"' ?> required />
				<datalist id="buildings">
					<?php foreach($buildings as $building) { ?>
						<option val=<?= '"' . $building['BuildingName'] . ' - ' . $building['BuildingAbbr'] . '"' ?> ><?= $building['BuildingName'] . ' - ' . $building['BuildingAbbr'] ?></option>
					<?php } ?>
				</datalist>
	 		</div>
	 		</div>
			
	 		<h2>Admin Information</h2>
	 		<div class="row">
		 		<div class="col-lg-6">
		 			<label>Add or remove Venue Operators attached to this location</label>
			 		<div class="list-group" id="admins">
					  <?php foreach($admins as $admin) { ?>
					  	<button id=<?= "{$admin['UserID']}" ?> type="button" class="list-group-item"><span class="glyphicon glyphicon-minus-sign"></span> <?= "{$admin['UserFname']} {$admin['UserLname']} - {$admin['NetID']}" ?> <input type="hidden" name="admins[]" value=<?= "{$admin['UserID']}" ?> /></button>
					  <?php } ?>
					  <li class="list-group-item">
					  	<span class="glyphicon glyphicon-plus-sign"></span>
				  		<select id=<?= "'" . $locationInfo['RoomID'] . "'"?> class="admin-add" style="width:75%; padding-bottom: 5px;">
				  			<!-- empty option tag for select2 placeholder -->
				  			<option></option>
				  		</select>
					</li>
					</div>
		 		</div>
		 		<div class="col-lg-6">
		 			<label>Location Status</label>		
		 			<label class="radio-inline"><input type="radio" name="IsApproved" value="1" required/> Approved</label>
		 			<label class="radio-inline"><input type="radio" name="IsApproved" value="0" /> Disapproved</label>
		 		</div>
	 		</div>

	 		<div class="input-group">
		 		<input class="btn btn-default" type="reset" name="" value="Reset">
				<input class="btn btn-success" type="submit" name="submit" value="Submit">
			</div>

		</form>