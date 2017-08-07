<?php

	
function buildTable($data, $viewType){
	
	switch($viewType){
		case "sponsor":
			$viewVal = 0;
			break;
		case "venueOperator":
			$viewVal = 1;
			break;
		case "committee":
			$viewVal = 2;
			break;
		default:
			$viewVal = 3;
			break;
	}
	
		if($data && is_array($data)): ?>
    <table data-viewtype="<?= $viewType ?>" class="tablesorter">
    	<thead>
    		<tr>
            	<th>View</th>
                <th>Submit Date</th>
                <th>Event Date</th>
                <th>Event Name</th>
                
                <th>Event Location</th>
                <th>Submitted By</th>
                <th>Org/Dept Name</th>
                <?php if($viewVal > 0): ?>
                <th>Sponsor</th>
                <?php endif; if($viewVal > 1): ?>
                <th>Venue</th>
                <?php endif; ?>
                <?php if($viewVal > 2): ?>
                <th>Committee</th>
                <?php endif; ?>
            </tr>
		</thead>
        <tbody>
	<?php foreach($data as $row): ?>
        <tr>
        	<td>
            	<a class="button success small" href="<?php echo base_url() ?>application_details/details/<?php echo $row['idApplication'].'/'.$viewType ?>">View</a>  
            </td>     
            <td>
            	<?= $row["submitDate"] ?>
            </td>
            <td>
            	<?= $row["eventDate"] ?>
            </td>
            
            <td>
            	<?= $row["eventName"] ?>
            </td>
            <td>
            	<?= $row["eventLocation"] ?>
            </td>
            <td>
            	<a href="mailto:<?= $row["email"] ?>"><?= $row["applicantName"] ?></a>
            </td>
            <td>
            	<?= $row["org_name"] ?>
            </td>
            <?php if($viewVal > 0): ?>
				<?php 
                //TODO: find artifact causing this error
                //sponsorName should == 'No Sponsor'
                if($row['sponsorName'] == "Sponsor Not Required"): ?>
                <td>N/A</td>
                <?php else: ?>
                <td>
                    <a href="mailto:<?= $row["sponsorEmail"]?>"><?= $row["sponsorName"] ?></a>
                    <br/>
                    <?= $row["sponsorAppr"] ?>
                   
                </td>
               
                <?php endif; ?>
            
            <?php endif; if($viewVal > 1):?>
            <td>
            	<?= $row["venueAppr"] ?>
            </td>
            <?php endif; if($viewVal > 2): ?>
            <td>
            	<?= ucfirst($row['status']) ?>
                <br/>
            	<?= $row["committeeAppr"] ?>
                
            </td>
            <?php endif; ?>
            
        </tr>

        <?php endforeach; ?>
        
    	</tbody>
    </table>
    
	<?php elseif ($data): ?>
    	<p class="alert-box alert"><?= $data ?></p>
    
	<?php else: ?>
    <p>
   		<em>
    		No additional requests require your approval at this time. You may log in and check the Admin Dashboard at any time. If you think this is an error, please <a href="<?= base_url() ?>contact_support/">contact support</a>.
    	</em>
    </p>
    <?php
	
	endif;
    if ($viewType == 'search'):?>
    <p>Looking for older results? <a href="<?= base_url() ?>search_archive">Click here</a> to search the archive. </p>
    <?php endif;
	
}
$this->load->view('initializeTable');

if($events && is_array($events)) : ?>
<?php $this->load->view('paginationControls') ?>
<?php endif; ?>
<?php buildTable($events, $viewType); ?>	


