<h2>Results From Query</h2>
<ul>
	<?php
	foreach($results as $result) {
		//var_dump($result);
		echo "<li><ul>";
		foreach($result as $key => $value) {
			echo "<li>{$key} : {$value}</li>";
		}
		//echo "<li>{$result->CityName}</li>";
		echo "</ul></li>";
	}
	?>
</ul>