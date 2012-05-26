<?php
/**
 * [PAGE DESCRIPTION HERE]
 *
 * @package Core Plus\Core
 * @since 1.9
 * @author Charlie Powell <charlie@eval.bz>
 * @copyright Copyright (C) 2009-2012  Charlie Powell
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

class Widget_2_1 {

	/**
	 * The view that gets returned when pages are executed.
	 *
	 * @var View
	 */
	private $_view = null;

	/**
	 * The WidgetInstance for this request.  Every widget MUST be instanced on some widgetarea.
	 *
	 * @var WidgetInstanceModel
	 */
	public $_model = null;


	/**
	 * Get the view for this controller.
	 * Up to the extending Controller to use this object is it wishes.
	 *
	 * @return View
	 */
	public function getView() {
		if ($this->_view === null) {

			$this->_view              = new View();
			$this->_view->contenttype = View::CTYPE_HTML;
			$this->_view->mode        = View::MODE_WIDGET;
			if ($this->getWidgetModel()) {
				// easy way
				$this->_view->baseurl = $this->getWidgetModel()->get('baseurl');
			}
			else {
				// difficult way
				$back = debug_backtrace();
				$cls  = $back[1]['class'];
				if (strpos($cls, 'Widget') !== false) $cls = substr($cls, 0, -6);
				$mth                  = $back[1]['function'];
				$this->_view->baseurl = $cls . '/' . $mth;
			}
		}

		return $this->_view;
	}


	/**
	 * Get the page model for the current page.
	 *
	 * @return WidgetInstanceModel
	 */
	public function getWidgetModel() {
		return $this->_model;
	}


	/**
	 * Set the access string for this view and do the access checks against the
	 * currently logged in user.
	 *
	 * Will also set the access string on the PageModel, since it needs to be reflected in the database.
	 *
	 * @since 2012.01
	 * @version 2.1
	 *
	 * @param string $accessstring
	 *
	 * @return boolean True or false based on access for current user.
	 */
	protected function setAccess($accessstring) {
		// Update the model
		$this->getWidgetModel()->set('access', $accessstring);

		return (\Core\user()->checkAccess($accessstring));
	}

	protected function setTemplate($template) {
		$this->getView()->templatename = $template;
	}

	protected function getParameter($param) {
		$dat = $this->getWidgetModel()->splitParts();
		return (isset($dat['parameters'][$param])) ? $dat['parameters'][$param] : null;
	}


	/**
	 * Return a valid Widget.
	 *
	 * This is used because new $pagedat['controller'](); cannot provide typecasting :p
	 *
	 * @param string $name
	 *
	 * @return Widget_2_1
	 */
	public static function Factory($name) {
		return new $name();
	}

}
