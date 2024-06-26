/* global lang, event_id */

var building_id_selection = "";
$(document).ready(function ()
{
	//FIXME
	//add
	$("#start_date").change(function ()
	{
		var temp_end_date = $("#end_date").datetimepicker('getValue');
		var temp_start_date = $("#start_date").datetimepicker('getValue');
		if (!temp_end_date || (temp_end_date < temp_start_date))
		{
			$("#end_date").val('');

			var minutesToAdd = 15;
			var new_end_date = new Date(temp_start_date.getTime() + minutesToAdd * 60000);
			$('#end_date').datetimepicker('setOptions', {
				startDate: new Date(new_end_date)
			});
		}
	});

	//edit
	$("#from_").change(function ()
	{
		var temp_end_date = $("#to_").datetimepicker('getValue');
		var temp_start_date = $("#from_").datetimepicker('getValue');
		if (!temp_end_date || (temp_end_date < temp_start_date))
		{
			$("#to_").val('');

			var minutesToAdd = 15;
			var new_end_date = new Date(temp_start_date.getTime() + minutesToAdd * 60000);
			$('#to_').datetimepicker('setOptions', {
				startDate: new Date(new_end_date)
			});
		}
	});

	/**
	 * Update quantity related to time
	 */
	$("#dates-container").on("change", ".datetime", function (event)
	{
		if (typeof (post_handle_order_table) !== 'undefined')
		{
			event.preventDefault();
			post_handle_order_table();
		}

	});

	$('#field_cost_comment').hide();
	$('#field_cost').on('input propertychange paste', function ()
	{
		if ($('#field_cost').val() != $('#field_cost_orig').val())
		{
			$('#field_cost_comment').show();
		}
		else
		{
			$('#field_cost_comment').hide();
		}
	});

	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uibuilding.index'}, true),
		'field_building_name', 'field_building_id', 'building_container');

	JqueryPortico.autocompleteHelper(phpGWLink('index.php', {menuaction: 'booking.uiorganization.index'}, true),
		'field_org_name', 'field_org_id', 'org_container');

	$("#field_activity").change(function ()
	{
		var oArgs = {menuaction: 'booking.uiapplication.get_activity_data', activity_id: $(this).val()};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				var html_agegroups = '';
				var html_audience = '';

				if (data != null)
				{
					var agegroups = data.agegroups;
					for (var i = 0; i < agegroups.length; ++i)
					{
						html_agegroups += "<tr>";
						html_agegroups += "<th>" + agegroups[i]['name'] + "</th>";
						html_agegroups += "<td>";
						html_agegroups += "<input class=\"input50\" type=\"text\" name='male[" + agegroups[i]['id'] + "]' value='0'></input>";
						html_agegroups += "</td>";
						html_agegroups += "<td>";
						html_agegroups += "<input class=\"input50\" type=\"text\" name='female[" + agegroups[i]['id'] + "]' value='0'></input>";
						html_agegroups += "</td>";
						html_agegroups += "</tr>";
					}
					$("#agegroup_tbody").html(html_agegroups);

					var audience = data.audience;
					for (var i = 0; i < audience.length; ++i)
					{
						html_audience += "<li>";
						html_audience += "<label>";
						html_audience += "<input type=\"radio\" name=\"audience[]\" value='" + audience[i]['id'] + "'></input>";
						html_audience += audience[i]['name'];
						html_audience += "</label>";
						html_audience += "</li>";
					}
					$("#audience").html(html_audience);
				}
			}
		});
	});

	SendSms = function ()
	{
		r = confirm(lang['send sms'] + '?');

		if (r !== true)
		{
			return;
		}

		oArgs = {menuaction: 'booking.uievent.send_sms_participants', id: event_id};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			data: {sms_content: $('#field_sms_content').val(), send_sms: true},
			dataType: 'json',
			url: requestUrl,
			success: function (data)
			{
				if (data != null)
				{
					var message = data.message;

					htmlString = "";
					var msg_class = "msg_good";
					if (data.status == 'error')
					{
						msg_class = "error";
					}
					htmlString += "<div class=\"" + msg_class + "\">";
					htmlString += message;
					htmlString += '</div>';
					$("#sms_receipt").html(htmlString);
					if (data.status == 'error')
					{
						setTimeout(function(){ $("#sms_receipt").html(''); }, 1000);
					}
				}
			}
		});

	};

});

