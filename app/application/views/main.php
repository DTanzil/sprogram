<!DOCTYPE html>
<html>
<head>
	<title><?= $head['title'] ?></title>


	<?= loadCommonAssets(); # defined in assests_helper.php ?>
	<?php if(isset($head['pageDependencies'])) { echo $head['pageDependencies']; } ?>
</head>
<body>
<div class="container-fluid" id="header">
	<?php $this->load->view($header['view'], $header); ?>
</div> <!-- end header -->
<div class="container-fluid">
<div class="row" style="width:90%; margin-left:auto; margin-right:auto;">
	<div id="content" class="col-lg-9">
		<div>
			<h1 class="header"><?php if(isset($content['description'])) {echo $content['description'];}?></h1>
		</div>
		<?php $this->load->view($content['view'], $content); ?>
	</div> <!-- end content -->
	<div id="sidebar-wrapper" class="col-lg-3">
		<?php $this->load->view('templates/sidebar', $sidebar); ?>
	</div> <!-- end sidebar -->
	
</div> <!-- end of content and sidebar -->
</div> <!-- end of container-fluid -->
</body>
</html>