<?php
use Core\CLI\CLI;

/**
 * Admin controller, handles all /Admin requests
 *
 * @package Core
 * @author Charlie Powell <charlie@evalagency.com>
 * @copyright Copyright (C) 2009-2015  Charlie Powell
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

class AdminController extends Controller_2_1 {

	public function __construct() {

	}

	/**
	 * Display the admin dashboard.
	 *
	 * This page is primarily made up of widgets added by other systems.
	 *
	 * @return int
	 */
	public function index() {

		$view     = $this->getView();
		$pages    = PageModel::Find(array('admin' => '1'));
		$viewable = array();

		foreach ($pages as $p) {
			if (!\Core\user()->checkAccess($p->get('access'))) continue;

			$viewable[] = $p;
		}

		// If there are no viewable pages... don't display any admin dashboard.
		if(!sizeof($viewable)){
			return View::ERROR_ACCESSDENIED;
		}

		$view->title = 'Admin Dashboard';
		$view->assign('links', $viewable);

		// Dispatch the hook that other systems can hook into and perform checks or operations on the admin dashboard page.
		HookHandler::DispatchHook('/core/admin/view');
	}

	/**
	 * Run through and reinstall all components and themes.
	 *
	 * @return int
	 */
	public function reinstallAll() {
		// Admin-only page.
		if(!\Core\user()->checkAccess('g:admin')){
			return View::ERROR_ACCESSDENIED;
		}

		// Just run through every component currently installed and reinstall it.
		// This will just ensure that the component is up to date and correct as per the component.xml metafile.
		$view    = $this->getView();
		$request = $this->getPageRequest();


		if($request->isPost()){
			$view->mode = View::MODE_NOOUTPUT;
			$view->contenttype = View::CTYPE_HTML;
			$view->record = false;
			$view->templatename = null;
			$view->render();

			// Try to perform the reinstall.
			$changes  = array();
			$errors   = array();
			$allpages = [];

			$t = ThemeHandler::GetTheme();

			CLI::PrintHeader('Reinstalling Theme ' . $t->getName());
			if (($change = $t->reinstall(1)) !== false) {

				SystemLogModel::LogInfoEvent('/updater/theme/reinstall', 'Theme ' . $t->getName() . ' reinstalled successfully', implode("\n", $change));

				$changes[] = '<b>Changes to theme [' . $t->getName() . ']:</b><br/>' . "\n" . implode("<br/>\n", $change) . "<br/>\n<br/>\n";
			}

			foreach (Core::GetComponents() as $c) {
				/** @var $c Component_2_1 */

				try{
					if(!$c->isInstalled()) continue;
					if(!$c->isEnabled()) continue;

					CLI::PrintHeader('Reinstalling Component ' . $c->getName());
					// Request the reinstallation
					$change = $c->reinstall(1);

					// 1.0 version components don't support verbose changes :(
					if ($change === true) {
						$changes[] = '<b>Changes to component [' . $c->getName() . ']:</b><br/>' . "\n(list of changes not supported with this component!)<br/>\n<br/>\n";
					}
					// 2.1 components support an array of changes, yay!
					elseif ($change !== false) {
						$changes[] = '<b>Changes to component [' . $c->getName() . ']:</b><br/>' . "\n" . implode("<br/>\n", $change) . "<br/>\n<br/>\n";
					}
					// I don't care about "else", nothing changed if it was false.

					// Get the pages, (for the cleanup operation)
					$allpages = array_merge($allpages, $c->getPagesDefined());
				}
				catch(DMI_Query_Exception $e){
					$changes[] = 'Attempted database changes to component [' . $c->getName() . '], but failed!<br/>';
					//var_dump($e); die();
					$errors[] = array(
						'type' => 'component',
						'name' => $c->getName(),
						'message' => $e->getMessage() . '<br/>' . $e->query,
					);
				}
				catch(Exception $e){
					$changes[] = 'Attempted changes to component [' . $c->getName() . '], but failed!<br/>';
					//var_dump($e); die();
					$errors[] = array(
						'type' => 'component',
						'name' => $c->getName(),
						'message' => $e->getMessage(),
					);
				}
			}

			// Flush any non-existent admin page.
			// These can be created from developers changing their page URLs after the page is already registered.
			// Purging admin-only pages is relatively safe because these are defined in component metafiles anyway.
			CLI::PrintHeader('Cleaning up non-existent pages');
			$pageremovecount = 0;
			foreach(
				\Core\Datamodel\Dataset::Init()
					->select('baseurl')
					->table('page')
					->where('admin = 1')
					->execute() as $row
			){
				$baseurl = $row['baseurl'];

				// This page existed already, no need to do anything :)
				if(isset($allpages[$baseurl])) continue;

				++$pageremovecount;

				// Otherwise, this page was deleted or for some reason doesn't exist in the component list.....
				// BUH BAI
				\Core\Datamodel\Dataset::Init()->delete()->table('page')->where('baseurl = ' . $baseurl)->execute();
				\Core\Datamodel\Dataset::Init()->delete()->table('page_meta')->where('baseurl = ' . $baseurl)->execute();
				CLI::PrintLine("Flushed non-existent admin page: " . $baseurl);
				$changes[] = "<b>Flushed non-existent admin page:</b> " . $baseurl;
			}
			if($pageremovecount == 0){
				CLI::PrintLine('No pages flushed');
			}



			if(sizeof($errors) > 0){
				CLI::PrintHeader('Done, but with errors');
				foreach($errors as $e){
					CLI::PrintError('Error while processing ' . $e['type'] . ' ' . $e['name'] . ': ' . $e['message']);
				}
			}
			else{
				CLI::PrintHeader('DONE!');
			}

			foreach($changes as $str){
				echo $str;
			}

			// Flush the system cache, just in case
			\Core\Cache::Flush();

			// Increment the version counter.
			$version = ConfigHandler::Get('/core/filestore/assetversion');
			ConfigHandler::Set('/core/filestore/assetversion', ++$version);
		} // End if is post.

		//$page->title = 'Reinstall All Components';
		$this->setTemplate('/pages/admin/reinstallall.tpl');
	}

	/**
	 * Display ALL the system configuration options.
	 *
	 * @return int
	 */
	public function config() {
		// Admin-only page.
		if(!\Core\user()->checkAccess('g:admin')){
			return View::ERROR_ACCESSDENIED;
		}

		$view = $this->getView();

		require_once(ROOT_PDIR . 'core/libs/core/configs/functions.php');

		$where = array();
		// If the enterprise component is installed and multisite is enabled, configurations have another layer of complexity.
		if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::GetCurrentSiteID()){
			$where['overrideable'] = '1';
		}

		$configs = ConfigModel::Find($where, null, 'key');

		$groups  = array();
		foreach ($configs as $c) {
			// Export out the group for this config option.
			$el = \Core\Configs\get_form_element_from_config($c);
			$gname = $el->get('group');

			if (!isset($groups[$gname])){
				$groups[$gname] = new FormGroup(
					[
						'title' => $gname,
						'name' => $gname,
						//'class' => 'collapsible collapsed'
						'class' => 'system-config-group'
					]
				);
			}

			$groups[$gname]->addElement($el);
		}


		$form = new Form();
		$form->set('callsmethod', 'AdminController::_ConfigSubmit');
		// This form gives me more trouble with its persistence....
		// @todo Implement a better option than this.
		// This hack is designed to prevent this form from using stale values from a previous page load instead of
		// pulling them from the database.
		$form->set('uniqueid', 'admin-config-' . Core::RandomHex(6));
		foreach ($groups as $g) {
			$form->addElement($g);
		}

		$form->addElement('submit', array('value' => 'Save'));

		$this->setTemplate('/pages/admin/config.tpl');
		$view->assign('form', $form);
		$view->assign('config_count', sizeof($configs));
	}

	/**
	 * Sync the search index fields of every model on the system.
	 *
	 * @return int
	 */
	public function syncSearchIndex(){
		// Admin-only page.
		if(!\Core\user()->checkAccess('g:admin')){
			return View::ERROR_ACCESSDENIED;
		}

		// Just run through every component currently installed and reinstall it.
		// This will just ensure that the component is up to date and correct as per the component.xml metafile.
		$view = $this->getView();
		$changes = [];
		$outoftime = false;
		$counter = 0;
		$resume = \Core\Session::Get('syncsearchresume', 1);
		$timeout = ini_get('max_execution_time');
		// Dunno why this is returning 0, but if it is, reset it to 30 seconds!
		if(!$timeout) $timeout = 30;
		$memorylimit = ini_get('memory_limit');
		if(stripos($memorylimit, 'M') !== false){
			$memorylimit = str_replace(['m', 'M'], '', $memorylimit);
			$memorylimit *= (1024*1024);
		}
		elseif(stripos($memorylimit, 'G') !== false){
			$memorylimit = str_replace(['g', 'G'], '', $memorylimit);
			$memorylimit *= (1024*1024*1024);
		}

		foreach(\Core::GetComponents() as $c){
			/** @var Component_2_1 $c */

			if($outoftime){
				break;
			}

			foreach($c->getClassList() as $class => $file){
				if($outoftime){
					break;
				}

				if($class == 'model'){
					continue;
				}
				if(strrpos($class, 'model') !== strlen($class) - 5){
					// If the class doesn't explicitly end with "Model", it's also not a model.
					continue;
				}
				if(strpos($class, '\\') !== false){
					// If this "Model" class is namespaced, it's not a valid model!
					// All Models MUST reside in the global namespace in order to be valid.
					continue;
				}

				$ref = new ReflectionClass($class);
				if(!$ref->getProperty('HasSearch')->getValue()){
					// This model doesn't have the searchable flag, skip it.
					continue;
				}

				$c = ['name' => $class, 'count' => 0];
				$fac = new ModelFactory($class);
				while(($m = $fac->getNext())){
					++$counter;

					if($counter < $resume){
						// Allow this process to be resumed where it left off, since it may take more than 30 seconds.
						continue;
					}

					if(\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->getTime() + 5 >= $timeout){
						// OUT OF TIME!
						// Remember where this process left off and exit.
						\Core\Session::Set('syncsearchresume', $counter);
						$outoftime = true;
						break;
					}

					if(memory_get_usage(true) + 40485760 >= $memorylimit){
						// OUT OF MEMORY!
						// Remember where this process left off and exit.
						\Core\Session::Set('syncsearchresume', $counter);
						$outoftime = true;
						break;
					}

					/** @var Model $m */
					$m->set('search_index_pri', '!');
					$m->save();
					$c['count']++;
				}

				$changes[] = $c;
			}
		}

		if(!$outoftime){
			// It finished!  Unset the resume counter.
			\Core\Session::UnsetKey('syncsearchresume');
		}


		$view->title = 'Sync Searchable Index';
		$view->assign('changes', $changes);
		$view->assign('outoftime', $outoftime);
	}

	/**
	 * Display a list of system logs that have been recorded.
	 *
	 * @return int
	 */
	public function log(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/core/systemlog/view')){
			return View::ERROR_ACCESSDENIED;
		}

		$codes = ['' => '-- All --'];
		$ds = Dataset::Init()
			->table('system_log')
			->select('code')
			->unique(true)
			->order('code')
			->execute();

		foreach($ds as $row){
			$codes[$row['code']] = $row['code'];
		}

		$listings = new Core\ListingTable\Table();
		$listings->setModelName('SystemLogModel');
		$listings->setName('system-log');

		$listings->addFilter(
			'select',
			array(
				'title' => 'Type',
				'name' => 'type',
				'options' => array(
					'' => '-- All --',
					'info' => 'Informative',
					'error' => 'Warning/Error',
					'security' => 'Security',
				),
				'link' => FilterForm::LINK_TYPE_STANDARD,
			)
		);

		$listings->addFilter(
			'select',
			[
				'title' => 'Code',
				'name' => 'code',
				'options' => $codes,
				'link' => FilterForm::LINK_TYPE_STANDARD,
			]
		);

		$listings->addFilter(
			'date',
			[
				'title' => 'On or After',
				'name' => 'datetime_onafter',
				'linkname' => 'datetime',
				'link' => FilterForm::LINK_TYPE_GE,
			]
		);
		$listings->addFilter(
			'date',
			[
				'title' => 'On or Before',
				'name' => 'datetime_onbefore',
				'linkname' => 'datetime',
				'link' => FilterForm::LINK_TYPE_LE,
				'linkvaluesuffix' => ' 23:59:59'
			]
		);

		/*$filters->addElement(
			'select',
			array(
				'title' => 'Cron',
				'name' => 'cron',
				'options' => array(
					'' => '-- All --',
					'hourly' => 'hourly',
					'daily' => 'daily',
					'weekly' => 'weekly',
					'monthly' => 'monthly'
				),
				'link' => FilterForm::LINK_TYPE_STANDARD,
			)
		);
		$filters->addElement(
			'select',
			array(
				'title' => 'Status',
				'name' => 'status',
				'options' => array(
					'' => '-- All --',
					'pass' => 'pass',
					'fail' => 'fail'
				),
				'link' => FilterForm::LINK_TYPE_STANDARD,
			)
		);*/

		$listings->addFilter(
			'hidden',
			array(
				'title' => 'Session',
				'name' => 'session_id',
				'link' => FilterForm::LINK_TYPE_STANDARD,
			)
		);
		$listings->addFilter(
			'user',
			array(
				'title' => 'User',
				'name' => 'affected_user_id',
			    'linkname' => [
				    'affected_user_id',
			        'user_id',
			    ]
				//'link' => FilterForm::LINK_TYPE_STANDARD,
			)
		);
		$listings->addFilter(
			'text',
			[
				'title' => 'IP Address',
			    'name' => 'ip_addr',
			    'link' => FilterForm::LINK_TYPE_STARTSWITH,
			]
		);

		$listings->addColumn('Date Time', 'datetime');
		$listings->addColumn('Session', 'session_id');
		$listings->addColumn('User', 'user_id');
		$listings->addColumn('IP Address', 'ip_addr');
		$listings->addColumn('User Agent', 'useragent');
		$listings->addColumn('Affected User', 'affected_user_id');

		$listings->loadFiltersFromRequest($request);

		$view->title = 'System Log';
		$view->assign('listings', $listings);
	}

	/**
	 * Display a listing of all pages registered in the system.
	 */
	public function pages(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/core/pages/view')){
			return View::ERROR_ACCESSDENIED;
		}

		// Build a list of create pages for all registered components.
		$components = Core::GetComponents();
		$links = [];
		$componentopts = ['' => '-- All Components --'];
		foreach($components as $c){
			/** @var Component_2_1 $c */
			foreach($c->getXML()->getElements('/pages/pagecreate') as $node){
				/** @var DOMElement $node */
				$links[] = ['baseurl' => $node->getAttribute('baseurl'), 'title' => $node->getAttribute('title')];
			}

			$componentopts[$c->getKeyName()] = $c->getName();
		}
		// Sort them by name!
		asort($componentopts);

		$pageschema = PageModel::GetSchema();

		$table = new Core\ListingTable\Table();

		$table->setLimit(20);

		// Set the model that this table will be pulling data from.
		$table->setModelName('PageModel');

		// Gimme filters!
		$table->addFilter(
			'text',
			[
				'name' => 'title',
				'title' => 'Page Title',
				'link' => FilterForm::LINK_TYPE_CONTAINS,
			]
		);

		$table->addFilter(
			'text',
			[
				'name' => 'rewriteurl',
				'title' => 'URL',
				'link' => FilterForm::LINK_TYPE_CONTAINS,
			]
		);

		$table->addFilter(
			'text',
			[
				'name' => 'parenturl',
				'title' => 'Parent URL',
				'link' => FilterForm::LINK_TYPE_STARTSWITH,
			]
		);

		$table->addFilter(
			'select',
			[
				'name' => 'component',
				'title' => 'Source Component',
				'options' => $componentopts,
				'link' => FilterForm::LINK_TYPE_STANDARD,
			]
		);

		$table->addFilter(
			'select',
			[
				'name' => 'page_types',
				'title' => 'Include Admin Pages',
				'options' => ['all' => 'All Pages', 'no_admin' => 'Exclude Admin'],
				'value' => 'no_admin',
			]
		);

		// Add in all the columns for this listing table.
		if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled() && \Core\user()->checkAccess('g:admin')){
			$table->addColumn('Site', 'site', false);
			$ms = true;
		}
		else{
			$ms = false;
		}
		$table->addColumn('Title', 'title');
		$table->addColumn('URL', 'rewriteurl');
		$table->addColumn('Views', 'pageviews', false);
		$table->addColumn('Score', 'popularity');
		$table->addColumn('Cache', 'expires');
		$table->addColumn('Created', 'created', false);
		$table->addColumn('Last Updated', 'updated', false);
		$table->addColumn('Status');
		$table->addColumn('Published', 'published');
		$table->addColumn('Expires', 'published_expires');
		$table->addColumn('SEO Title');
		$table->addColumn('SEO Description / Teaser', null, false);
		$table->addColumn('Access', 'access');
		$table->addColumn('Component', 'component', false);

		// This page will also feature a quick-edit feature.
		$table->setEditFormCaller('AdminController::PagesSave');

		$table->loadFiltersFromRequest();

		if($table->getFilterValue('page_types') == 'no_admin'){
			$table->getModelFactory()->where('admin = 0');
			$table->getModelFactory()->where('selectable = 1');
		}



		$view->title = 'All Pages';
		//$view->assign('filters', $filters);
		//$view->assign('listings', $listings);
		$view->assign('links', $links);

		$view->assign('multisite', $ms);
		$view->assign('listing', $table);
		$view->assign('page_opts', PageModel::GetPagesAsOptions(false, '-- Select Parent URL --'));
		$view->assign('expire_opts', $pageschema['expires']['form']['options']);
	}

	/**
	 * Shortcut for publishing a page.
	 */
	public function page_publish() {
		$view    = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/core/pages/manage')){
			return View::ERROR_ACCESSDENIED;
		}

		$baseurl = $request->getParameter('baseurl');

		$page = new PageModel($baseurl);
		if(!$page->exists()){
			return View::ERROR_NOTFOUND;
		}

		if(!$request->isPost()){
			return View::ERROR_BADREQUEST;
		}

		// Is this page already published?
		if($page->get('published_status') == 'published'){
			Core::SetMessage('Article is already published!', 'error');
			\Core\go_back();
		}

		$page->set('published_status', 'published');
		$page->save();

		Core::SetMessage('Published page successfully!', 'success');
		\Core\go_back();
	}

	/**
	 * Shortcut for unpublishing a page.
	 */
	public function page_unpublish() {
		$view    = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/core/pages/manage')){
			return View::ERROR_ACCESSDENIED;
		}

		$baseurl = $request->getParameter('baseurl');

		$page = new PageModel($baseurl);
		if(!$page->exists()){
			return View::ERROR_NOTFOUND;
		}

		if(!$request->isPost()){
			return View::ERROR_BADREQUEST;
		}

		// Is this page already un-published?
		if($page->get('published_status') == 'draft'){
			Core::SetMessage('Article is already unpublished!', 'error');
			\Core\go_back();
		}

		$page->set('published_status', 'draft');
		$page->save();

		Core::SetMessage('Unpublished page successfully!', 'success');
		\Core\go_back();
	}

	/**
	 * Display a listing of all pages registered in the system.
	 */
	public function widgets(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		$viewer = \Core\user()->checkAccess('p:/core/widgets/manage');
		$manager = \Core\user()->checkAccess('p:/core/widgets/manage');
		if(!($viewer || $manager)){
			return View::ERROR_ACCESSDENIED;
		}

		Core::Redirect('/widget/admin');
	}

	/**
	 * Create a simple widget with the standard settings configurations.
	 */
	public function widget_create(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/core/widgets/manage')){
			return View::ERROR_ACCESSDENIED;
		}
		Core::Redirect('/widget/create');
	}

	/**
	 * Create a simple widget with the standard settings configurations.
	 */
	public function widget_update(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/core/widgets/manage')){
			return View::ERROR_ACCESSDENIED;
		}

		$baseurl = $request->getParameter('baseurl');
		Core::Redirect('/widget/update?baseurl=' . $baseurl);
	}

	/**
	 * Delete a simple widget.
	 */
	public function widget_delete(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/core/widgets/manage')){
			return View::ERROR_ACCESSDENIED;
		}

		if(!$request->isPost()){
			return View::ERROR_BADREQUEST;
		}

		$baseurl = $request->getParameter('baseurl');
		$class = substr($baseurl, 0, strpos($baseurl, '/')) . 'widget';

		if(!class_exists($class)){
			Core::SetMessage('Class [' . $class . '] was not found on the system, invalid widget!', 'error');
			\Core\go_back();
		}

		/** @var Widget_2_1 $obj */
		$obj = new $class();

		if(!($obj instanceof Widget_2_1)){
			Core::SetMessage('Wrong parent class for [' . $class . '], it does not appear to be a Widget_2_1 instance, invalid widget!', 'error');
			\Core\go_back();
		}

		if(!$obj->is_simple){
			Core::SetMessage('Widget [' . $class . '] does not appear to be a simple widget.  Only simple widgets can be created via this page.', 'error');
			\Core\go_back();
		}

		$model = new WidgetModel($baseurl);

		$model->delete();
		Core::SetMessage('Deleted widget ' . $model->get('title') . ' successfully!', 'success');
		\Core\go_back();
	}

	public function widgetinstances_save(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/core/widgets/manage')){
			return View::ERROR_ACCESSDENIED;
		}

		if(!$request->isPost()){
			return View::ERROR_BADREQUEST;
		}

		$counter = 0;
		$changes = ['created' => 0, 'updated' => 0, 'deleted' => 0];

		$selected = $_POST['selected'];
		// For the incoming options, I want an explicit NULL if it's empty.
		$theme    = $_POST['theme'] == '' ? null : $_POST['theme'];
		$skin     = $_POST['skin'] == '' ? null : $_POST['skin']; $_POST['skin'];
		$template = $_POST['template'] == '' ? null : $_POST['template'];
		$baseurl  = $_POST['page_baseurl'] == '' ? null : $_POST['page_baseurl'];

		foreach($_POST['widgetarea'] as $id => $dat){

			// Merge in the global information for this request
			//$dat['theme']         = $theme;
			//$dat['skin']          = $skin;
			$dat['template']      = $template;
			$dat['page_baseurl']  = $baseurl;

			$dat['weight'] = ++$counter;
			$dat['access'] = $dat['widgetaccess'];

			$w = WidgetModel::Construct($dat['baseurl']);
			$dat['site'] = $w->get('site');

			if(strpos($id, 'new') !== false){
				$w = new WidgetInstanceModel();
				$w->setFromArray($dat);
				$w->save();
				$changes['created']++;
			}
			elseif(strpos($id, 'del-') !== false){
				$w = new WidgetInstanceModel(substr($id, 4));
				$w->delete();
				// Reset the counter back down one notch since this was a deletion request.
				--$counter;
				$changes['deleted']++;
			}
			else{
				$w = new WidgetInstanceModel($id);
				$w->setFromArray($dat);
				if($w->save()) $changes['updated']++;
			}
		} // foreach($_POST['widgetarea'] as $id => $dat)

		// Display some human friendly status message.
		if($changes['created'] || $changes['updated'] || $changes['deleted']){
			$changetext = [];

			if($changes['created'] == 1) $changetext[] = 'One widget added';
			elseif($changes['created'] > 1) $changetext[] = $changes['created'] . ' widgets added';

			if($changes['updated'] == 1) $changetext[] = 'One widget updated';
			elseif($changes['updated'] > 1) $changetext[] = $changes['updated'] . ' widgets updated';

			if($changes['deleted'] == 1) $changetext[] = 'One widget deleted';
			elseif($changes['deleted'] > 1) $changetext[] = $changes['deleted'] . ' widgets deleted';

			Core::SetMessage(implode('<br/>', $changetext), 'success');
		}
		else{
			Core::SetMessage('No changes performed', 'info');
		}

		if($baseurl){
			\Core\redirect($baseurl);
		}
		else{
			\Core\redirect('/admin/widgets?selected=' . $selected);
		}

	}

	/**
	 * Page to test the UI of various Core elements
	 */
	public function testui(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('g:admin')){
			// This test page is an admin-only utility.
			return View::ERROR_ACCESSDENIED;
		}

		$lorem = new BaconIpsumGenerator();

		$skins = [];
		$admindefault = null;
		foreach(ThemeHandler::GetTheme()->getSkins() as $dat){
			$skins[ $dat['file'] ] = $dat['title'];
			if($dat['admindefault']) $admindefault = $dat['file'];
		}

		if($request->getParameter('skin')){
			$skin = $request->getParameter('skin');
		}
		else{
			$skin = $admindefault;
		}

		$view->mastertemplate = $skin;
		$view->title = 'Test General UI/UX';
		$view->assign('lorem_p', $lorem->getParagraphsAsMarkup(3));
		$view->assign(
			'lis', [
				$lorem->getWord(3),
				$lorem->getWord(3),
				$lorem->getWord(3),
				$lorem->getWord(3),
				$lorem->getWord(3),
			]
		);
		$view->assign('skins', $skins);
		$view->assign('skin', $skin);
	}

	/**
	 * Configure several of the SEO-based options on Core.
	 */
	public function seo_config(){
		// Admin-only page.
		if(!\Core\user()->checkAccess('g:admin')){
			return View::ERROR_ACCESSDENIED;
		}

		$view = $this->getView();

		$keys = [
			'/core/page/title_remove_stop_words',
			'/core/page/title_template',
			'/core/page/teaser_template',
			'/core/page/url_remove_stop_words',
		];

		$form = new Form();
		$form->set('callsmethod', 'AdminController::_ConfigSubmit');

		foreach($keys as $k){
			$c = ConfigHandler::GetConfig($k);
			$f = $c->asFormElement();
			// Don't need them grouped
			$f->set('group', '');
			$form->addElement($f);
		}
		$form->addElement('submit', ['value' => 'Save Options']);

		$view->title = 'SEO Options';
		$view->assign('form', $form);
	}

	/**
	 * Configure several of the performance-based options on Core.
	 */
	public function performance_config(){
		// Admin-only page.
		if(!\Core\user()->checkAccess('g:admin')){
			return View::ERROR_ACCESSDENIED;
		}

		$view = $this->getView();

		$keys = [
			'/core/javascript/minified',
			'/core/markup/minified',
			//'/core/filestore/assetversion',
			'/core/assetversion/proxyfriendly',
			'/core/performance/anonymous_user_page_cache',
		];

		$form = new Form();
		$form->set('callsmethod', 'AdminController::_ConfigSubmit');

		foreach($keys as $k){
			$c = ConfigHandler::GetConfig($k);
			$f = $c->asFormElement();
			// Don't need them grouped
			$f->set('group', '');
			$form->addElement($f);
		}

		$form->addElement('submit', ['value' => 'Save Options']);

		$view->title = 'Performance Options';
		$view->assign('form', $form);
	}

	public function email_config(){
		// Admin-only page.
		if(!\Core\user()->checkAccess('g:admin')){
			return View::ERROR_ACCESSDENIED;
		}

		$view = $this->getView();

		$keys = [
			'/core/email/enable_sending',
			'/core/email/from',
			'/core/email/from_name',
			'/core/email/mailer',
			'/core/email/sandbox_to',
			'/core/email/sendmail_path',
			'/core/email/smtp_host',
			'/core/email/smtp_user',
			'/core/email/smtp_password',
			'/core/email/smtp_port',
			'/core/email/smtp_security',
		];

		$form = new Form();
		$form->set('callsmethod', 'AdminController::_ConfigSubmit');

		foreach($keys as $k){
			$c = ConfigHandler::GetConfig($k);
			$f = $c->asFormElement();
			// Don't need them grouped
			$f->set('group', '');
			$form->addElement($f);
		}
		$form->addElement('submit', ['value' => 'Save Options']);

		$view->title = 'Email Options &amp; Diagnostics';
		$view->assign('form', $form);
		$view->assign('email_enabled', ConfigHandler::Get('/core/email/enable_sending'));
	}

	public function email_test(){
		// Admin-only page.
		if(!\Core\user()->checkAccess('g:admin')){
			return View::ERROR_ACCESSDENIED;
		}

		$request = $this->getPageRequest();
		$view = $this->getView();

		if(!$request->isPost()){
			return View::ERROR_BADREQUEST;
		}

		if(!$request->getPost('email')){
			return View::ERROR_BADREQUEST;
		}

		$view->mode = View::MODE_NOOUTPUT;
		$view->contenttype = View::CTYPE_HTML;
		$view->render();

		$dest         = $request->getPost('email');
		$method       = ConfigHandler::Get('/core/email/mailer');
		$smtpHost     = ConfigHandler::Get('/core/email/smtp_host');
		$smtpUser     = ConfigHandler::Get('/core/email/smtp_user');
		$smtpPass     = ConfigHandler::Get('/core/email/smtp_password');
		$smtpPort     = ConfigHandler::Get('/core/email/smtp_port');
		$smtpSec      = ConfigHandler::Get('/core/email/smtp_security');
		$sendmailPath = ConfigHandler::Get('/core/email/sendmail_path');
		$emailDebug   = [];

		$emailDebug[] = 'Sending Method: ' . $method;

		switch($method){
			case 'smtp':
				$emailDebug[] = 'SMTP Host: ' . $smtpHost . ($smtpPort ? ':' . $smtpPort : '');
				$emailDebug[] = 'SMTP User/Pass: ' . ($smtpUser ? $smtpUser . '//' . ($smtpPass ? '*** saved ***' : 'NO PASS') : 'Anonymous');
				$emailDebug[] = 'SMTP Security: ' . $smtpSec;
				break;
			case 'sendmail':
				$emailDebug[] = 'Sendmail Path: ' . $sendmailPath;
				break;
		}

		CLI::PrintHeader('Sending test email to ' . $dest);

		CLI::PrintActionStart('Initializing Email System');
		try{
			$email = new Email();
			$email->addAddress($dest);
			$email->setSubject('Test Email');
			$email->templatename = 'emails/admin/test_email.tpl';
			$email->assign('debugs', $emailDebug);
			$email->getMailer()->SMTPDebug = 2;

			CLI::PrintActionStatus(true);
		}
		catch(Exception $e){
			CLI::PrintActionStatus(false);
			CLI::PrintError($e->getMessage());
			CLI::PrintLine($e->getTrace());

			return;
		}

		CLI::PrintActionStart('Sending Email via ' . $method);
		try{
			$email->send();

			CLI::PrintActionStatus(true);
		}
		catch(Exception $e){
			CLI::PrintActionStatus(false);
			CLI::PrintError($e->getMessage());
			CLI::PrintLine(explode("\n", $e->getTraceAsString()));
		}

		CLI::PrintHeader('Sent Headers:');
		CLI::PrintLine(explode("\n", $email->getMailer()->CreateHeader()));

		CLI::PrintHeader('Sent Body:');
		CLI::PrintLine(explode("\n", $email->getMailer()->CreateBody()));
	}

	public static function _WidgetCreateUpdateHandler(Form $form){
		$baseurl = $form->getElement('baseurl')->get('value');

		$model = new WidgetModel($baseurl);
		$model->set('editurl', '/admin/widget/update?baseurl=' . $baseurl);
		$model->set('deleteurl', '/admin/widget/delete?baseurl=' . $baseurl);
		$model->set('title', $form->getElement('title')->get('value'));
		if($form->getElement('template')){
			$model->set('template', $form->getElementValue('template'));
		}

		$elements = $form->getElements();
		foreach($elements as $el){
			/** @var FormElement $el */
			if(strpos($el->get('name'), 'setting[') === 0){
				$name = substr($el->get('name'), 8, -1);
				$model->setSetting($name, $el->get('value'));
			}
		}
		$model->save();

		return 'back';
	}

	public static function _ConfigSubmit(Form $form) {
		$elements = $form->getElements();

		$updatedcount = 0;

		foreach ($elements as $e) {
			// I'm only interested in config options.
			if (strpos($e->get('name'), 'config[') === false) continue;

			// Make the name usable a little.
			$n = $e->get('name');
			if (($pos = strpos($n, '[]')) !== false) $n = substr($n, 0, $pos);
			$n = substr($n, 7, -1);

			// And get the config object
			$c = new ConfigModel($n);
			$val = null;

			switch ($c->get('type')) {
				case 'string':
				case 'text':
				case 'enum':
				case 'boolean':
				case 'int':
					$val = $e->get('value');
					break;
				case 'set':
					$val = implode('|', $e->get('value'));
					break;
				default:
					throw new Exception('Unsupported configuration type for ' . $c->get('key') . ', [' . $c->get('type') . ']');
					break;
			}

			// This is required because enterprise multisite mode has a different location for site configs.
			if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::GetCurrentSiteID()){
				$siteconfig = MultiSiteConfigModel::Construct($c->get('key'), MultiSiteHelper::GetCurrentSiteID());
				$siteconfig->setValue($val);
				if($siteconfig->save()) ++$updatedcount;
			}
			else{
				$c->setValue($val);
				if ($c->save()) ++$updatedcount;
			}

		}

		if ($updatedcount == 0) {
			Core::SetMessage('No configuration options changed', 'info');
		}
		elseif ($updatedcount == 1) {
			Core::SetMessage('Updated ' . $updatedcount . ' configuration option', 'success');
		}
		else {
			Core::SetMessage('Updated ' . $updatedcount . ' configuration options', 'success');
		}

		return true;
	}

	/**
	 * The save handler for /admin/pages quick edit.
	 *
	 * @param Form $form
	 *
	 * @return bool
	 */
	public static function PagesSave(Form $form) {
		$models = [];

		foreach($form->getElements() as $el){
			/** @var FormElement $el */
			$n = $el->get('name');

			// i only want model
			if(strpos($n, 'model[') !== 0){
				continue;
			}

			$baseurl = substr($n, 6, strpos($n, ']')-6);
			$n = substr($n, strpos($n, ']')+1);

			// Is this a meta attribute?
			if(strpos($n, '[meta][') === 0){
				$ismeta = true;
				$n = substr($n, 7, -1);
			}
			else{
				$ismeta = false;
				$n = substr($n, 1, -1);
			}

			// Make sure the model is available.
			if(!isset($models[$baseurl])){
				$models[$baseurl] = PageModel::Construct($baseurl);
			}
			/** @var PageModel $p */
			$p = $models[$baseurl];

			if($ismeta){
				$p->setMeta($n, $el->get('value'));
			}
			else{
				$p->set($n, $el->get('value'));
			}
		}

		foreach($models as $p){
			/** @var PageModel $p */
			$p->save();
		}

		return true;
	}
}
