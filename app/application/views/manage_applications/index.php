<?php $sortMethod = '[[3, 0]]'; ?>

<div class="row" style="margin-bottom:10px;">
    <!-- <div class="container">
        <a class="btn btn-success" href=<?='"' . base_url() . 'Manage_locations/create' . '"'?>>New Location</a>
    </div> -->
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
        	<th>View</th>
            <th class="sort-by-first">Submit Date</th>
            <th>Event Date</th>
            <th>Event Name</th>
            <th>Submitted By</th>
            <th>Org/Dept Name</th>
            <th>Category</th>
            <th>Status</th>
            <th>Map</th>
        </tr>
	</thead>
    <tbody>
<?php foreach($apps as $app): ?>
    <tr>
    	<td>
        	<a class="btn btn-sm btn-success" href=<?= '"' . base_url("Applications/view/{$app['ApplicationID']}") . '"' ?> >View</a>
        </td>
        <td>
            <?= $app['DateApplied'] ?> 
        </td>
        <td>
            <?= $app['EventStartDate'] ?> 
        </td>
        <td>
            <?= $app['EventName'] ?> 
        </td>
        <td></td>
        <td></td>
        <td>
            <?= $app['ApplicationTypeName'] ?> 
        </td>

        <td>
            <?= $app['Status'] ?>
        </td>
        <td>
            <!-- <a target="_blank" href=<?= "'" . $location['MapURL'] . "'" ?>>Map Link</a>  -->
        </td>


    </tr>
<?php endforeach ?>

</tbody>
</table>