<?php $sortMethod = '[[3, 0]]'; ?>

<div class="row" style="margin-bottom:10px;">
    <div class="container">
        <a class="btn btn-success" href=<?='"' . base_url() . 'Manage_locations/create' . '"'?>>New Location</a>
    </div>
</div>

<div id="table-ctrl" class="row">
<div class="col-lg-3">
<input class="form-control" id='search' type="text" placeholder="Find in table"/>
</div>
</div>
<img class="loading" src="assets/images/ajax-loader.gif"/>
<table class="paginated tablesorter" style="visibility: hidden;" >
	<thead>
		<tr>
        	<th>Edit</th>
            <th>Room Name (short)</th>
            <th>Room Name (long)</th>
            <th class="sort-by-first">Building</th>
            <th>Approved?</th>
            <th colspan="2">Operators</th>
            <th>Map</th>
            <th>Delete</th>
        </tr>
	</thead>
    <tbody>
<?php foreach($locations as $location): ?>
    <tr>
    	<td>
        	<a class="btn btn-sm btn-primary" href=<?= '"' . base_url("Manage_locations/edit/{$location['RoomID']}") . '"' ?> >Edit</a>
        </td>
        <td>
            <?= $location['RoomAbbr'] ?> 
        </td>
        <td>
            <?= $location['RoomName'] ?> 
        </td>
        <td>
            <?= $location['BuildingAbbr'] ?> 
        </td>
        <td>
            <?= $location['IsApproved'] ?> 
        </td>
        <td colspan="2">
            <?= $location['Operators'] ?>
        </td>
        <td>
            <a target="_blank" href=<?= "'" . $location['MapURL'] . "'" ?>>Map Link</a> 
        </td>
        <td>
            <button class="btn btn-sm btn-danger delete" value=<?="{$location['RoomID']}"?>>Delete</button>
        </td>

    </tr>
<?php endforeach ?>

</tbody>
</table>