	<?php echo validation_errors(); ?>

	<?php echo form_open('databasetestcont/query'); ?>

		<label for="select">SELECT</label>
		<input type="input" name="select" />

		<label for="from">FROM</label>
		<input type="input" name="from" />

		<input type="submit" name="submit" value="Run Query" />
	</form>