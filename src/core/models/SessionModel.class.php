<?php
/**
 * Defines the schema for the Session table
 *
 * @package Core
 * @author Charlie Powell <charlie@evalagency.com>
 * @copyright Copyright (C) 2009-2017  Charlie Powell
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


/**
 * Model for SessionModel
 *
 * Generated automatically from the mysql_model_gen script.
 * Please update result to your preferences and copy to the final location.
 *
 * @author Charlie Powell <charlie@evalagency.com>
 * @date 2011-07-24
 */
class SessionModel extends Model {
	public static $Schema = array(
		'session_id' => array(
			'type'      => Model::ATT_TYPE_STRING,
			'maxlength' => 160,
			'required'  => true,
			'null'      => false,
		),
		'user_id'    => array(
			'type'    => Model::ATT_TYPE_UUID_FK,
			'default' => 0,
		),
		'ip_addr'    => array(
			'type'      => Model::ATT_TYPE_STRING,
			'maxlength' => 39,
		),
		'data'       => array(
			'type'    => Model::ATT_TYPE_DATA,
			'default' => null,
			'null'    => true,
			'encoding' => Model::ATT_ENCODING_GZIP,
		),
		'external_data' => array(
			'type' => Model::ATT_TYPE_DATA,
			'comment' => 'JSON-encoded array of any external data set onto this session.',
			'encoding' => Model::ATT_ENCODING_JSON,
			'default' => null,
			'null' => true,
		),
		'created'    => array(
			'type' => Model::ATT_TYPE_CREATED
		),
		'updated'    => array(
			'type' => Model::ATT_TYPE_UPDATED
		)
	);

	public static $Indexes = array(
		'primary' => array('session_id'),
	);

	public function __construct($key = null){
		return parent::__construct($key);
	}

	/**
	 * Get the data for this session.  Useful for compression :p
	 * 
	 * as of 5.1.0, this is handled natively.
	 */
	public function getData() {
		return $this->get('data');
	}

	/**
	 * Get the JSON-decoded data of the external data on this session.
	 * 
	 * as of 5.1.0, this is handled natively.
	 *
	 * @return array
	 */
	public function getExternalData(){
		return $this->get('external_data');
	}

	/**
	 * Set the data for this session.  This will automatically compress the contents.
	 * 
	 * as of 5.1.0, this is handled natively.
	 *
	 * @param $data mixed Uncompressed data
	 */
	public function setData($data) {
		return $this->set('data', $data);
	}

	/**
	 * Set data on this session from an external script or source.
	 * 
	 * as of 5.1.0, this is handled natively.
	 *
	 * @param array $data External data to set
	 */
	public function setExternalData($data){
		return $this->set('external_data', $data);
	}
/*
	public function save(){
		// I need to do this here because sessions have a tendency of getting overwritten very quickly.
		// This will sometimes cause "Primary key already exists" errors in the error log
		if($this->exists()){
			return parent::_saveExisting(true);
		}
		else{
			return parent::_saveNew();
		}
	}
*/

}
