<div class='row'>
	<h2>Reports</h2>
	<p><b>NOTE: Running reports against the database is very resource intensive. Users may not be able to submit or approve applications for several seconds while the report runs. Please excercise restraint and do not run reports more than is necessary.</b></p>

	<?php $att = array('class' => 'form-inline'); echo form_open('reporting/runOpenRSOAppReport', $att); ?>
		<p>Open Applications Submitted by RSOs From Date</p>
		<div class="row">
			<div class="small-3 columns">
				<div class="input-group">
					<label for="RSO-from">From date</label>
					<input class="form-control" type="date" name="RSO-from" id="RSO-from">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="small-3 columns">
				<input class="form-control button success small" type="submit" value="Run">
			</div>
		</div>
	</form>

	<?php $att = array('class' => 'form-inline'); echo form_open('reporting/runAppStatsReport', $att); ?>
		<p>Submitted Request Statistics, UUF and ASR, by Fiscal Year (7/1/yy - 6/30/yy+1)</p>

		<div class="row">
			<div class="small-3 columns">
				<label for="from">From Fiscal Year</label>
				<input size="4" maxlength="4" id="from" name="from" type="number" placeholder="yyyy">
			</div>
			<div class="small-3 columns">
				<label for="to">To Fiscal Year</label>
				<input size=4 maxlength="4" id="to" name="to" type="number" placeholder="yyyy">
			</div>
			<div class="small-3 columns"></div>
		</div>
		<div class="row">
			<div class="small-3 columns">
				<input class="button success small" type="submit" value="Run">
			</div>
		</div>
	</form>
</div>