<?php
	/**
	* phpGroupWare caching system
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
    * @author Sigurd Nes <sigurdne@gmail.com>
	* @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License Version 2 or later
	* @package phpgroupware
	* @subpackage phpgwapi
	* @version $Id$
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* phpGroupWare caching system
	*
	* Simple data caching system with common ways to store/retreive data
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category caching
	*/
    namespace App\Modules\Api\Services;
    use App\Modules\Api\Services\Redis;
    use App\Modules\Api\Services\Shm;
		
	use DirectoryIterator;
	use PDO;
	use App\Modules\Api\Services\Crypto;
		
	
	class Cache
	{
        
		static private $phpgwapi_redis;


        public function __construct()
        {
        }
 
		private static function get_redis()
		{
			self::$phpgwapi_redis = new Redis;
		}
		/**
		 * Decide whether to use database for caching - or not
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return bool use database
		 */
		protected static function _use_database($module, $id)
		{
			$use_database = array(
				'controller' => array('location_bookmark' => true),
				'phpgwapi' => array('bookmark_menu' => true)
				);
//			return !!$use_database[$module][$id];
			return empty($use_database[$module][$id]) ? false : true;
		}

		/**
		 * Clear stored data from shared memory
		 *
		 * @param string $key the data identifier
		 * @return bool was the data deleted?
		 */
		protected static function _file_clear($key)
		{
			$fn = self::_gen_filename($key);
			if ( is_file($fn) && is_writable($fn) )
			{
				return unlink($fn);
			}
			return true;
		}

		private static function _file_clear_all()
		{
			$serverSettings = Settings::getInstance()->get('server');
			$dir = new DirectoryIterator($serverSettings['temp_dir']); 

			if ( is_object($dir) )
			{
				foreach ( $dir as $file )
				{
					if ( $file->isDot()
						|| !$file->isFile()
						|| !$file->isReadable()
						|| strcasecmp(  substr($file->getFilename(),0, 12 ) , 'phpgw_cache_' ) != 0 )
 					{
						continue;
					}
					$file_path = $file->getPathname();
					unlink($file_path);
				}
			}
		}
		/**
		 * Retreive data from shared memory
		 *
		 * @param string $key the data identifier
		 * @return mixed the data from shared memory
		 */
		protected static function _file_get($key)
		{
			$fn = self::_gen_filename($key);
			if ( is_readable($fn) )
			{
				return file_get_contents($fn);
			}
			return null;
		}

		/**
		 * Store data in shared memory
		 *
		 * @param string $key the data identifier
		 * @param mixed $value the data to store
		 * @return bool was the data stored in shared memory
		 */
		protected static function _file_set($key, $value)
		{
			$fn = self::_gen_filename($key);
			return !!file_put_contents($fn, $value, LOCK_EX);
		}

		/**
		 * Generate the key for the data to be stored/retreived
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return string a unique hash for the data
		 */
		protected static function _gen_key($module, $id)
		{
			$serverSettings = Settings::getInstance()->get('server');

			return sha1("{$serverSettings['install_id']}::{$module}::{$id}");
		}

		/**
		 * Generate a filename for storing cached data
		 *
		 * @param string $key the data identifier
		 * @return string the filename for be used for caching data
		 */
		protected static function _gen_filename($key)
		{
			$serverSettings = Settings::getInstance()->get('server');

			return "{$serverSettings['temp_dir']}/phpgw_cache_{$key}";
		}

		/**
		 * Clear stored data from shared memory
		 *
		 * @param string $key the data identifier
		 * @return bool was the data deleted?
		 */
		protected static function _shm_clear($key)
		{
			return Shm::delete_key($key);
		}

		/**
		 * Retreive data from shared memory
		 *
		 * @param string $key the data identifier
		 * @return mixed the data from shared memory
		 */
		protected static function _shm_get($key)
		{
			return Shm::get_value($key);
		}

		/**
		 * Store data in shared memory
		 *
		 * @param string $key the data identifier
		 * @param mixed $value the data to store
		 * @return bool was the data stored in shared memory
		 */
		protected static function _shm_set($key, $value)
		{
			return Shm::store_value($key, $value);
		}

		/**
		 * Prepares a value for storage - all values must  be run through here before caching
		 *
		 * @param mixed the value to store
		 * @param bool $bypass to skip encryption
		 * @return string value to store as a string
		 */
		protected static function _value_prepare($value, $bypass = true)
		{
			$crypto = new Crypto();
			return $crypto->encrypt($value, $bypass);
		}

		/**
		 * Returns a value is a usable form - all values must be run through here before returning to the user
		 *
		 * @param string $str the string to process
		 * @param bool $bypass to skip encryption
		 * @return mixed the unserialized string
		 */
		protected static function _value_return($str, $bypass = true)
		{
			if ( is_null($str) )
			{
				return null;
			}

		// crypto class unserializes the data for us
			$crypto = new Crypto();
			return $crypto->decrypt($str, $bypass);
		}

		/**
		 * Clear a value from the session cache
		 *
		 * @param string $module the module to store the data
		 * @param string $id the identifier for the data
		 */
		public static function session_clear($module, $id)
		{
			$key = self::_gen_key($module, $id);
			if ( isset($_SESSION['phpgw_cache'][$key]) )
			{
				unset($_SESSION['phpgw_cache'][$key]);
			}
			// we don't really care if it is already not set
			return true;
		}

		/**
		 * Retreive data from session cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return mixed the data from session cache
		 */
		public static function session_get($module, $id)
		{
			$key = self::_gen_key($module, $id);
			if ( isset($_SESSION['phpgw_cache'][$key]) )
			{
				return self::_value_return($_SESSION['phpgw_cache'][$key], true);
			}
			return null;
		}

		/**
		 * Store data in the session cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param mixed $data the data to store
		 * @return bool was the data stored in the session cache?
		 */
		public static function session_set($module, $id, $data)
		{
			$key = self::_gen_key($module, $id);

			if($data)
			{
				$data = self::_value_prepare($data, true); // suhoshin is already encrypting the data
			}
			$_SESSION['phpgw_cache'][$key] = $data;
			return true;
		}

		/**
		 * Check for redis
		 */

		private static function _redis_enabled()
		{
			static $enabled = false;
			static $checked = false;
			if($checked)
			{
				return $enabled;
			}
			if(!self::$phpgwapi_redis)
			{
				self::get_redis();
			}
			$enabled = !! self::$phpgwapi_redis->get_is_connected();
			$checked = true;
			return $enabled;

		}
		/**
		 * Clear data stored in the system wide cache
		 *
		 * @return bool was the data deleted?
		 */
		public static function system_clear_all()
		{

			if ( self::_redis_enabled() )
			{
				self::$phpgwapi_redis->clear_cache();
			}
			else if ( Shm::is_enabled() )
			{
				Shm::clear_cache();
			}
			return self::_file_clear_all();
		}

		/**
		 * Clear data stored in the system wide cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return bool was the data deleted?
		 */
		public static function system_clear($module, $id)
		{
			$key = self::_gen_key($module, $id);

			if ( self::_redis_enabled() )
			{
				return self::$phpgwapi_redis->delete_key($key);
			}
			else if ( Shm::is_enabled() )
			{
				return self::_shm_clear($key);
			}
			return self::_file_clear($key);
		}

		/**
		 * Retreive data from system wide cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @return mixed the data from system wide cache
		 */
		public static function system_get($module, $id, $bypass = true, $compress = false)
		{
			$key = self::_gen_key($module, $id);

			if ( self::_redis_enabled() )
			{
				$value = self::$phpgwapi_redis->get_value($key);		
			}
			else if ( Shm::is_enabled() )
			{
				$value = self::_shm_get($key);
			}
			else
			{
				$value = self::_file_get($key);
			}

			if(!$value)
			{
				return null;
			}

			if(function_exists('gzcompress') && $compress)
			{
				$value =  self::_value_return(gzuncompress(base64_decode($value)), $bypass);
				return $value;
			}
			else
			{
				return self::_value_return($value, $bypass);
			}
		}

		/**
		 * Store data in the system wide cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param mixed $data the data to store
		 * @return bool was the data stored in the system wide cache?
		 */
		public static function system_set($module, $id, $value, $bypass = true, $compress = false)
		{
			$key = self::_gen_key($module, $id);
			$value = self::_value_prepare($value, $bypass);

			if(function_exists('gzcompress') && $compress)
			{
				$value =  base64_encode(gzcompress($value, 9));
			}

			if ( self::_redis_enabled() )
			{
				return self::$phpgwapi_redis->store_value($key, $value);				
			}

			if ( Shm::is_enabled() )
			{
				return self::_shm_set($key, $value);
			}
			return self::_file_set($key, $value);
		}

		/**
		 * Clear the data from the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param int $uid the user id the data is stored for
		 * @return bool was the data deleted?
		 */
		public static function user_clear($module, $id, $uid)
		{
			$db = self::_use_database($module, $id);
			if($db)
			{
				return self::_user_clear_db($module, $id, $uid);
			}
			else
			{
				return self::_user_clear($module, $id, $uid);			
			}
		}

		/**
		 * Retreive data from the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param int $uid the user id to the data is stored for
		 * @return mixed the data from user cache
		 */
		public static function user_get($module, $id, $uid, $bypass = true, $compress = false)
		{
			$db = self::_use_database($module, $id);
			if($db)
			{
				return self::_user_get_db($module, $id, $uid, $bypass, $compress);
			}
			else
			{
				return self::_user_get($module, $id, $uid, $bypass, $compress);
			}
		}

		/**
		 * Store data in the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param mixed $data the data to store in user cache
		 * @param int $uid the user id to store the data for
		 * @return bool was the data stored in the user cache?
		 */
		public static function user_set($module, $id, $value, $uid, $bypass = true, $compress = false)
		{
			$db = self::_use_database($module, $id);
			if($db)
			{
				return self::_user_set_db($module, $id, $value, $uid, $bypass, $compress);
			}
			else
			{
				return self::_user_set($module, $id, $value, $uid, $bypass, $compress);
			}
		}

		/**
		 * Clear the data from the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param int $uid the user id the data is stored for
		 * @return bool was the data deleted?
		 */
		protected static function _user_clear_db($module, $id, $uid)
		{
			$db = \App\Database\Db::getInstance();

			$key = $db->quote(self::_gen_key($module, $id));
			$uid = (int) $uid;

			$sql = "DELETE FROM phpgw_cache_user WHERE item_key = :key";

			// this is a bit of a hack, but we need some way of clearing cache values of all users - i am open to suggestions
			if ($uid <> -1) {
				$sql .= " AND user_id = :uid";
			}

			$stmt = $db->prepare($sql);
			$params = [':key' => $key];
			if ($uid <> -1) {
				$params[':uid'] = $uid;
			}

		return !!$stmt->execute($params);		}

		/**
		 * Retreive data from the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param int $uid the user id to the data is stored for
		 * @return mixed the data from user cache
		 */
		protected static function _user_get_db($module, $id, $uid, $bypass = true, $compress = true)
		{
			$db = \App\Database\Db::getInstance();

			$key = $db->quote(self::_gen_key($module, $id));
			$uid = (int) $uid;

			$ret = null;

			$sql = "SELECT cache_data FROM phpgw_cache_user WHERE user_id = :uid AND item_key = :key";
			$stmt = $db->prepare($sql);
			$stmt->execute([':uid' => $uid, ':key' => $key]);

			if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$ret = $row['cache_data'];
				if ($compress && function_exists('gzcompress')) {
					$ret =  gzuncompress(base64_decode($ret));
				} else {
					$ret = stripslashes($ret);
				}
				$ret = self::_value_return($ret, $bypass);
			}
			return $ret;
		}

		/**
		 * Store data in the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param mixed $data the data to store in user cache
		 * @param int $uid the user id to store the data for
		 * @return bool was the data stored in the user cache?
		 */
		protected static function _user_set_db($module, $id, $value, $uid, $bypass = true, $compress = true)
		{
			$uid = (int) $uid;

			if ($uid == 0)
			{
				return false;
			}
			$db = \App\Database\Db::getInstance();

			$key = $db->quote(self::_gen_key($module, $id));
			$value = self::_value_prepare($value, $bypass);
			if($compress && function_exists('gzcompress'))
			{
				$value =  base64_encode(gzcompress($value, 9));
			}
			else
			{
				$value = $db->quote($value);
			}

			$now = time();

			$stmt = $db->prepare("SELECT user_id FROM phpgw_cache_user WHERE item_key = :key AND user_id = :uid");
			$stmt->execute([':key' => $key, ':uid' => $uid]);

			if ($stmt->fetch(PDO::FETCH_ASSOC))
			{
				$sql = 'UPDATE phpgw_cache_user'
					. " SET cache_data = :value, lastmodts = :now"
					. " WHERE item_key = :key AND user_id = :uid";
			}
			else
			{
				$sql = "INSERT INTO phpgw_cache_user (item_key, user_id, cache_data, lastmodts) VALUES(:key, :uid, :value, :now)";
			}

			$stmt = $db->prepare($sql);
			$params = [':key' => $key, ':uid' => $uid, ':value' => $value, ':now' => $now];

			return !!$stmt->execute($params);
		}

		/**
		 * Clear the data from the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param int $uid the user id the data is stored for
		 * @return bool was the data deleted?
		 */
		protected static function _user_clear($module, $id, $uid)
		{
			$uid = (int) $uid;
			$module = $module . '_' . $uid;

			$key = self::_gen_key($module, $id);

			if ( Shm::is_enabled() )
			{
				return self::_shm_clear($key);
			}
			return self::_file_clear($key);
		}

		/**
		 * Retreive data from the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param int $uid the user id to the data is stored for
		 * @return mixed the data from user cache
		 */
		static function _user_get($module, $id, $uid, $bypass = true, $compress = false)
		{
			$uid = (int) $uid;
			$module = $module . '_' . $uid;
			$key = self::_gen_key($module, $id);

			if ( Shm::is_enabled() )
			{
				$value = self::_shm_get($key);
			}
			else
			{
				$value = self::_file_get($key);
			}

			if(!$value)
			{
				return null;
			}

			if(function_exists('gzcompress') && $compress)
			{
				$value =  self::_value_return(gzuncompress(base64_decode($value)), $bypass);
				return $value;
			}
			else
			{
				return self::_value_return($value, $bypass);
			}
		}

		/**
		 * Store data in the user cache
		 *
		 * @param string $module the module name the data belongs to
		 * @param string $id the internal module id for the data
		 * @param mixed $data the data to store in user cache
		 * @param int $uid the user id to store the data for
		 * @return bool was the data stored in the user cache?
		 */
		protected static function _user_set($module, $id, $value, $uid, $bypass = true, $compress = false)
		{
			$uid = (int) $uid;

			if ($uid == 0)
			{
				return false;
			}

			$module = $module . '_' . $uid;
			$key = self::_gen_key($module, $id);
			$value = self::_value_prepare($value, $bypass);

			if(function_exists('gzcompress') && $compress)
			{
				$value =  base64_encode(gzcompress($value, 9));
			}

			if ( Shm::is_enabled() )
			{
				return self::_shm_set($key, $value);
			}
			return self::_file_set($key, $value);
		}

		/**
		 * Store system message in the session cache
		 *
		 * @param string or array $message the message set to register
		 * @param string $type the type (error/message) of message
		 * @return bool was the data stored in the session cache?
		 */
		public static function message_set($message, $type = 'message')
		{
			if(!$type == 'message')
			{
				$type == 'error';
			}
			$receipt = self::session_get('phpgwapi', 'phpgw_messages');
			if(!is_array($receipt))
			{
				$receipt =array();
			}
			
			if(!is_array($message))
			{
				$_input = array($message);
			}
			else
			{
				$_input = $message;
			}
			foreach($_input as $msg)
			{
				$receipt[$type][]=array('msg'=> $msg);
			}

			return !!self::session_set('phpgwapi', 'phpgw_messages', $receipt);
		}

		/**
		 * GET system message from the session cache
		 *
		 * @param bool clear flag
		 * @return array containing messages
		 */
		public static function message_get($clear = false)
		{
			$messages =  self::session_get('phpgwapi', 'phpgw_messages');
			if($clear)
			{
				self::session_clear('phpgwapi', 'phpgw_messages');
			}
			return $messages;
		}
	}