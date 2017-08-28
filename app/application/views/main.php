<!DOCTYPE html>
<html>
<head>
	<title><?= $head['title'] ?></title>


	<?= loadCommonAssets(); # defined in assests_helper.php ?>
	<?php if(isset($head['pageDependencies'])) { echo $head['pageDependencies']; } ?>
</head>
<body>
<div id="header">
	<?php $this->load->view($header['view'], $header); ?>
</div> <!-- end header -->

		 <?php $this->load->view('templates/sidebar', $sidebar); ?>

	<div id="content" class="container">
		<div class="row">
			<div class="col-md-12">
				<div>
					<h1 class="header"><?php if(isset($content['description'])) {echo $content['description'];}?></h1>
				</div>
				<?php $this->load->view($content['view'], $content); ?>
			</div> <!-- end content -->
		</div>
	</div>
	<div id="sidebar-wrapper" class="col-md-3">
		 <?php //$this->load->view('templates/sidebar', $sidebar); ?>
	</div> <!-- end sidebar -->
	
</body>
</html>