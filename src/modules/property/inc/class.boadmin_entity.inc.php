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

use App\modules\phpgwapi\services\Settings;
use App\modules\phpgwapi\security\Acl;
use App\modules\phpgwapi\controllers\Locations;
use App\modules\phpgwapi\services\Cache;

	/**
	 * Description
	 * @package property
	 */
	class property_boadmin_entity
	{

		/**
		 * @var integer $start info for pagination
		 */
		public $start;

		/**
		 * @var string $query user input: search string
		 */
		public $query;

		/**
		 * @var string $sort how to sort: ASC or DESC
		 */
		public $sort;

		/**
		 * @var string $order field to order by
		 */
		public $order;

		/**
		 * @var string $type entity set
		 */
		public $type;

		/**
		 * @var integer $entity_id entity type
		 */
		public $entity_id;

		/**
		 * @var integer $cat_id category of entity type
		 */
		public $cat_id;

		/**
		 * @var bool $use_session read vars from session or not
		 */
		protected $use_session;

		/**
		 * @var object $custom reference to custom fields object
		 */
		protected $custom;

		public $total_records;
		var $public_functions = array
			(
			'read'				 => true,
			'read_single'		 => true,
			'save'				 => true,
			'delete'			 => true,
			'get_category_list'	 => true,
			'get_attrib_list'	 => true
		);
		var $type_app,$bocommon,$so,$allrows,$location_id, $acl_location, $location_obj, $custom_functions;

		function __construct()
		{
			$this->bocommon	 = CreateObject('property.bocommon');
			$this->custom	 = createObject('property.custom_fields');
			$this->custom_functions = createObject('phpgwapi.custom_functions');

			$start		 = Sanitizer::get_var('start', 'int', 'REQUEST', 0);
			$query		 = Sanitizer::get_var('query');
			$sort		 = Sanitizer::get_var('sort');
			$order		 = Sanitizer::get_var('order');
			$type		 = Sanitizer::get_var('type');
			$cat_id		 = Sanitizer::get_var('cat_id', 'int');
			$allrows	 = Sanitizer::get_var('allrows', 'bool');
			$entity_id	 = Sanitizer::get_var('entity_id', 'int');

			$location_id = Sanitizer::get_var('location_id', 'int');

			$this->location_obj = new Locations();
			if ($location_id)
			{
				$loc_arr	 = $this->location_obj->get_name($location_id);
				$type_arr	 = explode('.', $loc_arr['location']);
				if (count($type_arr) == 4 && in_array($type_arr[1], array('entity', 'catch')))
				{
					$type		 = $type_arr[1];
					$entity_id	 = $type_arr[2];
					$cat_id		 = $type_arr[3];
				}

			}

			$this->so		 = CreateObject('property.soadmin_entity', '', '', $this->bocommon);
			$this->type_app	 = $this->so->get_type_app();

			$this->start	 = $start ? $start : 0;
			$this->query	 = isset($query) ? $query : $this->query;
			$this->sort		 = isset($sort) && $sort ? $sort : '';
			$this->order	 = isset($order) && $order ? $order : '';
			$this->type		 = isset($type) && $type && isset($this->type_app[$type]) ? $type : 'entity';
			$this->cat_id	 = isset($cat_id) && $cat_id ? $cat_id : '';
			$this->entity_id = isset($entity_id) && $entity_id ? $entity_id : '';
			$this->acl_location	 = '.admin.entity';

			if(!$location_id)
			{
				if($this->cat_id)
				{
					$location_id = $this->location_obj->get_id($this->type_app[$this->type], ".{$this->type}.{$this->entity_id}.{$this->cat_id}");
				}
				else if($this->entity_id)
				{
					$location_id = $this->location_obj->get_id($this->type_app[$this->type], ".{$this->type}.{$this->entity_id}");
				}
				else
				{
					$location_id = $this->location_obj->get_id($this->type_app[$this->type], $this->acl_location);

				}
			}

			$this->location_id = !empty($location_id) ? $location_id : null;

			$this->allrows	 = $allrows;
			$this->so->type	 = $this->type;
		}

		function get_location_level_list( $selected = '' )
		{

			$soadmin_location	 = CreateObject('property.soadmin_location');
			$location_types		 = $soadmin_location->select_location_type();
			$max_location_type	 = count($location_types);

			for ($i = 1; $i <= $max_location_type; $i++)
			{
				$location[$i]['id']		 = $i;
				$location[$i]['name']	 = $i . '-' . $location_types[($i - 1)]['name'];
			}

			return $this->bocommon->select_list($selected, $location);
		}

		function get_entity_list( $selected = '' )
		{
			$list = $this->so->read(array('allrows' => true));
			return $this->bocommon->select_multi_list($selected, $list);
		}

		function get_entity_list_2( $selected = '' )
		{
			$list[0]['id']	 = 'project';
			$list[0]['name'] = 'project';
			$list[1]['id']	 = 'ticket';
			$list[1]['name'] = 'ticket';
			$list[2]['id']	 = 'document';
			$list[2]['name'] = 'document';
			$list[3]['id']	 = 'request';
			$list[3]['name'] = 'request';
			$list[4]['id']	 = 'investment';
			$list[4]['name'] = 'investment';
			$list[5]['id']	 = 's_agreement';
			$list[5]['name'] = 'service agreement';
			return $this->bocommon->select_multi_list($selected, $list);
		}

		function get_entity_list_3( $selected = '' )
		{
			$list[0]['id']	 = 'ticket';
			$list[0]['name'] = 'ticket';
			$list[1]['id']	 = 'request';
			$list[1]['name'] = 'request';
			return $this->bocommon->select_multi_list($selected, $list);
		}

		function read( $data = array() )
		{
			#$entity = $this->so->read(array
			#            (
			#                'start'=> $this->start,
			#                'query'=> $this->query,
			#                'sort'=> $this->sort,
			#                'order'=> $this->order,
			#                'allrows'=> $this->allrows
			#            ));
			$entity				 = $this->so->read($data);
			$this->total_records = $this->so->total_records;
			return $entity;
		}

		function read_category( $data = array() )
		{
			/* $category = $this->so->read_category( array
			  (
			  'start' => $this->start,
			  'query' => $this->query,
			  'sort' => $this->sort,
			  'order' => $this->order,
			  'allrows' => $this->allrows,
			  'entity_id' => $entity_id
			  ));
			 */
			$category			 = $this->so->read_category($data);
			$this->total_records = $this->so->total_records;

			return $category;
		}

		public function get_category_list()
		{
			$entity_id = Sanitizer::get_var('entity_id', 'int');
			return $this->so->read_category(array('allrows' => true, 'entity_id' => $entity_id));
		}

		/**
		 * Fetch custom attributes for an given komponent type
		 */
		public function get_attrib_list()
		{
			$entity_id	 = Sanitizer::get_var('entity_id');
			$cat_id		 = Sanitizer::get_var('cat_id');

			return $this->custom->find('property', ".entity.{$entity_id}.{$cat_id}", 0, '', '', '', true, true);
		}

		function read_single( $id )
		{
			return $this->so->read_single($id);
		}

		function read_single_category( $entity_id, $cat_id )
		{
			return $this->so->read_single_category($entity_id, $cat_id);
		}

		/**
		 * 
		 * @param int $id
		 * @return array checklist
		 */
		function read_single_checklist( $id )
		{
			return $this->so->read_single_checklist($id);
		}

		function save( $values, $action = '' )
		{
			if ($action == 'edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_entity($values);
				}
			}
			else
			{
				$receipt = $this->so->add_entity($values);
				execMethod('phpgwapi.menu.clear');
			}
			return $receipt;
		}

		function save_category( $values, $action = '' )
		{
			if ($action == 'edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_category($values);
				}
			}
			else
			{
				$receipt = $this->so->add_category($values);
				execMethod('phpgwapi.menu.clear');
				if (isset($values['category_template']) && $values['category_template'] && isset($receipt['id']) && $receipt['id'])
				{
					$values2 = array
						(
						'entity_id'			 => $values['entity_id'],
						'cat_id'			 => $receipt['id'],
						'category_template'	 => $values['category_template'],
						'selected'			 => $values['template_attrib']
					);

					$this->_add_attrib_from_template($values2);
				}
			}
			return $receipt;
		}

		function save_checklist( $values, $action = '' )
		{
			if ($action == 'edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_checklist($values);
				}
			}
			else
			{
				$receipt = $this->so->add_checklist($values);
				if (!empty($values['checklist_template']) && isset($receipt['id']) && $receipt['id'])
				{
					$checklist_template = $this->read_single_checklist((int)$values['checklist_template']);
					$values2 = array(
						'location_id'			 => $receipt['location_id'],
						'template_location_id'	 => $checklist_template['location_id'],
						'selected'				 => $values['template_attrib']
					);

					$this->_add_attrib_from_template($values2);
				}
			}
			return $receipt;
		}

		//delete_checklist
		/**
		 * 
		 * @param int $id
		 * @return array
		 */
		function delete_checklist( $id )
		{
			return $this->so->delete_checklist($id);
		}

		/**
		 * 
		 * @param array $data
		 * @return array
		 */
		function read_checklist_stage( $data )
		{
			$values = $this->so->read_checklist_stage($data);
			$this->total_records = $this->so->total_records;
			return $values;
		}

		/**
		 * read_single_checklist_stage
		 * @param int $id
		 * @return array
		 */
		function read_single_checklist_stage( $id )
		{
			return $this->so->read_single_checklist_stage($id);
		}

		/**
		 * save_checklist_stage
		 * @param array $values
		 * @param string $action
		 * @return array
		 */
		function save_checklist_stage( $values, $action = '' )
		{
			if ($action == 'edit')
			{
				if ($values['id'] != '')
				{
					$receipt = $this->so->edit_checklist_stage($values);
				}
			}
			else
			{
				$receipt = $this->so->add_checklist_stage($values);
			}
			return $receipt;
		}

		/**
		 * delete_checklist_stage
		 * @param int $id
		 * @return array
		 */
		function delete_checklist_stage( $id )
		{
			return $this->so->delete_checklist_stage($id);
		}
		
		protected function _add_attrib_from_template( $values )
		{
			$template_info		 = explode('_', $values['category_template']);
			$template_entity_id	 = $template_info[0];
			$template_cat_id	 = $template_info[1];
			$location_id		 = !empty($values['location_id']) ? $values['location_id'] : null;
			$template_location_id = !empty($values['template_location_id']) ? $values['template_location_id'] : null;

			$attrib_group_list = $this->read_attrib_group(array(
				'location_id'	 => $template_location_id,
				'entity_id'		 => $template_entity_id,
				'cat_id'		 => $template_cat_id,
				'allrows'		 => true,
				'start'			 => 0,
				'query'			 => '',
				'sort'			 => 'ASC',
				'order'			 => 'id'
			));

			if (!empty($location_id))
			{
				$location_info	 = $this->location_obj->get_name($location_id);
				$location		 = $location_info['location'];
				$appname		 = $location_info['appname'];
			}
			else
			{
				$location	 = ".{$this->type}.{$values['entity_id']}.{$values['cat_id']}";
				$appname	 = $this->type_app[$this->type];
			}

			foreach ($attrib_group_list as $attrib_group)
			{
				$group = array(
					'id'		 => $attrib_group['id'],
					'appname'	 => $appname,
					'location'	 => $location,
					'group_name' => $attrib_group['name'],
					'group_sort' => $attrib_group['group_sort'],
					'parent_id'	 => $attrib_group['parent_id'],
					'descr'		 => $attrib_group['descr'],
					'remark'	 => $attrib_group['remark']
				);
				$this->custom->add_group($group);
			}

			$attrib_list = $this->read_attrib(array(
				'location_id'	 => $template_location_id,
				'entity_id'		 => $template_entity_id,
				'cat_id'		 => $template_cat_id,
				'allrows'		 => true)
			);

			if (!empty($template_location_id))
			{
				$template_location_info	 = $this->location_obj->get_name($template_location_id);
				$template_location		 = $template_location_info['location'];
				$template_appname		 = $template_location_info['appname'];
			}
			else
			{
				$template_location	 = ".{$this->type}.{$template_entity_id}.{$template_cat_id}";
				$template_appname	 = $this->type_app[$this->type];
			}

			$template_attribs = array();
			foreach ($attrib_list as $attrib)
			{
				if (in_array($attrib['id'], $values['selected']))
				{
					if (!empty($template_location_id))
					{
						$template_attribs[] = $this->custom->get($template_appname, $template_location, $attrib['id'], true);
					}
					else
					{
						$template_attribs[] = $this->read_single_attrib($template_entity_id, $template_cat_id, $attrib['id']);
					}
				}
			}
			unset($attrib);

			foreach ($template_attribs as $attrib)
			{
				$attrib['appname']	 = $appname;
				$attrib['location']	 = $location;

				$choices = array();
				if (isset($attrib['choice']) && $attrib['choice'])
				{
					$choices = $attrib['choice'];
					unset($attrib['choice']);
				}

				$id = $this->custom->add($attrib); // $id is recalculated in add()
				$attrib['id'] = $id;
				if ($choices)
				{
					foreach ($choices as $choice)
					{
						$attrib['new_choice'][]	 = array(
							'id'	 => (int)$choice['id'] < 89 ? 0 : (int)$choice['id'],
							'value'	 => $choice['value'],
							'title'	 => $choice['title'],
							'sort'	 => $choice['order']
						);
					}
					$this->custom->edit($attrib);
				}
			}
		}

		function delete( $cat_id = '', $entity_id = '', $attrib_id = '', $acl_location = '', $custom_function_id = '', $group_id = '' )
		{
			if (!$attrib_id && !$cat_id && $entity_id && !$custom_function_id && !$group_id)
			{
				$this->so->delete_entity($entity_id);
				execMethod('phpgwapi.menu.clear');
			}
			else if (!$attrib_id && $cat_id && $entity_id && !$custom_function_id && !$group_id)
			{
				$this->so->delete_category($entity_id, $cat_id);
				execMethod('phpgwapi.menu.clear');
			}
			else if ($group_id && $cat_id && $entity_id && !$custom_function_id && !$attrib_id)
			{
				$this->custom->delete_group($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", $group_id);
			}
			else if ($attrib_id && $cat_id && $entity_id && !$custom_function_id && !$group_id)
			{
				$this->custom->delete($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", $attrib_id);
				$this->so->delete_history($entity_id, $cat_id, $attrib_id);
			}
			else if ($custom_function_id && $acl_location)
			{
				$this->custom_functions->delete($this->type_app[$this->type], $acl_location, $custom_function_id);
			}
		}

		function get_attrib_group_list( $entity_id, $cat_id, $selected )
		{
			$group_list = $this->read_attrib_group(array('entity_id'	 => $entity_id, 'cat_id'	 => $cat_id,
				'allrows'	 => true));

			foreach ($group_list as &$group)
			{
				if ($group['id'] == $selected)
				{
					$group['selected'] = true;
				}
			}
			return $group_list;
		}

		function read_attrib_group( $data = array() )
		{
			$entity_id	 = $data['entity_id'];
			$cat_id		 = $data['cat_id'];
			if ($data['allrows'])
			{
				$this->allrows = $data['allrows'];
			}

			if(!empty($data['location_id']))
			{
				$location_info = $this->location_obj->get_name($data['location_id']);
				$location = $location_info['location'];
				$appname = $location_info['appname'];
			}
			else
			{
				$location	= ".{$this->type}.{$entity_id}.{$cat_id}";
				$appname = $this->type_app[$this->type];
			}

			$attrib = $this->custom->find_group($appname, $location, $data['start'], $data['query'], $data['sort'], $data['order'], $this->allrows);

			$this->total_records = $this->custom->total_records;

			return $attrib;
		}

		function read_attrib( $data = array() )
		{
			if ($data['allrows'])
			{
				$this->allrows = $data['allrows'];
			}

			if (!empty($data['location_id']))
			{
				$location_info	 = $this->location_obj->get_name($data['location_id']);
				$location		 = $location_info['location'];
				$appname		 = $location_info['appname'];
			}
			else
			{
				$location	 = ".{$this->type}.{$data['entity_id']}.{$data['cat_id']}";
				$appname	 = $this->type_app[$this->type];
			}


			$attrib = $this->custom->find(
				$appname,
				$location,
				$data['start'], $data['query'],
				$data['sort'],
				$data['order'],
				$this->allrows,
				false,
				array(),
				(int)$data['results']
			);
			$this->total_records = $this->custom->total_records;
			return $attrib;
		}

		function read_checklist( $data )
		{
			$values = $this->so->read_checklist($data);
			$this->total_records = $this->so->total_records;
			return $values;
		}

		function read_single_attrib( $entity_id, $cat_id, $id )
		{
			return $this->custom->get($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", $id, true);
		}

		function read_single_attrib_group( $entity_id, $cat_id, $id )
		{
			return $this->custom->get_group($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", $id, true);
		}

		function resort_attrib_group( $id, $resort )
		{
			$this->custom->resort_group($id, $resort, $this->type_app[$this->type], ".{$this->type}.{$this->entity_id}.{$this->cat_id}");
		}

		function resort_attrib( $id, $resort )
		{
			$this->custom->resort($id, $resort, $this->type_app[$this->type], ".{$this->type}.{$this->entity_id}.{$this->cat_id}");
		}

		function resort_checklist_stage( $id, $resort )
		{
			$this->so->resort_checklist_stage($id, $resort);
		}


		public function save_attrib_group( $group, $action = '' )
		{
			$receipt			 = array();
			$group['appname']	 = $this->type_app[$this->type];
			$group['location']	 = ".{$this->type}.{$group['entity_id']}.{$group['cat_id']}";
			if ($action == 'edit' && $group['id'])
			{
				if ($this->custom->edit_group($group))
				{
					$receipt['message'][] = array('msg' => lang('group has been updated'));
					return $receipt;
				}

				$receipt['error'][] = array('msg' => lang('unable to update group'));
				return $receipt;
			}
			else
			{
				$id = $this->custom->add_group($group);
				if ($id <= 0)
				{
					$receipt['error'][] = array('msg' => lang('unable to add group'));
					return $receipt;
				}
				else if ($id == -1)
				{
					$receipt['id']		 = 0;
					$receipt['error'][]	 = array('msg' => lang('group already exists, please choose another name'));
					$receipt['error'][]	 = array('msg' => lang('Attribute group has NOT been saved'));
					return $receipt;
				}

				$receipt['id']			 = $id;
				$receipt['message'][]	 = array('msg' => lang('group has been created'));
				return $receipt;
			}
		}

		public function save_attrib( $attrib, $action = '' )
		{
			$receipt			 = array();
			$attrib['appname']	 = $this->type_app[$this->type];
			$attrib['location']	 = ".{$this->type}.{$attrib['entity_id']}.{$attrib['cat_id']}";
			$attrib_table		 = $this->location_obj->get_attrib_table($attrib['appname'], $attrib['location']);
			if ($action == 'edit' && $attrib['id'])
			{
				if ($this->custom->edit($attrib))
				{
					$receipt				 = $this->custom->receipt;
					$receipt['message'][]	 = array('msg' => lang('Field has been updated'));
					return $receipt;
				}
				$receipt['error'][] = array('msg' => lang('Unable to update field'));
				return $receipt;
			}
			else
			{
				$id = $this->custom->add($attrib, $attrib_table);
				if ($id <= 0)
				{
					$receipt['error'][] = array('msg' => lang('Unable to add field'));
					return $receipt;
				}
				else if ($id == -1)
				{
					$receipt['id']		 = 0;
					$receipt['error'][]	 = array('msg' => lang('field already exists, please choose another name'));
					$receipt['error'][]	 = array('msg' => lang('Attribute has NOT been saved'));
					return $receipt;
				}

				$receipt['id']			 = $id;
				$receipt['message'][]	 = array('msg' => lang('Custom field has been created'));
				return $receipt;
			}
		}

		function read_custom_function( $data = array() )
		{

			if (!$data['location'] && $data['entity_id'] && $data['cat_id'])
			{
				$data['location'] = ".{$this->type}.{$data['entity_id']}.{$data['cat_id']}";
			}

			$data['appname'] = $this->type_app[$this->type];
			switch ($data['order'])
			{
				case'sorting';
					$data['order'] = 'custom_sort';
					break;
			}

			$values = $this->custom_functions->find($data);

			$this->total_records = $this->custom_functions->total_records;

			return $values;
		}

		function resort_custom_function( $id, $resort )
		{
			$location = ".{$this->type}.{$this->entity_id}.{$this->cat_id}";
			return $this->custom_functions->resort($id, $resort, $this->type_app[$this->type], $location);
		}

		function save_custom_function( $custom_function, $action = '' )
		{
			$receipt					 = array();
			$custom_function['appname']	 = $this->type_app[$this->type];
			if (!$custom_function['location'] && $custom_function['entity_id'] && $custom_function['cat_id'])
			{
				$custom_function['location'] = ".{$this->type}.{$custom_function['entity_id']}.{$custom_function['cat_id']}";
			}

			if ($action == 'edit')
			{
				if ($custom_function['id'] != '')
				{

					$receipt['id'] = $custom_function['id'];
					if ($this->custom_functions->edit($custom_function))
					{
						$receipt['message'][] = array('msg' => 'OK');
					}
					else
					{
						$receipt['error'][] = array('msg' => 'Error');
					}
				}
			}
			else
			{
				if ($receipt['id'] = $this->custom_functions->add($custom_function))
				{
					$receipt['message'][] = array('msg' => 'OK');
				}
				else
				{
					$receipt['error'][] = array('msg' => 'Error');
				}
			}
			return $receipt;
		}

		function select_custom_function( $selected = '' )
		{
			$admin_custom = createObject('admin.bo_custom');
			return $admin_custom->select_custom_function($selected, $this->type_app[$this->type]);
		}

		function read_single_custom_function( $id, $entity_id = '', $cat_id = '',  $location = '' )
		{
			if (!$location && $entity_id && $cat_id)
			{
				$location = ".{$this->type}.{$entity_id}.{$cat_id}";
			}
			return $this->custom_functions->get($this->type_app[$this->type], $location, $id);
		}

		function get_path( $entity_id, $node )
		{
			return $this->so->get_path($entity_id, $node);
		}

		function read_category_tree2( $entity_id )
		{
			return $this->so->read_category_tree2($entity_id);
		}

		function get_children2( $entity_id, $parent, $level, $reset = false )
		{
			return $this->so->get_children2($entity_id, $parent, $level, $reset);
		}

		function convert_to_eav()
		{
			return $this->so->convert_to_eav();
		}
		
		function add_choice_value($location_id, $attribute_id, $new_value)
		{
			return $this->custom->add_choice($location_id, $attribute_id, $new_value);
		}

		function delete_choice_value($location_id, $attribute_id, $choice_id)
		{
			return $this->custom->delete_choice($location_id, $attribute_id, $choice_id );
		}

	}