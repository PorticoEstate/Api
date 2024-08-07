<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage admin
	 * @version $Id$
	 */

	use App\modules\phpgwapi\services\Cache;

	/**
	 * Description
	 * @package property
	 */
	class property_boasync
	{

		var $start;
		var $query;
		var $filter;
		var $sort;
		var $order;
		var $cat_id;
		var $allrows;
		var $so, $socommon, $use_session, $total_records;
		var $public_functions = array
			(
			'read'			 => true,
			'read_single'	 => true,
			'save'			 => true,
			'delete'		 => true,
		);

		function __construct( $session = false )
		{
			$this->so		 = CreateObject('property.soasync');
			$this->socommon	 = CreateObject('property.socommon');

			if ($session)
			{
				$this->read_sessiondata();
				$this->use_session = true;
			}

			$start	 = Sanitizer::get_var('start', 'int', 'REQUEST', 0);
			$query	 = Sanitizer::get_var('query');
			$sort	 = Sanitizer::get_var('sort');
			$order	 = Sanitizer::get_var('order');
			$cat_id	 = Sanitizer::get_var('cat_id', 'int');
			$allrows = Sanitizer::get_var('allrows', 'bool');

			$this->start	 = $start ? $start : 0;
			$this->query	 = isset($query) ? $query : $this->query;
			$this->sort		 = isset($sort) && $sort ? $sort : '';
			$this->order	 = isset($order) && $order ? $order : '';
			$this->cat_id	 = isset($cat_id) && $cat_id ? $cat_id : '';
			$this->allrows	 = isset($allrows) && $allrows ? $allrows : '';
		}

		function save_sessiondata( $data )
		{
			if ($this->use_session)
			{
				Cache::session_set('async', 'session_data', $data);
			}
		}

		function read_sessiondata()
		{
			$data = Cache::session_get('async', 'session_data');

			$this->start	 = $data['start'];
			$this->query	 = $data['query'];
			$this->filter	 = $data['filter'];
			$this->sort		 = $data['sort'];
			$this->order	 = $data['order'];
			$this->cat_id	 = $data['cat_id'];
		}

		function read( $data = array() )
		{
			/* $method = $this->so->read(array('start' => $this->start,'query' => $this->query,'sort' => $this->sort,'order' => $this->order,
			  'allrows' => $this->allrows)); */
			$method = $this->so->read($data);

			$this->total_records = $this->so->total_records;

			return $method;
		}

		function read_single( $id )
		{
			return $this->so->read_single($id);
		}

		function save( $method, $action = '' )
		{
			if ($action == 'edit')
			{
				if ($method['id'] != '')
				{

					$receipt = $this->so->edit($method);
				}
			}
			else
			{
				$receipt = $this->so->add($method);
			}
			return $receipt;
		}

		function delete( $id )
		{
			$this->so->delete($id);
		}
	}