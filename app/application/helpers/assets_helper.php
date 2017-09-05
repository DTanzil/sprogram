<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * The Asset Loader has functions for injecting CSS and JS scripts into the HTML head in two ways:
 * 		1.) loading a set of pre-defined assets that are needed on every page
 * 		2.) loading a set of given assets for a specific page
 *
 * Anything in this helper gets loaded via the main.php view
 */

# loads any common assets all pages need to function
function loadCommonAssets() {
	$baseURL = base_url();

	# CSS assets
	$css = array(
		'<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">',
		# '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/css/foundation.min.css">',
		# '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/6.0.0/normalize.css">',
		 "<link rel=\"stylesheet\" href=\"{$baseURL}assets/js/tablesorter/css/theme.default.css\" />",
		 "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$baseURL}assets/css/content-tools/content-tools.min.css\" />",


		# fonts
		'<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700" rel="stylesheet" type="text/css">',

		'<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">',

		'<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />',


		# custom style sheets
		link_tag('assets/css/style.css')

	);

	# JS assets
	$js = array(
		# jquery
		'<script
			  src="https://code.jquery.com/jquery-3.2.1.min.js"
			  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
			  crossorigin="anonymous">
		 </script>',
  		# bootstrap js
  		'<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
  		 </script>',
  		# '<script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.3.1/js/foundation.js"></script>',
  		# jquery UI
  		'<script
			  src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
			  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
			  crossorigin="anonymous">
		 </script>',
  		# tablesorter
  		"<script src=\"{$baseURL}assets/js/tablesorter/js/jquery.tablesorter.min.js\"></script>",

  		'<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>',

  		'<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>',

  		"<script src=\"{$baseURL}assets/js/content-tools/content-tools.js\"></script>",

  		# custom scripts
  		"<script src=\"{$baseURL}assets/js/general.js\"></script>"
	);

	$out = "";
	foreach($js as $script) {
		$out .= $script;
	}
	foreach($css as $link) {
		$out .= $link;
	}
	return $out;
}

# loads any custom scripts and stylesheets a particular page might need
# 	$js : array of paths to js scripts
#	$css : array of paths to css stylesheets
function getPageDependencies($js, $css) {
	$baseURL = base_url();
	$tags = "";
	foreach($js as $path) {
		$tags .= "<script src=\"{$baseURL}assets/js/{$path}\"></script>";
	}
	foreach($css as $path) {
		$tags .= "<link rel=\"stylesheet\" href=\"{$baseURL}assets/css/{$path}\">";
	}
	return $tags;
}

function find($file) {
	return base_url() . "assets/images/$file";
}