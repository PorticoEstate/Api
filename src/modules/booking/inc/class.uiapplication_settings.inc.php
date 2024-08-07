<?php

use App\modules\phpgwapi\services\Settings;

phpgw::import_class('booking.uicommon');

class booking_uiapplication_settings extends booking_uicommon
{

	public $public_functions = array(
		'index' => true,
		'query' => true,
	);

	public function __construct()
	{
		parent::__construct();
		self::set_active_menu('booking::settings::application_settings');
		Settings::getInstance()->update('flags', ['app_header' => lang('booking') . '::' . lang('Application Settings')]);
	}

	public function index()
	{
		$config = CreateObject('phpgwapi.config', 'booking');
		$config->read();

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			foreach ($_POST as $dim => $value)
			{
				if (strlen(trim($value)) > 0)
				{
					$config->value($dim, Sanitizer::clean_value($value));
				}
				else
				{
					unset($config->config_data[$dim]);
				}
			}
			$config->save_repository();
		}

		$tabs = array();
		$tabs['generic'] = array('label' => lang('Application Settings'), 'link' => '#settings');
		$active_tab = 'generic';

		$settings = array();
		$settings['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
		self::rich_text_editor('field_application_new_application');
		//			self::rich_text_editor('field_application_description');
		self::render_template_xsl('application_settings', array(
			'config_data' => $config->config_data,
			'data' => $settings
		));
	}

	public function query()
	{
	}
}
