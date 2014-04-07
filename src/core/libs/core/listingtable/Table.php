<?php
/**
 * File for class Table definition in the coreplus project
 * 
 * @package Core\ListingTable
 * @author Charlie Powell <charlie@eval.bz>
 * @date 20140406.2004
 * @copyright Copyright (C) 2009-2014  Charlie Powell
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

namespace Core\ListingTable;


/**
 * A short teaser of what Table does.
 *
 * More lengthy description of what Table does and why it's fantastic.
 *
 * <h3>Usage Examples</h3>
 *
 *
 * @todo Write documentation for Table
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
 * @package Core\ListingTable
 * @author Charlie Powell <charlie@eval.bz>
 *
 */
class Table implements \Iterator {

	/** @var string The session / key name of this listing table. */
	private $_name;

	/** @var \ModelFactory|null The underlying factory to control this listing table. */
	private $_modelFactory;

	/** @var \FilterForm|null The underlying FilterForm to handle pagination and filters on this listing table. */
	private $_filters;

	/** @var array The columns on this listing table. */
	private $_columns = [];

	/** @var bool Set to true when the filters have been applied to the Model, (set automatically). */
	private $_applied = false;

	/** @var bool Set automatically when a column is added with a sortable flag. */
	private $_hassort = false;

	/** @var array|null Array of the results, pulled directly from the ModelFactory's get method. */
	private $_results;

	public function __construct(){
		// Set the name by default to the system request's baseurl.
		// This is usually a safe default, but can be set by the caller script if desired.
		$request = \Core\page_request();
		$this->setName($request->getBaseURL());
	}


	//-----------------------------------------------------------------------\\
	//                                  GETTERS
	//-----------------------------------------------------------------------\\

	/**
	 * Get the underlying ModelFactory for this listing table, or null if none exists.
	 *
	 * @return \ModelFactory|null
	 */
	public function getModelFactory(){
		return $this->_modelFactory;
	}

	/**
	 * Get the underlying FilterForm object to control this listing table's pagination and filters.
	 *
	 * Will auto-create one if it doesn't exist.
	 *
	 * @return \FilterForm
	 */
	public function getFilters(){
		if($this->_filters === null){
			$this->_filters = new \FilterForm();
			$this->_filters->setName($this->_name);
			// Listing tables by default have pagination and sorting!
			$this->_filters->haspagination = true;
			$this->_filters->hassort = true;
		}

		return $this->_filters;
	}

	/**
	 * Get the value of the corresponding Filter element.
	 *
	 * @param $filter string
	 * @return mixed
	 */
	public function getFilterValue($filter){
		return $this->getFilters()->get($filter);
	}

	/**
	 * Get any/all the listings on this table.
	 *
	 * @return array|\Model|null
	 */
	public function getListings(){
		if(!$this->_applied){
			$this->getFilters()->applyToFactory($this->getModelFactory());
			$this->_results = $this->getModelFactory()->get();
			$this->_applied = true;
		}

		return $this->_results;
	}

	/**
	 * Get the total count for the number of records filtered.
	 *
	 * @return int|null
	 */
	public function getTotalCount(){
		if(!$this->_applied){
			$this->getFilters()->applyToFactory($this->getModelFactory());
			$this->_results = $this->getModelFactory()->get();
			$this->_applied = true;
		}

		return $this->getFilters()->getTotalCount();
	}



	//-----------------------------------------------------------------------\\
	//                                  SETTERS
	//-----------------------------------------------------------------------\\

	/**
	 * Add a new filter for this listing table.
	 *
	 * @param string $type
	 * @param array $atts
	 */
	public function addFilter($type, $atts){
		$form = $this->getFilters();
		$form->addElement($type, $atts);
	}

	/**
	 * Add a new Column onto this Listing Table.
	 *
	 * Will handle the filter sortable work automatically.
	 *
	 * @param string      $title
	 * @param string|null $sortkey
	 * @param boolean     $hidden
	 */
	public function addColumn($title, $sortkey = null, $hidden = false){
		$c = new Column();
		$c->title = $title;
		$c->sortkey = $sortkey;
		$c->hidden = $hidden;
		$this->_columns[] = $c;

		if($sortkey){
			$f = $this->getFilters();
			$f->addSortKey($sortkey);
			$this->_hassort = true;
		}
	}

