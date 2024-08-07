<?php

use App\Database\Db;
use App\modules\phpgwapi\services\Settings;

phpgw::import_class('booking.async_task');

/**
 * Delete the Anonymous frontend-user from access-log
 */
class booking_async_task_delete_access_log extends booking_async_task
{
	var $db;
	public function __construct()
	{
		parent::__construct();
		$this->db = Db::getInstance();
	}

	public function get_default_times()
	{
		return array('day' => '*/1');
	}

	public function run($options = array())
	{
		$config = createobject('phpgwapi.config', 'bookingfrontend')->read();

		$login = !empty($config['anonymous_user']) ? $config['anonymous_user'] : '';
		$serverSettings = Settings::getInstance()->get('server');

		$domain = $serverSettings['default_domain'];

		/**
		 * Bail out if not defined
		 */
		if (!$login)
		{
			return;
		}

		$sql = "DELETE FROM phpgw_access_log WHERE (loginid = '{$login}' OR loginid = '{$login}#{$domain}') AND li < (extract(epoch from now()) - 4000);";

		$this->db->query($sql);
	}
}
