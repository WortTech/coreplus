<?php
/**
 * File for class ModelHelper definition in the tenant-visitor project
 * 
 * @package Core\Search
 * @author Charlie Powell <charlie@evalagency.com>
 * @date 20131126.1744
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

namespace Core\Search;
use Core\Datamodel\DatasetWhereClause;


/**
 * A short teaser of what ModelHelper does.
 *
 * More lengthy description of what ModelHelper does and why it's fantastic.
 *
 * <h3>Usage Examples</h3>
 *
 *
 * @todo Write documentation for ModelHelper
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
 * @package Core\Search
 * @author Charlie Powell <charlie@evalagency.com>
 *
 */
class Helper {
	/**
	 * Get an array of words to skip in search and indexing.
	 *
	 * @return array
	 */
	public static function GetSkipWords(){
		return ['a', 'an', 'but', 'for', 'nor', 'of', 'or', 'so', 'the', 'to', 'yet'];
	}

	/**
	 * Hook handler to save the index data for a given model record.
	 *
	 * @param \Model $model
	 *
	 * @return bool
	 */
	public static function ModelPreSaveHandler(\Model $model){
		// Only process models that have a search index on them.
		if(!$model::$HasSearch) return true;

		$str = $model->getSearchIndexString();

		// Lowercase it.
		$str = strtolower($str);

		// Convert this string to latin.
		$str = \Core\str_to_latin($str);

		// Contractions get dropped with no space.
		$str = str_replace("'", '', $str);

		// Skip punctuation.
		$str = preg_replace('/[^a-z0-9 ]/', ' ', $str);

		$parts = explode(' ', $str);

		// Conjunction words to skip
		$skips = self::GetSkipWords();
		
		// Generate the plain text index and only run the doublemetaphone conversion if it changes.
		// This is because that function is painfully slow, so the less it runs the better!
		foreach($parts as $k => $word){
			if(!$word){
				// Skip blank words
				unset($parts[$k]);
			}
			elseif(in_array($word, $skips)){
				// Skip "skip" words
				unset($parts[$k]);	
			}
			// OK to add, no else needed here.
		}
		
		// Strip uniques
		$parts = array_unique($parts);
		
		$model->set('search_index_str', implode(' ', $parts));
		
		if($model->changed('search_index_str')){
			// It changed, NOW generate the doublemetaphone conversion strings.
			$primary = [];
			$secondary = [];
			foreach($parts as $k => $word){
				$it = new DoubleMetaPhone($word);
				$primary[] = $it->primary;
				$secondary[] = $it->secondary;
			}
			
			$model->set('search_index_pri', implode(' ', $primary));
			$model->set('search_index_sec', implode(' ', $secondary));
		}

		return true;
	}

	/**
	 * Translate a query string to a populated where clause based on the search index criteria.
	 *
	 * @param string $query
	 *
	 * @return DatasetWhereClause
	 */
	public static function GetWhereClause($query){
		$subwhere = new DatasetWhereClause('search');
		$subwhere->setSeparator('or');

		// Lowercase it.
		$query       = strtolower($query);
		// Convert this string to latin.
		$query       = \Core\str_to_latin($query);
		// Skip punctuation.
		$query       = preg_replace('/[^a-z0-9 ]/', '', $query);
		// Split out words.
		$parts       = explode(' ', $query);
		$skips       = self::GetSkipWords();
		$indexes     = [];
		$primaries   = [];
		$secondaries = [];

		foreach($parts as $word){
			if(in_array($word, $skips)) continue;
			if(!$word) continue;

			$it = new DoubleMetaPhone($word);

			$indexes[]     = $word;
			$primaries[]   = $it->primary;
			$secondaries[] = $it->secondary;
		}

		// Remove duplicates
		$indexes = array_unique($indexes);
		$primaries = array_unique($primaries);
		$secondaries = array_unique($secondaries);

		// And add a where clause for each one.
		foreach($indexes as $word){
			if($word) {
				// Required to skip spaces.
				$subwhere->addWhere('search_index_str LIKE %' . $word . '%');
			}
		}
		foreach($primaries as $word){
			if($word){
				// Required to skip spaces.
				$subwhere->addWhere('search_index_pri LIKE %' . $word . '%');
			}
		}
		foreach($secondaries as $word){
			if($word){
				// Required to skip spaces.
				$subwhere->addWhere('search_index_sec LIKE %' . $word . '%');
			}
		}

		return $subwhere;
	}
} 