	/**
	 * Set the model name, (and the underlying Factory object).
	 *
	 * @param string $name
	 */
	public function setModelName($name){
		$this->_modelFactory = new \ModelFactory($name);
	}

	/**
	 * Set the name for this table (and the corresponding filters), required for any session saving/lookup.
	 *
	 * @param string $name
	 */
	public function setName($name){
		$this->_name = $name;

		if($this->_filters !== null){
			$this->_filters->setName($this->_name);
		}
	}



	//-----------------------------------------------------------------------\\
	//                                  OTHER/ACTIONABLE
	//-----------------------------------------------------------------------\\

	/**
	 * Load the underlying Filters from a given request, (optionally).
	 *
	 * @param \PageRequest|null $request
	 */
	public function loadFiltersFromRequest($request = null){
		if($request === null){
			$request = \Core\page_request();
		}

		$this->getFilters()->load($request);
	}

	public function render($section){
		if(!$this->_applied){
			$this->getFilters()->applyToFactory($this->getModelFactory());
			$this->_results = $this->getModelFactory()->get();
			$this->_applied = true;
		}

		switch($section){
			case 'head':
				return $this->_renderHead();
			case 'foot':
				return $this->_renderFoot();
			case 'filters':
				return $this->_renderFilters();
			case 'pagination':
				return $this->_renderPagination();
			default:
				return 'Unknown section to render for this listing table, [' . $section . ']';
		}
	}



	//-----------------------------------------------------------------------\\
	//                            ITERATOR ACCESS
	//-----------------------------------------------------------------------\\

	public function rewind() {
		reset($this->_results);
	}

	public function current() {
		return current($this->_results);
	}

	public function key() {
		return key($this->_results);
	}

	public function next() {
		next($this->_results);
	}

	public function valid() {
		return isset($this->_results[key($this->_results)]);
	}

	//-----------------------------------------------------------------------\\
	//                              PRIVATE
	//-----------------------------------------------------------------------\\


	/**
	 * Render this table's head content, (everything above the records).
	 *
	 * @return string Full HTML Markup.
	 */
	private function _renderHead(){
		$out = '';

		$f = $this->getFilters();

		if(!$this->_hassort){
			// One final check for if these filters are sortable.
			$f->hassort = false;
		}

		$out .= $this->_renderFilters() . $this->_renderPagination();

		$tableclasses = ['listing'];
		if($this->_hassort){
			$tableclasses[] = 'column-sortable';
		}

		$out .= '<table class="' . implode(' ', $tableclasses) . '"><colgroup>';
		foreach($this->_columns as $c){
			/** @var Column $c */
			$out .= '<col class="' . $c->getClass() . '"/>';
		}
		$out .= '<col class="column-controls"/>';

		$out .= '</colgroup><tr>';
		foreach($this->_columns as $c){
			/** @var Column $c */
			$out .= $c->getTH();
		}

		// One extra column for the control links.
		$out .= '<th>&nbsp;</th>';
		$out .= '</tr>';

		return $out;
	}

	/**
	 * Render the filters
	 *
	 * @return string Full HTML Markup.
	 */
	private function _renderFilters(){
		$out = '';

		$f = $this->getFilters();

		if(!$this->_hassort){
			// One final check for if these filters are sortable.
			$f->hassort = false;
		}

		$out .= '<div class="screen">' . $f->render() . '</div>';
		$out .= '<div class="print">' . $f->renderReadonly() . '</div>';

		return $out;
	}

	/**
	 * Render the pagination options.
	 *
	 * @return string
	 */
	private function _renderPagination(){
		return $this->getFilters()->pagination();
	}

	/**
	 * Render this table's foot content, (everything below the records).
	 * @return string Full HTML Markup
	 */
	private function _renderFoot(){
		$out = '</table>';

		$f = $this->getFilters();
		$out .= $f->pagination();

		return $out;
	}
} 