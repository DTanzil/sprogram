

    	<!-- <div class="col-lg-1"></div> -->
        <div id="sidebar" class="col-lg-11">
            <div id="user-info">
                <h3><?= $UserFName . ' ' . $UserLName ?></h3>
                <p><?= $UserTitle ?></p>
                <dl>
                    <dt>Contact Information</dt>
                    <dd style="margin-bottom:0px"><?= $UserEmail ?></dd>
                    <dd><?= $UserPhone ?></dd>
                </dl>
                <dl class="links">
                	<dd><a href="<?php echo base_url() ?>manage_admins/edit/<?= $UserID ?>">Update Contact Information</a></dd>
                    <dd><a href="https://depts.washington.edu/sprogram/admin/admin/logout">Logout</a></dd>
                </dl>
          	</div>
            <ul id="widgets">
                <?php
                $controllers = array(
                    // array("text"=>"Search", 
                    //       "path"=>'Admin_test/search'),
                    array("text"=>"Manage Locations", 
                          "path"=>'Manage_locations'),
                    // array("text"=>"Recent Sublocations", 
                    //       "path"=>'Manage_locations/recent_sublocations'),
                    array("text"=>"Manage Admins", 
                          "path"=>'Manage_admins'),
                    // array("text"=>"View Email History", 
                    //       "path"=>'Email/history'),
                    // array("text"=>"Email Templates", 
                    //       "path"=>'Email/templates'),
                    // array("text"=>"Email Actions", 
                    //       "path"=>'Email/actions'),
                    // array("text"=>"Edit Application", 
                    //       "path"=>'Manage_applications/edit'),
                    // array("text"=>"Contact Support", 
                    //       "path"=>'Email/contact_support'),
                    // array("text"=>"Reporting", 
                    //       "path"=>'Reporting')
                );
                foreach($controllers as $controller) {
                ?>
                    <li><a class="gold-box-link" href="<?= base_url() . $controller['path'] ?>"><?= $controller['text'] ?></a></li>
                <?php
                }
                ?>
            </ul>
        </div>
 <!-- End Sidebar -->
