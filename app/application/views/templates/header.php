        <script type="text/javascript">
		$(document).ready(function(){
			var containerW = 0;
			//width for header elements
			setInterval(function(){
				if($("#action-flow").width() != containerW){

					containerW = $("#action-flow").width();
					var linkW = Math.floor(containerW / 4) - 80.25;
					var linkH = $("#action-flow a").height();
					$("#action-flow a").width(linkW);
					
					$("#arrows").empty();
					$.each($("#action-flow .has-arrow"), function(i, ele){
						var left = linkW + 75 +(i*(linkW+80));
						var top = -1;
						
						$("<div>").addClass("arrow").offset({top: top, left: left }).appendTo($("#arrows")).css('position', 'absolute');
					});
					
					
					
					var sidebarW = $("#sidebar").width();
					//.5 subtracted for correct scaling in some positions
					$("#admin-mode a").width(Math.floor(sidebarW/2) - 40.5);
				}
				
			}, 50);
			
			
			$("#action-flow a.disabled").click(function(e){
				e.preventDefault();
			});
			
			
			$("a.back-link").click(function(e){
				e.preventDefault();
				window.history.back();
			});
			
		});
		</script>
    	<div id="main-wrapper" class="row">
        	<header>
            	<div class="row" id="admin-banner"><h1><?=$mode?> Application Administration</h1></div>
                <div class="row" id="actions-container">
                	<div class="col-lg-9">
                    	<div class="row" id="action-flow">
                            <ul>
                                <li class="has-arrow">
        							<a href=<?= '"' . base_url('Applications/index/sponsor') . '" '?> class="gold-box-link active " style="width: 211.75px;">Sponsor</a>
    							</li>
                                <li class="has-arrow">
							        <a href=<?= '"' . base_url('Applications/index/venue') . '" '?>  class="gold-box-link  " style="width: 211.75px;">Venue</a>
							    </li>
                                <li class="has-arrow">
							        <a href=<?= '"' . base_url('Applications/index/committee') . '" '?>  class="gold-box-link  " style="width: 211.75px;">Committee</a>
							    </li>
                                <li class="">
							        <a href=<?= '"' . base_url('Applications/index/completed') . '" '?> class="gold-box-link  " style="width: 211.75px;">Completed</a>
							    </li>
                            </ul>
                            <div id="arrows">
                            
                            </div>
                    	</div>

                    </div> <!-- end col -->
                   <div class="col-lg-3" id="admin-mode">

                        <div>
                        	<a href="<?php echo base_url(); ?>admin/changeMode/uuf" class="<?php echo ($this->session->userdata('admin_mode') == "uuf") ? "active" : ""?>">UUF</a>
                        	<a href="<?php echo base_url(); ?>admin/changeMode/alc" class="<?php echo ($this->session->userdata('admin_mode') == "alc") ? "active" : ""?>">Alcohol</a>
                    	</div>
                    </div>
                </div><!--  end row -->
            </header>
        </div><!--  end #main-wrapper -->
         <!-- end header view -->



		