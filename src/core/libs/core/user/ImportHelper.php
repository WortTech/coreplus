<?php
/**
 * File for class ImportHelper definition in the coreplus project
 * 
 * @package User
 * @author Charlie Powell <charlie@evalagency.com>
 * @date 20130419.1256
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

namespace Core\User;
use Core\Filestore\Factory;
use Core\Session;


/**
 * A short teaser of what ImportHelper does.
 *
 * More lengthy description of what ImportHelper does and why it's fantastic.
 *
 * <h3>Usage Examples</h3>
 *
 *
 * @todo Write documentation for ImportHelper
 * <h4>Example 1</h4>
 * <p>Description 1</p>
 * <code>
 * // Some code for example 1
 * $a = $b;
 * </code>
 *
 *
 * <h4>Example 2</h4>
 * <p>Description 2</p>
 * <code>
 * // Some code for example 2
 * $b = $a;
 * </code>
 *
 * 
 * @package User
 * @author Charlie Powell <charlie@evalagency.com>
 *
 */
abstract class ImportHelper {
	/**
	 * Handler to save the CSV file locally.
	 *
	 * @param \Form $form
	 *
	 * @return bool
	 */
	public static function FormHandler1(\Core\Forms\Form $form) {

		// If it's gotten here, all the form validation has succeeded!
		// Pretty simple eh? :p
		/** @var $el \FormFileInput */
		$el = $form->getElement('file');
		$file = $el->getFile();

		Session::Set('user-import', [
			'file' => $file->getFilename(),
			'key' => \Core\random_hex(10),
		]);
		return true;
	}

	/**
	 * Handler to actually perform the import.
	 *
	 * @param \Form $form
	 * @return bool
	 */
	public static function FormHandler2(\Core\Forms\Form $form) {
		$filename = Session::Get('user-import/file');
		$file = Factory::File($filename);
		/** @var $contents \Core\Filestore\Contents\ContentCSV */
		$contents = $file->getContentsObject();

		// If the user checked that it has a header... do that.
		$contents->_hasheader = $form->getElement('has_header')->get('checked');

		// Merge
		$merge = $form->getElement('merge_duplicates')->get('checked');

		// Handle the map-to directives.
		$maptos = array();
		foreach($form->getElements() as $el){
			if(strpos($el->get('name'), 'mapto[') === 0 && $el->get('value')){
				$k = substr($el->get('name'), 6, -1);
				$maptos[$k] = $el->get('value');
			}
		}

		// Handle the group mappings
		$groups = $form->getElement('groups[]')->get('value');

		// And keep a log of the bad transfers and some other data.
		$counts = ['created' => 0, 'updated' => 0, 'failed' => 0, 'skipped' => 0];
		Session::Set('user-import/fails', []);

		$incoming = $contents->parse();
		foreach($incoming as $record){
			try{
				// Create a data map of this record for fields to actually map over.
				$dat = array();
				foreach($maptos as $recordkey => $userkey){
					$dat[$userkey] = $record[$recordkey];
				}

				// No email, NO IMPORT!
				if(!$dat['email']){
					$counts['skipped']++;
					continue;
				}

				// Try to find this record by email, since that's a primary key.
				$existing = \UserModel::Find(['email = ' . $dat['email'] ], 1);
				if($existing && !$merge){
					// Skip existing records.
					$counts['skipped']++;
				}
				elseif($existing){
					// Update!
					$existing->setFromArray($dat);
					$existing->setGroups($groups);

					if($existing->save()){
						$counts['updated']++;
					}
					else{
						$counts['skipped']++;
					}
				}
				else{
					$new = new \UserModel();
					$new->setFromArray($dat);
					$new->setGroups($groups);
					$new->save();
					$counts['created']++;
				}
			}
			catch(\Exception $e){
				// @todo Handle this
				die($e->getMessage());
			}
			//
		}

		Session::Set('user-import/counts', $counts);

		return true;
	}
}