<h2>Cities in City</h2>
<ul>
	<?php
	foreach($cities as $city) {
		//var_dump($city);
		echo "<li>{$city->CityName}</li>";
	}
	?>
</ul>