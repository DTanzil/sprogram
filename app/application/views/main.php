<!DOCTYPE html>
<html>
<head>
	<title><?= $head['title'] ?></title>
		<script type="text/javascript">
			// Determine the base URL for ajax/api endpoints based on our environment
			
			var domain = window.location.hostname;
			var protocol = window.location.protocol + '//';

			// Detect if we're in the live prod environment, test environment, or dev. Set path accordingly
			var path;
			if(domain.includes('depts.washington.edu')) {
				path = window.location.pathname.includes('admin') ? '/sprogram/admin' : '/sprogram/stage/sprogram/app/';
			} else {
				path = '/sprogram-app/app/';
			}

			window.sprogram = {
				baseUrl : protocol + domain + path
			}

			
		</script>

	<?= loadCommonAssets(); # defined in assests_helper.php ?>
	<?php if(isset($head['pageDependencies'])) { echo $head['pageDependencies']; } ?>
</head>
<body>
<div id="header">
	<?php $this->load->view($header['view'], $header); ?>
</div> <!-- end header -->

		 <?php $this->load->view('templates/sidebar', $sidebar); ?>

	<div id="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-10">
				<div>
					<h1 class="header"><?php if(isset($content['description'])) {echo $content['description'];}?></h1>
				</div>
				<?php $this->load->view($content['view'], $content); ?>
			</div> <!-- end content -->
		</div>
	</div>
	</div>
	<!-- <div id="sidebar-wrapper" class="col-md-3">
		 <?php //$this->load->view('templates/sidebar', $sidebar); ?>
	</div> <!-- end sidebar --> -->
	
</body>
</html>