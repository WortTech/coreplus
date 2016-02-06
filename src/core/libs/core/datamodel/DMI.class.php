<?php
/**
 * Provides the main interface system for the DMI subsystem.
 * 
 * @package Core\Datamodel
 * @since 0.1
 * @author Charlie Powell <charlie@evalagency.com>
 * @copyright Copyright (C) 2009-2016  Charlie Powell
 * @license GNU Affero General Public License v3 <http://www.gnu.org/licenses/agpl-3.0.txt>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/agpl-3.0.txt.
 */


// GLOBAL NAMESPACE!


// I has some dependencies...
//define('__DMI_PDIR', dirname(__FILE__) . '/');
//require_once(__DMI_PDIR . 'DMI_Backend.interface.php');
//require_once(__DMI_PDIR . 'Dataset.class.php');

define('__DMI_PDIR', ROOT_PDIR . 'core/libs/core/datamodel/');
require_once(ROOT_PDIR . 'core/libs/core/datamodel/' . 'BackendInterface.php');
require_once(ROOT_PDIR . 'core/libs/core/datamodel/' . 'Dataset.php');
require_once(ROOT_PDIR . 'core/libs/core/datamodel/' . 'DatasetWhere.php');
require_once(ROOT_PDIR . 'core/libs/core/datamodel/' . 'DatasetWhereClause.php');
require_once(ROOT_PDIR . 'core/libs/core/datamodel/' . 'Schema.php');
require_once(ROOT_PDIR . 'core/libs/core/datamodel/' . 'DatasetStream.php');


/**
 * A top level interface class for the Data Model Interface.
 * Provides abstraction for different backends.
 *
 */
class DMI {
	
	/** @var Core\Datamodel\DMI_Backend The backend currently in use for this DMI object. */
	protected $_backend = null;
	
	/**
	 * This points to the system/global DMI object.
	 * 
	 * @var DMI
	 */
	static protected $_Interface = null;
	
	public function __construct($backend = null, $host = null, $user = null, $pass = null, $database = null){
		// Provide shortcut to set the backend directly in the constructor.
		if($backend) $this->setBackend($backend);
		
		// Provide shortcut to set connection information directly in the constructor.
		if($host) $this->connect($host, $user, $pass, $database);
	}
	
	public function setBackend($backend){
		if($this->_backend) throw new DMI_Exception('Backend already set');

		// All backends are lowercase.
		$backend     = strtolower($backend);
		$class       = 'Core\\Datamodel\\Drivers\\' . $backend . '\\' . $backend . '_backend';
		$backendfile = $backend . '.backend.php';
		$schemafile  = $backend . '.schema.php';

		if(!file_exists(__DMI_PDIR . 'drivers/' . $backend . '/' . $backendfile)){
			throw new DMI_Exception('Could not locate backend file for ' . $class);
		}
		require_once(__DMI_PDIR . 'drivers/' . $backend . '/' . $backendfile);

		// Include the schemas too?
		if(file_exists(__DMI_PDIR . 'drivers/' . $backend . '/' . $schemafile)){
			require_once(__DMI_PDIR . 'drivers/' . $backend . '/' . $schemafile);
		}

		
		$this->_backend = new $class();
	}

	/**
	 * @param $host
	 * @param $user
	 * @param $pass
	 * @param $database
	 *
	 * @throws DMI_Exception
	 * @throws DMI_Authentication_Exception
	 *
	 * @return \Core\Datamodel\BackendInterface|null
	 */
	public function connect($host, $user, $pass, $database){
		$this->_backend->connect($host, $user, $pass, $database);
		
		return $this->_backend;
	}

	/**
	 * @return \Core\Datamodel\BackendInterface
	 */
	public function connection(){
		return $this->_backend;
	}
	
	
	/**
	 * Get the current system DMI based on configuration values.
	 *
	 * @throws DMI_Exception
	 * @throws DMI_Authentication_Exception
	 *
	 * @return DMI
	 */
	public static function GetSystemDMI(){
		if(self::$_Interface !== null) return self::$_Interface;
		
		self::$_Interface = new DMI();
		

		if(file_exists(ROOT_PDIR . 'config/configuration.xml')){
			// Because this is the system data connection, I also need to pull the settings automatically.
			// This will only be done if the configuration file exists.
			$cs = ConfigHandler::LoadConfigFile("configuration");
		}
		elseif(\Core\Session::Get('configs/*') !== null){
			// If the file doesn't exist, (ie: during installation), I need to check the session data.
			$cs = \Core\Session::Get('configs/*');
		}
		else{
			throw new DMI_Exception('No database settings defined for the DMI');
		}

		self::$_Interface->setBackend($cs['database_type']);
		
		self::$_Interface->connect($cs['database_server'], $cs['database_user'], $cs['database_pass'], $cs['database_name']);
		
		return self::$_Interface;
	}
	
}


// @TODO Break these out into their own file at some point in time.

class DMI_Exception extends Exception{
	const ERRNO_NODATASET = '42S02';
	const ERRNO_UNKNOWN = '07000';
	
	public $ansicode;
	
	public function __construct($message, $code = null, $previous = null, $ansicode = null) {
		parent::__construct($message, $code, $previous);
		
		if($ansicode) $this->ansicode = $ansicode;
		elseif($code) $this->ansicode = $code;
	}
}

class DMI_Authentication_Exception extends DMI_Exception{
	
}

class DMI_ServerNotFound_Exception extends DMI_Exception{
	
}

class DMI_Query_Exception extends DMI_Exception{
	/**
	 * The query that caused the exception.
	 * @var string
	 */
	public $query = null;
}
