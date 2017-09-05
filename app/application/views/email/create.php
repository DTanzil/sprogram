<?php
	echo form_open("Email/create"); ?>
			<h2>Template Triggers and Recipients</h2>
	 		<div class="row">
	 			<label for="action">Action</label>
	 		
	 		<div class="col-md-12">
		 		
				<select name="action" required>
					<?php foreach($actions as $action) { ?>
						<option value=<?='"'. $action['ActionID'] .'"'?>><?= $action['ActionName'] ?></option>
					<?php } ?>
				</select>
			</div>
			</div>
			<div class="row">
			<label>Recipients</label>
			<div class="col-md-12">
				<div class="col-xs-3">
					<label>Sponsors</label>
					<div class="radio">
					  <label><input type="radio" name="sponsor-rec">Sponsor</label>
					</div>
				</div>
				<div class="col-xs-3">
					<label>Venue Operators</label>
					<div class="radio">
					  <label><input type="radio" name="venue-rec">All Venue Operators</label>
					</div>
					<div class="radio">
					  <label><input type="radio" name="venue-rec">Operators for a single venue</label>
					</div>
				</div>
				<div class="col-xs-3">
					<label>Committee</label>
					<div class="radio">
					  <label><input type="radio" name="committee-rec">Committee</label>
					</div>
				</div>
				<div class="col-xs-3">
					<label>Applicant</label>
					<div class="radio">
					  <label><input type="radio" name="applicant-rec">Applicant</label>
					</div>
				</div>
			</div>
	 		
	 		</div>
					<div class="row">
					<label>CC</label>
					<div class="col-md-12">
						<div class="col-xs-3">
							<label>Sponsors</label>
							<div class="radio">
							  <label><input type="radio" name="sponsor-cc">Sponsor</label>
							</div>
						</div>
						<div class="col-xs-3">
							<label>Venue Operators</label>
							<div class="radio">
							  <label><input type="radio" name="venue-cc">All Venue Operators</label>
							</div>
							<div class="radio">
							  <label><input type="radio" name="venue-cc">Operators for a single venue</label>
							</div>
						</div>
						<div class="col-xs-3">
							<label>Committee</label>
							<div class="radio">
							  <label><input type="radio" name="committee-cc">Committee</label>
							</div>
						</div>
						<div class="col-xs-3">
							<label>Applicant</label>
							<div class="radio">
							  <label><input type="radio" name="applicant-cc">Applicant</label>
							</div>
						</div>
					</div>
			 		
			 		</div>
	 		<h2>Template Content</h2>
	 		<div class="row">
		 		<div class="col-md-12">
		 			<label for="email-subject">Email Subject</label>
		 			<input class="form-control" id="email-subject" type="text" name="email-subject" />
		 		</div>
	 		</div>
	 		<div class="row">

	 			<div class="col-md-12">
	 			<label for="email-subject">Email Body</label>
		 			<div data-editable data-name="main-content" style="width: 100%; border: 1px solid lightgrey; height: 400px; margin-top: 10px;">
		 			<h1>Click the pencil icon in the top right corner to edit me</h1>

		 			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
		 			tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
		 			quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
		 			consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
		 			cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
		 			proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		 			</div>
	 			</div>
	 		</div>


	 		<div class="input-group">
		 		<input class="btn btn-default" type="reset" name="" value="Reset">
				<input class="btn btn-success" type="submit" name="submit" value="Submit">
			</div>

		</form>