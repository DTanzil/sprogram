(function( $ ) {
	/*
	This is the locations widget. It will display all of the registered locations in an accordion style.
	//TODO utilize settings object
	*/
	
	var methods = {
		init : function(options){
			var settings = $.extend({
				"absUrl" : "/sprogram/admin/",
				"pageType" : "form",
				"activeLoc" : ""
				}, options);
				
			var target = this;
			
			target = $.extend({"settings" : settings}, target);
			
			$(target).addClass("accordion");
			
			methods.loadLocations(target);
			
			return target;
		},
		loadLocations : function(target){
			var $this = this;
			
			$(target).html("");
			showAlert("Loading locations...", target);

			$.ajax({
				type: "GET",
				url: target.settings.absUrl + "location_control/generateLocations/",
				data: {"activeLoc" : target.settings.activeLoc, "pageType": target.settings.pageType},
				dataType : "html",
				success: function(data){
					$(target).html("");
					$(target).html(data);
					
					//setup accordion
					$(target).accordion({
					  obj: "div", 
					  container: false,
					  wrapper: "div", 
					  el: ".h", 
					  head: "h3", 
					  next: "div", 
					  initShow: "div.initShow",
					  showMethod: "slideFadeDown",
					  hideMethod: "slideFadeUp",
					  elToWrap: "sup, img",
					  standardExpansible: true
					});
					
					/******************************************************************
					GENERAL FUNCTIONS
					These functions are available to all instances of the widget
					******************************************************************/
					
					
					
					//Show venueOperator details
					$(target).find("a.operator").click(function(){
						
						$this.adminDialog(target, $(this).attr("id"));
					});
					
					//Add a new parent location
					$(target).find(".add-loc.parent").click(function(){
						var parentEle = $(this).parents(".add-location");
						
						var name = parentEle.find("input[name=name]").val();
						var desc = parentEle.find("textarea").val();
						var abbr = parentEle.find("input[name=abbr]").val();
						
						$.post(target.settings.absUrl + "ajax/", {"type" : "addParentLoc", "name" : name, "abbr" : abbr, "desc" : desc}, function(data){
								target.settings.activeLoc = name;
								$this.loadLocations(target);	
							
						});
					});
					
					//Add a new sub location
					$(target).find(".add-loc.sub").click(function(){

						var parentEle = $(this).parents(".add-location");
						var name = parentEle.find("input[name=name]").val();
						var desc = parentEle.find("textarea").val();
						var abbr = parentEle.find("input[name=abbr]").val();
						var idLocation = $(this).closest(".new").siblings(".details").data("locid");
						var admin = parentEle.find("select[name=admin]").val();

																		
						$.post(target.settings.absUrl +"ajax/", {"type" : "addSubLoc", "name" : name, "abbr" : abbr, "desc" : desc, "idLocation" : idLocation, "idAdmin" : admin, "isApproved": 1}, function(data){
							target.settings.activeLoc = name;
							$this.loadLocations(target);
							
						});
					}); //end add loc
					
					/******************************************************************
					PAGE TYPE = "form"
					These functions will be loaded when this widget is used for forms
					******************************************************************/
					
					if(target.settings.pageType == "form"){
						
						//Date From
						$(target).find("input[name=dateFrom]").datetimepicker({
							"changeMonth" : true,
							"changeYear" : true,
							autosize: true,
							dateFormat: "yy-mm-dd",
							timeFormat: "hh:mm:ss",
				    		dateFormat: "m/d/yy",
				            timeFormat: "h:mm TT",
							altFormat: "Y-m-d",
							altTimeFormat: "h:i:s",
							minDate: new Date(),
							onSelect: function(dateText, dp_inst){
								properDateControl(this);
							}
						});
						
						//Date to
						$(target).find("input[name=dateTo]").datetimepicker({
							  "changeMonth" : true,
							  "changeYear" : true,
							  autosize: true,
							  dateFormat: "yy-mm-dd",
							  timeFormat: "hh:mm:ss",
							  dateFormat: "m/d/yy",
				              timeFormat: "h:mm TT",
				              altFormat: "Y-m-d",
							  altTimeFormat: "h:i:s",
							  minDate: new Date(),
							  onSelect: function(dateText, dp_inst){
								properDateControl(this);
							  }
						});
						$(target).find("input[name=attendeesQuests]").change(function(){updateTotal(this);});
						$(target).find("input[name=attendeesMembers]").change(function(){updateTotal(this);});
						$(target).find("input[name=attendeesUnder21]").change(function(){updateTotal(this);});
						function updateTotal(obj, value){
							var total = $(obj).closest("dl").find("input[name=attendeesTotal]");
							var newValue = 
								(parseInt($(obj).closest("dl").find("input[name=attendeesQuests]").attr("value")))
								+ (parseInt($(obj).closest("dl").find("input[name=attendeesMembers]").attr("value")))
								+ (parseInt($(obj).closest("dl").find("input[name=attendeesUnder21]").attr("value")));
							total.attr("value", newValue);
						}
						//Makes sure that dateTo is not before dateFrom
						function properDateControl(obj){
							//Get date fields
							var dateFrom = $(obj).closest("dl").find("input[name=dateFrom]");
							var dateTo = $(obj).closest("dl").find("input[name=dateTo]");
														
							var date2 = new Date(dateTo.datetimepicker("getDate"));
							var date1 = new Date(dateFrom.datetimepicker("getDate"));
							
							if(dateFrom.val() && (date2.getTime() < date1.getTime()) ){
								dateTo.datetimepicker('setDate', date1);
							}
						}
						
						//Add this location to application
						$(target).find(".add-loc-app").click(function(){
							
							var dateFrom = $(this).closest("dl").find("input[name=dateFrom]");
							var dateTo = $(this).closest("dl").find("input[name=dateTo]");
							var attendeesQuests = $(this).closest("dl").find("input[name=attendeesQuests]");
							var attendeesMembers = $(this).closest("dl").find("input[name=attendeesMembers]");
							var attendeesTotal = $(this).closest("dl").find("input[name=attendeesTotal]");
							
							//TODO more elegant error thing
							//Check dates
							if(!dateTo.val() || !dateFrom.val() || !attendeesTotal.val()){
								alert("You must provide all the required data for your event before adding it to your application");
							}
							else{								
								var head = $(this).closest(".outer").siblings(".h");
								if(head.hasClass("selected")){
									head.removeClass("selected")	
								}
								else{
									head.addClass("selected");	
								}
							}
						}); //End add to application
					}//end pageType = "form"
					
					/******************************************************************
					PAGE TYPE = "admin"
					These functions will be loaded when this widget is used for administration
					******************************************************************/
					else if(target.settings.pageType == "admin"){
						
						bindAdminButtons();					
						function bindAdminButtons(){
							//Editing Location Details
							$(target).find(".edit-loc-start").click(function(){
								editDetails(this);
							});
							
							$(target).find(".delete-loc").click(function(){
								deleteLoc(this);
							});
							
							$(target).find(".edit-operators").click(function(){
								editOperators(this);
							});
								
						}
						
						function editDetails(obj){
							var btn = $(obj);
							var details = btn.closest(".details");

							var tbl = btn.closest(".controls").find(".edit-location").show();
							
							tbl.remove();
							var oldDetails = details.clone();
							details.html("").append(tbl);
							
							//Push the edit to the database
							$(target).find(".edit-loc-submit").click(function(){
								var box = $(this).closest(".details");
								
								var name = box.find("input[name=name]").val();
								var desc = box.find("textarea").val();
								var abbr = box.find('input[name=abbr]').val();
								var id = box.find("input[name=id]").val();
								
								var type = ($(this).hasClass("parent")) ? "updateParentLoc" : "updateSubLoc";	
								
								
								$.post(target.settings.absUrl +"ajax/", {"type" : type, "name" : name, "desc" : desc, "id" : id, 'abbr' : abbr}, function(data){
									if(data == "OK"){
										target.settings.activeLoc = name;
										$this.loadLocations(target);	
									}
									else{
										console.log("edit location failed");	
									}
								});
							});	//End edit-submit fn
							
							//Cancel edit action
							$(target).find(".edit-location .cancel").click(function(){
								details.replaceWith(oldDetails);
								details = oldDetails;
																
								details.find(".controls").append(tbl.hide());

								bindAdminButtons();
							});	
						} // End edit location details fn
						
						//Delete this location
						function deleteLoc(obj){
							var id = $(obj).parents(".controls").find("input[name=id]").val();
			
							var del = window.confirm("Are you sure you wish to delete this location? This action cannot be undone. (Note - all children of this location will also be deleted)");
							
							if(del){
								
								var type = ($(obj).hasClass("parent")) ? "deleteParentLoc" : "deleteSubLoc";	
								
								$.post(target.settings.absUrl +"ajax/", {"type" : type, "id" : id}, function(data){
									if(data == "OK"){
										target.settings.activeLoc = "";
										$this.loadLocations(target);	
									}
									else{
										console.log("delete location failed");	
									}
								});
							}
						}// End delete location
						
						
						//Edit operators associated with this application
						function editOperators(obj){
							
							var ops = $(obj).closest(".controls").siblings(".operators");
							if(ops.hasClass("flat")){
								ops.removeClass("flat");
								
								ops.find("li").each(function(i, e){
									if($(e).find("a").length){
										
										//For removing operators
										$("<span>").text("x").addClass("circle-button red").appendTo($(e)).click(function(){
											
											var del = window.confirm("Are you sure you wish to remove this admin from this location?");
											
											if(del){
												var idAdmin = $(this).siblings("a").attr("id");
												var idLoc = $(this).closest(".details").data("locid");
												
												$.post(target.settings.absUrl +"ajax/", {"type" : "removeAdminFromLocation", "idAdmin" : idAdmin, "idLocation" : idLoc}, function(data){
													if(data == "OK"){
														target.settings.activeLoc = idLoc;
														$this.loadLocations(target);
													}
													else{
														alert("Operation failed");
													}
												});
											}
										}); // End remove operator feature
									}
								});
								
								//For adding new operators
								var selectList = $("#operatorList").first().clone();
								var newOpLi = $("<li>").append(selectList).appendTo(ops);
								
								newOpLi.append($("<button>").addClass("button tiny round").text("+").click(function(){
									var idAdmin = $(this).siblings("select").val();
									var idLoc = $(this).closest(".details").data("locid");
									
									$.post(target.settings.absUrl +"ajax/", {"type" : "addAdminToLocation", "idAdmin" : idAdmin, "idLocation" : idLoc}, function(data){
										if(data == "OK"){
											console.log("Operator Added")	
											target.settings.activeLoc = idLoc;
											$this.loadLocations(target);
										}
										else{
											alert(data);
										}
									});
									
								}));
								
							}
							else{
								ops.addClass("flat");
								ops.find("span.circle-button").add("li.add-op").remove();
							}
						}// End edit operators
						
						
				
					} // end pageType = "admin"
				} //End success fn of loadLocations
			});	
		},// End loadLocations */
		getSelected : function(){
			var $this = this;
			return $($this).find(".h.selected").map(function(i, e){
				return {
					"id" : $(e).data("locid"),
					"dateTo" : $(e).siblings(".outer").find("input[name=dateTo]:first").datetimepicker("getDate"),
					"dateFrom" : $(e).siblings(".outer").find("input[name=dateFrom]:first").datetimepicker("getDate"),
					"attendeesQuests" : $(e).siblings(".outer").find("input[name=attendeesQuests]:first").val(),
					"attendeesMembers" : $(e).siblings(".outer").find("input[name=attendeesMembers]:first").val(),
					"attendeesUnder21" : $(e).siblings(".outer").find("input[name=attendeesUnder21]:first").val(),
					"attendeesTotal" : $(e).siblings(".outer").find("input[name=attendeesTotal]:first").val(),
					"alcohol" : $(e).siblings(".outer").find("input[name=alcohol]:first").attr("checked"),
					"sound": $(e).siblings(".outer").find("input[name=sound]:first").attr("checked")
					};
		
			});
						
		},
		adminDialog : function(target, id){

			$(target).append($("<div>").addClass("admin-contact").attr("id", "a-"+id).hide());
	
			$(".admin-contact").bPopup({
				content: "ajax",
				loadUrl: target.settings.absUrl +"ajax/?type=adminContact&id=" + id,
				modalColor: "#666",
				onClose: function(){
					$(this).remove();	
				},
				loadCallback : function(){
					$(".admin-contact .close-dialog").click(function(){
						$(".admin-contact").bPopup().close();
					});
				}
				
			});
			
			
		}
		
		
	} // End methods

	$.fn.locationsWidget = function(method){
		
	   // Method calling logic
		if ( methods[method] ) {
		  return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
		  return methods.init.apply( this, arguments );
		} else {
		  $.error( 'Method ' +  method + ' does not exist on jQuery.locationsWidget' );
		}    
	}
	
})( jQuery );