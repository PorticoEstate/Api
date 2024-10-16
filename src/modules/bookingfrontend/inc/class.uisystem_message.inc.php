<?php
	phpgw::import_class('booking.uisystem_message');

	class bookingfrontend_uisystem_message extends booking_uisystem_message
	{

		public $public_functions = array
			(
			'index' => true,
			'edit' => true,
			'show' => true,
			'toggle_show_inactive' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->url_prefix = 'bookingfrontend.uisystem_message';
			$this->module = "bookingfrontend";
		}

		public function show()
		{
			phpgw::no_access();
//			parent::show();
//			self::rich_text_editor('field-message');
		}

		public function edit()
		{
			date_default_timezone_set("Europe/Oslo");
			$date = new DateTime(Sanitizer::get_var('date'));
			$system_message = array();
			$system_message['building_id'] = Sanitizer::get_var('building_id', 'int');
			$system_message['building_name'] = Sanitizer::get_var('building_name', 'string');
			$system_message['cancel_link'] = self::link(array('menuaction' => $this->module . '.uisearch.index'));
			$system_message['created'] = $date->format('Y-m-d  H:m');

			$building = CreateObject('booking.sobuilding')->read_single($system_message['building_id']);

			if($building['deactivate_sendmessage'] == 1)
			{
				phpgw::no_access();
			}

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$system_message = array_merge($system_message, extract_values($_POST, array(
					'time', 'title', 'message', 'name', 'phone', 'email')));

				if ($system_message['title'] == '')
				{
					$errors['system_message'] = lang("Field %1 is required", lang('title'));
				}
				elseif ($system_message['message'] == '')
				{
					$errors['system_message'] = lang("Field %1 is required", lang('message'));
				}
				elseif ($system_message['name'] == '')
				{
					$errors['system_message'] = lang("Field %1 is required", lang('name'));
				}

				if (!$errors)
				{
					$system_message['title'] = $system_message['building_name'] . " - " . $system_message['title'];
					$receipt = $this->bo->add($system_message);
					self::redirect(array('menuaction' => $this->module . '.uibuilding.show.index',
						'id' => $system_message['building_id']));
				}
			}
			$this->flash_form_errors($errors);

			self::rich_text_editor('field-message');

			phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security',
				'file'));

			self::render_template_xsl('system_message', array('system_message' => $system_message));
		}
		public function index()
		{
			if (Sanitizer::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgw::no_access();
		}
	}