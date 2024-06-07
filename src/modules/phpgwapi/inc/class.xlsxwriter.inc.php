<?php
	/**
	* phpGroupWare
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package phpgwapi
	* @subpackage utilities
 	* @version $Id$
	*
	*/

	/**
	*
	* @package phpgwapi
	* @subpackage utilities
	*/

	use App\modules\phpgwapi\services\Settings;

	require_once PHPGW_API_INC . '/xlsxwriter/xlsxwriter.class.php';

	class phpgwapi_xlsxwriter extends XLSXWriter
	{
 
		public function __construct()
		{
			parent::__construct();
			$serverSettings = Settings::getInstance()->get('server');
			$this->setTempDir($serverSettings['temp_dir']);
		}
	}
