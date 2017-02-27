<?php
/**
 * File for class PiwikController definition in the coreplus project
 * 
 * @author Charlie Powell <charlie@evalagency.com>
 * @date 20130619.0307
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


/**
 * A short teaser of what PiwikController does.
 *
 * More lengthy description of what PiwikController does and why it's fantastic.
 *
 * <h3>Usage Examples</h3>
 *
 *
 * @todo Write documentation for PiwikController
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
 * @author Charlie Powell <charlie@evalagency.com>
 *
 */
class PiwikController extends Controller_2_1 {
	public function configure(){
		$view    = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('g:admin')){
			return View::ERROR_ACCESSDENIED;
		}

		if($request->isPost()){
			\ConfigHandler::Set('/piwik/server/host', $_POST['server_host']);
			\ConfigHandler::Set('/piwik/siteid', $_POST['site_id']);
			\ConfigHandler::Set('/piwik/tracking/all_subdomains', $_POST['all_domains']);
			\ConfigHandler::Set('/piwik/tracking/domain_title', $_POST['domain_title']);

			\Core\set_message('Updated Piwik settings successfully', 'success');
			\Core\reload();
		}

		$form = new \Core\Forms\Form();
		$form->addElement(
			'text',
			[
				'name' => 'server_host',
				'title' => 'Server Host',
				'required' => false,
				'value' => \ConfigHandler::Get('/piwik/server/host'),
				'description' => 'Enter the hostname of your Piwik server without the protocol',
			]
		);
		$form->addElement(
			'text',
			[
				'name' => 'site_id',
				'title' => 'Site ID',
				'required' => false,
				'value' => \ConfigHandler::Get('/piwik/siteid'),
				'description' => 'Enter the Site ID of this installation',
			]
		);

		$form->addElement(
			'checkbox',
			[
				'name' => 'all_domains',
				'title' => 'Track visitors across all subdomains of your site',
				'description' => 'So if one visitor visits x.corepl.us and y.corepl.us, they will be counted as a single unique visitor.',
				'value' => '1',
				'checked' => \ConfigHandler::Get('/piwik/tracking/all_subdomains'),
			]
		);

		$form->addElement(
			'checkbox',
			[
				'name' => 'domain_title',
				'title' => 'Prepend the site domain to the page title when tracking',
				'description' => 'So if someone visits the "About" page on blog.corepl.us it will be recorded as "blog / About". This is the easiest way to get an overview of your traffic by sub-domain. ',
				'value' => '1',
				'checked' => \ConfigHandler::Get('/piwik/tracking/domain_title'),
			]
		);

		$form->addElement('submit', ['name' => 'submit', 'value' => 'Update']);

		$view->title = 'Piwik Analytics';
		$view->assign('form', $form);
	}
}