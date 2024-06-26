
$(document).ready(function ()
{
	show_feiltyper();

	$("#global_category_id").change(function ()
	{
		show_feiltyper();
	});

});

function show_feiltyper()
{
	document.getElementById('label_feiltyper').style.display = 'none';
	document.getElementById('id_feiltyper').style.display = 'none';
	document.getElementById('add_new_value_feiltyper').style.display = 'none';
	document.getElementById('delete_value_feiltyper').style.display = 'none';

	var category_id = $("#global_category_id").val();

	switch (category_id)
	{
		case '154': //Brann &amp; sikkerhet
		case '21': //Feilmelding
		case '74': // Garanti
		case '176': // title="Periodisk vedlikehold
			if (my_groups[15]) // forvalter
			{
				document.getElementById('label_feiltyper').style.display = 'inline-block';
				document.getElementById('id_feiltyper').style.display = 'inline-block';
				document.getElementById('add_new_value_feiltyper').style.display = 'block';
				document.getElementById('delete_value_feiltyper').style.display = 'block';
			}
			break;
		default:
	}
}

function validate_submit(action)
{
	var error = false;
	var feiltype_id = $("#id_feiltyper").val();
	var category_id = $("#global_category_id").val();
//	var group_id = $("#global_category_id").val();
	var status_id = $("#status_id").val();

	switch (category_id)
	{
		case '154': //Brann &amp; sikkerhet
		case '21': //Feilmelding
		case '74': // Garanti
		case '176': // title="Periodisk vedlikehold
			if (my_groups[15]) // forvalter
			{
				if (!feiltype_id && status_id == 'X')
				{
					error = true;
				}
			}
			break;
		default:
	}

	if (error)
	{
		alert('Feiltype må velges før meldingen kan avsluttes');
		var send_buttons = $('.pure-button');
		$(send_buttons).each(function ()
		{
			$(this).prop('disabled', false);
		});

	}
	else
	{
		/**
		 * In order to be consistent with the commit-processes in both "add" and "view"
		 */
		ajax_submit_form(action);
	}
}

