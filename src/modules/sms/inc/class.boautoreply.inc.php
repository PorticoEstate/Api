<?php

/**
 * phpGroupWare - SMS: A SMS Gateway.
 *
 * @author Sigurd Nes <sigurdne@online.no>
 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
 * @package sms
 * @subpackage autoreply
 * @version $Id$
 */

use App\modules\phpgwapi\services\Cache;

/**
 * Description
 * @package sms
 */
class sms_boautoreply
{

	var $start;
	var $query;
	var $filter;
	var $sort;
	var $order;
	var $cat_id, $so, $bocommon, $use_session, $allrows, $total_records;
	var $public_functions = array(
		'read' => true,
		'read_single' => true,
		'save' => true,
		'delete' => true,
		'check_perms' => true
	);

	function __construct($session = false)
	{
		$this->so = CreateObject('sms.soautoreply');
		$this->bocommon = CreateObject('sms.bocommon');

		if ($session)
		{
			$this->read_sessiondata();
			$this->use_session = true;
		}

		$start = Sanitizer::get_var('start', 'int', 'REQUEST', 0);
		$query = Sanitizer::get_var('query');
		$sort = Sanitizer::get_var('sort');
		$order = Sanitizer::get_var('order');
		$filter = Sanitizer::get_var('filter', 'int');
		$cat_id = Sanitizer::get_var('cat_id', 'int');
		$allrows = Sanitizer::get_var('allrows', 'bool');

		$this->start = $start ? $start : 0;

		if (array_key_exists('query', $_POST) || array_key_exists('query', $_GET))
		{
			$this->query = $query;
		}
		if (array_key_exists('filter', $_POST) || array_key_exists('filter', $_GET))
		{
			$this->filter = $filter;
		}
		if (array_key_exists('sort', $_POST) || array_key_exists('sort', $_GET))
		{
			$this->sort = $sort;
		}
		if (array_key_exists('order', $_POST) || array_key_exists('order', $_GET))
		{
			$this->order = $order;
		}
		if (array_key_exists('cat_id', $_POST) || array_key_exists('cat_id', $_GET))
		{
			$this->cat_id = $cat_id;
		}
		if ($allrows)
		{
			$this->allrows = $allrows;
		}
	}

	function save_sessiondata($data)
	{
		if ($this->use_session)
		{
			Cache::session_set('sms_autoreply', 'session_data', $data);
		}
	}

	function read_sessiondata()
	{

		$data = Cache::session_get('sms_autoreply', 'session_data');

		$this->start = $data['start'];
		$this->query = $data['query'];
		$this->filter = $data['filter'];
		$this->sort = $data['sort'];
		$this->order = $data['order'];
		$this->cat_id = $data['cat_id'];
	}

	function read()
	{
		$autoreply_info = $this->so->read(array(
			'start' => $this->start, 'query' => $this->query,
			'sort' => $this->sort, 'order' => $this->order,
			'allrows' => $this->allrows
		));
		$this->total_records = $this->so->total_records;
		return $autoreply_info;
	}
}
