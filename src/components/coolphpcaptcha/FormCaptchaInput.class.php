<?php
/**
 * File for the class FormCaptchaInput
 *
 * @package CoolPHPCaptcha
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
 * Class FormCaptchaInput
 *
 * Displays the system captcha as a form input.
 * Handles all the validation automatically.
 */
class FormCaptchaInput extends \Core\Forms\FormElement{
	public function  __construct($atts = null) {
		parent::__construct($atts);

		// Some defaults
		$this->_attributes['class'] = 'formelement formcaptchainput';
		if(!$this->get('name')){
			$this->set('name', 'captcha');
		}

		$this->_validattributes = array('id', 'name', 'required', 'tabindex', 'style');
	}
	
	public function render() {
		if(!isset($this->_attributes['title'])) $this->_attributes['title'] = ConfigHandler::Get('/captcha/formtext');
		
		return parent::render();
	}
	
	public function setValue($value) {
		if(!$value){
			$this->_error = $this->get('title') . ' is required.';
			return false;
		}
		if($value != \Core\Session::Get('captcha')){
			$this->_error = $this->get('title') . ' does not match image.';
			return false;
		}
		
		parent::setValue('');
	}
}