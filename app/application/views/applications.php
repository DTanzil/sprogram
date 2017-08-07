<div class="col-lg-9">

<div id="table-ctrl" class="row">
<div class="col-lg-3">
<input class="form-control" id='search' type="text" placeholder="Find in table"/>
</div>
</div>
<table class="paginated tablesorter">
	<thead>
		<tr>
            <?php if($viewVal > 2) { ?><th>Flag</th><?php } ?>
        	<th>View</th>
            <th>Submit Date</th>
            <th>Event Date</th>
            <th>Event Name</th>
            
            <th>Event Location</th>
            <th>Submitted By</th>
            <th>Org/Dept Name</th>
            <!-- <?php if($viewVal > 0): ?> -->
            <!-- <th>Sponsor</th> -->
            <!-- <?php endif; if($viewVal > 1): ?> -->
            <!-- <th>Venue</th> -->
            <!-- <?php endif; ?> -->
            <!-- <?php if($viewVal > 2): ?> -->
            <!-- <th>Committee</th> -->
            <!-- <?php endif; ?> -->
        </tr>
	</thead>
    <tbody>
<?php foreach($apps as $app): ?>
    <tr>
        <?php 
        if($viewVal > 2) { 

            ?><td id=<?= $app['ApplicationID'] ?> class="flag">
                <?php if($app['has_flag'] == '1') { ?> <div class="flagged"></div> <?php } ?>
            </td>
        <?php } ?>
    	<td>
        	<a class="button success small" href="<?php echo base_url() ?>application_details/details/">View</a>  
        </td>
        <td>
            <?= $app['DateApplied'] ?> 
        </td>
        <td>
            <?= 'oops' ?> 
        </td>
        <td>
            <?= $app['EventName'] ?> 
        </td>
        <td>
            <?= 'somewhere' ?> 
        </td>
        <td>
            <?= 'someone' ?> 
        </td>
        <td>
            <?= 'org' ?> 
        </td>

    </tr>
<?php endforeach ?>

</tbody>
</table>
</div>