$(window).on('load', function ()
{
	var building_id = $('#field_building_id').val();
	if (building_id)
	{
		populateTableChkResources(building_id, initialSelection);
		building_id_selection = building_id;
	}
	$("#field_building_name").on("autocompleteselect", function (event, ui)
	{
		var building_id = ui.item.value;
		if (building_id != building_id_selection)
		{
			populateTableChkResources(building_id, []);
			building_id_selection = building_id;
		}
	});
	$("#field_org_name").on("autocompleteselect", function (event, ui)
	{
		var organization_id = ui.item.value;
		var requestURL = phpGWLink('index.php', {menuaction: "booking.uiorganization.index", filter_id: organization_id}, true);

		$.getJSON(requestURL, function (result)
		{
			if (result.recordsTotal > 0)
			{
				var organization = result.data[0];
				$("#field_customer_ssn").val(organization.customer_ssn);
				$("#field_customer_organization_number").val(organization.customer_organization_number);

				if (organization.customer_identifier_type == "ssn")
				{
					document.getElementById("field_customer_identifier_type").selectedIndex = "1";
					$("#field_customer_ssn").show();
					$("#field_customer_organization_number").hide();
				}
				else if (organization.customer_identifier_type == "organization_number")
				{
					document.getElementById("field_customer_identifier_type").selectedIndex = "2";
					$("#field_customer_ssn").hide();
					$("#field_customer_organization_number").show();
				}

				if(typeof(organization.contacts[0]) !=='undefined')
				{
					$("#field_contact_name").val(organization.contacts[0].name);
					$("#field_contact_email").val(organization.contacts[0].email);
					$("#field_contact_phone").val(organization.contacts[0].phone);
				}
			}

		});
	});

	$('#resources_container').on('change', '.chkRegulations', function ()
	{
		var resources = new Array();
		$('#resources_container input[name="resources[]"]:checked').each(function ()
		{
			resources.push($(this).val());
		});

		if (typeof (application_id) === 'undefined')
		{
			application_id = '';
		}
		if (typeof (reservation_type) === 'undefined')
		{
			reservation_type = '';
		}
		if (typeof (reservation_id) === 'undefined')
		{
			reservation_id = '';
		}

		if (typeof (populateTableChkArticles) !== 'undefined')
		{

			populateTableChkArticles([
			], resources, application_id, reservation_type, reservation_id);
		}
	});


});

if ($.formUtils)
{
	$.formUtils.addValidator({
		name: 'target_audience',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var n = 0;
			$('#audience input[name="audience[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;
			if(!v)
			{
				$('#audience').addClass("error").css("border-color", "red");
			}
			else
			{
				$('#audience').removeClass("error").css("border-color", "");
			}

			return v;
		},
		errorMessage: 'Please choose at least 1 target audience',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'number_participants',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var n = 0;
			$('#agegroup_tbody input').each(function ()
			{
				if ($(this).val() != "" && $(this).val() > 0)
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;

			if(!v)
			{
				$('#agegroup_tbody').addClass("error").css("border-color", "red");
			}
			else
			{
				$('#agegroup_tbody').removeClass("error").css("border-color", "");
			}

			return v;
		},
		errorMessage: 'Number of participants is required',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'customer_identifier',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var v = true;
			var customer_ssn = $('#field_customer_ssn').val();
			var customer_organization_number = $('#field_customer_organization_number').val();
			var cost = $('#field_cost').val();
			if ((customer_ssn == "" && customer_organization_number == "") && (cost > 0))
			{
				v = false;
			}
			return v;
		},
		errorMessage: 'There is set a cost, but no invoice data is filled inn',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'application_dates',
		validatorFunction: function (value, $el, config, languaje, $form)
		{
			var n = 0;
			if ($('input[name="from_[]"]').length == 0 || $('input[name="from_[]"]').length == 0)
			{
				return false;
			}
			$('input[name="from_[]"]').each(function ()
			{
				if ($(this).val() == "")
				{
					$($(this).addClass("error").css("border-color", "red"));
					n++;
				}
				else
				{
					$($(this).removeClass("error").css("border-color", ""));
				}
			});
			$('input[name="to_[]"]').each(function ()
			{
				if ($(this).val() == "")
				{
					$($(this).addClass("error").css("border-color", "red"));
					n++;
				}
				else
				{
					$($(this).removeClass("error").css("border-color", ""));
				}
			});
			var v = (n == 0) ? true : false;
			return v;
		},
		errorMessage: 'Invalid date',
		errorMessageKey: ''
	});

	$.formUtils.addValidator({
		name: 'application_resources',
		validatorFunction: function (value, $el, config, language, $form)
		{
			var n = 0;
			$('#resources_container table input[name="resources[]"]').each(function ()
			{
				if ($(this).is(':checked'))
				{
					n++;
				}
			});
			var v = (n > 0) ? true : false;

			if(!v)
			{
				$('#resources_container').find('table').addClass("error").css("border-color", "red");
			}
			else
			{
				$('#resources_container').find('table').removeClass("error").css("border-color", "");
			}

			return v;
		},
		errorMessage: 'Please choose at least 1 resource',
		errorMessageKey: 'application_resources'
	});
}

function populateTableChkResources(building_id, selection)
{
	var url = phpGWLink('index.php', {menuaction: 'booking.uiresource.index', sort: 'name', filter_building_id: building_id, length: -1}, true);
	var container = 'resources_container';
	var colDefsResources = [{label: '', object: [{type: 'input', attrs: [
						{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}, {name: 'class', value: 'chkRegulations'}
					]}
			], value: 'id', checked: selection}, {key: 'name', label: lang['Name']}, {key: 'rescategory_name', label: lang['Resource Type']}
	];
	populateTableChk(url, container, colDefsResources);
}

function populateTableChk(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'pure-table pure-table-bordered');
}
