<div class="page-header">
	<h1>Dashboard</h1>

</div>

<div id="applications">
	<h2>Open Applications</h2>
	<small>These applications require your approval in one or more places</small>

	<div class="container">
	<?php foreach($openApps as $app) { ?>
		<a href=<?= '"' . site_url('/applications/view/' . $app['ApplicationID']) . '"' ?>>
		<div class="item row">
				<div class="col-xs-11">
				<h3><?= $app['EventName'] ?></h3>
				<p>Submitted <?= $app['DateApplied'] ?></p>
				</div>
				<div class="col-xs-1">
					<span class="glyphicon glyphicon-chevron-right go-button"></span>
				</div>
		</div>
		</a>
	<?php } ?>
	</div>
</div>