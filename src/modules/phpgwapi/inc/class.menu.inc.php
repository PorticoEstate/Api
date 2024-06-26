<?php

use App\modules\phpgwapi\services\Settings;
use App\modules\phpgwapi\services\Cache;
use App\modules\phpgwapi\services\Hooks;
use App\modules\phpgwapi\services\Translation;


	/**
	 * phpGroupWare menu handler class
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id$
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/*
	 * phpGroupWare menu handler class
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 */
	class phpgwapi_menu
	{

		var $public_functions = array
			(
			'get_local_menu_ajax' => true,
			'update_bookmark_menu' => true
		);

		/**
		 * Clear the user's menu so it can be regenerated cleanly
		 *
		 * @return void
		 */
		public function clear()
		{
		$userSettings = Settings::getInstance()->get('user');

			$account_id = $userSettings['account_id'];
			Cache::user_clear('phpgwapi', 'menu', $account_id);
		}

		/**
		 * Get the menu structure and return it
		 *
		 * @param string $mtype the type of menu sought - default all returned
		 *
		 * @return array menu structure
		 */
		public function get($mtype = null)
		{
		$userSettings = Settings::getInstance()->get('user');

			static $menu = null;

			$account_id = $userSettings['account_id'];

			if(!$menu)
			{
	//			$menu = Cache::user_get('phpgwapi', 'menu', $account_id, true, true);
			}

			if(!$menu)
			{
				$menu = self::load();
				Cache::user_set('phpgwapi', 'menu', $menu, $account_id, true, true);
			}

			if(!is_null($mtype) && isset($menu[$mtype]))
			{
				return $menu[$mtype];
			}
			return $menu;
		}

		/**
		 * Get categories available for the current user
		 *
		 * @param string $module the module the categories are sought for
		 *
		 * @return array menu class compatiable array of categories
		 */
		public static function get_categories($module)
		{
		$userSettings = Settings::getInstance()->get('user');

			$catobj = createObject('phpgwapi.categories', $userSettings['account_id'], $module);
			$cats = $catobj->return_sorted_array(0, false, '', 'ASC', 'cat_main, cat_level, cat_name', true);

			return $cats;
		}

		/**
		 * Callback for usort($navbar)
		 *
		 * @param array $item1 the first item to compare
		 * @param array $item2 the second item to compare
		 * @return int result of comparision
		 */
		private static function sort_navitems( $item1, $item2 )
		{
			return strcmp($item1['text'], $item2['text']);
		}

		/**
		 * Load the menu structure from all available applications
		 *
		 * @return array the menu structure for the current user
		 */
		private static function load()
		{
			$menus = array();
			$raw_menus = (new Hooks())->process('menu');

			$userSettings = Settings::getInstance()->get('user');

			foreach($userSettings['apps'] as $app => $app_info)
			{
				if($app_info['status'] === 2  || $app_info['enabled'] === false) // hidden
				{
					continue;
				}
				$raw_menu = $raw_menus[$app];
				// Ignore invalid entries
				if(!is_array($raw_menu))
				{
					continue;
				}
				foreach($raw_menu as $mtype => $menu)
				{
					//no point in adding empty items
					if(!count($menu))
					{
						continue;
					}

					/**
					 * the order of items is there for a reason...
					 */
//					uasort($menu, array('phpgwapi_menu', 'sort_navitems'));

					if(!isset($menus[$mtype]))
					{
						$menus[$mtype] = array();
					}

					switch($mtype)
					{
						case 'navbar':
							$menus[$mtype] = array_merge($menus[$mtype], $menu);
							break;
						case 'admin':
							$app_text = $app == 'admin' ? lang('General') : lang($app);
							$menus['navigation']['admin'][$app] = array
							(
								'text' => Translation::getInstance()->translate($app, array(), true),
								'url' => phpgw::link('/index.php', array('menuaction' => 'admin.uiconfig.index',
									'appname' => $app)),
								'image' => isset($raw_menu['navbar'][$app]['image']) ? $raw_menu['navbar'][$app]['image'] : '',
								'children' => $menu
							);
						// no break here - fall thru
						default:
							$menus[$mtype][$app] = $menu;
					}
				}
			}
			return $menus;
		}

		/**
		 * Render  a menu for an application
		 *
		 * @param array  $item the menu item
		 * @param string $id   identificator for current location
		 * @param string $children rendered sub menu
		 * @param bool   $show_appname show appname as top level
		 */
		public function render_menu($app, $navigation, $navbar, $show_appname = false)
		{
			$treemenu = '';
			$submenu = isset($navigation) ? self::_render_submenu($app, $navigation) : '';
			$treemenu .= self::_render_item($navbar, "navbar::{$app}", $submenu, $show_appname);
			$html = <<<HTML
			<ul id="menu">
				{$treemenu}
			</ul>

HTML;
			return $html;
		}

		/**
		 * Render items from a menu and append the children
		 *
		 * @param array  $item         the menu item
		 * @param string $id           identificator for current location
		 * @param string $children     rendered sub menu
		 * @param bool   $show_appname show appname as top level
		 */
		protected static function _render_item($item, $id = '', $children = '', $show_appname = false)
		{
			$flags = Settings::getInstance()->get('flags');
			$current_class = '';
			if($id == "navbar::{$flags['menu_selection']}")
			{
				$current_class = 'current';
			}

			$link_class = " class=\"{$current_class}\"";

			if(isset($item['group']) && $show_appname) // at application
			{
				return <<<HTML
				<li class="parent">
					<span>{$item['text']}</span>
				{$children}
				</li>
HTML;
			}
			if(isset($item['group']) && !$show_appname)
			{
				return <<<HTML
				{$children}
HTML;
			}
			else if(isset($item['url']))
			{
				return <<<HTML
				<li>
					<a href="{$item['url']}"{$link_class} id="{$id}">
						<span>{$item['text']}</span>
					</a>
					{$children}
				</li>
HTML;
			}
		}

		/**
		 * Get sub items from a menu 
		 *
		 * @param string $parent  name of parent item
		 * @param array  $menu the menu items to add to structure
		 * @param bool   $show_appname show appname as top level
		 */
		protected static function _render_submenu($parent, $menu, $show_appname = false)
		{
			$out = '';
			foreach($menu as $key => $item)
			{
				$children = isset($item['children']) ? self::_render_submenu("{$parent}::{$key}", $item['children'], $show_appname) : '';
				$out .= self::_render_item($item, "navbar::{$parent}::{$key}", $children, $show_appname);
			}

			$out = <<<HTML
			<ul>
				{$out}
			</ul>

HTML;
			return $out;
		}

		/**
		 * Render a horisontal menu for an application
		 *
		 * @param array  $menu the menu item
		 */
		public static function render_horisontal_menu($menu)
		{
			$html = <<<HTML
			<table id="menu">
				<tr>
					<td>
						<table>
							<tr>
HTML;
			foreach($menu as &$item)
			{
				$current_class = '';
				if($item['this'])
				{
					$current_class = 'current';
					$item['text'] = "[<b>{$item['text']}</b>]";
				}
				$link_class = " class=\"{$current_class}\"";
				$html .= <<<HTML
					<td>
						<a href="{$item['url']}"{$link_class} id="{$id}">
							<span>{$item['text']}</span>
						</a>
					</td>
HTML;

				if($item['children'])
				{
					$children = $item['children'];
				}
			}
			$html .= <<<HTML
							</tr>
						</table>
HTML;

			$html .= isset($children) ? self::_render_horisontal_submenu($children) : '';

			$html .= <<<HTML
				</tr>
			</table>
HTML;
			return $html;
		}

		/**
		 * Get sub items from a menu 
		 *
		 * @param array  $menu the menu items to add to structure
		 */
		protected static function _render_horisontal_submenu($menu)
		{
			$html = <<<HTML
				<tr>
					<td>
						<table>
							<tr>				
HTML;

			foreach($menu as &$item)
			{
				$current_class = '';
				if($item['this'])
				{
					$current_class = 'current';
					$item['text'] = "[<b>{$item['text']}</b>]";
				}
				$link_class = " class=\"{$current_class}\"";
				$html .= <<<HTML
					<td>
						<a href="{$item['url']}"{$link_class} id="{$id}">
							<span>{$item['text']}</span>
						</a>
					</td>
HTML;

				if($item['children'])
				{
					$children = $item['children'];
				}
			}
			$html .= <<<HTML
							</tr>
						</table>
HTML;

			$html .= isset($children) ? self::_render_horisontal_submenu($children) : '';
			$html .= <<<HTML
				</td>
			</tr>
HTML;
			return $html;
		}

		public function get_local_menu($app = '')
		{
		$flags = Settings::getInstance()->get('flags');

			$app = $app ? $app : $flags['currentapp'];
			switch($app)
			{
				case 'home':
				case 'login':
					return array();
				default:
				// nothing
			}

			
			if(!$menu = Cache::session_get( "menu_{$app}", $flags['menu_selection']))
			{
				$menu_gross = execMethod("{$app}.menu.get_menu", 'horisontal');
				$selection = explode('::', $flags['menu_selection']);
				$level = 0;
				$menu = self::_get_sub_menu($menu_gross['navigation'], $selection, $level);
				Cache::session_set("menu_{$app}", isset($flags['menu_selection']) && $flags['menu_selection'] ? $flags['menu_selection'] : 'menu_missing_selection', $menu);
				unset($menu_gross);
			}
			return $menu;
		}

		/**
		 * Used for jstree
		 * @return array menu
		 */
		public function get_top_level_menu_ajax()
		{
			$navbar = $this->get('navbar');
			$exclude = array('logout', 'about', 'preferences');
			$top_level_menu = array();
			foreach($navbar as $app => $app_data)
			{
				if(in_array($app, $exclude))
				{
					continue;
				}

				$top_level_menu[] = array
					(
					'app' => $app,
					'text' => $app_data['text'],
					'url' => str_replace('&amp;', '&', $app_data['url']),
					'children' => true
				);
			}

			return $top_level_menu;
		}

		/**
		 * Used for jstree
		 * @return array menu
		 */
		public function get_local_menu_ajax()
		{
			$userSettings = Settings::getInstance()->get('user');

			$node = Sanitizer::get_var('node');
			if($node == 'top_level')
			{
				return $this->get_top_level_menu_ajax();
			}

			$selection = explode('|', $node);
			$app = $selection[0];

			if(!isset($userSettings['apps'][$app]))
			{
				return array();
			}
			$menu = array();

			$parent_node = array();
			if(isset($selection[1]))
			{
				for($i = 1; $i < count($selection); $i++)
				{
					$parent_node[] = $selection[$i];
				}
			}

			$_section = 'navigation';
			$admin = false;
			if($app == 'admin')
			{
				$admin = true;
				if(!isset($selection[1]))
				{
					$navigation = $this->get('admin');

					foreach($userSettings['apps'] as $_app => $app_info)
					{
						if(!in_array($_app, array('logout', 'about', 'preferences')) && isset($navigation[$_app]))
						{
							$menu[] = array
							(
								'app' => 'admin',
								'key' => $_app,
								'is_leaf' => count($navigation[$_app]) > 1 ? false : true,
								'children' => count($navigation[$_app]) > 1 ? true : false,
								'text' => Translation::getInstance()->translate($_app, array(), true),
								'url' => str_replace('&amp;', '&', phpgw::link('/index.php', array(
									'menuaction' => 'admin.uiconfig.index', 'appname' => $_app)))
							);
						}
					}

					return $menu;
				}
				else
				{
					$_section = 'admin';
					$app = $selection[1];
					array_shift($selection);
				}
			}


			if(!$menu_gross = Cache::session_get('phpgwapi', "menu_{$app}"))
			{
				$menu_gross = execMethod("{$app}.menu.get_menu");
				Cache::session_set('phpgwapi', "menu_{$app}", $menu_gross);
			}

			$menu_gross = $menu_gross[$_section];

			$count_selection = count($selection);
			if($count_selection > 1)
			{
				for($i = 1; $i < count($selection); $i++)
				{
					if(isset($menu_gross[$selection[$i]]))
					{
						$menu_gross = $menu_gross[$selection[$i]];
					}
					else if(isset($menu_gross['children'][$selection[$i]]))
					{
						$menu_gross = $menu_gross['children'][$selection[$i]];
					}
					else
					{
						$menu_gross = array();
					}
				}
				$children = isset($menu_gross['children']) ? $menu_gross['children'] : array();
			}
			else
			{
				$children = $menu_gross;
			}

			$i = 0;
			foreach($children as $key => $vals)
			{
				$vals['url'] = str_replace('&amp;', '&', $vals['url']);
				$menu[$i] = $vals;
				$menu[$i]['app'] = $admin ? 'admin' : $app;
				$menu[$i]['key'] = implode('|', array_merge($parent_node, array($key)));
				if(isset($menu[$i]['children']))
				{
					$menu[$i]['is_leaf'] = false;
					$menu[$i]['children'] = true;
				}
				else
				{
					$menu[$i]['is_leaf'] = true;
					$menu[$i]['children'] = false;
				}
				$i++;
			}

			return $menu;
		}

		protected static function _get_sub_menu($children = array(), $selection = array(), $level = 0)
		{
			$level++;
			$i = 0;
			$menu = array();
			foreach($children as $key => $vals)
			{
				$menu[$i] = $vals;
				$menu[$i]['this'] = false;
				$menu[$i]['key'] = $key;
				$menu[$i]['is_leaf'] = true;
				if($key == $selection[$level])
				{
					$menu[$i]['this'] = true;
					if(isset($menu[$i]['children']))
					{
						$menu[$i]['children'] = self::_get_sub_menu($menu[$i]['children'], $selection, $level);
						$menu[$i]['is_leaf'] = false;
					}
				}
				else
				{
					if(isset($menu[$i]['children']))
					{
						$menu[$i]['is_leaf'] = false;
						unset($menu[$i]['children']);
					}
				}
				$i++;
			}

			return $menu;
		}

		public function update_bookmark_menu()
		{
			$bookmark_text = strip_tags(Sanitizer::get_var('text', 'html'));
			$bookmark_icon = Sanitizer::get_var('icon', 'string');
			$nav_location = Sanitizer::get_var('nav_location', 'string');
			
			$href_comopnents = parse_url(Sanitizer::get_var('href', 'raw'));
			parse_str($href_comopnents['query'],$query_arr);

			unset($query_arr['click_history']);

			$bookmark_href = $href_comopnents['path'] . '?' . http_build_query($query_arr);

			$bookmark_candidate_arr = explode('bookmark_', Sanitizer::get_var('bookmark_candidate', 'string'));

			$bookmark_candidate = empty($bookmark_candidate_arr[1]) ? $bookmark_candidate_arr[0] : $bookmark_candidate_arr[1];
			$userSettings = Settings::getInstance()->get('user');
			$user_id = $userSettings['account_id'];

			$bookmarks = Cache::user_get('phpgwapi', "bookmark_menu", $user_id);
			if($bookmarks && is_array($bookmarks) && isset($bookmarks[$bookmark_candidate]) && is_array($bookmarks[$bookmark_candidate]))
			{
				unset($bookmarks[$bookmark_candidate]);
				$status = lang('bookmark deleted');
			}
			else
			{
				if(!is_array($bookmarks))
				{
					$bookmarks = array();
				}

				$bookmarks[$bookmark_candidate]	 = array(
					'text'			 => $bookmark_text,
					'icon'			 => $bookmark_icon,
					'href'			 => $bookmark_href,
					'nav_location'	 => $nav_location
				);
				$status = lang('bookmark added');
			}

			Cache::user_set('phpgwapi', "bookmark_menu", $bookmarks, $user_id);

			return array
				(
				'status' => $status
			);
		}
	}