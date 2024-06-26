<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="container new-application-page pt-5" id="new-application-page">
		<form action="{add_action}" data-bind='' method="POST" id='application_form' enctype='multipart/form-data' name="form" novalidate="true" class="needs-validationm">
			<div class="row">

				<div class="col-md-8 offset-md-2">
			
					<a class="exitBtn float-right">
						<xsl:attribute name="href">
							<xsl:value-of select="application/frontpage_link"/>
						</xsl:attribute>
						<i class="fas fa-times" />
						<xsl:value-of select="php:function('lang', 'Exit to homepage')"/>
					</a>
				
					
					<h1 class="font-weight-bold">
						<xsl:value-of select="php:function('lang', 'New application')"/>
					</h1>

					<p>
						<xsl:value-of disable-output-escaping="yes" select="config/application_new_application"/>
					</p>
					<hr class="mt-5 mb-5"></hr>

					<div class="mb-4">
						<xsl:call-template name="msgbox"/>
					</div>

					<input type="text" hidden="hidden" name="activity_id" data-bind="value: activityId" />
					<input name="formstage" value="partial1" hidden="hidden"/>
					<h2 class="font-weight-bold mb-4">
						<xsl:value-of select="php:function('lang', 'Choose rent object and rentperiod')" />
					</h2>

					<p>
						<xsl:value-of select="php:function('lang', 'Application for')"/>:
						<xsl:value-of select="application/building_name"/>
						<br/>
					</p>

					<div class="form-group">
					
						<div class="form-group">
							<span class="font-weight-bold d-block mt-2 span-label">
								<xsl:value-of select="php:function('lang', 'Chosen rent object')" />:
							</span>
							<div id="bookable-resource" data-bind="foreach: bookableresource">
								<span class="mr-2" data-bind='html: selected() ? name : "", visible: selected()'></span>
							</div>
							<span id="chosenResource" data-bind="ifnot: isResourceSelected" class="isSelected validationMessage">
								<xsl:value-of select="php:function('lang', 'No rent object chosen')" />
							</span>
						</div>
					

						<div class="">	
							<div type="input"  class="choseResource form-control text-left dropdown-toggle w-100" data-toggle="dropdown">
								<xsl:value-of select="php:function('lang', 'choose')" />
								<span class="caret"></span>
							</div>

							<ul class="dropdown-menu px-2" data-bind="foreach: bookableresource">
								<li>
									<div class="form-check checkbox checkbox-primary">
										<label class="check-box-label">
											<input class="form-check-input choosenResource" type="checkbox" name="resources[]" data-bind="textInput: id, checked: selected" />
											<span class="label-text" data-bind="html: name"></span>
										</label>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<xsl:if test="config/activate_application_articles !=''">
						<div class="form-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Articles')" />
							</label>
							<input type="hidden" data-validation="application_articles">
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please choose at least 1 Article')" />
								</xsl:attribute>
							</input>
							<div id="articles_container" style="display:inline-block;">
							</div>
						</div>
					</xsl:if>
					<!-- Select Time and Date Section -->
					<div class="form-group">
						<!-- Display Time Chosen -->
						<div class="form-group">
							<span class="font-weight-bold d-block mt-2 span-label">
								<xsl:value-of select="php:function('lang', 'Chosen rent period')" />
							</span>
							<div data-bind="foreach: date">
								<div class="d-block">
									<input class="datetime" required="true" name="from_[]" hidden="hidden" data-bind="value: from_"/>
									<input class="datetime" required="true" name="to_[]" hidden="hidden" data-bind="value: to_"/>
									<span data-bind='text: formatedPeriode'></span>
									
									<button class="ml-2" data-bind="click: $parent.removeDate">
										<i class="fas fa-minus-circle"></i>
									</button>
								</div>
							</div>
							<span id="inputTime" data-bind="if: date().length == 0" class="validationMessage applicationSelectedDates">
								<xsl:value-of select="php:function('lang', 'Select a date and time')" />
							</span>
						</div>
						<div class="form-group">
							<div class="row">
								<!-- Date Pick -->
								<div class="form-group col-lg-5 col-sm-12 col-12">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="far fa-calendar-alt"></i>
											</span>
										</div>
										<input type="text" onkeydown="return false" class="bookingDate form-control datepicker-btn" data-bind="textInput: bookingDate">
											<xsl:attribute name="placeholder">
												<xsl:value-of select="php:function('lang', 'Date')"/>
											</xsl:attribute>
										</input>
									</div>
								</div>
								<!-- From Time Pick -->
								<div class="form-group col-lg-3 col-sm-6 col-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="far fa-clock"></i>
											</span>
										</div>
										<input type="text" onkeydown="return false" class="form-control bookingStartTime mr-2" data-bind="textInput: bookingStartTime">
											<xsl:attribute name="placeholder">
												<xsl:value-of select="php:function('lang', 'from')"/>
											</xsl:attribute>
										</input>
									</div>
								</div>
								<!-- To Time Pick -->
								<div class="form-group col-lg-3 col-sm-6 col-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="far fa-clock"></i>
											</span>
										</div>
										<input type="text" onkeydown="return false" class="form-control bookingEndTime" data-bind="textInput: bookingEndTime">
											<xsl:attribute name="placeholder">
												<xsl:value-of select="php:function('lang', 'to')"/>
											</xsl:attribute>
										</input>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<!-- Information About Event -->
					<hr class="mt-5 mb-5"></hr>
					<h2 class="font-weight-bold mb-4">
						<xsl:value-of select="php:function('lang', 'Information about the event')" />
					</h2>

					<!-- Target Audience Section-->
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Target audience')" />
						</label>
						<div class="form-control text-left dropdown-toggle w-100" id="audienceDropdownBtn" type="input" data-toggle="dropdown">
							<xsl:value-of select="php:function('lang', 'Choose target audience')" />
							<span class="caret"></span>
						</div>

						<ul class="dropdown-menu px-2" data-bind="foreach: audiences" aria-label="Large">
							<li class="dropdown-item" data-bind="text: name, id: id, click: $root.audienceSelected"></li>
							<!-- <a class="dropdown-item" data-bind="text: name, id: id, click: $root.audienceSelected" href="#"></a> -->
						</ul>
						<input class="form-control" id="inputTargetAudience" required="true" type="text" style="display: none" name="audience[]"  data-bind="value: audienceSelectedValue"/>
					</div>		
					<!-- Event Name -->
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Name for event/activity')" />
						</label>
						<input required="true" id="inputEventName" type="text" class="form-control" name="name" value="{application/name}">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="config/application_description"/>
							</xsl:attribute>
						</input>
					</div>
					<!-- Organizer -->
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'organizer/responsible seeker')" />
						</label>
						<input required="true" id="inputOrganizerName" type="text" class="form-control" name="organizer" value="{application/organizer}" placeholder="Navn på arrangør">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'organizer/responsible seeker')"/>
							</xsl:attribute>
						</input>
					</div>
					<!-- Homepage -->
					<div class="form-group">
						<div class="textContainer">
							<label>
								<xsl:value-of select="php:function('lang', 'Event/activity homepage')" />
							</label>
							<label>
								<xsl:value-of select="php:function('lang', 'Optional')" />
							</label>
						</div>
						<input placeholder="Hjemmeside for aktiviteten/arrangementet" type="text" class="form-input" name="homepage" value="{application/homepage}">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'Event/activity homepage')"/>
							</xsl:attribute>
						</input>
					</div>
					<!-- Description -->
					<div class="form-group">
						<div class="textContainer">
							<label>
								<xsl:value-of select="php:function('lang', 'Event/activity description')" />
							</label>
							<label>
								<xsl:value-of select="php:function('lang', 'Optional')" />
							</label>
						</div>

						<textarea id="field_description" style="resize: none;" class="form-input" rows="3" name="description" value="{application/description}">
							<xsl:attribute name="placeholder">
								<xsl:value-of select="php:function('lang', 'write here...')" />
							</xsl:attribute>
							<xsl:value-of select="application/description"/>
						</textarea>
					</div>

					<xsl:if test="config/application_equipment !=''">
						<div id="application_equipment" class="form-group" style="display: none;" >
							<label>
								<xsl:value-of select="config/application_equipment"/>
							</label>
							<textarea style="resize: none;" class="form-input" name="equipment">
								<xsl:attribute name="placeholder">
									<xsl:value-of select="php:function('lang', 'Extra information for the event')" />
								</xsl:attribute>
								<xsl:value-of select="application/equipment"/>
							</textarea>
						</div>
					</xsl:if>
					<!-- Estimated Number of Participants -->
					<div class="form-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Estimated number of participants')" />
						</label>
						<div class="p-2 border">
							<div class="row mb-2">
								<div class="col-3">
									<span class="span-label mt-2"></span>
								</div>
								<div class="col-4">
									<span>
										<xsl:value-of select="php:function('lang', 'Male')" />
									</span>
								</div>
								<div class="col-4">
									<xsl:value-of select="php:function('lang', 'Female')" />
								</div>
							</div>

							<div class="row mb-2" data-bind="foreach: agegroup">
								<span data-bind="text: id, visible: false"/>
								<div class="col-3">
									<span class="mt-2" data-bind="text: agegroupLabel"></span>
								</div>
								<div class="col-4">
									<input class="form-input sm-input maleInput" data-bind=""/>
								</div>
								<div class="col-4">
									<input class="form-input sm-input femaleInput" data-bind=""/>
								</div>
							</div>

						</div>
					</div>
					<!-- Upload Attachment -->
					<xsl:if test="config/enable_upload_attachment =1">
						<div id="attachment" class="form-group">
							<div class="textContainer">
								<label>
									<xsl:value-of select="php:function('lang', 'Upload Attachment')" />
								</label>
								<label>
									<xsl:value-of select="php:function('lang', 'optional')" />
								</label>
							</div>
						
							<div id="attachment-upload">
								<label for="field_name" class="upload-button">
									<xsl:value-of select="php:function('lang', 'Upload')" />
								</label>
								
							</div>
							<div id="show-attachment">
								<span id="field_name_input"></span>
								<a style="display: none" id="attachment-remove">Fjern Vedlegg</a>
								<!-- Input -->
								<input name="name" id='field_name' type='file' style="display: none" accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.txt,.pdf,.odt,.ods">
								</input>
							</div>
							<!-- Remove Attachment -->
							
						</div>
					</xsl:if>


					<!-- Terms and Conditions -->
					<div class="form-group termAccept mb-5">
						<label>
							<xsl:value-of select="php:function('lang', 'legal condition')" />
						</label>
						<span class="validationMessage" style="display: none">
							<xsl:value-of select="config/application_terms2"/>
						</span>
						<div id="regulation_documents" class="form-check checkbox">
						</div>
					</div>

					<hr class="mt-5 mb-5"></hr>
					<!-- Submit -->
					<div id="submitContainer" class="form-group float-right text-center">
						<button id="submitBtn" class="btn btn-light" type="submit">
							<xsl:value-of select="php:function('lang', 'Next step')" />
						</button>
						<div id="submit-error" style="display: none">
							<xsl:value-of select="php:function('lang', 'Please fill all fields')"/>
						</div>
					</div>
					
					<!-- Submit error modal -->
					<!-- <div id="errorModal" class="modal fade">
						<div class="modal-dialog modal-confirm">
							<div class="modal-content">
								<div class="modal-header">
									<div class="icon-box">
										<i class="material-icons"></i>
									</div>
									<button type="button" class="btn-close" data-dismiss="modal" aria-hidden="true"></button>
								</div>
								<div class="modal-body text-center">
									<h4>Ooops!</h4>	
									<p>Something went wrong. File was not uploaded.</p>
									<button class="btn btn-success" data-dismiss="modal">Try Again</button>
								</div>
							</div>
						</div>
					</div>      -->
				</div>
			</div>
		</form>

		<!--<pre data-bind="text: ko.toJSON(am, null, 2)"></pre>-->

		<div class="push"></div>
	</div>
	<script>
		var date_format = '<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />';
		var initialAcceptAllTerms = false;
		var initialSelection = <xsl:value-of select="application/resources_json"/>;
		var initialAudience = <xsl:value-of select="application/audience_json"/>;
		var initialDates = <xsl:value-of select="application/dates_json"/>;
		var initialAgegroups = <xsl:value-of select="application/agegroups_json"/>;
		var initialAcceptedDocs = <xsl:value-of select="application/accepted_documents_json"/>;
		var errorAcceptedDocs = '<xsl:value-of select="config/application_terms2"/>';
		var cache_refresh_token = "<xsl:value-of select="php:function('get_phpgw_info', 'server|cache_refresh_token')" />";
		var direct_booking = '<xsl:value-of select="direct_booking"/>';
		var building_id = '<xsl:value-of select="application/building_id"/>';
		var lang = <xsl:value-of select="php:function('js_lang', 'article', 'Select', 'price', 'unit', 'quantity', 'Selected', 'Delete', 'Sum', 'unit cost')"/>;
	</script>
</xsl:template>
