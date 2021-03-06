<?php
use Core\CLI\CLI;

/**
 * Core component system, responsible for reading and parsing the component.xml,
 * saving it, and installing all components on the system.
 *
 * @package Core
 * @since 1.9
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

// @todo Implement arrayaccess system.
class Component_2_1 {

	/**
	 * Underlying XML Loader object of the component.xml file.
	 *
	 * Responsible for retrieving most information about this component.
	 *
	 * @var XMLLoader
	 */
	private $_xmlloader = null;

	/**
	 * The name of the component.
	 * Has to be unique, (because the name is a directory in /components)
	 *
	 * @var string
	 */
	protected $_name;

	/**
	 * Version of the component, (propagates to libraries and modules).
	 *
	 * @var string
	 */
	protected $_version;

	/**
	 * Is this component explictly disabled?
	 *
	 * @var boolean
	 */
	protected $_enabled = false;

	/**
	 * Description of this library.
	 * As set from the XML file.
	 *
	 * @var string
	 */
	protected $_description;

	/**
	 * Any update sites provided in this library.
	 *
	 * @var array <<string>>
	 */
	protected $_updateSites = array();

	/**
	 * Array of any authors for the library.
	 * Each element is composed of an array of name, email and url.
	 *
	 * @var array <<array>>
	 */
	protected $_authors = array();

	/**
	 * The iterator for this object, kept as a cache.
	 * @var CAEDirectoryIterator
	 */
	///protected $_iterator;

	/**
	 * Version of the component, as per the database (installed version).
	 *
	 * @var string
	 */
	private $_versionDB = false;

	/**
	 * Each component can have an execution mode, by default it's "web".
	 * This is used because some components will bomb out in CLI mode, and vice versa.
	 * @var string
	 */
	private $_execMode = 'WEB';

	/**
	 * @var \Core\Filestore\File
	 */
	private $_file;

	/**
	 * The permissions along with their description that are registered for this component.
	 *
	 * @var array
	 */
	private $_permissions = array();

	/**
	 * Set to true or false after evaluating.
	 *
	 * @var null|boolean
	 */
	private $_hasview = null;

	// A set of error codes components may encounter.
	const ERROR_NOERROR = 0;           // 000000
	const ERROR_INVALID = 1;           // 000001
	const ERROR_WRONGEXECMODE = 2;     // 000010
	const ERROR_MISSINGDEPENDENCY = 4; // 000100
	const ERROR_CONFLICT = 8;          // 001000
	const ERROR_UPGRADEPATH = 16;      // 010000

	/**
	 * This is the error code of any errors encountered.
	 * @var int
	 */
	public $error = 0;

	/**
	 * Any error messages encountered in this component, mainly while loading.
	 * @var array <<string>>
	 */
	public $errstrs = array();

	/**
	 * Only try to load a component only once!
	 *
	 * @var bool
	 */
	private $_loaded = false;

	/**
	 * Only try to load the files for this component once!
	 *
	 * @var bool
	 */
	private $_filesloaded = false;

	/**
	 * The smarty plugin directory cache.  This is to reduce the number of lookups required.
	 *
	 * @var null|boolean|string
	 */
	private $_smartyPluginDirectory = null;

	/**
	 * View search directory cache.  This is to reduce the number of lookups required.
	 *
	 * @var null|boolean|string
	 */
	private $_viewSearchDirectory = null;

	/**
	 * Array of classes in this component.  This is to reduce the number of lookups required.
	 *
	 * @var null|array
	 */
	private $_classlist = null;

	/**
	 * Array of controllers in this component.
	 * Used to reduce the number of XML lookups required.
	 *
	 * @var null|array
	 */
	private $_controllerlist = null;

	/**
	 * Array of widgets in this component.  This is to reduce the number of lookups required.
	 * @var null|array
	 */
	private $_widgetlist = null;

	/**
	 * Array of require defintions in this component.  This is to reduce the number of lookups required.
	 * @var null|array
	 */
	private $_requires = null;

	/**
	 * Set to true after all the files have been loaded.
	 * This is done by the Core.
	 *
	 * @var bool
	 */
	private $_ready = false;
	
	/** @var string Raw signed license data from the database, populated by load */
	private $_licenseDBData = null;
	
	/** @var null|array Result of the LICENSER.php file in this component, populated by getLicenseData as-needed. */
	private $_licenserFileData = null;


	public function __construct($filename = null) {
		$this->_file = \Core\Filestore\Factory::File($filename);

		$this->_xmlloader = new XMLLoader();
		$this->_xmlloader->setRootName('component');

		if (!$this->_xmlloader->loadFromFile($filename)) {
			throw new Exception('Parsing of XML Metafile [' . $filename . '] failed, not valid XML.');
		}
	}

	/**
	 * Load this component's metadata from the XML file.
	 *
	 * Will setup the name, version, installed version (if available), and enabled flag (if available).
	 *
	 * @return void
	 */
	public function load() {
		if ($this->_loaded) return;

		if (($mode = $this->_xmlloader->getRootDOM()->getAttribute('execmode'))) {
			$this->_execMode = strtoupper($mode);
		}

		$this->_name    = $this->_xmlloader->getRootDOM()->getAttribute('name');
		$this->_version = $this->_xmlloader->getRootDOM()->getAttribute("version");
		
		\Core\log_verbose('Loading metadata for component [' . $this->_name . ']');

		// Load the database information, if there is any.
		$dat = ComponentFactory::_LookupComponentData($this->_name);
		if (!$dat) return;

		$this->_versionDB   = $dat['version'];
		$this->_enabled     = ($dat['enabled']) ? true : false;
		$this->_loaded      = true;
		$this->_licenseDBData = isset($dat['license']) ? $dat['license'] : null;

		// Set the permissions
		$this->_permissions = array();
		foreach($this->_xmlloader->getElements('/permissions/permission') as $el){
			/** @var $el DOMElement */
			$this->_permissions[$el->getAttribute('key')] = [
				'description' => $el->getAttribute('description'),
				'context' => ($el->getAttribute('context')) ? $el->getAttribute('context') : '',
			];
		}
	}
	
	public function loadSupplementalModels(){
		// Load all the ModelSupplementals for this Component.
		// This is allowed because if a given Component extends another Component,
		// that first component will already have been loaded via Core's require dependency management.
		// It must be here because it can't be loaded inline, as Core may not have been ready at that stage.
		$supplementals = $this->getSupplementalModelList();
		foreach($supplementals as $supplemental => $filename){
			$classname = substr($supplemental, strpos($supplemental, '_') + 1, -12);

			// Grab the original class so I can override it.
			$original = new ReflectionClass($classname);
			$original->getMethod('AddSupplemental')->invoke(null, $supplemental);
		}
	}


	/**
	 * Save this component metadata back to its XML file.
	 * Useful in packager scripts.
	 */
	public function save($minified = false) {
		// Set the schema version to the newest API version.
		$this->_xmlloader->setSchema('http://corepl.us/api/2_4/component.dtd');
		// Ensure there's a required namespace on the root node.
		$this->_xmlloader->getRootDOM()->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");

		// Hack
		// If there is an empty smartydir set, don't let that get saved.
		if(!$this->getSmartyPluginDirectory()){
			$this->_xmlloader->removeElements('/smartyplugins');
		}

		// This needs to be the final step... write the XML doc back to the file.
		$XMLFilename = $this->_file->getFilename();
		
		//echo $this->_xmlloader->asPrettyXML(); die(); // DEBUG //
		if ($minified) {
			file_put_contents($XMLFilename, $this->_xmlloader->asMinifiedXML());
		}
		else {
			file_put_contents($XMLFilename, $this->_xmlloader->asPrettyXML());
		}
	}

	/**
	 * Save or get the package XML for this component.  This is useful for the
	 * packager
	 *
	 * @param boolean     $minified
	 * @param bool|string $filename
	 *
	 * @return string|null
	 */
	public function savePackageXML($minified = true, $filename = false) {

		$packagexml = new PackageXML();
		$packagexml->setFromComponent($this);

		$out = ($minified) ? $packagexml->asMinifiedXML() : $packagexml->asPrettyXML();

		if ($filename) {
			file_put_contents($filename, $out);
		}
		else {
			return $out;
		}
	}

	/**
	 * Get a raw array of the requirements for this component.
	 * 
	 * Each array index contains 'type', 'name', 'version', and 'operation'.
	 * 
	 * @return array
	 */
	public function getRequires() {
		if($this->_requires === null){
			$this->_requires = array();
			foreach ($this->_xmlloader->getElements('/requires/require') as $r) {
				$t  = $r->getAttribute('type');
				$n  = $r->getAttribute('name');
				$v  = @$r->getAttribute('version');
				$op = @$r->getAttribute('operation');

				// Defaults.
				if ($v == '') $v = false;
				if ($op == '') $op = 'ge';
				
				if($v !== false){
					$vstr = $n . ' ';
					switch($op){
						case 'ge':
						case '>=':
							$vstr .= '>=';
							break;
						case 'gt':
						case '>':
							$vstr .= '>';
							break;
						case 'le':
						case '<=':
							$vstr .= '<=';
							break;
						case 'lt':
						case '<':
							$vstr .= '<';
							break;
						case 'eq':
						case '=':
							$vstr .= '=';
							break;
					}
					$vstr .= ' ' . $v;
				}
				else{
					$vstr = $n;
				}

				$this->_requires[] = array(
					'type'      => strtolower($t),
					'name'      => strtolower($n),
					'version'   => strtolower($v),
					'operation' => strtolower($op),
					'vstring'   => $vstr,
					//'value' => $value,
				);
			}
		}

		return $this->_requires;
	}

	/**
	 * Get the description for this component
	 * @return string
	 */
	public function getDescription() {
		if ($this->_description === null) {
			$this->_description = trim($this->_xmlloader->getElement('/description')->nodeValue);
		}

		return $this->_description;
	}

	/**
	 * Get the logo for this component as-per defined in the XML.
	 * 
	 * @return null|\Core\Filestore\File
	 */
	public function getLogo(){
		if($this->_xmlloader->getRootDOM()->hasAttribute('logo')){
			$icon = $this->_xmlloader->getRootDOM()->getAttribute('logo');
			
			// This icon must be local.
			if(strpos($icon, '://') !== false){
				return null;
			}
			
			$file = Core\Filestore\Factory::File($icon);
			if($file->_type != 'asset'){
				// Non-asset images do not play well with various utilities in Core.
				return null;
			}
			
			return $file;
		}
		else{
			return null;
		}
	}

	/**
	 * Set the description for this component
	 * @param $desc string
	 */
	public function setDescription($desc) {
		// Set the cache first.
		$this->_description = $desc;
		// And set the data in the original DOM.
		$this->_xmlloader->getElement('/description')->nodeValue = $desc;
	}

	/**
	 * Get the registered permissions for this component.
	 *
	 * @return array
	 */
	public function getPermissions(){
		return $this->_permissions;
	}

	/**
	 * Get all screenshots in this metafile
	 *
	 * @return array
	 */
	public function getScreenshots(){
		$s = $this->_xmlloader->getElements('/screenshots/screenshot');

		if(!$s){
			return [];
		}
		
		if($s->length == 0){
			return [];
		}
		
		$ret = [];
		for($i = 0; $i<$s->length; $i++){
			$n = $s->item($i);
			$ret[] = $n->attributes->getNamedItem('file')->nodeValue;
		}
		
		return $ret;
	}

	/**
	 * Get the pages defined in this component.
	 *
	 * These are usually admin-only pages, (but may not be).
	 *
	 * Each page is returned with some of its info as a nested array with the
	 * baseurl of the page as the index.
	 * 
	 * The keys returned are:
	 * 
	 * title
	 * : The title of the page, usually a "t:STRING_..." string.
	 *
	 * group
	 * : The parent link this page falls under, usually a "t:STRING_..." string.
	 * 
	 * baseurl
	 * : The relative base URL of this link
	 * 
	 * rewriteurl
	 * : The pretty URL of this link
	 * 
	 * admin
	 * : 1/0 whether this page is marked as an admin page
	 * 
	 * selectable
	 * : 1/0 whether this page is marked as a "user-selectable" page.
	 * 
	 * access
	 * : Access string for this page.
	 *
	 * @return array
	 */
	public function getPagesDefined(){
		$pages = [];

		// I need to get the schema definitions first.
		$node = $this->_xmlloader->getElement('pages');

		// Now, get every table under this node.
		foreach ($node->getElementsByTagName('page') as $subnode) {
			/** @var DOMElement $subnode */

			$baseurl = $subnode->getAttribute('baseurl');
			$admin   = $subnode->getAttribute('admin');
			$group   = ($admin ? $subnode->getAttribute('group') : '');
			if(($selectable = $subnode->getAttribute('selectable')) === ''){
				// Selectable is blank, means it's left up to if it's an admin page or not.
				$selectable = ($admin ? '0' : '1'); // Defaults
			}
			if(!($rewriteurl = $subnode->getAttribute('rewriteurl'))){
				// Just use the baseurl for this parameter instead.
				$rewriteurl = $baseurl;
			}
			$title = $subnode->getAttribute('title');
			$access = $subnode->getAttribute('access');

			// Toss this page onto the stack :)
			$pages[$baseurl] = [
				'title' => $title,
				'group' => $group,
				'baseurl' => $baseurl,
				'rewriteurl' => $rewriteurl,
				'admin' => $admin,
				'selectable' => $selectable,
				'access' => $access,
			];
		}

		return $pages;
	}

	/**
	 * Get an array of Page Creates that are defined in the XML for this component.
	 * 
	 * @return array
	 */
	public function getPageCreatesDefined(){
		$pages = [];

		// I need to get the schema definitions first.
		$node = $this->_xmlloader->getElement('pages');

		// Now, get every table under this node.
		foreach ($node->getElementsByTagName('pagecreate') as $subnode) {
			/** @var DOMElement $subnode */

			$title = $subnode->getAttribute('title');
			$baseurl = $subnode->getAttribute('baseurl');
			$description = $subnode->getAttribute('description');

			// Toss this page onto the stack :)
			$pages[] = [
				'title'      => $title,
				'baseurl'    => $baseurl,
				'description' => $description,
			];
		}

		return $pages;
	}
	
	/**
	 * Get all email provder classes defined in this component
	 *
	 * @return array
	 */
	public function getEmailBackends(){
		$backends = [];

		// I need to get the schema definitions first.
		$node = $this->_xmlloader->getElement('provides');

		// Now, get every table under this node.
		foreach ($node->getElementsByTagName('emailbackend') as $subnode) {
			/** @var DOMElement $subnode */

			$name  = $subnode->getAttribute('name');
			$class = $subnode->getAttribute('class');
			
			$backends[$class] = $name;
		}

		return $backends;
	}

	/**
	 * Set and override the list of authors for this component.
	 *
	 * @param $authors array Array of authors to set
	 */
	public function setAuthors($authors) {
		// First, remove any authors currently in the XML.
		$this->_xmlloader->removeElements('/authors');

		// Now I can add the ones in the authors array.
		foreach ($authors as $a) {
			if (isset($a['email']) && $a['email']) {
				$this->_xmlloader->getElement('/authors/author[@name="' . $a['name'] . '"][@email="' . $a['email'] . '"]');
			}
			else {
				$this->_xmlloader->getElement('/authors/author[@name="' . $a['name'] . '"]');
			}
		}
	}

	/**
	 * Set and override the list of licenses for this component.
	 *
	 * @param $licenses array Array of licenses to set
	 */
	public function setLicenses($licenses) {
		// First, remove any licenses currently in the XML.
		$this->_xmlloader->removeElements('/licenses');

		// Now I can add the ones in the licenses array.
		$path = '/licenses/';
		foreach ($licenses as $lic) {
			$el = 'license' . ((isset($lic['url']) && $lic['url']) ? '[@url="' . $lic['url'] . '"]' : '');
			$l  = $this->_xmlloader->createElement($path . $el, false, 1);
			if ($lic['title']) $l->nodeValue = $lic['title'];
		}
	}


	public function loadFiles() {

		// First of all, this cannot be called on disabled or uninstalled components.
		if(!$this->isInstalled()) return false;
		if(!$this->isEnabled()) return false;
		if($this->_filesloaded) return true;

		\Core\log_verbose('Loading files for component [' . $this->getName() . ']');

		$dir = $this->getBaseDir();

		// Include any includes requested.
		// This adds support for namespaced functions.
		// <includes>
		//     <include filename="core/functions/Core.functions.php"/>
		// </includes>
		foreach ($this->_xmlloader->getElements('/includes/include') as $f) {
			require_once($dir . $f->getAttribute('filename'));
		}


		// Register any hooks that may be present.
		foreach ($this->_xmlloader->getElementsByTagName('hookregister') as $h) {
			$hook              = new Hook($h->getAttribute('name'));
			$hook->description = $h->getAttribute('description');
			if($h->getAttribute('return')){
				$hook->returnType = $h->getAttribute('return');
			}
		}

		// Register any events that may be present.
		foreach ($this->_xmlloader->getElementsByTagName('hook') as $h) {
			$event = $h->getAttribute('name');
			$call  = $h->getAttribute('call');
			$type  = @$h->getAttribute('type');
			HookHandler::AttachToHook($event, $call, $type);
		}


		// This component may have special form elements registered.  Check!
		if(class_exists('\\Core\\Forms\\Form')){
			foreach ($this->_xmlloader->getElements('/forms/formelement') as $node) {
				\Core\Forms\Form::$Mappings[$node->getAttribute('name')] = $node->getAttribute('class');
			}	
		}

		if(DEVELOPMENT_MODE && defined('AUTO_INSTALL_ASSETS') && AUTO_INSTALL_ASSETS && EXEC_MODE == 'WEB' && CDN_TYPE == 'local'){
			\Core\log_verbose('Auto-installing assets for component [' . $this->getName() . ']');
			$this->_parseAssets();
		}

		$this->_filesloaded = true;

		return true;
	}

	/**
	 * Internal method used by the Core to set when a given component has been loaded and is ready for use.
	 *
	 * @param bool $status
	 */
	public function _setReady($status = true){
		$this->_ready = $status;
	}

	public function isReady(){
		return $this->_ready;
	}

	public function getLibraryList() {
		// Get an array of library -> version
		$libs = array();

		// Every component is a library
		$libs[strtolower($this->_name)] = $this->_versionDB;

		foreach ($this->_xmlloader->getElements('provides/provide') as $p) {
			if (strtolower($p->getAttribute('type')) == 'library') {
				$v = @$p->getAttribute('version');
				if (!$v) $v = $this->_versionDB;
				$libs[strtolower($p->getAttribute('name'))] = $v;
			}
		}

		return $libs;
	}

	/**
	 * Get the list of classes provided in this component, (and their filenames)
	 *
	 * @return array
	 */
	public function getClassList() {
		$dir = $this->getBaseDir();

		if($this->_classlist === null){
			// Get an array of class -> file (fully resolved)
			$this->_classlist = array();

			foreach ($this->_xmlloader->getElements('/files/file') as $f) {
				$filename = $dir . $f->getAttribute('filename');
				//foreach($f->getElementsByTagName('provides') as $p){
				foreach ($f->getElementsByTagName('class') as $p) {
					$n           = strtolower($p->getAttribute('name'));
					$this->_classlist[$n] = $filename;
				}

				foreach ($f->getElementsByTagName('interface') as $p) {
					$n           = strtolower($p->getAttribute('name'));
					$this->_classlist[$n] = $filename;
				}

				foreach ($f->getElementsByTagName('trait') as $p) {
					$n           = strtolower($p->getAttribute('name'));
					$this->_classlist[$n] = $filename;
				}

				foreach ($f->getElementsByTagName('controller') as $p) {
					$n           = strtolower($p->getAttribute('name'));
					$this->_classlist[$n] = $filename;
				}

				foreach ($f->getElementsByTagName('widget') as $p) {
					$n           = strtolower($p->getAttribute('name'));
					$this->_classlist[$n] = $filename;
				}
			}
		}

		return $this->_classlist;
	}

	/**
	 * Get the list of models provided in this component, (and their filenames)
	 *
	 * @return array
	 */
	public function getModelList(){
		// Get the table structure as it exists in the database first, this will be the comparison point.
		$classes = $this->getClassList();
		foreach ($classes as $k => $v) {
			if($k == 'model'){
				// Anything named "Model" is the actual Model object... the base object doesn't have an associated table!
				unset($classes[$k]);
			}
			elseif(strrpos($k, 'model') !== strlen($k) - 5){
				// If the class doesn't explicitly end with "Model", it's also not a model.
				unset($classes[$k]);
			}
			elseif(strpos($k, '\\') !== false){
				// If this "Model" class is namespaced, it's not a valid model!
				// All Models MUST reside in the global namespace in order to be valid.
				unset($classes[$k]);
			}
		}
		return $classes;
	}

	/**
	 * Similar to getModelList, only it returns any supplemental model in this Component.
	 * 
	 * @return array
	 */
	public function getSupplementalModelList(){
		$classes = $this->getClassList();
		foreach ($classes as $k => $v) {
			if($k == 'model' || $k == 'modelsupplemental'){
				// Anything named "Model" is the actual Model object... the base object doesn't have an associated table!
				unset($classes[$k]);
			}
			elseif(strrpos($k, 'modelsupplemental') !== strlen($k) - 17){
				// If the class doesn't explicitly end with "Model", it's also not a model.
				unset($classes[$k]);
			}
			elseif(strpos($k, '\\') !== false){
				// If this "Model" class is namespaced, it's not a valid model!
				// All Models MUST reside in the global namespace in order to be valid.
				unset($classes[$k]);
			}
		}
		return $classes;
	}

	/**
	 * Get an array of widget names provided in this component.
	 *
	 * @return array
	 */
	public function getWidgetList() {
		$dir = $this->getBaseDir();

		if($this->_widgetlist === null){
			$this->_widgetlist = array();

			foreach ($this->_xmlloader->getElements('/files/file') as $f) {
				$filename = $dir . $f->getAttribute('filename');
				foreach ($f->getElementsByTagName('widget') as $p) {
					$this->_widgetlist[] = $p->getAttribute('name');
				}
			}
		}

		return $this->_widgetlist;
	}
	
	/**
	 * Get an array of Page Creates that are defined in the XML for this component.
	 * 
	 * @return array
	 */
	public function getWidgetCreatesDefined(){
		$widgets = [];

		// I need to get the schema definitions first.
		$node = $this->_xmlloader->getElement('widgets');

		// Now, get every table under this node.
		foreach ($node->getElementsByTagName('widgetcreate') as $subnode) {
			/** @var DOMElement $subnode */

			$title       = $subnode->getAttribute('title');
			$description = $subnode->getAttribute('description');
			$image       = $subnode->getAttribute('image');
			
			if($subnode->getAttribute('baseurl')){
				$nodebaseurl = $subnode->getAttribute('baseurl');
			}
			elseif(($class = $subnode->getAttribute('class'))){
				$nodebaseurl = '/widget/create?class=' . $class;
			}
			else{
				\Core\set_message('Invalid "widgetcreate" found in ' . $this->getName() . ', ' . $subnode->getAttribute('title') . '; at least baseurl or class are required.', 'error');
				continue;
			}

			// Toss this widget onto the stack
			$widgets[] = [
				'title'       => $title,
				'baseurl'     => $nodebaseurl,
				'description' => $description,
				'preview'     => $image,
			];
		}

		return $widgets;
	}

	public function getViewClassList() {
		$classes = array();
		if ($this->hasModule()) {
			foreach ($this->_xmlloader->getElementByTagName('module')->getElementsByTagName('file') as $f) {
				$filename = $this->getBaseDir() . $f->getAttribute('filename');
				foreach ($f->getElementsByTagName('provides') as $p) {
					switch (strtolower($p->getAttribute('type'))) {
						case 'viewclass':
						case 'view_class':
							$classes[$p->getAttribute('name')] = $filename;
							break;
					}
				}
			}
		}
		return $classes;
	}

	/**
	 * Get a list of view templates provided by this component.
	 */
	public function getViewList() {
		$views = array();
		$dir = $this->getBaseDir();

		if ($this->hasView()) {
			foreach ($this->_xmlloader->getElementByTagName('view')->getElementsByTagName('tpl') as $t) {
				$filename     = $dir . $t->getAttribute('filename');
				$name         = $t->getAttribute('name');
				$views[$name] = $filename;
			}
		}
		return $views;
	}

	/**
	 * Get the list of controllers in this component.
	 *
	 * @return array
	 */
	public function getControllerList() {
		if($this->_controllerlist === null){
			// Get an array of class -> file (fully resolved)
			$this->_controllerlist = array();
			$dir = $this->getBaseDir();

			//foreach($this->_xmlloader->getElementByTagName('files')->getElementsByTagName('file') as $f){
			foreach ($this->_xmlloader->getElements('/files/file') as $f) {
				$filename = $dir . $f->getAttribute('filename');
				//foreach($f->getElementsByTagName('provides') as $p){

				foreach ($f->getElementsByTagName('controller') as $p) {
					$n           = strtolower($p->getAttribute('name'));
					$this->_controllerlist[$n] = $filename;
				}
			}
		}

		return $this->_controllerlist;
	}

	/**
	 * Return the fully resolved name of the smarty plugin directory for
	 * this component (if there is one).
	 *
	 * Not many templates will use this function, but it is there for when needed.
	 */
	public function getSmartyPluginDirectory() {
		if($this->_smartyPluginDirectory === null){
			$d = $this->_xmlloader->getElement('/smartyplugins')->getAttribute('directory');
			if ($d) $this->_smartyPluginDirectory = $this->getBaseDir() . $d;
			else $this->_smartyPluginDirectory = false;
		}

		return $this->_smartyPluginDirectory;
	}

	/**
	 * Get an array of name => call of the registered smarty plugins on this component.
	 *
	 * @return array
	 */
	public function getSmartyPlugins(){
		$plugins = [];

		$node = $this->_xmlloader->getElement('/smartyplugins');
		if(!$node) return $plugins;

		foreach($node->getElementsByTagName('smartyplugin') as $n){
			/** @var DOMElement $n */
			$plugins[ $n->getAttribute('name') ] = $n->getAttribute('call');
		}

		return $plugins;
	}

	public function getScriptLibraryList() {
		$libs = array();
		foreach ($this->_xmlloader->getElements('/provides/scriptlibrary') as $s) {
			$libs[strtolower($s->getAttribute('name'))] = $s->getAttribute('call');
		}
		return $libs;
	}

	public function getViewSearchDir() {
		if ($this->hasView()) {
			if($this->_viewSearchDirectory === null){
				// Using the searchdir attribute is the preferred method.
				$att = @$this->_xmlloader->getElement('/view')->getAttribute('searchdir');
				if ($att) {
					$this->_viewSearchDirectory = $this->getBaseDir() . $att . '/';
				}
				elseif (($att = $this->_xmlloader->getElements('/view/searchdir')->item(0))) {
					// Try the 'searchdir' element instead.
					$this->_viewSearchDirectory = $this->getBaseDir() . $att->getAttribute('dir') . '/';
				}
				elseif (is_dir($this->getBaseDir() . 'templates')) {
					// Still no?!?  Try just a filesystem check instead...
					$this->_viewSearchDirectory = $this->getBaseDir() . 'templates/';
				}
				else{
					$this->_viewSearchDirectory = false;
				}
			}

			return $this->_viewSearchDirectory;
		}
	}

	public function getAssetDir() {
		// Core has a special exception...
		if ($this->getName() == 'core') $d = $this->getBaseDir() . 'core/assets/';
		else $d = $this->getBaseDir() . 'assets/';

		if (is_dir($d)) return $d;
		else return null;
	}

	/**
	 * Get an array of this component's registered user auth drivers.
	 *
	 * @return array
	 */
	public function getUserAuthDrivers(){
		$ret = [];
		$nodes = $this->_xmlloader->getElements('/users/userauth');
		foreach($nodes as $n){
			/** @var DOMElement $n */
			$name = $n->getAttribute('name');
			$class = $n->getAttribute('class');

			$ret[ $name ] = $class;
		}

		return $ret;
	}

	/**
	 * @deprecated 2012.01
	 * @return array
	 */
	public function getIncludePaths() {
		return array();
	}


	/**
	 * Get an array of the table names in the DB schema.
	 * @return array
	 */
	public function getDBSchemaTableNames() {
		$ret = array();
		foreach ($this->_xmlloader->getElement('dbschema')->getElementsByTagName('table') as $table) {
			$ret[] = $table->getAttribute('name');
		}
		return $ret;
	}

	/**
	 * Set the DB Schema table names.
	 * Will override any setting of the current dbschema.
	 *
	 * @param array $arr
	 */
	public function setDBSchemaTableNames($arr) {
		// Easiest way... just drop the current set.
		$this->_xmlloader->getRootDOM()->removeChild($this->_xmlloader->getElement('/dbschema'));
		// And recreate it.
		$node = $this->_xmlloader->getElement('/dbschema[@prefix="' . DB_PREFIX . '"]');
		foreach ($arr as $k) {
			if (!trim($k)) continue;
			$tablenode = $this->getDOM()->createElement('table');
			$tablenode->setAttribute('name', $k);
			$node->appendChild($tablenode);
			unset($tablenode);
		}
	}


	public function getVersionInstalled() {
		return $this->_versionDB;
	}

	/**
	 * Components are components, (unless it's the core)
	 *
	 * @return string
	 */
	public function getType() {
		if ($this->_name == 'core') return 'core';
		else return 'component';
	}

	/**
	 * Get this component's name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Get this component's "key" name.
	 *
	 * This *must* be the name of the directory it's installed in
	 * and *must not* contain spaces or other weird characters.
	 *
	 * @return string
	 */
	public function getKeyName(){
		return str_replace(' ', '-', strtolower($this->_name));
	}

	/**
	 * Get this component's version
	 *
	 * @return string
	 */
	public function getVersion() {
		return $this->_version;
	}

	/**
	 * Return the fully populated array with the licensed data and the values from the license
	 * 
	 * @return array
	 */
	public function getLicenseData(){
		// This feature relies on a valid server id.
		if(!defined('SERVER_ID')){
			return [];
		}
		if(strlen(SERVER_ID) != 32){
			return [];
		}
		
		// See if I need to lookup the contents; this is cached internally to save on lookups.
		if($this->_licenserFileData === null){
			$f = ($this->getKeyName() == 'core' ? ROOT_PDIR . 'core/' : $this->getBaseDir() ) . 'LICENSER.php';

			if(file_exists($f)){
				$licenser = include($f);

				if(!isset($licenser)){
					$this->_licenserFileData = [];
					return [];
				}
				elseif(is_array($licenser)){
					$this->_licenserFileData = $licenser;
				}
				else{
					// Just a blank array.
					$this->_licenserFileData = [];
					return [];
				}
			}
			else{
				$this->_licenserFileData = [];
				return [];
			}

			$features = [];
			$status = false;
			$message = 'No license data present';
			$expires = null;
			if($this->_licenseDBData){
				// Only populate feature values if there is a license included.
				// Otherwise, they will get set to FALSE.
				
				// First through, try to pull them from cache to save on a decryption operation.
				// Lookup the cache for this licensed key value.
				$cacheKey = md5('LICENSER:' . SERVER_ID . $this->getKeyName());
				$cached = \Core\Cache::Get($cacheKey);
				if($cached){
					$features = $cached['features'];
					$status = $cached['status'];
					$message = $cached['message'];
					$expires = $cached['expires'];
				}
				else{
					try{
						$gpg = new \Core\GPG\GPG();

						$data = $gpg->decryptData($this->_licenseDBData);
						if($data && ($decoded = json_decode($data, true))){
							if($decoded['status'] && isset($decoded['features'])){
								$features = $decoded['features'];
							}
							
							$status = $decoded['status'];
							if(isset($decoded['message'])){
								$message = $decoded['message'];
							}
							elseif($status){
								$message = 'Valid license'; // Good licenses may not have a message.
							}
							
							if(isset($decoded['expires'])){
								$expires = $decoded['expires'];
							}
						}
						
						// Save these back to cache so that this try/catch can be skipped for a little while again.
						\Core\Cache::Set($cacheKey, ['features' => $features, 'status' => $status, 'message' => $message, 'expires' => $expires], 7200);
					}
					catch(Exception $e){
						// GPG cannot be instantiated; silently skip pulling license features.
					}
				}
			}

			// Pull the information from the cache or database for the keys!
			$newFeatures = [];
			foreach($this->_licenserFileData['features'] as $f){
				if(isset($features[$f])){
					$newFeatures[$f] = $features[$f];
				}
				else{
					$newFeatures[$f] = false;
				}
			}
			
			// Remap the stored key with the version containing the values.
			$this->_licenserFileData['features'] = $newFeatures;
			$this->_licenserFileData['status'] = $status;
			$this->_licenserFileData['message'] = $message;
			$this->_licenserFileData['component'] = $this->getName();
			$this->_licenserFileData['expires'] = $expires;
		}
		
		return $this->_licenserFileData;
	}

	/**
	 * Set the version of this component
	 *
	 * This affects the component.xml metafile of the package.
	 *
	 * @param $vers string
	 *
	 * @return void
	 */
	public function setVersion($vers) {
		if ($vers == $this->_version) return;

		// Switch over any unversioned upgrade directives to this version.
		// First, check just a plain <upgrade> directive.
		if (($upg = $this->_xmlloader->getElement('/upgrades/upgrade[@from=""][@to=""]', false))) {
			// Add the current and dest. attribute to it.
			$upg->setAttribute('from', $this->_version);
			$upg->setAttribute('to', $vers);
		}
		elseif (($upg = $this->_xmlloader->getElement('/upgrades/upgrade[@from="next"]', false))) {
			// Add the current and dest. attribute to it.
			$upg->setAttribute('from', $this->_version);
			$upg->setAttribute('to', $vers);
		}
		elseif (($upg = $this->_xmlloader->getElement('/upgrades/upgrade[@to="next"]', false))) {
			// Add the current and dest. attribute to it.
			$upg->setAttribute('from', $this->_version);
			$upg->setAttribute('to', $vers);
		}
		elseif (($upg = $this->_xmlloader->getElement('/upgrades/upgrade[@from="' . $this->_version . '"][@to=""]', false))) {
			$upg->setAttribute('to', $vers);
		}
		else {
			// No node found... just create a new one.
			$this->_xmlloader->getElement('/upgrades/upgrade[@from="' . $this->_version . '"][@to="' . $vers . '"]');
		}

		$this->_version = $vers;
		$this->_xmlloader->getRootDOM()->setAttribute('version', $vers);
	}

	/**
	 * Set all files in this component.  Only really usable in the installer.
	 *
	 * @param $files array Array of files to set.
	 */
	public function setFiles($files) {
		// Clear out the array first.
		$this->_xmlloader->removeElements('/files/file');

		// It would be nice to have them alphabetical.
		$newarray = array();
		foreach ($files as $f) {
			$newarray[$f['file']] = $f;
		}
		ksort($newarray);

		// And recreate them all.
		foreach ($newarray as $f) {
			$el = $this->_xmlloader->createElement('/files/file[@filename="' . $f['file'] . '"][@md5="' . $f['md5'] . '"]');

			if (isset($f['controllers'])) {
				foreach ($f['controllers'] as $c) {
					$this->_xmlloader->createElement('controller[@name="' . $c . '"]', $el);
				}
			}
			if (isset($f['classes'])) {
				foreach ($f['classes'] as $c) {
					$this->_xmlloader->createElement('class[@name="' . $c . '"]', $el);
				}
			}
			if (isset($f['interfaces'])) {
				foreach ($f['interfaces'] as $i) {
					$this->_xmlloader->createElement('interface[@name="' . $i . '"]', $el);
				}
			}
			if (isset($f['traits'])) {
				foreach ($f['traits'] as $i) {
					$this->_xmlloader->createElement('trait[@name="' . $i . '"]', $el);
				}
			}
		}
	}

	/**
	 * Set all asset files in this component.  Only really usable in the installer.
	 *
	 * @param $files array Array of files to set.
	 */
	public function setAssetFiles($files) {
		// Clear out the array first.
		$this->_xmlloader->removeElements('/assets/file');

		// It would be nice to have them alphabetical.
		$newarray = array();
		foreach ($files as $f) {
			$newarray[$f['file']] = $f;
		}
		ksort($newarray);

		// And recreate them all.
		foreach ($newarray as $f) {
			$el = $this->_xmlloader->createElement('/assets/file[@filename="' . $f['file'] . '"][@md5="' . $f['md5'] . '"]');
		}
	}

	/**
	 * Set all asset files in this component.  Only really usable in the installer.
	 *
	 * @param $files array Array of files to set.
	 */
	public function setViewFiles($files) {
		// Clear out the array first.
		$this->_xmlloader->removeElements('/view/file');

		// It would be nice to have them alphabetical.
		$newarray = array();
		foreach ($files as $f) {
			$newarray[$f['file']] = $f;
		}
		ksort($newarray);

		// And recreate them all.
		foreach ($newarray as $f) {
			$el = $this->_xmlloader->createElement('/view/file[@filename="' . $f['file'] . '"][@md5="' . $f['md5'] . '"]');
		}
	}

	/**
	 * Set a require in the XML
	 * 
	 * This is used by the packager.
	 * 
	 * @param string      $name
	 * @param string      $type
	 * @param null|string $version
	 * @param null|string $op
	 */
	public function setRequires($name, $type, $version = null, $op = null){
		// Get the node, (and auto-create if it doesn't exist).
		$node = $this->_xmlloader->getElement('/requires/require[@name="' . $name . '"][@type="' . $type . '"]');

		// Set the new version and operation for it.
		if($version){
			$node->setAttribute('version', $version);
			if($op){
				$node->setAttribute('operation', $op);
			}
			else{
				$node->removeAttribute('operation');
			}
		}
		else{
			$node->removeAttribute('version');
			$node->removeAttribute('operation');
		}
	}

	/**
	 * Get the raw XML of this component, useful for debugging.
	 *
	 * @return string (XML)
	 */
	public function getRawXML() {
		return $this->_xmlloader->asPrettyXML();
	}


	public function isValid() {
		return (!$this->error & Component_2_1::ERROR_INVALID);
	}

	public function isInstalled() {
		return ($this->_versionDB === false) ? false : true;
	}

	public function needsUpdated() {
		return ($this->_versionDB != $this->_version);
	}

	public function getErrors($glue = '<br/>') {
		if ($glue) {
			return implode($glue, $this->errstrs);
		}
		else {
			return $this->errors;
		}
	}

	public function runRequirementChecks(){
		$requires = $this->getRequires();
		$results = [];
		foreach ($requires as $r) {
			$check = [
				'require' => $r,
				'result' => [
					'passed' => false,
					'available' => null,
					'message' => null,
				],
			];
			
			switch ($r['type']) {
				case 'component':
					if (!Core::IsComponentAvailable($r['name'])) {
						// Component is not available.
						$check['result']['message'] = $check['result']['message'] = 'Missing component ' . $r['name'];
					}
					elseif (!Core::IsComponentAvailable($r['name'], $r['version'], $r['operation'])) {
						$check['result']['available'] = Core::GetComponent($r['name'])->getVersionInstalled();
						$check['result']['message'] = 'Requires component ' . $r['vstring'] . ',  ' . $check['available'] . ' available.';
					}
					else{
						$check['result']['passed'] = true;
						$check['result']['available'] = Core::GetComponent($r['name'])->getVersionInstalled();
						$check['result']['message'] = 'Component ' . $r['vstring'] . ' is available';
					}

					$results[] = $check;
					break;
				
				case 'define':
					// Ensure that whatever define the script is expecting is there... this is useful for the EXEC_MODE define.
					if (!defined($r['name'])) {
						$check['result']['message'] = $check['result']['message'] = 'Missing define ' . $r['name'];
					}
					elseif ($r['value'] != null && constant($r['name']) != $r['value']) {
						// Also if they opted to include a value... check that too.
						$check['result']['message'] = $check['result']['message'] = 'Incorrect define ' . $r['name'] . ', expected value of: ' . $r['value'];
					}
					else{
						$check['result']['passed'] = true;
						$check['result']['available'] = true;
						$check['result']['message'] = 'Define ' . $r['name'] . ' is set and correct';
					}

					$results[] = $check;
					break;

				case 'function':
					// Requires a specific function to exist.  This is most common with built-in PHP functions,
					// such as gd, ldap, or imap support.
					if(!function_exists($r['name'])){
						$check['result']['message'] = $check['result']['message'] = 'Missing function ' . $r['name'];
					}
					else{
						$check['result']['passed'] = true;
						$check['result']['available'] = true;
						$check['result']['message'] = 'Function ' . $r['name'] . ' is available';
					}

					$results[] = $check;
					break;

				case 'jslibrary':
					if (!Core::IsJSLibraryAvailable($r['name'])) {
						// The library is not even available!
						$check['result']['message'] = 'Missing JSlibrary ' . $r['name'];
					}
					else{
						$check['result']['passed'] = true;
						$check['result']['available'] = true;
						$check['result']['message'] = 'JSLibrary ' . $r['name'] . ' is available';
					}

					$results[] = $check;
					break;
				
				case 'library':
					if (!Core::IsLibraryAvailable($r['name'])) {
						// The library is not even available!
						$check['result']['message'] = 'Missing library ' . $r['name'];
					}
					elseif (!Core::IsLibraryAvailable($r['name'], $r['version'], $r['operation'])) {
						// The library is available, but is out of date.
						$check['result']['available'] = Core::GetLibraryVersion($r['name']);
						$check['result']['message'] = 'Requires library ' . $r['vstring'] . ',  ' . $check['available'] . ' available.';
					}
					else{
						$check['result']['passed'] = true;
						$check['result']['available'] = Core::GetLibraryVersion($r['name']);
						$check['result']['message'] = 'Library ' . $r['vstring'] . ' is available';
					}
					
					$results[] = $check;
					break;
				
				case 'phpextension':
					$v = phpversion($r['name']);
					$l = extension_loaded($r['name']);
					if($l === false){
						$check['result']['message'] = 'Missing PHP Extension ' . $r['name'];
					}
					elseif($r['version'] && !version_compare($v, $r['version'], $r['operation'])){
						$check['result']['available'] = $v;
						$check['result']['message'] = 'Requires PHP Extension ' . $r['vstring'] . ',  ' . $check['available'] . ' available.';
					}
					else{
						$check['result']['passed'] = true;
						$check['result']['available'] = $v;
						$check['result']['message'] = 'PHP Extension ' . $r['vstring'] . ' is available';
					}

					$results[] = $check;
					break;
			}
		}
		
		return $results;
	}
	
	/**
	 * Simple check if this component is currently enabled.
	 * 
	 * @return bool
	 */
	public function isEnabled() {
		return ($this->_enabled === true);
	}

	/**
	 * Check if this component is loadable in the environment's current state.
	 *
	 * This cannot be cached because it's called multiple times in the loader.
	 * ie: com1 needs com2, but com1 is checked first in the loop.
	 */
	public function isLoadable() {
		// Invalid ones are not loadable... don't even try ;)
		if ($this->error & Component_2_1::ERROR_INVALID) {
			return false;
		}

		// It's already loaded!
		if($this->_filesloaded) return true;

		// Reset the error info.
		$this->error   = 0;
		$this->errstrs = array();

		// Can this component be loaded as-is?
		$requireChecks = $this->runRequirementChecks();
		foreach($requireChecks as $r){
			if(!$r['result']['passed']){
				$this->error     = $this->error | Component_2_1::ERROR_MISSINGDEPENDENCY;
				$this->errstrs[] = $r['result']['message'];
			}
		}

		if ($this->error){
			return false;
		}

		// Check classes.  If a class is provided in another package, DON'T LOAD!
		$cs = $this->getClassList();
		foreach ($cs as $c => $file) {
			if (Core::IsClassAvailable($c)) {
				$this->error     = $this->error | Component_2_1::ERROR_CONFLICT;
				$this->errstrs[] = $c . ' already defined in another component';
				break;
			}
		}

		// Check the libraries themselves.
		$liblist = $this->getLibraryList();

		// Make sure the libraries contained herein aren't provided already!
		foreach($liblist as $k => $v){
			if(Core::IsLibraryAvailable($k)){
				$this->error     = $this->error | Component_2_1::ERROR_CONFLICT;
				$this->errstrs[] = 'Library ' . $k . ' already provided by another component!';
				break;
			}
		}

		// Check that if the version installed is not what's in the component file, that there is a valid upgrade path.
		if(!$this->_checkUpgradePath()){
			$this->error = $this->error | Component_2_1::ERROR_UPGRADEPATH;
			$this->errstrs[] = 'No upgrade path found (' . $this->_versionDB . ' to ' . $this->_version . ')';
		}

		// I should have a good idea of any errors by now...
		return (!$this->error) ? true : false;
	}


	/**
	 * Get every JSLibrary in this component as an object.
	 */
	public function getJSLibraries() {
		$ret = array();
		foreach ($this->_xmlloader->getRootDOM()->getElementsByTagName('jslibrary') as $node) {
			$lib       = new JSLibrary();
			$lib->name = $node->getAttribute('name');
			// The version doesn't have to be set... it can be derived from the component version.
			$lib->version                = (($v = @$node->getAttribute('version')) ? $v : $this->_xmlloader->getRootDOM()->getAttribute('version'));
			$lib->baseDirectory          = ROOT_PDIR . 'components/' . $this->getName() . '/';
			$lib->DOMNode                = $node;
			$ret[strtolower($lib->name)] = $lib;
		}
		return $ret;
	}


	public function hasLibrary() {
		// Every component is a library now.
		return true;
	}

	public function hasJSLibrary() {
		return ($this->_xmlloader->getRootDOM()->getElementsByTagName('jslibrary')->length) ? true : false;
	}

	public function hasModule() {
		return ($this->_xmlloader->getRootDOM()->getElementsByTagName('module')->length) ? true : false;
	}

	public function hasView() {
		if($this->_hasview === null){
			if($this->_xmlloader->getRootDOM()->getElementsByTagName('view')->length){
				$this->_hasview = true;
			}
			elseif(is_dir($this->getBaseDir() . 'templates/')){
				$this->_hasview = true;
			}
			else{
				$this->_hasview = false;
			}
		}

		return $this->_hasview;


	}

	/**
	 * Install this component.
	 *
	 * Returns false if nothing changed, else will return an array containing all changes.
	 *
	 * @return boolean | array
	 * @throws InstallerException
	 */
	public function install() {

		if ($this->isInstalled()) return false;

		if (!$this->isLoadable()) return false;

		$changes = $this->_performInstall();


		// Run through each task under <install> and execute it.
		/** @var $u DOMNode */
		$u = $this->_xmlloader->getRootDOM()->getElementsByTagName('install')->item(0);

		// This gets a bit tricky, I need to get all the valid upgrade elements in the order that they
		// are defined in the component.xml.
		if($u){
			$children = $u->childNodes;
		}
		else{
			$children = [];
		}


		// The various upgrade tasks that can happen
		foreach($children as $child){
			/** @var $child DOMNode */
			switch($child->nodeName){
				case 'dataset':
					$datachanges = $this->_parseDatasetNode($child);
					if($datachanges !== false) $changes = array_merge($changes, $datachanges);
					break;
				case 'phpfileinclude':
					// I need to do this in a method so that include file doesn't mess with my local variables!
					$this->_includeFileForUpgrade(ROOT_PDIR . trim($child->nodeValue));
					$changes[] = 'Included custom php file ' . basename($child->nodeValue);
					break;
				case 'php':
					$file = $child->getAttribute('file');
					if($file){
						// I need to do this in a method so that include file doesn't mess with my local variables!
						$this->_includeFileForUpgrade($this->getBaseDir() . $file);
						$changes[] = 'Included custom php file ' . $file;
					}
					else{
						$changes[] = 'Ignoring invalid &lt;php&gt; directive, no file attribute provided!';
					}
					break;
				case 'sql':
					$file = $child->getAttribute('file');
					if($file){
						$contents = file_get_contents($this->getBaseDir() . $file);
						$execs = 0;
						$parser = new SQL_Parser_Dataset($contents, SQL_Parser::DIALECT_MYSQL);
						$datasets = $parser->parse();
						foreach($datasets as $ds){
							$ds->execute();
							$execs++;
						}
						$changes[] = 'Executed custom sql file ' . $file . ' and ran ' . $execs . ($execs == 1 ? ' query' : 'queries');
					}
					else{
						$changes[] = 'Ignoring invalid &lt;sql&gt; directive, no file attribute provided!';
					}
					break;
				case '#text':
					// Text entries can silently be ignored.
					break;
				default:
					$changes[] = 'Ignoring unsupported install directive: [' . $child->nodeName . ']';
			}
		}

		if(is_array($changes) && sizeof($changes)){
			\Core\log_info('Component ' . $this->getName() . ' installed successfully!', '/updater/component/install', implode("\n", $changes));
		}

		// Yay, it should be installed now.	Update the version in the database.
		$c = new ComponentModel($this->_name);
		$c->set('version', $this->_version);
		$c->save();
		$this->_versionDB = $this->_version;
		$this->_enabled = ($c->get('enabled') == '1');

		// And load this component into the system so anything else can access it immediately.
		$this->loadFiles();
		if (class_exists('Core')) {
			$ch = Core::Singleton();
			$ch->_registerComponent($this);
		}

		return $changes;
	}

	/**
	 * Reinstall a component with its same version.
	 * Useful for replacing corrupt assets or what not.
	 *
	 * Returns false if nothing changed, else will return an array containing all changes.
	 *
	 * @param int $verbosity 0 for standard output, 1 for real-time, 2 for real-time verbose output.
	 *
	 * @return boolean | array
	 * @throws InstallerException
	 */
	public function reinstall($verbosity = 0) {
		// @todo I need actual error checking here.
		if (!$this->isInstalled()) return false;

		$changes = $this->_performInstall($verbosity);

		if(is_array($changes) && sizeof($changes) > 0){
			SystemLogModel::LogInfoEvent('/updater/component/reinstall', 'Component ' . $this->getName() . ' reinstalled successfully!', implode("\n", $changes));
		}

		return $changes;
	}

	/**
	 * Upgrade this component to the newer version, if possible.
	 *
	 * Returns false if nothing changed, else will return an array containing all changes.
	 *
	 * @param boolean $next    Set to true to run the "next" upgrades as well as any current.
	 * @param boolean $verbose Set to true to enable real-time output
	 *
	 * @return boolean | array
	 * @throws InstallerException
	 */
	public function upgrade($next = false, $verbose = false) {
		if (!$this->isInstalled()){
			if($verbose) CLI::PrintDebug('Skipping ' . $this->getName() . ' as it is marked as uninstalled.');
			return false;
		}

		if($verbose) CLI::PrintHeader('Beginning upgrade for ' . $this->getName());

		$changes = array();

		// I can now do everything else.
		$otherchanges = $this->_performInstall();
		if ($otherchanges !== false) $changes = array_merge($changes, $otherchanges);

		$canBeUpgraded = true;
		while ($canBeUpgraded) {
			// Set as false to begin with, (will be set back to true if an upgrade is ran).
			$canBeUpgraded = false;
			foreach ($this->_xmlloader->getRootDOM()->getElementsByTagName('upgrade') as $u) {
				/** @var $u DOMElement */

				$from = $u->getAttribute('from');
				$to   = $u->getAttribute('to') ? $u->getAttribute('to') : 'next';

				// look for a valid upgrade path.
				if (($this->_versionDB == $from) || ($next && $from == 'next')) {
					// w00t, found one...
					$canBeUpgraded = true;

					if($verbose){
						CLI::PrintLine('Processing upgrade from ' . $from . ' to ' . $to);
					}

					// This gets a bit tricky, I need to get all the valid upgrade elements in the order that they
					// are defined in the component.xml.
					$children = $u->childNodes;

					// The various upgrade tasks that can happen
					foreach($children as $child){
						/** @var $child DOMElement */
						switch($child->nodeName){
							case 'dataset':
								$datachanges = $this->_parseDatasetNode($child, $verbose);
								if($datachanges !== false) $changes = array_merge($changes, $datachanges);
								break;
							case 'phpfileinclude':
								// I need to do this in a method so that include file doesn't mess with my local variables!
								$this->_includeFileForUpgrade(ROOT_PDIR . trim($child->nodeValue), $verbose);
								$changes[] = 'Included custom php file ' . basename($child->nodeValue);
								break;
							case 'php':
								$file = $child->getAttribute('file');
								if($file){
									// I need to do this in a method so that include file doesn't mess with my local variables!
									$this->_includeFileForUpgrade($this->getBaseDir() . $file, $verbose);
									$changes[] = 'Included custom php file ' . $file;
								}
								else{
									$changes[] = 'Ignoring invalid &lt;php&gt; directive, no file attribute provided!';
								}
								break;
							case 'sql':
								$file = $child->getAttribute('file');
								if($file){
									if($verbose){
										CLI::PrintActionStart('Executing SQL statements from ' . $file);
									}
									$contents = file_get_contents($this->getBaseDir() . $file);
									$execs = 0;
									$parser = new SQL_Parser_Dataset($contents, SQL_Parser::DIALECT_MYSQL);
									$datasets = $parser->parse();
									foreach($datasets as $ds){
										$ds->execute();
										$execs++;
									}
									if($verbose){
										CLI::PrintActionStatus(true);
									}
									$changes[] = 'Executed custom sql file ' . $file . ' and ran ' . $execs . ($execs == 1 ? ' query' : ' queries');
								}
								else{
									$changes[] = 'Ignoring invalid &lt;sql&gt; directive, no file attribute provided!';
								}
								break;
							case '#text':
								// This can be ignored without triggering any notice.
								break;
							default:
								$changes[] = 'Ignoring unsupported upgrade directive: [' . $child->nodeName . ']';
						}
					}

					// Record this change.
					$changes[] = 'Upgraded from [' . $this->_versionDB . '] to [' . $u->getAttribute('to') . ']';

					SystemLogModel::LogInfoEvent('/updater/component/upgrade', 'Component ' . $this->getName() . ' upgraded successfully from ' . $this->_versionDB . ' to ' . $u->getAttribute('to') . '!', implode("\n", $changes));

					if($to == 'next'){
						$canBeUpgraded = false;
					}
					else{
						$this->_versionDB = $to;
						$c = new ComponentModel($this->_name);
						$c->set('version', $this->_versionDB);
						$c->save();
					}
				}
			}
		}

		if(sizeof($changes) == 0 && $verbose){
			CLI::PrintLine('No changes performed.');
		}

		return (sizeof($changes)) ? $changes : false;
	}

	/**
	 * Query the registered licenser URL for this Component.
	 * 
	 * @throws Exception
	 * 
	 * @return array 'status' and 'message' are keys returned from this call.
	 */
	public function queryLicenser(){
		$data = $this->getLicenseData();
		
		// Every query voids the cache!
		$cacheKey = md5('LICENSER:' . SERVER_ID . $this->getKeyName());
		\Core\Cache::Delete($cacheKey);
		$this->_licenserFileData = null;
		
		// No data?  Simple!
		if(!sizeof($data)){
			return null;
		}
		
		$url = $data['url'];
		
		$r = new \Core\Filestore\Backends\FileRemote();
		// I need to use POST here with a payload because Apache 2.4 is blocking custom X- headers!
		$r->setMethod('POST');
		$r->setPayload(['serverid' => SERVER_ID]);
		// Send the component and version as GET parameters so the retrieving server can gather analytics on who is using what version.
		$r->setFilename($url . '/licenser?component=' . $this->getKeyName() . '&version=' . $this->getVersion());

		$contents = $r->getContents();
		
		if(strpos($contents, '-----BEGIN PGP MESSAGE-----') === false){
			return [
				'status' => false,
				'message' => 'Unexpected return from the server! ' . htmlentities($contents),
			];
		}
		
		// Ensure that this content matches out with the recorded key.
		//$sig = \Core\Filestore\Factory::File('tmp/gpg-verify-' . \Core::RandomHex(6) . '.asc');
		//$sig->putContents($contents);
		$gpg = new Core\GPG\GPG();
		$verify = $gpg->verifySignedData($contents);
		if(!$verify->isValid){
			return [
				'status' => false,
				'message' => 'Invalid GPG signed content from server!  Do you have the correct keys installed?',
			];
		}
		
		// Verification was successful!  Record this onto the component.
		$c = ComponentModel::Construct($this->_name);
		$c->set('license', $contents);
		$c->save();
		$this->_licenseDBData = $contents;
		
		return [
			'status' => true,
			'message' => 'Retrieved license successfully!',
		];
	}

	/**
	 * Internal function to parse and handle the configs in the component.xml file.
	 * This is used for installations and upgrades.
	 *
	 * @param boolean $install   Set to false to force uninstall/disable mode.
	 * @param int     $verbosity (default 0) 0: standard output, 1: real-time, 2: real-time verbose output.
	 *
	 * @return boolean | int
	 * @throws InstallerException
	 */
	public function _parseWidgets($install = true, $verbosity = 0) {
		$overallChanges  = [];
		$overallAction   = $install ? 'Installing' : 'Uninstalling';
		$overallActioned = $install ? 'Installed' : 'Uninstalled';
		$overallSet      = $install ? 'Set' : 'Remove';

		\Core\log_verbose($overallAction . ' Widgets for ' . $this->getName());

		if(!$install){
			die('@todo Support uninstalling widgets via _parseWidgets!');
		}

		// I need to get the schema definitions first.
		$node = $this->_xmlloader->getElement('widgets');
		//$prefix = $node->getAttribute('prefix');

		// Now, get every table under this node.
		foreach ($node->getElementsByTagName('widget') as $subnode) {
			$baseurl     = $subnode->getAttribute('baseurl');
			$installable = $subnode->getAttribute('installable');
			$title       = $subnode->getAttribute('title');

			if($verbosity == 2){
				CLI::PrintActionStart($overallAction . ' widget ' . $baseurl . ' ("' . $title . '")');
			}

			// Insert/Update the defaults for an entry in the database.
			$m = new WidgetModel($baseurl);
			$action = ($m->exists()) ? 'Updated' : 'Added';

			if (!$m->get('title')){
				// Only set the title if it was previously unset
				$m->set('title', $title);
			}

			$m->set('installable', $installable);
			$saved = $m->save();

			if ($saved){
				if($verbosity == 2){
					CLI::PrintActionStatus(true);
				}
				$changes[] = $action . ' widget [' . $m->get('baseurl') . ']';

				// Is this a new widget and it's an admin installable one?
				// If so install it to the admin widgetarea!
				if($action == 'Added' && $installable == '/admin'){
					$weight = WidgetInstanceModel::Count(
						[
							'widgetarea' => 'Admin Dashboard',
							'page_baseurl' => '/admin',
						]
					) + 1;

					$wi = new WidgetInstanceModel();
					$wi->setFromArray(
						[
							'baseurl' => $m->get('baseurl'),
							'page_baseurl' => '/admin',
							'widgetarea' => 'Admin Dashboard',
							'weight' => $weight
						]
					);
					$wi->save();

					$overallChanges[] = $overallActioned . ' widget ' . $m->get('baseurl') . ' into the admin dashboard!';
				}
			}
			else{
				if($verbosity == 2){
					CLI::PrintActionStatus('skip');
				}
			}
		}

		return (sizeof($overallChanges) > 0) ? $overallChanges : false;
	}


	/**
	 * Internal function to parse and handle the DBSchema in the component.xml file.
	 * This is used for installations and upgrades.
	 *
	 * @param boolean $install   Set to false to force uninstall/disable mode.
	 * @param int     $verbosity (default 0) 0: standard output, 1: real-time, 2: real-time verbose output.
	 *
	 * @throws DMI_Query_Exception
	 * @throws Exception
	 * @return boolean | int
	 */
	public function _parseDBSchema($install = true, $verbosity = 0) {
		// I need to get the schema definitions first.
		$node   = $this->_xmlloader->getElement('dbschema');
		$prefix = $node->getAttribute('prefix');
		/** @var \Core\Datamodel\BackendInterface $db */
		$db     = \Core\db();

		$changes = array();

		\Core\log_verbose('Installing database schema for ' . $this->getName());

		// Get the table structure as it exists in the database first, this will be the comparison point.
		$classes = $this->getModelList();

		// Do the actual processing of every Model.
		foreach ($classes as $m => $file) {
			if(!class_exists($m)) require_once($file);

			$schema = ModelFactory::GetSchema($m);
			$tablename = $m::GetTableName();

			if($verbosity == 2){
				CLI::PrintActionStart('Processing database table ' . $tablename);
			}

			try{
				if ($db->tableExists($tablename)) {
					// modifyTable will not change the table if there are no changes to perform and
					// will return a list of the changes for reporting reasons.
					$res = $db->modifyTable($tablename, $schema);
					if($res !== false){
						// Changes detected!
						$changes[] = 'Modified table ' . $tablename;
						// $changes[] = '[' . $d['type'] . '] ' . $d['title'];
						$changes = array_merge($changes, $res);
						if($verbosity == 2){
							CLI::PrintActionStatus('ok');
						}
					}
					else{
						// No changes detected.
						if($verbosity == 2){
							CLI::PrintActionStatus('skip');
						}
					}
				}
				else {
					// Pass this schema into the DMI processor for create table.
					\Core\db()->createTable($tablename, $schema);
					$changes[] = 'Created table ' . $tablename;
					if($verbosity == 2){
						CLI::PrintActionStatus('ok');
					}
				}
			}
			catch(DMI_Query_Exception $e){
				error_log($e->query . "\n<br/>(original table " . $tablename . ")");
				// Append the table name since otherwise it may be "_tmptable"... which does not provide any useful information!
				$e->query = $e->query . "\n<br/>(original table " . $tablename . ")";
				//echo '<pre>' . $e->getTraceAsString() . '</pre>'; // DEBUG //
				throw $e;
			}
		}

		return sizeof($changes) ? $changes : false;
	} // public function _parseDBSchema()

	/**
	 * Copy in all the assets for this component into the assets location.
	 *
	 * Returns false if nothing changed, else will return an array of all the changes that occured.
	 *
	 * @param boolean $install   Set to false to force uninstall/disable mode.
	 * @param int     $verbosity (default 0) 0: standard output, 1: real-time, 2: real-time verbose output.
	 *
	 * @return false | array
	 * @throws InstallerException
	 */
	public function _parseAssets($install = true, $verbosity = 0) {
		$assetbase = CDN_LOCAL_ASSETDIR;
		$theme     = ConfigHandler::Get('/theme/selected');
		$change    = '';
		$changes   = array();

		\Core\log_verbose('Installing assets for ' . $this->getName());

		foreach ($this->_xmlloader->getElements('/assets/file') as $node) {
			/** @var DOMElement $node */
			$b = $this->getBaseDir();

			// The new file should have a filename identical to the original, with the exception of
			// everything before the filename.. ie: the ROOT_PDIR and the asset directory.
			$newfilename = 'assets/' . substr($b . $node->getAttribute('filename'), strlen($this->getAssetDir()));

			// Before anything, check and see if this file has a custom override file present.
			if(file_exists(ROOT_PDIR . 'themes/custom/' . $newfilename)){
				// If so, then copy that asset to the custom directory too!
				$f = new \Core\Filestore\Backends\FileLocal(ROOT_PDIR . 'themes/custom/' . $newfilename);
				$srcname = '!CUSTOM!';
			}
			elseif(file_exists(ROOT_PDIR . 'themes/' . $theme . '/' . $newfilename)){
				// Allow the currently enabled theme to override assets too.
				$f = new \Core\Filestore\Backends\FileLocal(ROOT_PDIR . 'themes/' . $theme . '/' . $newfilename);
				$srcname = '-theme- ';
			}
			else{
				// Otherwise, the local file is guaranteed to be a local file.
				$f = new \Core\Filestore\Backends\FileLocal($b . $node->getAttribute('filename'));
				$srcname = 'original';
			}

			if($verbosity == 2){
				CLI::PrintActionStart('Installing ' . $srcname . ' asset ' . $f->getBasename());
			}

			$nf = \Core\Filestore\Factory::File($newfilename);
			//var_dump($newfilename, $nf->getFilename(), $nf);

			// If it's null, don't change the path any.
			/*if ($theme === null) {
				// Don't do anything.
			}
			// The new destination must be in the default directory, this is a
			// bit of a hack from the usual behaviour of the filestore system.
			elseif ($theme != 'default' && strpos($nf->getFilename(), $assetbase . $theme) !== false) {
				$nf->setFilename(str_replace($assetbase . $theme, $assetbase . 'default', $nf->getFilename()));
			}*/

			// Check if this file even needs updated. (this is primarily used for reporting reasons)
			$newfileexists    = $nf->exists();
			$newfileidentical = $nf->identicalTo($f);


			if(
				$newfileexists &&
				$newfileidentical &&
				$f instanceof \Core\Filestore\Backends\FileLocal &&
				$nf instanceof \Core\Filestore\Backends\FileLocal &&
				$f->getMTime() != $nf->getMTime()
			){
				// This is a bit of a hack because in 2.6.0 and above, the mtime is duplicated along with the contents.
				// This is to speed up file scans for local -> local disk changes.
				touch($nf->getFilename(), $f->getMTime());
				$change = 'Modified timestamp on ' . $nf->getFilename();
				$changes[] = $change;

				if($verbosity == 1){
					CLI::PrintLine($change);
				}
				elseif($verbosity == 2){
					CLI::PrintActionStatus('ok');
				}

				continue;
			}
			elseif($newfileexists && $newfileidentical){
				// The new file and old file are identical, just continue.

				if($verbosity == 2){
					CLI::PrintActionStatus('skip');
				}

				continue;
			}
			// Otherwise if it exists, I want to be able to inform the user that it was replaced and not just installed.
			elseif ($newfileexists) {
				$action = 'Replaced';
			}
			// Otherwise otherwise, it's a new file.
			else {
				$action = 'Installed';
			}


			try {
				$f->copyTo($nf, true);
			}
			catch (Exception $e) {
				throw new InstallerException('Unable to copy [' . $f->getFilename() . '] to [' . $nf->getFilename() . ']');
			}

			$change = $action . ' ' . $nf->getFilename();
			$changes[] = $change;

			if($verbosity == 1){
				CLI::PrintLine($change);
			}
			elseif($verbosity == 2){
				CLI::PrintActionStatus('ok');
			}
		}

		if (!sizeof($changes)){
			if($verbosity > 0){
				CLI::PrintLine('No changes required');
			}
			return false;
		}

		// Make sure the asset cache is purged!
		\Core\Cache::Delete('core-components');

		return $changes;
	}

	/**
	 * Internal function to parse and handle the configs in the component.xml file.
	 * This is used for installations and upgrades.
	 *
	 * Returns false if nothing changed, else will return an int of the number of configuration options changed.
	 *
	 * @param boolean $install   Set to false to force uninstall/disable mode.
	 * @param int     $verbosity (default 0) 0: standard output, 1: real-time, 2: real-time verbose output.
	 *
	 * @return boolean | int
	 * @throws InstallerException
	 */
	public function _parseConfigs($install = true, $verbosity = 0) {
		// Keep track of if this changed anything.
		$changes = array();

		$action = $install ? 'Installing' : 'Uninstalling';
		$set    = $install ? 'Set' : 'Removed';

		\Core\log_verbose($action . ' configs for ' . $this->getName());

		// I need to get the schema definitions first.
		$node = $this->_xmlloader->getElement('configs');
		//$prefix = $node->getAttribute('prefix');
		$componentName = $this->getKeyName();

		// Now, get every table under this node.
		foreach ($node->getElementsByTagName('config') as $confignode) {
			/** @var DOMElement $confignode */
			$key         = $confignode->getAttribute('key');
			$options     = $confignode->getAttribute('options');
			$type        = $confignode->getAttribute('type');
			$default     = $confignode->getAttribute('default');
			$title       = $confignode->getAttribute('title');
			$description = $confignode->getAttribute('description');
			$mapto       = $confignode->getAttribute('mapto');
			$encrypted   = $confignode->getAttribute('encrypted');
			$formAtts    = $confignode->getAttribute('form-attributes');

			if($encrypted === null || $encrypted === '') $encrypted = '0';

			// Default if omitted.
			if(!$type) $type = 'string';

			if($verbosity == 2){
				CLI::PrintActionStart($action . ' config ' . $key);
			}

			$m   = ConfigHandler::GetConfig($key);
			if($install){
				// Installation/Upgrade Logic
				$m->set('options', $options);
				$m->set('type', $type);
				$m->set('default_value', $default);
				$m->set('title', $title);
				$m->set('description', $description);
				$m->set('mapto', $mapto);
				$m->set('encrypted', $encrypted);
				$m->set('form_attributes', $formAtts);
				$m->set('component', $componentName);

				// Default from the xml, only if it's not already set.
				if ($m->get('value') === null || !$m->exists()){
					$m->set('value', $confignode->getAttribute('default'));
				}
				// Allow configurations to overwrite any value.  This is useful on the initial installation.
				if(\Core\Session::Get('configs/' . $key) !== null){
					$m->set('value', \Core\Session::Get('configs/' . $key));
				}

				if ($m->save()){
					$changes[] = $set . ' configuration [' . $m->get('key') . '] to [' . $m->get('value') . ']';
					if($verbosity == 2){
						CLI::PrintActionStatus(true);
					}
				}
				else{
					if($verbosity == 2){
						CLI::PrintActionStatus('skip');
					}
				}

				// Make it available immediately
				ConfigHandler::CacheConfig($m);
			}
			else{
				// Uninstallation Logic
				$m->delete();

				$changes[] = $set . ' configuration [' . $key . ']';
				if($verbosity == 2){
					CLI::PrintActionStatus(true);
				}
			}
		}

		return (sizeof($changes)) ? $changes : false;

	} // private function _parseConfigs

	/**
	 * Internal function to parse and handle the user configs in the component.xml file.
	 * This is used for installations and upgrades.
	 *
	 * Returns false if nothing changed, else will return an int of the number of configuration options changed.
	 *
	 * @param boolean $install   Set to false to force uninstall/disable mode.
	 * @param int     $verbosity (default 0) 0: standard output, 1: real-time, 2: real-time verbose output.
	 *
	 * @return boolean | int
	 * @throws InstallerException
	 */
	public function _parseUserConfigs($install = true, $verbosity = 0) {
		// If the class isn't available, don't do anything here.
		// This is possible if I'm currently loading the user component!
		if(!class_exists('UserConfigModel')) return false;

		// Keep track of if this changed anything.
		$changes = array();

		$action = $install ? 'Installing' : 'Uninstalling';

		\Core\log_verbose($action . ' User Configs for ' . $this->getName());

		// I need to get the schema definitions first.
		$node = $this->_xmlloader->getElement('userconfigs', false);

		if($node){
			trigger_error('Use of the &lt;userconfigs/&gt; metatag is deprecated in favour of the &lt;users/&gt; metatag.  (In the ' . $this->getName() . ' component)', E_USER_DEPRECATED);
		}
		else{
			// Try the 2.8 version, <users/>.
			$node = $this->_xmlloader->getElement('users');
		}

		// Now, get every table under this node.
		foreach ($node->getElementsByTagName('userconfig') as $confignode) {
			/** @var DOMElement $confignode */

			//<userconfig key="first_name" name="First Name"/>
			//<userconfig key="last_name" name="Last Name" default="" formtype="" onregistration="" options=""/>

			$key        = $confignode->getAttribute('key');
			$name       = $confignode->getAttribute('name');
			$default    = $confignode->getAttribute('default');
			$formtype   = $confignode->getAttribute('formtype');
			$onreg      = $confignode->getAttribute('onregistration');
			$onedit     = $confignode->getAttribute('onedit');
			$hidden     = $confignode->getAttribute('hidden');
			$options    = $confignode->getAttribute('options');
			$searchable = $confignode->getAttribute('searchable');
			$validation = $confignode->getAttribute('validation');
			$required   = $confignode->getAttribute('required');
			$weight     = $confignode->getAttribute('weight');

			// Defaults
			if($onreg === null)      $onreg = 1;
			if($onedit === null)     $onedit = 1;
			if($searchable === null) $searchable = 0;
			if($required === null)   $required = 0;
			if($weight === null)     $weight = 0;
			if($weight == '')        $weight = 0;
			if($hidden === null)     $hidden = 0;

			// OVERRIDES!
			// Any hidden config option must be set to not-onedit and not-onreg.
			if($hidden){
				$onedit = 0;
				$onreg  = 0;
			}

			if($verbosity == 2){
				CLI::PrintActionStart($action . ' userconfig ' . $key);
			}

			$model = UserConfigModel::Construct($key);
			$isnew = !$model->exists();

			if($install){
				// Installations create/save it!

				// First, all the default and non-editable fields.
				$model->set('default_name', $name);
				if($default)  $model->set('default_value', $default);
				if($formtype) $model->set('formtype', $formtype);
				$model->set('default_onregistration', $onreg);
				$model->set('default_onedit', $onedit);
				$model->set('searchable', $searchable);
				$model->set('hidden', $hidden);
				if($options)  $model->set('options', $options);
				$model->set('validation', $validation);
				$model->set('required', $required);
				$model->set('default_weight', $weight);

				// And now the admin-editable fields.
				// These only get set if the configuration option does not exist prior.
				if($isnew || $hidden){
					$model->set('name', $name);
					$model->set('onregistration', $onreg);
					$model->set('onedit', $onedit);
					$model->set('weight', $weight);
				}

				if($default)  $model->set('default_value', $default);
				if($formtype) $model->set('formtype', $formtype);

				if($model->save()){
					if($isnew){
						$changes[] = 'Created user config [' . $model->get('key') . '] as a [' . $model->get('formtype') . ' input]';
					}
					else{
						$changes[] = 'Updated user config [' . $model->get('key') . '] as a [' . $model->get('formtype') . ' input]';
					}
					if($verbosity == 2){
						CLI::PrintActionStatus(true);
					}
				}
				else{
					if($verbosity == 2){
						CLI::PrintActionStatus('skip');
					}
				}
			}
			else{
				// Uninstallations remove user configuration keys.
				$model->delete();
				$changes[] = 'Removed user config [' . $key . ']';
				if($verbosity == 2){
					CLI::PrintActionStatus(true);
				}
			}
		}

		return (sizeof($changes)) ? $changes : false;

	} // private function _parseUserConfigs

	/**
	 * Internal function to parse and handle the configs in the component.xml file.
	 * This is used for installations and upgrades.
	 *
	 * @param boolean $install   Set to false to force uninstall/disable mode.
	 * @param int     $verbosity (default 0) 0: standard output, 1: real-time, 2: real-time verbose output.
	 *
	 * @return boolean | int
	 * @throws InstallerException
	 */
	public function _parsePages($install = true, $verbosity = 0) {
		$changes = array();

		$overallAction = $install ? 'Installing' : 'Uninstalling';

		\Core\log_verbose($overallAction . ' pages for ' . $this->getName());

		// I need to get the schema definitions first.
		$node = $this->_xmlloader->getElement('pages');
		//$prefix = $node->getAttribute('prefix');

		// Now, get every table under this node.
		foreach ($node->getElementsByTagName('page') as $subnode) {
			/** @var DomElement $subnode */
			$baseurl = $subnode->getAttribute('baseurl');
			// Insert/Update the defaults for an entry in the database.
			// These are always global pages.
			$m = new PageModel(-1, $baseurl);

			if($verbosity == 2){
				CLI::PrintActionStart($overallAction . ' page ' . $baseurl);
			}

			// Hard-set pages get removed upon disabling.  They'll be recreated if re-enabled.
			if($install){
				// Just something to help the log.
				$action     = ($m->exists()) ? 'Updated' : 'Added';
				$admin      = $subnode->getAttribute('admin');
				$selectable = ($admin ? '0' : '1'); // Defaults
				$group      = ($admin ? $subnode->getAttribute('group') : '');
				if($subnode->getAttribute('selectable') !== ''){
					$selectable = $subnode->getAttribute('selectable');
				}
				$indexable = ($subnode->getAttribute('indexable') !== '') ? $subnode->getAttribute('indexable') : $selectable;
				$editurl = $subnode->getAttribute('editurl') ? $subnode->getAttribute('editurl') : '';
				$access = ($subnode->getAttribute('access')) ? $subnode->getAttribute('access') : null;

				// Do not "update" value, keep whatever the user set previously.
				if (!$m->get('rewriteurl')) {
					if ($subnode->getAttribute('rewriteurl')) $m->set('rewriteurl', $subnode->getAttribute('rewriteurl'));
					else $m->set('rewriteurl', $subnode->getAttribute('baseurl'));
				}
				// Do not "update" value, keep whatever the user set previously.
				if (!$m->get('title')) $m->set('title', $subnode->getAttribute('title'));

				if($access !== null){
					$m->set('access', $access);
				}
				
				if($subnode->hasAttribute('image')){
					// This page has an image attribute requested!
					$m->setMeta('image', $subnode->getAttribute('image'));
				}

				// Do not update parent urls if the page already exists.
				if(!$m->exists()) $m->set('parenturl', $subnode->getAttribute('parenturl'));
				//$m->set('widget', $subnode->getAttribute('widget'));
				$m->set('admin', $admin);
				$m->set('admin_group', $group);
				$m->set('selectable', $selectable);
				$m->set('indexable', $indexable);
				$m->set('component', $this->getKeyName());
				$m->set('editurl', $editurl);
				if ($m->save()){
					$changes[] = $action . ' page [' . $baseurl . ']';
					if($verbosity == 2){
						CLI::PrintActionStatus(true);
					}
				}
				else{
					if($verbosity == 2){
						CLI::PrintActionStatus('skip');
					}
				}
			}
			else{
				$m->delete();
				$changes[] = 'Removed page [' . $subnode->getAttribute('baseurl') . ']';
				if($verbosity == 2){
					CLI::PrintActionStatus(true);
				}
			}
		}

		return ($changes > 0) ? $changes : false;
	}

	/**
	 * Set this component as disabled in the database.
	 *
	 * Hopefully it won't break anything else :p
	 */
	public function disable(){
		// If it's not installed already, it can't be disabled!
		if(!$this->isInstalled()) return false;

		$c = new ComponentModel($this->_name);
		$c->set('enabled', false);
		$c->save();
		$this->_versionDB = null;
		$this->_enabled = false;

		$changed = array();

		$change = $this->_parseUserConfigs(false);
		if ($change !== false) $changed = array_merge($changed, $change);

		$change = $this->_parsePages(false);
		if ($change !== false) $changed = array_merge($changed, $change);

		if(sizeof($changed)){
			SystemLogModel::LogInfoEvent('/updater/component/disable', 'Component ' . $this->getName() . ' disabled successfully!', implode("\n", $changed));
		}

		// Do this when I actually have widgets to test.
		//$change = $this->_parseWidgets(false);
		//if ($change !== false) $changed = array_merge($changed, $change);

		// Ensure that the core component cache is purged too!
		\Core\Cache::Delete('core-components');

		return (sizeof($changed)) ? $changed : false;
	}

	/**
	 * Set this component as enabled in the database.
	 */
	public function enable(){
		// If it's not installed already, it can't be disabled!
		if($this->isEnabled()) return false;

		$c = new ComponentModel($this->_name);
		$c->set('enabled', true);
		$c->save();
		$this->_enabled = true;

		$changed = array();

		$change = $this->_parseUserConfigs();
		if ($change !== false) $changed = array_merge($changed, $change);

		$change = $this->_parsePages();
		if ($change !== false) $changed = array_merge($changed, $change);

		// Do this when I actually have widgets to test.
		//$change = $this->_parseWidgets();
		//if ($change !== false) $changed = array_merge($changed, $change);

		if(sizeof($changed)){
			SystemLogModel::LogInfoEvent('/updater/component/enable', 'Component ' . $this->getName() . ' enabled successfully!', implode("\n", $changed));
		}

		// Ensure that the core component cache is purged too!
		\Core\Cache::Delete('core-components');

		return (sizeof($changed)) ? $changed : false;
	}

	/**
	 * Helper function for external classes and scripts to get this component's xml DOM.
	 *
	 * @return DOMNode
	 */
	public function getRootDOM(){
		return $this->_xmlloader->getRootDOM();
	}

	/**
	 * Get the XML Loader backend of this component.
	 *
	 * Useful for manipulating the XML structure.
	 *
	 * @return XMLLoader
	 */
	public function getXML(){
		return $this->_xmlloader;
	}

	public function getProvides() {
		$ret = array();
		// This element itself.
		$ret[] = array(
			'name'    => strtolower($this->getName()),
			'type'    => 'component',
			'version' => $this->getVersion()
		);
		foreach ($this->_xmlloader->getElements('provides/provide') as $el) {
			// <provide name="JQuery" type="library" version="1.4"/>
			$ret[] = array(
				'name'    => strtolower($el->getAttribute('name')),
				'type'    => $el->getAttribute('type'),
				'version' => $el->getAttribute('version'),
			);
		}
		return $ret;
	}

	/**
	 * Get the base directory of this component
	 *
	 * Generally /home/foo/public_html/components/componentname/
	 *
	 * @param mixed|string $prefix base directory to use before the directory.
	 *
	 * @return string
	 */
	public function getBaseDir($prefix = ROOT_PDIR) {
		if ($this->_name == 'core') {
			return $prefix;
		}
		else {
			return $prefix . 'components/' . $this->getKeyName() . '/';
		}
	}


	/**
	 * Function to get any changed files in this component.
	 * A changed file is any file whose md5 doesn't match what's in the component.xml metafile.
	 *
	 * @return array
	 */
	public function getChangedFiles(){
		$changes = array();

		foreach($this->_xmlloader->getElements('/files/file') as $file){
			// <file filename="CHANGELOG" md5="e55e5457abe2f0a3a8edcb6a927c92e3"/>
			/** @var $md5 string */
			$md5 = $file->getAttribute('md5');
			/** @var $filename string */
			$filename = $file->getAttribute('filename');

			// Skip the changelog.
			if($filename == 'CHANGELOG' || $filename == 'core/CHANGELOG') continue;

			/** @var $object \Core\Filestore\Backends\FileLocal */
			$object = \Core\Filestore\Factory::File($this->getBaseDir() . $filename);

			if($object->getHash() != $md5){
				$changes[] = $filename;
			}
		}
		return $changes;
	}

	/**
	 * Function to get any changed templates in this component.
	 * A changed file is any file whose md5 doesn't match what's in the component.xml metafile.
	 *
	 * @return array
	 */
	public function getChangedTemplates(){
		$changes = array();

		foreach($this->_xmlloader->getElements('/templates/file') as $file){
			// <file filename="CHANGELOG" md5="e55e5457abe2f0a3a8edcb6a927c92e3"/>
			/** @var $md5 string */
			$md5 = $file->getAttribute('md5');
			/** @var $filename string */
			$filename = $file->getAttribute('filename');

			/** @var $object \Core\Filestore\Backends\FileLocal */
			$object = \Core\Filestore\Factory::File($this->getBaseDir() . $filename);

			if($object->getHash() != $md5){
				$changes[] = $filename;
			}
		}
		return $changes;
	}


	/**
	 * Function to get any changed templates in this component.
	 * A changed file is any file whose md5 doesn't match what's in the component.xml metafile.
	 *
	 * @return array
	 */
	public function getChangedAssets(){
		$changes = array();

		foreach($this->_xmlloader->getElements('/assets/file') as $file){
			// <file filename="CHANGELOG" md5="e55e5457abe2f0a3a8edcb6a927c92e3"/>
			/** @var $md5 string */
			$md5 = $file->getAttribute('md5');
			/** @var $filename string */
			$filename = $file->getAttribute('filename');

			/** @var $object \Core\Filestore\Backends\FileLocal */
			$object = \Core\Filestore\Factory::File($this->getBaseDir() . $filename);

			if($object->getHash() != $md5){
				$changes[] = $filename;
			}
		}
		return $changes;
	}



	/**
	 * Component installation operations all share common actions, (mostly).
	 *
	 * Returns false if nothing changed, else will return an array containing all changes.
	 *
	 * @param int $verbosity 0 for standard output, 1 for real-time, 2 for real-time verbose output.
	 *
	 * @return false | array
	 * @throws InstallerException
	 */
	private function _performInstall($verbosity = 0) {
		// make sure that some of the installer elements are available!
		require_once(ROOT_PDIR . 'core/libs/core/InstallerException.php'); #SKIPCOMPILER

		$changed = array();

		$change = $this->_parseDBSchema(true, $verbosity);
		if ($change !== false){
			$changed = array_merge($changed, $change);
		}

		$change = $this->_parseConfigs(true, $verbosity);
		if ($change !== false){
			$changed = array_merge($changed, $change);
		}

		$change = $this->_parseUserConfigs(true, $verbosity);
		if ($change !== false){
			$changed = array_merge($changed, $change);
		}

		$change = $this->_parsePages(true, $verbosity);
		if ($change !== false){
			$changed = array_merge($changed, $change);
		}

		$change = $this->_parseWidgets(true, $verbosity);
		if ($change !== false){
			$changed = array_merge($changed, $change);
		}

		$change = $this->_parseAssets(true, $verbosity);
		if ($change !== false){
			$changed = array_merge($changed, $change);
		}

		// Core has some additional things that need to ran through.
		if($this->getKeyName() == 'core'){
			// Make sure that files/private has a restrictive .htaccess file installed.
			$f = \Core\Filestore\Factory::File('private/.htaccess');
			if(!$f->exists() && $f->isWritable()){
				$src = \Core\Filestore\Factory::File('core/htaccess.private');
				if($src->copyTo($f)){
					$changed[] = 'Installed private htaccess file into ' . $f->getFilename();
				}
			}

			// Make sure that files/public has the appropriate .htaccess file installed.
			$f = \Core\Filestore\Factory::File('public/.htaccess');
			if(!$f->exists() && $f->isWritable()){
				$src = \Core\Filestore\Factory::File('core/htaccess.public');
				if($src->copyTo($f)){
					$changed[] = 'Installed public htaccess file into ' . $f->getFilename();
				}
			}

			// Make sure that files/public has the appropriate .htaccess file installed.
			$f = \Core\Filestore\Factory::File('asset/.htaccess');
			// This is a bit of a hack because I need the parent directory for assets, not the theme-specific version.
			$f->setFilename(dirname(dirname($f->getFilename())) . '/.htaccess');
			if(!$f->exists() && $f->isWritable()){
				$src = \Core\Filestore\Factory::File('core/htaccess.assets');
				if($src->copyTo($f)){
					$changed[] = 'Installed assets htaccess file into ' . $f->getFilename();
				}
			}
		}

		// Ensure that the core component cache is purged too!
		\Core\Cache::Delete('core-components');

		return (sizeof($changed)) ? $changed : false;
	}

	/**
	 * Internal function to parse and handle the dataset in the <upgrade> and <install> tasks.
	 * This is used for installations and upgrades.
	 *
	 * Unlike the other parse functions, this handles a single node at a time.
	 *
	 * @param $node DOMElement
	 * @param $verbose bool
	 *
	 * @throws InstallerException
	 */
	private function _parseDatasetNode(DOMElement $node, $verbose = false){
		$action   = $node->getAttribute('action');
		$table    = $node->getAttribute('table');
		$haswhere = false;
		$sets     = array();
		$renames  = array();
		$ds       = new Core\Datamodel\Dataset();


		$ds->table($table);

		foreach($node->getElementsByTagName('datasetset') as $el){
			$sets[$el->getAttribute('key')] = $el->nodeValue;
		}

		foreach($node->getElementsByTagName('datasetrenamecolumn') as $el){
			// <datasetrenamecolumn oldname="ID" newname="id"/>
			$renames[$el->getAttribute('oldname')] = $el->getAttribute('newname');
		}

		foreach($node->getElementsByTagName('datasetwhere') as $el){
			$haswhere = true;
			$ds->where(trim($el->nodeValue));
		}

		switch($action){
			case 'alter':
				if(sizeof($sets)) throw new InstallerException('Invalid mix of arguments on ' . $action . ' dataset request, datasetset is not supported!');
				if($haswhere) throw new InstallerException('Invalid mix of arguments on ' . $action . ' dataset request, datasetwhere is not supported!');

				foreach($renames as $k => $v){
					// ALTER TABLE `controllers` CHANGE `ID` `id` INT( 11 ) NOT NULL AUTO_INCREMENT
					$ds->renameColumn($k, $v);
				}
				break;
			case 'update':
				foreach($sets as $k => $v){
					$ds->update($k, $v);
				}
				break;
			case 'insert':
				foreach($sets as $k => $v){
					$ds->insert($k, $v);
				}
				break;
			case 'delete':
				if(sizeof($sets)) throw new InstallerException('Invalid mix of arguments on ' . $action . ' dataset request');
				if(!$haswhere) throw new InstallerException('Cowardly refusing to delete with no where statement');
				$ds->delete();
				break;
			default:
				throw new InstallerException('Invalid action type, '. $action);
		}

		// and GO!
		if($verbose){
			CLI::PrintActionStart('Executing dataset ' . $action . ' command on ' . $table);
		}

		$ds->execute();
		if($ds->num_rows){
			CLI::PrintActionStatus(true);
			return array($action . ' on table ' . $table . ' affected ' . $ds->num_rows . ' records.');
		}
		else{
			CLI::PrintActionStatus(false);
			return false;
		}
	}

	/**
	 * @param $filename
	 * @param $verbose bool Set to true for verbose real-time output.
	 *
	 */
	private function _includeFileForUpgrade($filename, $verbose = false){
		if($verbose){
			CLI::PrintLine('Loading custom PHP file ' . $filename);
		}
		include($filename);
	}




	/**
	 * Helper function to see if there is a valid upgrade path from the current version installed
	 * to the version of the code available.
	 *
	 * @return bool
	 */
	private function _checkUpgradePath(){
		// Check that if the version installed is not what's in the component file, that there is a valid upgrade path.
		if($this->_versionDB && $this->_version != $this->_versionDB){

			// Assemble an array of upgrade paths, with the key/pairs being from//to versions.
			$paths = array();

			foreach ($this->_xmlloader->getRootDOM()->getElementsByTagName('upgrade') as $u) {
				$from = $u->getAttribute('from');
				$to   = $u->getAttribute('to');
				if(!isset($paths[$from])) $paths[$from] = array();

				$paths[$from][] = $to;
			}

			if(!sizeof($paths)){
				// No upgrade paths even defined!
				return false;
			}

			// Sort them version descending, makes finding the highest version number easier
			foreach($paths as $k => $vs){
				rsort($paths[$k], SORT_NATURAL);
			}
			$current = $this->_versionDB;
			$x = 0; // My anti-infinite-loop counter.
			while($current != $this->_version && $x < 20){
				++$x;
				if(isset($paths[$current])){
					$current = $paths[$current][0];
				}
				else{
					return false;
				}
			}

			// Yay, if it's gotten here, that means that there was a valid upgrade path!
			return true;
		}
		else{
			// Easy enough :)
			// The else is that it's installed and up to date.
			return true;
		}
	}
}
