<?php
	/**
	 * phpGroupWare - SMS
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003-2005 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package SMS
	 * @subpackage core
	 * @version $Id$
	 */
	$title = $appname;
	$file = Array(
		'Preferences' => phpgw::link('/preferences/preferences.php', array(
			'appname' => $appname, 'type' => 'user')),
		'Grant Access' => phpgw::link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.aclprefs',
			'acl_app' => $appname))
	);
	display_section($appname, $file);
