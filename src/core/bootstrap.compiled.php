<?php
/**
 * Core bootstrap (COMPILED) file that kicks off the entire application
 *
 * This file is the core of the application; it's responsible for setting up
 *  all the necessary paths, settings and includes.
 *
 * In addition, it has been compiled to include the source from the many included files automatically.
 * To manage some code here, please see which file the code is being included from, (as stated in the comment above
 * the respective code), edit there and re-run utilities/compiler.php
 *
 * @package Core\Core
 * @since 2.1.5
 * @author Charlie Powell <charlie@evalagency.com>
 * @copyright Copyright (C) 2009-2015  Charlie Powell
 * @license     GNU Affero General Public License v3 <http://www.gnu.org/licenses/agpl-3.0.txt>
 *
 * @compiled Thu, 10 Dec 2015 21:33:28 -0500
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


//===========================================================================\\
//                                       _____________________________       \\
//                                      |                             |      \\
//                                      |  You're in the wrong file!  |      \\
//                                      |_____    ____________________|      \\
//                                            \  /                           \\
//                                             \/                            \\
//                                                                           \\
//                                           _/|__                           \\
//                       _,-------,        _/ -|  \_     /~>.                \\
//                    _-~ __--~~/\ |      (  \   /  )   | / |                \\
//                 _-~__--    //   \\      \ *   * /   / | ||                \\
//              _-~_--       //     ||      \     /   | /  /|                \\
//             ~ ~~~~-_     //       \\     |( " )|  / | || /                \\
//                     \   //         ||    | VWV | | /  ///                 \\
//               |\     | //           \\ _/      |/ | ./|                   \\
//               | |    |// __         _-~         \// |  /                  \\
//              /  /   //_-~  ~~--_ _-~  /          |\// /                   \\
//             |  |   /-~        _-~    (     /   |/ / /                     \\
//            /   /           _-~  __    |   |____|/                         \\
//           |   |__         / _-~  ~-_  (_______  `\                        \\
//           |      ~~--__--~ /  _     \        __\)))                       \\
//            \               _-~       |     ./  \                          \\
//             ~~--__        /         /    _/     |                         \\
//                   ~~--___/       _-_____/      /                          \\
//                    _____/     _-_____/      _-~                           \\
//                 /^<  ___       -____         -____                        \\
//                    ~~   ~~--__      ``\--__       ``\                     \\
//                               ~~--\)\)\)   ~~--\)\)\)                     \\
//                                                                           \\
//===========================================================================\\

namespace  {
if (basename($_SERVER['SCRIPT_NAME']) == 'bootstrap.php') die('You cannot call that file directly.');
if (PHP_VERSION < '6.0.0' && ini_get('magic_quotes_gpc')) {
die('This application cannot run with magic_quotes_gpc enabled, please disable them now!');
}
if (PHP_VERSION < '5.4.0') {
die('This application requires at least PHP 5.4 to run!');
}
umask(0);
### REQUIRE_ONCE FROM core/libs/core/utilities/profiler/Profiler.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Utilities\Profiler {
use Core\i18n\I18NLoader;
class Profiler {
private $_name;
private $_events = array();
private $_microtime;
private static $_DefaultProfiler;
public function __construct($name){
$this->_name = $name;
$this->_microtime = microtime(true);
$this->record('Starting profiler ' . $name);
if(self::$_DefaultProfiler === null){
self::$_DefaultProfiler = $this;
}
}
public function record($event){
$now = microtime(true);
$time = $now - $this->_microtime;
$this->_events[] = array(
'event'     => $event,
'microtime' => $now,
'timetotal' => $time,
'memory'  => memory_get_usage(true),
);
}
public function getTime(){
return microtime(true) - $this->_microtime;
}
public function getEvents(){
return $this->_events;
}
public function getTimeFormatted(){
$time = $this->getTime();
if($time < 0.001){
return round($time, 4) * 1000000 . ' µs';
}
elseif($time < 2.0){
return round($time, 4) * 1000 . ' ms';
}
elseif($time < 120){
return round($time, 0) . ' s';
}
elseif($time < 3600) {
$m = round($time, 0) / 60;
$s = round($time - $m*60, 0);
return $m . ' m ' . $s . ' s';
}
else{
$h = round($time, 0) / 3600;
$m = round($time - $h*3600, 0);
return $h . ' h ' . $m . ' m';
}
}
public function getEventTimesFormatted(){
$out = '';
foreach ($this->getEvents() as $t) {
$in = round($t['timetotal'], 5) * 1000;
$dcm = I18NLoader::GetLocaleConv('decimal_point');
if ($in == 0){
$time = '0000' . $dcm . '00 ms';
}
else{
$parts = explode($dcm, $in);
$whole = str_pad($parts[0], 4, 0, STR_PAD_LEFT);
$dec   = (isset($parts[1])) ? str_pad($parts[1], 2, 0, STR_PAD_RIGHT) : '00';
$time = $whole . $dcm . $dec . ' ms';
}
$mem = '[mem: ' . \Core\Filestore\format_size($t['memory']) . '] ';
$event = $t['event'];
$out .= "[$time] $mem- $event" . "\n";
}
return $out;
}
public static function GetDefaultProfiler(){
if(self::$_DefaultProfiler === null){
global $profiler;
if($profiler){
self::$_DefaultProfiler = $profiler;
}
else{
self::$_DefaultProfiler = new self('Default');
}
}
return self::$_DefaultProfiler;
}
}
} // ENDING NAMESPACE Core\Utilities\Profiler

namespace  {

### REQUIRE_ONCE FROM core/libs/core/utilities/profiler/DatamodelProfiler.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Utilities\Profiler {
use Core\Session;
class DatamodelProfiler {
private $_name;
private $_events = [];
private $_last = [];
private static $_DefaultProfiler;
private $_reads = 0;
private $_writes = 0;
public function __construct($name){
$this->_name = $name;
if(self::$_DefaultProfiler === null){
self::$_DefaultProfiler = $this;
}
}
public function readCount(){
return $this->_reads;
}
public function writeCount(){
return $this->_writes;
}
public function start($type, $query){
if(FULL_DEBUG || (DEVELOPMENT_MODE && sizeof($this->_events) < 40)){
$debug = debug_backtrace();
$callinglocation = array();
$count = 0;
$totalcount = 0;
foreach($debug as $d){
$class = (isset($d['class'])) ? $d['class'] : null;
++$totalcount;
if(strpos($class, 'Core\\Datamodel') === 0) continue;
if(strpos($class, 'Core\\Utilities\\Profiler') === 0) continue;
if($class == 'Model') continue;
$file = (isset($d['file'])) ? (substr($d['file'], strlen(ROOT_PDIR))) : 'anonymous';
$line = (isset($d['line'])) ? (':' . $d['line']) : '';
$func = ($class !== null) ? ($d['class'] . $d['type'] . $d['function']) : $d['function'];
$callinglocation[] = $file . $line . ', [' . $func . '()]';
++$count;
if($count >= 3 && sizeof($debug) >= $totalcount + 2){
$callinglocation[] = '...';
break;
}
}
}
else{
$callinglocation = ['**SKIPPED**  Please enable FULL_DEBUG to see the calling stack.'];
}
$this->_last[] = [
'start' => microtime(true),
'type' => $type,
'query' => $query,
'caller' => $callinglocation,
'memory'  => memory_get_usage(true),
];
}
public function stopSuccess($count){
if(sizeof($this->_last) == 0){
return;
}
$last = array_pop($this->_last);
$time = microtime(true) - $last['start'];
$timeFormatted = $this->getTimeFormatted($time);
if($last['type'] == 'read'){
++$this->_reads;
}
else{
++$this->_writes;
}
if(DEVELOPMENT_MODE){
$events = Session::Get('datamodel_profiler_events/events', []);
$events[] = array(
'query'  => $last['query'],
'type'   => $last['type'],
'time'   => $timeFormatted,
'errno'  => null,
'error'  => '',
'caller' => $last['caller'],
'rows'   => $count
);
Session::Set('datamodel_profiler_events/events', $events);
if($last['type'] == 'read'){
Session::Set('datamodel_profiler_events/reads', Session::Get('datamodel_profiler_events/reads') + 1);
}
else{
Session::Set('datamodel_profiler_events/writes', Session::Get('datamodel_profiler_events/writes') + 1);
}
}
if(defined('DMI_QUERY_LOG_TIMEOUT') && DMI_QUERY_LOG_TIMEOUT >= 0){
if(DMI_QUERY_LOG_TIMEOUT == 0 || ($time * 1000) >= DMI_QUERY_LOG_TIMEOUT ){
\Core\Utilities\Logger\append_to('query', '[' . $timeFormatted . '] ' . $last['query'], 0);
}
}
}
public function stopError($code, $error){
if(sizeof($this->_last) == 0){
return;
}
$last = array_pop($this->_last);
$time = microtime(true) - $last['start'];
$timeFormatted = $this->getTimeFormatted($time);
if($last['type'] == 'read'){
++$this->_reads;
}
else{
++$this->_writes;
}
if(DEVELOPMENT_MODE) {
$events   = Session::Get('datamodel_profiler_events/events', []);
$events[] = [
'query'  => $last['query'],
'type'   => $last['type'],
'time'   => $timeFormatted,
'errno'  => $code,
'error'  => $error,
'caller' => $last['caller'],
'rows'   => 0
];
Session::Set('datamodel_profiler_events/events', $events);
if($last['type'] == 'read') {
Session::Set('datamodel_profiler_events/reads', Session::Get('datamodel_profiler_events/reads') + 1);
}
else {
Session::Set('datamodel_profiler_events/writes', Session::Get('datamodel_profiler_events/writes') + 1);
}
}
if(defined('DMI_QUERY_LOG_TIMEOUT') && DMI_QUERY_LOG_TIMEOUT >= 0){
if(DMI_QUERY_LOG_TIMEOUT == 0 || ($time * 1000) >= DMI_QUERY_LOG_TIMEOUT ){
\Core\Utilities\Logger\append_to('query', '[' . $timeFormatted . '] ' . $last['query'], 0);
}
}
}
public function getEvents(){
return Session::Get('datamodel_profiler_events/events', []);
}
public function getTimeFormatted($time){
if($time < 0.001){
return round($time, 4) * 1000000 . ' µs';
}
elseif($time < 2.0){
return round($time, 4) * 1000 . ' ms';
}
elseif($time < 120){
return round($time, 0) . ' s';
}
elseif($time < 3600) {
$m = round($time, 0) / 60;
$s = round($time - $m*60, 0);
return $m . ' m ' . $s . ' s';
}
else{
$h = round($time, 0) / 3600;
$m = round($time - $h*3600, 0);
return $h . ' h ' . $m . ' m';
}
}
public function getEventTimesFormatted(){
$out = '';
$ql = $this->getEvents();
$qls = sizeof($this->_events);
foreach($ql as $i => $dat){
if($i > 1000){
$out .= 'Plus ' . ($qls - 1000) . ' more!' . "\n";
break;
}
$typecolor = ($dat['type'] == 'read') ? '#88F' : '#005';
$tpad   = ($dat['type'] == 'read') ? '  ' : ' ';
$type   = $dat['type'];
$time   = str_pad($dat['time'], 5, '0', STR_PAD_RIGHT);
$query  = $dat['query'];
$caller = print_r($dat['caller'], true);
if($dat['rows'] !== null){
$caller .= "\n" . 'Number of affected rows: ' . $dat['rows'];
}
$out .= sprintf(
"<span title='%s'><span style='color:%s;'>[%s]</span>%s[%s] %s</span>\n",
$caller,
$typecolor,
$type,
$tpad,
$time,
htmlentities($query, ENT_QUOTES | ENT_HTML5)
);
}
Session::UnsetKey('datamodel_profiler_events/*');
return $out;
}
public static function GetDefaultProfiler(){
if(self::$_DefaultProfiler === null){
global $datamodelprofiler;
if($datamodelprofiler){
self::$_DefaultProfiler = $datamodelprofiler;
}
else{
self::$_DefaultProfiler = new self('Query Log');
}
}
return self::$_DefaultProfiler;
}
}
} // ENDING NAMESPACE Core\Utilities\Profiler

namespace  {

### REQUIRE_ONCE FROM core/libs/core/utilities/logger/functions.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Utilities\Logger {
use Core\Utilities\Profiler\Profiler;
const DEBUG_LEVEL_LOG = 1; // Basic debugging written to the error log.
const DEBUG_LEVEL_FULL = '5'; // Core debug level, typically not required unless working on the core.
function write_debug($message, $level = DEBUG_LEVEL_FULL){
if($level >= DEBUG_LEVEL_FULL && !FULL_DEBUG) return;
$profiler = Profiler::GetDefaultProfiler();
$time = $profiler->getTime();
$time = str_pad(number_format(round($time, 6) * 1000, 2), 7, '0', STR_PAD_LEFT);
if (EXEC_MODE == 'CLI'){
echo '[ DEBUG ' . $time . ' ms ] - ' . $message . "\n";
}
elseif($level == DEBUG_LEVEL_LOG){
error_log('[ DEBUG ' . $time . ' ms ] - ' . $message);
}
else{
echo '<pre class="xdebug-var-dump screen">[' . $time . ' ms] ' . $message . '</pre>';
}
}
function append_to($filebase, $message, $code = null){
if(class_exists('Core\\Utilities\\Logger\\LogFile')){
$log = new LogFile($filebase);
$log->write($message, $code);
}
else{
error_log($message);
}
}
} // ENDING NAMESPACE Core\Utilities\Logger

namespace  {

$profiler = new Core\Utilities\Profiler\Profiler('Core Plus');
mb_internal_encoding('UTF-8');
### REQUIRE_ONCE FROM core/bootstrap_predefines.php
if (PHP_VERSION < '6.0.0' && ini_get('magic_quotes_gpc')) {
die('This application cannot run with magic_quotes_gpc enabled, please disable them now!' . "\n");
}
if (PHP_VERSION < '5.4.0') {
die('This application requires at least PHP 5.4 to run!' . "\n");
}
if (isset($_SERVER['SHELL'])) {
$em = 'CLI';
$rpdr = realpath(__DIR__ . '/../') . '/';
$rwdr = null;
$rip  = '127.0.0.1';
}
else {
$em  = 'WEB';
$rip = '127.0.0.1';
$rpdr = pathinfo(realpath($_SERVER['SCRIPT_FILENAME']), PATHINFO_DIRNAME);
if ($rpdr != '/') $rpdr .= '/'; // Append a slash if it's not the root dir itself.
$rwdr = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
if ($rwdr != '/') $rwdr .= '/'; // Append a slash if it's not the root dir itself.
$rip = $_SERVER['REMOTE_ADDR'];
}
define('EXEC_MODE', $em);
if (!defined('ROOT_PDIR')) define('ROOT_PDIR', $rpdr);
if (!defined('ROOT_WDIR')) define('ROOT_WDIR', $rwdr);
define('REMOTE_IP', $rip);
define('FULL_DEBUG', false);
define('NL', "\n");
define('TAB', "\t");
define('DS', DIRECTORY_SEPARATOR);
##
# Gimme some colors!
# These are used to prettify the terminal.
# Color 1 is always standard and
# Color 2 is always the bold version.
if(EXEC_MODE == 'CLI'){
define('COLOR_LINE', "\033[0;30m");
define('COLOR_HEADER', "\033[1;36m");
define('COLOR_SUCCESS', "\033[1;32m");
define('COLOR_WARNING', "\033[1;33m");
define('COLOR_ERROR', "\033[1;31m");
define('COLOR_DEBUG', "\033[0;34m");
define('COLOR_NORMAL', "\033[0m");
define('COLOR_RESET', "\033[0m");
define('NBSP', ' ');
}
else{
define('COLOR_LINE', "<span style='color:grey; font-family:Courier,mono;'>");
define('COLOR_HEADER', "<span style='color:cyan; font-weight:bold; font-family:Courier,mono;'>");
define('COLOR_SUCCESS', "<span style='color:green; font-weight:bold; font-family:Courier,mono;'>");
define('COLOR_WARNING', "<span style='color:yellow; font-weight:bold; font-family:Courier,mono;'>");
define('COLOR_ERROR', "<span style='color:red; font-weight:bold; font-family:Courier,mono;'>");
define('COLOR_DEBUG', "<span style='color:lightskyblue; font-family:Courier,mono;'>");
define('COLOR_NORMAL', "<span style='font-family:Courier,mono;'>");
define('COLOR_RESET', "</span>");
define('NBSP', '&nbsp;');
}
unset($em, $rpdr, $rwdr, $rip);
define('SECONDS_ONE_MINUTE', 60);
define('SECONDS_ONE_HOUR',   3600);
define('SECONDS_TWO_HOUR',   7200);
define('SECONDS_ONE_DAY',    86400);
define('SECONDS_ONE_WEEK',   604800);  // 7 days
define('SECONDS_TWO_WEEK',   1209600); // 14 days
define('SECONDS_ONE_MONTH',  2592000); // 30 days
define('SECONDS_TWO_MONTH',  5184000); // 60 days


Core\Utilities\Logger\write_debug('Starting Application');
Core\Utilities\Logger\write_debug('Loading pre-include files');
### REQUIRE_ONCE FROM core/bootstrap_preincludes.php
### REQUIRE_ONCE FROM core/libs/core/ISingleton.interface.php
Interface ISingleton {
public static function Singleton();
}


### REQUIRE_ONCE FROM core/libs/core/XMLLoader.class.php
class XMLLoader implements Serializable {
protected $_rootname;
protected $_filename;
protected $_file;
protected $_DOM;
private $_rootnode = null;
protected $_schema = null;
public function serialize(){
$dat = array(
'rootname' => $this->_rootname,
'filename' => $this->_filename,
'file' => $this->_file,
'dom' => $this->getDOM()->saveXML()
);
$dat['dom'] = base64_encode(gzcompress($dat['dom']));
return serialize($dat);
}
public function unserialize($serialized){
$dat = unserialize($serialized);
$this->_rootname = $dat['rootname'];
$this->_filenme = $dat['filename'];
$this->_file = $dat['file'];
$this->_rootnode = null;
$this->_DOM = new DOMDocument();
$this->_DOM->formatOutput = true;
$this->_DOM->loadXML(gzuncompress(base64_decode($dat['dom'])));
}
public function load() {
if (!$this->_rootname) return false;
if($this->_schema){
$implementation = new DOMImplementation();
$dtd = $implementation->createDocumentType($this->_rootname, 'SYSTEM', $this->_schema);
$this->_DOM = $implementation->createDocument('', '', $dtd);
}
else{
$this->_DOM = new DOMDocument();
}
$this->_DOM->encoding = 'UTF-8';
$this->_DOM->formatOutput = true;
if ($this->_file) {
$contents = $this->_file->getContentsObject();
if (is_a($contents, '\Core\Filestore\Contents\ContentGZ')) {
$dat = $contents->uncompress();
}
else {
$dat = $contents->getContents();
}
if(!$dat){
return false;
}
$this->_DOM->loadXML($dat);
}
elseif ($this->_filename) {
if (!$this->_DOM->load($this->_filename)) return false;
}
else {
return false;
}
return true;
}
public function loadFromFile($file) {
if (is_a($file, '\\Core\\Filestore\\File')) {
$this->_file = $file;
}
else {
$this->_file = \Core\Filestore\Factory::File($file);
}
return $this->load();
}
public function loadFromNode(DOMNode $node) {
$this->_DOM = new DOMDocument();
$this->_DOM->formatOutput = true;
$nn = $this->_DOM->importNode($node, true);
$this->_DOM->appendChild($nn);
return true;
}
public function loadFromString($string){
if (!$this->_rootname) return false;
$this->_DOM = new DOMDocument();
$this->_DOM->formatOutput = true;
$this->_DOM->loadXML($string);
return true;
}
public function setFilename($file) {
$this->_filename = $file;
}
public function setRootName($name) {
$this->_rootname = $name;
}
public function setSchema($url){
$this->_schema = $url;
if($this->_DOM !== null && $this->_schema != $this->_DOM->doctype->systemId){
$implementation = new DOMImplementation();
$dtd = $implementation->createDocumentType($this->_rootname, 'SYSTEM', $this->_schema);
$newdom = $implementation->createDocument('', '', $dtd);
$root = $this->_DOM->getElementsByTagName($this->_rootname)->item(0);
$newroot = $newdom->importNode($root, true);
$newdom->appendChild($newroot);
$this->_DOM = $newdom;
$this->_rootnode = null;
}
}
public function getRootDOM() {
if($this->_DOM === null){
$this->load();
}
if($this->_rootnode === null){
$root = $this->_DOM->getElementsByTagName($this->_rootname);
if ($root->item(0) === null) {
$root = $this->_DOM->createElement($this->_rootname);
$this->_DOM->appendChild($root);
$this->_rootnode = $root; // Because it's already the item.
}
else {
$this->_rootnode = $root->item(0);
}
}
return $this->_rootnode;
}
public function getDOM() {
return $this->_DOM;
}
public function getElementsByTagName($name) {
return $this->_DOM->getElementsByTagName($name);
}
public function getElementByTagName($name) {
return $this->_DOM->getElementsByTagName($name)->item(0);
}
public function getElement($path, $autocreate = true) {
return $this->getElementFrom($path, false, $autocreate);
}
public function getElementFrom($path, $el = false, $autocreate = true) {
if (!$el) $el = $this->getRootDOM();
$path = $this->_translatePath($path);
$list = $this->getElementsFrom($path, $el);
if ($list->item(0)) return $list->item(0);
if (!$autocreate) return null;
return $this->createElement($path, $el);
}
private function _translatePath($path) {
if (preg_match(':^/[^/]:', $path)) {
if(strpos($path,  '/' . $this->getRootDOM()->tagName) === 0){
$path = '/' . $path;
}
else{
$path = '//' . $this->getRootDOM()->tagName . $path;
}
}
return $path;
}
public function createElement($path, $el = false, $forcecreate = 0) {
if (!$el){
$el = $this->getRootDOM();
$path = $this->_translatePath($path);
if(strpos($path, '//' . $this->getRootDOM()->nodeName) === 0){
$path = substr($path, strlen($this->getRootDOM()->nodeName) + 3);
}
}
else{
$path = $this->_translatePath($path);
if($el == $this->getRootDOM()){
if(strpos($path, '//' . $this->getRootDOM()->nodeName) === 0){
$path = substr($path, strlen($this->getRootDOM()->nodeName) + 3);
}
}
elseif($path{0} == '/'){
throw new Exception('Unable to append path ' . $path . ' onto an element from an absolute url!');
}
}
if($forcecreate == 0){
$createlast = false;
$createall  = false;
}
elseif($forcecreate == 1){
$createlast = true;
$createall  = false;
}
elseif($forcecreate == 2){
$createlast = true;
$createall  = true;
}
else{
throw new Exception('Unknown value provided for $forcecreate, please ensure it is one of the following [0, 1, 2]');
}
$xpath = new DOMXPath($this->_DOM);
$patharray = array();
if (strpos($path, '/') === false) {
$patharray[] = $path;
}
elseif (strpos($path, '[') === false) {
$patharray = explode('/', $path);
}
else {
$len    = strlen($path);
$inatt  = false;
$curstr = '';
for ($x = 0; $x < $len; $x++) {
$chr = $path{$x};
if ($chr == '/' && !$inatt && $curstr) {
$patharray[] = $curstr;
$curstr      = '';
}
elseif ($chr == '[') {
$inatt = true;
$curstr .= $chr;
}
elseif ($chr == ']') {
$inatt = false;
$curstr .= $chr;
}
else {
$curstr .= $chr;
}
}
if ($curstr) {
$patharray[] = $curstr;
$curstr      = '';
}
}
foreach ($patharray as $k => $s) {
if ($s == '') continue;
$entries = $xpath->query($s, $el);
if (!$entries) {
trigger_error("Invalid query - " . $s, E_USER_WARNING);
return false;
}
if (
$entries->item(0) == null ||
$createall ||
($createlast && $k == sizeof($patharray) - 1)
) {
if (strpos($s, '[') !== false) {
$tag = trim(substr($s, 0, strpos($s, '[')));
$node = $this->_DOM->createElement($tag);
preg_match_all('/\[([^=,\]]*)=([^\[]*)\]/', $s, $matches);
foreach ($matches[1] as $k => $v) {
$node->setAttribute(trim(trim($v), '@'), trim(trim($matches[2][$k]), '"'));
}
}
else {
$tag = trim($s);
$node = $this->_DOM->createElement($tag);
}
$el->appendChild($node);
$el = $node;
}
else {
$el = $entries->item(0);
}
}
return $el;
}
public function getElements($path) {
return $this->getElementsFrom($path, $this->getRootDOM());
}
public function getElementsFrom($path, $el = false) {
if (!$el) $el = $this->getRootDOM();
$path = $this->_translatePath($path);
$xpath   = new DOMXPath($this->_DOM);
$entries = $xpath->query($path, $el);
return $entries;
}
public function removeElements($path) {
return $this->removeElementsFrom($path, $this->getRootDOM());
}
public function removeElementsFrom($path, $el) {
$path = $this->_translatePath($path);
$xpath   = new DOMXPath($this->_DOM);
$entries = $xpath->query($path, $el);
foreach ($entries as $e) {
$e->parentNode->removeChild($e);
}
return true;
}
public function elementToArray($el, $nesting = true) {
$ret = array();
foreach ($this->getElementsFrom('*', $el, false) as $node) {
$c           = $node->childNodes->item(0);
$haschildren = ($c instanceof DOMElement);
if (isset($ret[$node->tagName])) {
if (!is_array($ret[$node->tagName])) {
$v                   = $ret[$node->tagName];
$ret[$node->tagName] = array($v);
}
if ($haschildren && $nesting) {
$ret[$node->tagName][] = $this->elementToArray($node, true);
}
else {
$ret[$node->tagName][] = ($node->getAttribute('xsi:nil') == 'true') ? null : $node->nodeValue;
}
}
else {
if ($haschildren && $nesting) {
$ret[$node->tagName] = $this->elementToArray($node, true);
}
else {
$ret[$node->tagName] = ($node->getAttribute('xsi:nil') == 'true') ? null : $node->nodeValue;
}
}
}
return $ret;
}
public function asMinifiedXML() {
$string = $this->getDOM()->saveXML();
$string = str_replace(array("\r\n", "\r", "\n"), NL, $string);
$string = preg_replace('/^(\s*)</m', '<', $string);
$string = preg_replace('/^' . NL . '/', '', $string);
$string = preg_replace('/' . NL . '+/', NL, $string);
$string = preg_replace('/>$' . NL . '/m', '>', $string);
$string = preg_replace('/(<\?xml version="1.0" encoding="UTF-8"\?>)/', '$1' . NL, $string);
$string = preg_replace('/(<!DOCTYPE component>)/', '$1' . NL, $string);
return $string;
}
public function asPrettyXML($html_output = false) {
$string = $this->getDOM()->saveXML();
$string = str_replace(array("\r\n", "\r", "\n"), NL, $string);
$string = preg_replace('/^(\s*)</m', '<', $string);
$string = preg_replace('/<([^>]*)>/', NL . '<$1>' . NL, $string);
$string = preg_replace('/^' . NL . '/', '', $string);
$string = preg_replace('/' . NL . '+/', NL, $string);
$lines = explode(NL, $string);
$indent     = 0;
$tab        = "\t";
$out        = '';
$_incomment = false;
$skip       = 0; // Counter used for skipping lines.
foreach ($lines as $k => $line) {
if ($skip > 0) {
$skip--;
continue;
}
if ($_incomment && !preg_match('/-->/', $line)) {
$out .= str_repeat($tab, $indent) . trim($line) . NL;
continue;
}
if (preg_match('/<\?[^\?]*\?>/', $line)) {
$out .= trim($line) . NL;
}
elseif (preg_match('/<!DOCTYPE[^>]*>/', $line)) {
$out .= trim($line) . NL;
}
elseif (preg_match('/<\!--.*-->/', $line)) {
$out .= str_repeat($tab, $indent) . trim($line) . NL;
}
elseif (preg_match('/<\!--/', $line)) {
$_incomment = true;
$out .= str_repeat($tab, $indent) . trim($line) . NL;
$indent++;
}
elseif ($_incomment && preg_match('/-->/', $line)) {
$_incomment = false;
$indent--;
$out .= str_repeat($tab, $indent) . trim($line) . NL;
}
elseif (preg_match('/<[^>]*(?<=\/)>/', $line)) {
$out .= str_repeat($tab, $indent) . trim($line) . NL;
}
elseif (preg_match('/<\/[^>]*>/', $line)) {
$indent--;
$out .= str_repeat($tab, $indent) . trim($line) . NL;
}
elseif (preg_match('/<[^>]*(?<!\/)>/', $line)) {
if (isset($lines[$k + 1]) && preg_match('/<\/[^>]*>/', $lines[$k + 1])) {
$out .= str_repeat($tab, $indent) . trim($line) . trim($lines[$k + 1]) . NL;
$skip = 1;
}
elseif (isset($lines[$k + 2]) && strpos($lines[$k + 1], '<') === false && strlen(trim($lines[$k + 1])) <= 31 && preg_match('/<\/[^>]*>/', $lines[$k + 2])) {
$out .= str_repeat($tab, $indent) . trim($line) . trim($lines[$k + 1]) . trim($lines[$k + 2]) . NL;
$skip = 2;
}
else {
$out .= str_repeat($tab, $indent) . trim($line) . NL;
$indent++;
}
}
else {
$out .= str_repeat($tab, $indent) . trim($line) . NL;
}
}
return $out;
$xml_obj = simplexml_import_dom($this->getDOM());
$xml_lines    = explode("\n", $xml_obj->asXML());
$indent_level = 0;
$tab          = "\t"; // Optionally, have this be "    " for a space'd version.
$new_xml_lines = array();
foreach ($xml_lines as $xml_line) {
if (preg_match('#^(<[a-z0-9_:-]+((s+[a-z0-9_:-]+="[^"]+")*)?>.*<s*/s*[^>]+>)|(<[a-z0-9_:-]+((s+[a-z0-9_:-]+="[^"]+")*)?s*/s*>)#i', ltrim($xml_line))) {
$new_line        = str_repeat($tab, $indent_level) . ltrim($xml_line);
$new_xml_lines[] = $new_line;
} elseif (preg_match('#^<[a-z0-9_:-]+((s+[a-z0-9_:-]+="[^"]+")*)?>#i', ltrim($xml_line))) {
$new_line = str_repeat($tab, $indent_level) . ltrim($xml_line);
$indent_level++;
$new_xml_lines[] = $new_line;
} elseif (preg_match('#<s*/s*[^>/]+>#i', $xml_line)) {
$indent_level--;
if (trim($new_xml_lines[sizeof($new_xml_lines) - 1]) == trim(str_replace("/", "", $xml_line))) {
$new_xml_lines[sizeof($new_xml_lines) - 1] .= $xml_line;
} else {
if ($indent_level < 0) $indent_level = 0;
$new_line        = str_repeat($tab, $indent_level) . $xml_line;
$new_xml_lines[] = $new_line;
}
} else {
$new_line        = str_repeat($tab, $indent_level) . $xml_line;
$new_xml_lines[] = $new_line;
}
}
$xml = join("\n", $new_xml_lines);
return ($html_output) ? '<pre>' . htmlentities($xml) . '</pre>' : $xml;
}
}


### REQUIRE_ONCE FROM core/libs/core/InstallArchive.class.php
class InstallArchive {
const SIGNATURE_NONE    = 0;
const SIGNATURE_VALID   = 1;
const SIGNATURE_INVALID = 2;
private $_file;
private $_manifestdata;
private $_signature;
private $_fileconflicts;
private $_filelist;
public function __construct($file) {
if ($file instanceof File) {
$this->_file = $file;
}
else {
$this->_file = \Core\Filestore\Factory::File($file);
}
}
public function hasValidSignature() {
$sig = $this->checkSignature();
return ($sig['state'] == InstallArchive::SIGNATURE_VALID);
}
public function checkSignature() {
if (is_null($this->_signature)) {
switch ($this->_file->getMimetype()) {
case 'application/pgp':
$this->_signature = $this->_checkGPGSignature();
break;
default:
$this->_signature = array('state' => InstallArchive::SIGNATURE_NONE,
'key'   => null,
'email' => null,
'name'  => null);
break;
}
}
return $this->_signature;
}
private function _checkGPGSignature() {
$crypt_gpg = GPG::Singleton();
try {
list($out) = $crypt_gpg->verifyFile($this->_file->getFilename());
}
catch (Exception $e) {
return array('state' => InstallArchive::SIGNATURE_INVALID,
'key'   => null,
'email' => null,
'name'  => null);
}
if (!$out->isValid()) {
return array('state' => InstallArchive::SIGNATURE_INVALID,
'key'   => null,
'email' => null,
'name'  => null);
}
else {
return array(
'state' => InstallArchive::SIGNATURE_VALID,
'key'   => $out->getKeyFingerprint(),
'email' => $out->getUserId()->getEmail(),
'name'  => $out->getUserId()->getName()
);
}
}
public function getManifest() {
if (is_null($this->_manifestdata)) {
switch ($this->_file->getMimetype()) {
case 'application/pgp':
if (!$this->hasValidSignature()) {
$this->_manifestdata = $this->_getManifest(null);
}
else {
$tmpfile = '/tmp/outtarball-manifest.tgz';
$this->_decryptTo($tmpfile);
$this->_manifestdata = $this->_getManifest($tmpfile);
unlink($tmpfile);
}
break;
default:
$this->_manifestdata = $this->_getManifest($this->_file->getFilename());
break;
}
}
return $this->_manifestdata;
}
private function _getManifest($filename) {
if ($filename === null) {
$fn = $this->_file->getFilename();
if (strpos($fn, '-')) {
list($name, $version) = explode('-', substr($fn, strrpos($fn, '/') + 1, strrpos($fn, '.')));
}
else {
$name    = substr($fn, strrpos($fn, '.'));
$version = null;
}
return array(
'Manifest-Version' => '1.0',
'manifestversion'  => '1.0',
'Bundle-Type'      => 'unknown',
'bundletype'       => 'unknown',
'Bundle-Name'      => $name,
'bundlename'       => $name,
'Bundle-Version'   => $version,
'bundleversion'    => $version
);
}
exec('tar -xzvf "' . $filename . '" ./META-INF/MANIFEST.MF -O', $output);
$ret = array();
foreach ($output as $line) {
if (strpos($line, ':') === false) continue;
list($k, $v) = explode(':', $line);
$ret[trim($k)]                                   = trim($v);
$ret[trim(strtolower(str_replace('-', '', $k)))] = trim($v);
}
return $ret;
}
private function _decryptTo($filename) {
$crypt_gpg = GPG::Singleton();
$crypt_gpg->decryptFile($this->_file->getFilename(), $filename);
}
public function getFilelist() {
if (is_null($this->_filelist)) {
switch ($this->_file->getMimetype()) {
case 'application/pgp':
$tmpfile = '/tmp/outtarball-filelist.tgz';
$this->_decryptTo($tmpfile);
$this->_filelist = $this->_getFilelist($tmpfile);
unlink($tmpfile);
break;
default:
$this->_filelist = $this->_getFilelist($this->_file->getFilename());
break;
}
}
return $this->_filelist;
}
private function _getFilelist($filename) {
$man  = $this->getManifest();
$type = $man['Bundle-Type'];
exec('tar -tzf "' . $filename . '"', $output);
$ret = array();
foreach ($output as $line) {
if ($line == './') continue;
if (!preg_match(':\./data:', $line)) continue;
if (preg_match(':/$:', $line)) continue;
if ($line == './data/component.xml' && $type == 'component') continue;
$file = str_replace('./data/', '', $line);
$ret[] = $file;
}
return $ret;
}
public function extractFile($filename, $to = false) {
if (!$to) $to = $this->getBaseDir();
$fp  = $to . $filename;
$fb  = './data/' . $filename;
$dir = dirname($fp);
if (!is_dir($dir)) {
exec('mkdir -p "' . $dir . '"');
exec('chmod a+w "' . $dir . '"');
}
if (!is_writable($dir)) {
throw new Exception('Cannot write to directory ' . $dir);
}
if (!is_writable($fp)) {
throw new Exception('Cannot write to file ' . $fp);
}
switch ($this->_file->getMimetype()) {
case 'application/pgp':
$file   = '/tmp/outtarball-extractfile.tgz';
$istemp = true;
$this->_decryptTo($file);
break;
default:
$file   = $this->_file->getFilename();
$istemp = false;
break;
}
exec('tar -xzvf "' . $file . '" "' . $fb . '" -O > "' . $fp . '"');
if ($istemp) {
unlink($file);
}
}
public function getFileConflicts() {
if (is_null($this->_fileconflicts)) {
$man = $this->getManifest();
$files = $this->getFileList();
switch ($man['Bundle-Type']) {
case 'component':
return $this->_getFileConflictsComponent($files);
break;
}
}
return $this->_fileconflicts;
}
public function getBaseDir() {
$man = $this->getManifest();
switch ($man['Bundle-Type']) {
case 'component':
return ROOT_PDIR . 'components/' . $man['Bundle-Name'] . '/';
}
}
private function _getFileConflictsComponent($arrayoffiles) {
$man       = $this->getManifest();
$basedir   = $this->getBaseDir();
$component = ComponentHandler::GetComponent($man['Bundle-Name']);
$changedfiles = $component->getChangedFiles();
$ret = array();
foreach ($arrayoffiles as $line) {
if (in_array($line, $changedfiles)) $ret[] = $line;
}
return $ret;
}
}


### REQUIRE_ONCE FROM core/libs/core/InstallArchiveAPI.class.php
abstract class InstallArchiveAPI extends XMLLoader {
const TYPE_COMPONENT = 'component';
const TYPE_LIBRARY   = 'library';
const TYPE_THEME     = 'theme';
protected $_name;
protected $_version;
protected $_description;
protected $_updateSites = array();
protected $_authors = array();
protected $_iterator;
protected $_type;
public function load() {
$XMLFilename = $this->getXMLFilename();
if (!is_readable($XMLFilename)) {
throw new Exception('Unable to open XML Metafile [' . $XMLFilename . '] for reading.');
}
$this->setFilename($XMLFilename);
$this->setRootName($this->_type);
if (!parent::load()) {
throw new Exception('Parsing of XML Metafile [' . $XMLFilename . '] failed, not valid XML.');
}
if (strtolower($this->getRootDOM()->getAttribute("name")) != strtolower($this->_name)) {
throw new Exception('Name mismatch in XML Metafile [' . $XMLFilename . '], defined name does not match expected name.');
}
$this->_version = $this->getRootDOM()->getAttribute("version");
}
public function getRequires() {
$ret = array();
foreach ($this->getRootDOM()->getElementsByTagName('requires') as $r) {
$t  = $r->getAttribute('type');
$n  = $r->getAttribute('name');
$v  = @$r->getAttribute('version');
$op = @$r->getAttribute('operation');
if ($v == '') $v = false;
if ($op == '') $op = 'ge';
$ret[] = array(
'type'      => strtolower($t),
'name'      => $n,
'version'   => strtolower($v),
'operation' => strtolower($op),
);
}
return $ret;
}
public function getDescription() {
if (is_null($this->_description)) $this->_description = $this->getElement('//description')->nodeValue;
return $this->_description;
}
public function setDescription($desc) {
$this->_description = $desc;
$this->getElement('//description')->nodeValue = $desc;
}
public function setPackageMaintainer($name, $email) {
$this->getElement('/changelog[@version="' . $this->_version . '"]/packagemeta/date')->nodeValue = Time::GetCurrent(Time::TIMEZONE_GMT, 'r');
$this->getElement('/changelog[@version="' . $this->_version . '"]/packagemeta/maintainer[@name="' . $name . '"][@email="' . $email . '"]');
$this->getElement('/changelog[@version="' . $this->_version . '"]/packagemeta/packager')->nodeValue = 'CAE2 ' . ComponentHandler::GetComponent('core')->getVersion();
}
public function getChangelog($version = false) {
if (!$version) $version = $this->getVersion();
return $this->getElement('/changelog[@version="' . $version . '"]/notes')->nodeValue;
}
public function setChangelog($text, $version = false) {
if (!$version) $version = $this->getVersion();
$this->getElement('/changelog[@version="' . $version . '"]/notes')->nodeValue = $text;
}
public function getXMLFilename($prefix = ROOT_PDIR) {
switch ($this->_type) {
case InstallArchiveAPI::TYPE_COMPONENT:
if ($this->_name == 'core') return $prefix . 'core/' . 'component.xml';
else return $prefix . 'components/' . $this->_name . '/' . 'component.xml';
break;
case InstallArchiveAPI::TYPE_LIBRARY:
return $prefix . 'libraries/' . $this->_name . '/' . 'library.xml';
break;
case InstallArchiveAPI::TYPE_THEME:
return $prefix . 'themes/' . $this->_name . '/' . 'theme.xml';
break;
}
}
public function getBaseDir($prefix = ROOT_PDIR) {
switch ($this->_type) {
case InstallArchiveAPI::TYPE_COMPONENT:
if ($this->_name == 'core') return $prefix;
else return $prefix . 'components/' . $this->_name . '/';
break;
case InstallArchiveAPI::TYPE_LIBRARY:
return $prefix . 'libraries/' . $this->_name . '/';
break;
case InstallArchiveAPI::TYPE_THEME:
return $prefix . 'themes/' . $this->_name . '/';
break;
}
}
public function getChangedFiles() {
$ret = array();
foreach ($this->getElementsByTagName('file') as $node) {
if (!($filename = @$node->getAttribute('filename'))) continue;
if ($node->getAttribute('md5') != md5_file($this->getBaseDir() . $filename)) {
$ret[] = $filename;
}
}
return $ret;
}
public function getName() {
return $this->_name;
}
public function getVersion() {
return $this->_version;
}
public function setVersion($vers) {
if ($vers == $this->_version) return;
if (($upg = $this->getElement('/upgrade[@from=""][@to=""]', false))) {
$upg->setAttribute('from', $this->_version);
$upg->setAttribute('to', $vers);
}
elseif (($upg = $this->getElement('/upgrade[@from="' . $this->_version . '"][@to=""]', false))) {
$upg->setAttribute('to', $vers);
}
else {
$newupgrade = $this->getElement('/upgrade[@from="' . $this->_version . '"][@to="' . $vers . '"]');
}
$newchangelog = $this->getElement('/changelog[@version="' . $vers . '"]');
foreach ($this->getElementsByTagName('changelog') as $el) {
if (!@$el->getAttribute('version')) {
$newchangelog->nodeValue .= "\n" . $el->nodeValue;
$el->nodeValue = '';
break;
}
}
$this->_version = $vers;
$this->getRootDOM()->setAttribute('version', $vers);
}
public function getRawXML() {
return $this->asPrettyXML();
}
public function getLicenses() {
$ret = array();
foreach ($this->getRootDOM()->getElementsByTagName('license') as $el) {
$url   = @$el->getAttribute('url');
$ret[] = array(
'title' => $el->nodeValue,
'url'   => $url
);
}
return $ret;
}
public function setLicenses($licenses) {
$this->removeElements('/license');
foreach ($licenses as $lic) {
$str          = '/license' . ((isset($lic['url']) && $lic['url']) ? '[@url="' . $lic['url'] . '"]' : '');
$l            = $this->getElement($str);
$l->nodeValue = $lic['title'];
}
}
public function getAuthors() {
$ret = array();
foreach ($this->getRootDOM()->getElementsByTagName('author') as $el) {
$ret[] = array(
'name'  => $el->getAttribute('name'),
'email' => @$el->getAttribute('email'),
);
}
return $ret;
}
public function setAuthors($authors) {
$this->removeElements('/author');
foreach ($authors as $a) {
if (isset($a['email']) && $a['email']) {
$this->getElement('//component/author[@name="' . $a['name'] . '"][@email="' . $a['email'] . '"]');
}
else {
$this->getElement('//component/author[@name="' . $a['name'] . '"]');
}
}
}
public function getAllFilenames() {
$ret  = array();
$list = $this->getElements('//component/library/file|//component/module/file|//component/view/file|//component/otherfiles/file|//component/assets/file');
foreach ($list as $el) {
$md5   = @$el->getAttribute('md5');
$ret[] = array(
'file' => $el->getAttribute('filename'),
'md5'  => $md5
);
}
return $ret;
}
public function getDirectoryIterator() {
if (is_null($this->_iterator)) {
$this->_iterator = new CAEDirectoryIterator();
$this->_iterator->addIgnore($this->getXMLFilename());
if ($this->_name == 'core') {
$this->_iterator->addIgnores('components/', 'config/', 'dropins/', 'exports/', 'nbproject/', 'scripts/', 'themes/', 'update_site/', 'utils/');
if (ConfigHandler::Get('/core/filestore/assetdir')) $this->_iterator->addIgnore(ConfigHandler::Get('/core/filestore/assetdir'));
if (ConfigHandler::Get('/core/filestore/publicdir')) $this->_iterator->addIgnore(ConfigHandler::Get('/core/filestore/publicdir'));
}
$list = $this->getElements('/ignorefiles/file');
foreach ($list as $el) {
$this->_iterator->addIgnores($this->getBaseDir() . $el->getAttribute('filename'));
}
$this->_iterator->setPath($this->getBaseDir());
$this->_iterator->scan();
}
return clone $this->_iterator;
}
}


### REQUIRE_ONCE FROM core/libs/core/Exceptions.php
class ModelException extends Exception {
}
class ModelValidationException extends ModelException {
}
class GeneralValidationException extends Exception {
}
class CoreException extends Exception {
}


### REQUIRE_ONCE FROM core/libs/core/datamodel/DMI.class.php
define('__DMI_PDIR', ROOT_PDIR . 'core/libs/core/datamodel/');
### REQUIRE_ONCE FROM core/libs/core/datamodel/BackendInterface.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Datamodel {
interface BackendInterface {
public function execute(Dataset $dataset);
public function tableExists($tablename);
public function createTable($table, Schema $schema);
public function modifyTable($table, Schema $schema);
public function dropTable($table);
public function describeTable($table);
public function showTables();
}
} // ENDING NAMESPACE Core\Datamodel

namespace  {

### REQUIRE_ONCE FROM core/libs/core/datamodel/Dataset.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Datamodel {
class Dataset implements \Iterator{
const MODE_ALTER = 'alter';
const MODE_GET = 'get';
const MODE_INSERT = 'insert';
const MODE_BULK_INSERT = 'bulk_insert';
const MODE_UPDATE = 'update';
const MODE_INSERTUPDATE = 'insertupdate';
const MODE_DELETE = 'delete';
const MODE_COUNT = 'count';
public $_table;
public $_selects = array();
public $_where = null;
public $_mode = Dataset::MODE_GET;
public $_sets = array();
public $_idcol = null;
public $_idval = null;
public $_limit = false;
public $_order = false;
public $_data = null;
public $num_rows = null;
public $_renames = array();
public $uniquerecords = false;
public function __construct(){
}
public function select(){
$n = func_num_args();
if($n == 0) throw new \DMI_Exception ('Invalid amount of parameters requested for Dataset::set()');
if($n == 1 && func_get_arg(0) === null){
$this->_selects = array();
return $this;
}
$this->_mode = Dataset::MODE_GET;
$args = func_get_args();
foreach($args as $a){
if(is_array($a)){
$this->_selects = array_merge($this->_selects, $a);
}
elseif(strpos($a, ',') !== false){
$parts = explode(',', $a);
foreach($parts as $p){
$this->_selects[] = trim($p);
}
}
else{
$this->_selects[] = $a;
}
}
$this->_selects = array_unique($this->_selects);
return $this;
}
public function insert(){
call_user_func_array(array($this, '_set'), func_get_args());
$this->_mode = Dataset::MODE_INSERT;
return $this;
}
public function update(){
call_user_func_array(array($this, '_set'), func_get_args());
$this->_mode = Dataset::MODE_UPDATE;
return $this;
}
public function set(){
call_user_func_array(array($this, '_set'), func_get_args());
$this->_mode = Dataset::MODE_INSERTUPDATE;
return $this;
}
public function renameColumn(){
call_user_func_array(array($this, '_renameColumn'), func_get_args());
$this->_mode = Dataset::MODE_ALTER;
return $this;
}
public function delete(){
$this->_mode = Dataset::MODE_DELETE;
return $this;
}
public function count(){
$this->_mode = Dataset::MODE_COUNT;
return $this;
}
private function _set(){
$n = func_num_args();
if($n == 0 || $n > 2){
throw new \DMI_Exception ('Invalid amount of parameters requested for Dataset::set(), ' . $n . ' provided, exactly 1 or 2 expected');
}
elseif($n == 1){
$a = func_get_arg(0);
if(!is_array($a)) throw new \DMI_Exception ('Invalid parameter sent for Dataset::set()');
foreach($a as $k => $v){
$this->_sets[$k] = $v;
}
}
else{
$k = func_get_arg(0);
$v = func_get_arg(1);
$this->_sets[$k] = $v;
}
}
private function _renameColumn(){
$n = func_num_args();
if($n != 2){
throw new \DMI_Exception ('Invalid amount of parameters requested for Dataset::renameColumn(), ' . $n . ' provided, exactly 2 expected');
}
$oldname = func_get_arg(0);
$newname = func_get_arg(1);
$this->_renames[$oldname] = $newname;
}
public function setID($key, $val = null){
$this->_idcol = $key;
$this->_idval = $val;
if($val) $this->where("$key = $val");
}
public function getID(){
return $this->_idval;
}
public function table($tablename){
if(DB_PREFIX && strpos($tablename, DB_PREFIX) === false){
$tablename = DB_PREFIX . $tablename;
}
$this->_table = $tablename;
return $this;
}
public function unique($unique = true){
$this->uniquerecords = $unique;
return $this;
}
public function getWhereClause(){
if($this->_where === null){
$this->_where = new DatasetWhereClause('root');
}
return $this->_where;
}
public function where(){
$args = func_get_args();
if(sizeof($args) == 2 && is_string($args[0]) && is_string($args[1])){
$this->getWhereClause()->addWhere($args[0] . ' = ' . $args[1]);
}
else{
$this->getWhereClause()->addWhere($args);
}
return $this;
}
public function whereGroup($separator, $wheres){
$args = func_get_args();
$sep = array_shift($args);
$clause = new DatasetWhereClause();
$clause->setSeparator($sep);
$clause->addWhere($args);
$this->getWhereClause()->addWhere($clause);
return $this;
}
public function limit(){
$n = func_num_args();
if($n == 1) $this->_limit = func_get_arg(0);
elseif($n == 2) $this->_limit = func_get_arg(0) . ', ' . func_get_arg(1);
else throw new \DMI_Exception('Invalid amount of parameters requested for Dataset::limit()');
return $this;
}
public function order(){
$n = func_num_args();
if($n == 1) $this->_order = func_get_arg(0);
elseif($n == 2) $this->_order = func_get_arg(0) . ', ' . func_get_arg(1);
else throw new \DMI_Exception('Invalid amount of parameters requested for Dataset::order()');
return $this;
}
public function execute($interface = null){
if(!$interface){
$dmi = \DMI::GetSystemDMI();
$interface = $dmi->connection();
}
$interface->execute($this);
if( $this->_data === null && $this->_mode == Dataset::MODE_GET ){
$this->_data = [];
reset($this->_data);
}
return $this;
}
public function executeAndGet($interface = null){
$this->execute($interface);
if($this->_mode == Dataset::MODE_COUNT){
return $this->num_rows;
}
elseif($this->_limit == 1 && $this->num_rows == 1){
if(sizeof($this->_selects) == 1 && $this->_selects[0] != '*'){
$k = $this->_selects[0];
return (isset($this->_data[0][$k])) ? $this->_data[0][$k] : null;
}
else{
return $this->_data[0];
}
}
elseif($this->_limit == 1 && $this->num_rows == 0){
if(sizeof($this->_selects) == 1 && $this->_selects[0] != '*'){
return null;
}
else{
return [];
}
}
elseif(sizeof($this->_selects) == 1 && $this->_selects[0] != '*'){
$ret = [];
$k = $this->_selects[0];
foreach($this as $d){
$ret[] = isset($d[$k]) ? $d[$k] : null;
}
return $ret;
}
else{
$ret = [];
foreach($this as $d){
$ret[] = $d;
}
return $ret;
}
}
function rewind() {
if($this->_data !== null) reset($this->_data);
}
function current() {
if($this->_data === null) $this->execute();
$k = key($this->_data);
return isset($this->_data[$k]) ? $this->_data[$k] : null;
}
function key() {
if($this->_data === null) $this->execute();
return key($this->_data);
}
function next() {
if($this->_data === null) $this->execute();
next($this->_data);
}
function valid() {
if($this->_data === null) $this->execute();
return isset($this->_data[key($this->_data)]);
}
public static function Init(){
return new self();
}
}
} // ENDING NAMESPACE Core\Datamodel

namespace  {

### REQUIRE_ONCE FROM core/libs/core/datamodel/DatasetWhere.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Datamodel {
class DatasetWhere{
public $field;
public $op;
public $value;
public function __construct($arguments = null){
if($arguments) $this->_parseWhere($arguments);
}
private function _parseWhere($statement){
$valid = false;
$operations = array('!=', '<=', '>=', '=', '>', '<', 'LIKE ', 'NOT LIKE', 'IN');
$k = preg_replace('/^([^ !=<>]*).*/', '$1', $statement);
$statement = trim(substr($statement, strlen($k)));
foreach($operations as $c){
if(($pos = strpos($statement, $c)) === 0){
$op = $c;
$statement = trim(substr($statement, strlen($op)));
$valid = true;
if($op == 'IN'){
$statement = ltrim($statement, " \t\n\r\0\x0B(");
$statement = rtrim($statement, " \t\n\r\0\x0B)");
$statement = array_map('trim', explode(',', $statement));
}
elseif($statement == 'NULL'){
$statement = null;
}
break;
}
}
if($valid){
$this->field = $k;
$this->op = $op;
$this->value = $statement;
}
}
}
} // ENDING NAMESPACE Core\Datamodel

namespace  {

### REQUIRE_ONCE FROM core/libs/core/datamodel/DatasetWhereClause.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Datamodel {
class DatasetWhereClause{
private $_separator = 'AND';
private $_statements = array();
private $_name;
public function __construct($name = '_unnamed_'){
$this->_name = $name;
}
public function addWhereParts($field, $operation, $value){
$c = new DatasetWhere();
$c->field = $field;
$c->op = $operation;
$c->value = $value;
$this->_statements[] = $c;
}
public function addWhere($arguments){
if($arguments instanceof DatasetWhereClause){
$this->_statements[] = $arguments;
return true;
}
if($arguments instanceof DatasetWhere){
$this->_statements[] = $arguments;
return true;
}
if(is_string($arguments)){
$this->_statements[] = new DatasetWhere($arguments);
return true;
}
foreach($arguments as $a){
if(is_array($a)){
foreach($a as $k => $v){
if(is_numeric($k)){
$this->_statements[] = new DatasetWhere($v);
}
else{
$dsw = new DatasetWhere();
$dsw->field = $k;
$dsw->op    = '=';
$dsw->value = $v;
$this->_statements[] = $dsw;
}
}
}
elseif($a instanceof DatasetWhereClause){
$this->_statements[] = $a;
}
elseif($a instanceof DatasetWhere){
$this->_statements[] = $a;
}
else{
$this->_statements[] = new DatasetWhere($a);
}
}
}
public function addWhereSub($sep, $arguments){
$subgroup = new DatasetWhereClause();
$subgroup->setSeparator($sep);
$subgroup->addWhere($arguments);
$this->addWhere($subgroup);
}
public function getStatements(){
return $this->_statements;
}
public function setSeparator($sep){
$sep = trim(strtoupper($sep));
switch($sep){
case 'AND':
case 'OR':
$this->_separator = $sep;
break;
default:
throw new DMI_Exception('Invalid separator, [' . $sep . ']');
}
}
public function getSeparator(){
return $this->_separator;
}
public function getAsArray(){
$children = array();
foreach($this->_statements as $s){
if($s instanceof DatasetWhereClause){
$children[] = $s->getAsArray();
}
elseif($s instanceof DatasetWhere){
if($s->field === null) continue;
$children[] = $s->field . ' ' . $s->op . ' ' . $s->value;
}
}
return array('sep' => $this->_separator, 'children' => $children);
}
public function findByField($fieldname){
$matches = array();
foreach($this->_statements as $s){
if($s instanceof DatasetWhereClause){
$matches = array_merge($matches, $s->findByField($fieldname));
}
elseif($s instanceof DatasetWhere){
if($s->field == $fieldname) $matches[] = $s;
}
}
return $matches;
}
}
} // ENDING NAMESPACE Core\Datamodel

namespace  {

### REQUIRE_ONCE FROM core/libs/core/datamodel/Schema.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Datamodel {
class Schema {
public $definitions = array();
public $order = array();
public $indexes = array();
public function getColumn($column){
if(is_int($column)){
if(isset($this->order[$column])) $column = $this->order[$column];
else return null;
}
if(isset($this->definitions[$column])) return $this->definitions[$column];
else return null;
}
public function getDiff(Schema $schema){
$diffs = array();
foreach($schema->definitions as $name => $dat){
$thiscol = $this->getColumn($name);
if(!$thiscol){
if($dat->type != \Model::ATT_TYPE_ALIAS){
$diffs[] = array(
'title' => 'A does not have column ' . $name,
'type' => 'column',
);
}
continue;
}
if($dat->type == \Model::ATT_TYPE_ALIAS){
continue;
}
if(($colchange = $thiscol->getDiff($dat)) !== null){
$diffs[] = array(
'title' => 'Column ' . $name . ' does not match up: ' . $colchange,
'type' => 'column',
);
}
}
$a_order = $this->order;
foreach($this->definitions as $name => $dat){
if(!$schema->getColumn($name)){
unset($a_order[array_search($name, $a_order)]);
}
elseif($schema->getColumn($name)->type == \Model::ATT_TYPE_ALIAS){
unset($a_order[array_search($name, $a_order)]);
}
}
if(implode(',', $a_order) != implode(',', $schema->order)){
$diffs[] = array(
'title' => 'Order of columns is different',
'type' => 'order',
);
}
$thisidx = '';
foreach($this->indexes as $name => $cols) $thisidx .= ';' . $name . '-' . implode(',', $cols);
$thatidx = '';
foreach($schema->indexes as $name => $cols) $thatidx .= ';' . $name . '-' . implode(',', $cols);
if($thisidx != $thatidx){
$diffs[] = array(
'title' => 'Indexes do not match up',
'type' => 'index'
);
}
return $diffs;
}
public function isDataIdentical(Schema $schema){
$diff = $this->getDiff($schema);
return !sizeof($diff);
}
}
class SchemaColumn {
public $field;
public $type = null;
public $required = false;
public $maxlength = false;
public $options = null;
public $default = false;
public $null = false;
public $comment = '';
public $precision = null;
public $encrypted = false;
public $autoinc = false;
public $encoding = null;
public $aliasof = null;
public function isIdenticalTo(SchemaColumn $col){
$diff = $this->getDiff($col);
return ($diff === null);
}
public function getDiff(SchemaColumn $col){
$thisarray = (array)$this;
$colarray  = (array)$col;
if($thisarray === $colarray) return null;
$differences = [];
if($this->field != $col->field) $differences[] = 'field name';
if($this->maxlength != $col->maxlength) $differences[] = 'max length';
if($this->null != $col->null) $differences[] = 'is null';
if($this->comment != $col->comment) $differences[] = 'comment';
if($this->precision != $col->precision) $differences[] = 'precision';
if($this->autoinc !== $col->autoinc) $differences[] = 'auto increment';
if($this->encoding != $col->encoding) $differences[] = 'encoding';
if($this->default === false){
}
elseif($this->default === $col->default){
}
elseif(\Core\compare_values($this->default, $col->default)){
}
elseif($col->default === false && $this->default !== false){
$differences[] = 'default value (#1)';
}
else{
$differences[] = 'default value (#2)';
}
if(is_array($this->options) != is_array($col->options)) $differences[] = 'options set/unset';
if(is_array($this->options) && is_array($col->options)){
if(implode(',', $this->options) != implode(',', $col->options)) $differences[] = 'options changed';
}
$typematches = array(
array(
\Model::ATT_TYPE_INT,
\Model::ATT_TYPE_UUID,
\Model::ATT_TYPE_UUID_FK,
\Model::ATT_TYPE_CREATED,
\Model::ATT_TYPE_UPDATED,
\Model::ATT_TYPE_DELETED,
\Model::ATT_TYPE_SITE,
)
);
$typesidentical = false;
foreach($typematches as $types){
if(in_array($this->type, $types) && in_array($col->type, $types)){
$typesidentical = true;
break;
}
}
if(!$typesidentical && $this->type != $col->type) $differences[] = 'type';
if(sizeof($differences)){
return implode(', ', $differences);
}
else{
return null;
}
}
}
} // ENDING NAMESPACE Core\Datamodel

namespace  {

### REQUIRE_ONCE FROM core/libs/core/datamodel/DatasetStream.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Datamodel {
class DatasetStream{
private $_dataset;
private $_totalcount;
private $_counter = -1;
private $_startlimit = 0;
public $bufferlimit = 100;
public function __construct(Dataset $ds){
$this->_dataset = $ds;
$mode = $this->_dataset->_mode;
$this->_totalcount = $this->_dataset->count()->execute()->num_rows;
$this->_dataset->_mode = $mode;
}
public function getRecord(){
++$this->_counter;
if($this->_dataset->_data === null || $this->_counter >= $this->bufferlimit){
$this->_dataset->limit($this->_startlimit, $this->bufferlimit);
$this->_dataset->execute();
$this->_startlimit += $this->bufferlimit;
$this->_counter = 0;
}
return isset($this->_dataset->_data[$this->_counter]) ? $this->_dataset->_data[$this->_counter] : null;
}
}
} // ENDING NAMESPACE Core\Datamodel

namespace  {

class DMI {
protected $_backend = null;
static protected $_Interface = null;
public function __construct($backend = null, $host = null, $user = null, $pass = null, $database = null){
if($backend) $this->setBackend($backend);
if($host) $this->connect($host, $user, $pass, $database);
}
public function setBackend($backend){
if($this->_backend) throw new DMI_Exception('Backend already set');
$backend     = strtolower($backend);
$class       = 'Core\\Datamodel\\Drivers\\' . $backend . '\\' . $backend . '_backend';
$backendfile = $backend . '.backend.php';
$schemafile  = $backend . '.schema.php';
if(!file_exists(__DMI_PDIR . 'drivers/' . $backend . '/' . $backendfile)){
throw new DMI_Exception('Could not locate backend file for ' . $class);
}
require_once(__DMI_PDIR . 'drivers/' . $backend . '/' . $backendfile);
if(file_exists(__DMI_PDIR . 'drivers/' . $backend . '/' . $schemafile)){
require_once(__DMI_PDIR . 'drivers/' . $backend . '/' . $schemafile);
}
$this->_backend = new $class();
}
public function connect($host, $user, $pass, $database){
$this->_backend->connect($host, $user, $pass, $database);
return $this->_backend;
}
public function connection(){
return $this->_backend;
}
public static function GetSystemDMI(){
if(self::$_Interface !== null) return self::$_Interface;
self::$_Interface = new DMI();
if(file_exists(ROOT_PDIR . 'config/configuration.xml')){
$cs = ConfigHandler::LoadConfigFile("configuration");
}
elseif(\Core\Session::Get('configs/*') !== null){
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
public $query = null;
}


### REQUIRE_ONCE FROM core/libs/core/Model.class.php
class Model implements ArrayAccess {
const ATT_TYPE_STRING = 'string';
const ATT_TYPE_TEXT = 'text';
const ATT_TYPE_DATA = 'data';
const ATT_TYPE_INT = 'int';
const ATT_TYPE_FLOAT = 'float';
const ATT_TYPE_BOOL = 'boolean';
const ATT_TYPE_ENUM = 'enum';
const ATT_TYPE_UUID = '__uuid';
const ATT_TYPE_UUID_FK = '__uuid_fk';
const ATT_TYPE_ID = '__id';
const ATT_TYPE_ID_FK = '__id_fk';
const ATT_TYPE_UPDATED = '__updated';
const ATT_TYPE_CREATED = '__created';
const ATT_TYPE_DELETED = '__deleted';
const ATT_TYPE_SITE = '__site';
const ATT_TYPE_ALIAS = '__alias';
const ATT_TYPE_ISO_8601_DATETIME = 'ISO_8601_datetime';
const ATT_TYPE_MYSQL_TIMESTAMP = 'mysql_timestamp';
const ATT_TYPE_ISO_8601_DATE = 'ISO_8601_date';
const VALIDATION_NOTBLANK = "/^.+$/";
const VALIDATION_EMAIL = 'Core::CheckEmailValidity';
const VALIDATION_URL = '#^[a-zA-Z]+://.+$#';
const VALIDATION_URL_WEB = '#^[hH][tT][tT][pP][sS]{0,1}://.+$#';
const VALIDATION_INT_GT0 = 'Core::CheckIntGT0Validity';
const VALIDATION_NUMBER_WHOLE = "/^[0-9]*$/";
const VALIDATION_CURRENCY_USD = '#^(\$)?[,0-9]*(?:\.[0-9]{2})?$#';
const LINK_HASONE  = 'one';
const LINK_HASMANY = 'many';
const LINK_BELONGSTOONE = 'belongs_one';
const LINK_BELONGSTOMANY = 'belongs_many';
public $interface = null;
protected $_data = [];
protected $_datainit = [];
protected $_datadecrypted = null;
protected $_dataother = [];
protected $_exists = false;
protected $_linked = [];
protected $_linkIndexCache = [];
protected $_cacheable = true;
public static $Schema = [];
public static $Indexes = [];
public static $HasSearch = false;
public static $HasCreated = false;
public static $HasUpdated = false;
public static $HasDeleted = false;
public static $_ModelCache = [];
public static $_ModelFindCache = [];
protected static $_ModelSchemaCache = [];
public function __construct($key = null) {
if(sizeof($this->_linked)){
$clone = $this->_linked;
$this->_linked = [];
foreach($clone as $model => $dat){
if(strrpos($model, 'Model') !== strlen($model) - 5){
$model .= 'Model';
}
$dat['model'] = $model;
$this->_linked[] = $dat;
}
}
$s = self::GetSchema();
foreach ($s as $k => $v) {
if($v['type'] == Model::ATT_TYPE_ALIAS) continue;
$this->_data[$k] = (isset($v['default'])) ? $v['default'] : null;
if(isset($v['link'])){
if(is_array($v['link'])){
if(!isset($v['link']['model'])){
throw new Exception('Required attribute [model] not provided on link [' . $k . '] of model [' . get_class($this) . ']');
}
$linkmodel = $v['link']['model'];
$linktype  = isset($v['link']['type']) ? $v['link']['type'] : Model::LINK_HASONE;
$linkon    = isset($v['link']['on']) ? $v['link']['on'] : 'id';
}
else{
$linkmodel = $v['link'];
$linktype  = Model::LINK_HASONE;
$linkon    = 'id'; // ... erm yeah... hopefully this is it!
}
if(strrpos($linkmodel, 'Model') !== strlen($linkmodel) - 5){
$linkmodel .= 'Model';
}
$this->_linked[] = [
'key'   => $k,
'model' => $linkmodel,
'on'    => is_array($linkon) ? $linkon : [$linkon => $k],
'link'  => $linktype,
];
}
}
$i = self::GetIndexes();
$pri = (isset($i['primary'])) ? $i['primary'] : false;
if($pri && !is_array($pri)) $pri = [$pri];
if ($pri && func_num_args() == sizeof($i['primary'])) {
foreach ($pri as $k => $v) {
$this->_data[$v] = func_get_arg($k);
}
}
if($key !== null){
$this->load();
}
}
public function load() {
if (!self::GetTableName()) {
return;
}
$i = self::GetIndexes();
$s = self::GetSchema();
$pri = (isset($i['primary'])) ? $i['primary'] : false;
if($pri && !is_array($pri)) $pri = [$pri];
$keys = [];
if ($pri && sizeof($i['primary'])) {
foreach ($pri as $k) {
$v = $this->get($k);
if ($v === null) continue;
$keys[$k] = $v;
}
}
if ($this->_cacheable) {
}
if(
isset($s['site']) &&
$s['site']['type'] == Model::ATT_TYPE_SITE &&
Core::IsComponentAvailable('enterprise') &&
MultiSiteHelper::IsEnabled() &&
$this->get('site') === null
){
$keys['site'] = MultiSiteHelper::GetCurrentSiteID();
}
$data = Core\Datamodel\Dataset::Init()
->select('*')
->table(self::GetTableName())
->where($keys)
->execute($this->interface);
if ($data->num_rows) {
$this->_data     = $data->current();
$this->_datainit = $data->current();
$this->_exists = true;
}
else {
$this->_exists = false;
}
return;
}
public function save() {
$save = false;
if(!$this->_exists){
$save = true;
}
elseif($this->changed()){
$save = true;
}
else{
foreach($this->_linked as $l){
if(isset($l['records'])){
$save = true;
break;
}
if(isset($l['purged'])){
$save = true;
break;
}
}
}
if(!$save){
return false;
}
HookHandler::DispatchHook('/core/model/presave', $this);
foreach($this->_linked as $l){
if(!is_array($l['on'])){
$l['on'] = [$l['on'] => $l['on'] ];
}
if($l['link'] == Model::LINK_HASONE && sizeof($l['on']) == 1){
reset($l['on']);
$remotek = key($l['on']);
$localk  = $l['on'][$remotek];
$locals = $this->getKeySchema($localk);
if(!$locals) continue;
if($locals['type'] != Model::ATT_TYPE_UUID_FK) continue;
if(isset($l['records'])){
$model = $l['records'];
}
elseif(isset($this->_data[$localk]) && $this->_data[$localk] instanceof Model){
$model = $this->_data[$localk];
}
else{
continue;
}
$model->save();
$this->set($localk, $model->get($remotek));
}
}
if ($this->_exists) $this->_saveExisting();
else $this->_saveNew();
foreach($this->_linked as $k => $l){
switch($l['link']){
case Model::LINK_HASONE:
$models = isset($l['records']) ? [$l['records']] : null;
$deletes = isset($l['purged']) ? $l['purged'] : null;
break;
case Model::LINK_HASMANY:
$models = isset($l['records']) ? $l['records'] : null;
$deletes = isset($l['purged']) ? $l['purged'] : null;
break;
default:
$models = null;
$deletes = null;
break;
}
if($deletes){
foreach($deletes as $model){
$model->delete();
}
unset($l['purged']);
}
if($models){
foreach($models as $model){
$model->setFromArray($this->_getLinkWhereArray($k));
$model->save();
}
}
}
$this->_exists     = true;
$this->_datainit = $this->_data;
if(($class = get_parent_class($this)) != 'Model'){
$idx = self::GetIndexes();
if(isset($idx['primary']) && sizeof($idx['primary']) == 1){
$schema = $this->getKeySchema($idx['primary'][0]);
if($schema['type'] == Model::ATT_TYPE_UUID){
$refp = new ReflectionClass($class);
$refm = $refp->getMethod('Construct');
$parent = $refm->invoke(null, $this->get($idx['primary'][0]));
$parent->setFromArray($this->getAsArray());
$parent->save();
}
}
}
HookHandler::DispatchHook('/core/model/postsave', $this);
return true;
}
public function get($k) {
if($k === '__CLASS__'){
return get_called_class();
}
elseif($k === '__PRIMARYKEY__'){
return $this->getPrimaryKeyString();
}
elseif($this->_datadecrypted !== null && array_key_exists($k, $this->_datadecrypted)){
return $this->_datadecrypted[$k];
}
elseif (array_key_exists($k, $this->_data)) {
return $this->_data[$k];
}
elseif($this->getKeySchema($k) && $this->getKeySchema($k)['type'] == Model::ATT_TYPE_ALIAS){
return $this->get( $this->getKeySchema($k)['alias'] );
}
elseif (array_key_exists($k, $this->_dataother)) {
return $this->_dataother[$k];
}
elseif($this->getLink($k)){
return $this->getLink($k);
}
else {
return null;
}
}
public function __toString(){
return $this->getLabel();
}
public function getLabel(){
$s = $this->getKeySchemas();
if(isset($s['name'])){
return $this->get('name');
}
elseif(isset($s['title'])){
return $this->get('title');
}
elseif(isset($s['key'])){
return $this->get('key');
}
else{
return 'Unnamed ' . $this->getPrimaryKeyString();
}
}
public function getAsArray() {
if($this->_datadecrypted !== null){
return array_merge($this->_data, $this->_dataother, $this->_datadecrypted);
}
else{
return array_merge($this->_data, $this->_dataother);
}
}
public function getAsJSON(){
return json_encode($this->getAsArray());
}
public function getData(){
return $this->_data;
}
public function getInitialData(){
return $this->_datainit;
}
public function getKeySchemas() {
return self::GetSchema();
}
public function getKeySchema($key) {
$s = self::GetSchema();
if (!isset($s[$key])) return null;
return $s[$key];
}
public function getSearchIndexString(){
$strs = [];
foreach($this->getKeySchemas() as $k => $dat){
if(isset($dat['form']) && isset($dat['form']['type'])){
if($dat['form']['type'] == 'file') continue;
}
if($k == 'search_index_str') continue;
if($k == 'search_index_pri') continue;
if($k == 'search_index_sec') continue;
switch($dat['type']){
case Model::ATT_TYPE_TEXT:
case Model::ATT_TYPE_STRING:
$val = $this->get($k);
if(preg_match('/^[0-9\- \.\(\)]*$/', $val) && trim($val) != ''){
$val = preg_replace('/[ \-\.\(\)]/', '', $val);
}
if($val){
$strs[] = $val;
}
break;
}
}
return implode(' ', $strs);
}
public function hasDraft(){
if(Core::IsComponentAvailable('model-audit')){
return ModelAudit\Helper::ModelHasDraft($this);
}
else{
return false;
}
}
public function getDraftStatus(){
if(!$this->exists()){
return 'pending_creation';
}
elseif($this->hasDraft() && $this->get('___auditmodel')->get('data') == '[]'){
return 'pending_deletion';
}
elseif($this->hasDraft()){
return 'pending_update';
}
else{
return '';
}
}
public function getControlLinks(){
return [];
}
public function _loadFromRecord($record) {
$this->_data = $record;
$this->_datainit = $this->_data;
$this->_exists   = true;
}
public function delete() {
$s = self::GetSchema();
foreach ($this->_data as $k => $v) {
if(!isset($s[$k])){
continue;
}
$keyschema = $s[$k];
if($keyschema['type'] == Model::ATT_TYPE_DELETED) {
if(!$v){
$nv = Time::GetCurrentGMT();
$this->set($k, $nv);
return $this->save();
}
else{
break;
}
}
}
foreach ($this->_linked as $k => $l) {
switch($l['link']){
case Model::LINK_HASONE:
$child = $this->getLink($k);
$child->delete();
break;
case Model::LINK_HASMANY:
$children = $this->getLink($k);
foreach($children as $child){
$child->delete();
}
break;
}
if (isset($this->_linked[$k]['records'])) unset($this->_linked[$k]['records']);
}
if ($this->exists()) {
if(($class = get_parent_class($this)) != 'Model'){
$idx = self::GetIndexes();
if(isset($idx['primary']) && sizeof($idx['primary']) == 1){
$schema = $this->getKeySchema($idx['primary'][0]);
if($schema['type'] == Model::ATT_TYPE_UUID){
$refp = new ReflectionClass($class);
$refm = $refp->getMethod('Construct');
$parent = $refm->invoke(null, $this->get($idx['primary'][0]));
$parent->delete();
}
}
}
$n = $this->_getTableName();
$i = self::GetIndexes();
$dat = new Core\Datamodel\Dataset();
$dat->table($n);
if (!isset($i['primary'])) {
throw new Exception('Unable to delete model [ ' . get_class($this) . ' ] without any primary keys.');
}
$pri = $i['primary'];
if(!is_array($pri)) $pri = [$pri];
foreach ($pri as $k) {
$dat->where([$k => $this->_data[$k]]);
}
$dat->limit(1)->delete();
if ($dat->execute($this->interface)) {
$this->_exists = false;
}
}
return true;
}
public function validate($k, $v, $throwexception = false) {
$s = self::GetSchema();
$valid = true;
if($v == '' || $v === null){
if(!isset($s['required']) || !$s['required']){
return true;
}
}
if (isset($s[$k]['validation'])) {
$check = $s[$k]['validation'];
if (is_array($check) && sizeof($check) == 2 && $check[0] == 'this') {
$valid = call_user_func([$this, $check[1]], $v);
}
elseif (strpos($check, '::') !== false) {
$valid = call_user_func($check, $v);
}
elseif (
($check{0} == '/' && !preg_match($check, $v)) ||
($check{0} == '#' && !preg_match($check, $v))
) {
$valid = false;
}
}
elseif($s[$k]['type'] == Model::ATT_TYPE_INT){
if(!isset($s[$k]['validationmessage'])){
$s[$k]['validationmessage'] = $k . ' must be a valid number.';
}
if(!(
is_int($v) ||
ctype_digit($v) ||
(is_float($v) && strpos($v, '.') === false)
)){
$valid = false;
}
}
if ($valid === true) {
return true;
}
if ($valid === false) $msg = isset($s[$k]['validationmessage']) ? $s[$k]['validationmessage'] : $k . ' fails validation';
else $msg = $valid;
if ($throwexception) {
throw new ModelValidationException($msg);
}
else {
return $msg;
}
}
public function translateKey($k, $v, $commit = false){
$s = self::GetSchema();
if(!isset($s[$k])) return $v;
$t = &$s[$k];
$type = $t['type']; // Type is one of the required properties.
if(!isset($t['default'])){
if(isset($t['null']) && $t['null']){
$default = null;
}
else{
switch($type){
case Model::ATT_TYPE_BOOL:
case Model::ATT_TYPE_CREATED:
case Model::ATT_TYPE_FLOAT:
case Model::ATT_TYPE_INT:
case Model::ATT_TYPE_UPDATED:
case Model::ATT_TYPE_DELETED:
$default = '0';
break;
case Model::ATT_TYPE_DATA:
case Model::ATT_TYPE_STRING:
case Model::ATT_TYPE_TEXT:
$default = '';
break;
case Model::ATT_TYPE_ISO_8601_DATE:
$default = '0000-00-00';
break;
case Model::ATT_TYPE_ISO_8601_DATETIME:
$default = '0000-00-00 00:00:00';
break;
case Model::ATT_TYPE_ID:
case Model::ATT_TYPE_UUID:
$default = null;
break;
default:
$default = '';
break;
}
}
}
else{
$default = $t['default'];
}
switch($type){
case Model::ATT_TYPE_ISO_8601_DATE:
if($v == '' || $v == '0000-00-00' || $v === null){
$v = $default;
}
break;
case Model::ATT_TYPE_ISO_8601_DATETIME:
if($v == '' || $v == '0000-00-00 00:00:00' || $v === null){
$v = $default;
}
break;
case Model::ATT_TYPE_BOOL:
if($v === true){
$v = '1';
}
elseif($v === false){
$v = '0';
}
else{
switch(strtolower($v)){
case 'yes':
case 'on':
case 1:
case 'true':
$v = '1';
break;
default:
$v = '0';
}
}
break;
default:
if($v === null){
$v = $default;
}
elseif($v instanceof Model && $commit){
$v->save();
$v = $v->get('id');
}
break;
}
return $v;
}
public function set($k, $v) {
if($v instanceof Model && $this->_getLinkIndex($k) !== null){
$this->setLink($k, $v);
return true;
}
elseif (array_key_exists($k, $this->_data)) {
$keydat = $this->getKeySchema($k);
if($this->_data[$k] === null && $v === null){
return false;
}
elseif(
$this->_data[$k] !== null &&
$keydat['type'] == Model::ATT_TYPE_STRING
){
if(\Core\compare_strings($this->_data[$k], $v)){
return false;
}
}
elseif ($this->_data[$k] !== null){
if(\Core\compare_values($this->_data[$k], $v)){
return false;
}
}
$this->validate($k, $v, true);
$v = $this->translateKey($k, $v);
$this->_setLinkKeyPropagation($k, $v);
if($keydat['encrypted']){
$this->decryptData();
$this->_datadecrypted[$k] = $v;
$this->_data[$k] = self::EncryptValue($v);
}
else{
$this->_data[$k] = $v;
}
return true;
}
elseif($this->getKeySchema($k) && $this->getKeySchema($k)['type'] == Model::ATT_TYPE_ALIAS){
return $this->set( $this->getKeySchema($k)['alias'], $v);
}
else {
$this->_dataother[$k] = $v;
return true;
}
}
public function getLinkFactory($linkname){
$idx = $this->_getLinkIndex($linkname);
if($idx === null){
return null; // @todo Error Handling
}
$c = $this->_getLinkClassName($linkname);
$f = new ModelFactory($c);
switch($this->_linked[$idx]['link']){
case Model::LINK_HASONE:
case Model::LINK_BELONGSTOONE:
$f->limit(1);
break;
}
$wheres = $this->_getLinkWhereArray($linkname);
$f->where($wheres);
return $f;
}
public function getLink($linkname, $order = null) {
$idx = $this->_getLinkIndex($linkname);
if($idx === null){
return null; // @todo Error Handling
}
if($order === null && isset($this->_linked[$idx]['order'])){
$order = $this->_linked[$idx]['order'];
}
if (!isset($this->_linked[$idx]['records'])) {
$f       = $this->getLinkFactory($linkname);
$c       = $this->_getLinkClassName($linkname);
$wheres  = $this->_getLinkWhereArray($linkname);
$isBlank = true;
foreach($wheres as $val){
if(trim($val) != ''){
$isBlank = false;
break;
}
}
if($isBlank){
$this->_linked[$idx]['records'] = ($f->getDataset()->_limit == 1) ? null : [];
}
else{
if ($order){
$f->order($order);
}
$this->_linked[$idx]['records'] = $f->get();
}
if ($this->_linked[$idx]['records'] === null) {
$this->_linked[$idx]['records'] = new $c();
foreach ($wheres as $k => $v) {
$this->_linked[$idx]['records']->set($k, $v);
}
}
}
return $this->_linked[$idx]['records'];
}
public function findLink($linkname, $searchkeys = []) {
$l = $this->getLink($linkname);
if ($l === null) return null;
if (!is_array($l)) {
$f = true;
foreach ($searchkeys as $k => $v) {
if ($l->get($k) != $v) {
$f = false;
break;
}
}
return ($f) ? $l : false;
}
else {
foreach ($l as $model) {
$f = true;
foreach ($searchkeys as $k => $v) {
if ($model->get($k) != $v) {
$f = false;
break;
}
}
if ($f) return $model;
}
$c = $this->_getLinkClassName($linkname);
$model = new $c();
$model->setFromArray($this->_getLinkWhereArray($linkname));
$model->setFromArray($searchkeys);
$model->load();
$this->_linked[$linkname]['records'][] = $model;
return $model;
}
}
public function setLink($linkname, Model $model) {
$idx = $this->_getLinkIndex($linkname);
if($idx === null){
return null; // @todo Error Handling
}
switch($this->_linked[$idx]['link']){
case Model::LINK_HASONE:
case Model::LINK_BELONGSTOONE:
$this->_linked[$idx]['records'] = $model;
break;
case Model::LINK_HASMANY:
case Model::LINK_BELONGSTOMANY:
if(!isset($this->_linked[$idx]['records'])) $this->_linked[$idx]['records'] = [];
$this->_linked[$idx]['records'][] = $model;
break;
}
}
public function resetLink($linkname){
$idx = $this->_getLinkIndex($linkname);
if($idx === null){
return null; // @todo Error Handling
}
$this->_linked[$idx]['records'] = null;
if(isset($this->_linked[$idx]['purged'])){
unset($this->_linked[$idx]['purged']);
}
}
public function deleteLink(Model $link){
foreach($this->_linked as $idx => $linkset){
if(!isset($linkset['records'])) continue;
if(is_array($linkset['records'])){
foreach($linkset['records'] as $k => $rec){
if($rec == $link){
if(!isset($this->_linked[$idx]['purged'])){
$this->_linked[$idx]['purged'] = [];
}
$this->_linked[$idx]['purged'][] = $link;
unset($this->_linked[$idx]['records'][$k]);
return true;
}
}
}
elseif($linkset['records'] == $link){
if(!isset($this->_linked[$idx]['purged'])){
$this->_linked[$idx]['purged'] = [];
}
$this->_linked[$idx]['purged'][] = $link;
$this->_linked[$idx]['records'] = null;
return true;
}
}
return false;
}
public function setFromArray($array) {
foreach ($array as $k => $v) {
$this->set($k, $v);
}
}
public function setFromForm(Form $form, $prefix = null){
$els = $form->getElements(true, false);
foreach ($els as $e) {
if ($prefix){
if(!preg_match('/^' . $prefix . '\[(.*?)\].*/', $e->get('name'), $matches)) continue;
$key = $matches[1];
}
else{
$key = $e->get('name');
}
$val    = $e->get('value');
$schema = $this->getKeySchema($key);
if(!$schema) continue;
$this->set($key, $val);
}
}
public function setToFormElement($key, FormElement $element){
}
public function addToFormPost(Form $form, $prefix){
}
public function exists() {
return $this->_exists;
}
public function isdeleted(){
$s = self::GetSchema();
foreach ($this->_data as $k => $v) {
if(!isset($s[$k])){
continue;
}
$keyschema = $s[$k];
if($keyschema['type'] == Model::ATT_TYPE_DELETED) {
if($v){
return true;
}
}
}
if( sizeof($this->_datainit) > 0 && !$this->_exists ){
return true;
}
return false;
}
public function isnew() {
return !$this->_exists;
}
public function changed($key = null){
$s = self::GetSchema();
foreach ($this->_data as $k => $v) {
if($key !== null && $key != $k){
continue;
}
if(!isset($s[$k])){
continue;
}
$keyschema = $s[$k];
switch ($keyschema['type']) {
case Model::ATT_TYPE_CREATED:
case Model::ATT_TYPE_UPDATED:
continue 2;
}
if(!array_key_exists($k, $this->_datainit)){
return true;
}
if($this->_datainit[$k] != $this->_data[$k]){
return true;
}
if(isset($keyschema['type']) && $keyschema['type'] == Model::ATT_TYPE_STRING){
if(!\Core\compare_strings($this->_datainit[$k], $this->_data[$k])) return true;
}
else{
if(!\Core\compare_values($this->_datainit[$k], $this->_data[$k])) return true;
}
}
return false;
}
public function decryptData(){
if($this->_datadecrypted === null){
$this->_datadecrypted = [];
foreach($this->getKeySchemas() as $k => $v){
if($v['encrypted']){
$this->_datadecrypted[$k] = self::DecryptValue($this->_data[$k]);
}
}
}
}
public function _getTableName(){
return self::GetTableName();
}
public function getPrimaryKeyString(){
$bits = [];
$i = self::GetIndexes();
if(isset($i['primary'])){
$pri = $i['primary'];
if(!is_array($pri)) $pri = [$pri];
foreach ($pri as $k) {
$val = $this->get($k);
if ($val === null) $val = 'null';
elseif ($val === false) $val = 'false';
$bits[] = $val;
}
}
return implode('-', $bits);
}
public function offsetExists($offset) {
return (array_key_exists($offset, $this->_data));
}
public function offsetGet($offset) {
return $this->get($offset);
}
public function offsetSet($offset, $value) {
$this->set($offset, $value);
}
public function offsetUnset($offset) {
$this->set($offset, null);
}
protected function _setLinkKeyPropagation($key, $newval) {
$exists = $this->exists();
foreach ($this->_linked as $lk => $l) {
if($l['link'] == Model::LINK_BELONGSTOONE) continue;
if($l['link'] == Model::LINK_BELONGSTOMANY) continue;
$dolink = false;
if (!isset($l['on'])) {
}
elseif (is_array($l['on'])) {
foreach ($l['on'] as $k => $v) {
if (is_numeric($k) && $v == $key) $dolink = true;
elseif (!is_numeric($k) && $k == $key) $dolink = true;
}
}
else {
if ($l['on'] == $key) $dolink = true;
}
if (!$dolink) continue;
if($exists){
$links = $this->getLink($lk);
if (!is_array($links)) $links = [$links];
foreach ($links as $model) {
$model->set($key, $newval);
}
}
else{
if(!isset($this->_linked[$lk]['records'])) continue;
if(is_array($this->_linked[$lk]['records'])){
foreach($this->_linked[$lk]['records'] as $model){
$model->set($key, $newval);
}
}
else{
$this->_linked[$lk]['records']->set($key, $newval);
}
}
}
}
protected function _getLinkClassName($linkname) {
$idx = $this->_getLinkIndex($linkname);
if($idx === null){
return null; // @todo Error Handling
}
$c = $this->_linked[$idx]['model'];
if (!is_subclass_of($c, 'Model')){
return null; // @todo Error Handling
}
return $c;
}
protected function _saveNew() {
$i = self::GetIndexes();
$s = self::GetSchema();
$n = $this->_getTableName();
if (!isset($i['primary'])) $i['primary'] = []; // No primary schema defined... just don't make the in_array bail out.
$dat = new Core\Datamodel\Dataset();
$dat->table($n);
$idcol = false;
foreach ($this->_data as $k => $v) {
$keyschema = $s[$k];
switch ($keyschema['type']) {
case Model::ATT_TYPE_CREATED:
case Model::ATT_TYPE_UPDATED:
if($v){
$dat->insert($k, $v);
}
else{
$nv = Time::GetCurrentGMT();
$dat->insert($k, $nv);
$this->_data[$k] = $nv;
}
break;
case Model::ATT_TYPE_ID:
$nv = $this->_data[$k];
if($this->_data[$k]){
$dat->insert($k, $nv);
}
$dat->setID($k, $nv);
$idcol = $k; // Remember this for after the save.
break;
case Model::ATT_TYPE_UUID:
if($this->_data[$k] && isset($this->_datainit[$k]) && $this->_datainit[$k]){
$nv = $this->_data[$k];
$dat->setID($k, $nv);
}
elseif($this->_data[$k]){
$nv = $this->_data[$k];
$dat->insert($k, $nv);
$dat->setID($k, $nv);
}
else{
$nv = Core::GenerateUUID();
$dat->insert($k, $nv);
$this->_data[$k] = $nv;
$dat->setID($k, $nv);
}
$idcol = $k;
break;
case Model::ATT_TYPE_SITE:
if(
Core::IsComponentAvailable('enterprise') &&
MultiSiteHelper::IsEnabled() &&
($v === null || $v === false)
){
$site = MultiSiteHelper::GetCurrentSiteID();
$dat->insert('site', $site);
$this->_data[$k] = $site;
}
elseif($v === null || $v === false){
$dat->insert('site', 0);
$this->_data[$k] = 0;
}
else{
$dat->insert($k, $v);
}
break;
default:
$v = $this->translateKey($k, $v, true);
$dat->insert($k, $v);
break;
}
}
$dat->execute($this->interface);
if ($idcol) $this->_data[$idcol] = $dat->getID();
}
protected function _saveExisting($useset = false) {
if(!$this->changed()) return false;
$i = self::GetIndexes();
$s = self::GetSchema();
$n = $this->_getTableName();
$pri = isset($i['primary']) ? $i['primary'] : [];
if($pri && !is_array($pri)) $pri = [$pri];
if($pri && !is_array($pri)) $pri = [$pri];
$dat = new Core\Datamodel\Dataset();
$dat->table($n);
$idcol = false;
foreach ($this->_data as $k => $v) {
if(!isset($s[$k])){
continue;
}
$keyschema = $s[$k];
switch ($keyschema['type']) {
case Model::ATT_TYPE_CREATED:
continue 2;
case Model::ATT_TYPE_UPDATED:
$nv = Time::GetCurrentGMT();
$dat->update($k, $nv);
$this->_data[$k] = $nv;
continue 2;
case Model::ATT_TYPE_ID:
case Model::ATT_TYPE_UUID:
$dat->setID($k, $this->_data[$k]);
$idcol = $k; // Remember this for after the save.
continue 2;
}
$v = $this->translateKey($k, $v, true);
if (in_array($k, $pri)) {
if ($this->_datainit[$k] != $v){
if($useset){
$dat->set($k, $v);
}
else{
$dat->update($k, $v);
}
}
$dat->where($k, $this->_datainit[$k]);
$this->_data[$k] = $v;
}
else {
if(isset($this->_datainit[$k])){
if($keyschema['type'] == Model::ATT_TYPE_STRING){
if(\Core\compare_strings($this->_datainit[$k], $v)) continue;
}
else{
if(\Core\compare_values($this->_datainit[$k], $v)) continue;
}
}
if($useset){
$dat->set($k, $v);
}
else{
$dat->update($k, $v);
}
}
}
if(!sizeof($dat->_sets)){
return false;
}
$dat->execute($this->interface);
}
protected function _getLinkWhereArray($linkname) {
$idx = $this->_getLinkIndex($linkname);
if($idx === null){
return null; // @todo Error Handling
}
$wheres = [];
if (!isset($this->_linked[$idx]['on'])) {
return null; // @todo automatic linking.
}
elseif (is_array($this->_linked[$idx]['on'])) {
foreach ($this->_linked[$idx]['on'] as $k => $v) {
if (is_numeric($k)) $wheres[$v] = $this->get($v);
else $wheres[$k] = $this->get($v);
}
}
else {
$k          = $this->_linked[$idx]['on'];
$wheres[$k] = $this->get($k);
}
if($linkname === 'Page' && Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$schema = self::GetSchema();
if(isset($schema['site']) && $schema['site']['type'] == Model::ATT_TYPE_SITE){
$wheres['site'] = $this->get('site');
}
}
return $wheres;
}
protected function _getLinkIndex($name){
if(isset($this->_linkIndexCache[$name])){
return $this->_linkIndexCache[ $name ];
}
foreach($this->_linked as $idx => $dat){
if($idx === $name){
return $idx;
}
if(isset($dat['key']) && $dat['key'] == $name){
$this->_linkIndexCache[$name] = $idx;
return $idx;
}
if(isset($dat['model']) && $dat['model'] == $name){
$this->_linkIndexCache[$name] = $idx;
return $idx;
}
if(isset($dat['model']) && $dat['model'] == $name . 'Model'){
$this->_linkIndexCache[$name] = $idx;
return $idx;
}
}
$this->_linkIndexCache[$name] = null;
return null;
}
protected function _getCacheKey() {
if (!$this->_cacheable) return false;
$i = self::GetIndexes();
if (!(isset($i['primary']) && sizeof($i['primary']))) return false;
$keys = $this->getPrimaryKeyString();
return 'DATA:' . self::GetTableName() . ':' . $keys;
}
public static function Construct($keys = null){
$class = get_called_class();
if($keys === null){
return new $class();
}
$cache = '';
foreach(func_get_args() as $a){
$cache .= $a . '-';
}
$cache = substr($cache, 0, -1);
if(!isset(self::$_ModelCache[$class])){
self::$_ModelCache[$class] = [];
}
if(!isset(self::$_ModelCache[$class][$cache])){
$reflection = new ReflectionClass($class);
$obj = $reflection->newInstanceArgs(func_get_args());
self::$_ModelCache[$class][$cache] = $obj;
}
return self::$_ModelCache[$class][$cache];
}
public static function Find($where = [], $limit = null, $order = null) {
$classname = get_called_class();
if(!sizeof($where) && $limit === null && $order === null){
if(!isset(self::$_ModelFindCache[$classname])){
$fac = new ModelFactory($classname);
self::$_ModelFindCache[$classname] = $fac->get();
}
return self::$_ModelFindCache[$classname];
}
$fac = new ModelFactory($classname);
$fac->where($where);
$fac->limit($limit);
$fac->order($order);
return $fac->get();
}
public static function GetAllAsOptions() {
$classname = get_called_class();
$ref = new ReflectionClass($classname);
$results = $ref->getMethod('Find')->invoke(null);
$idx = $ref->getMethod('GetIndexes')->invoke(null);
if(!isset($idx['primary'])){
return ['' => 'Unable to automatically get ' . $classname . ' as options because no primary key defined!'];
}
if(sizeof($idx['primary']) > 1){
return ['' => 'Unable to automatically get ' . $classname . ' as options because primary key defined as multiple columns!'];
}
$id = $idx['primary'][0];
$options = [];
foreach($results as $res){
$options[ $res->get($id) ] = $res->getLabel();
}
return $options;
}
public static function FindRaw($where = [], $limit = null, $order = null) {
$fac = new ModelFactory(get_called_class());
$fac->where($where);
$fac->limit($limit);
$fac->order($order);
return $fac->getRaw();
}
public static function Count($where = []) {
$fac = new ModelFactory(get_called_class());
$fac->where($where);
return $fac->count();
}
public static function Search($query, $where = []){
$ret = [];
$ref = new ReflectionClass(get_called_class());
if(!$ref->getProperty('HasSearch')->getValue()){
return $ret;
}
$fac = new ModelFactory(get_called_class());
if(sizeof($where)){
$fac->where($where);
}
if($ref->getProperty('HasDeleted')->getValue()){
$fac->where('deleted = 0');
}
$fac->where(\Core\Search\Helper::GetWhereClause($query));
foreach($fac->get() as $m){
$sr = new \Core\Search\ModelResult($query, $m);
if($sr->relevancy < 1) continue;
$sr->title = $m->getLabel();
$sr->link  = $m->get('baseurl');
$ret[] = $sr;
}
usort($ret, function($a, $b) {
return $a->relevancy < $b->relevancy;
});
return $ret;
}
public static function EncryptValue($value){
$cipher = 'AES-256-CBC';
$passes = 10;
$size = openssl_cipher_iv_length($cipher);
$iv = mcrypt_create_iv($size, MCRYPT_RAND);
if($value === '') return '';
elseif($value === null) return null;
$enc = $value;
for($i=0; $i<$passes; $i++){
$enc = openssl_encrypt($enc, $cipher, SECRET_ENCRYPTION_PASSPHRASE, true, $iv);
}
$payload = '$' . $cipher . '$' . str_pad($passes, 2, '0', STR_PAD_LEFT) . '$' . $enc . $iv;
return $payload;
}
public static function DecryptValue($payload) {
if($payload === null || $payload === '' || $payload === false){
return null;
}
preg_match('/^\$([^$]*)\$([0-9]*)\$(.*)$/m', $payload, $matches);
$cipher = $matches[1];
$passes = $matches[2];
$size = openssl_cipher_iv_length($cipher);
$dec = substr($payload, strlen($cipher) + 5, 0-$size);
$iv = substr($payload, 0-$size);
for($i=0; $i<$passes; $i++){
$dec = openssl_decrypt($dec, $cipher, SECRET_ENCRYPTION_PASSPHRASE, true, $iv);
}
return $dec;
}
public static function GetTableName() {
static $_tablenames = [];
$m = get_called_class();
if ($m == 'Model') return null;
if (!isset($_tablenames[$m])) {
$tbl = $m;
if (preg_match('/Model$/', $tbl)) $tbl = substr($tbl, 0, -5);
$tbl = preg_replace('/([A-Z])/', '_$1', $tbl);
if ($tbl{0} == '_') $tbl = substr($tbl, 1);
$tbl = strtolower($tbl);
$_tablenames[$m] = DB_PREFIX . $tbl;
}
return $_tablenames[$m];
}
public static function GetSchema() {
$classname = get_called_class();
if(!isset(self::$_ModelSchemaCache[$classname])){
$parent = get_parent_class($classname);
if($parent != 'Model'){
$parentref = new ReflectionClass($parent);
$ref = new ReflectionClass($classname);
self::$_ModelSchemaCache[$classname] = array_merge(
$parentref->getProperty('Schema')->getValue(),
$ref->getProperty('Schema')->getValue()
);
}
else{
$ref = new ReflectionClass($classname);
self::$_ModelSchemaCache[$classname] = $ref->getProperty('Schema')->getValue();
}
$schema =& self::$_ModelSchemaCache[$classname];
if($ref->getProperty('HasCreated')->getValue()){
if(!isset($schema['created'])){
$schema['created'] = [
'type' => Model::ATT_TYPE_CREATED,
'null' => false,
'default' => 0,
'comment' => 'The created timestamp of this record, populated automatically',
];
}
}
if($ref->getProperty('HasUpdated')->getValue()){
if(!isset($schema['updated'])){
$schema['updated'] = [
'type' => Model::ATT_TYPE_UPDATED,
'null' => false,
'default' => 0,
'comment' => 'The updated timestamp of this record, populated automatically',
];
}
}
if($ref->getProperty('HasDeleted')->getValue()){
if(!isset($schema['deleted'])){
$schema['deleted'] = [
'type' => Model::ATT_TYPE_DELETED,
'null' => false,
'default' => 0,
'comment' => 'The deleted timestamp of this record, populated automatically',
];
}
}
if($ref->getProperty('HasSearch')->getValue()){
$schema['search_index_str'] = [
'type' => Model::ATT_TYPE_TEXT,
'required' => false,
'null' => true,
'default' => null,
'formtype' => 'disabled',
'comment' => 'The search index of this record as a string'
];
$schema['search_index_pri'] = [
'type' => Model::ATT_TYPE_TEXT,
'required' => false,
'null' => true,
'default' => null,
'formtype' => 'disabled',
'comment' => 'The search index of this record as the DMP primary version'
];
$schema['search_index_sec'] = [
'type' => Model::ATT_TYPE_TEXT,
'required' => false,
'null' => true,
'default' => null,
'formtype' => 'disabled',
'comment' => 'The search index of this record as the DMP secondary version'
];
}
foreach ($schema as $k => $v) {
if (!isset($v['type']))               $schema[$k]['type']      = Model::ATT_TYPE_TEXT; // Default if not present.
if (!isset($v['maxlength']))          $schema[$k]['maxlength'] = false;
if (!isset($v['null']))               $schema[$k]['null']      = false;
if (!isset($v['comment']))            $schema[$k]['comment']   = '';
if (!array_key_exists('default', $v)) $schema[$k]['default']   = false;
if (!isset($v['encrypted']))          $schema[$k]['encrypted'] = false;
if (!isset($v['required']))           $schema[$k]['required']  = false;
if($v['type'] == Model::ATT_TYPE_ALIAS){
if(!isset($v['alias'])){
throw new Exception('Model [' . $classname . '] has alias key [' . $k . '] that does not have an "alias" attribute.  Every ATT_TYPE_ALIAS key MUST have exactly one "alias"');
}
if(!isset($schema[ $v['alias'] ])){
throw new Exception('Model [' . $classname . '] has alias key [' . $k . '] that points to a key that does not exist, [' . $v['alias'] . '].  All aliases MUST exist in the same model!');
}
if($schema[ $v['alias'] ]['type'] == Model::ATT_TYPE_ALIAS){
throw new Exception('Model [' . $classname . '] has alias key [' . $k . '] that points to another alias.  Aliases MUST NOT point to another alias... bad things could happen.');
}
}
if($schema[$k]['default'] === false && !$schema[$k]['null']){
if($schema[$k]['type'] == Model::ATT_TYPE_TEXT){
$schema[$k]['default'] = '';
}
}
if($v['type'] == Model::ATT_TYPE_ENUM){
$schema[$k]['options'] = isset($schema[$k]['options']) ? $schema[$k]['options'] : [];
}
else{
$schema[$k]['options'] = null;
}
if(isset($v['title'])){
$schema[$k]['title'] = $v['title'];
}
elseif(isset($v['form']) && is_array($v['form']) && isset($v['form']['title'])){
$schema[$k]['title'] = $v['form']['title'];
}
elseif(isset($v['formtitle'])){
$schema[$k]['title'] = $v['formtitle'];
}
else{
$schema[$k]['title'] = ucwords(str_replace('_', ' ', $k));
}
}
}
return self::$_ModelSchemaCache[$classname];
}
public static function GetIndexes() {
$classname = get_called_class();
$parent = get_parent_class($classname);
if($parent != 'Model'){
$parentref = new ReflectionClass($parent);
$ref = new ReflectionClass($classname);
return array_merge(
$parentref->getProperty('Indexes')->getValue(),
$ref->getProperty('Indexes')->getValue()
);
}
else{
$ref = new ReflectionClass($classname);
return $ref->getProperty('Indexes')->getValue();
}
}
}
class ModelFactory {
public $interface = null;
private $_model;
private $_dataset;
private $_stream;
public function __construct($model) {
$this->_model = $model;
$m              = $this->_model;
$this->_dataset = new Core\Datamodel\Dataset();
$this->_dataset->table($m::GetTablename());
$this->_dataset->select('*');
}
public function where() {
call_user_func_array([$this->_dataset, 'where'], func_get_args());
}
public function whereGroup() {
call_user_func_array([$this->_dataset, 'whereGroup'], func_get_args());
}
public function order() {
call_user_func_array([$this->_dataset, 'order'], func_get_args());
}
public function limit() {
call_user_func_array([$this->_dataset, 'limit'], func_get_args());
}
public function get() {
$this->_performMultisiteCheck();
$rs = $this->_dataset->execute($this->interface);
$ret = [];
foreach ($rs as $row) {
$model = new $this->_model();
$model->_loadFromRecord($row);
$ret[] = $model;
}
if ($this->_dataset->_limit == 1) {
return (sizeof($ret)) ? $ret[0] : null;
}
else {
return $ret;
}
}
public function getRaw(){
$this->_performMultisiteCheck();
$rs = $this->_dataset->execute($this->interface);
return $rs->_data;
}
public function getNext(){
if($this->_stream === null){
$this->_performMultisiteCheck();
$this->_stream = new \Core\Datamodel\DatasetStream($this->_dataset);
}
$next = $this->_stream->getRecord();
if($next === null){
return null;
}
$model = new $this->_model();
$model->_loadFromRecord($next);
return $model;
}
public function count() {
$this->_performMultisiteCheck();
$clone = clone $this->_dataset;
$rs    = $clone->count()->execute($this->interface);
return $rs->num_rows;
}
public function getDataset(){
return $this->_dataset;
}
private function _performMultisiteCheck(){
$m = $this->_model;
$ref = new ReflectionClass($m);
$schema = $ref->getMethod('GetSchema')->invoke(null);
$index = $ref->getMethod('GetIndexes')->invoke(null);
if(
isset($schema['site']) &&
$schema['site']['type'] == Model::ATT_TYPE_SITE &&
Core::IsComponentAvailable('enterprise') &&
MultiSiteHelper::IsEnabled()
){
$siteexact = (sizeof($this->_dataset->getWhereClause()->findByField('site')) > 0);
$idexact = false;
if(isset($index['primary'])){
$allids = true;
foreach($index['primary'] as $k){
if(sizeof($this->_dataset->getWhereClause()->findByField($k)) == 0){
$allids = false;
break;
}
}
if($allids) $idexact = true;
}
if(!($siteexact || $idexact)){
$w = new DatasetWhereClause();
$w->setSeparator('or');
$w->addWhere('site = ' . MultiSiteHelper::GetCurrentSiteID());
$w->addWhere('site = -1');
$this->_dataset->where($w);
}
}
}
public static function GetSchema($model){
$s = new ModelSchema($model);
return $s;
}
}


### REQUIRE_ONCE FROM core/libs/core/ModelSchema.php
class ModelSchema extends Core\Datamodel\Schema{
public function __construct($model = null){
if($model !== null){
$this->readModel($model);
}
}
public function readModel($model){
$ref = new ReflectionClass($model);
$obj = $ref->newInstanceWithoutConstructor();
$schema = $obj->getKeySchemas();
$indexes = $ref->getMethod('GetIndexes')->invoke(null);
$this->indexes     = [];
$this->definitions = [];
$this->order       = [];
foreach($schema as $name => $def){
$def['name'] = $name;
$column = $this->_getColumnDefinition($def);
$this->definitions[$name] = $column;
if($def['type'] != Model::ATT_TYPE_ALIAS){
$this->order[] = $name;
}
}
foreach($indexes as $key => $dat){
if(!is_array($dat)){
$this->indexes[$key] = array($dat);
}
else{
$this->indexes[$key] = $dat;
}
}
}
private function _getColumnDefinition($def){
$column = new \Core\Datamodel\SchemaColumn();
$column->field     = $def['name'];
$column->type      = $def['type'];
$column->required  = $def['required'];
$column->maxlength = $def['maxlength'];
$column->default   = $def['default'];
$column->null      = $def['null'];
$column->comment   = $def['comment'];
if(isset($def['precision'])) $column->precision = $def['precision'];
if($column->type == Model::ATT_TYPE_STRING && !$column->maxlength){
$column->maxlength = 255;
}
if($column->type == Model::ATT_TYPE_ID && !$column->maxlength){
$column->maxlength = 15;
$column->autoinc = true;
}
if($column->type == Model::ATT_TYPE_ID_FK){
$column->maxlength = 15;
}
if($column->type == Model::ATT_TYPE_UUID){
$column->maxlength = 21;
$column->autoinc = false;
}
if($column->type == Model::ATT_TYPE_UUID_FK){
$column->maxlength = 21;
}
if($column->type == Model::ATT_TYPE_INT && !$column->maxlength){
$column->maxlength = 15;
}
if($column->type == Model::ATT_TYPE_CREATED && !$column->maxlength){
$column->maxlength = 15;
}
if($column->type == Model::ATT_TYPE_UPDATED && !$column->maxlength){
$column->maxlength = 15;
}
if($column->type == Model::ATT_TYPE_DELETED && !$column->maxlength){
$column->maxlength = 15;
}
if($column->type == Model::ATT_TYPE_SITE){
$column->default = 0;
$column->comment = 'The site id in multisite mode, (or 0 otherwise)';
$column->maxlength = 15;
}
if($column->type == Model::ATT_TYPE_ALIAS){
$column->aliasof = $def['alias'];
}
if($column->type == Model::ATT_TYPE_ENUM){
if(!\Core\is_numeric_array($def['options'])){
$column->options = array_keys($def['options']);
}
else{
$column->options = $def['options'];
}
}
if($column->default === false){
if($column->null){
$column->default = null;
}
else{
switch($column->type){
case Model::ATT_TYPE_INT:
case Model::ATT_TYPE_BOOL:
case Model::ATT_TYPE_CREATED:
case Model::ATT_TYPE_UPDATED:
case Model::ATT_TYPE_DELETED:
case Model::ATT_TYPE_FLOAT:
$column->default = 0;
break;
case Model::ATT_TYPE_ISO_8601_DATE:
$column->default = '0000-00-00';
break;
case Model::ATT_TYPE_ISO_8601_DATETIME:
$column->default = '0000-00-00 00:00:00';
break;
default:
$column->default = '';
}
}
}
switch($column->type){
case Model::ATT_TYPE_BOOL:
case Model::ATT_TYPE_ENUM:
case Model::ATT_TYPE_STRING:
case Model::ATT_TYPE_TEXT:
case Model::ATT_TYPE_UUID:
case Model::ATT_TYPE_UUID_FK:
$column->encoding = 'utf8';
break;
}
return $column;
}
}


### REQUIRE_ONCE FROM core/libs/core/Time.class.php
class Time {
const TIMEZONE_GMT     = 0;
const TIMEZONE_DEFAULT = 100;
const TIMEZONE_USER    = 101;
const FORMAT_ISO8601 = 'c';
const FORMAT_RFC2822 = 'r';
const FORMAT_FULLDATETIME = self::FORMAT_ISO8601;
const FORMAT_EPOCH = 'U';
private static $_Instance = null;
private $timezones = array();
private function __construct() {
if (is_numeric(TIME_DEFAULT_TIMEZONE)) {
throw new Exception('Please ensure that the constant TIME_DEFAULT_TIMEZONE is set to a valid timezone string.');
}
$this->timezones[0]   = new DateTimeZone('GMT');
$this->timezones[100] = new DateTimeZone(TIME_DEFAULT_TIMEZONE);
}
private function _getTimezone($timezone) {
if ($timezone == Time::TIMEZONE_USER) {
$timezone = \Core\user()->get('timezone');
if($timezone === null) $timezone = date_default_timezone_get();
if (is_numeric($timezone)) $timezone = Time::TIMEZONE_DEFAULT;
}
if (!isset($this->timezones[$timezone])) {
$this->timezones[$timezone] = new DateTimeZone($timezone);
}
return $this->timezones[$timezone];
}
private static function _Singleton() {
if (self::$_Instance === null) {
self::$_Instance = new self();
}
return self::$_Instance;
}
public static function GetCurrentGMT($format = 'U') {
$date = new DateTime(null, self::_Singleton()->_getTimezone(0));
return $date->format($format);
}
public static function GetCurrent($timezone = Time::TIMEZONE_GMT, $format = 'U') {
$date = new DateTime(null, self::_Singleton()->_getTimezone($timezone));
return $date->format($format);
}
public static function GetRelativeAsString($time, $timezone = Time::TIMEZONE_GMT, $accuracy = 3, $timeformat = 'g:ia', $dateformat = 'M j, Y') {
$nowStamp = Time::GetCurrent($timezone, 'Ymd');
$cStamp   = Time::FormatGMT($time, $timezone, 'Ymd');
if ($nowStamp - $cStamp == 0) return 'Today at ' . Time::FormatGMT($time, $timezone, $timeformat);
elseif ($nowStamp - $cStamp == 1) return 'Yesterday at ' . Time::FormatGMT($time, $timezone, $timeformat);
elseif ($nowStamp - $cStamp == -1) return 'Tomorrow at ' . Time::FormatGMT($time, $timezone, $timeformat);
if ($accuracy <= 2) return Time::FormatGMT($time, $timezone, $dateformat);
if (abs($nowStamp - $cStamp) > 6) return Time::FormatGMT($time, $timezone, $dateformat);
return Time::FormatGMT($time, $timezone, 'l \a\t ' . $timeformat);
}
public static function FormatGMT($timeInGMT, $timezone = Time::TIMEZONE_GMT, $format = 'U') {
if ($timezone === null) $timezone = self::TIMEZONE_GMT;
if (is_numeric($timeInGMT)) $timeInGMT = '@' . $timeInGMT;
$date = new DateTime($timeInGMT, self::_Singleton()->_getTimezone(0));
if ($timezone != Time::TIMEZONE_GMT) $date->setTimezone(self::_Singleton()->_getTimezone($timezone));
return $date->format($format);
}
}


### REQUIRE_ONCE FROM core/libs/core/Session.class.php
} // ENDING GLOBAL NAMESPACE
namespace Core {
class Session implements \SessionHandlerInterface {
public static $Instance;
public static $Externals = [];
private static $_IsReady = false;
public function __construct(){
}
public function close() {
return true;
}
public function open($save_path, $session_id) {
\HookHandler::DispatchHook('/core/session/ready');
self::$_IsReady = true;
return true;
}
public function destroy($session_id) {
$dataset = new Datamodel\Dataset();
$dataset->table('session');
$dataset->where('session_id = ' . $session_id);
$dataset->where('ip_addr = ' . REMOTE_IP);
$dataset->delete();
$dataset->execute();
$_SESSION = null;
self::$_IsReady = false;
return true;
}
public function read($session_id) {
$model = self::_GetModel($session_id);
self::$Externals = $model->getExternalData();
return $model->getData();
}
public function write($session_id, $session_data) {
$model = self::_GetModel($session_id);
$model->setData($session_data);
$model->setExternalData(self::$Externals);
return $model->save();
}
public function gc($maxlifetime) {
return self::CleanupExpired();
}
public static function SetUser($u) {
$model = self::_GetModel(session_id());
$model->set('user_id', $u->get('id'));
$model->save();
if(isset($_SESSION['user_sudo'])){
Session::Set('user_sudo', $u);
}
else{
Session::Set('user', $u);
}
}
public static function DestroySession(){
if(self::$Instance !== null){
self::$Instance->destroy(session_id());
}
}
public static function ForceSave(){
}
public static function CleanupExpired(){
static $lastexecuted = 0;
$ttl = \ConfigHandler::Get('/core/session/ttl');
$datetime = (\Time::GetCurrentGMT() - $ttl);
if($lastexecuted == $datetime){
return true;
}
$dataset = new Datamodel\Dataset();
$dataset->table('session');
$dataset->where('updated < ' . $datetime);
$dataset->delete()->execute();
$lastexecuted = $datetime;
return true;
}
public static function Get($key, $default = null){
if(strpos($key, '/*') !== false){
$default = [];
}
if(sizeof($_COOKIE) == 0){
return $default;
}
self::_GetInstance();
if(strpos($key, '/*') !== false){
$sub = substr($key, 0, strpos($key, '/*'));
foreach($_SESSION as $k => $v){
if($k == $sub){
return $_SESSION[$k];
}
}
}
elseif(strpos($key, '/') !== false){
$sub = substr($key, 0, strpos($key, '/'));
$key = substr($key, strlen($sub)+1);
if(!isset($_SESSION[$sub])){
return $default;
}
return isset($_SESSION[$sub][$key]) ? $_SESSION[$sub][$key] : $default;
}
return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}
public static function Set($key, $value){
self::_GetInstance();
if(strpos($key, '/') !== false){
$sub = substr($key, 0, strpos($key, '/'));
$spr = substr($key, strlen($sub) + 1);
if(!isset($_SESSION[$sub])){
$_SESSION[$sub] = [];
}
$_SESSION[$sub][$spr] = $value;
}
else{
$_SESSION[$key] = $value;
}
}
public static function UnsetKey($key){
if(sizeof($_COOKIE) == 0){
return;
}
self::_GetInstance();
if($key === '*'){
$_SESSION = [];
}
elseif(strpos($key, '/*') !== false){
$sub = substr($key, 0, strpos($key, '/*'));
foreach($_SESSION as $k => $v){
if($k == $sub){
unset($_SESSION[$k]);
}
}
}
elseif(strpos($key, '/') !== false){
$sub = substr($key, 0, strpos($key, '/'));
$spr = substr($key, strlen($sub) + 1);
if(isset($_SESSION[$sub]) && isset($_SESSION[$sub][$spr])){
unset($_SESSION[$sub][$spr]);
}
}
elseif(isset($_SESSION[$key])){
unset($_SESSION[$key]);
}
}
private static function _GetInstance(){
if(self::$Instance === null){
ini_set('session.hash_bits_per_character', 5);
ini_set('session.hash_function', 1);
if(!defined('SESSION_COOKIE_NAME')){
define('SESSION_COOKIE_NAME', 'CorePlusSession');
}
session_name(SESSION_COOKIE_NAME);
if(defined('SESSION_COOKIE_DOMAIN') && SESSION_COOKIE_DOMAIN){
session_set_cookie_params(0, '/', SESSION_COOKIE_DOMAIN);
}
self::$Instance = new Session();
session_set_save_handler(self::$Instance, true);
session_start();
}
return self::$Instance;
}
private static function _GetModel($session_id) {
$model = new \SessionModel($session_id);
$model->set('ip_addr', REMOTE_IP);
return $model;
}
}
} // ENDING NAMESPACE Core

namespace  {

### REQUIRE_ONCE FROM core/models/ComponentModel.class.php
class ComponentModel extends Model {
public static $Schema = array(
'name'    => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 48,
'required'  => true,
'null'      => false,
),
'version' => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 24,
'null'      => false,
),
'enabled' => array(
'type'    => Model::ATT_TYPE_BOOL,
'default' => '1',
'null'    => false,
),
);
public static $Indexes = array(
'primary' => array('name'),
);
} // END class ComponentModel extends Model


### REQUIRE_ONCE FROM core/models/PageModel.class.php
class PageModel extends Model {
public static $Schema = array(
'title' => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'default'   => null,
'comment'   => '[Cached] Title of the page',
'null'      => true,
'form'      => array(
'type' => 'text',
'description' => 'Every page needs a title to accompany it, this should be short but meaningful.',
'group' => 'Basic',
'grouptype' => 'tabs',
),
),
'parenturl' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'null' => true,
'form' => array(
'type' => 'pageparentselect',
'title' => 'Parent Page',
'description' => 'The parent this page will appear under in the site breadcrumbs and structure.',
'group' => 'Meta Information & URL (SEO)',
'grouptype' => 'tabs',
),
),
'site' => array(
'type' => Model::ATT_TYPE_SITE,
'default' => -1,
'form' => [
'type' => 'system',
'group' => 'Access & Advanced',
'grouptype' => 'tabs',
'description' => 'Please note, changing the site ID on an existing page may result in loss of data or unexpected results.',
],
'comment' => 'The site id in multisite mode, (or -1 if global)',
),
'baseurl' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'required' => true,
'null' => false,
'form' => array(
'type' => 'system',
),
),
'rewriteurl' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'null' => false,
'validation' => array('this', 'validateRewriteURL'),
'form' => array(
'title' => 'Page URL',
'type' => 'pagerewriteurl',
'description' => 'Starts with a "/", omit the root web dir.',
'group' => 'Meta Information & URL (SEO)',
'grouptype' => 'tabs',
),
),
'editurl' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'default' => '',
'required' => false,
'null' => false,
'form' => array(
'type' => 'disabled',
),
'comment' => 'The edit URL for this page, set by the creating application.',
),
'deleteurl' => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'default'   => '',
'required'  => false,
'null'      => false,
'form' => array(
'type' => 'disabled',
),
'comment'   => 'The URL to perform the POST on to delete this page',
),
'component'    => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 48,
'required'  => false,
'default'   => '',
'null'      => false,
'form' => array(
'type' => 'disabled',
),
'comment'   => 'The component that registered this page, useful for uninstalling and cleanups',
),
'theme_template' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'default' => null,
'null' => true,
'comment' => 'Allows the page to define its own theme and widget information.',
'form' => array(
'type' => 'pagethemeselect',
'title' => 'Theme Skin',
'description' => 'This defines the master theme skin that will be used on this page.',
'group' => 'Access & Advanced',
'grouptype' => 'tabs',
)
),
'page_template' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 64,
'default' => null,
'null' => true,
'comment' => 'Allows the specific page template to be overridden.',
'form' => array(
'type' => 'pagepageselect',
'title' => 'Alternative Page Template',
'group' => 'Basic',
'grouptype' => 'tabs',
)
),
'last_template' => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 64,
'default'   => null,
'null'      => true,
'formtype'  => 'disabled',
'comment'   => 'The last page template used to render this page, useful in edit pages.',
),
'expires' => array(
'type' => Model::ATT_TYPE_INT,
'default' => 3600,
'form' => [
'title' => 'Cacheable / Expires',
'type' => 'select',
'options' => [
'0'       => 'No Cache Allowed',
'30'      => '30 seconds',
'60'      => '1 minute',
'120'     => '2 minutes',
'300'     => '5 minutes',
'600'     => '10 minutes',
'1800'    => '30 minutes',
'3600'    => '1 hour',
'7200'    => '2 hours',
'14400'   => '4 hours',
'21600'   => '6 hours',
'28800'   => '8 hours',
'43200'   => '12 hours',
'64800'   => '18 hours',
'86400'   => '24 hours',
'172800'  => '2 days',
'604800'  => '1 week',
'2462400' => '1 month',
],
'description' => 'Amount of time this page has a valid cache for, set to 0 to completely disable.
This cache only applies to guest users and bots.',
'group' => 'Access & Advanced',
'grouptype' => 'tabs',
],
),
'access' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 512,
'comment' => 'Access string of the page',
'null' => false,
'default' => '*',
'form' => array(
'type' => 'access',
'title' => 'Access Permissions',
'group' => 'Access & Advanced',
'grouptype' => 'tabs',
),
),
'password_protected' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'comment' => 'Password or phrase to protect this page',
'null' => false,
'default' => '',
'form' => array(
'type' => 'text',
'title' => 'Password',
'group' => 'Access & Advanced',
'grouptype' => 'tabs',
),
),
'fuzzy' => array(
'type' => Model::ATT_TYPE_BOOL,
'comment' => 'If this url is fuzzy or an exact match',
'null' => false,
'default' => '0',
'formtype' => 'system'
),
'admin' => array(
'type' => Model::ATT_TYPE_BOOL,
'comment' => 'If this page is an administration page',
'null' => false,
'default' => '0',
'formtype' => 'system'
),
'admin_group' => array(
'type' => Model::ATT_TYPE_STRING,
'comment' => 'Admin pages can be grouped together.  This is the name.',
'null' => false,
'default' => '',
'formtype' => 'disabled',
),
'pageviews' => array(
'type' => Model::ATT_TYPE_INT,
'formtype' => 'disabled',
'default' => 0,
'comment' => 'Number of page views',
'model_audit_ignore' => true, // Custom key for the component "Model Audit".
),
'selectable' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => 1,
'comment' => 'Selectable as a parent url',
'formtype' => 'disabled',
),
'indexable' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => 1,
'comment' => 'Page is displayed on the sitemap, search, and search crawlers',
'form' => [
'description' => 'Set to No if you do not want this page to be listed in search results.',
'group' => 'Meta Information & URL (SEO)',
'grouptype' => 'tabs',
],
),
'popularity' => array(
'type' => Model::ATT_TYPE_FLOAT,
'default' => 0.000,
'precision' => '10,8',
'comment' => 'Cache of the popularity score of this page',
'formtype' => 'disabled',
),
'published_status'      => array(
'type'    => Model::ATT_TYPE_ENUM,
'options' => array('published', 'draft'),
'default' => 'published',
'form' => array(
'title' => 'Published Status',
'description' => 'Set this to "draft" to make it visible to editors and admins
only.  Useful for saving a page without releasing it to public users.',
'group' => 'Publish Settings',
'grouptype' => 'tabs',
)
),
'published' => array(
'type' => Model::ATT_TYPE_INT,
'form' => array(
'title' => 'Published Date',
'type' => 'datetime',
'description' => 'Set this field to a desired date/time to mark the page to be published at that specific date and time.  If left blank, the current date and time are set automatically.  This CAN be set this to a future date to have the page to be published at that time.',
'group' => 'Publish Settings',
'grouptype' => 'tabs',
),
'comment' => 'The published date',
),
'published_expires' => array(
'type' => Model::ATT_TYPE_STRING,
'null' => true,
'default' => null,
'form' => array(
'title' => 'Publish Expires Date',
'type' => 'datetime',
'description' => 'Set to a future date/time to un-publish this page automatically at that specific date and time.',
'group' => 'Publish Settings',
'grouptype' => 'tabs',
'datetimepicker_dateformat' => 'yy-mm-dd',
'datetimepicker_timeformat' => 'HH:mm',
),
),
'body' => array(
'type'      => Model::ATT_TYPE_TEXT,
'default'   => '',
'comment'   => '[Cached] Body content of this page',
'null'      => false,
'form'      => array(
'type' => 'disabled',
),
),
);
public static $Indexes = array(
'primary' => array('site', 'baseurl'),
'unique:rewrite_url' => array('site', 'rewriteurl'),
'baseurlidx' => ['baseurl'],
'adminidx' => ['admin'],
'rewritefuzzy' => ['rewriteurl', 'fuzzy'],
'baseurlfuzzy' => ['baseurl', 'fuzzy'],
);
public static $HasCreated = true;
public static $HasUpdated = true;
public static $HasSearch  = true;
public $templatename = null;
private $_class;
private $_method;
private $_params;
private $_view;
private static $_RewriteCache = null;
private static $_FuzzyCache = null;
public function  __construct() {
$this->_linked = array(
'Insertable' => array(
'link' => Model::LINK_HASMANY,
'on' => 'baseurl'
),
'PageMeta' => array(
'link' => Model::LINK_HASMANY,
'on' => array('site' => 'site', 'baseurl' => 'baseurl'),
),
'RewriteMap' => array(
'link' => Model::LINK_HASMANY,
'on' => array('site' => 'site', 'baseurl' => 'baseurl', 'fuzzy' => 'fuzzy'),
)
);
if(func_num_args() == 1){
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$site = MultiSiteHelper::GetCurrentSiteID();
}
else{
$site = null;
}
$key = func_get_arg(0);
parent::__construct($site, $key);
$this->load();
}
elseif(func_num_args() == 2){
$site = func_get_arg(0);
$key  = func_get_arg(1);
parent::__construct($site, $key);
}
else{
parent::__construct();
}
}
public function getControllerClass() {
if (!$this->_class) {
$a = PageModel::SplitBaseURL($this->get('baseurl'));
$this->_class = ($a) ? $a['controller'] : null;
}
return $this->_class;
}
public function getControllerMethod() {
if (!$this->_method) {
$a = PageModel::SplitBaseURL($this->get('baseurl'));
$this->_method = ($a) ? $a['method'] : null;
}
return $this->_method;
}
public function getParameters() {
if (!$this->_params) {
$a = PageModel::SplitBaseURL($this->get('baseurl'));
$this->_params = ($a) ? $a['parameters'] : array();
}
return $this->_params;
}
public function getParameter($key) {
$p = $this->getParameters();
return (array_key_exists($key, $p)) ? $p[$key] : null;
}
public function setParameter($key, $val) {
$this->_params[$key] = $val;
}
public function validateRewriteURL($v) {
if (!$v) return true;
if ($v == $this->_data['baseurl']) return true;
if ($v{0} != '/') return "Rewrite URL must start with a '/'";
if(strpos($v, '#') !== false){
return 'Invalid Rewrite URL, cannot contain a pound sign (#).';
}
$controller = substr($v, 1, ( (strpos($v, '/', 1) !== false) ? strpos($v, '/', 1) : strlen($v)) );
if($controller && class_exists($controller . 'Controller')){
return 'Invalid Rewrite URL, "' . $controller . '" is a reserved system name!';
}
$ds = Core\Datamodel\Dataset::Init()
->table('page')
->select('*')
->whereGroup('OR', 'baseurl = ' . $v, 'rewriteurl = ' . $v);
if ($this->exists()) $ds->where('baseurl != ' . $this->_data['baseurl']);
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$ds->whereGroup('OR', 'site = -1', 'site = ' . MultiSiteHelper::GetCurrentSiteID());
}
$ds->execute();
if ($ds->num_rows > 0) {
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
foreach($ds as $row){
if($row['site'] == $this->get('site') || $row['site'] == '-1'){
return 'Rewrite URL already taken';
}
}
}
else{
return 'Rewrite URL already taken';
}
}
return true;
}
public function getBaseTemplateName(){
$t = 'pages/';
$c = $this->getControllerClass();
if (strlen($c) - strrpos($c, 'Controller') == 10) {
$c = substr($c, 0, -10);
}
$t .= $c . '/';
$t .= $this->getControllerMethod() . '.tpl';
return strtolower($t);
}
public function getTemplateName() {
if($this->get('last_template')){
return $this->get('last_template');
}
$t = $this->getBaseTemplateName();
if (($override = $this->get('page_template'))){
$t = substr($t, 0, -4) . '/' . $override;
}
return $t;
}
public function getView() {
if (!$this->_view) {
$this->_view = new View();
$this->_populateView();
}
return $this->_view;
}
public function hijackView(View $view) {
$this->_view = $view;
$this->_populateView();
}
public function getMetasArray() {
$fullmetas = array(
'title' => array(
'title'       => 'Search-Optimized Title',
'description' => 'If a value is entered here, the &lt;title&gt; tag of the page will be replaced with this value.  Useful for making the page more indexable by search bots.',
'type'        => 'text',
'value'       => (($meta = $this->getMeta('title')) ? $meta->get('meta_value_title') : null),
),
'image' => array(
'title'       => 'Image',
'description' => 'Optional image to showcase this page',
'type'        => 'file',
'basedir'     => 'public/page/image/',
'value'       => (($meta = $this->getMeta('image')) ? $meta->get('meta_value_title') : null),
),
'author' => array(
'title'       => 'Author',
'description' => 'Completely optional, but feel free to include it if relevant',
'type'        => 'pagemetaauthor',
'value'       => (($meta = $this->getMeta('author')) ? $meta->get('meta_value_title') : null),
),
'authorid' => array(
'type'        => 'hidden',
'value'       => (($meta = $this->getMeta('author')) ? $meta->get('meta_value') : null),
),
'keywords' => array(
'title'       => 'Keywords',
'description' => 'Provides taxonomy data for this page, separate different keywords with a comma.',
'type'        => 'pagemetakeywords',
'model'       => $this,
),
'description' => array(
'title'       => 'Description/Teaser',
'description' => 'Teaser text that displays on search engine and social network preview links',
'type'        => 'textarea',
'value'       => (($meta = $this->getMeta('description')) ? $meta->get('meta_value_title') : null),
)
);
return $fullmetas;
}
public function getMeta($name) {
$metas = $this->getLink('PageMeta');
if($name == 'keywords'){
$keywords = array();
foreach($metas as $meta){
if($meta->get('meta_key') == 'keyword') $keywords[] = $meta;
}
return $keywords;
}
else{
foreach($metas as $meta){
if($meta->get('meta_key') == $name) return $meta;
}
}
return null;
}
public function getMetaValue($name){
$m = $this->getMeta($name);
return $m ? $m->get('meta_value_title') : '';
}
public function set($k, $v){
if($k == 'site'){
$insertables = $this->getLink('Insertable');
foreach($insertables as $ins){
$ins->set('site', $v);
}
return parent::set($k, $v);
}
else{
return parent::set($k, $v);
}
}
public function setMetas($metaarray) {
if (is_array($metaarray) && count($metaarray)){
foreach($metaarray as $k => $v){
$this->setMeta($k, $v);
}
return true;
}
return false;
}
public function setMeta($name, $value) {
$metas = $this->getLink('PageMeta');
if($name == 'keywords'){
if(!is_array($value)) $value = array($value => $value);
foreach($value as $valueidx => $valueval){
if(is_numeric($valueidx)){
unset($value[$valueidx]);
$value[ \Core\str_to_url($valueval) ] = $valueval;
}
}
foreach($metas as $idx => $meta){
if($meta->get('meta_key') != 'keyword') continue;
if(isset($value[ $meta->get('meta_value') ])){
$meta->set('meta_value_title', $value[ $meta->get('meta_value') ]);
unset($value[ $meta->get('meta_value') ]);
}
else{
$this->deleteLink($meta);
}
}
foreach($value as $metavalue => $metavaluetitle){
if(!$metavaluetitle) continue;
$meta = new PageMetaModel($this->get('site'), $this->get('baseurl'), 'keyword', $metavalue);
$meta->set('meta_value_title', $metavaluetitle);
$this->setLink('PageMeta', $meta);
}
}
elseif($name == 'authorid'){
foreach($metas as $idx => $meta){
if($meta->get('meta_key') != 'author') continue;
$meta->set('meta_value', $value);
return; // :)
}
$meta = new PageMetaModel($this->get('baseurl'), 'author', $value);
$this->setLink('PageMeta', $meta);
}
else{
foreach($metas as $idx => $meta){
if($meta->get('meta_key') != $name) continue;
if($value){
$meta->set('meta_value_title', $value);
}
else{
$this->deleteLink($meta);
}
return; // :)
}
if($value !== null){
$meta = new PageMetaModel($this->get('site'), $this->get('baseurl'), $name, '');
$meta->set('meta_value_title', $value);
$this->setLink('PageMeta', $meta);
}
}
}
public function setInsertable($name, $value){
$insertables = $this->getLink('Insertable');
foreach($insertables as $ins){
if($ins->get('name') == $name){
$ins->set('site', $this->get('site'));
$ins->set('value', $value);
return; // :)
}
}
$ins = new InsertableModel($this->get('site'), $this->get('baseurl'), $name);
$ins->set('value', $value);
$this->setLink('Insertable', $ins);
}
public function getRewriteURLs(){
$rewrites = $this->getLink('RewriteMap');
$out = '';
foreach($rewrites as $r){
$v = $r->get('rewriteurl');
if($v{0} == '/') $out .= ROOT_URL . substr($v, 1) . "\n";
else $out .= $v . "\n";
}
return trim($out);
}
public function setRewriteURLs($urls){
if(!is_array($urls)){
$string = $urls;
$urls = array();
$string = str_replace([',', '|', "\r"], "\n", $string);
$urls = array_map('trim', explode("\n", $string));
}
foreach($urls as $k => $v){
if(!$v){
unset($urls[$k]);
}
elseif(strpos($v, ROOT_URL_NOSSL) === 0){
$urls[$k] = '/' . substr($v, strlen(ROOT_URL_NOSSL));
}
elseif(strpos($v, ROOT_URL_SSL) === 0){
$urls[$k] = '/' . substr($v, strlen(ROOT_URL_SSL));
}
elseif(strpos($v, '://') !== false){
$v = substr($v, strpos($v, '://') + 3);
$urls[$k] = substr($v, strpos($v, '/'));
}
else{
if($v{0} != '/'){
$urls[$k] = '/' . $v;
}
}
}
$rewrites = $this->getLink('RewriteMap');
foreach($rewrites as $rewrite){
if(!in_array($rewrite->get('rewriteurl'), $urls)){
$this->deleteLink($rewrite);
}
}
foreach($urls as $url){
$rewrite = RewriteMapModel::Find(['rewriteurl = ' . $url], 1);
if(!$rewrite){
$rewrite = new RewriteMapModel();
$rewrite->set('rewriteurl', $url);
}
$this->setLink('RewriteMap', $rewrite);
}
}
public function setFromForm(Form $form, $prefix = null){
$this->getLink('Insertable');
$this->getLink('PageMeta');
parent::setFromForm($form, $prefix);
if($form->getElement($prefix . '[rewrites]')){
$rewrites = $form->getElement($prefix . '[rewrites]')->get('value');
$this->setRewriteURLs($rewrites);
}
$baselen = strlen($prefix . '[metas]');
foreach($form->getElements(true, false) as $el){
$name = $el->get('name');
if(strpos($name, $prefix . '[metas]') === 0){
$key = substr($name, $baselen+1, -1);
$this->setMeta($key, $el->get('value'));
}
}
$baselen = strlen($prefix . '[insertables]');
foreach($form->getElements(true, false) as $el){
$name = $el->get('name');
if(strpos($name, $prefix . '[insertables]') === 0){
$key = substr($name, $baselen+1, -1);
$this->setInsertable($key, $el->get('value'));
}
}
}
public function setToFormElement($key, FormElement $element){
if($key == 'page_template'){
$element->set('templatename', $this->getBaseTemplateName());
}
}
public function addToFormPost(Form $form, $prefix){
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled() && \Core\user()->checkAccess('g:admin')){
$form->switchElementType($prefix . '[site]', 'select');
$el = $form->getElement($prefix . '[site]');
$opts = [
'-1' => 'Global Page',
'0'  => 'Root-Only Page',
];
$fac = MultiSiteModel::FindRaw([], null, null);
foreach($fac as $dat){
if($dat['status'] != 'active'){
continue;
}
$opts[ $dat['id'] ] = $dat['name'] . ' (' . $dat['id'] . ')';
}
$el->set( 'options', $opts );
}
$metasgroupname = 'Meta Information & URL (SEO)';
$insertablesgroupname = 'Basic';
$metasgroup = $form->getElement($metasgroupname);
if(!$metasgroup){
$metasgroup = new FormGroup(array('title' => $metasgroupname, 'name' => $metasgroupname));
$form->addElement($metasgroup);
}
$insertablesgroup = $form->getElement($insertablesgroupname);
if(!$insertablesgroup){
$insertablesgroup = new FormGroup(array('title' => $insertablesgroupname, 'name' => $insertablesgroupname));
$form->addElement($insertablesgroup);
}
$metasgroup->addElement(
'textarea',
[
'name' => $prefix . '[rewrites]',
'title' => 'Rewrite Aliases',
'value' => $this->getRewriteURLs(),
'description' => 'Enter rewrite aliases that point to this page, one per line.  You may use the fully resolved path or simply the part after the ".com".',
]
);
foreach($this->getMetasArray() as $key => $dat){
$type = $dat['type'];
$dat['name'] = $prefix . '[metas][' . $key . ']';
$metasgroup->addElement($type, $dat);
}
$tpl = Core\Templates\Template::Factory($this->getTemplateName());
if($tpl){
foreach($tpl->getInsertables() as $key => $dat){
$type = $dat['type'];
$dat['name'] = $prefix . '[insertables][' . $key . ']';
$i = InsertableModel::Construct($this->get('site'), $this->get('baseurl'), $key);
if ($i->get('value') !== null){
$dat['value'] = $i->get('value');
}
$dat['class'] = 'insertable';
$insertablesgroup->addElement($type, $dat);
}
}
}
public function getResolvedURL() {
if(strpos($this->get('baseurl'), '://') !== false){
return $this->get('baseurl');
}
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
if($this->get('site') == -1){
$base = ROOT_URL;
}
elseif($this->get('site') != MultiSiteHelper::GetCurrentSiteID()){
$site = MultiSiteModel::Construct($this->get('site'));
$base = 'http://' . $site->get('url') . '/';
}
else{
$base = ROOT_URL;
}
}
else{
$base = ROOT_URL;
}
if ($this->exists()) {
return $base . substr($this->get('rewriteurl'), 1);
}
else {
$s = self::SplitBaseURL($this->get('baseurl'));
return $base . substr($s['baseurl'], 1);
}
}
public function execute() {
$transport = $this->getView();
$c = $this->getControllerClass();
$m = $this->getControllerMethod();
if (!($c && $m)) {
$transport->error = View::ERROR_NOTFOUND;
return $transport;
}
if ($c::$AccessString !== null) {
$transport->access = $c::$AccessString;
if (!Core::User()->checkAccess($c::$AccessString)) {
$transport->error = View::ERROR_ACCESSDENIED;
return $transport;
}
}
if ($this->exists()) {
$transport->title  = $this->get('title');
$transport->access = $this->get('access');
}
$r = call_user_func(array($c, $m), $transport);
if ($r === null) {
$r = $transport;
}
elseif (is_numeric($r)) {
$transport->error = $r;
}
if ($transport->error == View::ERROR_NOERROR && $this->exists()) {
$this->set('title', $transport->title);
$this->set('access', $transport->access);
$this->save();
}
return $transport;
}
public function save() {
if (!$this->get('rewriteurl')) $this->set('rewriteurl', $this->get('baseurl'));
if(!isset($this->_datainit['rewriteurl'])) $this->_datainit['rewriteurl'] = null;
if($this->_data['rewriteurl'] != $this->_datainit['rewriteurl']){
self::$_FuzzyCache = null;
self::$_RewriteCache = null;
}
if($this->exists() && $this->_data['rewriteurl'] != $this->_datainit['rewriteurl']){
$map = new RewriteMapModel($this->_datainit['rewriteurl']);
$map->set('site', $this->_data['site']);
$map->set('baseurl', $this->_data['baseurl']);
$map->set('fuzzy', $this->_data['fuzzy']);
$map->save();
}
if($this->get('published_status') == 'published' && !$this->get('published')){
$this->set('published', \Core\Date\DateTime::NowGMT());
}
elseif($this->get('published_status') == 'draft'){
$this->set('published', 0);
}
$this->set('popularity', $this->getPopularityScore());
return parent::save();
}
public function getParent(){
if(!$this->exists()){
return null;
}
$tree = $this->getParentTree();
if(!sizeof($tree)){
return null;
}
$last = sizeof($tree) - 1;
return $tree[$last];
}
public function getParentTree() {
if (!$this->exists()) {
$m               = strtolower($this->getControllerMethod());
$b               = strtolower($this->get('baseurl'));
$controllerclass = $this->getControllerClass();
$hasview         = method_exists($controllerclass, 'view');
$hasadmin        = method_exists($controllerclass, 'admin');
if (
($m == 'edit' || $m == 'update' || $m == 'delete') && $hasview
) {
$altbaseurl = str_replace('/' . $m . '/', '/view/', $b);
$p = PageModel::Construct($altbaseurl);
if ($p->exists() && \Core\user()->checkAccess($p->get('access'))) {
return array_merge($p->getParentTree(), array($p));
}
elseif(!$p->exists() && Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$p = PageModel::Construct(-1, $altbaseurl);
if ($p->exists() && \Core\user()->checkAccess($p->get('access'))) {
return array_merge($p->getParentTree(), array($p));
}
}
}
if(
($m == 'create' || $m == 'update' || $m == 'edit' || $m == 'delete') && $hasadmin
){
$parentb = strpos($b, '/' . $m) ? substr($b, 0, strpos($b, '/' . $m)) : $b;
$parentb .= '/admin';
$p = PageModel::Construct($parentb);
if ($p->exists() && \Core\user()->checkAccess($p->get('access'))) {
return array_merge($p->getParentTree(), array($p));
}
elseif(!$p->exists() && Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$p = PageModel::Construct(-1, $parentb);
if ($p->exists() && \Core\user()->checkAccess($p->get('access'))) {
return array_merge($p->getParentTree(), array($p));
}
}
}
}
$ret = array();
foreach ($this->_getParentTree() as $p) {
if ($p->exists() || $p->get('title')) {
$ret[] = $p;
}
}
return $ret;
}
public function getPopularityScore(){
$score = $this->get('pageviews');
$created = $this->get('published');
if(!$created){
return 0.000;
}
if(!$this->get('indexable')){
return 0.000;
}
if($score == 0){
return 0.000;
}
$order = log10($score);
$seconds = time() - $created;
$months = $seconds / SECONDS_TWO_MONTH;
$months = max($months, 0.5);
$long_number = $order - $months;
$long_number += 10;
return round($long_number, 5);
}
public function getSEOTitle(){
$metatitle = $this->getMeta('title');
$title = $this->get('title');
$config = \ConfigHandler::Get('/core/page/title_template');
if($metatitle && $metatitle->get('meta_value_title')){
$t = $metatitle->get('meta_value_title');
}
elseif($config){
$t = $this->_parseTemplateString($config);
}
else{
$t = $title;
}
if(ConfigHandler::Get('/core/page/title_remove_stop_words')){
$stopwords = \Core\get_stop_words();
$exploded = explode(' ', $t);
$nt = '';
foreach($exploded as $w){
$lw = strtolower($w);
if(!in_array($lw, $stopwords)){
$nt .= ' ' . $w;
}
}
$t = trim($nt);
}
return $t;
}
public function getTeaser($require_something = false){
$meta = $this->getMeta('description');
$config = \ConfigHandler::Get('/core/page/teaser_template');
if($meta){
return $meta->get('meta_value_title');
}
elseif($config){
return $this->_parseTemplateString($config);
}
elseif($require_something){
return substr(strip_tags($this->get('body')), 0, 150);
}
else{
return '';
}
}
public function getImage(){
$meta = $this->getMeta('image');
if(!$meta){
return null;
}
$file = $meta->get('meta_value_title');
if(!$file){
return null;
}
$f = \Core\Filestore\Factory::File($file);
return $f;
}
public function getAuthor(){
$meta = $this->getMeta('author');
if(!$meta) return null;
$uid = $meta->get('meta_value');
if(!$uid) return null;
$u = UserModel::Construct($uid);
return $u;
}
public function getIndexCacheKey(){
return 'page-cache-index-' . $this->get('site') . '-' . md5($this->get('baseurl'));
}
public function purgePageCache(){
$indexkey = $this->getIndexCacheKey();
$index = \Core\Cache::Get($indexkey);
if($index && is_array($index)){
foreach($index as $key){
\Core\Cache::Delete($key);
}
}
\Core\Cache::Delete($indexkey);
}
public function getPublishedStatus(){
if($this->get('published_status') == 'draft'){
return 'draft';
}
if($this->get('published') > \Core\Date\DateTime::NowGMT()){
return 'pending';
}
if($this->get('published_expires') && $this->get('published_expires') <= \Core\Date\DateTime::NowGMT()){
return 'expired';
}
return 'published';
}
public function isPublished(){
return ($this->getPublishedStatus() == 'published');
}
private function _getParentTree($antiinfiniteloopcounter = 5) {
if ($antiinfiniteloopcounter <= 0) return array();
$p = false;
if (!$this->exists()) {
self::_LookupUrl('/');
$url = strtolower($this->get('baseurl'));
do {
$url = substr($url, 0, strrpos($url, '/'));
$lookup = self::_LookupUrl($url);
if($lookup['found']){
$url = $lookup['url'];
}
$p = PageModel::Construct($url);
return array_merge($p->_getParentTree(--$antiinfiniteloopcounter), array($p));
}
while ($url);
}
if (!$this->get('parenturl') && $this->get('admin') && strtolower($this->get('baseurl')) != '/admin') {
$url = '/admin';
if (isset(self::$_RewriteCache[$url])) {
$p = PageModel::Construct($url);
}
return $p ? array($p) : array();
}
if (!$this->get('parenturl')) return array();
$p = PageModel::Construct($this->get('parenturl'));
return array_merge($p->_getParentTree(--$antiinfiniteloopcounter), array($p));
}
private function _populateView() {
$this->_view->error = View::ERROR_NOERROR;
$this->_view->baseurl = $this->get('baseurl');
$this->_view->setParameters($this->getParameters());
$this->_view->templatename = $this->getTemplateName();
$this->_view->mastertemplate = ($this->get('template')) ? $this->get('template') : ConfigHandler::Get('/theme/default_template');
$this->_view->setBreadcrumbs($this->getParentTree());
}
private function _parseTemplateString($string_template){
$parent = $this->getParent();
$metadescription = $this->getMeta('description');
$bodysnippet = substr(strip_tags($this->get('body')), 0, 150);
$author = $this->getAuthor();
$rep = [
'%%date%%' => \Core\Date\DateTime::FormatString($this->get('published'), \Core\Date\DateTime::SHORTDATE),
'%%title%%' => $this->get('title'),
'%%parent_title%%' => ($parent ? $parent->get('title') : ''),
'%%sitename%%' => SITENAME,
'%%excerpt%%' => ($metadescription ? $metadescription->get('meta_value_title') : $bodysnippet),
'%%tag%%' => '',
'%%searchphrase%%' => '',
'%%modified%%' => \Core\Date\DateTime::FormatString($this->get('updated'), \Core\Date\DateTime::SHORTDATE),
'%%name%%' => ($author ? $author->getDisplayName() : ''),
'%%currenttime%%' => \Core\Date\DateTime::Now(\Core\Date\DateTime::TIME),
'%%currentdate%%' => \Core\Date\DateTime::Now(\Core\Date\DateTime::SHORTDATE),
'%%currentday%%' => \Core\Date\DateTime::Now('d'),
'%%currentmonth%%' => \Core\Date\DateTime::Now('m'),
'%%currentyear%%' => \Core\Date\DateTime::Now('Y'),
'%%page%%' => '1', // @todo Support for this.
'%%pagetotal%%' => '1', // @todo Support for this.
];
return str_ireplace(array_keys($rep), array_values($rep), $string_template);
}
public static function SplitBaseURL($base, $site = null) {
if (!$base) return null;
$args = null;
$argstring = '';
if (($qpos = strpos($base, '?')) !== false) {
$argstring = urldecode(substr($base, $qpos + 1));
preg_match_all('/([^=&]*)={0,1}([^&]*)/', $argstring, $matches);
$args = array();
foreach ($matches[1] as $idx => $v) {
if (!$v) continue;
$a =& $args;
while(($paranpos = strpos($v, '[')) !== false){
$k1 = substr($v, 0, $paranpos);
$v = substr($v, $paranpos+1, strpos($v, ']')-$paranpos-1);
if(!isset($a[$k1])){
$a[$k1] = [];
}
$a =& $a[$k1];
}
$a[$v] = $matches[2][$idx];
}
$base = substr($base, 0, $qpos);
}
$ext = 'html';
$posofdot = strpos($base, '.');
if($posofdot){
$ext  = substr($base, $posofdot+1);
$base = substr($base, 0, $posofdot);
}
$ctype = \Core\Filestore\extension_to_mimetype($ext);
if(!$ctype){
$ctype = 'text/html';
}
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
if($site === null){
$site = MultiSiteHelper::GetCurrentSiteID();
}
}
else{
$site = null;
}
$lookup = self::_LookupUrl($base, $site);
if($lookup['found']){
$base = $lookup['url'];
}
else{
$try = $lookup['url'];
if($site === null){
$tries = [];
foreach(self::$_FuzzyCache as $dat){
$tries = array_merge($tries, $dat);
}
}
elseif(isset(self::$_FuzzyCache[$site])){
$tries = array_merge(self::$_FuzzyCache['_GLOBAL_'], self::$_FuzzyCache[$site]);
}
else{
$tries = self::$_FuzzyCache['_GLOBAL_'];
}
while($try != '' && $try != '/') {
if(isset($tries[$try])) {
$base = $tries[$try] . substr($base, strlen($try));
break;
}
elseif(in_array($try, $tries)) {
$base = $tries[array_search($try, $tries)] . substr($base, strlen($try));
break;
}
$try = substr($try, 0, strrpos($try, '/'));
}
}
$base = trim($base, '/');
$posofslash = strpos($base, '/');
if ($posofslash){
$controller = substr($base, 0, $posofslash);
$base = substr($base, $posofslash+1);
}
else{
$controller = $base;
$base = false;
}
if (class_exists($controller . 'Controller')) {
switch (true) {
case is_subclass_of($controller . 'Controller', 'Controller_2_1'):
case is_subclass_of($controller . 'Controller', 'Controller'):
$controller = $controller . 'Controller';
break;
default:
return null;
}
}
elseif (class_exists($controller)) {
if(!
(is_subclass_of($controller, 'Controller_2_1') || is_subclass_of($controller, 'Controller'))
){
return null;
}
}
else {
return null;
}
if ($base) {
$posofslash = strpos($base, '/');
if ($posofslash) {
$method = str_replace('/', '_', $base);
while (!method_exists($controller, $method) && strpos($method, '_')) {
$method = substr($method, 0, strrpos($method, '_'));
}
}
else {
$method = $base;
}
$base = substr($base, strlen($method) + 1);
}
else {
$method = 'index';
}
if (!method_exists($controller, $method)) {
return null;
}
if ($method{0} == '_') return null;
$params = ($base !== false) ? explode('/', $base) : null;
$baseurl = '/' . ((strpos($controller, 'Controller') == strlen($controller) - 10) ? substr($controller, 0, -10) : $controller);
if (!($method == 'index' && !$params)) $baseurl .= '/' . str_replace('_', '/', $method);
$baseurl .= ($params) ? '/' . implode('/', $params) : '';
$rewriteurl = self::_LookupReverseUrl($baseurl, $site);
if($ext != 'html'){
$rewriteurl .= '.' . $ext;
}
if ($args) {
$rewriteurl .= '?' . $argstring;
if ($params) $params = array_merge($params, $args);
else $params = $args;
}
if($site === null){
$rooturl = ROOT_URL;
}
else{
$rooturl = MultiSiteModel::Construct($site)->getResolvedURL();
}
$fullurl = trim($rooturl, '/') . '/' . trim($rewriteurl, '/');
return array(
'controller' => $controller,
'method'     => $method,
'parameters' => $params,
'rooturl'    => $rooturl,
'baseurl'    => $baseurl,
'rewriteurl' => $rewriteurl,
'ctype'      => $ctype,
'extension'  => $ext,
'fullurl'    => $fullurl,
);
}
private static function _LookupUrl($url = null, $site = null) {
if (self::$_RewriteCache === null) {
$s = new Core\Datamodel\Dataset();
$s->select('site, rewriteurl, baseurl, fuzzy');
$s->table('page');
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
if($site === null){
$site = MultiSiteHelper::GetCurrentSiteID();
}
}
else{
$site = null;
}
$rs = $s->execute();
self::$_RewriteCache = array();
self::$_FuzzyCache = array();
foreach ($rs as $row) {
$rewrite = strtolower($row['rewriteurl']);
$base    = strtolower($row['baseurl']);
$siteid  = ($row['site'] == -1) ? '_GLOBAL_' : $row['site'];
if(!isset(self::$_RewriteCache[$siteid])){
self::$_RewriteCache[$siteid] = [];
}
if(!isset(self::$_FuzzyCache[$siteid])){
self::$_FuzzyCache[$siteid] = [];
}
self::$_RewriteCache[$siteid][$rewrite] = $base;
if ($row['fuzzy']){
self::$_FuzzyCache[$siteid][$rewrite] = $base;
}
}
}
if ($url === null){
return null;
}
$url = strtolower($url);
if($site === null){
foreach(self::$_RewriteCache as $set){
if(isset($set[$url])){
return [
'found' => true,
'url' => $set[$url],
];
}
}
}
else{
if(isset(self::$_RewriteCache[$site]) && isset(self::$_RewriteCache[$site][$url])){
return [
'found' => true,
'url' => self::$_RewriteCache[$site][$url],
];
}
elseif(isset(self::$_RewriteCache['_GLOBAL_']) && isset(self::$_RewriteCache['_GLOBAL_'][$url])){
return [
'found' => true,
'url' => self::$_RewriteCache['_GLOBAL_'][$url],
];
}
}
return [
'found' => false,
'url' => $url,
];
}
private static function _LookupReverseUrl($url, $site = null) {
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
if($site === null){
$site = MultiSiteHelper::GetCurrentSiteID();
}
}
else{
$site = null;
}
self::_LookupUrl(null);
if($site === null){
foreach(self::$_RewriteCache as $set){
if(($key = array_search($url, $set)) !== false){
return $key;
}
}
}
else{
if(isset(self::$_RewriteCache[$site])){
if(($key = array_search($url, self::$_RewriteCache[$site])) !== false){
return $key;
}
}
if(($key = array_search($url, self::$_RewriteCache['_GLOBAL_'])) !== false){
return $key;
}
}
$try = $url;
if($site === null){
$tries = [];
foreach(self::$_FuzzyCache as $dat){
$tries = array_merge($tries, $dat);
}
}
else{
$tries = array_merge(self::$_FuzzyCache['_GLOBAL_'], self::$_FuzzyCache[$site]);
}
while($try != '' && $try != '/') {
if(isset($tries[$try])) {
$url = $tries[$try] . substr($url, strlen($try));
break;
}
elseif(in_array($try, $tries)) {
$url = array_search($try, $tries) . substr($url, strlen($try));
break;
}
$try = substr($try, 0, strrpos($try, '/'));
}
return $url;
}
public static function GetPagesAsOptions($where = false, $blanktext = false) {
if ($where instanceof ModelFactory) {
$f = $where;
}
elseif (!$where) {
$f = new ModelFactory('PageModel');
}
else {
$f = new ModelFactory('PageModel');
$f->where($where);
}
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$g = new Core\Datamodel\DatasetWhereClause();
$g->setSeparator('OR');
$g->addWhere('site = -1');
$g->addWhere('site = ' . MultiSiteHelper::GetCurrentSiteID());
$f->where($g);
}
$f->where('selectable = 1');
$pages = $f->get();
$opts = array();
foreach ($pages as $p) {
$baseurl = strtolower($p->get('baseurl'));
$t = '';
foreach ($p->getParentTree() as $subp) {
$t .= $subp->get('title') . ' &raquo; ';
}
$t .= $p->get('title');
$t .= ' ( ' . $p->get('rewriteurl') . ' )';
$tlen = strlen(html_entity_decode($t));
if($tlen > 80){
$t = substr($t, 0, (77 + (strlen($t) - $tlen)) ) . '&hellip;';
}
$opts[$baseurl] = $t;
}
asort($opts);
if ($blanktext) $opts = array_merge(array("" => $blanktext), $opts);
return $opts;
}
public static function PopularityMassUpdateHook(){
$pages = PageModel::Find();
foreach($pages as $page){
$page->save();
}
return true;
}
}


### REQUIRE_ONCE FROM core/models/SessionModel.class.php
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
),
'external_data' => array(
'type' => Model::ATT_TYPE_DATA,
'comment' => 'JSON-encoded array of any external data set onto this session.',
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
public function get($k) {
if ($k == 'data') {
return $this->getData();
} else {
return parent::get($k);
}
}
public function set($k, $v) {
if ($k == 'data') {
$this->setData($v);
} else {
parent::set($k, $v);
}
}
public function getData() {
$data     = $this->_data['data'];
if(!$data) return array();
$unzipped = gzuncompress($data);
if ($unzipped === false) {
return $data;
}
else {
return $unzipped;
}
}
public function getExternalData(){
$ext = $this->get('external_data');
if($ext == '') return [];
$json = json_decode($ext, true);
if(!$json) return [];
return $json;
}
public function setData($data) {
$zipped              = gzcompress($data);
$this->_data['data'] = $zipped;
$this->_dirty = true;
}
public function setExternalData($data){
if(!is_array($data)){
$this->set('external_data', null);
}
elseif(!sizeof($data)){
$this->set('external_data', null);
}
else{
$this->set('external_data', json_encode($data));
}
}
}


### REQUIRE_ONCE FROM core/models/PageMetaModel.class.php
class PageMetaModel extends Model {
public static $Schema = array(
'site' => array(
'type' => Model::ATT_TYPE_SITE,
'default' => -1,
'formtype' => 'system',
'comment' => 'The site id in multisite mode, (or -1 if global)',
),
'baseurl' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'required' => true,
'null' => false,
'form' => array('type' => 'system'),
),
'meta_key' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 24,
'required' => true,
'comment' => 'The key of this meta tag',
),
'meta_value' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'required' => true,
'comment' => 'Machine version of the value of this meta tag',
),
'meta_value_title' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 256,
'required' => true,
'comment' => 'Human readable version of the value of this meta tag',
),
);
public static $Indexes = array(
'primary' => array('site', 'baseurl', 'meta_key', 'meta_value'),
);
public function  __construct() {
$this->_linked = array(
'Page' => array(
'link' => Model::LINK_BELONGSTOONE,
'on' => 'baseurl',
),
);
if(func_num_args() == 3){
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$site = MultiSiteHelper::GetCurrentSiteID();
}
else{
$site = null;
}
$key1 = func_get_arg(0);
$key2 = func_get_arg(1);
$key3 = func_get_arg(2);
parent::__construct($site, $key1, $key2, $key3);
$this->load();
}
elseif(func_num_args() == 4){
$site = func_get_arg(0);
$key1 = func_get_arg(1);
$key2 = func_get_arg(2);
$key3 = func_get_arg(3);
parent::__construct($site, $key1, $key2, $key3);
}
else{
parent::__construct();
}
}
public function getViewMetaObject(){
$m = ViewMeta::Factory($this->get('meta_key'));
$m->contentkey = $this->get('meta_value');
$m->content = $this->get('meta_value_title');
return $m;
}
}


### REQUIRE_ONCE FROM core/models/Insertable.class.php
class InsertableModel extends Model {
public static $Schema = array(
'site' => array(
'type' => Model::ATT_TYPE_SITE,
'formtype' => 'system',
),
'baseurl' => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'required'  => true,
'null'      => false,
),
'name'    => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'required'  => true,
'null'      => false,
),
'value'   => array(
'type' => Model::ATT_TYPE_TEXT,
'null' => false,
),
);
public static $Indexes = array(
'primary' => array('site', 'baseurl', 'name'),
);
public function __construct(){
if(func_num_args() == 2){
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$site = MultiSiteHelper::GetCurrentSiteID();
}
else{
$site = 0;
}
$baseurl = func_get_arg(0);
$name = func_get_arg(1);
parent::__construct($site, $baseurl, $name);
}
elseif(func_num_args() == 3){
$site = func_get_arg(0);
$baseurl = func_get_arg(1);
$name  = func_get_arg(2);
parent::__construct($site, $baseurl, $name);
}
else{
parent::__construct();
}
}
} // END class InsertableModel extends Model


### REQUIRE_ONCE FROM core/models/SystemLogModel.php
class SystemLogModel extends Model {
const TYPE_SECURITY = 'security';
const TYPE_ERROR    = 'error';
const TYPE_INFO     = 'info';
public $_ua = null;
public static $Schema = array(
'id' => array(
'type' => Model::ATT_TYPE_UUID,
),
'datetime' => array(
'type' => Model::ATT_TYPE_CREATED
),
'type' => array(
'type' => Model::ATT_TYPE_ENUM,
'options' => array('security', 'error', 'info'),
'default' => 'info',
),
'session_id' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 160,
),
'user_id'    => array(
'type'    => Model::ATT_TYPE_UUID_FK,
'default' => 0,
),
'ip_addr'    => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 39,
),
'useragent' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 128
),
'affected_user_id'    => array(
'type'    => Model::ATT_TYPE_UUID_FK,
'default' => null,
'null'    => true,
'comment' => 'If this action potentially affects a user, list the ID here.'
),
'code' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 64,
'comment' => 'A short phrase or code for this log event, used by sorting'
),
'message' => array(
'type' => Model::ATT_TYPE_STRING,
'maxlength' => 512,
'comment' => 'Any primary message that goes with this log event',
),
'details' => array(
'type' => Model::ATT_TYPE_TEXT,
'comment' => 'Any details or backtrace that needs to accompany this log event',
),
);
public static $Indexes = array(
'primary'       => ['id'],
'datetime'      => ['datetime'],
'user'          => ['user_id'],
'affected_user' => ['affected_user_id'],
'code'          => ['code']
);
public function isBot(){
switch($this->getUserAgent()->type){
case 'Robot':
case 'Offline Browser':
case 'Other':
return true;
default:
return false;
}
}
public function getUserAgent(){
if($this->_ua === null){
$this->_ua = new \Core\UserAgent($this->get('useragent'));
}
return $this->_ua;
}
public function save(){
$isnew = !$this->exists();
$ret = parent::save();
if(!$ret) return $ret;
if(!$isnew) return $ret;
if(
($this->get('type') == 'error' || $this->get('type') == 'security') &&
$this->get('details')
){
Core\Utilities\Logger\append_to($this->get('type'), $this->get('message') . "\n" . $this->get('details'), $this->get('code'));
}
else{
Core\Utilities\Logger\append_to($this->get('type'), $this->get('message'), $this->get('code'));
}
}
public static function LogSecurityEvent($code, $message = '', $details = '', $affected_user = null) {
try{
$log = self::Factory();
$log->set('type', 'security');
$log->set('code', $code);
$log->set('message', $message);
$log->set('details', $details);
$log->set('affected_user_id', $affected_user);
$log->save();
}
catch(Exception $e){
error_log($code . ': ' . $message);
error_log('ADDITIONALLY, ' . $e->getMessage());
}
}
public static function LogErrorEvent($code, $message, $details = '') {
try{
$log = self::Factory();
$log->set('type', 'error');
$log->set('code', $code);
$log->set('message', $message);
$log->set('details', $details);
$log->save();
}
catch(Exception $e){
error_log($code . ': ' . $message);
error_log('ADDITIONALLY, ' . $e->getMessage());
}
}
public static function LogInfoEvent($code, $message, $details = '') {
try{
$log = self::Factory();
$log->set('type', 'info');
$log->set('code', $code);
$log->set('message', $message);
$log->set('details', $details);
$log->save();
}
catch(Exception $e){
error_log($code . ': ' . $message);
error_log('ADDITIONALLY, ' . $e->getMessage());
}
}
public static function Factory() {
$log = new self();
$log->setFromArray(
array(
'session_id' => session_id(),
'user_id' => (\Core\user() ? \Core\user()->get('id') : 0),
'ip_addr' => REMOTE_IP,
'useragent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
)
);
return $log;
}
}


### REQUIRE_ONCE FROM core/models/UserModel.php
class UserModel extends Model {
public static $Schema = [
'id'                   => [
'type'     => Model::ATT_TYPE_UUID,
'required' => true,
'null'     => false,
],
'email'                => [
'type'       => Model::ATT_TYPE_STRING,
'maxlength'  => 64,
'null'       => false,
'validation' => ['this', 'validateEmail'],
'required'   => true,
],
'backend'              => [
'type'     => Model::ATT_TYPE_STRING,
'formtype' => 'hidden',
'default'  => 'datastore',
'comment'  => 'Pipe-delimited list of authentication drivers on this user'
],
'password'             => [
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 60,
'null'      => false,
],
'apikey'               => [
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 64,
'null'      => false,
],
'active'               => [
'type'    => Model::ATT_TYPE_ENUM,
'default' => '1',
'options' => ['-1', '0', '1'],
'null'    => false,
'form'    => [
'title'   => 'User Status',
'options' => [
'-1' => 'Disabled',
'0'  => 'Not Activated Yet',
'1'  => 'Active',
],
],
],
'admin'                => [
'type'    => Model::ATT_TYPE_BOOL,
'default' => '0',
'null'    => false,
],
'avatar'               => [
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => '64',
'form'      => [
'type'    => 'file',
'accept'  => 'image/*',
'basedir' => 'public/user/avatar',
],
],
'registration_ip'      => [
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => '24',
'comment'   => 'The original IP of the user registration',
],
'registration_source'  => [
'type'    => Model::ATT_TYPE_STRING,
'default' => 'self',
'comment' => 'The source of the user registration, either self, admin, or other.'
],
'registration_invitee' => [
'type'    => Model::ATT_TYPE_UUID_FK,
'comment' => 'If invited/created by a user, this is the ID of that user.',
],
'last_login'           => [
'type'    => Model::ATT_TYPE_INT,
'default' => 0,
'comment' => 'The timestamp of the last login of this user',
],
'last_password'        => [
'type'    => Model::ATT_TYPE_INT,
'default' => 0,
'comment' => 'The timestamp of the last password reset of this user',
],
];
public static $Indexes = [
'primary'      => ['id'],
'unique:email' => ['email'],
];
public static $HasSearch = true;
public static $HasCreated = true;
public static $HasUpdated = true;
protected $_accessstringchecks = [];
protected $_resolvedpermissions = null;
protected $_configs = null;
protected $_authdriver = [];
public function __construct($id = null) {
$this->_linked['UserUserConfig'] = [
'link' => Model::LINK_HASMANY,
'on'   => ['user_id' => 'id'],
];
$this->_linked['UserUserGroup']  = [
'link' => Model::LINK_HASMANY,
'on'   => ['user_id' => 'id'],
];
parent::__construct($id);
}
public function get($key) {
if(array_key_exists($key, $this->_data)) {
return parent::get($key);
}
elseif(($c = $this->getConfigObject($key)) !== null) {
return $c->get('value');
}
elseif(array_key_exists($key, $this->_dataother)) {
return $this->_dataother[ $key ];
}
else {
return null;
}
}
public function getLabel() {
if(!$this->exists()) {
return ConfigHandler::Get('/user/displayname/anonymous');
}
$displayas = ConfigHandler::Get('/user/displayas');
switch($displayas) {
case 'username':
return $this->get('username');
case 'firstname':
return $this->get('first_name');
case 'emailfull':
return $this->get('email');
case 'emailbase':
default:
return strstr($this->get('email'), '@', true);
}
}
public function getDisplayName() {
return $this->getLabel();
}
public function getConfigs() {
if($this->_configs === null) {
$this->_configs = [];
$uucrecords     = $this->getLink('UserUserConfig');
$fac = UserConfigModel::Find();
foreach($fac as $f) {
$key     = $f->get('key');
$default = $f->get('default_value');
foreach($uucrecords as $uuc) {
if($uuc->get('key') == $key) {
$this->_configs[ $key ] = $uuc;
continue 2;
}
}
try {
$uuc = new UserUserConfigModel($this->get('id'), $key);
$uuc->set('value', $default);
$this->setLink('UserUserConfig', $uuc);
$this->_configs[ $key ] = $uuc;
}
catch(Exception $e) {
trigger_error('Invalid UserConfig [' . $f->get('key') . '], ' . $e->getMessage(), E_USER_NOTICE);
}
}
}
$ret = [];
foreach($this->_configs as $k => $obj) {
$ret[ $k ] = $obj->get('value');
}
return $ret;
}
public function getConfigObject($key) {
$this->getConfigs();
return (isset($this->_configs[ $key ])) ? $this->_configs[ $key ] : null;
}
public function getConfigObjects() {
$this->getConfigs();
return $this->_configs;
}
public function getGroups() {
$out  = [];
$uugs = $this->getLink('UserUserGroup');
foreach($uugs as $uug) {
if($uug->get('context')) continue;
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()) {
$g = $uug->getLink('UserGroup');
if($g->get('site') == MultiSiteHelper::GetCurrentSiteID()) {
$out[] = $g->get('id');
}
}
else {
$out[] = $uug->get('group_id');
}
}
return $out;
}
public function getContextGroups($context = null, $return_objects = false) {
$out  = [];
$uugs = $this->getLink('UserUserGroup');
if($context && $context instanceof Model) {
$contextname = substr(get_class($context), 0, -5);
$contextpk   = $context->getPrimaryKeyString();
}
elseif(is_scalar($context)) {
$contextname = $context;
$contextpk   = null;
}
else {
$contextname = null;
$contextpk   = null;
}
foreach($uugs as $uug) {
if(!$uug->get('context')) continue;
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()) {
$g     = $uug->getLink('UserGroup');
$gsite = $g->get('site');
if(!($gsite == '-1' || $gsite == MultiSiteHelper::GetCurrentSiteID())
) {
continue;
}
}
if($contextname && $uug->get('context') != $contextname) continue;
if($contextpk && $uug->get('context_pk') != $contextpk) continue;
if($return_objects) {
$out[] = $uug;
}
else {
$out[] = [
'group_id'   => $uug->get('group_id'),
'context'    => $uug->get('context'),
'context_pk' => $uug->get('context_pk'),
];
}
}
return $out;
}
public function getAuthDriver($driver = null) {
$enabled = explode('|', $this->get('backend'));
if(!sizeof($enabled)) {
throw new Exception('There are no enabled authentication drivers for this user!');
}
if(!$driver) {
$driver = $enabled[0];
}
elseif(!in_array($driver, $enabled)) {
throw new Exception('The ' . $driver . ' authentication driver is not enabled for this user!');
}
if(!isset($this->_authdriver[ $driver ])) {
if(!isset(\Core\User\Helper::$AuthDrivers[ $driver ])) {
throw new Exception('Invalid auth backend for user, ' . $driver . '.  Auth driver is not registered.');
}
$classname = \Core\User\Helper::$AuthDrivers[ $driver ];
if(!class_exists($classname)) {
throw new Exception(
'Invalid auth backend for user, ' . $driver . '.  Auth driver class was not found.'
);
}
$ref                          = new ReflectionClass($classname);
$this->_authdriver[ $driver ] = $ref->newInstance($this);
}
return $this->_authdriver[ $driver ];
}
public function getEnabledAuthDrivers() {
$enabled = explode('|', $this->get('backend'));
$ret     = [];
foreach($enabled as $name) {
try {
$ret[] = $this->getAuthDriver($name);
}
catch(Exception $e) {
}
}
return $ret;
}
public function enableAuthDriver($driver) {
$enabled = explode('|', $this->get('backend'));
$drivers = User\Helper::GetEnabledAuthDrivers();
if(!isset($drivers[ $driver ])) {
return false;
}
if(in_array($driver, $enabled)) {
return false;
}
$enabled[] = $driver;
$this->set('backend', implode('|', $enabled));
return true;
}
public function disableAuthDriver($driver) {
$enabled = explode('|', $this->get('backend'));
$drivers = User\Helper::GetEnabledAuthDrivers();
if(!isset($drivers[ $driver ])) {
return false;
}
if(!in_array($driver, $enabled)) {
return false;
}
unset($enabled[ array_search($driver, $enabled) ]);
if(sizeof($enabled) == 0) {
$enabled = ['datastore'];
}
$this->set('backend', implode('|', $enabled));
return true;
}
public function getSearchIndexString() {
$strs = [];
$strs[] = $this->get('email');
$opts = UserConfigModel::Find();
foreach($opts as $uc) {
if($uc->get('searchable')) {
$val = $this->get($uc->get('key'));
if(preg_match('/^[0-9\- \.\(\)]*$/', $val) && trim($val) != ''){
$val = preg_replace('/[ \-\.\(\)]/', '', $val);
}
if($val){
$strs[] = $val;
}
}
}
return implode(' ', $strs);
}
public function validateEmail($email) {
if($email == $this->get('email')) {
return true;
}
if(!Core::CheckEmailValidity($email)) {
return 'Does not appear to be a valid email address';
}
if(UserModel::Find(['email' => $email], 1)) {
return 'Requested email is already registered';
}
return true;
}
public function isActive() {
if(!$this->exists()) {
return false;
}
elseif($this->get('active') == 1) {
return true;
}
else {
return false;
}
}
public function set($k, $v) {
if(array_key_exists($k, $this->_data)) {
return parent::set($k, $v);
}
elseif(($c = $this->getConfigObject($k)) !== null) {
return $c->set('value', $v);
}
else {
$this->_dataother[ $k ] = $v;
return true;
}
}
public function setGroups($groups) {
if(!is_array($groups)) $groups = [];
$this->_setGroups($groups, false);
}
public function setContextGroups($groups, $context = null) {
if(!is_array($groups)) $groups = [];
$this->_setGroups($groups, $context === null ? true : $context);
}
public function setFromForm(Form $form, $prefix = null) {
foreach($form->getElements() as $el) {
$name  = $el->get('name');
$value = $el->get('value');
if($prefix && strpos($name, $prefix . '[') !== 0) continue;
if($prefix) {
if(strpos($name, '][')) {
$name = str_replace('][', '[', substr($name, strlen($prefix) + 1));
}
else {
$name = substr($name, strlen($prefix) + 1, -1);
}
}
if($name == 'email') {
$this->set('email', $value);
}
elseif(strpos($name, 'option[') === 0) {
$k   = substr($name, 7, -1);
$obj = $this->getConfigObject($k);
if($obj === null){
continue;
}
$obj = $obj->getLink('UserConfig');
if($value === null && $obj->get('formtype') == 'checkbox') {
$value = 0;
}
if($el instanceof FormFileInput) {
$value = 'public/user/config/' . $value;
}
$this->set($k, $value);
}
elseif($name == 'groups[]') {
$this->setGroups($value);
}
elseif($name == 'active') {
$this->set('active', $value ? 1 : 0);
}
elseif($name == 'admin') {
$this->set('admin', $value);
}
elseif($name == 'avatar') {
$this->set('avatar', $value);
}
elseif($name == 'contextgroup[]') {
$gids       = $value;
$contextpks = $form->getElement('contextgroupcontext[]')->get('value');
$groups     = [];
foreach($gids as $key => $gid) {
if(!$gid) continue;
$group = UserGroupModel::Construct($gid);
$context   = $group->get('context');
$contextpk = $contextpks[ $key ];
$groups[] = [
'group_id'   => $gid,
'context'    => $context,
'context_pk' => $contextpk,
];
}
$this->setContextGroups($groups);
}
else {
}
} // foreach(elements)
}
public function save() {
if(!$this->_data['apikey']) {
$this->generateNewApiKey();
}
$status = parent::save();
HookHandler::DispatchHook('/user/postsave', $this);
return $status;
}
public function generateNewApiKey() {
$this->set('apikey', Core::RandomHex(64, true));
}
public function clearAccessStringCache() {
$this->_accessstringchecks  = [];
$this->_resolvedpermissions = null;
}
public function checkAccess($accessstring, $context = null) {
$findkey = $accessstring . '-' . $this->_getContextKey($context);
if(isset($this->_accessstringchecks[ $findkey ])) {
return $this->_accessstringchecks[ $findkey ];
}
$default = false;
$loggedin = $this->exists();
$isadmin  = $this->get('admin');
$cache    =& $this->_accessstringchecks[ $findkey ];
$isactive = $this->isActive();
$accessstring = strtolower($accessstring);
if($isadmin && strpos($accessstring, 'g:!admin') === false) {
$cache = true;
return true;
}
$parts = array_map('trim', explode(';', $accessstring));
foreach($parts as $p) {
if($p == '') continue;
if($p == '*' || $p == '!*') {
$type = '*';
$dat  = $p;
}
else {
list($type, $dat) = array_map('trim', explode(':', $p));
}
if($dat{0} == '!') {
$ret = false;
$dat = substr($dat, 1);
}
elseif($type{0} == '!') {
$ret  = false;
$type = substr($type, 1);
}
else {
$ret = true;
}
if($type == '*') {
$default = $ret;
continue;
}
elseif($type == 'g' && $dat == 'anonymous') {
if(!$loggedin) {
$cache = $ret;
return $ret;
}
}
elseif($type == 'g' && $dat == 'authenticated') {
if($loggedin && $isactive) {
$cache = $ret;
return $ret;
}
}
elseif($type == 'g' && $dat == 'admin') {
if($isadmin) {
$cache = $ret;
return $ret;
}
}
elseif($type == 'g') {
if(in_array($dat, $this->getGroups())) {
$cache = $ret;
return $ret;
}
}
elseif($type == 'p') {
if(in_array($dat, $this->_getResolvedPermissions($context))) {
$cache = $ret;
return $ret;
}
}
elseif($type == 'u') {
var_dump($type, $dat, $ret);
die('@todo Finish the user lookup logic in User::checkAccess()');
}
else {
var_dump($type, $dat, $ret);
die('Implement that access string check!');
}
}
$cache = $default;
return $default;
}
public function getControlLinks(){
$a = array();
$userid      = $this->get('id');
$usersudo    = \Core\user()->checkAccess('p:/user/users/sudo');
$usermanager = \Core\user()->checkAccess('p:/user/users/manage');
$selfaccount = \Core\user()->get('id') == $userid;
if($usersudo && !$selfaccount){
$a[] = array(
'title' => 'Switch To User',
'icon' => 'bullseye',
'link' => '/user/sudo/' . $userid,
'confirm' => 'By switching, (or SUDOing), to a user, you inherit that user permissions.',
);
}
if($usermanager){
$a[] = array(
'title' => t('STRING_VIEW'),
'icon' => 'view',
'link' => '/user/view/' . $userid,
);
}
elseif($selfaccount){
$a[] = array(
'title' => t('STRING_VIEW'),
'icon' => 'view',
'link' => '/user/me',
);
}
if($usermanager || $selfaccount){
$a[] = array(
'title' => t('STRING_EDIT'),
'icon' => 'edit',
'link' => '/user/edit/' . $userid,
);
$a[] = array(
'title' => 'Public Profiles',
'icon' => 'link',
'link' => '/user/connectedprofiles/' . $userid,
);
if(!$selfaccount){
$a[] = array(
'title' => 'Delete',
'icon' => 'remove',
'link' => '/user/delete/' . $userid,
'confirm' => 'Are you sure you want to delete user ' . $this->getDisplayName() . '?',
);
}
}
return $a;
}
protected function _getResolvedPermissions($context = null) {
if(!$this->isActive()) {
return [];
}
$findkey = $this->_getContextKey($context);
if($this->_resolvedpermissions === null) {
$this->_resolvedpermissions = [];
foreach($this->getLink('UserUserGroup') as $uug) {
$key = $uug->get('context') ? $uug->get('context') . ':' . $uug->get('context_pk') : '';
if(!isset($this->_resolvedpermissions[ $key ])) {
$this->_resolvedpermissions[ $key ] = [];
}
$group = $uug->getLink('UserGroup');
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()) {
if(!($group->get('site') == -1 || $group->get('site') == MultiSiteHelper::GetCurrentSiteID())) {
continue;
}
}
$this->_resolvedpermissions[ $key ] =
array_merge($this->_resolvedpermissions[ $key ], $group->getPermissions());
}
}
return isset($this->_resolvedpermissions[ $findkey ]) ? $this->_resolvedpermissions[ $findkey ] : [];
}
protected function _setGroups($groups, $context) {
foreach($groups as $key => $data) {
if(!is_array($data)) {
$groups[ $key ] = [
'group_id'   => $data,
'context'    => '',
'context_pk' => '',
];
}
}
if($context === false) {
$contextname = null;
$contextpk   = null;
}
elseif($context === true) {
$contextname = null;
$contextpk   = null;
}
elseif($context instanceof Model) {
$contextname = substr(get_class($context), 0, -5);
$contextpk   = $context->getPrimaryKeyString();
$context     = true;
}
elseif(is_scalar($context)) {
$contextname = $context;
$contextpk   = null;
$context     = true;
}
else {
throw new Exception('If a context is provided, please ensure it is either a model or model name');
}
$uugs = $this->getLink('UserUserGroup');
foreach($uugs as $uug) {
if($context && !$uug->get('context')) {
continue;
}
elseif(!$context && $uug->get('context')) {
continue;
}
elseif($context && $contextname && $uug->get('context') != $contextname) {
continue;
}
elseif($context && $contextpk && $uug->get('context_pk') != $contextpk) {
continue;
}
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()) {
$ugsite = $uug->getLink('UserGroup')->get('site');
if(!($ugsite == -1 || $ugsite == MultiSiteHelper::GetCurrentSiteID())) {
continue;
}
}
$gid        = $uug->get('group_id');
$gcontext   = $uug->get('context');
$gcontextpk = $uug->get('context_pk');
foreach($groups as $key => $data) {
if($data['group_id'] == $gid && $data['context'] == $gcontext && $data['context_pk'] == $gcontextpk
) {
unset($groups[ $key ]);
continue 2;
}
}
$this->deleteLink($uug);
}
foreach($groups as $data) {
$this->setLink(
'UserUserGroup', new UserUserGroupModel(
$this->get('id'), $data['group_id'], $data['context'], $data['context_pk']
)
);
}
$this->clearAccessStringCache();
}
protected function _getContextKey($context) {
if($context === null || $context === '') {
return '';
}
elseif($context instanceof Model) {
return substr(get_class($context), 0, -5) . ':' . $context->getPrimaryKeyString();
}
else {
throw new Exception('Invalid context provided for _getResolvedPermissions!');
}
}
public static function Search($query, $where = []) {
$ret          = [];
$schema       = self::GetSchema();
$configwheres = [];
$ref = new ReflectionClass(get_called_class());
if(!$ref->getProperty('HasSearch')->getValue()) {
return $ret;
}
$fac = new ModelFactory(get_called_class());
if(sizeof($where)) {
$clause = new \Core\Datamodel\DatasetWhereClause();
$clause->addWhere($where);
foreach($clause->getStatements() as $statement) {
if(isset($schema[ $statement->field ])) {
$fac->where($statement);
}
else {
$configwheres[] = $statement;
}
}
}
if($ref->getProperty('HasDeleted')->getValue()) {
$fac->where('deleted = 0');
}
$fac->where(\Core\Search\Helper::GetWhereClause($query));
foreach($fac->get() as $m) {
$add = true;
foreach($configwheres as $statement) {
if(($config = $m->getConfigObject($statement->field))) {
switch($statement->op) {
case '=':
if($config->get('value') != $statement->value) {
$add = false;
break 2;
}
break;
default:
}
}
}
if($add) {
$sr = new \Core\Search\ModelResult($query, $m);
if($sr->relevancy < 1) continue;
$sr->title = $m->getLabel();
$sr->link  = $m->get('baseurl');
$ret[] = $sr;
}
}
return $ret;
}
public static function Import($data, $options, $output_realtime = false) {
$log = new \Core\ModelImportLogger('User Importer', $output_realtime);
$merge = isset($options['merge']) ? $options['merge'] : true;
$pk    = isset($options['key']) ? $options['key'] : null;
if(!$pk) {
throw new Exception(
'Import requires a "key" field on options containing the primary key to compare against locally.'
);
}
$defaultgroups = \UserGroupModel::Find(["default = 1"]);
$groups        = [];
$gnames        = [];
foreach($defaultgroups as $g) {
$groups[] = $g->get('id');
$gnames[] = $g->get('name');
}
if(sizeof($groups)) {
$log->log('Found ' . sizeof($groups) . ' default groups for new users: ' . implode(', ', $gnames));
}
else {
$log->log('No groups set as default, new users will not belong to any groups.');
}
$users = [];
foreach($data as $dat) {
if($pk == 'email' || $pk == 'id') {
$user = UserModel::Find([$pk . ' = ' . $dat[ $pk ]], 1);
}
else {
$uucm = UserUserConfigModel::Find(['key = ' . $pk, 'value = ' . $dat[ $pk ]], 1);
if($uucm) {
$user = $uucm->getLink('UserModel');
}
else {
$user = UserModel::Find(['email = ' . $dat['email']], 1);
}
}
$status_type = $user ? 'Updated' : 'Created';
if($user && !$merge) {
$log->duplicate('Skipped user ' . $user->getLabel() . ', already exists and merge not requested');
continue;
}
if(!$user) {
if(!isset($dat['email'])) {
$log->error('Unable to import user without an email address!');
continue;
}
if(!isset($dat['registration_ip'])) {
$dat['registration_ip'] = REMOTE_IP;
}
if(!isset($dat['registration_source'])) {
$dat['registration_source'] = \Core\user()->exists() ? 'admin' : 'self';
}
if(!isset($dat['registration_invitee'])) {
$dat['registration_invitee'] = \Core\user()->get('id');
}
$user = new UserModel();
}
if(isset($dat['avatar']) && strpos($dat['avatar'], '://') !== false) {
$log->actionStart('Downloading ' . $dat['avatar']);
$f    = new \Core\Filestore\Backends\FileRemote($dat['avatar']);
$dest = \Core\Filestore\Factory::File('public/user/avatar/' . $f->getBaseFilename());
if($dest->identicalTo($f)) {
$log->actionSkipped();
}
else {
$f->copyTo($dest);
$dat['avatar'] = 'public/user/avatar/' . $dest->getBaseFilename();
$log->actionSuccess();
}
}
if(isset($dat['profiles']) && is_array($dat['profiles'])) {
$new_profiles = $dat['profiles'];
$profiles = $user->get('json:profiles');
if($profiles) {
$current_flat = [];
$profiles     = json_decode($profiles, true);
foreach($profiles as $current_profile) {
$current_flat[] = $current_profile['url'];
}
foreach($new_profiles as $new_profile) {
if(!in_array($new_profile['url'], $current_flat)) {
$profiles[] = $new_profile;
}
}
unset($new_profile, $new_profiles, $current_flat, $current_profile);
}
else {
$profiles = $new_profiles;
unset($new_profiles);
}
unset($dat['profiles']);
$dat['json:profiles'] = json_encode($profiles);
}
$save = $user->isnew();
foreach($dat as $k => $v){
if($k == 'created' || $k == 'updated'){
continue;
}
if($user->get($k) != $v){
$save = true;
break;
}
}
if($save){
try {
$user->setFromArray($dat);
$user->setGroups($groups);
$user->save();
$status = true;
}
catch(Exception $e) {
$log->error($e->getMessage());
continue;
}
}
else{
$status = false;
}
if($status) {
$log->success($status_type . ' user ' . $user->getLabel() . ' successfully!');
}
else {
$log->skip('Skipped user ' . $user->getLabel() . ', no changes detected.');
}
}
$log->finalize();
return $log;
}
}


### REQUIRE_ONCE FROM core/models/ConfigModel.class.php
class ConfigModel extends Model {
public static $Schema = array(
'key'           => [
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 255,
'required'  => true,
'null'      => false,
],
'type'          => [
'type'    => Model::ATT_TYPE_ENUM,
'options' => ['string', 'text', 'int', 'boolean', 'enum', 'set'],
'default' => 'string',
'null'    => false,
],
'encrypted' => [
'type' => Model::ATT_TYPE_BOOL,
'default' => 0,
],
'component'    => [
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 48,
'required'  => false,
'default'   => '',
'null'      => false,
'form' => array(
'type' => 'disabled',
),
'comment'   => 'The component that registered this config, useful for uninstalling and cleanups',
],
'default_value' => [
'type'    => Model::ATT_TYPE_TEXT,
'default' => null,
'null'    => true,
],
'value'         => [
'type'    => Model::ATT_TYPE_TEXT,
'default' => null,
'null'    => true,
],
'options'       => [
'type'      => Model::ATT_TYPE_TEXT,
'default'   => null,
'null'      => true,
],
'title'         => [
'type'    => Model::ATT_TYPE_STRING,
'comment' => 'The title from the config parameter, optional',
],
'description'   => [
'type'    => Model::ATT_TYPE_TEXT,
'default' => null,
'null'    => true,
],
'mapto'         => [
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 32,
'default'   => null,
'comment'   => 'The define constant to map the value to on system load.',
'null'      => true,
],
'overrideable' => [
'type' => Model::ATT_TYPE_BOOL,
'default' => false,
'comment' => 'If children sites can override this configuration option',
],
'form_attributes' => [
'type' => Model::ATT_TYPE_TEXT,
'comment' => 'Set from the content of the form-attributes XML parameter.',
],
'created'       => [
'type' => Model::ATT_TYPE_CREATED
],
'updated'       => [
'type' => Model::ATT_TYPE_UPDATED
]
);
public static $Indexes = array(
'primary' => array('key'),
);
public function getValue() {
$v = $this->get('value');
if ($v === null){
$v = $this->get('default');
}
elseif($this->get('encrypted') && $v !== ''){
preg_match('/^\$([^$]*)\$([0-9]*)\$(.*)$/m', $v, $matches);
$cipher = $matches[1];
$passes = $matches[2];
$size = openssl_cipher_iv_length($cipher);
$dec = substr($v, strlen($cipher) + 5, 0-$size);
$iv = substr($v, 0-$size);
for($i=0; $i<$passes; $i++){
$dec = openssl_decrypt($dec, $cipher, SECRET_ENCRYPTION_PASSPHRASE, true, $iv);
}
$v = $dec;
}
return self::TranslateValue($this->get('type'), $v);
}
public function setValue($value){
if($this->get('encrypted')){
$cipher = 'AES-256-CBC';
$passes = 10;
$size = openssl_cipher_iv_length($cipher);
$iv = mcrypt_create_iv($size, MCRYPT_RAND);
$enc = $value;
for($i=0; $i<$passes; $i++){
$enc = openssl_encrypt($enc, $cipher, SECRET_ENCRYPTION_PASSPHRASE, true, $iv);
}
$payload = '$' . $cipher . '$' . str_pad($passes, 2, '0', STR_PAD_LEFT) . '$' . $enc . $iv;
return parent::set('value', $payload);
}
else{
return parent::set('value', $value);
}
}
public static function TranslateValue($type, $value){
switch ($type) {
case 'int':
return (int)$value;
case 'boolean':
return ($value == '1' || $value == 'true') ? true : false;
case 'set':
return array_map('trim', explode('|', $value));
default:
return $value;
}
}
public function getFormAttributes(){
$opts = [];
$formOptions = $this->get('form_attributes');
if($formOptions != ''){
$formOptions = array_map('trim', explode(';', $formOptions));
foreach($formOptions as $o){
if(($cpos = strpos($o, ':')) !== false){
$k = substr($o, 0, $cpos);
$v = substr($o, $cpos+1);
$opts[$k] = $v;
}
}
}
if(!isset($opts['type'])){
switch ($this->get('type')) {
case 'string':
case 'int':
$opts['type'] = 'text';
break;
case 'text':
$opts['type'] = 'textarea';
break;
case 'enum':
$opts['type'] = 'select';
break;
case 'boolean':
$opts['type'] = 'radio';
break;
case 'set':
$opts['type'] ='checkboxes';
break;
default:
$opts['type'] = 'text';
break;
}
}
if(($opts['type'] == 'select' || $opts['type'] == 'checkboxes') && trim($this->get('options'))){
$opts['options'] =  array_map('trim', explode('|', $this->get('options')));
}
if($opts['type'] == 'radio'){
$opts['options'] = ['false' => t('STRING_NO'), 'true'  => t('STRING_YES')];
}
$key = $this->get('key');
$gname = substr($key, 1);
$gname = ucwords(substr($gname, 0, strpos($gname, '/')));
$i18nKey = \Core\i18n\I18NLoader::KeyifyString($key);
$opts['title'] = t('STRING_CONFIG_' . $i18nKey);
$opts['description'] = t('MESSAGE_CONFIG_' . $i18nKey);
if(!isset($opts['title'])){
if($this->get('title')){
$opts['title'] = $this->get('title');
}
else{
$title = substr($key, strlen($gname) + 2);
$title = str_replace('/', ' ', $title);
$title = str_replace('_', ' ', $title);
$title = ucwords($title);
$opts['title'] = $title;
}
}
if(!isset($opts['description'])){
$desc = $this->get('description');
if ($this->get('default_value') && $desc){
$desc .= ' (' . t('MESSAGE_DEFAULT_VALUE_IS_S_', $this->get('default_value')) . ')';
}
elseif($this->get('default_value')) {
$desc = t('MESSAGE_DEFAULT_VALUE_IS_S_', $this->get('default_value'));
}
$opts['description'] = $desc;
}
if(!isset($opts['group'])){
$opts['group'] = $gname;
}
if($opts['type'] == 'checkboxes'){
$opts['name'] = 'config[' . $key . '][]';
}
else{
$opts['name'] = 'config[' . $key . ']';
}
return $opts;
}
public function getAsFormElement(){
$key        = $this->get('key');
$attributes = $this->getFormAttributes();
$val        = \ConfigHandler::Get($key);
$type       = $attributes['type'];
$el         = \FormElement::Factory($type, $attributes);
if($type == 'radio'){
if ($val == '1' || $val == 'true' || $val == 'yes') $val = 'true';
else $val = 'false';
}
if($this->get('type') == 'int' && $type == 'text'){
$el->validation        = '/^[0-9]*$/';
$el->validationmessage = $attributes['group'] . ' - ' . $attributes['title'] . ' expects only whole numbers with no punctuation.';
}
if($type == 'checkboxes' && !is_array($val)){
$val  = array_map('trim', explode('|', $val));
}
$el->set('value', $val);
return $el;
}
public function asFormElement(){
return self::getAsFormElement();
}
} // END class ConfigModel extends Model


### REQUIRE_ONCE FROM core/models/WidgetModel.class.php
class WidgetModel extends Model {
private $_settings = null;
private $_widget;
public static $Schema = array(
'site' => array(
'type' => Model::ATT_TYPE_INT,
'default' => -1,
'formtype' => 'system',
'comment' => 'The site id in multisite mode, (or -1 if global)',
),
'baseurl' => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'required'  => true,
'null'      => false,
'link'      => [
'model' => 'WidgetInstance',
'type'  => Model::LINK_HASMANY,
'on'    => 'baseurl',
],
),
'installable' => array(
'type'    => Model::ATT_TYPE_STRING,
'null'    => false,
'default' => '',
'comment' => 'Baseurl that this widget "plugs" into, if any.',
),
'title'   => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'default'   => null,
'comment'   => '[Cached] Title of the page',
'null'      => true,
),
'settings' => array(
'type' => Model::ATT_TYPE_TEXT,
'formtype' => 'disabled',
'comment' => 'Provides a section for saving json-encoded settings on the widget.'
),
'editurl' => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'default'   => '',
'required'  => false,
'null'      => false,
'comment'   => 'The URL to edit this widget',
),
'deleteurl' => array(
'type'      => Model::ATT_TYPE_STRING,
'maxlength' => 128,
'default'   => '',
'required'  => false,
'null'      => false,
'comment'   => 'The URL to perform the POST on to delete this widget',
),
'created' => array(
'type' => Model::ATT_TYPE_CREATED,
'null' => false,
),
'updated' => array(
'type' => Model::ATT_TYPE_UPDATED,
'null' => false,
),
);
public static $Indexes = array(
'primary' => array('baseurl'),
);
public function getSetting($key){
if($this->_settings === null){
$string = $this->get('settings');
if($string){
$this->_settings = json_decode($this->get('settings'), true);
if(!$this->_settings) $this->_settings = array();
}
else{
$this->_settings = array();
}
}
return (isset($this->_settings[$key])) ? $this->_settings[$key] : null;
}
public function setSetting($key, $value){
$current = $this->getSetting($key);
if($current === $value) return;
$this->_settings[$key] = $value;
$this->set('settings', json_encode($this->_settings));
}
public function getID(){
$split = self::SplitBaseURL($this->get('baseurl'));
if(!$split) return null;
if(isset($split['parameters'][0])) return $split['parameters'][0];
else return null;
}
public function splitParts() {
$ret = WidgetModel::SplitBaseURL($this->get('baseurl'));
if (!$ret) {
$ret = [
'controller' => null,
'method'     => null,
'parameters' => null,
'baseurl'    => null
];
}
if ($ret['parameters'] === null) $ret['parameters'] = [];
return $ret;
}
public function getWidget(){
if($this->_widget === null){
$pagedat = $this->splitParts();
$this->_widget = Widget_2_1::Factory($pagedat['controller']);
if($this->_widget === null){
$this->_widget = false;
}
else{
$this->_widget->_instance = $this;
if($this->get('installable')){
$this->_widget->_installable = $this->get('installable');
}
$this->_widget->_params = $pagedat['parameters'];
}
}
return $this->_widget === false ? null : $this->_widget;
}
public static function SplitBaseURL($base) {
if (!$base) return null;
$base = trim($base, '/');
$args = null;
if (($qpos = strpos($base, '?')) !== false) {
$argstring = substr($base, $qpos + 1);
preg_match_all('/([^=&]*)={0,1}([^&]*)/', $argstring, $matches);
$args = array();
foreach ($matches[1] as $k => $v) {
if (!$v) continue;
$args[$v] = $matches[2][$k];
}
$base = substr($base, 0, $qpos);
}
$posofslash = strpos($base, '/');
if ($posofslash) $controller = substr($base, 0, $posofslash);
else $controller = $base;
if (class_exists($controller . 'Widget')) {
switch (true) {
case is_subclass_of($controller . 'Widget', 'Widget_2_1'):
case is_subclass_of($controller . 'Widget', 'Widget'):
$controller = $controller . 'Widget';
break;
default:
return null;
}
}
elseif (class_exists($controller)) {
if(!
(is_subclass_of($controller, 'Widget_2_1') || is_subclass_of($controller, 'Widget'))
){
return null;
}
}
else {
return null;
}
if ($posofslash !== false) $base = substr($base, $posofslash + 1);
else $base = false;
if ($base) {
$posofslash = strpos($base, '/');
if ($posofslash) {
$method = str_replace('/', '_', $base);
while (!method_exists($controller, $method) && strpos($method, '_')) {
$method = substr($method, 0, strrpos($method, '_'));
}
}
else {
$method = $base;
}
$base = substr($base, strlen($method) + 1);
}
else {
$method = 'index';
}
if (!method_exists($controller, $method)) {
return null;
}
if ($method{0} == '_') return null;
$params = ($base !== false) ? explode('/', $base) : null;
$baseurl = '/' . ((strpos($controller, 'Widget') == strlen($controller) - 6) ? substr($controller, 0, -6) : $controller);
if (!($method == 'index' && !$params)) $baseurl .= '/' . str_replace('_', '/', $method);
$baseurl .= ($params) ? '/' . implode('/', $params) : '';
if($args){
$params = ($params) ? array_merge($params, $args) : $args;
}
return array('controller' => $controller,
'method'     => $method,
'parameters' => $params,
'baseurl'    => $baseurl);
}
}


### REQUIRE_ONCE FROM core/models/UserUserConfigModel.php
class UserUserConfigModel extends Model{
public static $Schema = array(
'user_id' => array(
'type' => Model::ATT_TYPE_UUID_FK,
'required' => true,
'null' => false,
'link' => [
'model' => 'User',
'type' => Model::LINK_BELONGSTOONE,
'on' => 'id',
],
),
'key' => array(
'type' => Model::ATT_TYPE_STRING,
'required' => true,
'null' => false,
'maxlength' => 64,
'link' => [
'model' => 'UserConfig',
'type' => Model::LINK_BELONGSTOONE,
'on' => 'key',
],
),
'value' => array(
'type' => Model::ATT_TYPE_TEXT,
'required' => false,
'null' => true
),
'created' => array(
'type' => Model::ATT_TYPE_CREATED,
'null' => false,
),
'updated' => array(
'type' => Model::ATT_TYPE_UPDATED,
'null' => false,
),
);
public static $Indexes = array(
'primary' => array('user_id', 'key'),
);
public function set($k, $v){
if($k == 'value'){
$config = UserConfigModel::Construct($this->_data['key']);
if(!$config->get('validation')){
return parent::set($k, $v);
}
else{
$check = $config->get('validation');
$valid = true;
if (strpos($check, '::') !== false) {
$ref = new ReflectionClass(substr($check, 0, strpos($check, ':')));
$checklast = substr($check, strrpos($check, ':')+1);
if($ref->hasMethod($checklast)){
$valid = call_user_func($check, $v, $this);
}
elseif($ref->hasProperty($checklast)){
$check = $ref->getProperty($checklast)->getValue();
if (
($check{0} == '/' && !preg_match($check, $v)) ||
($check{0} == '#' && !preg_match($check, $v))
) {
$valid = false;
}
}
}
elseif (
($check{0} == '/' && !preg_match($check, $v)) ||
($check{0} == '#' && !preg_match($check, $v))
) {
$valid = false;
}
if($valid === true){
return parent::set($k, $v);
}
else{
if($valid === false){
$msg = $this->_data['key'] . ' fails validation!';
}
elseif($valid === null){
$msg = $this->_data['key'] . ' fails validation! (no reason given though)';
}
else{
$msg = $valid;
}
throw new ModelValidationException($msg);
}
}
}
else{
return parent::set($k, $v);
}
}
}


### REQUIRE_ONCE FROM core/models/UserConfigModel.php
class UserConfigModel extends Model{
public static $Schema = array(
'key' => array(
'type' => Model::ATT_TYPE_STRING,
'required' => true,
'null' => false,
'maxlength' => 64,
),
'default_name' => array(
'type' => Model::ATT_TYPE_STRING,
'required' => false,
'comment' => 'The default name/title',
),
'name' => array(
'type' => Model::ATT_TYPE_STRING,
'required' => false,
'comment' => 'The name/title displayed on the system',
),
'formtype' => array(
'type' => Model::ATT_TYPE_STRING,
'required' => false,
'default' => 'text'
),
'default_value' => array(
'type' => Model::ATT_TYPE_TEXT
),
'options' => array(
'type' => Model::ATT_TYPE_TEXT,
'required' => false,
'null' => true
),
'default_weight' => array(
'type' => Model::ATT_TYPE_INT,
'default' => 0,
),
'weight' => array(
'type' => Model::ATT_TYPE_INT,
'default' => 0,
),
'default_onregistration' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => true
),
'onregistration' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => true
),
'default_onedit' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => true
),
'onedit' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => true
),
'searchable' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => 0,
),
'required' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => 0,
),
'hidden' => array(
'type' => Model::ATT_TYPE_BOOL,
'default' => false,
'comment' => 'Set to true to make this a hidden value, ie: it will not appear even to super admins.',
),
'validation' => array(
'type' => Model::ATT_TYPE_STRING,
'formtype' => 'disabled',
'comment' => 'Class or function to call on validation',
),
'created' => array(
'type' => Model::ATT_TYPE_CREATED,
'null' => false,
),
'updated' => array(
'type' => Model::ATT_TYPE_UPDATED,
'null' => false,
),
);
public static $Indexes = array(
'primary' => array('key'),
'searchable' => array('searchable'),
);
}


### REQUIRE_ONCE FROM core/libs/core/VersionString.php
} // ENDING GLOBAL NAMESPACE
namespace Core {
class VersionString implements \ArrayAccess {
public $major;
public $minor = 0;
public $point = 0;
public $user;
public $stability;
public $build;
public function __construct($version = null){
if($version){
$this->parseString($version);
}
}
public function __toString(){
$ret = $this->major . '.' . $this->minor . '.' . $this->point;
if($this->stability){
$ret .= $this->stability;
}
if($this->build){
$ret .= '.' . $this->build;
}
if($this->user){
$ret .= '~' . $this->user;
}
return $ret;
}
public function parseString($version) {
$parts = explode('.', strtolower($version));
if(isset($parts[0])){
$this->major = $parts[0];
}
if(isset($parts[1])){
if(is_numeric($parts[1])){
$this->minor = $parts[1];
}
else{
$digit = $parts[1];
if(($pos = strpos($digit, '~')) !== false){
$this->minor = substr($digit, 0, $pos);
$this->user = substr($digit, $pos);
}
elseif(($pos = strpos($digit, 'a')) !== false){
$this->minor = substr($digit, 0, $pos);
$this->stability = substr($digit, $pos);
}
elseif(($pos = strpos($digit, 'b')) !== false){
$this->minor = substr($digit, 0, $pos);
$this->stability = substr($digit, $pos);
}
elseif(($pos = strpos($digit, 'rc')) !== false){
$this->minor = substr($digit, 0, $pos);
$this->stability = substr($digit, $pos);
}
}
}
if(isset($parts[2])){
if(is_numeric($parts[2])){
$this->point = $parts[2];
}
else{
$digit = $parts[2];
if(($pos = strpos($digit, '~')) !== false){
$this->point = substr($digit, 0, $pos);
$this->user = substr($digit, $pos);
}
elseif(($pos = strpos($digit, 'a')) !== false){
$this->point = substr($digit, 0, $pos);
$this->stability = substr($digit, $pos);
}
elseif(($pos = strpos($digit, 'b')) !== false){
$this->point = substr($digit, 0, $pos);
$this->stability = substr($digit, $pos);
}
elseif(($pos = strpos($digit, 'rc')) !== false){
$this->point = substr($digit, 0, $pos);
$this->stability = substr($digit, $pos);
}
}
}
if(isset($parts[3])){
if(is_numeric($parts[3])){
$this->build = $parts[3];
}
else{
$digit = $parts[3];
if(($pos = strpos($digit, '~')) !== false){
$this->build = substr($digit, 0, $pos);
$this->user = substr($digit, $pos);
}
else{
$this->build = $digit;
}
}
}
}
public function setMajor($int){
$this->major = $int;
}
public function setMinor($int){
$this->minor = $int;
}
public function setPoint($int){
$this->point = $int;
}
public function setUser($string){
$this->user = $string;
}
public function setBuild($string){
$this->build = $string;
}
public function setStability($type){
$type = strtolower($type);
switch($type){
case 'dev':
$this->stability = 'dev';
break;
case 'a':
case 'alpha':
$this->stability = 'a';
break;
case 'b':
case 'beta':
$this->stability = 'b';
break;
case 'rc':
$this->stability = 'rc';
break;
default:
$this->stability = null;
break;
}
}
public function compare($other, $operation = null){
if(!$other instanceof VersionString){
$other = new VersionString($other);
}
$v1    = $this->major . '.' . $this->minor . '.' . $this->point;
$v2    = $other->major . '.' . $other->minor . '.' . $other->point;
$check = version_compare($v1, $v2);
if($check == 0 && $this->user && $other->user){
$check = version_compare('1.0' . $this->user, '1.0' . $other->user);
}
if($check == 0 && ($this->stability || $other->stability)){
$check = version_compare('1.0' . $this->stability, '1.0' . $other->stability);
}
if ($operation === null){
return $check;
}
elseif($check == -1){
switch($operation){
case 'lt':
case '<':
case 'le':
case '<=':
return true;
default:
return false;
}
}
elseif($check == 0){
switch($operation){
case 'le':
case '<=':
case 'eq':
case '=':
case '==':
case 'ge':
case '>=':
return true;
default:
return false;
}
}
else{
switch($operation){
case 'ge':
case '>=':
case 'gt':
case '>':
return true;
default:
return false;
}
}
}
public function offsetExists($offset) {
return property_exists($this, $offset);
}
public function offsetGet($offset) {
return $this->$offset;
}
public function offsetSet($offset, $value) {
$this->$offset = $value;
}
public function offsetUnset($offset) {
$this->$offset = null;
}
}
} // ENDING NAMESPACE Core

namespace  {

### REQUIRE_ONCE FROM core/libs/core/Component_2_1.php
use Core\CLI\CLI;
class Component_2_1 {
private $_xmlloader = null;
protected $_name;
protected $_version;
protected $_enabled = false;
protected $_description;
protected $_updateSites = array();
protected $_authors = array();
private $_versionDB = false;
private $_execMode = 'WEB';
private $_file;
private $_permissions = array();
private $_hasview = null;
const ERROR_NOERROR = 0;           // 000000
const ERROR_INVALID = 1;           // 000001
const ERROR_WRONGEXECMODE = 2;     // 000010
const ERROR_MISSINGDEPENDENCY = 4; // 000100
const ERROR_CONFLICT = 8;          // 001000
const ERROR_UPGRADEPATH = 16;      // 010000
public $error = 0;
public $errstrs = array();
private $_loaded = false;
private $_filesloaded = false;
private $_smartyPluginDirectory = null;
private $_viewSearchDirectory = null;
private $_classlist = null;
private $_controllerlist = null;
private $_widgetlist = null;
private $_requires = null;
private $_ready = false;
public function __construct($filename = null) {
$this->_file = \Core\Filestore\Factory::File($filename);
$this->_xmlloader = new XMLLoader();
$this->_xmlloader->setRootName('component');
if (!$this->_xmlloader->loadFromFile($filename)) {
throw new Exception('Parsing of XML Metafile [' . $filename . '] failed, not valid XML.');
}
}
public function load() {
if ($this->_loaded) return;
if (($mode = $this->_xmlloader->getRootDOM()->getAttribute('execmode'))) {
$this->_execMode = strtoupper($mode);
}
$this->_name    = $this->_xmlloader->getRootDOM()->getAttribute('name');
$this->_version = $this->_xmlloader->getRootDOM()->getAttribute("version");
Core\Utilities\Logger\write_debug('Loading metadata for component [' . $this->_name . ']');
$dat = ComponentFactory::_LookupComponentData($this->_name);
if (!$dat) return;
$this->_versionDB = $dat['version'];
$this->_enabled   = ($dat['enabled']) ? true : false;
$this->_loaded    = true;
$this->_permissions = array();
foreach($this->_xmlloader->getElements('/permissions/permission') as $el){
$this->_permissions[$el->getAttribute('key')] = [
'description' => $el->getAttribute('description'),
'context' => ($el->getAttribute('context')) ? $el->getAttribute('context') : '',
];
}
}
public function save($minified = false) {
$this->_xmlloader->setSchema('http://corepl.us/api/2_4/component.dtd');
$this->_xmlloader->getRootDOM()->setAttribute('xmlns:xsi', "http://www.w3.org/2001/XMLSchema-instance");
if(!$this->getSmartyPluginDirectory()){
$this->_xmlloader->removeElements('//smartyplugins');
}
$XMLFilename = $this->_file->getFilename();
if ($minified) {
file_put_contents($XMLFilename, $this->_xmlloader->asMinifiedXML());
}
else {
file_put_contents($XMLFilename, $this->_xmlloader->asPrettyXML());
}
}
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
public function getRequires() {
if($this->_requires === null){
$this->_requires = array();
foreach ($this->_xmlloader->getElements('//component/requires/require') as $r) {
$t  = $r->getAttribute('type');
$n  = $r->getAttribute('name');
$v  = @$r->getAttribute('version');
$op = @$r->getAttribute('operation');
if ($v == '') $v = false;
if ($op == '') $op = 'ge';
$this->_requires[] = array(
'type'      => strtolower($t),
'name'      => strtolower($n),
'version'   => strtolower($v),
'operation' => strtolower($op),
);
}
}
return $this->_requires;
}
public function getDescription() {
if ($this->_description === null) {
$this->_description = trim($this->_xmlloader->getElement('//description')->nodeValue);
}
return $this->_description;
}
public function setDescription($desc) {
$this->_description = $desc;
$this->_xmlloader->getElement('//description')->nodeValue = $desc;
}
public function getPermissions(){
return $this->_permissions;
}
public function getPagesDefined(){
$pages = [];
$node = $this->_xmlloader->getElement('pages');
foreach ($node->getElementsByTagName('page') as $subnode) {
$baseurl = $subnode->getAttribute('baseurl');
$admin   = $subnode->getAttribute('admin');
$group   = ($admin ? $subnode->getAttribute('group') : '');
if(($selectable = $subnode->getAttribute('selectable')) === ''){
$selectable = ($admin ? '0' : '1'); // Defaults
}
if(!($rewriteurl = $subnode->getAttribute('rewriteurl'))){
$rewriteurl = $baseurl;
}
$title = $subnode->getAttribute('title');
$access = $subnode->getAttribute('access');
$parent = $subnode->getAttribute('parenturl');
$pages[$baseurl] = [
'title' => $title,
'group' => $group,
'baseurl' => $baseurl,
'rewriteurl' => $rewriteurl,
'parent' => $parent,
'admin' => $admin,
'selectable' => $selectable,
'access' => $access,
];
}
return $pages;
}
public function setAuthors($authors) {
$this->_xmlloader->removeElements('/authors');
foreach ($authors as $a) {
if (isset($a['email']) && $a['email']) {
$this->_xmlloader->getElement('//component/authors/author[@name="' . $a['name'] . '"][@email="' . $a['email'] . '"]');
}
else {
$this->_xmlloader->getElement('//component/authors/author[@name="' . $a['name'] . '"]');
}
}
}
public function setLicenses($licenses) {
$this->_xmlloader->removeElements('//component/licenses');
$path = '//component/licenses/';
foreach ($licenses as $lic) {
$el = 'license' . ((isset($lic['url']) && $lic['url']) ? '[@url="' . $lic['url'] . '"]' : '');
$l  = $this->_xmlloader->createElement($path . $el, false, 1);
if ($lic['title']) $l->nodeValue = $lic['title'];
}
}
public function loadFiles() {
if(!$this->isInstalled()) return false;
if(!$this->isEnabled()) return false;
if($this->_filesloaded) return true;
Core\Utilities\Logger\write_debug('Loading files for component [' . $this->getName() . ']');
$dir = $this->getBaseDir();
foreach ($this->_xmlloader->getElements('/includes/include') as $f) {
require_once($dir . $f->getAttribute('filename'));
}
foreach ($this->_xmlloader->getElementsByTagName('hookregister') as $h) {
$hook              = new Hook($h->getAttribute('name'));
$hook->description = $h->getAttribute('description');
if($h->getAttribute('return')){
$hook->returnType = $h->getAttribute('return');
}
}
foreach ($this->_xmlloader->getElementsByTagName('hook') as $h) {
$event = $h->getAttribute('name');
$call  = $h->getAttribute('call');
$type  = @$h->getAttribute('type');
HookHandler::AttachToHook($event, $call, $type);
}
foreach ($this->_xmlloader->getElements('/forms/formelement') as $node) {
Form::$Mappings[$node->getAttribute('name')] = $node->getAttribute('class');
}
if(DEVELOPMENT_MODE && defined('AUTO_INSTALL_ASSETS') && AUTO_INSTALL_ASSETS && EXEC_MODE == 'WEB' && CDN_TYPE == 'local'){
Core\Utilities\Logger\write_debug('Auto-installing assets for component [' . $this->getName() . ']');
$this->_parseAssets();
}
$this->_filesloaded = true;
return true;
}
public function _setReady($status = true){
$this->_ready = $status;
}
public function isReady(){
return $this->_ready;
}
public function getLibraryList() {
$libs = array();
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
public function getClassList() {
$dir = $this->getBaseDir();
if($this->_classlist === null){
$this->_classlist = array();
foreach ($this->_xmlloader->getElements('/files/file') as $f) {
$filename = $dir . $f->getAttribute('filename');
foreach ($f->getElementsByTagName('class') as $p) {
$n           = strtolower($p->getAttribute('name'));
$this->_classlist[$n] = $filename;
}
foreach ($f->getElementsByTagName('interface') as $p) {
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
public function getModelList(){
$classes = $this->getClassList();
foreach ($classes as $k => $v) {
if($k == 'model'){
unset($classes[$k]);
}
elseif(strrpos($k, 'model') !== strlen($k) - 5){
unset($classes[$k]);
}
elseif(strpos($k, '\\') !== false){
unset($classes[$k]);
}
}
return $classes;
}
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
public function getControllerList() {
if($this->_controllerlist === null){
$this->_controllerlist = array();
$dir = $this->getBaseDir();
foreach ($this->_xmlloader->getElements('/files/file') as $f) {
$filename = $dir . $f->getAttribute('filename');
foreach ($f->getElementsByTagName('controller') as $p) {
$n           = strtolower($p->getAttribute('name'));
$this->_controllerlist[$n] = $filename;
}
}
}
return $this->_controllerlist;
}
public function getSmartyPluginDirectory() {
if($this->_smartyPluginDirectory === null){
$d = $this->_xmlloader->getElement('/smartyplugins')->getAttribute('directory');
if ($d) $this->_smartyPluginDirectory = $this->getBaseDir() . $d;
else $this->_smartyPluginDirectory = false;
}
return $this->_smartyPluginDirectory;
}
public function getSmartyPlugins(){
$plugins = [];
$node = $this->_xmlloader->getElement('/smartyplugins');
if(!$node) return $plugins;
foreach($node->getElementsByTagName('smartyplugin') as $n){
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
$att = @$this->_xmlloader->getElement('/view')->getAttribute('searchdir');
if ($att) {
$this->_viewSearchDirectory = $this->getBaseDir() . $att . '/';
}
elseif (($att = $this->_xmlloader->getElements('/view/searchdir')->item(0))) {
$this->_viewSearchDirectory = $this->getBaseDir() . $att->getAttribute('dir') . '/';
}
elseif (is_dir($this->getBaseDir() . 'templates')) {
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
if ($this->getName() == 'core') $d = $this->getBaseDir() . 'core/assets/';
else $d = $this->getBaseDir() . 'assets/';
if (is_dir($d)) return $d;
else return null;
}
public function getUserAuthDrivers(){
$ret = [];
$nodes = $this->_xmlloader->getElements('/users/userauth');
foreach($nodes as $n){
$name = $n->getAttribute('name');
$class = $n->getAttribute('class');
$ret[ $name ] = $class;
}
return $ret;
}
public function getIncludePaths() {
return array();
}
public function getDBSchemaTableNames() {
$ret = array();
foreach ($this->_xmlloader->getElement('dbschema')->getElementsByTagName('table') as $table) {
$ret[] = $table->getAttribute('name');
}
return $ret;
}
public function setDBSchemaTableNames($arr) {
$this->_xmlloader->getRootDOM()->removeChild($this->_xmlloader->getElement('/dbschema'));
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
public function getType() {
if ($this->_name == 'core') return 'core';
else return 'component';
}
public function getName() {
return $this->_name;
}
public function getKeyName(){
return str_replace(' ', '-', strtolower($this->_name));
}
public function getVersion() {
return $this->_version;
}
public function setVersion($vers) {
if ($vers == $this->_version) return;
if (($upg = $this->_xmlloader->getElement('/upgrades/upgrade[@from=""][@to=""]', false))) {
$upg->setAttribute('from', $this->_version);
$upg->setAttribute('to', $vers);
}
elseif (($upg = $this->_xmlloader->getElement('/upgrades/upgrade[@from="next"]', false))) {
$upg->setAttribute('from', $this->_version);
$upg->setAttribute('to', $vers);
}
elseif (($upg = $this->_xmlloader->getElement('/upgrades/upgrade[@to="next"]', false))) {
$upg->setAttribute('from', $this->_version);
$upg->setAttribute('to', $vers);
}
elseif (($upg = $this->_xmlloader->getElement('/upgrades/upgrade[@from="' . $this->_version . '"][@to=""]', false))) {
$upg->setAttribute('to', $vers);
}
else {
$this->_xmlloader->getElement('/upgrades/upgrade[@from="' . $this->_version . '"][@to="' . $vers . '"]');
}
$this->_version = $vers;
$this->_xmlloader->getRootDOM()->setAttribute('version', $vers);
}
public function setFiles($files) {
$this->_xmlloader->removeElements('//component/files/file');
$newarray = array();
foreach ($files as $f) {
$newarray[$f['file']] = $f;
}
ksort($newarray);
foreach ($newarray as $f) {
$el = $this->_xmlloader->createElement('//component/files/file[@filename="' . $f['file'] . '"][@md5="' . $f['md5'] . '"]');
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
}
}
public function setAssetFiles($files) {
$this->_xmlloader->removeElements('//component/assets/file');
$newarray = array();
foreach ($files as $f) {
$newarray[$f['file']] = $f;
}
ksort($newarray);
foreach ($newarray as $f) {
$el = $this->_xmlloader->createElement('//component/assets/file[@filename="' . $f['file'] . '"][@md5="' . $f['md5'] . '"]');
}
}
public function setViewFiles($files) {
$this->_xmlloader->removeElements('//component/view/file');
$newarray = array();
foreach ($files as $f) {
$newarray[$f['file']] = $f;
}
ksort($newarray);
foreach ($newarray as $f) {
$el = $this->_xmlloader->createElement('//component/view/file[@filename="' . $f['file'] . '"][@md5="' . $f['md5'] . '"]');
}
}
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
public function isEnabled() {
return ($this->_enabled === true);
}
public function isLoadable() {
if ($this->error & Component_2_1::ERROR_INVALID) {
return false;
}
if($this->_filesloaded) return true;
$this->error   = 0;
$this->errstrs = array();
foreach ($this->getRequires() as $r) {
switch ($r['type']) {
case 'library':
if (!Core::IsLibraryAvailable($r['name'], $r['version'], $r['operation'])) {
$this->error     = $this->error | Component_2_1::ERROR_MISSINGDEPENDENCY;
$this->errstrs[] = 'Requires missing library ' . $r['name'] . ' ' . $r['version'];
}
break;
case 'jslibrary':
if (!Core::IsJSLibraryAvailable($r['name'], $r['version'], $r['operation'])) {
$this->error     = $this->error | Component_2_1::ERROR_MISSINGDEPENDENCY;
$this->errstrs[] = 'Requires missing JSlibrary ' . $r['name'] . ' ' . $r['version'];
}
break;
case 'component':
if (!Core::IsComponentAvailable($r['name'], $r['version'], $r['operation'])) {
$this->error     = $this->error | Component_2_1::ERROR_MISSINGDEPENDENCY;
$this->errstrs[] = 'Requires missing component ' . $r['name'] . ' ' . $r['version'];
}
break;
case 'function':
if(!function_exists($r['name'])){
$this->error     = $this->error | Component_2_1::ERROR_MISSINGDEPENDENCY;
$this->errstrs[] = 'Requires missing function ' . $r['name'];
}
break;
case 'define':
if (!defined($r['name'])) {
$this->error     = $this->error | Component_2_1::ERROR_MISSINGDEPENDENCY;
$this->errstrs[] = 'Requires missing define ' . $r['name'];
}
if ($r['value'] != null && constant($r['name']) != $r['value']) {
$this->error     = $this->error | Component_2_1::ERROR_MISSINGDEPENDENCY;
$this->errstrs[] = 'Requires wrong define ' . $r['name'] . '(' . $r['value'] . ')';
}
break;
}
}
if ($this->error) return false;
$cs = $this->getClassList();
foreach ($cs as $c => $file) {
if (Core::IsClassAvailable($c)) {
$this->error     = $this->error | Component_2_1::ERROR_CONFLICT;
$this->errstrs[] = $c . ' already defined in another component';
break;
}
}
$liblist = $this->getLibraryList();
foreach($liblist as $k => $v){
if(Core::IsLibraryAvailable($k)){
$this->error     = $this->error | Component_2_1::ERROR_CONFLICT;
$this->errstrs[] = 'Library ' . $k . ' already provided by another component!';
break;
}
}
if(!$this->_checkUpgradePath()){
$this->error = $this->error | Component_2_1::ERROR_UPGRADEPATH;
$this->errstrs[] = 'No upgrade path found (' . $this->_versionDB . ' to ' . $this->_version . ')';
}
return (!$this->error) ? true : false;
}
public function getJSLibraries() {
$ret = array();
foreach ($this->_xmlloader->getRootDOM()->getElementsByTagName('jslibrary') as $node) {
$lib       = new JSLibrary();
$lib->name = $node->getAttribute('name');
$lib->version                = (($v = @$node->getAttribute('version')) ? $v : $this->_xmlloader->getRootDOM()->getAttribute('version'));
$lib->baseDirectory          = ROOT_PDIR . 'components/' . $this->getName() . '/';
$lib->DOMNode                = $node;
$ret[strtolower($lib->name)] = $lib;
}
return $ret;
}
public function hasLibrary() {
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
public function install() {
if ($this->isInstalled()) return false;
if (!$this->isLoadable()) return false;
$changes = $this->_performInstall();
$u = $this->_xmlloader->getRootDOM()->getElementsByTagName('install')->item(0);
if($u){
$children = $u->childNodes;
}
else{
$children = [];
}
foreach($children as $child){
switch($child->nodeName){
case 'dataset':
$datachanges = $this->_parseDatasetNode($child);
if($datachanges !== false) $changes = array_merge($changes, $datachanges);
break;
case 'phpfileinclude':
$this->_includeFileForUpgrade(ROOT_PDIR . trim($child->nodeValue));
$changes[] = 'Included custom php file ' . basename($child->nodeValue);
break;
case 'php':
$file = $child->getAttribute('file');
if($file){
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
break;
default:
$changes[] = 'Ignoring unsupported install directive: [' . $child->nodeName . ']';
}
}
if(is_array($changes) && sizeof($changes)){
SystemLogModel::LogInfoEvent('/updater/component/install', 'Component ' . $this->getName() . ' installed successfully!', implode("\n", $changes));
}
$c = new ComponentModel($this->_name);
$c->set('version', $this->_version);
$c->save();
$this->_versionDB = $this->_version;
$this->_enabled = ($c->get('enabled') == '1');
$this->loadFiles();
if (class_exists('Core')) {
$ch = Core::Singleton();
$ch->_registerComponent($this);
}
return $changes;
}
public function reinstall($verbosity = 0) {
if (!$this->isInstalled()) return false;
$changes = $this->_performInstall($verbosity);
if(is_array($changes) && sizeof($changes) > 0){
SystemLogModel::LogInfoEvent('/updater/component/reinstall', 'Component ' . $this->getName() . ' reinstalled successfully!', implode("\n", $changes));
}
return $changes;
}
public function upgrade($next = false, $verbose = false) {
if (!$this->isInstalled()){
if($verbose) CLI::PrintDebug('Skipping ' . $this->getName() . ' as it is marked as uninstalled.');
return false;
}
if($verbose) CLI::PrintHeader('Beginning upgrade for ' . $this->getName());
$changes = array();
$otherchanges = $this->_performInstall();
if ($otherchanges !== false) $changes = array_merge($changes, $otherchanges);
$canBeUpgraded = true;
while ($canBeUpgraded) {
$canBeUpgraded = false;
foreach ($this->_xmlloader->getRootDOM()->getElementsByTagName('upgrade') as $u) {
$from = $u->getAttribute('from');
$to   = $u->getAttribute('to') ? $u->getAttribute('to') : 'next';
if (($this->_versionDB == $from) || ($next && $from == 'next')) {
$canBeUpgraded = true;
if($verbose){
CLI::PrintLine('Processing upgrade from ' . $from . ' to ' . $to);
}
$children = $u->childNodes;
foreach($children as $child){
switch($child->nodeName){
case 'dataset':
$datachanges = $this->_parseDatasetNode($child, $verbose);
if($datachanges !== false) $changes = array_merge($changes, $datachanges);
break;
case 'phpfileinclude':
$this->_includeFileForUpgrade(ROOT_PDIR . trim($child->nodeValue), $verbose);
$changes[] = 'Included custom php file ' . basename($child->nodeValue);
break;
case 'php':
$file = $child->getAttribute('file');
if($file){
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
break;
default:
$changes[] = 'Ignoring unsupported upgrade directive: [' . $child->nodeName . ']';
}
}
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
public function _parseWidgets($install = true, $verbosity = 0) {
$overallChanges  = [];
$overallAction   = $install ? 'Installing' : 'Uninstalling';
$overallActioned = $install ? 'Installed' : 'Uninstalled';
$overallSet      = $install ? 'Set' : 'Remove';
Core\Utilities\Logger\write_debug($overallAction . ' Widgets for ' . $this->getName());
if(!$install){
die('@todo Support uninstalling widgets via _parseWidgets!');
}
$node = $this->_xmlloader->getElement('widgets');
foreach ($node->getElementsByTagName('widget') as $subnode) {
$baseurl     = $subnode->getAttribute('baseurl');
$installable = $subnode->getAttribute('installable');
$title       = $subnode->getAttribute('title');
if($verbosity == 2){
CLI::PrintActionStart($overallAction . ' widget ' . $baseurl . ' ("' . $title . '")');
}
$m = new WidgetModel($baseurl);
$action = ($m->exists()) ? 'Updated' : 'Added';
if (!$m->get('title')){
$m->set('title', $title);
}
$m->set('installable', $installable);
$saved = $m->save();
if ($saved){
if($verbosity == 2){
CLI::PrintActionStatus(true);
}
$changes[] = $action . ' widget [' . $m->get('baseurl') . ']';
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
public function _parseDBSchema($install = true, $verbosity = 0) {
$node   = $this->_xmlloader->getElement('dbschema');
$prefix = $node->getAttribute('prefix');
$changes = array();
Core\Utilities\Logger\write_debug('Installing database schema for ' . $this->getName());
$classes = $this->getModelList();
foreach ($classes as $m => $file) {
if(!class_exists($m)) require_once($file);
$schema = ModelFactory::GetSchema($m);
$tablename = $m::GetTableName();
if($verbosity == 2){
CLI::PrintActionStart('Processing database table ' . $tablename);
}
try{
if (\Core\db()->tableExists($tablename)) {
$old_schema = \Core\db()->describeTable($tablename);
$tablediffs = $old_schema->getDiff($schema);
if(sizeof($tablediffs)){
\Core\db()->modifyTable($tablename, $schema);
$changes[] = 'Modified table ' . $tablename;
foreach($tablediffs as $d){
$changes[] = '[' . $d['type'] . '] ' . $d['title'];
}
if($verbosity == 2){
CLI::PrintActionStatus('ok');
}
}
else{
if($verbosity == 2){
CLI::PrintActionStatus('skip');
}
}
}
else {
\Core\db()->createTable($tablename, $schema);
$changes[] = 'Created table ' . $tablename;
if($verbosity == 2){
CLI::PrintActionStatus('ok');
}
}
}
catch(DMI_Query_Exception $e){
error_log($e->query . "\n<br/>(original table " . $tablename . ")");
$e->query = $e->query . "\n<br/>(original table " . $tablename . ")";
throw $e;
}
}
return sizeof($changes) ? $changes : false;
} // public function _parseDBSchema()
public function _parseAssets($install = true, $verbosity = 0) {
$assetbase = CDN_LOCAL_ASSETDIR;
$theme     = ConfigHandler::Get('/theme/selected');
$change    = '';
$changes   = array();
Core\Utilities\Logger\write_debug('Installing assets for ' . $this->getName());
foreach ($this->_xmlloader->getElements('/assets/file') as $node) {
$b = $this->getBaseDir();
$newfilename = 'assets/' . substr($b . $node->getAttribute('filename'), strlen($this->getAssetDir()));
if(file_exists(ROOT_PDIR . 'themes/custom/' . $newfilename)){
$f = new \Core\Filestore\Backends\FileLocal(ROOT_PDIR . 'themes/custom/' . $newfilename);
$srcname = '!CUSTOM!';
}
elseif(file_exists(ROOT_PDIR . 'themes/' . $theme . '/' . $newfilename)){
$f = new \Core\Filestore\Backends\FileLocal(ROOT_PDIR . 'themes/' . $theme . '/' . $newfilename);
$srcname = '-theme- ';
}
else{
$f = new \Core\Filestore\Backends\FileLocal($b . $node->getAttribute('filename'));
$srcname = 'original';
}
if($verbosity == 2){
CLI::PrintActionStart('Installing ' . $srcname . ' asset ' . $f->getBasename());
}
$nf = \Core\Filestore\Factory::File($newfilename);
$newfileexists    = $nf->exists();
$newfileidentical = $nf->identicalTo($f);
if(
$newfileexists &&
$newfileidentical &&
$f instanceof \Core\Filestore\Backends\FileLocal &&
$nf instanceof \Core\Filestore\Backends\FileLocal &&
$f->getMTime() != $nf->getMTime()
){
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
if($verbosity == 2){
CLI::PrintActionStatus('skip');
}
continue;
}
elseif ($newfileexists) {
$action = 'Replaced';
}
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
\Core\Cache::Delete('core-components');
return $changes;
}
public function _parseConfigs($install = true, $verbosity = 0) {
$changes = array();
$action = $install ? 'Installing' : 'Uninstalling';
$set    = $install ? 'Set' : 'Removed';
Core\Utilities\Logger\write_debug($action . ' configs for ' . $this->getName());
$node = $this->_xmlloader->getElement('configs');
$componentName = $this->getKeyName();
foreach ($node->getElementsByTagName('config') as $confignode) {
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
if(!$type) $type = 'string';
if($verbosity == 2){
CLI::PrintActionStart($action . ' config ' . $key);
}
$m   = ConfigHandler::GetConfig($key);
if($install){
$m->set('options', $options);
$m->set('type', $type);
$m->set('default_value', $default);
$m->set('title', $title);
$m->set('description', $description);
$m->set('mapto', $mapto);
$m->set('encrypted', $encrypted);
$m->set('form_attributes', $formAtts);
$m->set('component', $componentName);
if ($m->get('value') === null || !$m->exists()){
$m->set('value', $confignode->getAttribute('default'));
}
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
ConfigHandler::CacheConfig($m);
}
else{
$m->delete();
$changes[] = $set . ' configuration [' . $key . ']';
if($verbosity == 2){
CLI::PrintActionStatus(true);
}
}
}
return (sizeof($changes)) ? $changes : false;
} // private function _parseConfigs
public function _parseUserConfigs($install = true, $verbosity = 0) {
if(!class_exists('UserConfigModel')) return false;
$changes = array();
$action = $install ? 'Installing' : 'Uninstalling';
Core\Utilities\Logger\write_debug($action . ' User Configs for ' . $this->getName());
$node = $this->_xmlloader->getElement('userconfigs', false);
if($node){
trigger_error('Use of the &lt;userconfigs/&gt; metatag is deprecated in favour of the &lt;users/&gt; metatag.  (In the ' . $this->getName() . ' component)', E_USER_DEPRECATED);
}
else{
$node = $this->_xmlloader->getElement('users');
}
foreach ($node->getElementsByTagName('userconfig') as $confignode) {
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
if($onreg === null)      $onreg = 1;
if($onedit === null)     $onedit = 1;
if($searchable === null) $searchable = 0;
if($required === null)   $required = 0;
if($weight === null)     $weight = 0;
if($weight == '')        $weight = 0;
if($hidden === null)     $hidden = 0;
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
$model->delete();
$changes[] = 'Removed user config [' . $key . ']';
if($verbosity == 2){
CLI::PrintActionStatus(true);
}
}
}
return (sizeof($changes)) ? $changes : false;
} // private function _parseUserConfigs
public function _parsePages($install = true, $verbosity = 0) {
$changes = array();
$overallAction = $install ? 'Installing' : 'Uninstalling';
Core\Utilities\Logger\write_debug($overallAction . ' pages for ' . $this->getName());
$node = $this->_xmlloader->getElement('pages');
foreach ($node->getElementsByTagName('page') as $subnode) {
$baseurl = $subnode->getAttribute('baseurl');
$m = new PageModel(-1, $baseurl);
if($verbosity == 2){
CLI::PrintActionStart($overallAction . ' page ' . $baseurl);
}
if($install){
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
if (!$m->get('rewriteurl')) {
if ($subnode->getAttribute('rewriteurl')) $m->set('rewriteurl', $subnode->getAttribute('rewriteurl'));
else $m->set('rewriteurl', $subnode->getAttribute('baseurl'));
}
if (!$m->get('title')) $m->set('title', $subnode->getAttribute('title'));
if($access !== null){
$m->set('access', $access);
}
if(!$m->exists()) $m->set('parenturl', $subnode->getAttribute('parenturl'));
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
public function disable(){
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
\Core\Cache::Delete('core-components');
return (sizeof($changed)) ? $changed : false;
}
public function enable(){
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
if(sizeof($changed)){
SystemLogModel::LogInfoEvent('/updater/component/enable', 'Component ' . $this->getName() . ' enabled successfully!', implode("\n", $changed));
}
\Core\Cache::Delete('core-components');
return (sizeof($changed)) ? $changed : false;
}
public function getRootDOM(){
return $this->_xmlloader->getRootDOM();
}
public function getXML(){
return $this->_xmlloader;
}
public function getProvides() {
$ret = array();
$ret[] = array(
'name'    => strtolower($this->getName()),
'type'    => 'component',
'version' => $this->getVersion()
);
foreach ($this->_xmlloader->getElements('provides/provide') as $el) {
$ret[] = array(
'name'    => strtolower($el->getAttribute('name')),
'type'    => $el->getAttribute('type'),
'version' => $el->getAttribute('version'),
);
}
return $ret;
}
public function getBaseDir($prefix = ROOT_PDIR) {
if ($this->_name == 'core') {
return $prefix;
}
else {
return $prefix . 'components/' . $this->getKeyName() . '/';
}
}
public function getChangedFiles(){
$changes = array();
foreach($this->_xmlloader->getElements('/files/file') as $file){
$md5 = $file->getAttribute('md5');
$filename = $file->getAttribute('filename');
if($filename == 'CHANGELOG' || $filename == 'core/CHANGELOG') continue;
$object = \Core\Filestore\Factory::File($this->getBaseDir() . $filename);
if($object->getHash() != $md5){
$changes[] = $filename;
}
}
return $changes;
}
public function getChangedTemplates(){
$changes = array();
foreach($this->_xmlloader->getElements('/templates/file') as $file){
$md5 = $file->getAttribute('md5');
$filename = $file->getAttribute('filename');
$object = \Core\Filestore\Factory::File($this->getBaseDir() . $filename);
if($object->getHash() != $md5){
$changes[] = $filename;
}
}
return $changes;
}
public function getChangedAssets(){
$changes = array();
foreach($this->_xmlloader->getElements('/assets/file') as $file){
$md5 = $file->getAttribute('md5');
$filename = $file->getAttribute('filename');
$object = \Core\Filestore\Factory::File($this->getBaseDir() . $filename);
if($object->getHash() != $md5){
$changes[] = $filename;
}
}
return $changes;
}
private function _performInstall($verbosity = 0) {
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
if($this->getKeyName() == 'core'){
$f = \Core\Filestore\Factory::File('private/.htaccess');
if(!$f->exists() && $f->isWritable()){
$src = \Core\Filestore\Factory::File('core/htaccess.private');
if($src->copyTo($f)){
$changed[] = 'Installed private htaccess file into ' . $f->getFilename();
}
}
$f = \Core\Filestore\Factory::File('public/.htaccess');
if(!$f->exists() && $f->isWritable()){
$src = \Core\Filestore\Factory::File('core/htaccess.public');
if($src->copyTo($f)){
$changed[] = 'Installed public htaccess file into ' . $f->getFilename();
}
}
$f = \Core\Filestore\Factory::File('asset/.htaccess');
$f->setFilename(dirname(dirname($f->getFilename())) . '/.htaccess');
if(!$f->exists() && $f->isWritable()){
$src = \Core\Filestore\Factory::File('core/htaccess.assets');
if($src->copyTo($f)){
$changed[] = 'Installed assets htaccess file into ' . $f->getFilename();
}
}
}
\Core\Cache::Delete('core-components');
return (sizeof($changed)) ? $changed : false;
}
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
private function _includeFileForUpgrade($filename, $verbose = false){
if($verbose){
CLI::PrintLine('Loading custom PHP file ' . $filename);
}
include($filename);
}
private function _checkUpgradePath(){
if($this->_versionDB && $this->_version != $this->_versionDB){
$paths = array();
foreach ($this->_xmlloader->getRootDOM()->getElementsByTagName('upgrade') as $u) {
$from = $u->getAttribute('from');
$to   = $u->getAttribute('to');
if(!isset($paths[$from])) $paths[$from] = array();
$paths[$from][] = $to;
}
if(!sizeof($paths)){
return false;
}
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
return true;
}
else{
return true;
}
}
}


### REQUIRE_ONCE FROM core/functions/Core.functions.php
} // ENDING GLOBAL NAMESPACE
namespace Core {
use Core\Datamodel;
use Core\Filestore\FTP\FTPConnection;
use DMI;
use Cache;
function db(){
return DMI::GetSystemDMI()->connection();
}
function ftp(){
static $ftp = null;
if($ftp === null){
if(!defined('FTP_USERNAME')){
$ftp = false;
return false;
}
if(!defined('FTP_PASSWORD')){
$ftp = false;
return false;
}
if(!defined('FTP_PATH')){
$ftp = false;
return false;
}
if(!FTP_USERNAME){
$ftp = false;
return false;
}
$ftp = new FTPConnection();
$ftp->host = '127.0.0.1';
$ftp->username = FTP_USERNAME;
$ftp->password = FTP_PASSWORD;
$ftp->root = FTP_PATH;
$ftp->url = ROOT_WDIR;
try{
$ftp->connect();
}
catch(\Exception $e){
\Core\ErrorManagement\exception_handler($e);
$ftp = false;
return false;
}
}
if($ftp && $ftp instanceof FTPConnection){
try{
$ftp->reset();
}
catch(\Exception $e){
\Core\ErrorManagement\exception_handler($e);
$ftp = false;
return false;
}
}
return $ftp;
}
function user(){
static $_CurrentUserAccount = null;
if(!class_exists('\\UserModel')){
return null;
}
if($_CurrentUserAccount !== null){
return $_CurrentUserAccount;
}
if(isset($_SERVER['HTTP_X_CORE_AUTH_KEY'])){
$user = \UserModel::Find(['apikey = ' . $_SERVER['HTTP_X_CORE_AUTH_KEY']], 1);
if($user){
$_CurrentUserAccount = $user;
}
}
elseif(Session::Get('user') instanceof \UserModel){
if(isset(Session::$Externals['user_forcesync'])){
$_CurrentUserAccount = \UserModel::Construct(Session::Get('user')->get('id'));
Session::Set('user', $_CurrentUserAccount);
unset(Session::$Externals['user_forcesync']);
}
else{
$_CurrentUserAccount = Session::Get('user');
}
}
if($_CurrentUserAccount === null){
$_CurrentUserAccount = new \UserModel();
}
if(\Core::IsComponentAvailable('enterprise') && class_exists('MultiSiteHelper') && \MultiSiteHelper::IsEnabled()){
$_CurrentUserAccount->clearAccessStringCache();
}
if(Session::Get('user_sudo') !== null){
$sudo = Session::Get('user_sudo');
if($sudo instanceof \UserModel){
if($_CurrentUserAccount->checkAccess('p:/user/users/sudo')){
if($sudo->checkAccess('g:admin') && !$_CurrentUserAccount->checkAccess('g:admin')){
Session::UnsetKey('user_sudo');
\SystemLogModel::LogSecurityEvent('/user/sudo', 'Authorized but non-SA user requested sudo access to a system admin!', null, $sudo->get('id'));
}
else{
return $sudo;
}
}
else{
Session::UnsetKey('user_sudo');
\SystemLogModel::LogSecurityEvent('/user/sudo', 'Unauthorized user requested sudo access to another user!', null, $sudo->get('id'));
}
}
else{
Session::UnsetKey('user_sudo');
}
}
return $_CurrentUserAccount;
}
function file($filename = null){
return \Core\Filestore\Factory::File($filename);
}
function directory($directory){
return \Core\Filestore\Factory::Directory($directory);
}
function page_request(){
return \PageRequest::GetSystemRequest();
}
function view(){
return page_request()->getView();
}
function get_standard_http_headers($forcurl = false, $autoclose = false){
$headers = array(
'User-Agent: Core Plus ' . \Core::GetComponent()->getVersion() . ' (http://corepl.us)',
'Servername: ' . SERVERNAME,
);
if($autoclose){
$headers[] = 'Connection: close';
}
if($forcurl){
return $headers;
}
else{
return implode("\r\n", $headers);
}
}
function resolve_asset($asset){
if(strpos($asset, '://') !== false) return $asset;
if(strpos($asset, 'assets/') !== 0) $asset = 'assets/' . $asset;
$version = \ConfigHandler::Get('/core/filestore/assetversion');
$proxyfriendly = \ConfigHandler::Get('/core/assetversion/proxyfriendly');
$file     = \Core\Filestore\Factory::File($asset);
$filename = $file->getFilename();
$ext      = $file->getExtension();
$suffix   = '';
$url = substr($file->getURL(), 0, -1-strlen($ext));
if(\ConfigHandler::Get('/core/javascript/minified')){
if($ext == 'js'){
$minified = substr($filename, 0, -3) . '.min.js';
$minfile = \Core\Filestore\Factory::File($minified);
if($minfile->exists()){
$ext = 'min.js';
}
}
elseif($ext == 'css'){
$minified = substr($filename, 0, -4) . '.min.css';
$minfile = \Core\Filestore\Factory::File($minified);
if($minfile->exists()){
$ext = 'min.css';
}
}
}
if($version && $proxyfriendly){
$ext = 'v' . $version . '.' . $ext;
}
elseif($version){
$suffix = '?v=' . $version;
}
return $url . '.' . $ext . $suffix;
}
function resolve_link($url) {
if ($url == '#') return $url;
if (strpos($url, '://') !== false) return $url;
if($url{0} == '?'){
$url = REL_REQUEST_PATH . $url;
}
if(stripos($url, 'site:') === 0){
$slashpos = strpos($url, '/');
$site = substr($url, 5, $slashpos-5);
$url = substr($url, $slashpos);
}
else{
$site = null;
}
try{
$a = \PageModel::SplitBaseURL($url, $site);
}
catch(\Exception $e){
\Core\ErrorManagement\exception_handler($e);
error_log('Unable to resolve URL [' . $url . '] due to exception [' . $e->getMessage() . ']');
return '';
}
return $a['fullurl'];
}
function redirect($page, $code = 302){
if(!($code == 301 || $code == 302)){
throw new \Exception('Invalid response code requested for redirect, [' . $code . '].  Please ensure it is either a 301 (permanent), or 302 (temporary) redirect!');
}
$hp = ($page == '/');
$page = resolve_link($page);
if(!$page && $hp) $page = ROOT_URL;
if ($page == CUR_CALL) return;
switch($code){
case 301:
$movetext = '301 Moved Permanently';
break;
case 302:
$movetext = '302 Moved Temporarily';
break;
default:
$movetext = $code . ' Moved Temporarily';
break;
}
header('X-Content-Encoded-By: Core Plus ' . (DEVELOPMENT_MODE ? \Core::GetComponent()->getVersion() : ''));
if(\ConfigHandler::Get('/core/security/x-frame-options')){
header('X-Frame-Options: ' . \ConfigHandler::Get('/core/security/x-frame-options'));
}
if(\ConfigHandler::Get('/core/security/csp-frame-ancestors')){
header('Content-Security-Policy: frame-ancestors \'self\' ' . \ConfigHandler::Get('/core/security/content-security-policy'));
}
header('HTTP/1.1 ' . $movetext);
header('Location: ' . $page);
\HookHandler::DispatchHook('/core/page/postrender');
die('If your browser does not refresh, please <a href="' . $page . '">Click Here</a>');
}
function reload(){
$movetext = '302 Moved Temporarily';
header('X-Content-Encoded-By: Core Plus ' . (DEVELOPMENT_MODE ? \Core::GetComponent()->getVersion() : ''));
if(\ConfigHandler::Get('/core/security/x-frame-options')){
header('X-Frame-Options: ' . \ConfigHandler::Get('/core/security/x-frame-options'));
}
if(\ConfigHandler::Get('/core/security/csp-frame-ancestors')){
header('Content-Security-Policy: frame-ancestors \'self\' ' . \ConfigHandler::Get('/core/security/content-security-policy'));
}
header('HTTP/1.1 302 Moved Temporarily');
header('Location:' . CUR_CALL);
\HookHandler::DispatchHook('/core/page/postrender');
die('If your browser does not refresh, please <a href="' . CUR_CALL . '">Click Here</a>');
}
function go_back() {
$request = page_request();
$history = $request->getReferrer();
if($history != CUR_CALL){
redirect($history);
}
else{
reload();
}
}
function parse_html($html){
$x = 0;
$imagestart = null;
while($x < strlen($html)){
if(substr($html, $x, 4) == '<img'){
$imagestart = $x;
$x+= 3;
continue;
}
$fullimagetag = null;
if($imagestart !== null && $html{$x} == '>'){
$fullimagetag = substr($html, $imagestart, $x-$imagestart+1);
}
elseif($imagestart !== null && substr($html, $x, 2) == '/>'){
$fullimagetag = substr($html, $imagestart, $x-$imagestart+2);
}
if($imagestart !== null && $fullimagetag){
$simple = new \SimpleXMLElement($fullimagetag);
$attributes = array();
foreach($simple->attributes() as $k => $v){
$attributes[$k] = (string)$v;
}
$file = \Core\Filestore\Factory::File($attributes['src']);
if(!isset($attributes['alt']) || $attributes['alt'] == ''){
$attributes['alt'] = $file->getTitle();
}
if(isset($attributes['width']) || isset($attributes['height'])){
if(isset($attributes['width']) && isset($attributes['height'])){
$dimension = $attributes['width'] . 'x' . $attributes['height'] . '!';
unset($attributes['width'], $attributes['height']);
}
elseif(isset($attributes['width'])){
$dimension = $attributes['width'];
unset($attributes['width']);
}
else{
$dimension = $attributes['height'];
unset($attributes['height']);
}
$attributes['src'] = $file->getPreviewURL($dimension);
}
$img = '<img';
foreach($attributes as $k => $v){
$img .= ' ' . $k . '="' . str_replace('"', '&quot;', $v) . '"';
}
$img .= '/>';
$metahelper  = new \Core\Filestore\FileMetaHelper($file);
$metacontent = $metahelper->getAsHTML();
if($metacontent){
$img = '<div class="image-metadata-wrapper">' . $img . $metacontent . '</div>';
}
$x += strlen($img) - strlen($fullimagetag);
$html = substr_replace($html, $img, $imagestart, strlen($fullimagetag));
$imagestart = null;
}
$x++;
}
return $html;
}
function GetNavigation($base){
trigger_error('\\Core\\GetNavigation is deprecated and will be removed shortly', E_USER_DEPRECATED);
return page_request()->getReferrer();
}
function RecordNavigation(\PageModel $page){
trigger_error('\\Core\\RecordNavigation is deprecated and will be removed shortly', E_USER_DEPRECATED);
}
function set_message($messageText, $messageType = 'info'){
if(strpos($messageText, 'MESSAGE_') === 0){
if(strpos($messageText, 'MESSAGE_SUCCESS_') === 0){
$messageType = 'success';
}
elseif(strpos($messageText, 'MESSAGE_ERROR_') === 0){
$messageType = 'error';
}
elseif(strpos($messageText, 'MESSAGE_TUTORIAL_') === 0){
$messageType = 'tutorial';
}
elseif(strpos($messageText, 'MESSAGE_WARNING_') === 0){
$messageType = 'warning';
}
elseif(strpos($messageText, 'MESSAGE_INFO_') === 0){
$messageType = 'info';
}
else{
$messageType = 'info';
}
if(func_num_args() > 1){
$messageText = call_user_func_array('t', func_get_args());
}
else{
$messageText = t($messageText);
}
}
if(EXEC_MODE == 'CLI'){
$messageText = preg_replace('/<br[^>]*>/i', "\n", $messageText);
echo "[" . $messageType . "] - " . $messageText . "\n";
}
else{
$stack = Session::Get('message_stack', []);
$stack[] = array(
'mtext' => $messageText,
'mtype' => $messageType,
);
Session::Set('message_stack', $stack);
}
}
function get_messages($returnSorted = FALSE, $clearStack = TRUE){
return \Core::GetMessages($returnSorted, $clearStack);
}
function SortByKey($named_recs, $order_by, $rev=false, $flags=0){
$named_hash = array();
foreach($named_recs as $key=>$fields) $named_hash["$key"] = $fields[$order_by];
if($rev) arsort($named_hash,$flags) ;
else asort($named_hash, $flags);
$sorted_records = array();
foreach($named_hash as $key=>$val) $sorted_records["$key"]= $named_recs[$key];
return $sorted_records;
}
function ImplodeKey($glue, &$array){
$arrayKeys = array();
foreach($array as $key => $value){
$arrayKeys[] = $key;
}
return implode($glue, $arrayKeys);
}
function random_hex($length = 1, $casesensitive = false){
$output = '';
if($casesensitive){
$chars = '0123456789ABCDEFabcdef';
$charlen = 21; // (needs to be -1 of the actual length)
}
else{
$chars = '0123456789ABCDEF';
$charlen = 15; // (needs to be -1 of the actual length)
}
$output = '';
for ($i = 0; $i < $length; $i++){
$pos = rand(0, $charlen);
$output .= $chars{$pos};
}
return $output;
}
function compare_values($val1, $val2){
if($val1 === $val2){
return true;
}
if(is_numeric($val1) && is_numeric($val2) && $val1 == $val2){
return true;
}
if(is_scalar($val1) && is_scalar($val2) && strlen($val1) == strlen($val2) && $val1 == $val2){
return true;
}
return false;
}
function compare_strings($val1, $val2) {
if($val1 === $val2){
return true;
}
if(strlen($val1) == strlen($val2) && $val1 == $val2){
return true;
}
return false;
}
function FormatSize($filesize, $round = 2){
return \Core\Filestore\format_size($filesize, $round);
}
function GetExtensionFromString($str){
if(strpos($str, '.') === false) return '';
return substr($str, strrpos($str, '.') + 1 );
}
function CheckEmailValidity($email){
$atIndex = strrpos($email, "@");
if (is_bool($atIndex) && !$atIndex) return false;
$domain = substr($email, $atIndex+1);
$local = substr($email, 0, $atIndex);
$localLen = strlen($local);
$domainLen = strlen($domain);
if ($localLen < 1 || $localLen > 64) {
return false;
}
if ($domainLen < 1 || $domainLen > 255) {
return false;
}
if ($local[0] == '.' || $local[$localLen-1] == '.') {
return false;
}
if (preg_match('/\\.\\./', $local)) {
return false;
}
if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
return false;
}
if (preg_match('/\\.\\./', $domain)) {
return false;
}
if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
return false;
}
}
if (ConfigHandler::Get('/core/email/verify_with_dns') &&  !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
return false;
}
return true;
}
function VersionCompare($version1, $version2, $operation = null){
if(!$version1) $version1 = 0;
if(!$version2) $version2 = 0;
$version1 = Core::VersionSplit($version1);
$version2 = Core::VersionSplit($version2);
$keys = array('major', 'minor', 'point', 'core', 'user', 'stability');
$v1 = $version1['major'] . '.' . $version1['minor'] . '.' . $version1['point'] . '.' . $version1['core'];
$v2 = $version2['major'] . '.' . $version2['minor'] . '.' . $version2['point'] . '.' . $version2['core'];
$check = version_compare($v1, $v2);
if($check != 0){
if($operation == null) return $check;
switch($operation){
case 'lt':
case 'le':
case '<':
case '<=':
return ($check == -1);
default:
return true;
}
}
else{
if($operation == null) return $check;
switch($operation){
case 'le':
case '<=':
case 'ge':
case '>=':
case 'eq':
case '=':
return true;
default:
return false;
}
}
}
function VersionSplit($version){
$ret = array(
'major' => 0,
'minor' => 0,
'point' => 0,
'core' => 0,
'user' => 0,
'stability' => '',
);
$v = array();
$lengthall = strlen($version);
$pos = 0;
$x = 0;
while($pos < $lengthall && $x < 10){
$nextpos = strpos($version, '.', $pos) - $pos;
$part = ($nextpos > 0) ? substr($version, $pos, $nextpos) : substr($version, $pos);
if(($subpos = strpos($part, '-')) !== false){
$subpart = strtolower(substr($part, $subpos + 1));
if(is_numeric($subpart)){
$ret['core'] = $subpart;
}
elseif($subpart == 'a'){
$ret['stability'] = 'alpha';
}
elseif($subpart == 'b'){
$ret['stability'] = 'beta';
}
else{
$ret['stability'] = $subpart;
}
$part = substr($part, 0, $subpos);
}
$v[] = (int)$part;
$pos = ($nextpos > 0) ? $pos + $nextpos + 1 : $lengthall;
$x++; // Just in case something really bad happens here...
}
for($i = 0; $i < 3; $i++){
if(!isset($v[$i])) $v[$i] = 0;
}
$ret['major'] = $v[0];
$ret['minor'] = $v[1];
$ret['point'] = $v[2];
return $ret;
}
function str_to_latin($string){
$internationalmappings = array(
'À' => 'A',
'Á' => 'A',
'Â' => 'A',
'Ã' => 'A',
'Ä' => 'A',
'Å' => 'AA',
'Æ' => 'AE',
'Ç' => 'C',
'È' => 'E',
'É' => 'E',
'Ê' => 'E',
'Ë' => 'E',
'Ì' => 'I',
'Í' => 'I',
'Î' => 'I',
'Ï' => 'I',
'Ð' => 'D',
'Ł' => 'L',
'Ñ' => 'N',
'Ò' => 'O',
'Ó' => 'O',
'Ô' => 'O',
'Õ' => 'O',
'Ö' => 'O',
'Ø' => 'OE',
'Ù' => 'U',
'Ú' => 'U',
'Ü' => 'U',
'Û' => 'U',
'Ý' => 'Y',
'Þ' => 'Th',
'ß' => 'ss',
'à' => 'a',
'á' => 'a',
'â' => 'a',
'ã' => 'a',
'ä' => 'a',
'å' => 'aa',
'æ' => 'ae',
'ç' => 'c',
'è' => 'e',
'é' => 'e',
'ê' => 'e',
'ë' => 'e',
'ì' => 'i',
'í' => 'i',
'î' => 'i',
'ï' => 'i',
'ð' => 'd',
'ł' => 'l',
'ñ' => 'n',
'ń' => 'n',
'ò' => 'o',
'ó' => 'o',
'ô' => 'o',
'õ' => 'o',
'ō' => 'o',
'ö' => 'o',
'ø' => 'oe',
'ś' => 's',
'ù' => 'u',
'ú' => 'u',
'û' => 'u',
'ū' => 'u',
'ü' => 'u',
'ý' => 'y',
'þ' => 'th',
'ÿ' => 'y',
'ż' => 'z',
'Œ' => 'OE',
'œ' => 'oe',
'&' => 'and'
);
return str_replace(array_keys($internationalmappings), array_values($internationalmappings), $string);
}
function str_to_url($string, $keepdots = false){
$string = str_to_latin($string);
if(\ConfigHandler::Get('/core/page/url_remove_stop_words')){
$stopwords = get_stop_words();
$exploded = explode(' ', $string);
$nt = '';
foreach($exploded as $w){
$lw = strtolower($w);
if(!in_array($lw, $stopwords)){
$nt .= ' ' . $w;
}
}
$string = trim($string);
}
$string = str_replace(' ', '-', $string);
if($keepdots){
$string = preg_replace('/[^a-z0-9\-\.]/i', '', $string);
}
else{
$string = preg_replace('/[^a-z0-9\-]/i', '', $string);
}
$string = preg_replace('/[-]+/', '-', $string);
$string = preg_replace('/^-/', '', $string);
$string = preg_replace('/-$/', '', $string);
$string = strtolower($string);
return $string;
}
function translate_upload_error($errno){
return \Core\Filestore\translate_upload_error($errno);
}
function check_file_mimetype($acceptlist, $mimetype, $extension = null){
return \Core\Filestore\check_file_mimetype($acceptlist, $mimetype, $extension);
}
function is_numeric_array($array){
if(!is_array($array)) return false;
reset($array);
if(key($array) !== 0) return false;
$c = count($array) - 1;
end($array);
if(key($array) !== $c) return false;
return true;
}
function get_stop_words(){
$stopwords = array('a', 'about', 'above', 'above', 'across', 'after', 'afterwards', 'again', 'against', 'all', 'almost', 'alone', 'along', 'already', 'also','although','always','am','among', 'amongst', 'amoungst', 'amount',  'an', 'and', 'another', 'any','anyhow','anyone','anything','anyway', 'anywhere', 'are', 'around', 'as',  'at', 'back','be','became', 'because','become','becomes', 'becoming', 'been', 'before', 'beforehand', 'behind', 'being', 'below', 'beside', 'besides', 'between', 'beyond', 'bill', 'both', 'bottom','but', 'by', 'call', 'can', 'cannot', 'cant', 'co', 'con', 'could', 'couldnt', 'cry', 'de', 'describe', 'detail', 'do', 'done', 'down', 'due', 'during', 'each', 'eg', 'eight', 'either', 'eleven','else', 'elsewhere', 'empty', 'enough', 'etc', 'even', 'ever', 'every', 'everyone', 'everything', 'everywhere', 'except', 'few', 'fifteen', 'fify', 'fill', 'find', 'fire', 'first', 'five', 'for', 'former', 'formerly', 'forty', 'found', 'four', 'from', 'front', 'full', 'further', 'get', 'give', 'go',
'had', 'has', 'hasnt', 'have', 'he', 'hence', 'her', 'here', 'hereafter', 'hereby', 'herein', 'hereupon', 'hers', 'herself', 'him', 'himself', 'his', 'how', 'however', 'hundred', 'ie', 'if', 'in', 'inc', 'indeed', 'interest', 'into', 'is', 'it', 'its', 'itself', 'keep', 'last', 'latter', 'latterly', 'least', 'less', 'ltd', 'made', 'many', 'may', 'me', 'meanwhile', 'might', 'mill', 'mine', 'more', 'moreover', 'most', 'mostly', 'move', 'much', 'must', 'my', 'myself', 'name', 'namely', 'neither', 'never', 'nevertheless', 'next', 'nine', 'no', 'nobody', 'none', 'noone', 'nor', 'not', 'nothing', 'now', 'nowhere', 'of', 'off', 'often', 'on', 'once', 'one', 'only', 'onto', 'or', 'other', 'others', 'otherwise', 'our', 'ours', 'ourselves', 'out', 'over', 'own','part', 'per', 'perhaps', 'please', 'put', 'rather', 're', 'same', 'see', 'seem', 'seemed', 'seeming', 'seems', 'serious', 'several', 'she', 'should', 'show', 'side', 'since', 'sincere', 'six', 'sixty', 'so', 'some', 'somehow', 'someone', 'something', 'sometim
e', 'sometimes', 'somewhere', 'still', 'such', 'system', 'take', 'ten', 'than', 'that', 'the', 'their', 'them', 'themselves', 'then', 'thence', 'there', 'thereafter', 'thereby', 'therefore', 'therein', 'thereupon', 'these', 'they', 'thickv', 'thin', 'third', 'this', 'those', 'though', 'three', 'through', 'throughout', 'thru', 'thus', 'to', 'together', 'too', 'top', 'toward', 'towards', 'twelve', 'twenty', 'two', 'un', 'under', 'until', 'up', 'upon', 'us', 'very', 'via', 'was', 'we', 'well', 'were', 'what', 'whatever', 'when', 'whence', 'whenever', 'where', 'whereafter', 'whereas', 'whereby', 'wherein', 'whereupon', 'wherever', 'whether', 'which', 'while', 'whither', 'who', 'whoever', 'whole', 'whom', 'whose', 'why', 'will', 'with', 'within', 'without', 'would', 'yet', 'you', 'your', 'yours', 'yourself', 'yourselves', 'the');
return $stopwords;
}
} // ENDING NAMESPACE Core

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/functions.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore {
use Core\Filestore\CDN;
use Core\Filestore\FTP\FTPConnection;
use Core\i18n\I18NLoader;
function format_size($filesize, $round = 2) {
$suf = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');
$c   = 0;
while ($filesize >= 1024) {
$c++;
$filesize = $filesize / 1024;
}
return I18NLoader::FormatNumber($filesize, $round) . ' ' . $suf[$c];
}
function get_asset_path(){
static $_path;
if ($_path === null) {
switch(CDN_TYPE){
case 'local':
$dir = CDN_LOCAL_ASSETDIR;
if($dir){
if ($dir{0} != '/') $dir = ROOT_PDIR . $dir;
if (substr($dir, -1) != '/') $dir = $dir . '/';
$_path = $dir;
}
break;
case 'ftp':
$dir = CDN_FTP_ASSETDIR;
if($dir){
if ($dir{0} != '/') $dir = CDN_FTP_PATH . $dir;
if (substr($dir, -1) != '/') $dir = $dir . '/';
$_path = $dir;
}
break;
default:
throw new \Exception('Unsupported CDN type: ' . CDN_TYPE);
}
}
return $_path;
}
function get_public_path(){
static $_path;
if ($_path === null) {
switch(CDN_TYPE){
case 'local':
$dir = CDN_LOCAL_PUBLICDIR;
if($dir){
if ($dir{0} != '/') $dir = ROOT_PDIR . $dir;
if (substr($dir, -1) != '/') $dir = $dir . '/';
$_path = $dir;
}
break;
case 'ftp':
$dir = CDN_FTP_PUBLICDIR;
if($dir){
if ($dir{0} != '/') $dir = CDN_FTP_PATH . $dir;
if (substr($dir, -1) != '/') $dir = $dir . '/';
$_path = $dir;
}
break;
default:
throw new \Exception('Unsupported CDN type: ' . CDN_TYPE);
}
}
return $_path;
}
function get_private_path(){
static $_path;
if ($_path === null) {
switch(CDN_TYPE){
case 'local':
$dir = CDN_LOCAL_PRIVATEDIR;
if($dir){
if ($dir{0} != '/') $dir = ROOT_PDIR . $dir;
if (substr($dir, -1) != '/') $dir = $dir . '/';
$_path = $dir;
}
break;
case 'ftp':
$dir = CDN_FTP_PRIVATEDIR;
if($dir){
if ($dir{0} != '/') $dir = CDN_FTP_PATH . $dir;
if (substr($dir, -1) != '/') $dir = $dir . '/';
$_path = $dir;
}
break;
default:
throw new \Exception('Unsupported CDN type: ' . CDN_TYPE);
}
}
return $_path;
}
function get_tmp_path(){
static $_path;
if ($_path === null) {
$dir = TMP_DIR;
if($dir){
if ($dir{0} != '/') $dir = ROOT_PDIR . $dir;
if (substr($dir, -1) != '/') $dir = $dir . '/';
$_path = $dir;
}
}
return $_path;
}
function resolve_contents_object(File $file){
$class = null;
$ext = $file->getExtension();
$mime = $file->getMimetype();
switch ($mime) {
case 'application/x-gzip':
case 'application/gzip':
if ($ext == 'tgz'){
$class = 'ContentTGZ';
}
elseif($ext == 'tar.gz'){
$class = 'ContentTGZ';
}
else{
$class = 'ContentGZ';
}
break;
case 'text/plain':
if ($ext == 'asc'){
$class = 'ContentASC';
}
else{
$class = 'ContentUnknown';
}
break;
case 'text/xml':
case 'application/xml':
$class = 'ContentXML';
break;
case 'application/pgp-signature':
case 'application/pgp':
$class = 'ContentASC';
break;
case 'application/zip':
$class = 'ContentZIP';
break;
case 'text/csv':
$class = 'ContentCSV';
break;
case 'application/octet-stream':
if($ext == 'zip'){
$class = 'ContentZIP';
}
else{
error_log('@fixme Unknown extension for application/octet-stream mimetype [' . $ext . ']');
$class = 'ContentUnknown';
}
break;
default:
error_log('@fixme Unknown file mimetype [' . $mime . '] with extension [' . $ext . ']');
$class = 'ContentUnknown';
}
$resolved = '\\Core\\Filestore\\Contents\\' . $class;
if(!class_exists($resolved)){
if(file_exists(ROOT_PDIR . 'core/libs/core/filestore/contents/' . $class . '.php')){
require_once(ROOT_PDIR . 'core/libs/core/filestore/contents/' . $class . '.php');
}
else{
throw new \Exception('Unable to locate file for class [' . $class . ']');
}
}
$ref = new \ReflectionClass($resolved);
return $ref->newInstance($file);
}
function get_extension_from_string($str) {
if (strpos($str, '.') === false) return '';
$str = strtolower($str);
$ext = substr($str, strrpos($str, '.') + 1);
if($ext == 'gz' && substr($str, -7) == '.tar.gz'){
return 'tar.gz';
}
return $ext;
}
function resolve_asset_file($filename){
$resolved = get_asset_path();
$theme = \ConfigHandler::Get('/theme/selected');
if (strpos($filename, 'assets/') === 0) {
$filename = substr($filename, 7);
}
elseif(strpos($filename, 'asset/') === 0){
$filename = substr($filename, 6);
}
elseif(strpos($filename, $resolved) === 0){
if(strpos($filename, $resolved . 'custom/') === 0){
$filename = substr($filename, strlen($resolved . 'custom/'));
}
elseif(strpos($filename, $resolved . $theme . '/') === 0){
$filename = substr($filename, strlen($resolved . $theme . '/'));
}
elseif(strpos($filename, $resolved . 'default/') === 0){
$filename = substr($filename, strlen($resolved . 'default/'));
}
else{
$filename = substr($filename, strlen($resolved));
}
}
switch(CDN_TYPE){
case 'local':
if(\Core\ftp()){
return new Backends\FileFTP($resolved  . $filename);
}
else{
return new Backends\FileLocal($resolved  . $filename);
}
break;
case 'ftp':
return new Backends\FileFTP($resolved  . $filename, cdn_ftp());
break;
default:
throw new \Exception('Unsupported CDN type: ' . CDN_TYPE);
break;
}
}
function resolve_public_file($filename){
$resolved = get_public_path();
if (strpos($filename, 'public/') === 0) {
$filename = substr($filename, 7);
}
elseif(strpos($filename, $resolved) === 0){
$filename = substr($filename, strlen($resolved));
}
switch(CDN_TYPE){
case 'local':
if(\Core\ftp()){
return new Backends\FileFTP($resolved . $filename);
}
else{
return new Backends\FileLocal($resolved . $filename);
}
break;
case 'ftp':
return new Backends\FileFTP($resolved  . $filename, cdn_ftp());
break;
default:
throw new \Exception('Unsupported CDN type: ' . CDN_TYPE);
break;
}
}
function resolve_private_file($filename){
$resolved = get_private_path();
if (strpos($filename, 'private/') === 0) {
$filename = substr($filename, 8);
}
elseif(strpos($filename, $resolved) === 0){
$filename = substr($filename, strlen($resolved));
}
switch(CDN_TYPE){
case 'local':
if(\Core\ftp()){
return new Backends\FileFTP($resolved . $filename);
}
else{
return new Backends\FileLocal($resolved . $filename);
}
break;
case 'ftp':
return new Backends\FileFTP($resolved  . $filename, cdn_ftp());
break;
default:
throw new \Exception('Unsupported CDN type: ' . CDN_TYPE);
break;
}
}
function resolve_asset_directory($filename){
$resolved = get_asset_path();
if (strpos($filename, 'assets/') === 0) {
$filename = substr($filename, 7);
}
elseif(strpos($filename, 'asset/') === 0){
$filename = substr($filename, 6);
}
elseif(strpos($filename, $resolved) === 0){
$filename = substr($filename, strlen($resolved));
}
$theme = \ConfigHandler::Get('/theme/selected');
switch(CDN_TYPE){
case 'local':
if(\Core\ftp()){
$custom  = new Backends\DirectoryFTP($resolved  . 'custom/' . $filename);
$themed  = new Backends\DirectoryFTP($resolved  . $theme . '/' . $filename);
$default = new Backends\DirectoryFTP($resolved  . 'default/' . $filename);
}
else{
$custom  = new Backends\DirectoryLocal($resolved  . 'custom/' . $filename);
$themed  = new Backends\DirectoryLocal($resolved  . $theme . '/' . $filename);
$default = new Backends\DirectoryLocal($resolved  . 'default/' . $filename);
}
break;
default:
throw new \Exception('Unsupported CDN type: ' . CDN_TYPE);
break;
}
if($custom->exists()){
return $custom;
}
elseif($themed->exists()){
return $themed;
}
else{
return $default;
}
}
function resolve_public_directory($filename){
$resolved = get_public_path();
if (strpos($filename, 'public/') === 0) {
$filename = substr($filename, 7);
}
elseif(strpos($filename, $resolved) === 0){
$filename = substr($filename, strlen($resolved));
}
$theme = \ConfigHandler::Get('/theme/selected');
switch(CDN_TYPE){
case 'local':
if(\Core\ftp()){
return new Backends\DirectoryFTP($resolved . $filename);
}
else{
return new Backends\DirectoryLocal($resolved . $filename);
}
break;
default:
throw new \Exception('Unsupported CDN type: ' . CDN_TYPE);
break;
}
}
function translate_upload_error($errno){
switch($errno){
case UPLOAD_ERR_OK:
return '';
case UPLOAD_ERR_INI_SIZE:
if(DEVELOPMENT_MODE){
return 'The uploaded file exceeds the upload_max_filesize directive in php.ini [' . ini_get('upload_max_filesize') . ']';
}
else{
return 'The uploaded file is too large, maximum size is ' . ini_get('upload_max_filesize');
}
case UPLOAD_ERR_FORM_SIZE:
if(DEVELOPMENT_MODE){
return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. ';
}
else{
return 'The uploaded file is too large.';
}
default:
return 'An error occurred while trying to upload the file.';
}
}
function check_file_mimetype($acceptlist, $mimetype, $extension = null){
$acceptgood = false;
$accepts = array_map(
'trim',
explode(
',',
strtolower($acceptlist)
)
);
$extension = strtolower($extension);
foreach($accepts as $accepttype){
if($accepttype == '*'){
$acceptgood = true;
break;
}
elseif(preg_match('#^[a-z\-\+]+/[0-9a-z\-\+\.]+#', $accepttype)){
if($accepttype == $mimetype){
$acceptgood = true;
break;
}
}
elseif(preg_match('#^[a-z\-\+]+/\*#', $accepttype)){
if(strpos($mimetype, substr($accepttype, 0, -1)) === 0){
$acceptgood = true;
break;
}
}
elseif($extension && preg_match('#^\.*#', $accepttype)){
if(substr($accepttype, 1) == $extension){
$acceptgood = true;
break;
}
}
else{
return 'Unsupported accept option, ' . $accepttype;
}
}
if(!$acceptgood){
if(sizeof($accepts) > 1){
$err = 'matches one of [ ' . implode(', ', $accepts) . ' ]';
}
else{
$err = 'is a ' . $accepts[0] . ' file';
}
return 'Invalid file uploaded, please ensure it ' . $err;
}
else{
return '';
}
}
function extension_to_mimetype($ext){
switch($ext){
case 'atom':
return 'application/atom+xml';
case 'css':
return 'text/css';
case 'csv':
return 'text/csv';
case 'fgl':
return 'application/fgl+text';
case 'gif':
return 'image/gif';
case 'html':
case 'htm':
return 'text/html';
case 'ics':
return 'text/calendar';
case 'jpg':
case 'jpeg':
return 'image/jpeg';
case 'js':
return 'text/javascript';
case 'json':
return 'application/json';
case 'otf':
return 'font/otf';
case 'png':
return 'image/png';
case 'rss':
return 'application/rss+xml';
case 'ttf':
return 'font/ttf';
case 'xhtml':
return 'application/xhtml+xml';
case 'xml':
return 'application/xml';
default:
return 'application/octet-stream';
}
}
function mimetype_to_extension($mimetype){
switch($mimetype){
case 'application/atom+xml':
return 'atom';
case 'application/json':
return 'json';
case 'application/rss+xml':
return 'rss';
case 'application/xhtml+xml':
return 'xhtml';
case 'application/xml':
return 'xml';
case 'font/otf':
return 'otf';
case 'font/ttf':
return 'ttf';
case 'image/gif':
return 'gif';
case 'image/jpeg':
return 'jpeg';
case 'image/png':
return 'png';
case 'text/calendar':
return 'ics';
case 'text/css':
return 'css';
case 'text/csv':
return 'csv';
case 'text/html':
return 'html';
case 'text/javascript':
return 'js';
default:
return '';
}
}
function cdn_ftp(){
static $ftp = null;
if($ftp === null){
$ftp = new FTPConnection();
$ftp->host = CDN_FTP_HOST;
$ftp->username = CDN_FTP_USERNAME;
$ftp->password = CDN_FTP_PASSWORD;
$ftp->root = CDN_FTP_PATH;
$ftp->url = CDN_FTP_URL;
}
return $ftp;
}
function get_resized_key_components($dimensions, $file){
if (is_numeric($dimensions)) {
$width  = $dimensions;
$height = $dimensions;
$mode = '';
}
elseif ($dimensions === null) {
$width  = 300;
$height = 300;
$mode = '';
}
elseif($dimensions === false){
$width = false;
$height = false;
$mode = '';
}
else {
if(strpos($dimensions, '^') !== false){
$mode = '^';
$dimensions = str_replace('^', '', $dimensions);
}
elseif(strpos($dimensions, '!') !== false){
$mode = '!';
$dimensions = str_replace('!', '', $dimensions);
}
elseif(strpos($dimensions, '>') !== false){
$mode = '>';
$dimensions = str_replace('>', '', $dimensions);
}
elseif(strpos($dimensions, '<') !== false){
$mode = '<';
$dimensions = str_replace('<', '', $dimensions);
}
else{
$mode = '';
}
$vals   = explode('x', strtolower($dimensions));
$width  = (int)$vals[0];
$height = (int)$vals[1];
}
$ext = $file->getExtension();
if(!$ext){
$ext = mimetype_to_extension($file->getMimetype());
}
$key = str_replace(' ', '-', $file->getBasename(true)) . '-' . $file->getHash() . '-' . $width . 'x' . $height . $mode . '.' . $ext;
$dir = dirname($file->getFilename(false)) . '/';
if(substr($dir, 0, 7) == 'public/'){
$dir = 'public/tmp/' . substr($dir, 7);
}
else{
$dir = 'public/tmp/';
}
return array(
'width'  => $width,
'height' => $height,
'mode'   => $mode,
'key'    => $key,
'ext'    => $ext,
'dir'    => $dir,
);
}
} // ENDING NAMESPACE Core\Filestore

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/File.interface.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore {
interface File {
const TYPE_ASSET = 'asset';
const TYPE_PUBLIC = 'public';
const TYPE_PRIVATE = 'private';
const TYPE_TMP = 'tmp';
const TYPE_OTHER = 'other';
public function getFilesize($formatted = false);
public function getMimetype();
public function getExtension();
public function getTitle();
public function getURL();
public function getPreviewURL($dimensions = "300x300");
public function getFilename($prefix = \ROOT_PDIR);
public function setFilename($filename);
public function getBasename($withoutext = false);
public function getBaseFilename($withoutext = false);
public function getDirectoryName();
public function getLocalFilename();
public function getHash();
public function getFilenameHash();
public function delete();
public function rename($newname);
public function isImage();
public function isText();
public function isPreviewable();
public function displayPreview($dimensions = "300x300", $includeHeader = true);
public function getMimetypeIconURL($dimensions = '32x32');
public function getQuickPreviewFile($dimensions = '300x300');
public function getPreviewFile($dimensions = '300x300');
public function inDirectory($path);
public function identicalTo($otherfile);
public function copyTo($dest, $overwrite = false);
public function copyFrom(File $src, $overwrite = false);
public function getContents();
public function putContents($data);
public function getContentsObject();
public function exists();
public function isReadable();
public function isWritable();
public function isLocal();
public function getMTime();
public function sendToUserAgent($forcedownload = false);
}
} // ENDING NAMESPACE Core\Filestore

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/Directory.interface.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore {
interface Directory {
public function ls($extension = null, $recursive = false);
public function isReadable();
public function isWritable();
public function exists();
public function mkdir();
public function rename($newname);
public function getPath();
public function setPath($path);
public function getBasename();
public function delete();
public function remove();
public function get($name);
}
} // ENDING NAMESPACE Core\Filestore

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/Factory.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore {
use Core\Filestore\Backends;
abstract class Factory {
protected static $_Files = array();
protected static $_Directories = array();
protected static $_ResolveCache = array();
public static function File($uri) {
if(isset(self::$_ResolveCache[$uri])){
$resolved = self::$_ResolveCache[$uri]->getFilename();
if(isset(self::$_Files[$resolved])){
return self::$_Files[$resolved];
}
}
if (strpos($uri, 'base64:') === 0){
$uri = base64_decode(substr($uri, 7));
}
if(strpos($uri, 'ftp://') === 0){
return new Backends\FileFTP($uri);
}
if(strpos($uri, ROOT_PDIR) === 0){
}
elseif(strpos($uri, ROOT_URL_NOSSL) === 0){
$uri = ROOT_PDIR . substr($uri, strlen(ROOT_URL_NOSSL));
}
elseif(strpos($uri, ROOT_URL_SSL) === 0){
$uri = ROOT_PDIR . substr($uri, strlen(ROOT_URL_SSL));
}
if(strpos($uri, '://') !== false){
return new Backends\FileRemote($uri);
}
if(
strpos($uri, 'asset/') === 0 ||
strpos($uri, 'assets/') === 0 ||
strpos($uri, get_asset_path()) === 0
){
$file = resolve_asset_file($uri);
}
elseif(
strpos($uri, 'public/') === 0 ||
strpos($uri, get_public_path()) === 0
){
$file = resolve_public_file($uri);
}
elseif(
strpos($uri, 'private/') === 0 ||
strpos($uri, get_private_path()) === 0
){
$file = resolve_private_file($uri);
}
elseif(
strpos($uri, 'tmp/') === 0
){
$file = new Backends\FileLocal(get_tmp_path() . substr($uri, 4));
}
elseif(
strpos($uri, get_tmp_path()) === 0 ||
strpos($uri, '/tmp/') === 0
){
$file = new Backends\FileLocal($uri);
}
elseif(\Core\ftp() && EXEC_MODE == 'WEB'){
$file = new Backends\FileFTP($uri);
}
else{
$file = new Backends\FileLocal($uri);
}
self::$_Files[$file->getFilename()] = $file;
return $file;
}
static function Directory($uri){
if (strpos($uri, 'base64:') === 0){
$uri = base64_decode(substr($uri, 7));
}
if(strpos($uri, 'ftp://') === 0){
return new Backends\DirectoryFTP($uri);
}
if(
strpos($uri, 'asset/') === 0 ||
strpos($uri, 'assets/') === 0 ||
strpos($uri, get_asset_path()) === 0
){
return resolve_asset_directory($uri);
}
if(
strpos($uri, 'public/') === 0 ||
strpos($uri, get_public_path()) === 0
){
return resolve_public_directory($uri);
}
if(
strpos($uri, 'private/') === 0 ||
strpos($uri, get_private_path()) === 0
){
}
if(strpos($uri, 'tmp/') === 0){
return new Backends\DirectoryLocal(get_tmp_path() . substr($uri, 4));
}
elseif(strpos($uri, get_tmp_path()) === 0){
return new Backends\DirectoryLocal($uri);
}
return new Backends\DirectoryLocal($uri);
}
public static function ResolveAssetFile($filename){
return resolve_asset_file($filename);
}
public static function RemoveFromCache($file) {
if($file instanceof File){
$filename = $file->getFilename();
}
else{
$filename = $file;
}
if(isset(self::$_Files[$filename])){
unset(self::$_Files[$filename]);
}
$keys = array_keys(self::$_ResolveCache, $filename);
foreach($keys as $k){
unset(self::$_ResolveCache[$k]);
}
}
}
} // ENDING NAMESPACE Core\Filestore

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/Directory_Backend.interface.php
interface Directory_Backend {
public function __construct($directory);
public function ls();
public function isReadable();
public function isWritable();
public function mkdir();
public function rename($newname);
public function getPath();
public function getBasename();
public function remove();
public function get($name);
}


### REQUIRE_ONCE FROM core/libs/core/filestore/FileContentFactory.class.php
class FileContentFactory {
public static function GetFromFile(\Core\Filestore\File $file) {
return \Core\Filestore\resolve_contents_object($file);
}
}


### REQUIRE_ONCE FROM core/libs/core/filestore/Contents.interface.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore {
interface Contents {
public function __construct(File $file);
}
} // ENDING NAMESPACE Core\Filestore

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/contents/ContentXML.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore\Contents {
use Core\Filestore;
class ContentXML implements Filestore\Contents {
private $_file = null;
public function __construct(Filestore\File $file) {
$this->_file = $file;
}
public function getContents() {
return $this->_file->getContents();
}
public function getLoader(){
$xml = new \XMLLoader();
$xml->loadFromFile($this->_file);
return $xml;
}
}
} // ENDING NAMESPACE Core\Filestore\Contents

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/backends/FileLocal.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore\Backends {
use Core\Filestore;
use Core\Filestore\Factory;
class FileLocal implements Filestore\File {
public $_type = Filestore\File::TYPE_OTHER;
protected $_filename = null;
private $_filenamecache = [];
public function __construct($filename = null) {
if ($filename) $this->setFilename($filename);
}
public function getTitle(){
$metas = new Filestore\FileMetaHelper($this);
if(($t = $metas->getMetaTitle('title'))){
return $t;
}
else{
$title = $this->getBasename(true);
$title = preg_replace('/[^a-zA-Z0-9 ]/', ' ', $title);
$title = trim(preg_replace('/[ ]+/', ' ', $title));
$title = ucwords($title);
return $title;
}
}
public function getFilesize($formatted = false) {
$f = filesize($this->_filename);
return ($formatted) ? Filestore\format_size($f, 2) : $f;
}
public function getMimetype() {
if (!$this->exists()) return null;
if (!function_exists('finfo_open')) {
$cli = exec('file -ib "' . $this->_filename . '"');
list($type,) = explode(';', $cli);
$type = trim($type);
}
else {
$finfo = finfo_open(FILEINFO_MIME);
$type  = finfo_file($finfo, $this->_filename);
finfo_close($finfo);
}
if (($pos = strpos($type, ';')) !== false) $type = substr($type, 0, $pos);
$type = trim($type);
$ext = $this->getExtension();
if(
($ext == 'js' || $ext == 'csv' || $ext == 'css' || $ext == 'html' || $ext == 'fgl') &&
(strpos($type, 'text/') === 0)
){
$type = \Core\Filestore\extension_to_mimetype($ext);
}
elseif ($ext == 'ttf'  && $type == 'application/octet-stream') $type = 'font/ttf';
elseif ($ext == 'otf'  && $type == 'application/octet-stream') $type = 'font/otf';
return $type;
}
public function getExtension() {
return Filestore\get_extension_from_string($this->_filename);
}
public function getURL() {
if (!preg_match('/^' . str_replace('/', '\\/', ROOT_PDIR) . '/', $this->_filename)) return false;
return preg_replace('/^' . str_replace('/', '\\/', ROOT_PDIR) . '(.*)/', ROOT_URL . '$1', $this->_filename);
}
public function getFilename($prefix = ROOT_PDIR) {
if ($prefix == ROOT_PDIR) return $this->_filename;
if(!isset($this->_filenamecache[$prefix])){
if ($prefix === false) {
if ($this->_type == 'asset'){
$this->_filenamecache[$prefix] = 'asset/' . substr($this->_filename, strlen(Filestore\get_asset_path()));
}
elseif ($this->_type == 'public'){
$this->_filenamecache[$prefix] = 'public/' . substr($this->_filename, strlen(Filestore\get_public_path()));
}
elseif ($this->_type == 'private'){
$this->_filenamecache[$prefix] = 'private/' . substr($this->_filename, strlen(Filestore\get_private_path()));
}
elseif ($this->_type == 'tmp'){
$this->_filenamecache[$prefix] = 'tmp/' . substr($this->_filename, strlen(Filestore\get_tmp_path()));
}
elseif(strpos($this->_filename, ROOT_PDIR) === 0){
$this->_filenamecache[$prefix] = substr($this->_filename, strlen(ROOT_PDIR));
}
else{
$this->_filenamecache[$prefix] = $this->_filename;
}
}
else{
$this->_filenamecache[$prefix] = preg_replace('/^' . str_replace('/', '\\/', ROOT_PDIR) . '(.*)/', $prefix . '$1', $this->_filename);
}
}
return $this->_filenamecache[$prefix];
}
public function setFilename($filename) {
if($this->_filename){
Factory::RemoveFromCache($this);
$this->_filenamecache = [];
}
if ($filename{0} != '/') $filename = ROOT_PDIR . $filename; // Needs to be fully resolved
$filename = str_replace('//', '/', $filename);
$this->_filename = $filename;
if(strpos($this->_filename, Filestore\get_asset_path()) === 0){
$this->_type = 'asset';
}
elseif(strpos($this->_filename, Filestore\get_public_path()) === 0){
$this->_type = 'public';
}
elseif(strpos($this->_filename, Filestore\get_private_path()) === 0){
$this->_type = 'private';
}
elseif(strpos($this->_filename, Filestore\get_tmp_path()) === 0){
$this->_type = 'tmp';
}
}
public function getBasename($withoutext = false) {
$b = basename($this->_filename);
if ($withoutext) {
$ext = $this->getExtension();
if($ext != '') {
return substr($b, 0, (-1 - strlen($ext)));
}
}
return $b;
}
public function getBaseFilename($withoutext = false) {
return $this->getBasename($withoutext);
}
public function getDirectoryName(){
return dirname($this->_filename) . '/';
}
public function getLocalFilename() {
return $this->getFilename();
}
public function getFilenameHash() {
if ($this->_type == 'asset'){
$base = 'assets/';
$filename = substr($this->_filename, strlen(Filestore\get_asset_path()));
if(strpos($filename, \ConfigHandler::Get('/theme/selected') . '/') === 0){
$filename = substr($filename, strlen(\ConfigHandler::Get('/theme/selected')) + 1);
}
elseif(strpos($filename, 'default/') === 0){
$filename = substr($filename, 8);
}
$filename = $base . $filename;
}
elseif ($this->_type == 'public'){
$filename = 'public/' . substr($this->_filename, strlen(Filestore\get_public_path() ));
}
elseif ($this->_type == 'private'){
$filename = 'private/' . substr($this->_filename, strlen(Filestore\get_private_path() ));
}
elseif ($this->_type == 'tmp'){
$filename = 'tmp/' . substr($this->_filename, strlen(Filestore\get_tmp_path() ));
}
else{
$filename = $this->_filename;
}
return 'base64:' . base64_encode($filename);
}
public function getHash() {
if (!file_exists($this->_filename)) return null;
return md5_file($this->_filename);
}
public function delete() {
$ftp    = \Core\ftp();
$tmpdir = TMP_DIR;
if ($tmpdir{0} != '/') $tmpdir = ROOT_PDIR . $tmpdir; // Needs to be fully resolved
if(
!$ftp || // FTP not enabled or
(strpos($this->_filename, $tmpdir) === 0) // Destination is a temporary file.
){
if (!@unlink($this->getFilename())) return false;
$this->_filename = null;
return true;
}
else{
if(!ftp_delete($ftp, $this->getFilename())) return false;
$this->_filename = null;
return true;
}
}
public function rename($newname){
if($newname{0} != '/'){
$newname = substr($this->getFilename(), 0, 0 - strlen($this->getBaseFilename())) . $newname;
}
if(self::_Rename($this->getFilename(), $newname)){
$this->_filename = $newname;
return true;
}
return false;
}
public function copyTo($dest, $overwrite = false) {
if (!(is_a($dest, 'File') || $dest instanceof Filestore\File)) {
$file = $dest;
if ($file{0} != '/') {
$file = dirname($this->_filename) . '/' . $file;
}
if (substr($file, -1) == '/') {
$file .= $this->getBaseFilename();
}
$dest = Factory::File($file);
}
if ($this->identicalTo($dest)) return $dest;
$dest->copyFrom($this, $overwrite);
return $dest;
}
public function copyFrom(Filestore\File $src, $overwrite = false) {
if (!$overwrite) {
$c    = 0;
$ext  = $this->getExtension();
$base = $this->getBaseFilename(true);
$dir  = dirname($this->_filename);
$prefix = $dir . '/' . $base;
$suffix = (($ext == '') ? '' : '.' . $ext);
$thathash = $src->getHash();
$f = $prefix . $suffix;
while(file_exists($f) && md5_file($f) != $thathash){
$f = $prefix . '-' . ++$c . '' . $suffix;
}
$this->_filename = $f;
}
$localfilename = $src->getLocalFilename();
$modifiedtime = $src->getMTime();
$ftp    = \Core\ftp();
$tmpdir = TMP_DIR;
if ($tmpdir{0} != '/') $tmpdir = ROOT_PDIR . $tmpdir; // Needs to be fully resolved
$mode = (defined('DEFAULT_FILE_PERMS') ? DEFAULT_FILE_PERMS : 0644);
self::_Mkdir(dirname($this->_filename), null, true);
if (
!$ftp || // FTP not enabled or
(strpos($this->_filename, $tmpdir) === 0) // Destination is a temporary file.
) {
$maxbuffer = (1024 * 1024 * 10);
$handlein  = fopen($localfilename, 'r');
$handleout = fopen($this->_filename, 'w');
if(!$handlein){
throw new \Exception('Unable to open file ' . $localfilename . ' for reading.');
}
if(!$handleout){
throw new \Exception('Unable to open file ' . $this->_filename . ' for writing.');
}
while(!feof($handlein)){
fwrite($handleout, fread($handlein, $maxbuffer));
}
fclose($handlein);
fclose($handleout);
chmod($this->_filename, $mode);
touch($this->_filename, $modifiedtime);
return true;
}
else {
if (strpos($this->_filename, ROOT_PDIR) === 0){
$filename = substr($this->_filename, strlen(ROOT_PDIR));
}
else{
$filename = $this->_filename;
}
$ftp = \Core\ftp();
if (!ftp_put($ftp, $filename, $localfilename, FTP_BINARY)) {
throw new \Exception(error_get_last()['message']);
}
if (!ftp_chmod($ftp, $mode, $filename)){
throw new \Exception(error_get_last()['message']);
}
return true;
}
}
public function getContents() {
return file_get_contents($this->_filename);
}
public function putContents($data) {
self::_Mkdir(dirname($this->_filename), null, true);
$dmode = (defined('DEFAULT_DIRECTORY_PERMS') ? DEFAULT_DIRECTORY_PERMS : 0777);
if(!is_dir( dirname($this->_filename) )){
mkdir(dirname($this->_filename), $dmode, true);
}
if(!is_dir(dirname($this->_filename))){
throw new \Exception("Unable to make directory " . dirname($this->_filename) . ", please check permissions.");
}
$mode = (defined('DEFAULT_FILE_PERMS') ? DEFAULT_FILE_PERMS : 0644);
$ret = file_put_contents($this->_filename, $data);
if ($ret === false) return $ret;
chmod($this->_filename, $mode);
return true;
}
public function getContentsObject() {
return Filestore\resolve_contents_object($this);
}
public function isImage() {
$m = $this->getMimetype();
return (preg_match('/image\/jpeg|image\/png|image\/gif/', $m) ? true : false);
}
public function isText() {
$m = $this->getMimetype();
return (preg_match('/text\/.*|application\/x-shellscript/', $m) ? true : false);
}
public function isPreviewable() {
return ($this->isImage());
}
public function displayPreview($dimensions = "300x300", $includeHeader = true) {
$preview = $this->getPreviewFile($dimensions);
if ($includeHeader){
header('Content-Disposition: filename="' . $this->getBaseFilename(true) . '-' . $dimensions . '.' . $this->getExtension() . '"');
header('Content-Type: ' . $this->getMimetype());
header('Content-Length: ' . $preview->getFilesize());
header('X-Alternative-Location: ' . $preview->getURL());
header('X-Content-Encoded-By: Core Plus ' . (DEVELOPMENT_MODE ? \Core::GetComponent()->getVersion() : ''));
}
echo $preview->getContents();
return;
}
public function getMimetypeIconURL($dimensions = '32x32'){
$filemime = str_replace('/', '-', $this->getMimetype());
$file = Factory::File('assets/images/mimetypes/' . $filemime . '.png');
if(!$file->exists()){
if(DEVELOPMENT_MODE){
error_log('Unable to locate mimetype icon [' . $filemime . '], resorting to "unknown" (filename: ' . $this->getFilename('') . ')');
}
$file = Factory::File('assets/images/mimetypes/unknown.png');
}
return $file->getPreviewURL($dimensions);
}
public function getQuickPreviewFile($dimensions = '300x300'){
$bits   = \Core\Filestore\get_resized_key_components($dimensions, $this);
$width  = $bits['width'];
$height = $bits['height'];
$mode   = $bits['mode'];
$key    = $bits['key'];
if (!$this->exists()) {
error_log('File not found [ ' . $this->_filename . ' ]', E_USER_NOTICE);
$file = Factory::File('assets/images/mimetypes/notfound.png');
if(!$file->exists()){
trigger_error('The 404 image could not be located.', E_USER_WARNING);
return null;
}
$preview = $file->getPreviewFile($dimensions);
}
elseif ($this->isPreviewable()) {
if($width === false) return $this;
$currentdata = getimagesize($this->getFilename());
if(($mode == '' || $mode == '<') && $currentdata[0] <= $width){
return $this;
}
$preview = Factory::File($bits['dir'] . $bits['key']);
}
else {
$filemime = str_replace('/', '-', $this->getMimetype());
$file = Factory::File('assets/images/mimetypes/' . $filemime . '.png');
if(!$file->exists()){
if(DEVELOPMENT_MODE){
error_log('Unable to locate mimetype icon [' . $filemime . '], resorting to "unknown"');
}
$file = Factory::File('assets/images/mimetypes/unknown.png');
}
$preview = $file->getPreviewFile($dimensions);
}
return $preview;
}
public function getPreviewFile($dimensions = '300x300'){
if(ini_get('max_execution_time') && \Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->getTime() + 5 >= ini_get('max_execution_time')){
$filemime = str_replace('/', '-', $this->getMimetype());
$file = Factory::File('assets/images/mimetypes/' . $filemime . '.png');
if(!$file->exists()){
$file = Factory::File('assets/images/mimetypes/unknown.png');
}
return $file;
}
$file = $this->getQuickPreviewFile($dimensions);
$bits   = \Core\Filestore\get_resized_key_components($dimensions, $this);
$width  = $bits['width'];
$height = $bits['height'];
$mode   = $bits['mode'];
$key    = $bits['key'];
if($file == $this){
return $this;
}
if (!$this->exists()) {
return $file->getPreviewFile($dimensions);
}
elseif ($this->isPreviewable()) {
if($width === false) return $file;
$currentdata = getimagesize($this->getFilename());
if(($mode == '' || $mode == '<') && $currentdata[0] <= $width){
return $this;
}
if (!$file->exists()) {
$this->_resizeTo($file, $width, $height, $mode);
}
return $file;
}
else {
return $file->getPreviewFile($dimensions);
}
}
public function getPreviewURL($dimensions = "300x300") {
$file = $this->getPreviewFile($dimensions);
return $file->getURL();
}
public function inDirectory($path) {
if (strpos($path, ROOT_PDIR) === false) $path = ROOT_PDIR . $path;
return (strpos($this->_filename, $path) !== false);
}
public function identicalTo($otherfile) {
if (is_a($otherfile, 'File') || $otherfile instanceof Filestore\File) {
if($otherfile instanceof FileLocal){
if($this->getMTime() == $otherfile->getMTime() && $this->getFilesize() == $otherfile->getFilesize()){
return true;
}
}
return ($this->getHash() == $otherfile->getHash());
}
else {
if (!file_exists($otherfile)){
return false;
}
if (!file_exists($this->_filename)){
return false;
}
$result = exec('diff -q "' . $this->_filename . '" "' . $otherfile . '"', $array, $return);
return ($return == 0);
}
}
public function exists() {
return file_exists($this->_filename);
}
public function isReadable() {
return is_readable($this->_filename);
}
public function isWritable(){
if(file_exists($this->_filename)){
return is_writable($this->_filename);
}
else{
$dir = dirname($this->_filename);
if(is_dir($dir) && is_writable($dir)){
return true;
}
else{
return false;
}
}
}
public function isLocal() {
return true;
}
public function getMTime() {
if (!$this->exists()) return false;
return filemtime($this->getFilename());
}
public function sendToUserAgent($forcedownload = false) {
$view = \Core\view();
$request = \Core\page_request();
$view->mode = \View::MODE_NOOUTPUT;
$view->contenttype = $this->getMimetype();
$view->updated = $this->getMTime();
if($forcedownload){
$view->headers['Content-Disposition'] = 'attachment; filename="' . $this->getBasename() . '"';
$view->headers['Cache-Control'] = 'no-cache, must-revalidate';
$view->headers['Content-Transfer-Encoding'] = 'binary';
}
$view->headers['Content-Length'] = $this->getFilesize();
$view->render();
if($request->method != \PageRequest::METHOD_HEAD){
echo $this->getContents();
}
}
public static function _Mkdir($pathname, $mode = null, $recursive = false) {
$ftp    = \Core\ftp();
$tmpdir = TMP_DIR;
if ($tmpdir{0} != '/') $tmpdir = ROOT_PDIR . $tmpdir; // Needs to be fully resolved
if ($mode === null) {
$mode = (defined('DEFAULT_DIRECTORY_PERMS') ? DEFAULT_DIRECTORY_PERMS : 0777);
}
if (!$ftp) {
if(is_dir($pathname)){
return false;
}
else{
return mkdir($pathname, $mode, $recursive);
}
}
elseif (strpos($pathname, $tmpdir) === 0) {
if(is_dir($pathname)) return false;
else return mkdir($pathname, $mode, $recursive);
}
else {
if (strpos($pathname, ROOT_PDIR) === 0) $pathname = substr($pathname, strlen(ROOT_PDIR));
$paths = explode('/', $pathname);
foreach ($paths as $p) {
if(trim($p) == '') continue;
if (!@ftp_chdir($ftp, $p)) {
if (!ftp_mkdir($ftp, $p)) return false;
if (!ftp_chmod($ftp, $mode, $p)) return false;
ftp_chdir($ftp, $p);
}
}
return true;
}
}
public static function _Rename($oldpath, $newpath){
$ftp    = \Core\ftp();
if(!$ftp){
return rename($oldpath, $newpath);
}
else{
if (strpos($oldpath, ROOT_PDIR) === 0) $oldpath = substr($oldpath, strlen(ROOT_PDIR));
if (strpos($newpath, ROOT_PDIR) === 0) $newpath = substr($newpath, strlen(ROOT_PDIR));
return ftp_rename($ftp, $oldpath, $newpath);
}
}
public static function _PutContents($filename, $data) {
$ftp    = \Core\ftp();
$tmpdir = TMP_DIR;
if ($tmpdir{0} != '/') $tmpdir = ROOT_PDIR . $tmpdir; // Needs to be fully resolved
$mode = (defined('DEFAULT_FILE_PERMS') ? DEFAULT_FILE_PERMS : 0644);
if (!$ftp) {
$ret = file_put_contents($filename, $data);
if ($ret === false) return $ret;
chmod($filename, $mode);
return $ret;
}
elseif (strpos($filename, $tmpdir) === 0) {
$ret = file_put_contents($filename, $data);
if ($ret === false) return $ret;
chmod($filename, $mode);
return $ret;
}
else {
if (strpos($filename, ROOT_PDIR) === 0) $filename = substr($filename, strlen(ROOT_PDIR));
$tmpfile = $tmpdir . 'ftpupload-' . Core::RandomHex(4);
file_put_contents($tmpfile, $data);
if (!ftp_put($ftp, $filename, $tmpfile, FTP_BINARY)) {
unlink($tmpfile);
return false;
}
if (!ftp_chmod($ftp, $mode, $filename)) return false;
unlink($tmpfile);
return true;
}
}
private function _resizeTo(Filestore\File $file, $width, $height, $mode){
if(!$this->isImage()){
return;
}
\Core\Utilities\Logger\write_debug('Resizing image ' . $this->getFilename('') . ' to ' . $width . 'x' . $height . $mode);
$m = $this->getMimetype();
$file->putContents('');
if($m == 'image/gif' && exec('which convert 2>/dev/null')){
$resize = escapeshellarg($mode . $width . 'x' . $height);
exec('convert ' . escapeshellarg($this->getFilename()) . ' -resize ' . $resize . ' ' . escapeshellarg($file->getFilename()));
\Core\Utilities\Logger\write_debug('Resizing complete (via convert)');
return;
}
switch ($m) {
case 'image/jpeg':
$thumbType = 'JPEG';
$thumbWidth = $width;
$thumbHeight = $height;
if($width <= 200 && $height <= 200 && function_exists('exif_thumbnail')){
$img = exif_thumbnail($this->getFilename(), $thumbWidth, $thumbHeight, $thumbType);
if($img){
\Core\Utilities\Logger\write_debug('JPEG has thumbnail data of ' . $thumbWidth . 'x' . $thumbHeight . '!');
$file->putContents($img);
$img = imagecreatefromjpeg($file->getFilename());
}
else{
$img = imagecreatefromjpeg($this->getFilename());
}
}
else{
$img = imagecreatefromjpeg($this->getFilename());
}
break;
case 'image/png':
$img = imagecreatefrompng($this->getFilename());
break;
case 'image/gif':
$img = imagecreatefromgif($this->getFilename());
break;
default:
\Core\Utilities\Logger\write_debug('Resizing complete (failed, not sure what it was)');
return;
}
if ($img) {
$sW = imagesx($img);
$sH = imagesy($img);
$nW = $sW;
$nH = $sH;
switch($mode){
case '':
case '<':
if ($nW > $width) {
$nH = $width * $sH / $sW;
$nW = $width;
}
if ($nH > $height) {
$nW = $height * $sW / $sH;
$nH = $height;
}
break;
case '>':
if ($nW < $width) {
$nH = $width * $sH / $sW;
$nW = $width;
}
if ($nH < $height) {
$nW = $height * $sW / $sH;
$nH = $height;
}
break;
case '!':
$nW = $width;
$nH = $height;
break;
case '^':
$ratioheight = $sW / $height;
$ratiowidth  = $sH / $width;
if($ratioheight > 1 && $ratiowidth > 1){
if(($width * $sH / $sW) > ($height * $sW / $sH)){
$nH = $width * $sH / $sW;
$nW = $width;
}
else{
$nH = $height;
$nW = $height * $sW / $sH;
}
}
elseif($ratiowidth > $ratioheight){
$nW = $width;
$nH = round($width * $sH / $sW);
}
else{
$nH = $height;
$nW = round($height * $sW / $sH);
}
}
$img2 = imagecreatetruecolor($nW, $nH);
imagealphablending($img2, false);
imagesavealpha($img2, true);
imagealphablending($img, true);
imagecopyresampled($img2, $img, 0, 0, 0, 0, $nW, $nH, $sW, $sH);
imagedestroy($img);
switch ($m) {
case 'image/jpeg':
imagejpeg($img2, $file->getFilename(), 60);
\Core\Utilities\Logger\write_debug('Resizing complete (via imagejpeg)');
break;
case 'image/png':
imagepng($img2, $file->getFilename(), 9);
\Core\Utilities\Logger\write_debug('Resizing complete (via imagepng)');
break;
case 'image/gif':
imagegif($img2, $file->getFilename());
\Core\Utilities\Logger\write_debug('Resizing complete (via imagegif)');
break;
default:
\Core\Utilities\Logger\write_debug('Resizing complete (failed, not sure what it was)');
return;
}
}
}
}
} // ENDING NAMESPACE Core\Filestore\Backends

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/ftp/FTPConnection.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore\FTP {
use Core\Date\DateTime;
class FTPConnection {
private $conn;
public $username;
public $password;
public $host;
public $url;
public $root;
public $isLocal = false;
protected $metaFiles = [];
private static $_OpenConnections = [];
private $lastSave = 0;
private $connected = false;
public function getConn(){
$this->connect();
return $this->conn;
}
public function connect(){
if($this->connected){
return;
}
if(!$this->host){
throw new \Exception('Please set the host before connecting to an FTP server.');
}
if(!$this->root){
throw new \Exception('Please set the root path before connecting to an FTP server.');
}
$this->conn = ftp_connect($this->host);
if(!$this->conn){
throw new \Exception('Unable to connect to the FTP server at ' . $this->host);
}
if($this->username){
if(!ftp_login($this->conn, $this->username, $this->password)){
throw new \Exception('Bad FTP username or password for ' . $this->host);
}
$this->password = '--hidden--';
}
else{
if(!ftp_login($this->conn, 'anonymous', '')){
throw new \Exception('Anonymous logins are disabled for ' . $this->host);
}
}
ftp_set_option($this->conn, FTP_TIMEOUT_SEC, 600);
$this->reset();
if($this->host == '127.0.0.1'){
$this->isLocal = true;
}
$this->connected = true;
$this->lastSave = DateTime::NowGMT();
self::$_OpenConnections[] = $this;
if(sizeof(self::$_OpenConnections) == 1){
\HookHandler::AttachToHook('/core/shutdown', '\\Core\\Filestore\\FTP\\FTPConnection::ShutdownHook');
}
}
public function reset(){
if(!ftp_chdir($this->conn, $this->root)){
throw new \Exception('FTP functional, but root of [' . $this->root . '] was not valid or does not exist!');
}
}
public function getFileHash($filename){
$dir   = dirname($filename) . '/';
$file  = basename($filename);
$obj   = $this->getMetaFileObject($dir);
$metas = $obj->getMetas($file);
return isset($metas['hash']) ? $metas['hash'] : '';
}
public function getFileModified($filename){
$dir   = dirname($filename) . '/';
$file  = basename($filename);
$obj   = $this->getMetaFileObject($dir);
$metas = $obj->getMetas($file);
return isset($metas['modified']) ? $metas['modified'] : '';
}
public function getFileSize($filename){
$dir   = dirname($filename) . '/';
$file  = basename($filename);
$obj   = $this->getMetaFileObject($dir);
$metas = $obj->getMetas($file);
return isset($metas['size']) ? $metas['size'] : '';
}
public function setFileHash($filename, $hash){
$dir = dirname($filename) . '/';
$file = basename($filename);
$obj = $this->getMetaFileObject($dir);
$obj->set($file, 'hash', $hash);
$this->_syncMetas();
}
public function setFileModified($filename, $timestamp){
$dir = dirname($filename) . '/';
$file = basename($filename);
$obj = $this->getMetaFileObject($dir);
$obj->set($file, 'modified', $timestamp);
$this->_syncMetas();
}
public function setFileSize($filename, $size){
$dir = dirname($filename) . '/';
$file = basename($filename);
$obj = $this->getMetaFileObject($dir);
$obj->set($file, 'size', $size);
$this->_syncMetas();
}
public function getMetaFileObject($directory){
if(!isset($this->metaFiles[$directory])){
$this->metaFiles[$directory] = new FTPMetaFile($directory, $this);
}
return $this->metaFiles[$directory];
}
private function _syncMetas(){
if($this->lastSave + 25 >= DateTime::NowGMT()){
return;
}
$this->lastSave = DateTime::NowGMT();
foreach($this->metaFiles as $file){
$file->saveMetas();
}
}
public static function ShutdownHook(){
foreach(self::$_OpenConnections as $conn){
foreach($conn->metaFiles as $file){
$file->saveMetas();
}
}
}
}
} // ENDING NAMESPACE Core\Filestore\FTP

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/ftp/FTPMetaFile.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore\FTP {
use Core\Date\DateTime;
use Core\Filestore\Backends\FileLocal;
use Core\Filestore\Factory;
class FTPMetaFile {
private $_ftp;
private $_dir;
private $_contents;
private $_local;
private $_changed = false;
public function __construct($directory, $ftp){
$this->_dir = $directory;
$this->_ftp = $ftp;
}
public function getMetas($file){
$allkeys = ['filename', 'hash', 'modified', 'size'];
if($this->_contents === null){
$this->_contents = [];
$remotefile = $this->_dir . '.ftpmetas';
$f = md5($remotefile);
$this->_local = Factory::File('tmp/remotefile-cache/' . $f);
if(
(!$this->_local->exists()) ||
($this->_local->exists() && $this->_local->getMTime() + 1800 < DateTime::NowGMT())
){
if(ftp_size($this->_ftp->getConn(), $remotefile) != -1){
$this->_local->putContents('');
ftp_get($this->_ftp->getConn(), $this->_local->getFilename(), $remotefile, FTP_BINARY);
}
}
if(!$this->_local->exists()){
return array_merge($allkeys, ['filename' => $file]);
}
$fh = fopen($this->_local->getFilename(), 'r');
if(!$fh){
throw new \Exception('Unable to open ' . $this->_local->getFilename() . ' for reading.');
}
$line    = 0;
$map     = [];
$headers = [];
do{
$data = fgetcsv($fh, 2048);
if($data === null) break;
if($data === false) break;
$line++;
if($line == 1){
$map = $data;
foreach($data as $k => $v){
$headers[$v] = $k;
}
foreach($allkeys as $key){
if(!isset($headers[$key])){
$map[] = $key;
$headers[$key] = -1;
}
}
}
else{
$assoc = [];
foreach($map as $k => $v){
$assoc[$v] = isset($data[$k]) ? $data[$k] : '';
}
if(!isset($assoc['filename'])){
fclose($fh);
return array_merge($allkeys, ['filename' => $file]);
}
$this->_contents[ $assoc['filename'] ] = $assoc;
}
}
while(true);
}
return isset($this->_contents[$file]) ? $this->_contents[$file] : array_merge($allkeys, ['filename' => $file]);
}
public function set($file, $key, $value, $commit = false){
$this->getMetas($file);
if(!isset($this->_contents[$file])){
$this->_contents[$file] = [
'filename' => $file,
'hash' => '',
'modified' => '',
'size' => '',
];
}
$this->_contents[$file][$key] = $value;
$this->_changed = true;
if($commit){
$this->saveMetas();
}
}
public function saveMetas(){
if($this->_contents === null){
return;
}
if(!$this->_changed){
return;
}
$remotefile = $this->_dir . '.ftpmetas';
$this->_local->putContents('');
$fh = fopen($this->_local->getFilename(), 'w');
if(!$fh){
throw new \Exception('Unable to open ' . $this->_local->getFilename() . ' for writing.');
}
fputcsv($fh, ['filename', 'hash', 'modified', 'size']);
foreach($this->_contents as $c){
fputcsv($fh, array_values($c));
}
fclose($fh);
ftp_put($this->_ftp->getConn(), $remotefile, $this->_local->getFilename(), FTP_BINARY);
$this->_changed = false;
}
}
} // ENDING NAMESPACE Core\Filestore\FTP

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/backends/FileFTP.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore\Backends {
use Core\Filestore\Contents;
use Core\Filestore;
class FileFTP implements Filestore\File{
public $_type = Filestore\File::TYPE_OTHER;
protected $_ftp;
protected $_prefix;
protected $_filename;
protected $_tmplocal;
public function __construct($filename = null, $ftpobject = null) {
if($ftpobject !== null){
$this->_ftp = $ftpobject;
}
else{
$this->_ftp = \Core\ftp();
}
if($filename){
$this->setFilename($filename);
}
}
public function getFilesize($formatted = false) {
$filename = $this->getFilename();
if(($f = $this->_ftp->getFileSize($filename)) == ''){
$f = ftp_size($this->_ftp->getConn(), $filename);
$this->_ftp->setFileSize($filename, $f);
}
if($f == -1){
return 0;
}
return ($formatted) ? Filestore\format_size($f, 2) : $f;
}
public function getMimetype() {
if(!$this->exists()){
return '';
}
return $this->_getTmpLocal()->getMimetype();
}
public function getTitle(){
$metas = new Filestore\FileMetaHelper($this);
if(($t = $metas->getMetaTitle('title'))){
return $t;
}
else{
$title = $this->getBasename(true);
$title = preg_replace('/[^a-zA-Z0-9 ]/', ' ', $title);
$title = trim(preg_replace('/[ ]+/', ' ', $title));
$title = ucwords($title);
return $title;
}
}
public function getExtension() {
return \Core::GetExtensionFromString($this->getBasename());
}
public function getURL() {
if($this->_ftp->isLocal){
$file = $this->_getTmpLocal();
return $file->getURL();
}
else{
return (SSL ? 'https' : 'http') . '://' . $this->_ftp->url . $this->_filename;
}
}
public function getPreviewURL($dimensions = "300x300") {
if(!$this->exists()){
$file = Filestore\Factory::File('assets/images/mimetypes/notfound.png');
if(!$file->exists()){
trigger_error('The 404 image could not be located.', E_USER_WARNING);
return null;
}
return $file->getPreviewURL($dimensions);
}
else{
$file = $this->getPreviewFile($dimensions);
return $file->getURL();
}
}
public function getFilename($prefix = \ROOT_PDIR) {
if($this->_ftp->isLocal){
return $this->_getTmpLocal()->getFilename($prefix);
}
else{
$full = $this->_prefix . $this->_filename;
if ($prefix === false) {
if ($this->_type == 'asset'){
return 'asset/' . substr($full, strlen(Filestore\get_asset_path()));
}
elseif ($this->_type == 'public'){
return 'public/' . substr($full, strlen(Filestore\get_public_path()));
}
elseif ($this->_type == 'private'){
return 'private/' . substr($full, strlen(Filestore\get_private_path()));
}
elseif ($this->_type == 'tmp'){
return 'tmp/' . substr($full, strlen(Filestore\get_tmp_path()));
}
else{
return $this->_filename;
}
}
else{
return $full;
}
}
}
public function getBasename($withoutext = false) {
$b = basename($this->_filename);
if ($withoutext) {
$ext = $this->getExtension();
if($ext != '') {
return substr($b, 0, (-1 - strlen($ext)));
}
}
return $b;
}
public function getBaseFilename($withoutext = false) {
return $this->getBasename($withoutext);
}
public function getDirectoryName(){
return dirname($this->getFilename()) . '/';
}
public function getLocalFilename() {
return $this->_getTmpLocal()->getFilename();
}
public function getHash() {
if(!$this->exists()){
return null;
}
if($this->_ftp->isLocal){
return md5_file(ROOT_PDIR . $this->_filename);
}
if(($hash = $this->_ftp->getFileHash($this->getFilename())) == ''){
$this->_getTmpLocal();
$hash = $this->_ftp->getFileHash($this->getFilename());
}
return $hash;
}
public function getFilenameHash() {
$full = $this->getFilename();
if ($this->_type == 'asset'){
$base = 'assets/';
$filename = substr($full, strlen(Filestore\get_asset_path()));
if(strpos($filename, \ConfigHandler::Get('/theme/selected') . '/') === 0){
$filename = substr($filename, strlen(\ConfigHandler::Get('/theme/selected')) + 1);
}
elseif(strpos($filename, 'default/') === 0){
$filename = substr($filename, 8);
}
$filename = $base . $filename;
}
elseif ($this->_type == 'public'){
$filename = 'public/' . substr($full, strlen(Filestore\get_public_path() ));
}
elseif ($this->_type == 'private'){
$filename = 'private/' . substr($full, strlen(Filestore\get_private_path() ));
}
elseif ($this->_type == 'tmp'){
$filename = 'tmp/' . substr($full, strlen(Filestore\get_tmp_path() ));
}
else{
$filename = $full;
}
return 'base64:' . base64_encode($filename);
}
public function delete() {
return ftp_delete($this->_ftp->getConn(), $this->_filename);
}
public function rename($newname) {
$cwd = ftp_pwd($this->_ftp->getConn());
if(strpos($newname, ROOT_PDIR) === 0){
$newname = substr($newname, strlen(ROOT_PDIR));
}
elseif(strpos($newname, $cwd) === 0){
$newname = substr($newname, strlen($cwd));
}
else{
$newname = dirname($this->_filename) . '/' . $newname;
}
$status = ftp_rename($this->_ftp->getConn(), $this->_filename, $newname);
if($status){
$this->_filename = $newname;
$this->_tmplocal = null;
}
return $status;
}
public function isImage() {
$m = $this->getMimetype();
return (preg_match('/image\/jpeg|image\/png|image\/gif/', $m) ? true : false);
}
public function isText() {
$m = $this->getMimetype();
return (preg_match('/text\/.*|application\/x-shellscript/', $m) ? true : false);
}
public function isPreviewable() {
return ($this->isImage());
}
public function displayPreview($dimensions = "300x300", $includeHeader = true) {
if($this->_ftp->isLocal){
$this->_getTmpLocal()->displayPreview($dimensions, $includeHeader);
}
else{
$bits         = \Core\Filestore\get_resized_key_components($dimensions, $this);
$resized      = Filestore\Factory::File($bits['dir'] . $bits['key']);
$localresized = null;
$view         = \Core\view();
if(!$resized->exists()){
$local = $this->_getTmpLocal();
$localresized = $local->getPreviewFile($dimensions);
$localresized->copyTo($resized);
}
if ($includeHeader){
$view->contenttype = $this->getMimetype();
$view->updated = $this->getMTime();
$view->addHeader('Content-Length', $resized->getFilesize());
$view->addHeader('X-Alternative-Location', $resized->getURL());
$view->mode = \View::MODE_NOOUTPUT;
$view->render();
}
echo $localresized ? $localresized->getContents() : $resized->getContents();
}
}
public function getMimetypeIconURL($dimensions = '32x32') {
return $this->_getTmpLocal()->getMimetypeIconURL($dimensions);
}
public function getQuickPreviewFile($dimensions = '300x300') {
if($this->_ftp->isLocal){
return $this->_getTmpLocal()->getQuickPreviewFile($dimensions);
}
else{
$bits         = \Core\Filestore\get_resized_key_components($dimensions, $this);
$resized      = Filestore\Factory::File($bits['dir'] . $bits['key']);
return $resized;
}
}
public function getPreviewFile($dimensions = '300x300') {
if($this->_ftp->isLocal){
return $this->_getTmpLocal()->getPreviewFile($dimensions);
}
else{
$bits         = \Core\Filestore\get_resized_key_components($dimensions, $this);
$resized      = Filestore\Factory::File($bits['dir'] . $bits['key']);
if(!$resized->exists()){
$local = $this->_getTmpLocal();
$localresized = $local->getPreviewFile($dimensions);
$localresized->copyTo($resized);
}
return $resized;
}
}
public function inDirectory($path) {
return (strpos($this->_prefix . $this->_filename, $path) !== false);
}
public function identicalTo($otherfile) {
$thish = $this->getHash();
$thath = $otherfile->getHash();
return ($thish == $thath);
}
public function copyTo($dest, $overwrite = false) {
if (!(is_a($dest, 'File') || $dest instanceof Filestore\File)) {
$file = $dest;
if (substr($file, -1) == '/') {
$file .= $this->getBaseFilename();
}
$dest = Filestore\Factory::File($file);
}
if ($this->identicalTo($dest)) return $dest;
$dest->copyFrom($this, $overwrite);
return $dest;
}
public function copyFrom(Filestore\File $src, $overwrite = false) {
if (!$overwrite) {
$c    = 0;
$ext  = $this->getExtension();
$base = $this->getBaseFilename(true);
$dir  = dirname($this->_filename);
$prefix = $dir . '/' . $base;
$suffix = (($ext == '') ? '' : '.' . $ext);
$thathash = $src->getHash();
$f = $prefix . $suffix;
while(file_exists($f) && md5_file($f) != $thathash){
$f = $prefix . ' (' . ++$c . ')' . $suffix;
}
$this->_filename = $f;
}
$localfilename = $src->getLocalFilename();
$localhash     = $src->getHash();
$localmodified = $src->getMTime();
$localsize     = $src->getFilesize();
$mode = (defined('DEFAULT_FILE_PERMS') ? DEFAULT_FILE_PERMS : 0644);
$this->_mkdir(dirname($this->_filename), null, true);
if (!ftp_put($this->_ftp->getConn(), $this->_filename, $localfilename, FTP_BINARY)) {
throw new \Exception(error_get_last()['message']);
}
if (!ftp_chmod($this->_ftp->getConn(), $mode, $this->_filename)){
throw new \Exception(error_get_last()['message']);
}
$filename = $this->getFilename();
$this->_ftp->setFileHash($filename, $localhash);
$this->_ftp->setFileModified($filename, $localmodified);
$this->_ftp->setFileSize($filename, $localsize);
return true;
}
public function getContents() {
$local = $this->_getTmpLocal();
return $local->getContents();
}
public function putContents($data) {
$mode = (defined('DEFAULT_FILE_PERMS') ? DEFAULT_FILE_PERMS : 0644);
$tmpfile = Filestore\get_tmp_path() . 'ftpupload-' . \Core::RandomHex(4);
file_put_contents($tmpfile, $data);
if (!ftp_put($this->_ftp->getConn(), $this->_filename, $tmpfile, FTP_BINARY)) {
unlink($tmpfile);
return false;
}
if (!ftp_chmod($this->_ftp->getConn(), $mode, $this->_filename)) return false;
unlink($tmpfile);
$this->_tmplocal = null;
return true;
}
public function getContentsObject() {
return $this->_getTmpLocal()->getContentsObject();
}
public function exists() {
$filename = $this->getFilename();
if(($f = $this->_ftp->getFileSize($filename)) == ''){
$f = ftp_size($this->_ftp->getConn(), $filename);
$this->_ftp->setFileSize($filename, $f);
}
return ($f != -1);
}
public function isReadable() {
return $this->exists();
}
public function isWritable() {
return ($this->_ftp->username != '');
}
public function isLocal(){
return false;
}
public function getMTime() {
if (!$this->exists()) return false;
return ftp_mdtm($this->_ftp->getConn(), $this->_filename);
}
public function setFilename($filename) {
if($this->_filename){
Filestore\Factory::RemoveFromCache($this);
}
$cwd = $this->_ftp->root;
if(strpos($filename, ROOT_PDIR) === 0){
$filename = substr($filename, strlen(ROOT_PDIR));
$prefix = ROOT_PDIR;
}
elseif(strpos($filename, $cwd) === 0){
$filename = substr($filename, strlen($cwd));
$prefix = $cwd;
}
else{
$prefix = $cwd;
}
if(substr($prefix, -1) != '/') $prefix .= '/';
if(strpos($prefix . $filename, Filestore\get_asset_path()) === 0){
$this->_type = 'asset';
}
elseif(strpos($prefix . $filename, Filestore\get_public_path()) === 0){
$this->_type = 'public';
}
elseif(strpos($prefix . $filename, Filestore\get_private_path()) === 0){
$this->_type = 'private';
}
$this->_filename = $filename;
$this->_prefix = $prefix;
$this->_tmplocal = null;
}
public function sendToUserAgent($forcedownload = false) {
$view = \Core\view();
$request = \Core\page_request();
$view->mode = \View::MODE_NOOUTPUT;
$view->contenttype = $this->getMimetype();
$view->updated = $this->getMTime();
if($forcedownload){
$view->headers['Content-Disposition'] = 'attachment; filename="' . $this->getBasename() . '"';
$view->headers['Cache-Control'] = 'no-cache, must-revalidate';
$view->headers['Content-Transfer-Encoding'] = 'binary';
}
$view->headers['Content-Length'] = $this->getFilesize();
$view->render();
if($request->method != \PageRequest::METHOD_HEAD){
echo $this->getContents();
}
}
private function _mkdir($pathname, $mode = null, $recursive = false) {
if (strpos($pathname, ROOT_PDIR) === 0){
$pathname = substr($pathname, strlen(ROOT_PDIR));
}
if ($mode === null) {
$mode = (defined('DEFAULT_DIRECTORY_PERMS') ? DEFAULT_DIRECTORY_PERMS : 0777);
}
$paths = explode('/', $pathname);
$cwd = ftp_pwd($this->_ftp->getConn());
foreach ($paths as $p) {
if(trim($p) == '') continue;
if (!@ftp_chdir($this->_ftp->getConn(), $p)) {
if (!ftp_mkdir($this->_ftp->getConn(), $p)) return false;
if (!ftp_chmod($this->_ftp->getConn(), $mode, $p)) return false;
ftp_chdir($this->_ftp->getConn(), $p);
}
}
ftp_chdir($this->_ftp->getConn(), $cwd);
return true;
}
private function _getTmpLocal() {
if ($this->_tmplocal === null) {
if($this->_ftp->isLocal){
$this->_tmplocal = new FileLocal(ROOT_PDIR . $this->_filename);
}
else{
$filename = $this->getFilename();
$fhash = md5($filename);
$this->_tmplocal = Filestore\Factory::File('tmp/remotefile-cache/' . $fhash);
if(!$this->_tmplocal->exists()){
ftp_get($this->_ftp->getConn(), $this->_tmplocal->getFilename(), $filename, FTP_BINARY);
}
if(!$this->_ftp->getFileHash($filename)){
$this->_ftp->setFileHash($filename, $this->_tmplocal->getHash());
}
if(!$this->_ftp->getFileModified($filename)){
$this->_ftp->setFileModified($filename, ftp_mdtm($this->_ftp->getConn(), $filename));
}
if(!$this->_ftp->getFileSize($filename)){
$this->_ftp->setFileSize($filename, ftp_size($this->_ftp->getConn(), $filename));
}
}
}
return $this->_tmplocal;
}
}
} // ENDING NAMESPACE Core\Filestore\Backends

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/backends/FileRemote.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore\Backends {
use Core\Cache;
use Core\Filestore;
class FileRemote implements Filestore\File {
public $username = null;
public $password = null;
public $cacheable = true;
private $_url = null;
private $_headers = null;
private $_response = null;
private $_tmplocal = null;
protected $_redirectFile = null;
protected $_redirectCount = 0;
public function __construct($filename = null) {
if ($filename) $this->setFilename($filename);
}
public function getTitle(){
$metas = new Filestore\FileMetaHelper($this);
if(($t = $metas->getMetaTitle('title'))){
return $t;
}
else{
$title = $this->getBasename(true);
$title = preg_replace('/[^a-zA-Z0-9 ]/', ' ', $title);
$title = trim(preg_replace('/[ ]+/', ' ', $title));
$title = ucwords($title);
return $title;
}
}
public function getFilesize($formatted = false) {
$h = $this->_getHeaders();
if(isset($h['Content-Length'])){
$size = $h['Content-Length'];
}
else{
$tmp = $this->_getTmpLocal();
$size = $tmp->getFilesize(false);
}
return ($formatted) ? \Core::FormatSize($size, 2) : $size;
}
public function getMimetype() {
if (!$this->exists()) return null;
$h    = $this->_getHeaders();
$type = (isset($h['Content-Type'])) ? $h['Content-Type'] : null;
return $type;
}
public function getExtension() {
return \Core::GetExtensionFromString($this->getBasename());
}
public function getURL() {
if ($this->username && $this->password) {
$url = str_replace('://', '://' . $this->username . ':' . $this->password . '@', $this->_url);
}
elseif ($this->username) {
$url = str_replace('://', '://' . $this->username . '@', $this->_url);
}
else {
$url = $this->_url;
}
return $url;
}
public function getFilename($prefix = ROOT_PDIR) {
return $this->_url;
}
public function setFilename($filename) {
$this->_url = $filename;
}
public function getBaseFilename($withoutext = false) {
return $this->getBasename($withoutext);
}
public function getDirectoryName(){
return dirname($this->getFilename()) . '/';
}
public function getLocalFilename() {
return $this->_getTmpLocal()->getFilename();
}
public function getFilenameHash() {
return 'base64:' . base64_encode($this->_url);
}
public function getHash() {
$local = $this->_getTmpLocal();
return $local->getHash();
}
public function delete() {
throw new \Exception('Cannot delete a remote file!');
}
public function copyTo($dest, $overwrite = false) {
if (!(is_a($dest, 'File') || $dest instanceof Filestore\File)) {
$file = $dest;
if (substr($file, -1) == '/') {
$file .= $this->getBaseFilename();
}
$dest = Filestore\Factory::File($file);
}
if ($this->identicalTo($dest)) return $dest;
$dest->copyFrom($this, $overwrite);
return $dest;
}
public function copyFrom(Filestore\File $src, $overwrite = false) {
throw new \Exception('Unable to write to remote files!');
}
public function getContents() {
return $this->_getTmpLocal()->getContents();
}
public function putContents($data) {
throw new \Exception('Unable to write to remote files!');
}
public function getContentsObject() {
return Filestore\resolve_contents_object($this);
}
public function isImage() {
$m = $this->getMimetype();
return (preg_match('/image\/jpeg|image\/png|image\/gif/', $m) ? true : false);
}
public function isText() {
$m = $this->getMimetype();
return (preg_match('/text\/.*|application\/x-shellscript/', $m) ? true : false);
}
public function isPreviewable() {
return ($this->isImage());
}
public function displayPreview($dimensions = "300x300", $includeHeader = true) {
if (!$this->exists()) {
error_log('File not found [ ' . $this->_url . ' ]', E_USER_NOTICE);
$file = Filestore\Factory::File('asset/images/mimetypes/notfound.png');
$file->displayPreview($dimensions, $includeHeader);
}
else{
$file = $this->_getTmpLocal();
$file->displayPreview($dimensions, $includeHeader);
}
}
public function getPreviewURL($dimensions = "300x300") {
if (!$this->exists()) {
error_log('File not found [ ' . $this->_url . ' ]', E_USER_NOTICE);
$file = Filestore\Factory::File('asset/images/mimetypes/notfound.png');
return $file->getPreviewURL($dimensions);
}
else{
$file = $this->_getTmpLocal();
return $file->getPreviewURL($dimensions);
}
}
public function inDirectory($path) {
return (strpos($this->_url, $path) !== false);
}
public function identicalTo($otherfile) {
if (is_a($otherfile, 'File') || $otherfile instanceof Filestore\File) {
return ($this->_getTmpLocal()->getHash() == $otherfile->getHash());
}
else {
if (!file_exists($otherfile)){
return false;
}
$result = exec('diff -q "' . $this->_getTmpLocal()->getFilename() . '" "' . $otherfile . '"', $array, $return);
return ($return == 0);
}
}
public function exists() {
$this->_getHeaders();
return ($this->_response != 404);
}
public function isReadable() {
$this->_getHeaders();
return ($this->_response != 404);
}
public function isOK(){
$this->_getHeaders();
return ($this->_response == 200 || $this->_response == 301 || $this->_response == 302);
}
public function requiresAuthentication(){
$this->_getHeaders();
return ($this->_response == 401 || $this->_response == 403);
}
public function getStatus(){
$this->_getHeaders();
return $this->_response;
}
public function isLocal() {
return false;
}
public function getMTime() {
return false;
}
public function getBasename($withoutext = false) {
$basename = null;
$d = $this->_getHeader('Content-Disposition');
if($d !== null) {
$dParts = explode(';', $d);
foreach($dParts as $p) {
if(strpos($p, 'filename=') !== false) {
$value = trim(substr($p, strpos($p, '=') + 1), " '\"");
$value = str_replace('/', '-', $value);
$basename = $value;
}
}
}
if($basename === null && ($l = $this->_getHeader('Location'))){
$basename = $l;
}
if($basename === null){
$basename = $this->getFilename();
}
if (strpos($basename, '?') !== false) {
$basename = substr($basename, 0, strpos($basename, '?'));
}
$basename = basename($basename);
if ($withoutext) {
$ext = $this->getExtension();
if($ext != '') {
return substr($basename, 0, (-1 - strlen($ext)));
}
}
return $basename;
}
public function rename($newname) {
return false;
}
public function getMimetypeIconURL($dimensions = '32x32'){
$filemime = str_replace('/', '-', $this->getMimetype());
$file = Filestore\Factory::File('assets/images/mimetypes/' . $filemime . '.png');
if(!$file->exists()){
if(DEVELOPMENT_MODE){
error_log('Unable to locate mimetype icon [' . $filemime . '], resorting to "unknown" (filename: ' . $this->getFilename('') . ')');
}
$file = Filestore\Factory::File('assets/images/mimetypes/unknown.png');
}
return $file->getPreviewURL($dimensions);
}
public function getQuickPreviewFile($dimensions = '300x300') {
return $this->_getTmpLocal()->getQuickPreviewFile($dimensions);
}
public function getPreviewFile($dimensions = '300x300') {
return $this->_getTmpLocal()->getPreviewFile($dimensions);
}
public function isWritable() {
return false;
}
protected function _getHeaders() {
if ($this->_headers === null) {
$this->_headers = array();
$curl = curl_init();
curl_setopt_array(
$curl, array(
CURLOPT_HEADER         => true,
CURLOPT_NOBODY         => true,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_URL            => $this->getURL(),
CURLOPT_HTTPHEADER     => \Core::GetStandardHTTPHeaders(true),
)
);
$result = curl_exec($curl);
if($result === false){
switch(curl_errno($curl)){
case CURLE_COULDNT_CONNECT:
case CURLE_COULDNT_RESOLVE_HOST:
case CURLE_COULDNT_RESOLVE_PROXY:
$this->_response = 404;
break;
default:
$this->_response = 500;
break;
}
}
$h = explode("\n", $result);
curl_close($curl);
foreach ($h as $line) {
if (strpos($line, 'HTTP/1.') !== false) {
$this->_response = substr($line, 9, 3);
}
elseif (strpos($line, ':') !== false) {
$k                  = substr($line, 0, strpos($line, ':'));
$v                  = trim(substr($line, strpos($line, ':') + 1));
if($k == 'Content-Type' && strpos($v, 'charset=') !== false){
$this->_headers['Charset'] = substr($v, strpos($v, 'charset=') + 8);
$v = substr($v, 0, strpos($v, 'charset=') - 2);
}
$this->_headers[$k] = $v;
}
}
if(($this->_response == '302' || $this->_response == '301') && isset($this->_headers['Location'])){
$newcount = $this->_redirectCount + 1;
if($newcount <= 5){
$this->_redirectFile = new FileRemote();
$this->_redirectFile->_redirectCount = ($this->_redirectCount + 1);
$this->_redirectFile->setFilename($this->_headers['Location']);
$this->_redirectFile->_getHeaders();
}
else{
trigger_error('Too many redirects when requesting ' . $this->getURL(), E_USER_WARNING);
}
}
}
if(($this->_response == '302' || $this->_response == '301') && $this->_redirectFile !== null){
return $this->_redirectFile->_headers;
}
else{
return $this->_headers;
}
}
protected function _getHeader($header) {
$h = $this->_getHeaders();
return (isset($h[$header])) ? $h[$header] : null;
}
protected function _getTmpLocal() {
if ($this->_tmplocal === null) {
$f = md5($this->getFilename());
$needtodownload = true;
$this->_tmplocal = Filestore\Factory::File('tmp/remotefile-cache/' . $f);
if ($this->cacheable && $this->_tmplocal->exists()) {
$systemcachedata = Cache::Get('remotefile-cache-header-' . $f);
if ($systemcachedata && isset($systemcachedata['headers'])) {
if(isset($systemcachedata['headers']['Expires']) && strtotime($systemcachedata['headers']['Expires']) > time()){
$needtodownload = false;
$this->_headers = $systemcachedata['headers'];
$this->_response = $systemcachedata['response'];
}
elseif ($this->_getHeader('ETag') && isset($systemcachedata['headers']['ETag'])) {
$needtodownload = ($this->_getHeader('ETag') != $systemcachedata['headers']['ETag']);
}
elseif ($this->_getHeader('Last-Modified') && isset($systemcachedata['headers']['Last-Modified'])) {
$needtodownload = ($this->_getHeader('Last-Modified') != $systemcachedata['headers']['Last-Modified']);
}
}
}
if ($needtodownload || !$this->cacheable) {
$this->_getHeaders();
if(($this->_response == '302' || $this->_response == '301') && $this->_redirectFile !== null){
$this->_tmplocal = $this->_redirectFile->_getTmpLocal();
}
else{
$curl = curl_init();
curl_setopt_array(
$curl, array(
CURLOPT_HEADER         => false,
CURLOPT_NOBODY         => false,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_URL            => $this->getURL(),
CURLOPT_HTTPHEADER     => \Core::GetStandardHTTPHeaders(true),
)
);
$result = curl_exec($curl);
if($result === false){
switch(curl_errno($curl)){
case CURLE_COULDNT_CONNECT:
case CURLE_COULDNT_RESOLVE_HOST:
case CURLE_COULDNT_RESOLVE_PROXY:
$this->_response = 404;
return $this->_tmplocal;
break;
default:
$this->_response = 500;
return $this->_tmplocal;
break;
}
}
curl_close($curl);
$this->_tmplocal->putContents($result);
}
Cache::Set(
'remotefile-cache-header-' . $f,
[
'headers'  => $this->_getHeaders(),
'response' => $this->_response,
]
);
}
}
return $this->_tmplocal;
}
public function sendToUserAgent($forcedownload = false) {
$view = \Core\view();
$request = \Core\page_request();
$view->mode = \View::MODE_NOOUTPUT;
$view->contenttype = $this->getMimetype();
$view->updated = $this->getMTime();
if($forcedownload){
$view->headers['Content-Disposition'] = 'attachment; filename="' . $this->getBasename() . '"';
$view->headers['Cache-Control'] = 'no-cache, must-revalidate';
$view->headers['Content-Transfer-Encoding'] = 'binary';
}
$view->headers['Content-Length'] = $this->getFilesize();
$view->render();
if($request->method != \PageRequest::METHOD_HEAD){
echo $this->getContents();
}
}
}
} // ENDING NAMESPACE Core\Filestore\Backends

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/backends/DirectoryLocal.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore\Backends {
use Core\Filestore;
class DirectoryLocal implements Filestore\Directory {
private $_path;
private $_type;
private $_files = null;
public function __construct($directory) {
if (!is_null($directory)) {
$this->setPath($directory);
}
}
public function ls($extension = null, $recursive = false) {
if (!$this->isReadable()) return array();
if ($this->_files === null) $this->_sift();
$ret = array();
foreach ($this->_files as $file => $obj) {
if($extension){
if($obj instanceof Filestore\Directory && $recursive){
$ret = array_merge($ret, $obj->ls($extension, $recursive));
}
elseif($obj instanceof Filestore\File){
if($obj->getExtension() == $extension){
$ret[] = $obj;
}
}
}
elseif($recursive){
$ret[] = $obj;
if($obj instanceof Filestore\Directory && $recursive){
$ret = array_merge($ret, $obj->ls($extension, $recursive));
}
}
else{
$ret[] = $obj;
}
}
return $ret;
}
public function isReadable() {
return is_readable($this->_path);
}
public function isWritable() {
$ftp    = \Core\ftp();
$tmpdir = TMP_DIR;
if ($tmpdir{0} != '/') $tmpdir = ROOT_PDIR . $tmpdir; // Needs to be fully resolved
if (!$ftp) {
$testpath = $this->_path;
while($testpath && !is_dir($testpath)){
$testpath = substr($testpath, 0, strrpos($testpath, '/'));
}
return is_writable($testpath);
}
elseif (strpos($this->_path, $tmpdir) === 0) {
return is_writable($this->_path);
}
else {
return true;
}
}
public function exists(){
return (is_dir($this->getPath()));
}
public function mkdir() {
if($this->exists()) return null;
return mkdir($this->getPath(), DEFAULT_DIRECTORY_PERMS, true);
}
public function rename($newname) {
if($newname{0} != '/'){
$newname = substr($this->getPath(), 0, -1 - strlen($this->getBasename())) . $newname;
}
$status = rename($this->_path, $newname);
if($status){
$this->path = $newname;
$this->_files = null;
}
return $status;
}
public function getPath() {
return $this->_path;
}
public function setPath($path){
if(substr($path, -1) != '/'){
$path = $path . '/';
}
if ($path{0} != '/'){
$path = ROOT_PDIR . $path;
}
$path = preg_replace(':/+:', '/', $path);
$this->_path = $path;
if(strpos($this->_path, Filestore\get_asset_path()) === 0){
$this->_type = 'asset';
}
elseif(strpos($this->_path, Filestore\get_public_path()) === 0){
$this->_type = 'public';
}
elseif(strpos($this->_path, Filestore\get_tmp_path()) === 0){
$this->_type = 'tmp';
}
}
public function getBasename() {
return basename($this->_path);
}
public function delete() {
$ftp    = \Core\ftp();
$tmpdir = TMP_DIR;
if ($tmpdir{0} != '/') $tmpdir = ROOT_PDIR . $tmpdir; // Needs to be fully resolved
if(
!$ftp || // FTP not enabled or
(strpos($this->getPath(), $tmpdir) === 0) // Destination is a temporary directory.
){
$dirqueue = array($this->getPath());
$x        = 0;
do {
$x++;
foreach ($dirqueue as $k => $d) {
$isempty = true;
$dh      = opendir($d);
if (!$dh) return false;
while (($file = readdir($dh)) !== false) {
if ($file == '.') continue;
if ($file == '..') continue;
$isempty = false;
if (is_dir($d . $file)) $dirqueue[] = $d . $file . '/';
else unlink($d . $file);
}
closedir($dh);
if ($isempty) {
rmdir($d);
unset($dirqueue[$k]);
}
}
$dirqueue = array_unique($dirqueue);
krsort($dirqueue);
}
while (sizeof($dirqueue) && $x <= 10);
return true;
}
else{
foreach($this->ls() as $sub){
if($sub instanceof Filestore\File) $sub->delete();
else $sub->delete();
}
$path = $this->getPath();
if (strpos($path, ROOT_PDIR) === 0) $path = substr($path, strlen(ROOT_PDIR));
return ftp_rmdir($ftp, $path);
}
}
public function remove(){
return $this->delete();
}
public function get($name) {
$name    = trim($name, '/');
$parts   = explode('/', $name);
$lastkey = sizeof($parts) - 1; // -1 because 0-indexed arrays.
$obj = $this;
foreach ($parts as $k => $step) {
$listing = $obj->ls();
foreach ($listing as $l) {
if ($l->getBasename() == $step) {
if ($k == $lastkey) return $l;
$obj = $l;
continue 2;
}
}
return null;
}
return null;
}
public function getExtension(){
return null;
}
private function _sift() {
$this->_files = array();
$dh = opendir($this->_path);
if (!$dh) return;
while ($sub = readdir($dh)) {
if ($sub{0} == '.') continue;
if (is_dir($this->_path . $sub)) {
$this->_files[$sub] = new DirectoryLocal($this->_path . $sub);
}
else {
$this->_files[$sub] = new FileLocal($this->_path . $sub);
}
}
closedir($dh);
} // private function _sift()
}
} // ENDING NAMESPACE Core\Filestore\Backends

namespace  {

### REQUIRE_ONCE FROM core/libs/core/filestore/backends/DirectoryFTP.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Filestore\Backends {
use Core\Filestore;
class DirectoryFTP implements Filestore\Directory {
protected $_prefix;
protected $_path;
private $_type;
private $_files = null;
private $_ignores = array();
protected $_ftp;
protected $_islocal = false;
public function __construct($directory, $ftpobject = null) {
if($ftpobject !== null){
$this->_ftp = $ftpobject;
}
else{
$this->_ftp = \Core\ftp();
}
if($this->_ftp == \Core\ftp()){
$this->_islocal = true;
}
if (!is_null($directory)) {
$this->setPath($directory);
}
}
public function ls($extension = null, $recursive = false) {
if (!$this->isReadable()) return array();
if ($this->_files === null) $this->_sift();
$ret = array();
foreach ($this->_files as $file => $obj) {
if (sizeof($this->_ignores) && in_array($file, $this->_ignores)) continue;
if($extension){
if($obj instanceof Filestore\Directory && $recursive){
$ret = array_merge($ret, $obj->ls($extension, $recursive));
}
elseif($obj instanceof Filestore\File){
if($obj->getExtension() == $extension){
$ret[] = $obj;
}
}
}
elseif($recursive){
$ret[] = $obj;
if($obj instanceof Filestore\Directory && $recursive){
$ret = array_merge($ret, $obj->ls($extension, $recursive));
}
}
else{
$ret[] = $obj;
}
}
return $ret;
}
public function isReadable() {
return is_readable($this->getPath());
}
public function isWritable() {
return true;
}
public function exists(){
return (is_dir($this->getPath()));
}
public function mkdir() {
if($this->exists()) return null;
$mode = (defined('DEFAULT_DIRECTORY_PERMS') ? DEFAULT_DIRECTORY_PERMS : 0777);
$paths = explode('/', $this->_path);
foreach ($paths as $p) {
if(trim($p) == '') continue;
if (!@ftp_chdir($this->_ftp, $p)) {
if (!ftp_mkdir($this->_ftp, $p)) return false;
if (!ftp_chmod($this->_ftp, $mode, $p)) return false;
ftp_chdir($this->_ftp, $p);
}
}
return true;
}
public function rename($newname) {
$cwd = ftp_pwd($this->_ftp);
if(strpos($newname, ROOT_PDIR) === 0){
$newname = substr($newname, strlen(ROOT_PDIR));
}
elseif(strpos($newname, $cwd) === 0){
$newname = substr($newname, strlen($cwd));
}
else{
$newname = dirname($this->_path) . '/' . $newname;
}
$status = ftp_rename($this->_ftp, $this->_path, $newname);
if($status){
$this->_path = $newname;
$this->_files = null;
}
return $status;
}
public function getPath() {
return $this->_prefix . $this->_path;
}
public function setPath($path){
if(substr($path, -1) != '/'){
$path = $path . '/';
}
$cwd = ftp_pwd($this->_ftp);
if(strpos($path, ROOT_PDIR) === 0){
$path = substr($path, strlen(ROOT_PDIR));
$prefix = ROOT_PDIR;
}
elseif(strpos($path, $cwd) === 0){
$path = substr($path, strlen($cwd));
$prefix = $cwd;
}
else{
$prefix = $cwd;
}
if(substr($prefix, -1) != '/') $prefix .= '/';
$path = preg_replace(':/+:', '/', $path);
$this->_prefix = $prefix;
$this->_path = $path;
if(strpos($this->_path, Filestore\get_asset_path()) === 0){
$this->_type = 'asset';
}
elseif(strpos($this->_path, Filestore\get_public_path()) === 0){
$this->_type = 'public';
}
elseif(strpos($this->_path, Filestore\get_tmp_path()) === 0){
$this->_type = 'tmp';
}
}
public function getBasename() {
return basename($this->_path);
}
public function delete() {
$ftp    = \Core\ftp();
$tmpdir = TMP_DIR;
if ($tmpdir{0} != '/') $tmpdir = ROOT_PDIR . $tmpdir; // Needs to be fully resolved
if(
!$ftp || // FTP not enabled or
(strpos($this->getPath(), $tmpdir) === 0) // Destination is a temporary directory.
){
$dirqueue = array($this->getPath());
$x        = 0;
do {
$x++;
foreach ($dirqueue as $k => $d) {
$isempty = true;
$dh      = opendir($d);
if (!$dh) return false;
while (($file = readdir($dh)) !== false) {
if ($file == '.') continue;
if ($file == '..') continue;
$isempty = false;
if (is_dir($d . $file)) $dirqueue[] = $d . $file . '/';
else unlink($d . $file);
}
closedir($dh);
if ($isempty) {
rmdir($d);
unset($dirqueue[$k]);
}
}
$dirqueue = array_unique($dirqueue);
krsort($dirqueue);
}
while (sizeof($dirqueue) && $x <= 10);
return true;
}
else{
foreach($this->ls() as $sub){
if($sub instanceof Filestore\File) $sub->delete();
else $sub->delete();
}
$path = $this->getPath();
if (strpos($path, ROOT_PDIR) === 0) $path = substr($path, strlen(ROOT_PDIR));
return ftp_rmdir($ftp, $path);
}
}
public function remove(){
return $this->delete();
}
public function get($name) {
$name    = trim($name, '/');
$parts   = explode('/', $name);
$lastkey = sizeof($parts) - 1; // -1 because 0-indexed arrays.
$obj = $this;
foreach ($parts as $k => $step) {
$listing = $obj->ls();
foreach ($listing as $l) {
if ($l->getBasename() == $step) {
if ($k == $lastkey) return $l;
$obj = $l;
continue 2;
}
}
return null;
}
return null;
}
public function getExtension(){
return null;
}
private function _sift() {
$this->_files = array();
$dh = opendir($this->getPath());
if (!$dh) return;
while ($sub = readdir($dh)) {
if ($sub{0} == '.') continue;
if (is_dir($this->getPath() . $sub)) {
$this->_files[$sub] = new DirectoryFTP($this->getPath() . $sub);
}
else {
$this->_files[$sub] = new FileFTP($this->getPath() . $sub);
}
}
closedir($dh);
} // private function _sift()
}
} // ENDING NAMESPACE Core\Filestore\Backends

namespace  {

### REQUIRE_ONCE FROM core/libs/core/ComponentFactory.php
abstract class ComponentFactory {
private static $_DBCache = null;
public static function _LookupComponentData($componentname) {
if (self::$_DBCache === null) {
self::$_DBCache = array();
try {
$res = Core\Datamodel\Dataset::Init()->table('component')->select('*')->execute();
}
catch (DMI_Exception $e) {
return false;
}
foreach ($res as $r) {
$n                  = strtolower($r['name']);
self::$_DBCache[$n] = $r;
}
}
$componentname = strtolower($componentname);
return (isset(self::$_DBCache[$componentname])) ? self::$_DBCache[$componentname] : null;
}
public static function Load($filename) {
$fh = fopen($filename, 'r');
if (!$fh) return null;
$line = fread($fh, 512);
fclose($fh);
if (strpos($line, 'http://corepl.us/api/2_4/component.dtd') !== false) {
return new Component_2_1($filename);
}
elseif (strpos($line, 'http://corepl.us/api/2_1/component.dtd') !== false) {
return new Component_2_1($filename);
}
else {
$name = substr($filename, 0, -14);
$name = substr($name, strrpos($name, '/') + 1);
return new Component($name);
}
}
public static function ResolveNameToFile($name) {
$name = strtolower($name);
if ($name == 'core') return 'core/component.xml';
elseif (file_exists(ROOT_PDIR . 'components/' . $name . '/component.xml')) return 'components/' . $name . '/component.xml';
else return false;
}
}


### REQUIRE_ONCE FROM core/libs/core/ComponentHandler.class.php
class ComponentHandler implements ISingleton {
private static $instance = null;
private $_componentCache = array();
private $_classes = array();
private $_widgets = array();
private $_viewClasses = array();
private $_scriptlibraries = array();
private $_loaded = false;
private $_loadedComponents = array();
private $_viewSearchDirs = array();
public $_dbcache = array();
private function __construct() {
$this->_componentCache['core'] = ComponentHandler::_Factory(ROOT_PDIR . 'core/component.xml');
$dh = opendir(ROOT_PDIR . 'components');
if (!$dh) return;
while ($file = readdir($dh)) {
if ($file{0} == '.') continue;
if (!is_dir(ROOT_PDIR . 'components/' . $file)) continue;
if (!is_readable(ROOT_PDIR . 'components/' . $file . '/component.xml')) continue;
$c = ComponentHandler::_Factory(ROOT_PDIR . 'components/' . $file . '/component.xml');
$file = strtolower($file);
if (!$c->isValid()) {
if (DEVELOPMENT_MODE) {
CAEUtils::AddMessage('Component ' . $c->getName() . ' appears to be invalid.');
}
continue;
}
$this->_componentCache[$file] = $c;
unset($c);
}
closedir($dh);
}
private function load() {
if ($this->_loaded) return;
try {
$res            = Core\Datamodel\Dataset::Init()->table('component')->select('*')->execute();
$this->_dbcache = array();
foreach ($res as $r) {
$n                  = strtolower($r['name']);
$this->_dbcache[$n] = $r;
}
}
catch (Exception $e) {
return;
}
foreach ($this->_componentCache as $n => $c) {
$c->load();
if (!isset($this->_dbcache[$n])) {
continue;
}
$c->_versionDB = $this->_dbcache[$n]['version'];
$c->enabled    = ($this->_dbcache[$n]['enabled']);
if (!$c->enabled) {
unset($this->_componentCache[$n]);
continue;
}
if (!$c->isValid()) {
if (DEVELOPMENT_MODE) {
echo 'Component ' . $c->getName() . ' appears to be invalid due to:<br/>' . $c->getErrors();
}
unset($this->_componentCache[$n]);
}
}
if (EXEC_MODE == 'CLI') {
$cli_component = $this->getComponent('CLI');
}
$list = $this->_componentCache;
do {
$size = sizeof($list);
foreach ($list as $n => $c) {
if ($c->isInstalled() && $c->isLoadable() && $c->loadFiles()) {
if ($c->needsUpdated()) {
$c->upgrade();
}
$this->_registerComponent($c);
unset($list[$n]);
continue;
}
if ($c->isInstalled() && $c->needsUpdated() && $c->isLoadable()) {
$c->upgrade();
$c->loadFiles();
$this->_registerComponent($c);
unset($list[$n]);
continue;
}
if (!$c->isInstalled() && DEVELOPMENT_MODE && $c->isLoadable()) {
$c->install();
$c->loadFiles();
$this->_registerComponent($c);
unset($list[$n]);
continue;
}
}
}
while ($size > 0 && ($size != sizeof($list)));
if (DEVELOPMENT_MODE) {
foreach ($list as $l) {
if ($l->error & Component_2_1::ERROR_WRONGEXECMODE) continue;
$msg = 'Could not load installed component ' . $l->getName() . ' due to requirement failed.<br/>' . $l->getErrors();
echo $msg . '<br/>';
}
}
$this->_loaded = true;
}
public function _registerComponent($c) {
$name = strtolower($c->getName());
if ($c->hasLibrary()) {
$this->_libraries = array_merge($this->_libraries, $c->getLibraryList());
foreach ($c->getIncludePaths() as $path) {
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}
$this->_scriptlibraries = array_merge($this->_scriptlibraries, $c->getScriptLibraryList());
}
if ($c->hasModule()) $this->_modules[$name] = $c->getVersionInstalled();
$this->_classes                 = array_merge($this->_classes, $c->getClassList());
$this->_viewClasses             = array_merge($this->_viewClasses, $c->getViewClassList());
$this->_widgets                 = array_merge($this->_widgets, $c->getWidgetList());
$this->_loadedComponents[$name] = $c;
}
public static function Singleton() {
if (is_null(self::$instance)) {
$cached = false;
if ($cached) {
self::$instance = unserialize($cached);
}
else {
self::$instance = new self();
}
self::$instance->load();
}
return self::$instance;
}
public static function GetInstance() {
return self::Singleton();
}
public static function GetComponent($componentName) {
$componentName = strtolower($componentName);
if (isset(ComponentHandler::Singleton()->_componentCache[$componentName])) return ComponentHandler::Singleton()->_componentCache[$componentName];
else return false;
}
public static function IsLibraryAvailable($name, $version = false, $operation = 'ge') {
$ch   = ComponentHandler::Singleton();
$name = strtolower($name);
if (!isset($ch->_libraries[$name])) {
return false;
}
elseif ($version !== false) {
return Core::VersionCompare($ch->_libraries[$name], $version, $operation);
}
else return true;
}
public static function IsJSLibraryAvailable($name, $version = false, $operation = 'ge') {
$ch   = ComponentHandler::Singleton();
$name = strtolower($name);
if (!isset($ch->_jslibraries[$name])) return false;
elseif ($version) return version_compare(str_replace('~', '-', $ch->_jslibraries[$name]->version), $version, $operation);
else return true;
}
public static function GetJSLibrary($library) {
$library = strtolower($library);
return ComponentHandler::Singleton()->_jslibraries[$library];
}
public static function LoadScriptLibrary($library) {
return Core::LoadScriptLibrary($library);
$library = strtolower($library);
$obj     = ComponentHandler::Singleton();
if (isset($obj->_scriptlibraries[$library])) {
return call_user_func($obj->_scriptlibraries[$library]);
}
else {
return false;
}
}
public static function IsComponentAvailable($name, $version = false, $operation = 'ge') {
$ch   = ComponentHandler::Singleton();
$name = strtolower($name);
if ($name == 'DB') return ComponentHandler::IsLibraryAvailable($name, $version, $operation);
if (!isset($ch->_loadedComponents[$name])) return false;
elseif ($version) return version_compare(str_replace('~', '-', $ch->_loadedComponents[$name]->getVersionInstalled()), $version, $operation);
else return true;
}
public static function IsViewClassAvailable($name, $casesensitive = true) {
if (!$casesensitive) $name = strtolower($name);
foreach (ComponentHandler::Singleton()->_viewClasses as $c => $l) {
if (!$casesensitive && strtolower($c) == $name) return $c;
elseif ($c == $name) return $c;
}
return false;
}
public static function CheckClass($classname) {
if (class_exists($classname)) return;
$classname = strtolower($classname);
if (isset(ComponentHandler::Singleton()->_classes[$classname])) {
require_once(ComponentHandler::Singleton()->_classes[$classname]);
}
}
public static function IsClassAvailable($classname) {
return (isset(self::Singleton()->_classes[$classname]));
}
public static function GetAllComponents() {
return ComponentHandler::Singleton()->_componentCache;
}
public static function GetLoadedComponents() {
$ret = array();
foreach (ComponentHandler::Singleton()->_loadedComponents as $c) {
$ret[] = $c;
}
return $ret;
}
public static function GetLoadedClasses() {
return ComponentHandler::Singleton()->_classes;
}
public static function GetLoadedWidgets() {
return ComponentHandler::Singleton()->_widgets;
}
public static function GetLoadedViewClasses() {
return ComponentHandler::Singleton()->_viewClasses;
}
public static function GetLoadedLibraries() {
return ComponentHandler::Singleton()->_libraries;
}
}


### REQUIRE_ONCE FROM core/libs/core/Cache.php
} // ENDING GLOBAL NAMESPACE
namespace Core {
define('__CACHE_PDIR', ROOT_PDIR . 'core/libs/core/cache/');
class Cache {
private static $_KeyCache = array();
private static $_Backend = null;
public static function Get($key, $expires = 7200){
$obj = self::_Factory($key, $expires);
return $obj->read();
}
public static function Set($key, $value, $expires = 7200){
$obj = self::_Factory($key, $expires);
if($obj->create($value)){
return true;
}
elseif($obj->update($value)){
return true;
}
else{
return false;
}
}
public static function Delete($key){
return self::_Factory($key)->delete();
}
public static function Flush(){
$s = self::_Factory('FLUSH')->flush();
self::$_KeyCache = array();
return $s;
}
private static function _Factory($key, $expires = 7200){
if(self::$_Backend === null){
$cs = \ConfigHandler::LoadConfigFile("configuration");
self::$_Backend = $cs['cache_type'];
}
if(isset(self::$_KeyCache[$key])){
return self::$_KeyCache[$key];
}
switch(self::$_Backend){
case 'apc':
if(!class_exists('CacheAPC')){
require_once(__CACHE_PDIR . 'backends/cacheapc.class.php'); ##SKIPCOMPILER
}
$obj = new CacheAPC($key, null, $expires);
break;
case 'memcache':
case 'memcached':
if(!class_exists('Core\Cache\Memcache')){
require_once(__CACHE_PDIR . 'Memcache.php'); ##SKIPCOMPILER
}
$obj = new Cache\Memcache($key, $expires);
break;
case 'file':
default:
if(!class_exists('Core\Cache\File')){
require_once(__CACHE_PDIR . 'File.php'); ##SKIPCOMPILER
}
if(!is_dir(TMP_DIR . 'cache')){
mkdir(TMP_DIR . 'cache');
}
$obj = new Cache\File($key, $expires);
break;
}
self::$_KeyCache[$key] = $obj;
return $obj;
}
}
} // ENDING NAMESPACE Core

namespace  {

### REQUIRE_ONCE FROM core/libs/core/cache/CacheInterface.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Cache {
interface CacheInterface {
public function __construct($key, $expires);
public function create($data);
public function read();
public function update($data);
public function delete();
public function flush();
}
} // ENDING NAMESPACE Core\Cache

namespace  {

### REQUIRE_ONCE FROM core/libs/core/cache/File.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Cache {
class File implements CacheInterface {
private $_key;
private $_expires;
private $_dir;
private $_file;
private $_gzip;
public function __construct($key, $expires) {
$this->_key = $key;
$this->_expires = $expires;
$this->_dir = TMP_DIR . 'cache/';
$this->_file = TMP_DIR . 'cache/' . $key . '.cache';
$this->_gzip = (extension_loaded('zlib'));
}
public function create($data) {
if (file_exists($this->_file)) {
return false;
}
elseif (file_exists($this->_dir) && is_writeable($this->_dir)) {
$data = serialize($data);
$data = $this->_gzip ? gzcompress($data) : $data;
return (bool) file_put_contents($this->_file, $data);
}
return false;
}
public function read() {
if(!file_exists($this->_file)){
return false;
}
elseif(!is_readable($this->_file)){
return false;
}
elseif($this->is_expired()){
return false;
}
else{
$data = file_get_contents($this->_file);
$data = $this->_gzip ? gzuncompress($data) : $data;
$data = unserialize($data);
if ($data === false) {
$this->delete();
return false;
}
return $data;
}
}
public function update($data) {
if (file_exists($this->_file) && is_writeable($this->_file)) {
$data = serialize($data);
$data = $this->_gzip ? gzcompress($data) : $data;
return (bool) file_put_contents($this->_file, $data);
}
return false;
}
public function delete() {
if (file_exists($this->_file)) {
return unlink($this->_file);
}
return false;
}
public function flush() {
$dir = opendir($this->_dir);
if(!$dir){
return true;
}
while(($file = readdir($dir)) !== false){
if($file == '.' || $file == '..') continue;
unlink($this->_dir . $file);
}
closedir($dir);
return true;
}
private function is_expired() {
clearstatcache();
if(filemtime($this->_file) + $this->_expires < time()){
return true;
}
else{
return false;
}
}
}
} // ENDING NAMESPACE Core\Cache

namespace  {

### REQUIRE_ONCE FROM core/libs/core/ViewControl.class.php
class ViewControls implements Iterator, ArrayAccess {
public $hovercontext = true;
private $_links = [];
private $_pos = 0;
private $_data = [];
public function current() {
return $this->_links[$this->_pos];
}
public function next() {
++$this->_pos;
}
public function key() {
return $this->_pos;
}
public function valid() {
return isset($this->_links[$this->_pos]);
}
public function rewind() {
$this->_pos = 0;
}
public function offsetExists($offset) {
return array_key_exists($offset, $this->_links);
}
public function offsetGet($offset) {
return $this->_links[$offset];
}
public function offsetSet($offset, $value) {
if($offset === null){
if($this->valid()){
$this->next();
}
$offset = $this->key();
}
if($value instanceof ViewControl){
$this->_links[$offset] = $value;
}
elseif(is_array($value)){
$control = new ViewControl();
foreach($value as $k => $v){
$control->set($k, $v);
}
if(!$control->icon){
switch($control->class){
case 'add':
case 'edit':
case 'directory':
$control->icon = $control->class;
break;
case 'delete':
$control->icon = 'remove';
break;
case 'view':
$control->icon = 'eye-open';
break;
}
}
$this->_links[] = $control;
}
else{
throw new Exception('Invalid offset type for ViewControls::offsetSet, please only set a ViewControl or an associative array');
}
}
public function offsetUnset($offset) {
unset($this->_links[$offset]);
}
public function addLinks(array $links){
foreach($links as $l){
$this[] = $l;
}
}
public function addLink($link){
$this[] = $link;
}
public function fetch(){
if(!$this->hasLinks()){
return '';
}
$ulclass = ['controls'];
if($this->hovercontext) $ulclass[] = 'controls-hover';
$atts = [];
$atts['class'] = implode(' ', $ulclass);
foreach($this->_data as $k => $v){
$atts['data-' . $k] = $v;
}
$html = '<ul ';
foreach($atts as $k => $v){
$html .= $k . '="' . str_replace('"', '&quot;', $v) . '" ';
}
$html .= '>';
foreach($this->_links as $l){
$html .= $l->fetch();
}
$html .= '</ul>';
return $html;
}
public function hasLinks(){
return (sizeof($this->_links) > 0);
}
public function setProxyText($text){
$this->_data['proxy-text'] = $text;
}
public function setProxyForce($force){
$this->_data['proxy-force'] = $force ? '1' : '0';
}
public static function Dispatch($baseurl, $subject){
$links = HookHandler::DispatchHook('/core/controllinks' . $baseurl, $subject);
$controls = new ViewControls();
$controls->addLinks($links);
return $controls;
}
public static function DispatchModel(\Model $model){
$baseurl = '/' . strtolower(get_class($model));
$firstlinks = $model->getControlLinks();
$additionallinks = HookHandler::DispatchHook('/core/controllinks' . $baseurl, $model);
$links = array_merge($firstlinks, $additionallinks);
$controls = new ViewControls();
$controls->addLinks($links);
return $controls;
}
public static function DispatchAndFetch($baseurl, $subject){
$links = HookHandler::DispatchHook('/core/controllinks' . $baseurl, $subject);
$controls = new ViewControls();
$controls->addLinks($links);
return $controls->fetch();
}
}
class ViewControl implements ArrayAccess {
public $link = '#';
public $title = '';
public $class = '';
public $icon = '';
public $confirm = null;
public $otherattributes = [];
public function fetch(){
$html = '';
if(!$this->icon){
switch($this->class){
case 'delete':
$this->icon = 'remove';
break;
case 'view':
$this->icon = 'eye-open';
break;
default:
$this->icon = $this->class;
break;
}
}
$html .= '<li' . ($this->class ? (' class="' . $this->class . '"') : '') . '>';
if($this->link){
$html .= $this->_fetchA();
}
if($this->icon){
$html .= '<i class="icon-' . $this->icon . '"></i> ';
}
$html .= '<span>' . $this->title . '</span>';
if($this->link){
$html .= '</a>';
}
$html .= '</li>';
return $html;
}
private function _fetchA(){
if(!$this->link) return null;
$dat = $this->otherattributes;
if($this->confirm !== null){
$dat['onclick'] = 'return Core.ConfirmEvent(this);';
$dat['data-href'] = \Core\resolve_link($this->link);
$dat['data-confirm'] = $this->confirm;
$dat['href'] = '#false';
}
else{
$dat['href'] = $this->link;
}
$dat['title'] = $this->title;
if($this->class) $dat['class'] = $this->class;
$html = '<a ';
foreach($dat as $k => $v){
$html .= " $k=\"$v\"";
}
$html .= '>';
return $html;
}
public function set($key, $value){
switch($key){
case 'class':
$this->class = $value;
break;
case 'confirm':
$this->confirm = $value;
break;
case 'icon':
$this->icon = $value;
break;
case 'link':
case 'url':
case 'href': // Just for an alias of the link.
$this->link = \Core\resolve_link($value);
break;
case 'title':
$this->title = $value;
break;
default:
$this->otherattributes[$key] = $value;
break;
}
}
public function offsetExists($offset) {
return(property_exists($this, $offset));
}
public function offsetGet($offset) {
$dat = get_object_vars($this);
if(isset($dat[$offset])){
return $dat[$offset];
}
elseif(isset($this->otherattributes[$offset])){
return $this->otherattributes[$offset];
}
else{
return null;
}
}
public function offsetSet($offset, $value) {
$this->set($offset, $value);
}
public function offsetUnset($offset) {
return void;
}
}


### REQUIRE_ONCE FROM core/libs/core/ViewMeta.class.php
class ViewMetas implements Iterator, ArrayAccess {
private $_links = array();
private $_pos = 0;
public function current() {
return $this->_links[$this->_pos];
}
public function next() {
++$this->_pos;
}
public function key() {
return $this->_pos;
}
public function valid() {
return isset($this->_links[$this->_pos]);
}
public function rewind() {
$this->_pos = 0;
}
public function offsetExists($offset) {
if(!isset($this->_links[$offset])){
return false;
}
$meta = $this->_links[$offset];
if($meta->content === null){
return false;
}
return true;
}
public function offsetGet($offset) {
return $this->_links[$offset];
}
public function offsetSet($offset, $value) {
if($offset === null){
if($this->valid()){
$this->next();
}
$offset = $this->key();
}
if($value instanceof ViewMeta || is_subclass_of($value, 'ViewMeta')){
if(isset($this->_links[$offset])){
$existingmeta = $this->_links[$offset];
if($existingmeta->multiple){
if(!is_array($existingmeta->content)){
$existingmeta->content = array(
$existingmeta->contentkey => $existingmeta->content
);
$existingmeta->contentkey = null;
}
$existingmeta->content[ $value->contentkey ] = $value->content;
}
else{
$existingmeta->contentkey = $value->contentkey;
$existingmeta->content = $value->content;
}
}
else{
$this->_links[$offset] = $value;
}
return;
}
if(isset($this->_links[$offset])){
$meta = $this->_links[$offset];
}
else{
$meta = ViewMeta::Factory($offset);
$meta->parent = $this;
$this->_links[$offset] = $meta;
}
$meta->content = $value;
}
public function offsetUnset($offset) {
unset($this->_links[$offset]);
}
public function addLinks(array $links){
foreach($links as $l){
$this[] = $l;
}
}
public function fetch(){
$data = array();
foreach($this->_links as $l){
$ea = $l->fetch();
if(is_array($ea) && sizeof($ea)){
$data = array_merge($data, $l->fetch());
}
}
return $data;
}
}
class ViewMeta {
const BASE_META = 'meta';
const BASE_LINK = 'link';
public $base = ViewMeta::BASE_META;
public $href = '';
public $property = '';
public $contentkey = '';
public $content = '';
public $otherattributes = array();
public $parent;
public $multiple = false;
public function __toString(){
if($this->content === false || $this->content === null){
return '';
}
elseif(is_array($this->content)){
return implode("\n<br/>", $this->content);
}
else{
return $this->content;
}
}
public function fetch(){
switch($this->base){
case ViewMeta::BASE_META: return $this->_fetchMeta();
case ViewMeta::BASE_LINK: return $this->_fetchLink();
}
}
private function _fetchMeta(){
if(!$this->content) return '';
return array(
$this->property => '<meta property="' . $this->property . '" content="' . str_replace('"', '&quot;', $this->content) . '"/>'
);
}
private function _fetchLink(){
die('finish fetchLink');
}
public static function Factory($property){
$classcheck = 'ViewMeta_' . preg_replace('/[^a-zA-Z]/', '_', $property);
if(class_exists($classcheck)){
$meta = new $classcheck();
}
else{
$meta = new ViewMeta();
}
$meta->property = $property;
return $meta;
}
}
class ViewMeta_description extends ViewMeta {
public function fetch(){
if(!$this->content) return array();
$content = $this->content;
if(strlen($content) > 300) $content = substr($content, 0, 297) . '...';
return array('description' => '<meta name="description" content="' . str_replace('"', '&quot;', $content) . '"/>');
}
}
class ViewMeta_keyword extends ViewMeta {
public function __construct(){
$this->multiple = true;
}
public function fetch(){
if(!$this->content) return array();
if(is_array($this->content)){
$keywords = implode(',', $this->content);
}
else{
$keywords = $this->content;
}
return array('keywords' => '<meta name="keywords" content="' . str_replace('"', '&quot;', $keywords) . '"/>');
}
}
class ViewMeta_name extends ViewMeta {
public function fetch(){
if(!$this->content) return array();
return array('name' => '<meta name="name" content="' . str_replace('"', '&quot;', $this->content) . '"/>');
}
}
class ViewMeta_title extends ViewMeta {
public function __toString(){
if(strpos($this->content, 't:') === 0){
return t(substr($this->content, 2));
}
else{
return $this->content;
}
}
}
class ViewMeta_author extends ViewMeta {
public function __toString(){
if(is_subclass_of($this->content, 'User')){
return $this->content->getDisplayName();
}
else{
return $this->content;
}
}
public function fetch(){
if($this->contentkey){
$authorid = $this->contentkey;
}
else{
$authorid = null;
}
if(!$this->content) return '';
$data = array();
if(is_subclass_of($this->content, 'User')){
$data['author'] = '<meta property="author" content="' . str_replace('"', '&quot;', $this->content->getDisplayName()) . '"/>';
if(Core::IsComponentAvailable('user-social')){
$data['link-author'] = '<link rel="author" href="' . UserSocialHelper::ResolveProfileLink($this->content) . '"/>';
}
}
elseif($authorid){
$user = UserModel::Construct($authorid);
$data['author'] = '<meta property="author" content="' . str_replace('"', '&quot;', $user->getDisplayName()) . '"/>';
if(Core::IsComponentAvailable('user-social')){
$data['link-author'] = '<link rel="author" href="' . UserSocialHelper::ResolveProfileLink($user) . '"/>';
}
}
else{
$data['author'] = '<meta property="author" content="' . str_replace('"', '&quot;', $this->content) . '"/>';
}
return $data;
}
}
class ViewMeta_canonical extends ViewMeta {
public function fetch(){
if(!$this->content) return '';
$data = array();
$data['link-canonical'] = '<link rel="canonical" href="' . $this->content . '" />';
$data['og:url'] = '<meta property="og:url" content="' . str_replace('"', '&quot;', $this->content) . '"/>';
return $data;
}
}
class ViewMeta_generator extends ViewMeta {
public function fetch(){
$generator = 'Core Plus';
if(DEVELOPMENT_MODE) $generator .= ' ' . Core::GetComponent()->getVersion();
return array(
'generator' => '<meta name="generator" content="' . $generator . '"/>'
);
}
}
class ViewMeta_image extends ViewMeta {
public function fetch(){
if(!$this->content) return array();
$image   = \Core\Filestore\Factory::File($this->content);
$apple   = $image->getPreviewURL('800x800');
$large   = $image->getPreviewURL('1500x1500');
$data = [];
$data['link-apple-touch-startup-image'] = '<link rel="apple-touch-startup-image" href="' . $apple . '" />';
$data['og:image'] = '<meta name="og:image" content="' . $large . '"/>';
return $data;
}
}


### REQUIRE_ONCE FROM core/libs/core/errormanagement/functions.php
} // ENDING GLOBAL NAMESPACE
namespace Core\ErrorManagement {
use Core\Utilities\Logger;
use Core\Utilities\Logger\LogFile;
function exception_handler(\Exception $e, $fatal = false){
$type  = 'error';
$class = $fatal ? 'error' : 'warning';
$code  = get_class($e);
$errstr  = $e->getMessage();
$errfile = $e->getFile();
$errline = $e->getLine();
if($errfile && strpos($errfile, ROOT_PDIR) === 0){
$details = '[src: ' . '/' . substr($errfile, strlen(ROOT_PDIR)) . ':' . $errline . '] ';
}
elseif($errfile){
$details = '[src: ' . $errfile . ':' . $errline . '] ';
}
else{
$details = '';
}
if($e instanceof \DMI_Query_Exception){
$details .= '[query: ' . $e->query . '] ';
}
try{
if(!\Core::GetComponent()){
return;
}
$log = \SystemLogModel::Factory();
$log->setFromArray([
'type'    => $type,
'code'    => $code,
'message' => $details . $errstr
]);
$log->save();
}
catch(\Exception $e){
try{
if(class_exists('Core\\Utilities\\Logger\\LogFile')){
$log = new LogFile($type);
$log->write($details . $errstr, $code);
}
else{
error_log($details . $errstr);
}
}
catch(\Exception $e){
}
}
if(DEVELOPMENT_MODE){
if(isset($_SERVER['TERM']) || isset($_SERVER['SHELL'])){
print_error_as_text($class, $code, $e);
}
elseif(EXEC_MODE == 'WEB'){
print_error_as_html($class, $code, $e);
}
else{
print_error_as_text($class, $code, $e);
}
}
if($fatal){
if(EXEC_MODE == 'WEB'){
require(ROOT_PDIR . 'core/templates/halt_pages/fatal_error.inc.html');
}
exit();
}
}
function error_handler($errno, $errstr, $errfile, $errline, $errcontext = null){
$type       = null;
$fatal      = false;
$code       = null;
$class      = '';
$suppressed = (error_reporting() === 0);
switch($errno){
case E_ERROR:
case E_USER_ERROR:
$fatal = true;
$type  = 'error';
$class = 'error';
$code  = 'PHP Error';
break;
case E_WARNING:
case E_USER_WARNING:
$type = 'error';
$class = 'warning';
$code = 'PHP Warning';
break;
case E_NOTICE:
case E_USER_NOTICE:
$type = 'info';
$class = 'info';
$code = 'PHP Notice';
break;
case E_DEPRECATED:
case E_USER_DEPRECATED:
$type = 'info';
$class = 'deprecated';
$code = 'PHP Deprecated Notice';
break;
case E_STRICT:
$type = 'info';
$class = 'warning';
$code = 'PHP Strict Warning';
$suppressed = true;
break;
default:
$type = 'info';
$class = 'unknown';
$code = 'Unknown PHP Error [' . $errno . ']';
break;
}
if($suppressed){
$code .= ' @SUPPRESSED';
}
if($errfile && strpos($errfile, ROOT_PDIR) === 0){
$details = '[src: ' . '/' . substr($errfile, strlen(ROOT_PDIR)) . ':' . $errline . '] ';
}
elseif($errfile){
$details = '[src: ' . $errfile . ':' . $errline . '] ';
}
else{
$details = '';
}
try{
if(!\Core::GetComponent()){
throw new \Exception('Error retrieved before Core was loaded!');
}
$log = \SystemLogModel::Factory();
$log->setFromArray([
'type'    => $type,
'code'    => $code,
'message' => $details . $errstr
]);
$log->save();
}
catch(\Exception $e){
try{
if(class_exists('Core\\Utilities\\Logger\\LogFile')){
$log = new LogFile($type);
$log->write($details . $errstr, $code);
}
else{
error_log($details . $errstr);
}
}
catch(\Exception $e){
}
}
if(DEVELOPMENT_MODE && !$suppressed){
if(isset($_SERVER['TERM']) || isset($_SERVER['SHELL'])){
print_error_as_text($class, $code, $errstr);
}
elseif(EXEC_MODE == 'WEB'){
print_error_as_html($class, $code, $errstr);
}
else{
print_error_as_text($class, $code, $errstr);
}
}
if($fatal){
if(EXEC_MODE == 'WEB'){
require(ROOT_PDIR . 'core/templates/halt_pages/fatal_error.inc.html');
}
exit();
}
}
function check_for_fatal() {
$error = error_get_last();
if ( $error["type"] == E_ERROR ){
$file = $error['file'];
if(strpos($file, ROOT_PDIR) === 0) $file = '/' . substr($file, strlen(ROOT_PDIR));
if(file_exists(TMP_DIR . 'lock.message')){
unlink(TMP_DIR . 'lock.message');
}
error_handler($error["type"], $error["message"] . ' in ' . $file . ':' . $error['line'], null, null);
}
}
function print_error_as_html($class, $code, $errstr){
echo '<div class="message-' . $class . '">' . "\n";
if($errstr instanceof \Exception){
$exception = $errstr;
$errstr = $exception->getMessage();
$back = $exception->getTrace();
}
else{
$back = debug_backtrace();
}
echo '<strong>' . $code . ':</strong> ' . $errstr . "\n<br/>\n<br/>";
echo '<em>Stack Trace</em>' . "\n<br/>" . '<table class="stacktrace">';
echo '<tr><th>Function/Method</th><th>File Location:Line Number</th></tr>';
foreach($back as $entry){
if(
!isset($entry['file']) &&
!isset($entry['line']) &&
isset($entry['function']) &&
$entry['function'] == 'Core\ErrorManagement\error_handler'
){
continue;
}
if(isset($entry['function']) && $entry['function'] == 'Core\ErrorManagement\print_error_as_html'){
continue;
}
if(isset($entry['function']) && $entry['function'] == 'Core\ErrorManagement\check_for_fatal'){
continue;
}
$file = (isset($entry['file']) ? $entry['file'] : 'unknown');
if(strpos($file, ROOT_PDIR) === 0){
$file = '/' . substr($file, strlen(ROOT_PDIR));
}
$line = isset($entry['line']) ? $entry['line'] : 'N/A';
if(isset($entry['class'])){
$linecode = $entry['class'] . $entry['type'] . $entry['function'] . '()';
}
elseif(isset($entry['function']) && $entry['function'] == 'Core\ErrorManagement\error_handler'){
$linecode = '****';
}
elseif(isset($entry['function'])){
$linecode = $entry['function'] . '()';
}
else{
$linecode = 'Unknown!?!';
}
echo '<tr><td>' . $linecode . '</td><td>' . $file . ':' . $line . '</td></tr>';
}
echo '</table>';
echo '</div>';
}
function print_error_as_text($class, $code, $errstr){
if($errstr instanceof \Exception){
$exception = $errstr;
$errstr = $exception->getMessage();
$back = $exception->getTrace();
}
else{
$back = debug_backtrace();
}
echo '[' . $code . ']' . $errstr . "\n";
$stderr = fopen('php://stderr', 'w');
fwrite($stderr, '[' . $code . ']' . $errstr . "\n");
fclose($stderr);
$lines = [];
$maxlength1 = $maxlength2 = 0;
foreach($back as $entry){
if(
!isset($entry['file']) &&
!isset($entry['line']) &&
isset($entry['function']) &&
$entry['function'] == 'Core\ErrorManagement\error_handler'
){
continue;
}
if(isset($entry['function']) && $entry['function'] == 'Core\ErrorManagement\print_error_as_text'){
continue;
}
if(isset($entry['function']) && $entry['function'] == 'Core\ErrorManagement\check_for_fatal'){
continue;
}
$file = (isset($entry['file']) ? $entry['file'] : 'unknown');
if(strpos($file, ROOT_PDIR) === 0){
$file = '/' . substr($file, strlen(ROOT_PDIR));
}
$line = isset($entry['line']) ? $entry['line'] : 'N/A';
if(isset($entry['class'])){
$linecode = $entry['class'] . $entry['type'] . $entry['function'] . '()';
}
elseif(isset($entry['function']) && $entry['function'] == 'Core\ErrorManagement\error_handler'){
$linecode = '****';
}
elseif(isset($entry['function'])){
$linecode = $entry['function'] . '()';
}
else{
$linecode = 'Unknown!?!';
}
$lines[] = [
'code' => $linecode,
'file' => $file,
'line' => $line,
];
$maxlength1 = max($maxlength1, strlen($linecode));
$maxlength2 = max($maxlength2, strlen($file) + 6 + strlen($line));
}
$borderheader = '+' . str_repeat('-', $maxlength1 + $maxlength2) . '+';
$borderinner = '+' . str_repeat('-', $maxlength1+2) . '+' . str_repeat('-', $maxlength2-3) . '+';
echo $borderheader . "\n";
echo '| ' . str_pad('STACK TRACE', $maxlength1 + $maxlength2-1, ' ', STR_PAD_BOTH) . '|' . "\n";
echo $borderheader . "\n";
$padding1 = max(0, $maxlength1-15);
$padding2 = max(0, $maxlength2-29);
echo '| Function/Method' . str_repeat(' ', $padding1) . ' | File Location:Line Number' . str_repeat(' ', $padding2) . '|' . "\n";
echo $borderinner . "\n";
foreach($lines as $entry){
$padding1 = max(0, $maxlength1-strlen($entry['code']));
$padding2 = max(0, $maxlength2-strlen($entry['file'] . ':' . $entry['line'] . '    '));
echo '| ' . $entry['code'] . str_repeat(' ', $padding1) . ' | ' . $entry['file'] . ':' . $entry['line'] . str_repeat(' ', $padding2) . '|' . "\n";
}
echo $borderinner . "\n";
}
} // ENDING NAMESPACE Core\ErrorManagement

namespace  {

### REQUIRE_ONCE FROM core/functions/global.php
function t(){
$params   = func_get_args();
$key      = $params[0];
$string = new \Core\i18n\I18NString($key);
$string->setParameters($params);
return $string->getTranslation();
}




Core\Utilities\Logger\write_debug('Loading hook handler');
### REQUIRE_ONCE FROM core/libs/core/HookHandler.class.php
class HookHandler implements ISingleton {
private static $RegisteredHooks = array();
private static $Instance = null;
private static $EarlyRegisteredHooks = array();
private function __construct() {
}
public static function Singleton() {
if (is_null(self::$Instance)) self::$Instance = new self();
return self::$Instance;
}
public static function GetInstance() {
return self::singleton();
}
public static function AttachToHook($hookName, $callFunction) {
$hookName = strtolower($hookName); // Case insensitive will prevent errors later on.
Core\Utilities\Logger\write_debug('Registering function ' . $callFunction . ' to hook ' . $hookName);
if (!isset(HookHandler::$RegisteredHooks[$hookName])) {
if (!isset(self::$EarlyRegisteredHooks[$hookName])) self::$EarlyRegisteredHooks[$hookName] = array();
self::$EarlyRegisteredHooks[$hookName][] = array('call' => $callFunction);
return;
}
HookHandler::$RegisteredHooks[$hookName]->attach($callFunction);
}
public static function RegisterHook(Hook $hook) {
$name = $hook->getName();
Core\Utilities\Logger\write_debug('Registering new hook [' . $name . ']');
if(isset(HookHandler::$RegisteredHooks[$name]) && FULL_DEBUG){
trigger_error('Registering hook that is already registered [' . $name . ']', E_USER_NOTICE);
}
HookHandler::$RegisteredHooks[$name] = $hook;
if (isset(self::$EarlyRegisteredHooks[$name])) {
foreach (self::$EarlyRegisteredHooks[$name] as $b) {
$hook->attach($b['call']);
}
unset(self::$EarlyRegisteredHooks[$name]);
}
}
public static function RegisterNewHook($hookName, $description = null) {
$hook = new Hook($hookName);
if($description) $hook->description = $description;
HookHandler::RegisterHook($hook);
}
public static function DispatchHook($hookName, $args = null) {
if(!Core::GetComponent()) return null;
$hookName = strtolower($hookName); // Case insensitive will prevent errors later on.
Core\Utilities\Logger\write_debug('Dispatching hook ' . $hookName);
if (!isset(HookHandler::$RegisteredHooks[$hookName])) {
trigger_error('Tried to dispatch an undefined hook ' . $hookName, E_USER_NOTICE);
return null;
}
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Dispatching hook ' . $hookName);
$args = func_get_args();
array_shift($args);
$hook   = HookHandler::$RegisteredHooks[$hookName];
$result = call_user_func_array(array(&$hook, 'dispatch'), $args);
Core\Utilities\Logger\write_debug('Dispatched hook ' . $hookName);
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Dispatched hook ' . $hookName);
return $result;
}
public static function GetAllHooks() {
return self::$RegisteredHooks;
}
public static function GetHook($hookname){
return isset(self::$RegisteredHooks[$hookname]) ? self::$RegisteredHooks[$hookname] : null;
}
public static function PrintHooks() {
echo '<dl class="xdebug-var-dump">';
foreach (self::$RegisteredHooks as $h) {
echo '<dt>' . $h->name . '</dt>';
if ($h->description) echo '<dd>' . $h->description . '</dd>';
echo "<br/>\n";
}
echo '</dl>';
}
}
class Hook {
const RETURN_TYPE_BOOL = 'bool';
const RETURN_TYPE_VOID = 'void';
const RETURN_TYPE_ARRAY = 'array';
const RETURN_TYPE_STRING = 'string';
public $name;
public $description;
public $returnType = self::RETURN_TYPE_BOOL;
private $_bindings = array();
public function __construct($name) {
$this->name = $name;
HookHandler::RegisterHook($this);
}
public function attach($function) {
$this->_bindings[] = array('call' => $function);
}
public function dispatch($args = null) {
switch($this->returnType){
case self::RETURN_TYPE_BOOL:
$return = true;
break;
case self::RETURN_TYPE_ARRAY:
$return = array();
break;
case self::RETURN_TYPE_VOID:
$return = null;
break;
case self::RETURN_TYPE_STRING:
$return = false;
break;
}
foreach ($this->_bindings as $call) {
$result = $this->callBinding($call, func_get_args());
switch($this->returnType){
case self::RETURN_TYPE_BOOL:
if ($result === false){
return false;
}
break;
case self::RETURN_TYPE_ARRAY:
if(is_array($result)){
$return = array_merge($return, $result);
}
break;
case self::RETURN_TYPE_VOID:
break;
case self::RETURN_TYPE_STRING:
if(is_scalar($result) && $result != ''){
return $result;
}
break;
}
}
return $return;
}
public function callBinding($call, $args){
if(strpos($call['call'], '::') !== false){
$parts = explode('::', $call['call']);
if(!class_exists($parts[0])){
trigger_error('The hook [' . $this->name . '] has an invalid call binding, the class [' . $parts[0] . '] does not appear to exist.', E_USER_NOTICE);
$result = null;
}
else{
$result = call_user_func_array($call['call'], $args);
}
}
else{
$result = call_user_func_array($call['call'], $args);
}
if($this->returnType == self::RETURN_TYPE_ARRAY && !is_array($result)){
$result = array();
}
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Called Hook Binding ' . $call['call']);
return $result;
}
public function __toString() {
return $this->getName();
}
public function getName() {
return strtolower($this->name);
}
public function getBindingCount(){
return sizeof($this->_bindings);
}
public function getBindings(){
return $this->_bindings;
}
}
HookHandler::singleton();


$preincludes_time = microtime(true);
Core\Utilities\Logger\write_debug('Loading core system');
### REQUIRE_ONCE FROM core/libs/core/Core.class.php
use Core\Session;
class Core implements ISingleton {
private static $instance;
private static $_LoadedComponents = false;
private $_components = null;
private $_componentsDisabled = array();
private $_libraries = array();
private $_classes = array();
private $_tmpclasses = array();
private $_widgets = array();
private $_viewClasses = array();
private $_scriptlibraries = array();
private $_loaded = false;
private $_componentobj;
private $_profiletimes = array();
private $_permissions = array();
public function load() {
return;
if ($this->_loaded) return;
$XMLFilename = ROOT_PDIR . 'core/core.xml';
$this->setFilename($XMLFilename);
$this->setRootName('core');
$this->version = $this->getRootDOM()->getAttribute("version");
$this->_loaded = true;
}
public function isLoadable() {
return $this->_isInstalled();
}
public function isValid() {
return $this->valid;
}
public function loadFiles() {
return true;
}
public function hasLibrary() {
return true;
}
public function hasModule() {
return true;
}
public function hasJSLibrary() {
return false;
}
public function getClassList() {
return array('Core'     => ROOT_PDIR . 'core/Core.class.php',
'CoreView' => ROOT_PDIR . 'core/CoreView.class.php');
}
public function getViewClassList() {
return array('CoreView' => ROOT_PDIR . 'core/CoreView.class.php');
}
public function getLibraryList() {
return array('Core' => $this->versionDB);
}
public function getViewSearchDirs() {
return array(ROOT_PDIR . 'core/view/');
}
public function getIncludePaths() {
return array();
}
public function install() {
if ($this->_isInstalled()) return;
if (!class_exists('DB')) return; // I need a database present before I can install.
InstallTask::ParseNode(
$this->getRootDOM()->getElementsByTagName('install')->item(0),
ROOT_PDIR . 'core/'
);
DB::Execute("REPLACE INTO `" . DB_PREFIX . "component` (`name`, `version`) VALUES (?, ?)", array('Core', $this->version));
$this->versionDB = $this->version;
}
public function upgrade() {
if (!$this->_isInstalled()) return false;
if (!class_exists('DB')) return; // I need a database present before I can install.
$canBeUpgraded = true;
while ($canBeUpgraded) {
$canBeUpgraded = false;
foreach ($this->getElements('upgrade') as $u) {
if (Core::GetComponent()->getVersionInstalled() == @$u->getAttribute('from')) {
$canBeUpgraded = true;
InstallTask::ParseNode($u, ROOT_PDIR . 'core/');
$this->versionDB = @$u->getAttribute('to');
DB::Execute("REPLACE INTO `" . DB_PREFIX . "component` (`name`, `version`) VALUES (?, ?)", array($this->name, $this->versionDB));
}
}
}
}
private function __construct() {
}
private function _isInstalled() {
return ($this->_componentobj->getVersionInstalled() === false) ? false : true;
}
private function _needsUpdated() {
return ($this->_componentobj->getVersionInstalled() != $this->_componentobj->getVersion());
}
private function _loadComponents() {
if ($this->_components) return null;
$this->_components = array();
$this->_libraries  = array();
$tempcomponents    = false;
Core\Utilities\Logger\write_debug('Starting loading of component metadata');
if(DEVELOPMENT_MODE){
$enablecache = false;
}
else{
$enablecache = true;
}
if($enablecache){
Core\Utilities\Logger\write_debug('Checking core-components cache');
$tempcomponents = \Core\Cache::Get('core-components', (3600 * 24));
if($tempcomponents !== false){
foreach ($tempcomponents as $c) {
try {
$c->load();
}
catch (Exception $e) {
\Core\Cache::Delete('core-components');
$tempcomponents = false;
}
}
}
}
if(!$enablecache || $tempcomponents == false){
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Scanning for component.xml files manually');
Core\Utilities\Logger\write_debug('Scanning for component.xml files manually');
$tempcomponents['core'] = ComponentFactory::Load(ROOT_PDIR . 'core/component.xml');
Core\Utilities\Logger\write_debug('Core component loaded');
$dh = opendir(ROOT_PDIR . 'components');
if (!$dh) throw new CoreException('Unable to open directory [' . ROOT_PDIR . 'components/] for reading.');
while (($file = readdir($dh)) !== false) {
if ($file{0} == '.') continue;
if (!is_dir(ROOT_PDIR . 'components/' . $file)) continue;
if (!is_readable(ROOT_PDIR . 'components/' . $file . '/component.xml')) continue;
$c = ComponentFactory::Load(ROOT_PDIR . 'components/' . $file . '/component.xml');
Core\Utilities\Logger\write_debug('Opened component ' . $file);
$file = strtolower($file);
if (!$c->isValid()) {
if (DEVELOPMENT_MODE) {
Core::SetMessage('Component ' . $c->getName() . ' appears to be invalid.');
}
continue;
}
$tempcomponents[$file] = $c;
unset($c);
}
closedir($dh);
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Component XML files scanned');
foreach ($tempcomponents as $c) {
try {
$c->load();
$c->getClassList();
$c->getViewSearchDir();
$c->getSmartyPluginDirectory();
$c->getWidgetList();
}
catch (Exception $e) {
var_dump($e);
die();
}
}
if($enablecache){
Core\Utilities\Logger\write_debug(' * Caching core-components for next pass');
\Core\Cache::Set('core-components', $tempcomponents, (3600 * 24));
}
}
$list = $tempcomponents;
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Component metadata loaded, starting registration');
Core\Utilities\Logger\write_debug(' * Component metadata loaded, starting registration');
do {
$size = sizeof($list);
foreach ($list as $n => $c) {
if($c->isInstalled() && !$c->isEnabled()){
$this->_componentsDisabled[$n] = $c;
unset($list[$n]);
continue;
}
$this->_tmpclasses = [];
if ($c->isInstalled() && $c->isLoadable() && $c->loadFiles()) {
try{
if ($c->needsUpdated()) {
$this->_tmpclasses = $c->getClassList();
file_put_contents(TMP_DIR . 'lock.message', 'Core Plus is being upgraded, please try again in a minute. ');
$c->upgrade();
unlink(TMP_DIR . 'lock.message');
}
}
catch(Exception $e){
SystemLogModel::LogErrorEvent('/core/component/failedupgrade', 'Ignoring component [' . $n . '] due to an error during upgrading!', $e->getMessage());
unlink(TMP_DIR . 'lock.message');
$this->_componentsDisabled[$n] = $c;
unset($list[$n]);
continue;
}
try{
$this->_components[$n] = $c;
$this->_registerComponent($c);
}
catch(Exception $e){
SystemLogModel::LogErrorEvent('/core/component/failedregister', 'Ignoring component [' . $n . '] due to an error during registration!', $e->getMessage());
$this->_componentsDisabled[$n] = $c;
unset($list[$n]);
continue;
}
unset($list[$n]);
continue;
}
if ($c->isInstalled() && $c->needsUpdated() && $c->isLoadable()) {
file_put_contents(TMP_DIR . 'lock.message', 'Core Plus is being upgraded, please try again in a minute. ');
$c->upgrade();
$c->loadFiles();
$this->_components[$n] = $c;
$this->_registerComponent($c);
unlink(TMP_DIR . 'lock.message');
unset($list[$n]);
continue;
}
if (!$c->isInstalled() && $c->isLoadable()) {
$this->_tmpclasses = $c->getClassList();
$c->install();
$c->enable();
$c->loadFiles();
$this->_components[$n] = $c;
$this->_registerComponent($c);
unset($list[$n]);
continue;
}
}
}
while ($size > 0 && ($size != sizeof($list)));
foreach ($list as $n => $c) {
$this->_componentsDisabled[$n] = $c;
if ($c->error & Component_2_1::ERROR_WRONGEXECMODE) continue;
if (DEVELOPMENT_MODE) {
SystemLogModel::LogErrorEvent('/core/component/missingrequirement', 'Could not load installed component ' . $n . ' due to requirement failed.', $c->getErrors());
}
}
if(class_exists('ThemeHandler')){
foreach(ThemeHandler::GetAllThemes() as $theme){
$theme->load();
}
}
\Core\Templates\Template::RequeryPaths();
}
public function _registerComponent(Component_2_1 $c) {
$name = str_replace(' ', '-', strtolower($c->getName()));
if ($c->hasLibrary()) {
$liblist = $c->getLibraryList();
$this->_libraries = array_merge($this->_libraries, $liblist);
foreach ($c->getIncludePaths() as $path) {
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
}
}
$this->_scriptlibraries = array_merge($this->_scriptlibraries, $c->getScriptLibraryList());
if ($c->hasModule()) $this->_modules[$name] = $c->getVersionInstalled();
$this->_classes           = array_merge($this->_classes, $c->getClassList());
$this->_viewClasses       = array_merge($this->_viewClasses, $c->getViewClassList());
$this->_widgets           = array_merge($this->_widgets, $c->getWidgetList());
$this->_components[$name] = $c;
if($c instanceof Component_2_1){
$this->_permissions       = array_merge($this->_permissions, $c->getPermissions());
ksort($this->_permissions);
$auths = $c->getUserAuthDrivers();
foreach($auths as $name => $class){
\Core\User\Helper::$AuthDrivers[$name] = $class;
}
}
$models = $c->getModelList();
foreach($models as $class => $file){
if(!HookHandler::GetHook('/core/controllinks/' . $class)){
$h = new Hook('/core/controllinks/' . $class);
$h->returnType = Hook::RETURN_TYPE_ARRAY;
$h->description = 'Automatic hook for control links on the ' . $class . ' object.  Attach onto this hook if you want to add a custom link anytime this object\'s control is displayed.';
}
}
$c->_setReady(true);
}
public static function CheckClass($classname) {
if (class_exists($classname)) return;
$classname = strtolower($classname);
if (isset(Core::Singleton()->_classes[$classname])) {
if(!file_exists(Core::Singleton()->_classes[$classname])){
throw new Exception('Unable to open file for class ' . $classname . ' (' . Core::Singleton()->_classes[$classname] . ')');
}
require_once(Core::Singleton()->_classes[$classname]);
}
elseif (isset(Core::Singleton()->_tmpclasses[$classname])) {
if(!file_exists(Core::Singleton()->_tmpclasses[$classname])){
throw new Exception('Unable to open file for class ' . $classname . ' (' . Core::Singleton()->_tmpclasses[$classname] . ')');
}
require_once(Core::Singleton()->_tmpclasses[$classname]);
}
}
public static function LoadComponents() {
$self = self::Singleton();
$self->_loadComponents();
}
public static function DB() {
return \Core\DB();
}
public static function FTP() {
return \Core\ftp();
}
public static function User() {
return \Core\user();
}
public static function File($filename = null) {
return \Core\Filestore\Factory::File($filename);
}
public static function Directory($directory) {
return \Core\directory($directory);
}
public static function TranslateDimensionToPreviewSize($dimensions) {
$themesizes = array(
'sm'  => ConfigHandler::Get('/theme/filestore/preview-size-sm'),
'med' => ConfigHandler::Get('/theme/filestore/preview-size-med'),
'lg'  => ConfigHandler::Get('/theme/filestore/preview-size-lg'),
'xl'  => ConfigHandler::Get('/theme/filestore/preview-size-xl'),
);
if (sizeof(func_get_args()) == 2) {
$width  = (int)func_get_arg(0);
$height = (int)func_get_arg(1);
}
elseif (is_numeric($dimensions)) {
$width  = $dimensions;
$height = $dimensions;
}
elseif (stripos($dimensions, 'x') !== false) {
$ds     = explode('x', strtolower($dimensions));
$width  = trim($ds[0]);
$height = trim($ds[1]);
}
else {
return null;
}
$smaller = min($width, $height);
if ($smaller >= $themesizes['xl']) return 'xl';
elseif ($smaller >= $themesizes['lg']) return 'lg';
elseif ($smaller >= $themesizes['med']) return 'med';
else return 'sm';
}
public static function GetPermissions(){
return self::Singleton()->_permissions;
}
public static function GetComponent($name = 'core') {
$s = self::Singleton();
if(isset($s->_components[$name])) return $s->_components[$name];
if(isset($s->_componentsDisabled[$name])) return $s->_componentsDisabled[$name];
return null;
}
public static function GetComponents() {
return self::Singleton()->_components;
}
public static function GetDisabledComponents(){
return self::Singleton()->_componentsDisabled;
}
public static function GetComponentByController($controller) {
$controller = strtolower($controller);
$self = self::Singleton();
foreach ($self->_components as $c) {
$controllers = $c->getControllerList();
if (isset($controllers[$controller])) return $c;
}
return null;
}
public static function GetStandardHTTPHeaders($forcurl = false, $autoclose = false) {
$headers = array(
'User-Agent: Core Plus ' . self::GetComponent()->getVersion() . ' (http://corepl.us)',
'Referer: ' . SERVERNAME,
);
if ($autoclose) {
$headers[] = 'Connection: close';
}
if ($forcurl) {
return $headers;
}
else {
return implode("\r\n", $headers);
}
}
public static function Singleton() {
if(self::$instance === null){
self::$instance = new self();
}
return self::$instance;
}
public static function GetInstance() {
return self::Singleton();
}
public static function _LoadFromDatabase() {
if (!self::GetComponent()->load()) {
if (DEVELOPMENT_MODE) {
self::GetComponent()->install();
die('Installed core!  <a href="' . ROOT_WDIR . '">continue</a>');
}
else {
die('There was a server error, please notify the administrator of this.');
}
}
return;
}
public static function IsClassAvailable($classname) {
if(self::$instance == null){
self::Singleton();
}
if(isset(self::$instance->_classes[$classname])){
return true;
}
else{
return false;
}
}
public static function IsLibraryAvailable($name, $version = false, $operation = 'ge') {
$ch   = self::Singleton();
$name = strtolower($name);
if (!isset($ch->_libraries[$name])) {
return false;
}
elseif ($version !== false) {
return Core::VersionCompare($ch->_libraries[$name], $version, $operation);
}
else return true;
}
public static function IsJSLibraryAvailable($name, $version = false, $operation = 'ge') {
$ch   = self::Singleton();
$name = strtolower($name);
if (!isset($ch->_scriptlibraries[$name])) return false;
elseif ($version) return version_compare(str_replace('~', '-', $ch->_scriptlibraries[$name]->version), $version, $operation);
else return true;
}
public static function GetJSLibrary($library) {
$library = strtolower($library);
return self::Singleton()->_scriptlibraries[$library];
}
public static function GetJSLibraries() {
return self::Singleton()->_scriptlibraries;
}
public static function GetClasses(){
return self::Singleton()->_classes;
}
public static function LoadScriptLibrary($library) {
$library = strtolower($library);
$obj     = self::Singleton();
if (isset($obj->_scriptlibraries[$library])) {
return call_user_func($obj->_scriptlibraries[$library]);
}
else {
return false;
}
}
public static function IsComponentAvailable($name, $version = false, $operation = 'ge') {
$self = self::Singleton();
$name = strtolower($name);
if (!isset($self->_components[$name])){
return false;
}
elseif (!$self->_components[$name]->isEnabled()){
return false;
}
elseif ($version){
return Core::VersionCompare($self->_components[$name]->getVersionInstalled(), $version, $operation);
}
else{
return true;
}
}
public static function IsComponentReady($name){
if(!self::IsComponentAvailable($name)){
return 'Component ' . $name . ' is not available!';
}
$self = self::Singleton();
$name = strtolower($name);
$c = $self->_components[$name];
$attr = $c->getRootDOM()->getAttribute('isready');
if($attr === null || $attr === ''){
return true;
}
return call_user_func($attr);
}
public static function IsInstalled() {
return Core::Singleton()->_isInstalled();
}
public static function NeedsUpdated() {
return Core::Singleton()->_needsUpdated();
}
public static function GetVersion() {
return Core::GetComponent()->getVersionInstalled();
}
public static function ResolveAsset($asset) {
trigger_error('Core::ResolveAsset is deprecated, please use \\Core\\resolve_asset() instead.', E_USER_DEPRECATED);
return \Core\resolve_asset($asset);
}
public static function ResolveLink($url) {
trigger_error('Core::ResolveLink is deprecated, please use \\Core\\resolve_link() instead.', E_USER_DEPRECATED);
return \Core\resolve_link($url);
}
static public function Redirect($page, $code = 302) {
trigger_error('Core::Redirect is deprecated, please use \\Core\\redirect() instead.', E_USER_DEPRECATED);
\Core\redirect($page, $code);
}
static public function Reload() {
trigger_error('Core::Reload is deprecated, please use \\Core\\reload() instead.', E_USER_DEPRECATED);
\Core\reload();
}
static public function GoBack($depth=1) {
trigger_error('Core::GoBack is deprecated, please use \\Core\\go_back() instead.', E_USER_DEPRECATED);
\Core\go_back();
}
public static function GetHistory($depth = 2){
trigger_error('Core::GetHistory is deprecated and will be removed shortly.', E_USER_DEPRECATED);
return \Core\page_request()->getReferrer();
}
static public function GetNavigation($base) {
trigger_error('\\Core\\GetNavigation is deprecated and will be removed shortly', E_USER_DEPRECATED);
return \Core\page_request()->getReferrer();
}
static public function _RecordNavigation() {
trigger_error('\\Core\\RecordNavigation is deprecated and will be removed shortly', E_USER_DEPRECATED);
}
static public function SetMessage($messageText, $messageType = 'info') {
if(trim($messageText) == '') return;
$messageType = strtolower($messageType);
if(EXEC_MODE == 'CLI'){
$messageText = preg_replace('/<br[^>]*>/i', "\n", $messageText);
echo "[" . $messageType . "] - " . $messageText . "\n";
}
else{
$stack = Session::Get('message_stack', []);
$stack[] = array(
'mtext' => $messageText,
'mtype' => $messageType,
);
Session::Set('message_stack', $stack);
}
}
static public function AddMessage($messageText, $messageType = 'info') {
Core::SetMessage($messageText, $messageType);
}
static public function GetMessages($returnSorted = false, $clearStack = true) {
$stack = Session::Get('message_stack', []);
if($returnSorted){
$stack = \Core::SortByKey($stack, 'mtype');
}
if($clearStack){
Session::UnsetKey('message_stack');
}
return $stack;
}
static public function SortByKey($named_recs, $order_by, $rev = false, $flags = 0) {
$named_hash = array();
foreach ($named_recs as $key=> $fields) $named_hash["$key"] = $fields[$order_by];
if ($rev) arsort($named_hash, $flags);
else asort($named_hash, $flags);
$sorted_records = array();
foreach ($named_hash as $key=> $val) $sorted_records["$key"] = $named_recs[$key];
return $sorted_records;
}
static public function ImplodeKey($glue, &$array) {
$arrayKeys = array();
foreach ($array as $key => $value) {
$arrayKeys[] = $key;
}
return implode($glue, $arrayKeys);
}
static public function RandomHex($length = 1, $casesensitive = false) {
return \Core\random_hex($length, $casesensitive);
}
public static function FormatSize($filesize, $round = 2) {
return \Core\Filestore\format_size($filesize, $round);
}
public static function GetExtensionFromString($str) {
if (strpos($str, '.') === false) return '';
return substr($str, strrpos($str, '.') + 1);
}
public static function GetProfileTimeTotal() {
error_log(__FUNCTION__ . ' is deprecated, please use \Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->getTime() instead', E_USER_DEPRECATED);
return \Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->getTime();
}
public static function CheckEmailValidity($email) {
$atIndex = strrpos($email, "@");
if (is_bool($atIndex) && !$atIndex) return false;
$domain    = substr($email, $atIndex + 1);
$local     = substr($email, 0, $atIndex);
$localLen  = strlen($local);
$domainLen = strlen($domain);
if ($localLen < 1 || $localLen > 64) {
return false;
}
if ($domainLen < 1 || $domainLen > 255) {
return false;
}
if ($local[0] == '.' || $local[$localLen - 1] == '.') {
return false;
}
if (preg_match('/\\.\\./', $local)) {
return false;
}
if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
return false;
}
if (preg_match('/\\.\\./', $domain)) {
return false;
}
if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
return false;
}
}
if (ConfigHandler::Get('/core/email/verify_with_dns') && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
return false;
}
return true;
}
public static function CheckIntGT0Validity($val){
if(!(is_int($val) || ctype_digit($val))){
return false;
}
if($val <= 0){
return false;
}
return true;
}
public static function _AttachCoreJavascript() {
if(Core::IsComponentAvailable('User')){
$userid   = (\Core\user()->get('id') ? \Core\user()->get('id') : 0);
$userauth = \Core\user()->exists() ? 'true' : 'false';
}
else{
$userid   = 0;
$userauth = 'false';
}
$ua = \Core\UserAgent::Construct();
$uastring = '';
foreach($ua->asArray() as $k => $v){
if($v === true){
$uastring .= "\t\t\t$k: true,\n";
}
elseif($v === false){
$uastring .= "\t\t\t$k: false,\n";
}
else{
$uastring .= "\t\t\t$k: \"$v\",\n";
}
}
$uastring .= "\t\t\tis_mobile: " . ($ua->isMobile() ? 'true' : 'false') . "\n";
$url = htmlentities(\Core\page_request()->uriresolved);
if(ConfigHandler::Get('/core/page/url_remove_stop_words')){
$stopwords = json_encode(\Core\get_stop_words());
$removeStopWords = 'true';
}
else{
$stopwords = '""';
$removeStopWords = 'false';
}
$version      = DEVELOPMENT_MODE ? self::GetComponent()->getVersion() : '';
$rootWDIR     = ROOT_WDIR;
$rootURL      = ROOT_URL;
$rootURLSSL   = ROOT_URL_SSL;
$rootURLnoSSL = ROOT_URL_NOSSL;
$ssl          = SSL ? 'true' : 'false';
$sslMode      = SSL_MODE;
$script = <<<EOD
<script type="text/javascript">
var Core = {
Version: "$version",
ROOT_WDIR: "$rootWDIR",
ROOT_URL: "$rootURL",
ROOT_URL_SSL: "$rootURLSSL",
ROOT_URL_NOSSL: "$rootURLnoSSL",
SSL: $ssl,
SSL_MODE: "$sslMode",
User: {
id: "$userid",
authenticated: $userauth
},
Url: "$url",
Browser: { $uastring },
URLRemoveStopWords: $removeStopWords,
StopWords: $stopwords
};
</script>
EOD;
$minified = \ConfigHandler::Get('/core/javascript/minified');
if($minified){
$script = str_replace(["\t", "\n"], ['', ''], $script);
}
\Core\view()->addScript($script, 'head');
\Core\view()->addScript('js/core.js', 'foot');
}
public static function _AttachCoreStrings() {
\Core\view()->addScript('js/core.strings.js');
return true;
}
public static function _AttachAjaxLinks(){
JQuery::IncludeJQueryUI();
\Core\view()->addScript('js/core.ajaxlinks.js', 'foot');
return true;
}
public static function _AttachLessJS(){
\Core\view()->addScript('js/less-1.7.1.js', 'head');
return true;
}
public static function _AttachJSON(){
\Core\view()->addScript ('js/json2.js', 'head');
return true;
}
public static function VersionCompare($version1, $version2, $operation = null) {
$version1 = new \Core\VersionString($version1);
return $version1->compare($version2, $operation);
}
public static function VersionSplit($version) {
return new \Core\VersionString($version);
}
public static function CompareValues($val1, $val2){
return \Core\compare_values($val1, $val2);
}
public static function CompareStrings($val1, $val2) {
return \Core\compare_strings($val1, $val2);
}
public static function GenerateUUID(){
$serverid = 1;
return dechex($serverid) . '-' . dechex(microtime(true) * 10000) . '-' . strtolower(Core::RandomHex(4));
}
}
spl_autoload_register('Core::CheckClass');


Core\Utilities\Logger\write_debug('Loading configs');
### REQUIRE_ONCE FROM core/libs/core/ConfigHandler.class.php
class ConfigHandler implements ISingleton {
private static $Instance = null;
private $_directory;
private $_cacheFromDB = array();
private $_overrides = array();
private function __construct() {
$this->_directory = ROOT_PDIR . "config/";
if (!is_readable($this->_directory)) {
throw new Exception("Could not open config directory [" . $this->_directory . "] for reading.");
}
}
private function _loadConfigFile($config) {
$return = array();
$file = $this->_directory . $config . '.xml';
if (!file_exists($file)) {
trigger_error("Requested config file $config.xml not located within " . $this->_directory, E_USER_NOTICE);
return false;
}
if (!is_readable($file)) {
trigger_error("Unable to read $file, please ensure it's permissions are set correctly", E_USER_NOTICE);
return false;
}
$xml = new DOMDocument();
$xml->load($file);
foreach ($xml->getElementsByTagName("define") as $xmlEl) {
$name  = $xmlEl->getAttribute("name");
$type  = $xmlEl->getAttribute("type");
$value = trim($xmlEl->getElementsByTagName("value")->item(0)->nodeValue);
switch (strtolower($type)) {
case 'int':
$value = (int)$value;
break;
case 'octal':
$value = octdec($value);
break;
case 'boolean':
$value = (($value == 'true' || $value == '1' || $value == 'yes') ? true : false);
break;
}
if (!defined($name))
define($name, $value);
} // foreach($xml->getElementsByTagName("define") as $xmlEl)
foreach ($xml->getElementsByTagName("return") as $xmlEl) {
$name  = $xmlEl->getAttribute("name");
$type  = $xmlEl->getAttribute("type");
$value = trim($xmlEl->getElementsByTagName("value")->item(0)->nodeValue);
switch (strtolower($type)) {
case 'int':
$value = (int)$value;
break;
case 'octal':
$value = octdec($value);
break;
case 'boolean':
$value = (($value == 'true' || $value == '1' || $value == 'yes') ? true : false);
break;
}
$return[$name] = $value;
} // foreach($xml->getElementsByTagName("define") as $xmlEl)
return (!count($return) ? true : $return);
}
private function _clearCache(){
$this->_cacheFromDB = array();
$this->_overrides = array();
}
private function _get($key){
if(isset($this->_cacheFromDB[$key])){
if(isset($this->_overrides[$key])){
return ConfigModel::TranslateValue($this->_cacheFromDB[$key]->get('type'), $this->_overrides[$key]);
}
else{
return $this->_cacheFromDB[$key]->getValue();
}
}
elseif(\Core\Session::Get('configs/' . $key) !== null){
return \Core\Session::Get('configs/' . $key);
}
else{
return null;
}
}
private function _loadDB(){
Core\Utilities\Logger\write_debug('Config data loading from database');
$this->_clearCache();
$fac = ConfigModel::Find();
foreach ($fac as $config) {
$key = $config->get('key');
$this->_cacheFromDB[$key] = $config;
$val = $config->getValue();
if($config->get('mapto') && !defined($config->get('mapto'))){
define($config->get('mapto'), $val);
}
}
}
public static function Singleton() {
if (self::$Instance === null) {
self::$Instance = new self();
}
return self::$Instance;
}
public static function GetInstance() {
return self::Singleton();
}
public static function LoadConfigFile($config) {
return self::Singleton()->_loadConfigFile($config);
}
public static function GetValue($key) {
return self::Singleton()->_get($key);
}
public static function GetConfig($key, $autocreate = true) {
$instance = self::GetInstance();
if(!isset($instance->_cacheFromDB[$key])){
if(!$autocreate) return null;
$instance->_cacheFromDB[$key] = new ConfigModel($key);
}
return $instance->_cacheFromDB[$key];
}
public static function Get($key) {
return self::Singleton()->_get($key);
}
public static function Set($key, $value) {
$instance = self::GetInstance();
if(!isset($instance->_cacheFromDB[$key])){
return false;
}
$config = $instance->_cacheFromDB[$key];
if(
$config->get('overrideable') == 1 &&
Core::IsComponentAvailable('enterprise') &&
MultiSiteHelper::GetCurrentSiteID()
){
$siteconfig = MultiSiteConfigModel::Construct($key, MultiSiteHelper::GetCurrentSiteID());
$siteconfig->set('value', $value);
$siteconfig->save();
$instance->_overrides[$key] = $value;
}
else{
$config->setValue($value);
$config->save();
}
return true;
}
public static function FindConfigs($keymatch){
$return = [];
foreach(self::Singleton()->_cacheFromDB as $k => $config){
if(strpos($k, $keymatch) !== false){
$return[$k] = $config;
}
}
return $return;
}
public static function SetOverride($key, $value){
self::Singleton()->_overrides[$key] = $value;
}
public static function IsOverridden($key){
$s = self::Singleton();
if(!isset($s->_overrides[$key])) return false;
if($s->_overrides[$key] == $s->_cacheFromDB[$key]) return false;
return true;
}
public static function CacheConfig(ConfigModel $config) {
$instance = self::GetInstance();
$instance->_cacheFromDB[$config->get('key')] = $config;
}
public static function _DBReadyHook() {
$singleton = self::Singleton();
$singleton->_loadDB();
}
public static function var_dump_cache() {
var_dump(ConfigHandler::$cacheFromDB);
}
}


ConfigHandler::Singleton();
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Configuration loaded and available');
$core_settings = ConfigHandler::LoadConfigFile("configuration");
if (!$core_settings) {
if(EXEC_MODE == 'WEB'){
$newURL = 'install/';
die("Please <a href=\"{$newURL}\">install Core Plus.</a><br/><br/>(You may need to hard-refresh this page a time or two if you just installed)");
}
else{
die('Please install core plus through the web interface first!' . "\n");
}
}
if (!DEVELOPMENT_MODE) {
ini_set('display_errors', 0);
ini_set('html_errors', 0);
}
else{
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
}
set_error_handler('Core\\ErrorManagement\\error_handler', error_reporting());
register_shutdown_function('HookHandler::DispatchHook', '/core/shutdown');
register_shutdown_function('Core\\ErrorManagement\\check_for_fatal');
if (EXEC_MODE == 'CLI') {
$servername          = null;
$servernameSSL       = null;
$servernameNOSSL     = null;
$rooturl             = isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : null;
$rooturlNOSSL        = $rooturl;
$rooturlSSL          = $rooturl;
$curcall             = null;
$relativerequestpath = null;
$ssl                 = false;
$sslmode             = 'disabled';
$tmpdir              = $core_settings['tmp_dir_cli'];
$host                = 'localhost';
if (isset($_SERVER['HOME']) && is_dir($_SERVER['HOME'] . '/.gnupg')) $gnupgdir = $_SERVER['HOME'] . '/.gnupg/';
else $gnupgdir = false;
ini_set('html_errors', 0);
}
else {
if (isset ($_SERVER ['HTTPS'])) $servername = "https://";
else $servername = "http://";
if ($core_settings['site_url'] != '') $servername .= $core_settings['site_url'];
else $servername .= $_SERVER['HTTP_HOST'];
if ($core_settings['site_url'] != '' && $_SERVER['HTTP_HOST'] != $core_settings['site_url']) {
$newURL = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $core_settings['site_url'] . $_SERVER['REQUEST_URI'];
header('HTTP/1.1 301 Moved Permanently'); // 301 transfers page rank.
header("Location:" . $newURL);
die("If your browser does not refresh, please <a href=\"{$newURL}\">Click Here</a>");
}
$host = $_SERVER['HTTP_HOST'];
$servernameNOSSL = str_replace('https://', 'http://', $servername);
if (preg_match('/\:\d+$/', substr($servernameNOSSL, -6))) {
$servernameNOSSL = preg_replace('/\:\d+$/', ':' . PORT_NUMBER, $servernameNOSSL);
}
else {
$servernameNOSSL .= ':' . PORT_NUMBER;
}
if (PORT_NUMBER == 80) {
$servernameNOSSL = str_replace(':80', '', $servernameNOSSL);
}
if(defined('ENABLE_SSL')){
if(ENABLE_SSL){
$sslmode = 'ondemand';
}
else{
$sslmode = 'disabled';
}
define('SSL_MODE', $sslmode);
}
elseif(defined('SSL_MODE')){
if(SSL_MODE == 'disabled') $enablessl = false;
else $enablessl = true;
define('ENABLE_SSL', $enablessl);
}
else{
define('SSL_MODE', 'disabled');
define('ENABLE_SSL', false);
}
if (ENABLE_SSL) {
$servernameSSL = str_replace('http://', 'https://', $servername);
if (preg_match('/\:\d+$/', substr($servernameSSL, -6))) {
$servernameSSL = preg_replace('/\:\d+$/', ':' . PORT_NUMBER_SSL, $servernameSSL);
}
else {
$servernameSSL .= ':' . PORT_NUMBER_SSL;
}
if (PORT_NUMBER_SSL == 443) {
$servernameSSL = str_replace(':443', '', $servernameSSL);
}
}
else {
$servernameSSL = $servernameNOSSL;
}
$rooturl             = $servername . ROOT_WDIR;
$rooturlNOSSL        = $servernameNOSSL . ROOT_WDIR;
$rooturlSSL          = $servernameSSL . ROOT_WDIR;
$curcall             = $servername . $_SERVER['REQUEST_URI'];
$relativerequestpath = strtolower('/' . substr($_SERVER['REQUEST_URI'], strlen(ROOT_WDIR)));
if (strpos($relativerequestpath, '?') !== false) $relativerequestpath = substr($relativerequestpath, 0, strpos($relativerequestpath, '?'));
$ssl = (
(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ||
(isset($_SERVER['FRONT_END_HTTPS']) && $_SERVER['FRONT_END_HTTPS'] == 'on') ||
(isset($_SERVER['HTTP_HTTPS']) && $_SERVER['HTTP_HTTPS'] == 'on')
);
$tmpdir = $core_settings['tmp_dir_web'];
$gnupgdir = false;
}
define('SERVERNAME', $servername);
define('SERVERNAME_NOSSL', $servernameNOSSL);
define('SERVERNAME_SSL', $servernameSSL);
define('ROOT_URL', $rooturl);
define('ROOT_URL_NOSSL', $rooturlNOSSL);
define('ROOT_URL_SSL', $rooturlSSL);
define('CUR_CALL', $curcall);
define('REL_REQUEST_PATH', $relativerequestpath);
define('SSL', $ssl);
define('SSL_MODE_DISABLED', 'disabled');
define('SSL_MODE_ONDEMAND', 'ondemand');
define('SSL_MODE_ALLOWED',  'allowed');
define('SSL_MODE_REQUIRED', 'required');
if(!defined('TMP_DIR')) {
define('TMP_DIR', $tmpdir);
}
define('TMP_DIR_WEB', $core_settings['tmp_dir_web']);
define('TMP_DIR_CLI', $core_settings['tmp_dir_cli']);
define('HOST', $host);
if (!is_dir(TMP_DIR)) {
mkdir(TMP_DIR, 0777, true);
}
if(EXEC_MODE == 'WEB' && SSL_MODE == SSL_MODE_REQUIRED && !SSL){
if(!DEVELOPMENT_MODE) header("HTTP/1.1 301 Moved Permanently");
header('Location: ' . ROOT_URL_SSL . substr(REL_REQUEST_PATH, 1));
die('This site requires SSL, if it does not redirect you automatically, please <a href="' . ROOT_URL_SSL . substr(REL_REQUEST_PATH, 1) . '">Click Here</a>.');
}
elseif(EXEC_MODE == 'WEB' && SSL_MODE == SSL_MODE_DISABLED && SSL){
if(!DEVELOPMENT_MODE) header("HTTP/1.1 301 Moved Permanently");
header('Location: ' . ROOT_URL_NOSSL . substr(REL_REQUEST_PATH, 1));
die('This site has SSL disabled, if it does not redirect you automatically, please <a href="' . ROOT_URL_NOSSL . substr(REL_REQUEST_PATH, 1) . '">Click Here</a>.');
}
if(file_exists(TMP_DIR . 'lock.message')){
$contents = file_get_contents(TMP_DIR . 'lock.message');
$adminmsg = '(Site is currently locked via ' . TMP_DIR . 'lock.message.  If this is in error, simply remove that file).';
if(DEVELOPMENT_MODE){
echo $adminmsg . "<br/>\n";
}
error_log($adminmsg);
die($contents);
}
if (!defined('GPG_HOMEDIR')) {
define('GPG_HOMEDIR', ($gnupgdir) ? $gnupgdir : ROOT_PDIR . 'gnupg');
}
putenv('GNUPGHOME=' . GPG_HOMEDIR);
if(!defined('XHPROF')){
define('XHPROF', 0);
}
if(XHPROF > 0 && function_exists('xhprof_enable')){
if(XHPROF == 100){
define('ENABLE_XHPROF', true);
}
else{
define('ENABLE_XHPROF', (XHPROF > rand(1,100)));
}
}
else{
define('ENABLE_XHPROF', false);
}
unset(
$enablessl, $servername, $servernameNOSSL, $servernameSSL, $rooturl, $rooturlNOSSL,
$rooturlSSL, $curcall, $ssl, $gnupgdir, $host, $sslmode, $tmpdir, $relativerequestpath,
$core_settings
);
$maindefines_time = microtime(true);
if(ENABLE_XHPROF){
xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
}
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Core Plus bootstrapped and application starting');
try {
$dbconn = DMI::GetSystemDMI();
ConfigHandler::_DBReadyHook();
}
catch (Exception $e) {
error_log($e->getMessage());
if (DEVELOPMENT_MODE) {
die('Please <a href="' . ROOT_WDIR . 'install' . '">install Core Plus.</a>');
}
else {
require(ROOT_PDIR . 'core/templates/halt_pages/fatal_error.inc.html');
die();
}
}
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Core Plus Data Model Interface loaded and ready');
unset($start_time, $predefines_time, $preincludes_time, $maindefines_time, $dbconn);
if(!defined('FTP_USERNAME')){
define('FTP_USERNAME', ConfigHandler::Get('/core/ftp/username'));
}
if(!defined('FTP_PASSWORD')){
define('FTP_PASSWORD', ConfigHandler::Get('/core/ftp/password'));
}
if(!defined('FTP_PATH')){
define('FTP_PATH', ConfigHandler::Get('/core/ftp/path'));
}
if(!defined('CDN_TYPE')){
define('CDN_TYPE', ConfigHandler::Get('/core/filestore/backend'));
}
if(!defined('CDN_LOCAL_ASSETDIR')){
error_log('Please define the CDN_LOCAL_ASSETDIR in your config.xml file!  This has been migrated from the web config.', E_USER_DEPRECATED);
define('CDN_LOCAL_ASSETDIR', ConfigHandler::Get('/core/filestore/assetdir'));
}
if(!defined('CDN_LOCAL_PUBLICDIR')){
error_log('Please define the CDN_LOCAL_PUBLICDIR in your config.xml file!  This has been migrated from the web config.', E_USER_DEPRECATED);
define('CDN_LOCAL_PUBLICDIR', ConfigHandler::Get('/core/filestore/publicdir'));
}
if(!defined('CDN_LOCAL_PRIVATEDIR')){
error_log('Please define the CDN_LOCAL_PRIVATEDIR in your config.xml file!  This has been migrated from the web config.', E_USER_DEPRECATED);
define('CDN_LOCAL_PRIVATEDIR', 'files/private');
}
date_default_timezone_set(TIME_DEFAULT_TIMEZONE);
Core::LoadComponents();
if (EXEC_MODE == 'WEB') {
try {
}
catch (DMI_Exception $e) {
if (DEVELOPMENT_MODE) {
die("Please <a href=\"{$newURL}\">install Core Plus.</a>");
}
else {
require(ROOT_PDIR . 'core/templates/halt_pages/fatal_error.inc.html');
die();
}
}
}
HookHandler::DispatchHook('/core/components/loaded');
$profiler->record('Components Load Complete');
### REQUIRE_ONCE FROM core/bootstrap_postincludes.php
if(!defined('SMARTY_DIR')){
define('SMARTY_DIR', ROOT_PDIR . 'core/libs/smarty/');
}
### REQUIRE_ONCE FROM core/libs/smarty/Smarty.class.php
if (!defined('DS')) {
define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('SMARTY_DIR')) {
define('SMARTY_DIR', dirname(__FILE__) . DS);
}
if (!defined('SMARTY_SYSPLUGINS_DIR')) {
define('SMARTY_SYSPLUGINS_DIR', SMARTY_DIR . 'sysplugins' . DS);
}
if (!defined('SMARTY_PLUGINS_DIR')) {
define('SMARTY_PLUGINS_DIR', SMARTY_DIR . 'plugins' . DS);
}
if (!defined('SMARTY_MBSTRING')) {
define('SMARTY_MBSTRING', function_exists('mb_get_info'));
}
if (!defined('SMARTY_RESOURCE_CHAR_SET')) {
define('SMARTY_RESOURCE_CHAR_SET', SMARTY_MBSTRING ? 'UTF-8' : 'ISO-8859-1');
}
if (!defined('SMARTY_RESOURCE_DATE_FORMAT')) {
define('SMARTY_RESOURCE_DATE_FORMAT', '%b %e, %Y');
}
if (!class_exists('Smarty_Internal_Data', false)) {
### REQUIRE_ONCE FROM core/libs/smarty/sysplugins/smarty_internal_data.php
class Smarty_Internal_Data
{
public $template_class = 'Smarty_Internal_Template';
public $tpl_vars = array();
public $parent = null;
public $config_vars = array();
public function assign($tpl_var, $value = null, $nocache = false)
{
if (is_array($tpl_var)) {
foreach ($tpl_var as $_key => $_val) {
if ($_key != '') {
$this->tpl_vars[$_key] = new Smarty_Variable($_val, $nocache);
}
}
} else {
if ($tpl_var != '') {
$this->tpl_vars[$tpl_var] = new Smarty_Variable($value, $nocache);
}
}
return $this;
}
public function assignGlobal($varname, $value = null, $nocache = false)
{
if ($varname != '') {
Smarty::$global_tpl_vars[$varname] = new Smarty_Variable($value, $nocache);
$ptr = $this;
while ($ptr instanceof Smarty_Internal_Template) {
$ptr->tpl_vars[$varname] = clone Smarty::$global_tpl_vars[$varname];
$ptr = $ptr->parent;
}
}
return $this;
}
public function assignByRef($tpl_var, &$value, $nocache = false)
{
if ($tpl_var != '') {
$this->tpl_vars[$tpl_var] = new Smarty_Variable(null, $nocache);
$this->tpl_vars[$tpl_var]->value = &$value;
}
return $this;
}
public function append($tpl_var, $value = null, $merge = false, $nocache = false)
{
if (is_array($tpl_var)) {
foreach ($tpl_var as $_key => $_val) {
if ($_key != '') {
if (!isset($this->tpl_vars[$_key])) {
$tpl_var_inst = $this->getVariable($_key, null, true, false);
if ($tpl_var_inst instanceof Smarty_Undefined_Variable) {
$this->tpl_vars[$_key] = new Smarty_Variable(null, $nocache);
} else {
$this->tpl_vars[$_key] = clone $tpl_var_inst;
}
}
if (!(is_array($this->tpl_vars[$_key]->value) || $this->tpl_vars[$_key]->value instanceof ArrayAccess)) {
settype($this->tpl_vars[$_key]->value, 'array');
}
if ($merge && is_array($_val)) {
foreach ($_val as $_mkey => $_mval) {
$this->tpl_vars[$_key]->value[$_mkey] = $_mval;
}
} else {
$this->tpl_vars[$_key]->value[] = $_val;
}
}
}
} else {
if ($tpl_var != '' && isset($value)) {
if (!isset($this->tpl_vars[$tpl_var])) {
$tpl_var_inst = $this->getVariable($tpl_var, null, true, false);
if ($tpl_var_inst instanceof Smarty_Undefined_Variable) {
$this->tpl_vars[$tpl_var] = new Smarty_Variable(null, $nocache);
} else {
$this->tpl_vars[$tpl_var] = clone $tpl_var_inst;
}
}
if (!(is_array($this->tpl_vars[$tpl_var]->value) || $this->tpl_vars[$tpl_var]->value instanceof ArrayAccess)) {
settype($this->tpl_vars[$tpl_var]->value, 'array');
}
if ($merge && is_array($value)) {
foreach ($value as $_mkey => $_mval) {
$this->tpl_vars[$tpl_var]->value[$_mkey] = $_mval;
}
} else {
$this->tpl_vars[$tpl_var]->value[] = $value;
}
}
}
return $this;
}
public function appendByRef($tpl_var, &$value, $merge = false)
{
if ($tpl_var != '' && isset($value)) {
if (!isset($this->tpl_vars[$tpl_var])) {
$this->tpl_vars[$tpl_var] = new Smarty_Variable();
}
if (!is_array($this->tpl_vars[$tpl_var]->value)) {
settype($this->tpl_vars[$tpl_var]->value, 'array');
}
if ($merge && is_array($value)) {
foreach ($value as $_key => $_val) {
$this->tpl_vars[$tpl_var]->value[$_key] = &$value[$_key];
}
} else {
$this->tpl_vars[$tpl_var]->value[] = &$value;
}
}
return $this;
}
public function getTemplateVars($varname = null, $_ptr = null, $search_parents = true)
{
if (isset($varname)) {
$_var = $this->getVariable($varname, $_ptr, $search_parents, false);
if (is_object($_var)) {
return $_var->value;
} else {
return null;
}
} else {
$_result = array();
if ($_ptr === null) {
$_ptr = $this;
}
while ($_ptr !== null) {
foreach ($_ptr->tpl_vars AS $key => $var) {
if (!array_key_exists($key, $_result)) {
$_result[$key] = $var->value;
}
}
if ($search_parents) {
$_ptr = $_ptr->parent;
} else {
$_ptr = null;
}
}
if ($search_parents && isset(Smarty::$global_tpl_vars)) {
foreach (Smarty::$global_tpl_vars AS $key => $var) {
if (!array_key_exists($key, $_result)) {
$_result[$key] = $var->value;
}
}
}
return $_result;
}
}
public function clearAssign($tpl_var)
{
if (is_array($tpl_var)) {
foreach ($tpl_var as $curr_var) {
unset($this->tpl_vars[$curr_var]);
}
} else {
unset($this->tpl_vars[$tpl_var]);
}
return $this;
}
public function clearAllAssign()
{
$this->tpl_vars = array();
return $this;
}
public function configLoad($config_file, $sections = null)
{
Smarty_Internal_Extension_Config::configLoad($this, $config_file, $sections);
return $this;
}
public function getVariable($variable, $_ptr = null, $search_parents = true, $error_enable = true)
{
if ($_ptr === null) {
$_ptr = $this;
}
while ($_ptr !== null) {
if (isset($_ptr->tpl_vars[$variable])) {
return $_ptr->tpl_vars[$variable];
}
if ($search_parents) {
$_ptr = $_ptr->parent;
} else {
$_ptr = null;
}
}
if (isset(Smarty::$global_tpl_vars[$variable])) {
return Smarty::$global_tpl_vars[$variable];
}
$smarty = isset($this->smarty) ? $this->smarty : $this;
if ($smarty->error_unassigned && $error_enable) {
$x = $$variable;
}
return new Smarty_Undefined_Variable;
}
public function getConfigVariable($variable, $error_enable = true)
{
return Smarty_Internal_Extension_Config::getConfigVariable($this, $variable, $error_enable = true);
}
public function getConfigVars($varname = null, $search_parents = true)
{
return Smarty_Internal_Extension_Config::getConfigVars($this, $varname, $search_parents);
}
public function clearConfig($varname = null)
{
return Smarty_Internal_Extension_Config::clearConfig($this, $varname);
}
public function getStreamVariable($variable)
{
$_result = '';
$fp = fopen($variable, 'r+');
if ($fp) {
while (!feof($fp) && ($current_line = fgets($fp)) !== false) {
$_result .= $current_line;
}
fclose($fp);
return $_result;
}
$smarty = isset($this->smarty) ? $this->smarty : $this;
if ($smarty->error_unassigned) {
throw new SmartyException('Undefined stream variable "' . $variable . '"');
} else {
return null;
}
}
}


}
### REQUIRE_ONCE FROM core/libs/smarty/sysplugins/smarty_internal_templatebase.php
abstract class Smarty_Internal_TemplateBase extends Smarty_Internal_Data
{
public $cache_id = null;
public $compile_id = null;
public $caching = false;
public $cache_lifetime = 3600;
public function isCached($template = null, $cache_id = null, $compile_id = null, $parent = null)
{
if ($template === null && $this instanceof $this->template_class) {
$template = $this;
} else {
if (!($template instanceof $this->template_class)) {
if ($parent === null) {
$parent = $this;
}
$smarty = isset($this->smarty) ? $this->smarty : $this;
$template = $smarty->createTemplate($template, $cache_id, $compile_id, $parent, false);
}
}
if (!isset($template->cached)) {
$template->loadCached();
}
return $template->cached->isCached($template);
}
public function createData($parent = null, $name = null)
{
$dataObj = new Smarty_Data($parent, $this, $name);
if ($this->debugging) {
Smarty_Internal_Debug::register_data($dataObj);
}
return $dataObj;
}
public function getTemplateId($template_name, $cache_id = null, $compile_id = null)
{
$cache_id = isset($cache_id) ? $cache_id : $this->cache_id;
$compile_id = isset($compile_id) ? $compile_id : $this->compile_id;
$smarty = isset($this->smarty) ? $this->smarty : $this;
if ($smarty->allow_ambiguous_resources) {
$_templateId = Smarty_Resource::getUniqueTemplateName($this, $template_name) . "#{$cache_id}#{$compile_id}";
} else {
$_templateId = $smarty->joined_template_dir . "#{$template_name}#{$cache_id}#{$compile_id}";
}
if (isset($_templateId[150])) {
$_templateId = sha1($_templateId);
}
return $_templateId;
}
public function registerPlugin($type, $tag, $callback, $cacheable = true, $cache_attr = null)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
if (isset($smarty->registered_plugins[$type][$tag])) {
throw new SmartyException("Plugin tag \"{$tag}\" already registered");
} elseif (!is_callable($callback)) {
throw new SmartyException("Plugin \"{$tag}\" not callable");
} else {
$smarty->registered_plugins[$type][$tag] = array($callback, (bool) $cacheable, (array) $cache_attr);
}
return $this;
}
public function unregisterPlugin($type, $tag)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
if (isset($smarty->registered_plugins[$type][$tag])) {
unset($smarty->registered_plugins[$type][$tag]);
}
return $this;
}
public function registerResource($type, $callback)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
$smarty->registered_resources[$type] = $callback instanceof Smarty_Resource ? $callback : array($callback, false);
return $this;
}
public function unregisterResource($type)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
if (isset($smarty->registered_resources[$type])) {
unset($smarty->registered_resources[$type]);
}
return $this;
}
public function registerCacheResource($type, Smarty_CacheResource $callback)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
$smarty->registered_cache_resources[$type] = $callback;
return $this;
}
public function unregisterCacheResource($type)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
if (isset($smarty->registered_cache_resources[$type])) {
unset($smarty->registered_cache_resources[$type]);
}
return $this;
}
public function registerObject($object_name, $object_impl, $allowed = array(), $smarty_args = true, $block_methods = array())
{
if (!empty($allowed)) {
foreach ((array) $allowed as $method) {
if (!is_callable(array($object_impl, $method)) && !property_exists($object_impl, $method)) {
throw new SmartyException("Undefined method or property '$method' in registered object");
}
}
}
if (!empty($block_methods)) {
foreach ((array) $block_methods as $method) {
if (!is_callable(array($object_impl, $method))) {
throw new SmartyException("Undefined method '$method' in registered object");
}
}
}
$smarty = isset($this->smarty) ? $this->smarty : $this;
$smarty->registered_objects[$object_name] =
array($object_impl, (array) $allowed, (boolean) $smarty_args, (array) $block_methods);
return $this;
}
public function getRegisteredObject($name)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
if (!isset($smarty->registered_objects[$name])) {
throw new SmartyException("'$name' is not a registered object");
}
if (!is_object($smarty->registered_objects[$name][0])) {
throw new SmartyException("registered '$name' is not an object");
}
return $smarty->registered_objects[$name][0];
}
public function unregisterObject($name)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
if (isset($smarty->registered_objects[$name])) {
unset($smarty->registered_objects[$name]);
}
return $this;
}
public function registerClass($class_name, $class_impl)
{
if (!class_exists($class_impl)) {
throw new SmartyException("Undefined class '$class_impl' in register template class");
}
$smarty = isset($this->smarty) ? $this->smarty : $this;
$smarty->registered_classes[$class_name] = $class_impl;
return $this;
}
public function registerDefaultPluginHandler($callback)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
if (is_callable($callback)) {
$smarty->default_plugin_handler_func = $callback;
} else {
throw new SmartyException("Default plugin handler '$callback' not callable");
}
return $this;
}
public function registerDefaultTemplateHandler($callback)
{
Smarty_Internal_Extension_DefaultTemplateHandler::registerDefaultTemplateHandler($this, $callback);
return $this;
}
public function registerDefaultConfigHandler($callback)
{
Smarty_Internal_Extension_DefaultTemplateHandler::registerDefaultConfigHandler($this, $callback);
return $this;
}
public function registerFilter($type, $callback)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
$smarty->registered_filters[$type][$this->_get_filter_name($callback)] = $callback;
return $this;
}
public function unregisterFilter($type, $callback)
{
$name = $this->_get_filter_name($callback);
$smarty = isset($this->smarty) ? $this->smarty : $this;
if (isset($smarty->registered_filters[$type][$name])) {
unset($smarty->registered_filters[$type][$name]);
}
return $this;
}
public function _get_filter_name($function_name)
{
if (is_array($function_name)) {
$_class_name = (is_object($function_name[0]) ?
get_class($function_name[0]) : $function_name[0]);
return $_class_name . '_' . $function_name[1];
} else {
return $function_name;
}
}
public function loadFilter($type, $name)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
$_plugin = "smarty_{$type}filter_{$name}";
$_filter_name = $_plugin;
if ($smarty->loadPlugin($_plugin)) {
if (class_exists($_plugin, false)) {
$_plugin = array($_plugin, 'execute');
}
if (is_callable($_plugin)) {
$smarty->registered_filters[$type][$_filter_name] = $_plugin;
return true;
}
}
throw new SmartyException("{$type}filter \"{$name}\" not callable");
}
public function unloadFilter($type, $name)
{
$smarty = isset($this->smarty) ? $this->smarty : $this;
$_filter_name = "smarty_{$type}filter_{$name}";
if (isset($smarty->registered_filters[$type][$_filter_name])) {
unset ($smarty->registered_filters[$type][$_filter_name]);
}
return $this;
}
private function replaceCamelcase($match)
{
return "_" . strtolower($match[1]);
}
public function __call($name, $args)
{
static $_prefixes = array('set' => true, 'get' => true);
static $_resolved_property_name = array();
static $_resolved_property_source = array();
$first3 = strtolower(substr($name, 0, 3));
if (isset($_prefixes[$first3]) && isset($name[3]) && $name[3] !== '_') {
if (isset($_resolved_property_name[$name])) {
$property_name = $_resolved_property_name[$name];
} else {
$property_name = strtolower(substr($name, 3, 1)) . substr($name, 4);
$property_name = preg_replace_callback('/([A-Z])/', array($this, 'replaceCamelcase'), $property_name);
$_resolved_property_name[$name] = $property_name;
}
if (isset($_resolved_property_source[$property_name])) {
$status = $_resolved_property_source[$property_name];
} else {
$status = null;
if (property_exists($this, $property_name)) {
$status = true;
} elseif (property_exists($this->smarty, $property_name)) {
$status = false;
}
$_resolved_property_source[$property_name] = $status;
}
$smarty = null;
if ($status === true) {
$smarty = $this;
} elseif ($status === false) {
$smarty = $this->smarty;
}
if ($smarty) {
if ($first3 == 'get') {
return $smarty->$property_name;
} else {
return $smarty->$property_name = $args[0];
}
}
throw new SmartyException("property '$property_name' does not exist.");
}
throw new SmartyException("Call of unknown method '$name'.");
}
}


### REQUIRE_ONCE FROM core/libs/smarty/sysplugins/smarty_internal_template.php
class Smarty_Internal_Template extends Smarty_Internal_TemplateBase
{
public $smarty = null;
public $template_resource = null;
public $templateId = null;
public $mustCompile = null;
public $has_nocache_code = false;
public $properties = array('file_dependency' => array(),
'nocache_hash'    => '',
'tpl_function'    => array(),
);
public $required_plugins = array('compiled' => array(), 'nocache' => array());
public $block_data = array();
public $variable_filters = array();
public $used_tags = array();
public $allow_relative_path = false;
public $_capture_stack = array(0 => array());
public function __construct($template_resource, $smarty, $_parent = null, $_cache_id = null, $_compile_id = null, $_caching = null, $_cache_lifetime = null)
{
$this->smarty = &$smarty;
$this->cache_id = $_cache_id === null ? $this->smarty->cache_id : $_cache_id;
$this->compile_id = $_compile_id === null ? $this->smarty->compile_id : $_compile_id;
$this->caching = $_caching === null ? $this->smarty->caching : $_caching;
if ($this->caching === true) {
$this->caching = Smarty::CACHING_LIFETIME_CURRENT;
}
$this->cache_lifetime = $_cache_lifetime === null ? $this->smarty->cache_lifetime : $_cache_lifetime;
$this->parent = $_parent;
$this->template_resource = $template_resource;
if ($this->parent instanceof Smarty_Internal_Template) {
$this->block_data = $this->parent->block_data;
}
}
public function fetch()
{
return $this->render(true, false, false);
}
public function display()
{
$this->render(true, false, true);
}
public function render($merge_tpl_vars = false, $no_output_filter = true, $display = null)
{
$parentIsTpl = $this->parent instanceof Smarty_Internal_Template;
if ($this->smarty->debugging) {
Smarty_Internal_Debug::start_template($this, $display);
}
$save_tpl_vars = null;
$save_config_vars = null;
if ($merge_tpl_vars) {
$save_tpl_vars = $this->tpl_vars;
$save_config_vars = $this->config_vars;
$ptr_array = array($this);
$ptr = $this;
while (isset($ptr->parent)) {
$ptr_array[] = $ptr = $ptr->parent;
}
$ptr_array = array_reverse($ptr_array);
$parent_ptr = reset($ptr_array);
$tpl_vars = $parent_ptr->tpl_vars;
$config_vars = $parent_ptr->config_vars;
while ($parent_ptr = next($ptr_array)) {
if (!empty($parent_ptr->tpl_vars)) {
$tpl_vars = array_merge($tpl_vars, $parent_ptr->tpl_vars);
}
if (!empty($parent_ptr->config_vars)) {
$config_vars = array_merge($config_vars, $parent_ptr->config_vars);
}
}
if (!empty(Smarty::$global_tpl_vars)) {
$tpl_vars = array_merge(Smarty::$global_tpl_vars, $tpl_vars);
}
$this->tpl_vars = $tpl_vars;
$this->config_vars = $config_vars;
}
if (!isset($this->tpl_vars['smarty'])) {
$this->tpl_vars['smarty'] = new Smarty_Variable;
}
$_smarty_old_error_level = isset($this->smarty->error_reporting) ? error_reporting($this->smarty->error_reporting) : null;
if (!$this->smarty->debugging && $this->smarty->debugging_ctrl == 'URL') {
Smarty_Internal_Debug::debugUrl($this);
}
if (!isset($this->source)) {
$this->loadSource();
}
if (!$this->source->exists) {
if ($parentIsTpl) {
$parent_resource = " in '{$this->parent->template_resource}'";
} else {
$parent_resource = '';
}
throw new SmartyException("Unable to load template {$this->source->type} '{$this->source->name}'{$parent_resource}");
}
if ($this->source->recompiled) {
$this->caching = false;
}
$isCacheTpl = $this->caching == Smarty::CACHING_LIFETIME_CURRENT || $this->caching == Smarty::CACHING_LIFETIME_SAVED;
if ($isCacheTpl) {
if (!isset($this->cached)) {
$this->loadCached();
}
$this->cached->isCached($this);
}
if (!($isCacheTpl) || !$this->cached->valid) {
if ($isCacheTpl) {
$this->properties['tpl_function'] = array();
}
if ($this->smarty->debugging) {
Smarty_Internal_Debug::start_render($this);
}
if (!$this->source->uncompiled) {
if (!isset($this->compiled)) {
$this->loadCompiled();
}
$content = $this->compiled->render($this);
} else {
$content = $this->source->renderUncompiled($this);
}
if (!$this->source->recompiled && empty($this->properties['file_dependency'][$this->source->uid])) {
$this->properties['file_dependency'][$this->source->uid] = array($this->source->filepath, $this->source->timestamp, $this->source->type);
}
if ($parentIsTpl) {
$this->parent->properties['file_dependency'] = array_merge($this->parent->properties['file_dependency'], $this->properties['file_dependency']);
}
if ($this->smarty->debugging) {
Smarty_Internal_Debug::end_render($this);
}
if (!$this->source->recompiled && $isCacheTpl) {
if ($this->smarty->debugging) {
Smarty_Internal_Debug::start_cache($this);
}
$this->cached->updateCache($this, $content, $no_output_filter);
$compile_check = $this->smarty->compile_check;
$this->smarty->compile_check = false;
if ($parentIsTpl) {
$this->properties['tpl_function'] = $this->parent->properties['tpl_function'];
}
if (!$this->cached->processed) {
$this->cached->process($this);
}
$this->smarty->compile_check = $compile_check;
$content = $this->getRenderedTemplateCode();
if ($this->smarty->debugging) {
Smarty_Internal_Debug::end_cache($this);
}
} else {
if (!empty($this->properties['nocache_hash']) && !empty($this->parent->properties['nocache_hash'])) {
$content = str_replace("{$this->properties['nocache_hash']}", $this->parent->properties['nocache_hash'], $content);
$this->parent->has_nocache_code = $this->parent->has_nocache_code || $this->has_nocache_code;
}
}
} else {
if ($this->smarty->debugging) {
Smarty_Internal_Debug::start_cache($this);
}
$content = $this->cached->render($this);
if ($this->smarty->debugging) {
Smarty_Internal_Debug::end_cache($this);
}
}
if ((!$this->caching || $this->has_nocache_code || $this->source->recompiled) && !$no_output_filter && (isset($this->smarty->autoload_filters['output']) || isset($this->smarty->registered_filters['output']))) {
$content = Smarty_Internal_Filter_Handler::runFilter('output', $content, $this);
}
if (isset($_smarty_old_error_level)) {
error_reporting($_smarty_old_error_level);
}
if ($display) {
if ($this->caching && $this->smarty->cache_modified_check) {
$this->cached->cacheModifiedCheck($this, $content);
} else {
echo $content;
}
if ($this->smarty->debugging) {
Smarty_Internal_Debug::end_template($this);
}
if ($this->smarty->debugging) {
Smarty_Internal_Debug::display_debug($this, true);
}
if ($merge_tpl_vars) {
$this->tpl_vars = $save_tpl_vars;
$this->config_vars = $save_config_vars;
}
return '';
} else {
if ($merge_tpl_vars) {
$this->tpl_vars = $save_tpl_vars;
$this->config_vars = $save_config_vars;
}
if ($this->smarty->debugging) {
Smarty_Internal_Debug::end_template($this);
}
if ($this->smarty->debugging == 2 and $display === false) {
if ($this->smarty->debugging) {
Smarty_Internal_Debug::display_debug($this, true);
}
}
if ($parentIsTpl) {
$this->parent->properties['tpl_function'] = array_merge($this->parent->properties['tpl_function'], $this->properties['tpl_function']);
foreach ($this->required_plugins as $code => $tmp1) {
foreach ($tmp1 as $name => $tmp) {
foreach ($tmp as $type => $data) {
$this->parent->required_plugins[$code][$name][$type] = $data;
}
}
}
}
return $content;
}
}
public function getRenderedTemplateCode()
{
$level = ob_get_level();
try {
ob_start();
if (empty($this->properties['unifunc']) || !is_callable($this->properties['unifunc'])) {
throw new SmartyException("Invalid compiled template for '{$this->template_resource}'");
}
if (isset($this->smarty->security_policy)) {
$this->smarty->security_policy->startTemplate($this);
}
array_unshift($this->_capture_stack, array());
$this->properties['unifunc']($this);
if (isset($this->_capture_stack[0][0])) {
$this->capture_error();
}
array_shift($this->_capture_stack);
if (isset($this->smarty->security_policy)) {
$this->smarty->security_policy->exitTemplate($this);
}
return ob_get_clean();
}
catch (Exception $e) {
while (ob_get_level() > $level) {
ob_end_clean();
}
throw $e;
}
}
public function mustCompile()
{
if (!$this->source->exists) {
if ($this->parent instanceof Smarty_Internal_Template) {
$parent_resource = " in '$this->parent->template_resource}'";
} else {
$parent_resource = '';
}
throw new SmartyException("Unable to load template {$this->source->type} '{$this->source->name}'{$parent_resource}");
}
if ($this->mustCompile === null) {
$this->mustCompile = (!$this->source->uncompiled && ($this->smarty->force_compile || $this->source->recompiled || $this->compiled->timestamp === false ||
($this->smarty->compile_check && $this->compiled->timestamp < $this->source->timestamp)));
}
return $this->mustCompile;
}
public function compileTemplateSource()
{
return $this->compiled->compileTemplateSource($this);
}
public function writeCachedContent($content)
{
return $this->cached->writeCachedContent($this, $content);
}
public function getSubTemplate($template, $cache_id, $compile_id, $caching, $cache_lifetime, $data, $parent_scope)
{
$tpl = $this->setupSubTemplate($template, $cache_id, $compile_id, $caching, $cache_lifetime, $data, $parent_scope);
return $tpl->render();
}
public function setupSubTemplate($template, $cache_id, $compile_id, $caching, $cache_lifetime, $data, $parent_scope)
{
$_templateId = $this->getTemplateId($template, $cache_id, $compile_id);
if (isset($this->smarty->template_objects[$_templateId])) {
$tpl = clone $this->smarty->template_objects[$_templateId];
$tpl->parent = $this;
if ((bool) $tpl->caching !== (bool) $caching) {
unset($tpl->compiled);
}
$tpl->caching = $caching;
$tpl->cache_lifetime = $cache_lifetime;
} else {
$tpl = new $this->smarty->template_class($template, $this->smarty, $this, $cache_id, $compile_id, $caching, $cache_lifetime);
$tpl->templateId = $_templateId;
}
if ($parent_scope == Smarty::SCOPE_LOCAL) {
$tpl->tpl_vars = $this->tpl_vars;
$tpl->tpl_vars['smarty'] = clone $this->tpl_vars['smarty'];
} elseif ($parent_scope == Smarty::SCOPE_PARENT) {
$tpl->tpl_vars = &$this->tpl_vars;
} elseif ($parent_scope == Smarty::SCOPE_GLOBAL) {
$tpl->tpl_vars = &Smarty::$global_tpl_vars;
} elseif (($scope_ptr = $this->getScopePointer($parent_scope)) == null) {
$tpl->tpl_vars = &$this->tpl_vars;
} else {
$tpl->tpl_vars = &$scope_ptr->tpl_vars;
}
$tpl->config_vars = $this->config_vars;
if (!empty($data)) {
foreach ($data as $_key => $_val) {
$tpl->tpl_vars[$_key] = new Smarty_Variable($_val);
}
}
$tpl->properties['tpl_function'] = $this->properties['tpl_function'];
return $tpl;
}
public function getInlineSubTemplate($template, $cache_id, $compile_id, $caching, $cache_lifetime, $data, $parent_scope, $hash, $content_func)
{
$tpl = $this->setupSubTemplate($template, $cache_id, $compile_id, $caching, $cache_lifetime, $data, $parent_scope);
$tpl->properties['nocache_hash'] = $hash;
if (!isset($this->smarty->template_objects[$tpl->templateId])) {
$this->smarty->template_objects[$tpl->templateId] = $tpl;
}
if ($this->smarty->debugging) {
Smarty_Internal_Debug::start_template($tpl);
Smarty_Internal_Debug::start_render($tpl);
}
$tpl->properties['unifunc'] = $content_func;
$output = $tpl->getRenderedTemplateCode();
if ($this->smarty->debugging) {
Smarty_Internal_Debug::end_template($tpl);
Smarty_Internal_Debug::end_render($tpl);
}
if (!empty($tpl->properties['file_dependency'])) {
$this->properties['file_dependency'] = array_merge($this->properties['file_dependency'], $tpl->properties['file_dependency']);
}
$this->properties['tpl_function'] = $tpl->properties['tpl_function'];
return str_replace($tpl->properties['nocache_hash'], $this->properties['nocache_hash'], $output);
}
public function callTemplateFunction($name, Smarty_Internal_Template $_smarty_tpl, $params, $nocache)
{
if (isset($_smarty_tpl->properties['tpl_function'][$name])) {
if (!$_smarty_tpl->caching || ($_smarty_tpl->caching && $nocache)) {
$function = $_smarty_tpl->properties['tpl_function'][$name]['call_name'];
} else {
if (isset($_smarty_tpl->properties['tpl_function'][$name]['call_name_caching'])) {
$function = $_smarty_tpl->properties['tpl_function'][$name]['call_name_caching'];
} else {
$function = $_smarty_tpl->properties['tpl_function'][$name]['call_name'];
}
}
if (function_exists($function)) {
$function ($_smarty_tpl, $params);
return;
}
if (Smarty_Internal_Function_Call_Handler::call($name, $_smarty_tpl, $function, $params, $nocache)) {
$function ($_smarty_tpl, $params);
return;
}
}
throw new SmartyException("Unable to find template function '{$name}'");
}
public function decodeProperties($properties, $cache = false)
{
$properties['version'] = (isset($properties['version'])) ? $properties['version'] : '';
$is_valid = true;
if (Smarty::SMARTY_VERSION != $properties['version']) {
$is_valid = false;
} elseif ((!$cache && $this->smarty->compile_check || $cache && ($this->smarty->compile_check === true || $this->smarty->compile_check === Smarty::COMPILECHECK_ON)) && !empty($properties['file_dependency'])) {
foreach ($properties['file_dependency'] as $_file_to_check) {
if ($_file_to_check[2] == 'file' || $_file_to_check[2] == 'php') {
if ($this->source->filepath == $_file_to_check[0] && isset($this->source->timestamp)) {
$mtime = $this->source->timestamp;
} else {
$mtime = is_file($_file_to_check[0]) ? @filemtime($_file_to_check[0]) : false;
}
} elseif ($_file_to_check[2] == 'string') {
continue;
} else {
$source = Smarty_Resource::source(null, $this->smarty, $_file_to_check[0]);
$mtime = $source->timestamp;
}
if (!$mtime || $mtime > $_file_to_check[1]) {
$is_valid = false;
break;
}
}
}
if ($cache) {
if ($this->caching === Smarty::CACHING_LIFETIME_SAVED &&
$properties['cache_lifetime'] >= 0 &&
(time() > ($this->cached->timestamp + $properties['cache_lifetime']))
) {
$is_valid = false;
}
$this->cached->valid = $is_valid;
} else {
$this->mustCompile = !$is_valid;
}
if ($is_valid) {
$this->has_nocache_code = $properties['has_nocache_code'];
if (isset($properties['cache_lifetime'])) {
$this->properties['cache_lifetime'] = $properties['cache_lifetime'];
}
if (isset($properties['file_dependency'])) {
$this->properties['file_dependency'] = array_merge($this->properties['file_dependency'], $properties['file_dependency']);
}
if (isset($properties['tpl_function'])) {
$this->properties['tpl_function'] = array_merge($this->properties['tpl_function'], $properties['tpl_function']);
}
$this->properties['version'] = $properties['version'];
$this->properties['unifunc'] = $properties['unifunc'];
}
return $is_valid;
}
public function createLocalArrayVariable($tpl_var, $nocache = false, $scope = Smarty::SCOPE_LOCAL)
{
if (!isset($this->tpl_vars[$tpl_var])) {
$this->tpl_vars[$tpl_var] = new Smarty_Variable(array(), $nocache, $scope);
} else {
$this->tpl_vars[$tpl_var] = clone $this->tpl_vars[$tpl_var];
if ($scope != Smarty::SCOPE_LOCAL) {
$this->tpl_vars[$tpl_var]->scope = $scope;
}
if (!(is_array($this->tpl_vars[$tpl_var]->value) || $this->tpl_vars[$tpl_var]->value instanceof ArrayAccess)) {
settype($this->tpl_vars[$tpl_var]->value, 'array');
}
}
}
public function &getScope($scope)
{
if ($scope == Smarty::SCOPE_PARENT && !empty($this->parent)) {
return $this->parent->tpl_vars;
} elseif ($scope == Smarty::SCOPE_ROOT && !empty($this->parent)) {
$ptr = $this->parent;
while (!empty($ptr->parent)) {
$ptr = $ptr->parent;
}
return $ptr->tpl_vars;
} elseif ($scope == Smarty::SCOPE_GLOBAL) {
return Smarty::$global_tpl_vars;
}
$null = null;
return $null;
}
public function getScopePointer($scope)
{
if ($scope == Smarty::SCOPE_PARENT && !empty($this->parent)) {
return $this->parent;
} elseif ($scope == Smarty::SCOPE_ROOT && !empty($this->parent)) {
$ptr = $this->parent;
while (!empty($ptr->parent)) {
$ptr = $ptr->parent;
}
return $ptr;
}
return null;
}
public function _count($value)
{
if (is_array($value) === true || $value instanceof Countable) {
return count($value);
} elseif ($value instanceof IteratorAggregate) {
return iterator_count($value->getIterator());
} elseif ($value instanceof Iterator) {
return iterator_count($value);
} elseif ($value instanceof PDOStatement) {
return $value->rowCount();
} elseif ($value instanceof Traversable) {
return iterator_count($value);
} elseif ($value instanceof ArrayAccess) {
if ($value->offsetExists(0)) {
return 1;
}
} elseif (is_object($value)) {
return count($value);
}
return 0;
}
public function capture_error()
{
throw new SmartyException("Not matching {capture} open/close in \"{$this->template_resource}\"");
}
public function clearCache($exp_time = null)
{
Smarty_CacheResource::invalidLoadedCache($this->smarty);
return $this->cached->handler->clear($this->smarty, $this->template_resource, $this->cache_id, $this->compile_id, $exp_time);
}
public function loadSource()
{
$this->source = Smarty_Template_Source::load($this);
if ($this->smarty->template_resource_caching && !$this->source->recompiled && isset($this->templateId)) {
$this->smarty->template_objects[$this->templateId] = $this;
}
}
public function loadCompiled()
{
if (!isset($this->compiled)) {
if (!class_exists('Smarty_Template_Compiled', false)) {
require SMARTY_SYSPLUGINS_DIR . 'smarty_template_compiled.php';
}
$this->compiled = Smarty_Template_Compiled::load($this);
}
}
public function loadCached()
{
if (!isset($this->cached)) {
if (!class_exists('Smarty_Template_Cached', false)) {
require SMARTY_SYSPLUGINS_DIR . 'smarty_template_cached.php';
}
$this->cached = Smarty_Template_Cached::load($this);
}
}
public function loadCompiler()
{
$this->smarty->loadPlugin($this->source->compiler_class);
$this->compiler = new $this->source->compiler_class($this->source->template_lexer_class, $this->source->template_parser_class, $this->smarty);
}
public function __call($name, $args)
{
if (method_exists($this->smarty, $name)) {
return call_user_func_array(array($this->smarty, $name), $args);
}
return parent::__call($name, $args);
}
public function __set($property_name, $value)
{
switch ($property_name) {
case 'source':
case 'compiled':
case 'cached':
case 'compiler':
$this->$property_name = $value;
return;
default:
if (property_exists($this->smarty, $property_name)) {
$this->smarty->$property_name = $value;
return;
}
}
throw new SmartyException("invalid template property '$property_name'.");
}
public function __get($property_name)
{
switch ($property_name) {
case 'source':
$this->loadSource();
return $this->source;
case 'compiled':
$this->loadCompiled();
return $this->compiled;
case 'cached':
$this->loadCached();
return $this->cached;
case 'compiler':
$this->loadCompiler();
return $this->compiler;
default:
if (property_exists($this->smarty, $property_name)) {
return $this->smarty->$property_name;
}
}
throw new SmartyException("template property '$property_name' does not exist.");
}
public function __destruct()
{
if ($this->smarty->cache_locking && isset($this->cached) && $this->cached->is_locked) {
$this->cached->handler->releaseLock($this->smarty, $this->cached);
}
}
}


### REQUIRE_ONCE FROM core/libs/smarty/sysplugins/smarty_resource.php
abstract class Smarty_Resource
{
public $uncompiled = false;
public $recompiled = false;
public $handler = null;
public static $sources = array();
public static $compileds = array();
protected static $sysplugins = array(
'file'    => 'smarty_internal_resource_file.php',
'string'  => 'smarty_internal_resource_string.php',
'extends' => 'smarty_internal_resource_extends.php',
'stream'  => 'smarty_internal_resource_stream.php',
'eval'    => 'smarty_internal_resource_eval.php',
'php'     => 'smarty_internal_resource_php.php'
);
public $compiler_class = 'Smarty_Internal_SmartyTemplateCompiler';
public $template_lexer_class = 'Smarty_Internal_Templatelexer';
public $template_parser_class = 'Smarty_Internal_Templateparser';
abstract public function getContent(Smarty_Template_Source $source);
abstract public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = null);
public function populateTimestamp(Smarty_Template_Source $source)
{
}
public function buildUniqueResourceName(Smarty $smarty, $resource_name, $isConfig = false)
{
if ($isConfig) {
return get_class($this) . '#' . $smarty->joined_config_dir . '#' . $resource_name;
} else {
return get_class($this) . '#' . $smarty->joined_template_dir . '#' . $resource_name;
}
}
public function getBasename(Smarty_Template_Source $source)
{
return null;
}
public static function load(Smarty $smarty, $type)
{
if (isset($smarty->_resource_handlers[$type])) {
return $smarty->_resource_handlers[$type];
}
if (isset($smarty->registered_resources[$type])) {
if ($smarty->registered_resources[$type] instanceof Smarty_Resource) {
$smarty->_resource_handlers[$type] = $smarty->registered_resources[$type];
} else {
$smarty->_resource_handlers[$type] = new Smarty_Internal_Resource_Registered();
}
return $smarty->_resource_handlers[$type];
}
if (isset(self::$sysplugins[$type])) {
$_resource_class = 'Smarty_Internal_Resource_' . ucfirst($type);
if (!class_exists($_resource_class, false)) {
require SMARTY_SYSPLUGINS_DIR . self::$sysplugins[$type];
}
return $smarty->_resource_handlers[$type] = new $_resource_class();
}
$_resource_class = 'Smarty_Resource_' . ucfirst($type);
if ($smarty->loadPlugin($_resource_class)) {
if (class_exists($_resource_class, false)) {
return $smarty->_resource_handlers[$type] = new $_resource_class();
} else {
$smarty->registerResource($type, array(
"smarty_resource_{$type}_source",
"smarty_resource_{$type}_timestamp",
"smarty_resource_{$type}_secure",
"smarty_resource_{$type}_trusted"
));
return self::load($smarty, $type);
}
}
$_known_stream = stream_get_wrappers();
if (in_array($type, $_known_stream)) {
if (is_object($smarty->security_policy)) {
$smarty->security_policy->isTrustedStream($type);
}
return $smarty->_resource_handlers[$type] = new Smarty_Internal_Resource_Stream();;
}
throw new SmartyException("Unknown resource type '{$type}'");
}
public static function parseResourceName($resource_name, $default_resource)
{
if (preg_match('/^([A-Za-z0-9_\-]{2,})[:]/', $resource_name, $match)) {
$type = $match[1];
$name = substr($resource_name, strlen($match[0]));
} else {
$type = $default_resource;
$name = $resource_name;
}
return array($name, $type);
}
public static function getUniqueTemplateName($template, $template_resource)
{
$smarty = isset($template->smarty) ? $template->smarty : $template;
list($name, $type) = self::parseResourceName($template_resource, $smarty->default_resource_type);
$resource = Smarty_Resource::load($smarty, $type);
$_file_is_dotted = $name[0] == '.' && ($name[1] == '.' || $name[1] == '/');
if ($template instanceof Smarty_Internal_Template && $_file_is_dotted && ($template->source->type == 'file' || $template->parent->source->type == 'extends')) {
$name = dirname($template->source->filepath) . DS . $name;
}
return $resource->buildUniqueResourceName($smarty, $name);
}
public static function source(Smarty_Internal_Template $_template = null, Smarty $smarty = null, $template_resource = null)
{
return Smarty_Template_Source::load($_template, $smarty, $template_resource);
}
}


### REQUIRE_ONCE FROM core/libs/smarty/sysplugins/smarty_variable.php
class Smarty_Variable
{
public $value = null;
public $nocache = false;
public $scope = Smarty::SCOPE_LOCAL;
public function __construct($value = null, $nocache = false, $scope = Smarty::SCOPE_LOCAL)
{
$this->value = $value;
$this->nocache = $nocache;
$this->scope = $scope;
}
public function __toString()
{
return (string) $this->value;
}
}


### REQUIRE_ONCE FROM core/libs/smarty/sysplugins/smarty_template_source.php
class Smarty_Template_Source
{
public $compiler_class = null;
public $template_lexer_class = null;
public $template_parser_class = null;
public $uid = null;
public $resource = null;
public $type = null;
public $name = null;
public $unique_resource = null;
public $filepath = null;
public $basename = null;
public $components = null;
public $handler = null;
public $smarty = null;
public $isConfig = false;
public $uncompiled = false;
public $recompiled = false;
public $compileds = array();
public function __construct(Smarty_Resource $handler, Smarty $smarty, $resource, $type, $name)
{
$this->handler = $handler; // Note: prone to circular references
$this->recompiled = $handler->recompiled;
$this->uncompiled = $handler->uncompiled;
$this->compiler_class = $handler->compiler_class;
$this->template_lexer_class = $handler->template_lexer_class;
$this->template_parser_class = $handler->template_parser_class;
$this->smarty = $smarty;
$this->resource = $resource;
$this->type = $type;
$this->name = $name;
}
public static function load(Smarty_Internal_Template $_template = null, Smarty $smarty = null, $template_resource = null)
{
if ($_template) {
$smarty = $_template->smarty;
$template_resource = $_template->template_resource;
}
if (empty($template_resource)) {
throw new SmartyException('Missing template name');
}
list($name, $type) = Smarty_Resource::parseResourceName($template_resource, $smarty->default_resource_type);
$resource = Smarty_Resource::load($smarty, $type);
if ($smarty->resource_caching && !$resource->recompiled && !(isset($name[1]) && $name[0] == '.' && ($name[1] == '.' || $name[1] == '/'))) {
$unique_resource = $resource->buildUniqueResourceName($smarty, $name);
if (isset($smarty->source_objects[$unique_resource])) {
return $smarty->source_objects[$unique_resource];
}
} else {
$unique_resource = null;
}
$source = new Smarty_Template_Source($resource, $smarty, $template_resource, $type, $name);
$resource->populate($source, $_template);
if ((!isset($source->exists) || !$source->exists) && isset($_template->smarty->default_template_handler_func)) {
Smarty_Internal_Extension_DefaultTemplateHandler::_getDefault($_template, $source, $resObj);
}
if ($smarty->resource_caching && !$resource->recompiled) {
$is_relative = false;
if (!isset($unique_resource)) {
$is_relative = isset($name[1]) && $name[0] == '.' && ($name[1] == '.' || $name[1] == '/') &&
($type == 'file' || (isset($_template->parent->source) && $_template->parent->source->type == 'extends'));
$unique_resource = $resource->buildUniqueResourceName($smarty, $is_relative ? $source->filepath . $name : $name);
}
$source->unique_resource = $unique_resource;
if (!$is_relative) {
$smarty->source_objects[$unique_resource] = $source;
}
}
return $source;
}
public function renderUncompiled(Smarty_Internal_Template $_template)
{
$level = ob_get_level();
ob_start();
try {
$this->handler->renderUncompiled($_template->source, $_template);
return ob_get_clean();
}
catch (Exception $e) {
while (ob_get_level() > $level) {
ob_end_clean();
}
throw $e;
}
}
public function __set($property_name, $value)
{
switch ($property_name) {
case 'timestamp':
case 'exists':
case 'content':
case 'template':
$this->$property_name = $value;
break;
default:
throw new SmartyException("source property '$property_name' does not exist.");
}
}
public function __get($property_name)
{
switch ($property_name) {
case 'timestamp':
case 'exists':
$this->handler->populateTimestamp($this);
return $this->$property_name;
case 'content':
return $this->content = $this->handler->getContent($this);
default:
throw new SmartyException("source property '$property_name' does not exist.");
}
}
}


class Smarty extends Smarty_Internal_TemplateBase
{
const SMARTY_VERSION = '3.1.27';
const SCOPE_LOCAL = 0;
const SCOPE_PARENT = 1;
const SCOPE_ROOT = 2;
const SCOPE_GLOBAL = 3;
const CACHING_OFF = 0;
const CACHING_LIFETIME_CURRENT = 1;
const CACHING_LIFETIME_SAVED = 2;
const CLEAR_EXPIRED = - 1;
const COMPILECHECK_OFF = 0;
const COMPILECHECK_ON = 1;
const COMPILECHECK_CACHEMISS = 2;
const DEBUG_OFF = 0;
const DEBUG_ON = 1;
const DEBUG_INDIVIDUAL = 2;
const PHP_PASSTHRU = 0; //-> print tags as plain text
const PHP_QUOTE = 1; //-> escape tags as entities
const PHP_REMOVE = 2; //-> escape tags as entities
const PHP_ALLOW = 3; //-> escape tags as entities
const FILTER_POST = 'post';
const FILTER_PRE = 'pre';
const FILTER_OUTPUT = 'output';
const FILTER_VARIABLE = 'variable';
const PLUGIN_FUNCTION = 'function';
const PLUGIN_BLOCK = 'block';
const PLUGIN_COMPILER = 'compiler';
const PLUGIN_MODIFIER = 'modifier';
const PLUGIN_MODIFIERCOMPILER = 'modifiercompiler';
public static $global_tpl_vars = array();
public static $_previous_error_handler = null;
public static $_muted_directories = array('./templates_c/' => null, './cache/' => null);
public static $_MBSTRING = SMARTY_MBSTRING;
public static $_CHARSET = SMARTY_RESOURCE_CHAR_SET;
public static $_DATE_FORMAT = SMARTY_RESOURCE_DATE_FORMAT;
public static $_UTF8_MODIFIER = 'u';
public static $_IS_WINDOWS = false;
public $auto_literal = true;
public $error_unassigned = false;
public $use_include_path = false;
private $template_dir = array('./templates/');
public $joined_template_dir = './templates/';
public $joined_config_dir = './configs/';
public $default_template_handler_func = null;
public $default_config_handler_func = null;
public $default_plugin_handler_func = null;
private $compile_dir = './templates_c/';
private $plugins_dir = null;
private $cache_dir = './cache/';
private $config_dir = array('./configs/');
public $force_compile = false;
public $compile_check = true;
public $use_sub_dirs = false;
public $allow_ambiguous_resources = false;
public $merge_compiled_includes = false;
public $inheritance_merge_compiled_includes = true;
public $force_cache = false;
public $left_delimiter = "{";
public $right_delimiter = "}";
public $security_class = 'Smarty_Security';
public $security_policy = null;
public $php_handling = self::PHP_PASSTHRU;
public $allow_php_templates = false;
public $direct_access_security = true;
public $debugging = false;
public $debugging_ctrl = 'NONE';
public $smarty_debug_id = 'SMARTY_DEBUG';
public $debug_tpl = null;
public $error_reporting = null;
public $get_used_tags = false;
public $config_overwrite = true;
public $config_booleanize = true;
public $config_read_hidden = false;
public $compile_locking = true;
public $cache_locking = false;
public $locking_timeout = 10;
public $default_resource_type = 'file';
public $caching_type = 'file';
public $properties = array();
public $default_config_type = 'file';
public $source_objects = array();
public $template_objects = array();
public $resource_caching = false;
public $template_resource_caching = true;
public $cache_modified_check = false;
public $registered_plugins = array();
public $plugin_search_order = array('function', 'block', 'compiler', 'class');
public $registered_objects = array();
public $registered_classes = array();
public $registered_filters = array();
public $registered_resources = array();
public $_resource_handlers = array();
public $registered_cache_resources = array();
public $_cacheresource_handlers = array();
public $autoload_filters = array();
public $default_modifiers = array();
public $escape_html = false;
public static $_smarty_vars = array();
public $start_time = 0;
public $_file_perms = 0644;
public $_dir_perms = 0771;
public $_tag_stack = array();
public $_current_file = null;
public $_parserdebug = false;
public $_is_file_cache = array();
public function __construct()
{
if (is_callable('mb_internal_encoding')) {
mb_internal_encoding(Smarty::$_CHARSET);
}
$this->start_time = microtime(true);
if ($this->template_dir[0] !== './templates/' || isset($this->template_dir[1])) {
$this->setTemplateDir($this->template_dir);
}
if ($this->config_dir[0] !== './configs/' || isset($this->config_dir[1])) {
$this->setConfigDir($this->config_dir);
}
if ($this->compile_dir !== './templates_c/') {
unset(self::$_muted_directories['./templates_c/']);
$this->setCompileDir($this->compile_dir);
}
if ($this->cache_dir !== './cache/') {
unset(self::$_muted_directories['./cache/']);
$this->setCacheDir($this->cache_dir);
}
if (isset($this->plugins_dir)) {
$this->setPluginsDir($this->plugins_dir);
} else {
$this->setPluginsDir(SMARTY_PLUGINS_DIR);
}
if (isset($_SERVER['SCRIPT_NAME'])) {
Smarty::$global_tpl_vars['SCRIPT_NAME'] = new Smarty_Variable($_SERVER['SCRIPT_NAME']);
}
Smarty::$_IS_WINDOWS = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
if (Smarty::$_CHARSET !== 'UTF-8') {
Smarty::$_UTF8_MODIFIER = '';
}
}
public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null, $display = false, $merge_tpl_vars = true, $no_output_filter = false)
{
if ($cache_id !== null && is_object($cache_id)) {
$parent = $cache_id;
$cache_id = null;
}
if ($parent === null) {
$parent = $this;
}
$_template = is_object($template) ? $template : $this->createTemplate($template, $cache_id, $compile_id, $parent, false);
$_template->caching = $this->caching;
return $_template->render(true, false, $display);
}
public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
{
$this->fetch($template, $cache_id, $compile_id, $parent, true);
}
public function templateExists($resource_name)
{
$save = $this->template_objects;
$tpl = new $this->template_class($resource_name, $this);
$result = $tpl->source->exists;
$this->template_objects = $save;
return $result;
}
public function getGlobal($varname = null)
{
if (isset($varname)) {
if (isset(self::$global_tpl_vars[$varname])) {
return self::$global_tpl_vars[$varname]->value;
} else {
return '';
}
} else {
$_result = array();
foreach (self::$global_tpl_vars AS $key => $var) {
$_result[$key] = $var->value;
}
return $_result;
}
}
public function clearAllCache($exp_time = null, $type = null)
{
$_cache_resource = Smarty_CacheResource::load($this, $type);
Smarty_CacheResource::invalidLoadedCache($this);
return $_cache_resource->clearAll($this, $exp_time);
}
public function clearCache($template_name, $cache_id = null, $compile_id = null, $exp_time = null, $type = null)
{
$_cache_resource = Smarty_CacheResource::load($this, $type);
Smarty_CacheResource::invalidLoadedCache($this);
return $_cache_resource->clear($this, $template_name, $cache_id, $compile_id, $exp_time);
}
public function enableSecurity($security_class = null)
{
if ($security_class instanceof Smarty_Security) {
$this->security_policy = $security_class;
return $this;
} elseif (is_object($security_class)) {
throw new SmartyException("Class '" . get_class($security_class) . "' must extend Smarty_Security.");
}
if ($security_class == null) {
$security_class = $this->security_class;
}
if (!class_exists($security_class)) {
throw new SmartyException("Security class '$security_class' is not defined");
} elseif ($security_class !== 'Smarty_Security' && !is_subclass_of($security_class, 'Smarty_Security')) {
throw new SmartyException("Class '$security_class' must extend Smarty_Security.");
} else {
$this->security_policy = new $security_class($this);
}
return $this;
}
public function disableSecurity()
{
$this->security_policy = null;
return $this;
}
public function setTemplateDir($template_dir)
{
$this->template_dir = array();
foreach ((array) $template_dir as $k => $v) {
$this->template_dir[$k] = rtrim($v, '/\\') . DS;
}
$this->joined_template_dir = join(' # ', $this->template_dir);
return $this;
}
public function addTemplateDir($template_dir, $key = null)
{
$this->_addDir('template_dir', $template_dir, $key);
$this->joined_template_dir = join(' # ', $this->template_dir);
return $this;
}
public function getTemplateDir($index = null)
{
if ($index !== null) {
return isset($this->template_dir[$index]) ? $this->template_dir[$index] : null;
}
return (array) $this->template_dir;
}
public function setConfigDir($config_dir)
{
$this->config_dir = array();
foreach ((array) $config_dir as $k => $v) {
$this->config_dir[$k] = rtrim($v, '/\\') . DS;
}
$this->joined_config_dir = join(' # ', $this->config_dir);
return $this;
}
public function addConfigDir($config_dir, $key = null)
{
$this->_addDir('config_dir', $config_dir, $key);
$this->joined_config_dir = join(' # ', $this->config_dir);
return $this;
}
public function getConfigDir($index = null)
{
if ($index !== null) {
return isset($this->config_dir[$index]) ? $this->config_dir[$index] : null;
}
return (array) $this->config_dir;
}
public function setPluginsDir($plugins_dir)
{
$this->plugins_dir = array();
$this->addPluginsDir($plugins_dir);
return $this;
}
public function addPluginsDir($plugins_dir)
{
$this->plugins_dir = (array) $this->plugins_dir;
foreach ((array) $plugins_dir as $v) {
$this->plugins_dir[] = rtrim($v, '/\\') . DS;
}
$this->plugins_dir = array_unique($this->plugins_dir);
$this->_is_file_cache = array();
return $this;
}
public function getPluginsDir()
{
return (array) $this->plugins_dir;
}
public function setCompileDir($compile_dir)
{
$this->compile_dir = rtrim($compile_dir, '/\\') . DS;
if (!isset(Smarty::$_muted_directories[$this->compile_dir])) {
Smarty::$_muted_directories[$this->compile_dir] = null;
}
return $this;
}
public function getCompileDir()
{
return $this->compile_dir;
}
public function setCacheDir($cache_dir)
{
$this->cache_dir = rtrim($cache_dir, '/\\') . DS;
if (!isset(Smarty::$_muted_directories[$this->cache_dir])) {
Smarty::$_muted_directories[$this->cache_dir] = null;
}
return $this;
}
public function getCacheDir()
{
return $this->cache_dir;
}
private function _addDir($dirName, $dir, $key = null)
{
$this->$dirName = (array) $this->$dirName;
if (is_array($dir)) {
foreach ($dir as $k => $v) {
if (is_int($k)) {
$this->{$dirName}[] = rtrim($v, '/\\') . DS;
} else {
$this->{$dirName}[$k] = rtrim($v, '/\\') . DS;
}
}
} else {
if ($key !== null) {
$this->{$dirName}[$key] = rtrim($dir, '/\\') . DS;
} else {
$this->{$dirName}[] = rtrim($dir, '/\\') . DS;
}
}
}
public function setDefaultModifiers($modifiers)
{
$this->default_modifiers = (array) $modifiers;
return $this;
}
public function addDefaultModifiers($modifiers)
{
if (is_array($modifiers)) {
$this->default_modifiers = array_merge($this->default_modifiers, $modifiers);
} else {
$this->default_modifiers[] = $modifiers;
}
return $this;
}
public function getDefaultModifiers()
{
return $this->default_modifiers;
}
public function setAutoloadFilters($filters, $type = null)
{
if ($type !== null) {
$this->autoload_filters[$type] = (array) $filters;
} else {
$this->autoload_filters = (array) $filters;
}
return $this;
}
public function addAutoloadFilters($filters, $type = null)
{
if ($type !== null) {
if (!empty($this->autoload_filters[$type])) {
$this->autoload_filters[$type] = array_merge($this->autoload_filters[$type], (array) $filters);
} else {
$this->autoload_filters[$type] = (array) $filters;
}
} else {
foreach ((array) $filters as $key => $value) {
if (!empty($this->autoload_filters[$key])) {
$this->autoload_filters[$key] = array_merge($this->autoload_filters[$key], (array) $value);
} else {
$this->autoload_filters[$key] = (array) $value;
}
}
}
return $this;
}
public function getAutoloadFilters($type = null)
{
if ($type !== null) {
return isset($this->autoload_filters[$type]) ? $this->autoload_filters[$type] : array();
}
return $this->autoload_filters;
}
public function getDebugTemplate()
{
return $this->debug_tpl;
}
public function setDebugTemplate($tpl_name)
{
if (!is_readable($tpl_name)) {
throw new SmartyException("Unknown file '{$tpl_name}'");
}
$this->debug_tpl = $tpl_name;
return $this;
}
public function createTemplate($template, $cache_id = null, $compile_id = null, $parent = null, $do_clone = true)
{
if ($cache_id !== null && (is_object($cache_id) || is_array($cache_id))) {
$parent = $cache_id;
$cache_id = null;
}
if ($parent !== null && is_array($parent)) {
$data = $parent;
$parent = null;
} else {
$data = null;
}
$_templateId = $this->getTemplateId($template, $cache_id, $compile_id);
if (isset($this->template_objects[$_templateId])) {
if ($do_clone) {
$tpl = clone $this->template_objects[$_templateId];
$tpl->smarty = clone $tpl->smarty;
} else {
$tpl = $this->template_objects[$_templateId];
}
$tpl->parent = $parent;
$tpl->tpl_vars = array();
$tpl->config_vars = array();
} else {
$tpl = new $this->template_class($template, $this, $parent, $cache_id, $compile_id);
if ($do_clone) {
$tpl->smarty = clone $tpl->smarty;
}
$tpl->templateId = $_templateId;
}
if (!empty($data) && is_array($data)) {
foreach ($data as $_key => $_val) {
$tpl->tpl_vars[$_key] = new Smarty_Variable($_val);
}
}
if ($this->debugging) {
Smarty_Internal_Debug::register_template($tpl);
}
return $tpl;
}
public function loadPlugin($plugin_name, $check = true)
{
if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) {
return true;
}
$_name_parts = explode('_', $plugin_name, 3);
if (!isset($_name_parts[2]) || strtolower($_name_parts[0]) !== 'smarty') {
throw new SmartyException("plugin {$plugin_name} is not a valid name format");
}
if (strtolower($_name_parts[1]) == 'internal') {
$file = SMARTY_SYSPLUGINS_DIR . strtolower($plugin_name) . '.php';
if (isset($this->_is_file_cache[$file]) ? $this->_is_file_cache[$file] : $this->_is_file_cache[$file] = is_file($file)) {
require_once($file);
return $file;
} else {
return false;
}
}
$_plugin_filename = "{$_name_parts[1]}.{$_name_parts[2]}.php";
$_stream_resolve_include_path = function_exists('stream_resolve_include_path');
foreach ($this->getPluginsDir() as $_plugin_dir) {
$names = array($_plugin_dir . $_plugin_filename, $_plugin_dir . strtolower($_plugin_filename),);
foreach ($names as $file) {
if (isset($this->_is_file_cache[$file]) ? $this->_is_file_cache[$file] : $this->_is_file_cache[$file] = is_file($file)) {
require_once($file);
return $file;
}
if ($this->use_include_path && !preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/', $_plugin_dir)) {
if ($_stream_resolve_include_path) {
$file = stream_resolve_include_path($file);
} else {
$file = Smarty_Internal_Get_Include_Path::getIncludePath($file);
}
if ($file !== false) {
require_once($file);
return $file;
}
}
}
}
return false;
}
public function compileAllTemplates($extension = '.tpl', $force_compile = false, $time_limit = 0, $max_errors = null)
{
return Smarty_Internal_Utility::compileAllTemplates($extension, $force_compile, $time_limit, $max_errors, $this);
}
public function compileAllConfig($extension = '.conf', $force_compile = false, $time_limit = 0, $max_errors = null)
{
return Smarty_Internal_Utility::compileAllConfig($extension, $force_compile, $time_limit, $max_errors, $this);
}
public function clearCompiledTemplate($resource_name = null, $compile_id = null, $exp_time = null)
{
return Smarty_Internal_Utility::clearCompiledTemplate($resource_name, $compile_id, $exp_time, $this);
}
public function getTags(Smarty_Internal_Template $template)
{
return Smarty_Internal_Utility::getTags($template);
}
public function testInstall(&$errors = null)
{
return Smarty_Internal_TestInstall::testInstall($this, $errors);
}
public function setCompileCheck($compile_check)
{
$this->compile_check = $compile_check;
}
public function setUseSubDirs($use_sub_dirs)
{
$this->use_sub_dirs = $use_sub_dirs;
}
public function setCaching($caching)
{
$this->caching = $caching;
}
public function setCacheLifetime($cache_lifetime)
{
$this->cache_lifetime = $cache_lifetime;
}
public function setCompileId($compile_id)
{
$this->compile_id = $compile_id;
}
public function setCacheId($cache_id)
{
$this->cache_id = $cache_id;
}
public function setErrorReporting($error_reporting)
{
$this->error_reporting = $error_reporting;
}
public function setEscapeHtml($escape_html)
{
$this->escape_html = $escape_html;
}
public function setAutoLiteral($auto_literal)
{
$this->auto_literal = $auto_literal;
}
public function setForceCompile($force_compile)
{
$this->force_compile = $force_compile;
}
public function setMergeCompiledIncludes($merge_compiled_includes)
{
$this->merge_compiled_includes = $merge_compiled_includes;
}
public function setLeftDelimiter($left_delimiter)
{
$this->left_delimiter = $left_delimiter;
}
public function setRightDelimiter($right_delimiter)
{
$this->right_delimiter = $right_delimiter;
}
public function setDebugging($debugging)
{
$this->debugging = $debugging;
}
public function setConfigOverwrite($config_overwrite)
{
$this->config_overwrite = $config_overwrite;
}
public function setConfigBooleanize($config_booleanize)
{
$this->config_booleanize = $config_booleanize;
}
public function setConfigReadHidden($config_read_hidden)
{
$this->config_read_hidden = $config_read_hidden;
}
public function setCompileLocking($compile_locking)
{
$this->compile_locking = $compile_locking;
}
public function __destruct()
{
}
public function __get($name)
{
$allowed = array('template_dir' => 'getTemplateDir', 'config_dir' => 'getConfigDir',
'plugins_dir'  => 'getPluginsDir', 'compile_dir' => 'getCompileDir',
'cache_dir'    => 'getCacheDir',);
if (isset($allowed[$name])) {
return $this->{$allowed[$name]}();
} else {
trigger_error('Undefined property: ' . get_class($this) . '::$' . $name, E_USER_NOTICE);
}
}
public function __set($name, $value)
{
$allowed = array('template_dir' => 'setTemplateDir', 'config_dir' => 'setConfigDir',
'plugins_dir'  => 'setPluginsDir', 'compile_dir' => 'setCompileDir',
'cache_dir'    => 'setCacheDir',);
if (isset($allowed[$name])) {
$this->{$allowed[$name]}($value);
} else {
trigger_error('Undefined property: ' . get_class($this) . '::$' . $name, E_USER_NOTICE);
}
}
public static function mutingErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
$_is_muted_directory = false;
if (!isset(Smarty::$_muted_directories[SMARTY_DIR])) {
$smarty_dir = realpath(SMARTY_DIR);
if ($smarty_dir !== false) {
Smarty::$_muted_directories[SMARTY_DIR] = array('file'   => $smarty_dir,
'length' => strlen($smarty_dir),);
}
}
foreach (Smarty::$_muted_directories as $key => &$dir) {
if (!$dir) {
$file = realpath($key);
if ($file === false) {
unset(Smarty::$_muted_directories[$key]);
continue;
}
$dir = array('file' => $file, 'length' => strlen($file),);
}
if (!strncmp($errfile, $dir['file'], $dir['length'])) {
$_is_muted_directory = true;
break;
}
}
if (!$_is_muted_directory || ($errno && $errno & error_reporting())) {
if (Smarty::$_previous_error_handler) {
return call_user_func(Smarty::$_previous_error_handler, $errno, $errstr, $errfile, $errline, $errcontext);
} else {
return false;
}
}
}
public static function muteExpectedErrors()
{
$error_handler = array('Smarty', 'mutingErrorHandler');
$previous = set_error_handler($error_handler);
if ($previous !== $error_handler) {
Smarty::$_previous_error_handler = $previous;
}
}
public static function unmuteExpectedErrors()
{
restore_error_handler();
}
}


### REQUIRE_ONCE FROM core/libs/core/templates/Template.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Templates {
abstract class Template {
private static $_Paths = null;
public static function Factory($filename){
$resolved = self::ResolveFile($filename);
$ext = \Core\GetExtensionFromString($resolved);
switch($ext){
case 'phtml':
$template = new Backends\PHTML();
break;
case 'tpl':
$template =  new Backends\Smarty();
break;
default:
$template =  new Backends\Smarty();
break;
}
$template->setFilename($resolved);
return $template;
}
public static function ResolveFile($filename) {
if(strpos($filename, ROOT_PDIR) === 0){
return $filename;
}
$dirs = self::GetPaths();
if ($filename{0} == '/') $filename = substr($filename, 1);
foreach ($dirs as $d) {
if (file_exists($d . $filename)) return $d . $filename;
}
return null;
}
public static function GetPaths(){
if(self::$_Paths === null){
self::RequeryPaths();
}
return self::$_Paths;
}
public static function RequeryPaths() {
self::$_Paths = array();
self::$_Paths[] = ROOT_PDIR . 'themes/custom/';
self::$_Paths[] = ROOT_PDIR . 'themes/' . \ConfigHandler::Get('/theme/selected') . '/';
foreach (\Core::GetComponents() as $c) {
$d = $c->getViewSearchDir();
if ($d){
if($d{strlen($d)-1} != '/') $d .= '/';
self::$_Paths[] = $d;
}
}
}
}
} // ENDING NAMESPACE Core\Templates

namespace  {

### REQUIRE_ONCE FROM core/libs/core/templates/Exception.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Templates {
class Exception extends \Exception{
}
} // ENDING NAMESPACE Core\Templates

namespace  {

### REQUIRE_ONCE FROM core/libs/core/templates/TemplateInterface.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Templates {
interface TemplateInterface {
public function fetch($template = null);
public function render($template = null);
public function getTemplateVars($varname = null);
public function getVariable($varname);
public function assign($tpl_var, $value = null);
public function setFilename($template);
public function getBasename();
public function hasOptionalStylesheets();
public function getOptionalStylesheets();
public function hasWidgetAreas();
public function getWidgetAreas();
public function getInsertables();
public function getView();
public function setView(\View $view);
}
} // ENDING NAMESPACE Core\Templates

namespace  {

### REQUIRE_ONCE FROM core/libs/core/templates/backends/Smarty.php
} // ENDING GLOBAL NAMESPACE
namespace Core\Templates\Backends {
use Core\Templates;
class Smarty implements Templates\TemplateInterface {
private $_baseurl;
protected $_filename;
private $_smarty;
private $_view = null;
public function  __construct() {
$this->getSmarty()->addTemplateDir(Templates\Template::GetPaths());
foreach (\Core::GetComponents() as $c) {
$plugindir = $c->getSmartyPluginDirectory();
if ($plugindir) $this->getSmarty()->addPluginsDir($plugindir);
foreach($c->getSmartyPlugins() as $name => $call){
if(strpos($call, '::') !== false){
$parts = explode('::', $call);
$this->getSmarty()->registerPlugin('function', $name, $parts);
}
else{
$this->getSmarty()->registerPlugin('function', $name, $call);
}
}
}
}
public function setBaseURL($url) {
$this->_baseurl = $url;
}
public function getBaseURL() {
return $this->_baseurl;
}
public function fetch($template = null) {
$cache_id = null;
$compile_id = null;
$parent = null;
$display = false;
$merge_tpl_vars = true;
$no_output_filter = false;
if($template === null){
$file = $this->_filename;
}
else{
$file = Templates\Template::ResolveFile($template);
if($file === null){
throw new Templates\Exception('Template ' . $template . ' could not be found!');
}
}
try{
return $this->getSmarty()->fetch($file, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
}
catch(\SmartyException $e){
throw $e;
}
}
public function render($template = null){
$cache_id = null;
$compile_id = null;
$parent = null;
$display = true;
$merge_tpl_vars = true;
$no_output_filter = false;
if($template === null){
$template = $this->_filename;
}
else{
$template = Templates\Template::ResolveFile($template);
}
try{
return $this->getSmarty()->fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);
}
catch(\SmartyException $e){
throw new Templates\Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
}
}
public function getSmarty(){
if($this->_smarty === null){
$this->_smarty = new \Smarty();
$this->_smarty->caching = \Smarty::CACHING_OFF;
$this->_smarty->compile_dir = TMP_DIR . 'smarty_templates_c';
$this->_smarty->cache_dir   = TMP_DIR . 'smarty_cache';
$this->_smarty->force_compile = DEVELOPMENT_MODE ? true : false;
$this->_smarty->compile_check = DEVELOPMENT_MODE ? true : false;
$this->_smarty->assign('__core_template', $this);
}
return $this->_smarty;
}
public function getTemplateVars($varname = null) {
return $this->getSmarty()->getTemplateVars($varname);
}
public function assign($tpl_var, $value = null) {
$this->getSmarty()->assign($tpl_var, $value);
}
public function getTemplateDir(){
return $this->getSmarty()->getTemplateDir();
}
public function getVariable($varname) {
return $this->getSmarty()->getVariable($varname);
}
public function setFilename($template) {
$this->_filename = Templates\Template::ResolveFile($template);
}
public function getBasename(){
return basename($this->_filename);
}
public function hasOptionalStylesheets() {
$contents = file_get_contents($this->_filename);
return (preg_match('/{css[^}]*optional=["\']1["\'].*}/', $contents) == 1);
}
public function getOptionalStylesheets(){
$contents = file_get_contents($this->_filename);
preg_match_all('/{(css[^}]*optional=["\']1["\'][^}]*)}/', $contents, $matches);
$results = array();
foreach($matches[1] as $match){
$simple = new \SimpleXMLElement('<' . $match . '/>');
$attributes = array();
foreach($simple->attributes() as $k => $v){
$attributes[$k] = (string)$v;
}
if(!isset($attributes['src']) && isset($attributes['link'])) $attributes['src'] = $attributes['link'];
if(!isset($attributes['src']) && isset($attributes['href'])) $attributes['src'] = $attributes['href'];
if(!isset($attributes['title'])) $attributes['title'] = basename($attributes['src']);
$results[] = $attributes;
}
return $results;
}
public function hasWidgetAreas() {
$contents = file_get_contents($this->_filename);
if(strpos($contents, '{widgetarea') !== false){
return true;
}
else{
return false;
}
}
public function getWidgetAreas(){
if(!is_readable($this->_filename)){
return [];
}
$fullsearch = file_get_contents($this->_filename);
$fullsearch = preg_replace('#\{widgetarea(.*)\}#isU', '<widgetarea$1/>', $fullsearch);
$areas = [];
$dom = new \DOMDocument();
libxml_use_internal_errors(true);
try{
$dom->loadHTML('<html>' . $fullsearch . '</html>');
$nodes = $dom->getElementsByTagName('widgetarea');
$validattributes = ['name', 'installable'];
foreach($nodes as $n){
$nodedata = [];
foreach($validattributes as $k){
$nodedata[$k] = $n->getAttribute($k);
}
if(!isset($nodedata['installable'])){
$nodedata['installable'] = '';
}
$areas[ $nodedata['name'] ] = $nodedata;
}
}
catch(\Exception $e){
}
return $areas;
}
public function getInsertables() {
$insertables = [];
$contents = file_get_contents($this->_filename);
$fullsearch = $contents;
$fullsearch = preg_replace('#\{insertable(.*)\}#isU', '<insertable$1>', $fullsearch);
$fullsearch = preg_replace('#\{\/insertable[ ]*\}#', '</insertable>', $fullsearch);
$dom = new \DOMDocument();
libxml_use_internal_errors(true);
try{
@$dom->loadHTML('<html>' . $fullsearch . '</html>');
$nodes = $dom->getElementsByTagName('insertable');
$validattributes = ['accept', 'basedir', 'cols', 'default', 'description', 'name', 'options', 'rows', 'size', 'type', 'title', 'value', 'width'];
foreach($nodes as $n){
$nodedata = [];
foreach($validattributes as $k){
$nodedata[$k] = $n->getAttribute($k);
}
$inner = $dom->saveXML($n->firstChild);
if(!$nodedata['type']){
if (preg_match('/<img(.*?)>/i', $inner)) {
$nodedata['type'] = 'image';
}
elseif (preg_match('/{img(.*?)}/i', $inner)) {
$nodedata['type'] = 'image';
}
elseif (strpos($inner, "\n") === false && strpos($inner, "<") === false) {
$nodedata['type'] = 'text';
}
else {
$nodedata['type'] = 'wysiwyg';
}
}
if(!$nodedata['title']){
$nodedata['title'] = $nodedata['name'];
}
if(!$nodedata['default'] && $nodedata['value']){
$nodedata['default'] = $nodedata['value'];
}
elseif(!$nodedata['default'] && $inner){
$nodedata['default'] = $inner;
}
if(!$nodedata['value'] && $nodedata['default']){
$nodedata['value'] = $nodedata['default'];
}
switch($nodedata['type']){
case 'image':
$nodedata['type'] = 'file';
if(!$nodedata['accept'])  $nodedata['accept']  = 'image/*';
if(!$nodedata['basedir']) $nodedata['basedir'] = 'public/insertable';
break;
case 'file':
if(!$nodedata['basedir']) $nodedata['basedir'] = 'public/insertable';
break;
case 'select':
$nodedata['options'] = array_map('trim', explode('|', $nodedata['options']));
break;
}
$insertables[ $nodedata['name'] ] = $nodedata;
}
}
catch(\Exception $e){
}
return $insertables;
}
public function getView() {
return $this->_view === null ? \Core\view() : $this->_view;
}
public function setView(\View $view) {
$this->_view = $view;
}
}
} // ENDING NAMESPACE Core\Templates\Backends

namespace  {

### REQUIRE_ONCE FROM core/libs/core/UserAgent.php
} // ENDING GLOBAL NAMESPACE
namespace Core {
use Core\Filestore\Contents\ContentGZ;
class UserAgent {
private static $updateInterval =   604800; // 1 week
private static $_ini_url    =   'http://repo.corepl.us/full_php_browscap.ini.gz';
const REGEX_DELIMITER = '@';
const REGEX_MODIFIERS = 'i';
const VALUES_TO_QUOTE = 'Browser|Parent';
const ORDER_FUNC_ARGS = '$a, $b';
const ORDER_FUNC_LOGIC = '$a=strlen($a);$b=strlen($b);return$a==$b?0:($a<$b?1:-1);';
public static $Map = [
'Parent' => 'parent',
'Platform_Version' => 'platform_version',
'Comment' => 'comment',
'Browser' => 'browser',
'Version' => 'version',
'MajorVer' => 'major_ver',
'MinorVer' => 'minor_ver',
'Platform' => 'platform',
'Frames' => 'frames',
'IFrames' => 'iframes',
'Tables' => 'tables',
'Cookies' => 'cookies',
'JavaScript' => 'javascript',
'isMobileDevice' => 'is_mobile_device',
'CssVersion' => 'css_version',
'Device_Name' => 'device_name',
'Device_Maker' => 'device_maker',
'RenderingEngine_Name' => 'rendering_engine_name',
'RenderingEngine_Version' => 'rendering_engine_version',
'RenderingEngine_Description' => 'rendering_engine_description',
'Platform_Description' => 'platform_description',
'Alpha' => 'alpha',
'Beta' => 'beta',
'Win16' => 'win16',
'Win32' => 'win32',
'Win64' => 'win64',
'BackgroundSounds' => 'background_sounds',
'VBScript' => 'vbscript',
'JavaApplets' => 'java_applets',
'ActiveXControls' => 'activex_controls',
'isSyndicationReader' => 'is_syndication_reader',
'Crawler' => 'crawler',
'AolVersion' => 'aol_version',
];
public $useragent                    = null;
public $parent                       = null;
public $comment                      = null;
public $browser                      = null;
public $version                      = 0.0;
public $major_ver                    = 0;
public $minor_ver                    = 0;
public $platform                     = null;
public $platform_version             = null;
public $platform_architecture        = null;
public $platform_bits                = null;
public $frames                       = false;
public $iframes                      = false;
public $tables                       = false;
public $cookies                      = false;
public $javascript                   = false;
public $is_mobile_device             = false;
public $css_version                  = 0;
public $device_name                  = null;
public $device_maker                 = null;
public $rendering_engine_name        = null;
public $rendering_engine_version     = null;
public $rendering_engine_description = null;
public $platform_description         = null;
public $alpha                        = false;
public $beta                         = false;
public $win16                        = false;
public $win32                        = false;
public $win64                        = false;
public $background_sounds            = false;
public $vbscript                     = false;
public $java_applets                 = false;
public $activex_controls             = false;
public $is_syndication_reader        = false;
public $crawler                      = false;
public $aol_version                  = null;
protected static $_Cache = [];
public function __construct($useragent = null) {
if($useragent === null) $useragent = $_SERVER['HTTP_USER_AGENT'];
if(class_exists('DeviceDetector\\DeviceDetector')){
$dd = new \DeviceDetector\DeviceDetector($useragent);
$dd->parse();
$this->useragent = $useragent;
$c = $dd->getClient();
if($c !== null){
$this->browser = $c['name'];
$this->version = $c['version'];
$this->rendering_engine_name = isset($c['engine']) ? $c['engine'] : null;
if($this->rendering_engine_name == 'Text-based' && $this->browser == 'Lynx'){
$this->rendering_engine_name = 'libwww-FM';
}
}
elseif(($bot = $dd->getBot()) !== null){
$this->browser = $bot['name'];
}
elseif(strpos($this->useragent, 'Core Plus') !== false){
$this->browser = 'Core Plus';
$this->version = preg_replace('#.*Core Plus ([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
if($this->browser == 'MJ12 Bot'){
$this->version = preg_replace('#.*MJ12bot/v([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
if($this->browser == 'BingBot'){
$this->version = preg_replace('#.*bingbot/([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
if($this->browser == 'Baidu Spider'){
$this->version = preg_replace('#.*Baiduspider/([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
$os = $dd->getOs();
if($os !== null && sizeof($os)){
$this->platform = $os['name'];
$this->platform_architecture = $os['platform'];
$this->platform_version = $os['version'];
}
if($this->platform == 'Mac'){
$this->platform = 'MacOSX';
$this->platform_version = preg_replace('#.*Mac OS X ([0-9\._]+).*#', '$1', $this->useragent);
$this->platform_version = str_replace('_', '.', $this->platform_version);
}
if($this->platform == 'iOS'){
$this->platform_version = preg_replace('#.*OS ([0-9\._]+).*#', '$1', $this->useragent);
$this->platform_version = str_replace('_', '.', $this->platform_version);
}
if($this->platform == 'Android'){
$this->platform_version = preg_replace('#.*Android ([0-9\.]+);.*#', '$1', $this->useragent);
}
if($this->platform_architecture == 'x64'){
$this->platform_architecture = 'x86';
$this->platform_bits = 64;
}
elseif($this->platform_architecture == 'x86'){
$this->platform_bits = 32;
}
$this->crawler = $dd->isBot();
$this->is_mobile_device = $dd->isMobile();
$this->device_maker = $dd->getBrandName();
$this->device_name = $dd->getModel();
$this->_fixVersion();
}
else{
$data = self::_LoadData();
if(!isset($data['patterns'])){
$data['patterns'] = [];
}
$browser = [];
foreach ($data['patterns'] as $key => $pattern) {
if (preg_match($pattern . 'i', $useragent)) {
$browser = [
$useragent, // Original useragent
trim(strtolower($pattern), self::REGEX_DELIMITER),
$data['useragents'][$key]
];
$browser = $value = $browser + $data['browsers'][$key];
while (array_key_exists(3, $value) && $value[3]) {
$value = $data['browsers'][$value[3]];
$browser += $value;
}
if (!empty($browser[3])) {
$browser[3] = $data['useragents'][$browser[3]];
}
break;
}
}
$this->useragent = $useragent;
foreach ($browser as $key => $value) {
if ($value === 'true') {
$value = true;
} elseif ($value === 'false') {
$value = false;
}
$key = $data['properties'][$key];
if(isset(self::$Map[$key])){
$prop = self::$Map[$key];
$this->$prop = $value;
}
}
if($this->browser == 'Default Browser' || $this->browser === null){
if(stripos($this->useragent, 'iceweasel/') !== false){
$this->browser = 'Iceweasel';
$this->javascript = true;
$this->cookies = true;
$this->tables = true;
$this->frames = true;
$this->iframes = true;
}
elseif(stripos($this->useragent, 'firefox/') !== false){
$this->browser = 'Firefox';
$this->javascript = true;
$this->cookies = true;
$this->tables = true;
$this->frames = true;
$this->iframes = true;
}
elseif(stripos($this->useragent, 'googlebot/') !== false){
$this->browser = 'Googlebot';
$this->rendering_engine_name = '';
$this->javascript = true;
$this->cookies = true;
$this->tables = true;
$this->frames = true;
$this->iframes = true;
$this->crawler = true;
}
elseif(stripos($this->useragent, 'msie ') !== false){
$this->browser = 'IE';
$this->javascript = true;
$this->cookies = true;
$this->tables = true;
$this->frames = true;
$this->iframes = true;
$this->version = preg_replace('#.*MSIE ([0-9\.]+);.*#', '$1', $this->useragent);
}
elseif(stripos($this->useragent, 'lynx/') !== false){
$this->browser = 'Lynx';
$this->javascript = false;
$this->cookies = true;
$this->tables = true;
$this->frames = true;
$this->iframes = true;
$this->crawler = false;
}
elseif(stripos($this->useragent, 'wget/') !== false){
$this->browser = 'Wget';
$this->javascript = false;
$this->cookies = false;
}
elseif(stripos($this->useragent, 'MJ12bot/') !== false){
$this->browser = 'MJ12 Bot';
$this->javascript = false;
$this->cookies = false;
$this->crawler = true;
}
elseif(stripos($this->useragent, 'bingbot/') !== false){
$this->browser = 'BingBot';
$this->javascript = false;
$this->cookies = false;
$this->crawler = true;
}
elseif(stripos($this->useragent, 'Baiduspider/') !== false){
$this->browser = 'Baidu Spider';
$this->javascript = false;
$this->cookies = false;
$this->crawler = true;
}
elseif(strpos($this->useragent, 'Core Plus') !== false){
$this->browser = 'Core Plus';
$this->version = preg_replace('#.*Core Plus ([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
}
switch($this->platform){
case 'WinXP':
case 'Win32':
$this->platform = 'Windows';
$this->platform_version = 'XP';
break;
case 'WinVista':
$this->platform = 'Windows';
$this->platform_version = 'Vista';
break;
case 'Win7':
$this->platform = 'Windows';
$this->platform_version = '7';
break;
case 'Win8':
$this->platform = 'Windows';
$this->platform_version = '8';
break;
case 'Win8.1':
$this->platform = 'Windows';
$this->platform_version = '8.1';
break;
case 'Win10':
$this->platform = 'Windows';
$this->platform_version = '10';
break;
case 'Linux':
if(strpos($this->useragent, 'Ubuntu') !== false){
$this->platform = 'Ubuntu';
}
else{
$this->platform = 'GNU/Linux';
}
break;
case 'GNU/Linux':
if(strpos($this->useragent, 'Ubuntu') !== false){
$this->platform = 'Ubuntu';
}
break;
case 'MacOSX':
$this->platform_version = preg_replace('#.*Mac OS X ([0-9\._]+).*#', '$1', $this->useragent);
$this->platform_version = str_replace('_', '.', $this->platform_version);
break;
case 'Android':
$this->platform_version = preg_replace('#.*Android ([0-9\.]+);.*#', '$1', $this->useragent);
$this->is_mobile_device = true;
break;
case 'iOS':
$this->platform_version = preg_replace('#.*OS ([0-9\._]+).*#', '$1', $this->useragent);
$this->platform_version = str_replace('_', '.', $this->platform_version);
$this->is_mobile_device = true;
break;
}
if($this->browser == 'Firefox' && stripos($this->useragent, 'iceweasel/') !== false){
$this->browser = 'Iceweasel';
$this->version = preg_replace('#.*Iceweasel/([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
if($this->browser == 'MJ12 Bot'){
$this->version = preg_replace('#.*MJ12bot/v([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
if($this->browser == 'BingBot'){
$this->version = preg_replace('#.*bingbot/([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
if($this->browser == 'Baidu Spider'){
$this->version = preg_replace('#.*Baiduspider/([0-9]+\.[0-9]+).*#', '$1', $this->useragent);
}
if($this->browser == 'IE'){
$this->browser = 'Internet Explorer';
}
$this->_fixVersion();
if($this->browser == 'Chrome'){
$this->rendering_engine_name = $this->version >= 28 ? 'Blink' : 'WebKit';
}
if($this->browser == 'Safari' && $this->rendering_engine_name == 'WebKit' && $this->version >= 28){
$this->rendering_engine_name = 'Blink';
}
if($this->is_mobile_device && $this->browser == 'Safari'){
$this->browser = 'Mobile Safari';
}
if($this->is_mobile_device && $this->browser == 'Android'){
$this->browser = 'Android Browser';
}
}
if($this->platform == 'unknown' || $this->platform === null){
if(stripos($this->useragent, 'Ubuntu') !== false){
$this->platform = 'Ubuntu';
}
elseif(stripos($this->useragent, 'linux') !== false){
$this->platform = 'GNU/Linux';
}
elseif(stripos($this->useragent, 'windows nt 5.0') !== false){
$this->platform = 'Windows';
$this->platform_version = '2000'; // February 17, 2000
}
elseif(stripos($this->useragent, 'windows nt 5.1') !== false){
$this->platform = 'Windows';
$this->platform_version = 'XP'; // October 25, 2001
}
elseif(stripos($this->useragent, 'windows nt 5.2') !== false){
$this->platform = 'Windows';
$this->platform_version = 'XP'; // March 28, 2003
}
elseif(stripos($this->useragent, 'windows nt 6.0') !== false){
$this->platform = 'Windows';
$this->platform_version = 'Vista'; // January 30, 2007
}
elseif(stripos($this->useragent, 'windows nt 6.1') !== false){
$this->platform = 'Windows';
$this->platform_version = '7'; // October 22, 2009
}
elseif(stripos($this->useragent, 'windows nt 6.2') !== false){
$this->platform = 'Windows';
$this->platform_version = '8'; // October 26, 2012
}
elseif(stripos($this->useragent, 'windows nt 6.3') !== false){
$this->platform = 'Windows';
$this->platform_version = '8.1'; // October 18, 2013
}
elseif(stripos($this->useragent, 'windows nt 10') !== false){
$this->platform = 'Windows';
$this->platform_version = '10'; // July 29, 2015
}
elseif(stripos($this->useragent, 'windows phone 8.0') !== false){
$this->platform = 'Windows Phone';
$this->platform_version = '8.0';
$this->is_mobile_device = true;
}
elseif(stripos($this->useragent, 'mozilla/5.0 (mobile;') !== false){
$this->platform = 'Firefox OS';
$this->is_mobile_device = true;
}
}
if($this->rendering_engine_name == 'unknown' || $this->rendering_engine_name == null){
if(stripos($this->useragent, 'gecko/') !== false){
$this->rendering_engine_name = 'Gecko';
$this->rendering_engine_version = preg_replace('#.*Gecko/([0-9\.]+).*#i', '$1', $this->useragent);
}
elseif(stripos($this->useragent, 'AppleWebKit/') !== false){
$this->rendering_engine_name = 'WebKit';
$this->rendering_engine_version = preg_replace('#.*AppleWebKit/([0-9\.]+).*#i', '$1', $this->useragent);
}
elseif(stripos($this->useragent, 'trident/') !== false){
$this->rendering_engine_name = 'Trident';
$this->rendering_engine_version = preg_replace('#.*trident/([0-9\.]+).*#i', '$1', $this->useragent);
}
elseif(strpos($this->useragent, 'MSIE') !== false){
$this->rendering_engine_name = 'Trident';
}
elseif(stripos($this->useragent, 'libwww-fm/') !== false){
$this->rendering_engine_name = 'libwww-FM';
$this->rendering_engine_version = preg_replace('#.*libwww-fm/([0-9\.]+).*#i', '$1', $this->useragent);
}
}
if($this->platform_bits === null){
if($this->platform == 'Windows'){
if(strpos($this->useragent, 'WOW64') !== false){
$this->platform_bits = '64';
$this->platform_architecture = 'x86';
}
else{
$this->platform_bits = '32';
$this->platform_architecture = 'x86';
}
}
elseif($this->platform == 'GNU/Linux' || $this->platform == 'Ubuntu'){
if(strpos($this->useragent, 'x86_64') !== false){
$this->platform_bits = '64';
$this->platform_architecture = 'x86';
}
elseif(strpos($this->useragent, 'x86') !== false){
$this->platform_bits = '32';
$this->platform_architecture = 'x86';
}
}
elseif($this->platform == 'MacOSX'){
if(strpos($this->useragent, 'Intel Mac') !== false){
$this->platform_architecture = 'x86';
}
}
}
if($this->platform === null){
$this->platform = 'unknown';
}
}
public function isBot(){
return $this->crawler;
}
public function isMobile(){
return $this->is_mobile_device;
}
public function asArray(){
$ret = [];
$ret['useragent'] = $this->useragent;
foreach(self::$Map as $k => $v){
$ret[$v] = $this->$v;
}
return $ret;
}
public function getPseudoIdentifier($as_array = false){
$a = [];
$a[] = 'ua-browser-' . $this->browser;
$a[] = 'ua-engine-' . $this->rendering_engine_name;
$a[] = 'ua-browser-version-' . $this->major_ver;
$a[] = 'ua-platform-' . $this->platform;
if($this->isMobile()) $a[] = 'ua-is-mobile';
if($as_array){
return $a;
}
else{
return strtolower(implode(';', $a));
}
}
private function _fixVersion(){
if($this->version == 0.0){
if(preg_match('#' . $this->browser . '/[0-9\.]+#', $this->useragent) !== 0){
$this->version = preg_replace('#.*' . $this->browser . '/([0-9]+)\.([0-9]+).*#', '$1.$2', $this->useragent);
}
elseif(strpos($this->useragent, ' Version/') !== false){
$this->version = preg_replace('#.* Version/([0-9\.]+).*#i', '$1', $this->useragent);
}
}
if($this->major_ver == 0){
$this->major_ver = substr($this->version, 0, strpos($this->version, '.'));
$this->minor_ver = substr($this->version, strpos($this->version, '.')+1);
if(strpos($this->minor_ver, '.') !== false){
$this->minor_ver = substr($this->minor_ver, 0, strpos($this->minor_ver, '.'));
}
}
}
private static function _LoadData() {
$cachekey = 'useragent-browsecap-data';
$cachetime = SECONDS_ONE_WEEK;
$cache = Cache::Get($cachekey, $cachetime);
if($cache === false){
$file   = \Core\Filestore\Factory::File('tmp/php_browscap.ini');
$remote = \Core\Filestore\Factory::File(self::$_ini_url);
$rcontents = $remote->getContentsObject();
if($rcontents instanceof ContentGZ){
$rcontents->uncompress($file);
}
else {
if(!$file->exists()){
$remote->copyTo($file);
}
if($file->getMTime() < (\Time::GetCurrent() - self::$updateInterval)){
$remote->copyTo($file);
}
}
$_browsers = parse_ini_file($file->getFilename(), true, INI_SCANNER_RAW);
$patterns = [];
$browsers = [];
array_shift($_browsers);
$properties = array_keys($_browsers['DefaultProperties']);
array_unshift(
$properties,
'browser_name',
'browser_name_regex',
'browser_name_pattern',
'Parent'
);
$uas = array_keys($_browsers);
usort(
$uas,
create_function(self::ORDER_FUNC_ARGS, self::ORDER_FUNC_LOGIC)
);
$user_agents_keys = array_flip($uas);
$properties_keys = array_flip($properties);
$search = ['\*', '\?'];
$replace = ['.*', '.'];
foreach ($uas as $user_agent) {
$browser = [];
$pattern = preg_quote($user_agent, self::REGEX_DELIMITER);
$patterns[] = self::REGEX_DELIMITER
. '^'
. str_replace($search, $replace, $pattern)
. '$'
. self::REGEX_DELIMITER;
if (!empty($_browsers[$user_agent]['Parent'])) {
$parent = $_browsers[$user_agent]['Parent'];
$_browsers[$user_agent]['Parent'] = $user_agents_keys[$parent];
}
foreach ($_browsers[$user_agent] as $key => $value) {
$key = $properties_keys[$key];
$browser[$key] = $value;
}
$browsers[] = $browser;
unset($browser);
}
unset($user_agents_keys, $properties_keys, $_browsers);
Cache::Set(
$cachekey,
[
'browsers'   => $browsers,
'useragents' => $uas,
'patterns'   => $patterns,
'properties' => $properties,
],
$cachetime
);
}
return Cache::Get($cachekey, $cachetime);
}
public static function Construct($useragent = null){
if($useragent === null) $useragent = $_SERVER['HTTP_USER_AGENT'];
$cachekey = 'useragent-constructor-' . md5($useragent);
$cache = Cache::Get($cachekey);
if(!$cache){
$cache = new UserAgent($useragent);
Cache::Set($cachekey, $cache, SECONDS_ONE_WEEK);
}
return $cache;
}
}
} // ENDING NAMESPACE Core

namespace  {

### REQUIRE_ONCE FROM core/libs/core/View.class.php
class View {
const ERROR_OTHER                       = 1;
const ERROR_NOERROR                     = 200;  // Request OK
const ERROR_BADREQUEST                  = 400;  // Section 10.4.1: Bad Request
const ERROR_UNAUTHORIZED                = 401;  // Section 10.4.2: Unauthorized
const ERROR_PAYMENTREQUIRED             = 402;  // Section 10.4.3: Payment Required
const ERROR_ACCESSDENIED                = 403;  // Section 10.4.4: Forbidden
const ERROR_NOTFOUND                    = 404;  // Section 10.4.5: Not Found
const ERROR_METHODNOTALLOWED            = 405;  // Section 10.4.6: Method Not Allowed
const ERROR_NOTACCEPTABLE               = 406;  // Section 10.4.7: Not Acceptable
const ERROR_PROXYAUTHENTICATIONREQUIRED = 407;  // Section 10.4.8: Proxy Authentication Required
const ERROR_REQUESTTIMEOUT              = 408;  // Section 10.4.9: Request Time-out
const ERROR_CONFLICT                    = 409;  // Section 10.4.10: Conflict
const ERROR_GONE                        = 410;  // Section 10.4.11: Gone
const ERROR_LENGTHREQUIRED              = 411;  // Section 10.4.12: Length Required
const ERROR_PRECONDITIONFAILED          = 412;  // Section 10.4.13: Precondition Failed
const ERROR_ENTITYTOOLARGE              = 413;  // Section 10.4.14: Request Entity Too Large
const ERROR_URITOOLARGE                 = 414;  // Section 10.4.15: Request-URI Too Large
const ERROR_UNSUPPORTEDMEDIATYPE        = 415;  // Section 10.4.16: Unsupported Media Type
const ERROR_RANGENOTSATISFIABLE         = 416;  // Section 10.4.17: Requested range not satisfiable
const ERROR_EXPECTATIONFAILED           = 417;  // Section 10.4.18: Expectation Failed
const ERROR_SERVERERROR                 = 500;  // Generic server error
const MODE_PAGE = 'page';
const MODE_WIDGET = 'widget';
const MODE_NOOUTPUT = 'nooutput';
const MODE_AJAX = 'ajax';
const MODE_PAGEORAJAX = 'pageorajax';
const MODE_EMAILORPRINT = 'print';
const METHOD_GET = 'GET';
const METHOD_POST = 'POST';
const METHOD_PUT = 'PUT';
const METHOD_HEAD = 'HEAD';
const METHOD_DELETE = 'DELETE';
const CTYPE_HTML  = 'text/html';
const CTYPE_PLAIN = 'text/plain';
const CTYPE_JSON  = 'application/json';
const CTYPE_XML   = 'application/xml';
const CTYPE_ICS   = 'text/calendar';
const CTYPE_RSS   = 'application/rss+xml';
public $error;
private $_template;
private $_params;
public $baseurl;
public $title;
public $access;
public $templatename;
public $contenttype = View::CTYPE_HTML;
public $mastertemplate;
public $breadcrumbs = array();
public $controls;
public $mode;
public $jsondata = null;
public $updated = null;
public $head = array();
public $meta = array();
public $scripts = array('head' => array(), 'foot' => array());
public $stylesheets = array();
public $canonicalurl = null;
public $allowerrors = false;
public $ssl = false;
public $record = true;
private $_bodyCache = null;
private $_fetchCache = null;
public $bodyclasses = [];
public $htmlAttributes = [];
public $headers = [];
protected $cacheable = true;
public $parent = null;
public function __construct() {
$this->error = View::ERROR_NOERROR;
$this->mode  = View::MODE_PAGE;
$this->controls = new ViewControls();
$this->meta = new ViewMetas();
}
public function setParameters($params) {
$this->_params = $params;
}
public function getParameters() {
if (!$this->_params) {
$this->_params = array();
}
return $this->_params;
}
public function getParameter($key) {
$p = $this->getParameters();
return (array_key_exists($key, $p)) ? $p[$key] : null;
}
public function getTemplate() {
if (!$this->_template) {
$this->_template = \Core\Templates\Template::Factory($this->templatename);
$this->_template->setView($this);
}
return $this->_template;
}
public function getTitle(){
if(strpos($this->title, 't:') === 0){
return t(substr($this->title, 2));
}
else{
return $this->title;
}
}
public function overrideTemplate($template){
if(!is_a($template, 'TemplateInterface')){
return false;
}
if($template == $this->_template){
return false;
}
if($this->_template !== null){
foreach($this->_template->getTemplateVars() as $k => $v){
$template->assign($k, $v);
}
}
$this->_template = $template;
return true;
}
public function assign($key, $val) {
$this->getTemplate()->assign($key, $val);
}
public function assignVariable($key, $val) {
$this->assign($key, $val);
}
public function getVariable($key) {
$v = $this->getTemplate()->getVariable($key);
return ($v) ? $v->value : null;
}
public function fetchBody() {
if ($this->mode == View::MODE_NOOUTPUT) {
return null;
}
if($this->_bodyCache !== null){
return $this->_bodyCache;
}
if ($this->error != View::ERROR_NOERROR && !$this->allowerrors) {
$tmpl = '/pages/error/error' . $this->error . '.tpl';
}
else {
$tmpl = $this->templatename;
}
switch ($this->contenttype) {
case View::CTYPE_XML:
if (strpos($tmpl, ROOT_PDIR) === 0 && strpos($tmpl, '.xml.tpl') !== false) {
$this->mastertemplate = false;
}
else {
$ctemp = Core\Templates\Template::ResolveFile(preg_replace('/tpl$/i', 'xml.tpl', $tmpl));
if ($ctemp) {
$tmpl                 = $ctemp;
$this->mastertemplate = false;
}
else {
$this->contenttype = View::CTYPE_HTML;
}
}
break;
case View::CTYPE_ICS:
if(strpos($tmpl, ROOT_PDIR) === 0 && strpos($tmpl, '.ics.tpl') !== false){
$this->mastertemplate = false;
}
else{
$ctemp = Core\Templates\Template::ResolveFile(preg_replace('/tpl$/i', 'ics.tpl', $tmpl));
if($ctemp){
$tmpl = $ctemp;
$this->mastertemplate = false;
}
else{
$this->contenttype = View::CTYPE_HTML;
}
}
break;
case View::CTYPE_JSON:
if ($this->jsondata !== null) {
$this->mastertemplate = false;
$tmpl                 = false;
return json_encode($this->jsondata);
}
$ctemp = Core\Templates\Template::ResolveFile(preg_replace('/tpl$/i', 'json.tpl', $tmpl));
if ($ctemp) {
$tmpl                 = $ctemp;
$this->mastertemplate = false;
}
else {
$this->contenttype = View::CTYPE_HTML;
}
break;
}
if (!$tmpl && $this->templatename == '') {
throw new Exception('Please set the variable "templatename" on the page view.');
}
switch ($this->mode) {
case View::MODE_PAGE:
case View::MODE_AJAX:
case View::MODE_PAGEORAJAX:
case View::MODE_EMAILORPRINT:
$t = $this->getTemplate();
$html = $t->fetch($tmpl);
if($this->parent){
$this->parent->_syncFromView($this);
}
break;
case View::MODE_WIDGET:
$tn = Core\Templates\Template::ResolveFile(preg_replace(':^[/]{0,1}pages/:', '/widgets/', $tmpl));
if (!$tn) $tn = $tmpl;
$t = $this->getTemplate();
$html = $t->fetch($tn);
if($this->parent){
$this->parent->_syncFromView($this);
}
break;
}
$this->_bodyCache = $html;
return $html;
}
public function fetch() {
if($this->_fetchCache !== null){
return $this->_fetchCache;
}
try{
$body = $this->fetchBody();
}
catch(Exception $e){
$this->error = View::ERROR_SERVERERROR;
\Core\ErrorManagement\exception_handler($e, ($this->mode == View::MODE_PAGE));
$body = '';
}
if ($this->mastertemplate === false) {
return $body;
}
elseif ($this->mastertemplate === null) {
$this->mastertemplate = ConfigHandler::Get('/theme/default_template');
}
if ($this->contenttype == View::CTYPE_JSON) {
$mastertpl = false;
}
else {
switch ($this->mode) {
case View::MODE_PAGEORAJAX:
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
$mastertpl = false;
$this->mode = View::MODE_AJAX;
}
else{
$mastertpl = ROOT_PDIR . 'themes/' . ConfigHandler::Get('/theme/selected') . '/skins/' . $this->mastertemplate;
$this->mode = View::MODE_PAGE;
}
break;
case View::MODE_NOOUTPUT:
case View::MODE_AJAX:
$mastertpl = false;
break;
case View::MODE_PAGE:
case View::MODE_EMAILORPRINT:
$mastertpl = Core\Templates\Template::ResolveFile('skins/' . $this->mastertemplate);
break;
case View::MODE_WIDGET:
$mastertpl = Core\Templates\Template::ResolveFile('widgetcontainers/' . $this->mastertemplate);
break;
}
}
if (!$mastertpl) return $body;
$template = \Core\Templates\Template::Factory($mastertpl);
$template->setView($this);
if ($this->mode == View::MODE_PAGE) {
$template->assign('breadcrumbs', $this->getBreadcrumbs());
$template->assign('controls', $this->controls);
$template->assign('messages', Core::GetMessages());
}
if(isset($this->meta['title']) && $this->meta['title']){
$template->assign('seotitle', $this->meta['title']);
}
else{
$template->assign('seotitle', $this->getTitle());
}
$template->assign('title', $this->getTitle());
$template->assign('body', $body);
$ua = \Core\UserAgent::Construct();
$this->bodyclasses = array_merge($this->bodyclasses, $ua->getPseudoIdentifier(true));
switch ($this->error) {
case View::ERROR_BADREQUEST:
case View::ERROR_PAYMENTREQUIRED:
case View::ERROR_ACCESSDENIED:
case View::ERROR_NOTFOUND:
case View::ERROR_METHODNOTALLOWED:
case View::ERROR_NOTACCEPTABLE:
case View::ERROR_PROXYAUTHENTICATIONREQUIRED:
case View::ERROR_REQUESTTIMEOUT:
case View::ERROR_CONFLICT:
case View::ERROR_GONE:
case View::ERROR_LENGTHREQUIRED:
case View::ERROR_PRECONDITIONFAILED:
case View::ERROR_ENTITYTOOLARGE:
case View::ERROR_URITOOLARGE:
case View::ERROR_UNSUPPORTEDMEDIATYPE:
case View::ERROR_RANGENOTSATISFIABLE:
case View::ERROR_EXPECTATIONFAILED:
case View::ERROR_UNAUTHORIZED:
$url = 'error-' . $this->error;
break;
case 403:
$url = "error-403 page-user-login";
break;
default:
$url  = strtolower(trim(preg_replace('/[^a-z0-9\-]*/i', '', str_replace('/', '-', $this->baseurl)), '-'));
}
while($url != ''){
$this->bodyclasses[] = 'page-' . $url;
$url = substr($url, 0, strrpos($url, '-'));
}
$bodyclasses = strtolower(implode(' ', $this->bodyclasses));
$template->assign('body_classes', $bodyclasses);
try{
$data = $template->fetch();
}
catch(SmartyException $e){
$this->error = View::ERROR_SERVERERROR;
error_log('[view error]');
error_log('Template name: [' . $mastertpl . ']');
\Core\ErrorManagement\exception_handler($e);
require(ROOT_PDIR . 'core/templates/halt_pages/fatal_error.inc.html');
die();
}
catch(TemplateException $e){
$this->error = View::ERROR_SERVERERROR;
error_log('[view error]');
error_log('Template name: [' . $mastertpl . ']');
\Core\ErrorManagement\exception_handler($e);
require(ROOT_PDIR . 'core/templates/halt_pages/fatal_error.inc.html');
die();
}
if($this->mode == View::MODE_EMAILORPRINT && $this->contenttype == View::CTYPE_HTML){
HookHandler::DispatchHook('/core/page/rendering', $this);
if(preg_match('#</head>#i', $data)){
$data = preg_replace('#</head>#i', $this->getHeadContent() . "\n" . '</head>', $data, 1);
}
}
elseif ($this->mode == View::MODE_PAGE && $this->contenttype == View::CTYPE_HTML) {
HookHandler::DispatchHook('/core/page/rendering', $this);
if(preg_match('#</head>#i', $data)){
$data = preg_replace('#</head>#i', $this->getHeadContent() . "\n" . '</head>', $data, 1);
}
if(preg_match('#</body>#i', $data)){
$match = strrpos($data, '</body>');
$foot = $this->getFootContent();
if(ENABLE_XHPROF){
require_once('xhprof_lib/utils/xhprof_lib.php'); #SKIPCOMPILER
require_once('xhprof_lib/utils/xhprof_runs.php'); #SKIPCOMPILER
$xhprof_data = xhprof_disable();
$namespace = trim(str_replace(['.', '/'], '-', HOST . REL_REQUEST_PATH), '-');
$xhprof_runs = new XHProfRuns_Default();
$run_id = $xhprof_runs->save_run($xhprof_data, $namespace);
define('XHPROF_RUN', $run_id);
define('XHPROF_SOURCE', $namespace);
$xhprof_link = sprintf(
'<a href="' . SERVERNAME . '/xhprof/index.php?run=%s&source=%s" target="_blank">View XHprof Profiler Report</a>' . "\n",
$run_id,
$namespace
);
}
else{
$xhprof_link = '';
}
if (DEVELOPMENT_MODE) {
$legend = '<div class="fieldset-title">%s<i class="icon-chevron-down expandable-hint"></i><i class="icon-chevron-up collapsible-hint"></i></div>' . "\n";
$debug = '';
$debug .= '<pre class="xdebug-var-dump screen">';
$debug .= '<fieldset class="debug-section collapsible" id="debug-section-template-information">';
$debug .= sprintf($legend, 'Template Information');
$debug .= "<span>";
$debug .= 'Base URL: ' . $this->baseurl . "\n";
$debug .= 'Template Requested: ' . $this->templatename . "\n";
$debug .= 'Template Actually Used: ' . \Core\Templates\Template::ResolveFile($this->templatename) . "\n";
$debug .= 'Master Skin: ' . $this->mastertemplate . "\n";
$debug .= "</span>";
$debug .= '</fieldset>';
$debug .= '<fieldset class="debug-section collapsible" id="debug-section-performance-information">';
$debug .= sprintf($legend, 'Performance Information');
$debug .= "<span>";
$debug .= $xhprof_link;
$debug .= "Database Reads: " . \Core\Utilities\Profiler\DatamodelProfiler::GetDefaultProfiler()->readCount() . "\n";
$debug .= "Database Writes: " . \Core\Utilities\Profiler\DatamodelProfiler::GetDefaultProfiler()->writeCount() . "\n";
$debug .= "Amount of memory used by PHP: " . \Core\Filestore\format_size(memory_get_peak_usage(true)) . "\n";
$profiler = Core\Utilities\Profiler\Profiler::GetDefaultProfiler();
$debug .= "Total processing time: " . $profiler->getTimeFormatted() . "\n";
$debug .= "</span>";
$debug .= '</fieldset>';
$debug .= '<fieldset class="debug-section collapsible" id="debug-section-profiler-information">';
$debug .= sprintf($legend, 'Core Profiler');
$debug .= "<span>";
$debug .= $profiler->getEventTimesFormatted();
$debug .= "</span>";
$debug .= '</fieldset>';
$debug .= '<fieldset class="debug-section collapsible collapsed" id="debug-section-components-information">';
$debug .= sprintf($legend, 'Available Components');
$debugcomponents = array_merge(Core::GetComponents(), Core::GetDisabledComponents());
$debug .= "<span>";
ksort($debugcomponents);
foreach ($debugcomponents as $l => $v) {
if($v->isEnabled() && $v->isReady()){
$debug .= '[<span style="color:green;">Enabled</span>]';
}
elseif($v->isEnabled() && !$v->isReady()){
$debug .= '[<span style="color:red;">!ERROR!</span>]';
}
else{
$debug .= '[<span style="color:red;">Disabled</span>]';
}
$debug .= $v->getName() . ' ' . $v->getVersion() . "<br/>";
}
$debug .= "</span>";
$debug .= '</fieldset>';
$debug .= '<fieldset class="debug-section collapsible collapsed" id="debug-section-hooks-information">';
$debug .= sprintf($legend, 'Registered Hooks');
foreach(HookHandler::GetAllHooks() as $hook){
$debug .= "<span>";
$debug .= $hook->name;
if($hook->description) $debug .= ' <em> - ' . $hook->description . '</em>';
$debug .= "\n" . '<span style="color:#999;">Return expected: ' . $hook->returnType . '</span>';
$debug .= "\n" . '<span style="color:#999;">Attached by ' . $hook->getBindingCount() . ' binding(s).</span>';
foreach($hook->getBindings() as $b){
$debug .= "\n" . ' * ' . $b['call'];
}
$debug .= "\n\n";
$debug .= "</span>";
}
$debug .= '</fieldset>';
$debug .= '<fieldset class="debug-section collapsible collapsed" id="debug-section-includes-information">';
$debug .= sprintf($legend, 'Included Files');
$debug .= '<span>Number: ' . sizeof(get_included_files()) . "</span>";
$debug .= '<span>'. implode("<br/>", get_included_files()) . "</span>";
$debug .= '</fieldset>';
$debug .= '<fieldset class="debug-section collapsible collapsed" id="debug-section-query-information">';
$debug .= sprintf($legend, 'Query Log');
$profiler = \Core\Utilities\Profiler\DatamodelProfiler::GetDefaultProfiler();
$debug .= $profiler->getEventTimesFormatted();
$debug .= '</fieldset>';
$debug .= '</pre>';
$foot .= "\n" . $debug;
}
$data = substr_replace($data, $foot . "\n" . '</body>', $match, 7);
}
$data = preg_replace('#<html#', '<html ' . $this->getHTMLAttributes(), $data, 1);
}
$this->_fetchCache = $data;
return $data;
}
public function render() {
if ($this->contenttype && $this->contenttype == View::CTYPE_HTML) {
View::AddMeta('http-equiv="Content-Type" content="text/html;charset=UTF-8"');
}
$data = $this->fetch();
if (
!headers_sent() &&
($this->mode == View::MODE_PAGE || $this->mode == View::MODE_PAGEORAJAX || $this->mode == View::MODE_AJAX || $this->mode == View::MODE_NOOUTPUT || $this->mode == View::MODE_EMAILORPRINT)
) {
switch ($this->error) {
case View::ERROR_NOERROR:
header('Status: 200 OK', true, $this->error);
break;
case View::ERROR_BADREQUEST:
header('Status: 400 Bad Request', true, $this->error);
break;
case View::ERROR_UNAUTHORIZED:
header('Status: 401 Unauthorized', true, $this->error);
break;
case View::ERROR_PAYMENTREQUIRED:
header('Status: 402 Payment Required', true, $this->error);
break;
case View::ERROR_ACCESSDENIED:
header('Status: 403 Forbidden', true, $this->error);
break;
case View::ERROR_NOTFOUND:
header('Status: 404 Not Found', true, $this->error);
break;
case View::ERROR_METHODNOTALLOWED:
header('Status: 405 Method Not Allowed', true, $this->error);
break;
case View::ERROR_NOTACCEPTABLE:
header('Status: 406 Not Acceptable', true, $this->error);
break;
case View::ERROR_PROXYAUTHENTICATIONREQUIRED:
header('Status: 407 Proxy Authentication Required', true, $this->error);
break;
case View::ERROR_REQUESTTIMEOUT:
header('Status: 408 Request Time-out', true, $this->error);
break;
case View::ERROR_CONFLICT:
header('Status: 409 Conflict', true, $this->error);
break;
case View::ERROR_GONE:
header('Status: 410 Gone', true, $this->error);
break;
case View::ERROR_LENGTHREQUIRED:
header('Status: 411 Length Required', true, $this->error);
break;
case View::ERROR_PRECONDITIONFAILED:
header('Status: 412 Precondition Failed', true, $this->error);
break;
case View::ERROR_ENTITYTOOLARGE:
header('Status: 413 Request Entity Too Large', true, $this->error);
break;
case View::ERROR_URITOOLARGE:
header('Status: 414 Request-URI Too Large', true, $this->error);
break;
case View::ERROR_UNSUPPORTEDMEDIATYPE:
header('Status: 415 Unsupported Media Type', true, $this->error);
break;
case View::ERROR_RANGENOTSATISFIABLE:
header('Status: 416 Requested range not satisfiable', true, $this->error);
break;
case View::ERROR_EXPECTATIONFAILED:
header('Status: 417 Expectation Failed', true, $this->error);
break;
case View::ERROR_SERVERERROR:
header('Status: 500 Internal Server Error', true, $this->error);
break;
default:
header('Status: 500 Internal Server Error', true, $this->error);
break; // I don't know WTF happened...
}
if ($this->contenttype) {
if ($this->contenttype == View::CTYPE_HTML) header('Content-Type: text/html; charset=UTF-8');
else header('Content-Type: ' . $this->contenttype);
}
header('X-Content-Encoded-By: Core Plus' . (DEVELOPMENT_MODE ? ' ' . Core::GetComponent()->getVersion() : ''));
if(\ConfigHandler::Get('/core/security/x-frame-options')){
header('X-Frame-Options: ' . \ConfigHandler::Get('/core/security/x-frame-options'));
}
if(\ConfigHandler::Get('/core/security/csp-frame-ancestors')){
header('Content-Security-Policy: frame-ancestors \'self\' ' . \ConfigHandler::Get('/core/security/content-security-policy'));
}
if($this->updated !== null){
header('Last-Modified: ' . Time::FormatGMT($this->updated, Time::TIMEZONE_USER, Time::FORMAT_RFC2822));
}
foreach($this->headers as $k => $v){
header($k . ': ' . $v);
}
}
if(SSL_MODE != SSL_MODE_DISABLED){
if($this->ssl && !SSL){
header('Location: ' . ROOT_URL_SSL . substr(REL_REQUEST_PATH, 1));
die('This page requires SSL, if it does not redirect you automatically, please <a href="' . ROOT_URL_SSL . substr(REL_REQUEST_PATH, 1) . '">Click Here</a>.');
}
elseif(!$this->ssl && SSL && SSL_MODE == SSL_MODE_ONDEMAND){
header('Location: ' . ROOT_URL_NOSSL . substr(REL_REQUEST_PATH, 1));
die('This page does not require SSL, if it does not redirect you automatically, please <a href="' . ROOT_URL_NOSSL . substr(REL_REQUEST_PATH, 1) . '">Click Here</a>.');
}
}
echo $data;
}
public function addBreadcrumb($title, $link = null) {
if ($link !== null && strpos($link, '://') === false){
$link = \Core\resolve_link($link);
}
if(strpos($title, 't:') === 0){
$title = t(substr($title, 2));
}
$this->breadcrumbs[] = array(
'title' => $title,
'link'  => $link
);
}
public function setBreadcrumbs($array) {
$this->breadcrumbs = array();
if (!$array) return;
foreach ($array as $k => $v) {
if ($v instanceof PageModel) $this->addBreadcrumb($v->get('title'), $v->getResolvedURL());
else $this->addBreadcrumb($v, $k);
}
}
public function getBreadcrumbs() {
$crumbs = $this->breadcrumbs;
if ($this->title){
$crumbs[] = [
'title' => $this->getTitle(),
'link'  => null
];
}
$seen = [];
foreach($crumbs as $k => $dat){
if(in_array($dat['link'], $seen)){
unset($crumbs[$k]);
}
else{
$seen[] = $dat['link'];
}
}
return $crumbs;
}
public function addControl($title, $link = null, $class = 'edit') {
if($title instanceof Model){
$this->controls = ViewControls::DispatchModel($title);
return;
}
$control = new ViewControl();
if(func_num_args() == 1 && is_array($title)){
foreach($title as $k => $v){
$control->set($k, $v);
}
}
else{
if(is_array($class)){
foreach($class as $k => $v){
$control->set($k, $v);
}
}
else{
$control->class = $class;
}
$control->title = $title;
$control->link = \Core\resolve_link($link);
}
if($control->link != \Core\resolve_link($this->baseurl)){
$this->controls[] = $control;
}
}
public function addControls($controls){
if($controls instanceof Model){
$this->controls = ViewControls::DispatchModel($controls);
return;
}
foreach($controls as $c){
$this->addControl($c);
}
}
public function setAccess($accessstring) {
$this->access = $accessstring;
return $this->checkAccess();
}
public function checkAccess() {
$u = \Core\user();
if ($u->checkAccess($this->access)) {
return true;
}
else {
$this->error = View::ERROR_ACCESSDENIED;
return false;
}
}
public function isCacheable(){
return $this->cacheable;
}
public function getHeadContent(){
$minified = ConfigHandler::Get('/core/markup/minified');
if($minified){
$data = array_merge($this->stylesheets, $this->head, $this->scripts['head']);
}
else{
$data = array_merge(
['<!-- BEGIN STYLESHEET INSERTIONS -->'],
$this->stylesheets,
['<!-- END STYLESHEET INSERTIONS -->'],
['<!-- BEGIN HEAD CONTENT INSERTIONS -->'],
$this->head,
['<!-- END HEAD CONTENT INSERTIONS -->'],
['<!-- BEGIN JAVASCRIPT INSERTIONS -->'],
$this->scripts['head'],
['<!-- END JAVASCRIPT INSERTIONS -->']
);
}
if($this->error == View::ERROR_NOERROR){
if($this->updated !== null){
$this->meta['article:modified_time'] = Time::FormatGMT($this->updated, Time::TIMEZONE_GMT, Time::FORMAT_ISO8601);
}
$this->meta['generator'] = true;
if(!isset($this->meta['og:title'])){
$this->meta['og:title'] = $this->title;
}
if($this->canonicalurl === null){
$this->canonicalurl = \Core\resolve_link($this->baseurl);
}
if($this->canonicalurl !== false){
$this->meta['canonical'] = $this->canonicalurl;
}
$this->meta['og:site_name'] = SITENAME;
}
$data = array_merge($data, $this->meta->fetch());
if ($minified) {
$out = implode('', $data);
}
else {
$out = '<!-- BEGIN Automatic HEAD generation -->' . "\n\n" . implode("\n", $data) . "\n\n" . '<!-- END Automatic HEAD generation -->';
}
return trim($out);
}
public function getFootContent(){
$minified = ConfigHandler::Get('/core/markup/minified');
if($minified){
$data = $this->scripts['foot'];
}
else{
$data = array_merge(
['<!-- BEGIN JAVASCRIPT INSERTIONS -->'],
$this->scripts['foot'],
['<!-- END JAVASCRIPT INSERTIONS -->']
);
}
if ($minified) {
$out = implode('', $data);
}
else {
$out = implode("\n", $data);
}
return trim($out);
}
public function addScript($script, $location = 'head') {
if (strpos($script, '<script') === false) {
$script = '<script type="text/javascript" src="' . \Core\resolve_asset($script) . '"></script>';
}
if(isset($this)){
$scripts =& $this->scripts;
}
else{
$scripts =& \Core\view()->scripts;
}
if (in_array($script, $scripts['head'])) return;
if (in_array($script, $scripts['foot'])) return;
if ($location == 'head'){
$scripts['head'][] = $script;
}
else{
$scripts['foot'][] = $script;
}
}
public function appendBodyContent($content){
if(isset($this)){
$scripts =& $this->scripts;
}
else{
$scripts =& \Core\view()->scripts;
}
if (in_array($content, $scripts['foot'])) return;
$scripts['foot'][] = $content;
}
public function addStylesheet($link, $media = "all") {
if (strpos($link, '<style') === 0) {
$this->addStyle($link);
return;
}
if (strpos($link, '<link') === false) {
if(strripos($link, '.less') == strlen($link)-5 ){
$rel = 'stylesheet/less';
Core::_AttachLessJS();
}
else{
$rel = 'stylesheet';
}
$link = '<link type="text/css" href="' . \Core\resolve_asset($link) . '" media="' . $media . '" rel="' . $rel . '"/>';
}
if(isset($this)){
$styles =& $this->stylesheets;
}
else{
$styles =& \Core\view()->stylesheets;
}
if (!in_array($link, $styles)) $styles[] = $link;
}
public function addStyle($style) {
if(strpos($style, '<link ') === 0){
$this->addStylesheet($style);
return;
}
if (strpos($style, '<style') === false) {
$style = '<style>' . $style . '</style>';
}
if(strpos($style, 'rel="stylesheet/less"') !== false){
Core::_AttachLessJS();
}
if(strpos($style, "rel='stylesheet/less'") !== false){
Core::_AttachLessJS();
}
if(isset($this)){
$styles =& $this->stylesheets;
}
else{
$styles =& \Core\view()->stylesheets;
}
if (!in_array($style, $styles)) $styles[] = $style;
}
public function setHTMLAttribute($attribute, $value) {
$this->htmlAttributes[$attribute] = $value;
}
public function getHTMLAttributes($asarray = false) {
$atts = $this->htmlAttributes;
if ($asarray) {
return $atts;
}
else {
$str = '';
foreach ($atts as $k => $v) $str .= " $k=\"" . str_replace('"', '&quot;', $v) . "\"";
return trim($str);
}
}
public function addMetaName($key, $value) {
if(isset($this)){
$this->meta[$key] = $value;
}
else{
\Core\view()->meta[$key] = $value;
}
}
public function addMeta($string) {
if (strpos($string, '<meta') === false) $string = '<meta ' . $string . '/>';
if(isset($this)){
$this->head[] = $string;
}
else{
\Core\view()->head[] = $string;
}
}
public function addHead($string){
if(isset($this)){
$this->head[] = $string;
}
else{
\Core\view()->head[] = $string;
}
}
public function addHeader($key, $value){
$this->headers[$key] = $value;
}
public function disableCache(){
$this->cacheable = false;
if($this->parent){
$this->parent->disableCache();
}
}
protected function _syncFromView(View $view){
if($view === $this){
return;
}
foreach($view->head as $h){
$this->addHead($h);
}
foreach($view->meta as $m){
$this->addMeta($m);
}
foreach($view->scripts['head'] as $s){
$this->addScript($s, 'head');
}
foreach($view->scripts['foot'] as $s){
$this->addScript($s, 'foot');
}
foreach($view->stylesheets as $s){
$this->addStyle($s);
}
if($view->ssl){
$this->ssl = true;
}
$this->bodyclasses += $view->bodyclasses;
$this->htmlAttributes += $view->htmlAttributes;
}
public static function GetHead() {
trigger_error('View::GetHead is deprecated, please use \Core\view()->getHeadContent instead!', E_USER_DEPRECATED);
return \Core\view()->getHeadContent();
}
public static function GetFoot() {
trigger_error('View::GetFoot is deprecated, please use \Core\view()->getFootContent instead!', E_USER_DEPRECATED);
return \Core\view()->getFootContent();
}
}


### REQUIRE_ONCE FROM core/libs/core/Widget_2_1.class.php
class Widget_2_1 {
private $_view = null;
private $_request = null;
public $is_simple = false;
public $settings = [];
public $_instance = null;
public $_params = null;
public $_installable = null;
public function getView() {
if ($this->_view === null) {
$this->_view              = new View();
$this->_view->contenttype = View::CTYPE_HTML;
$this->_view->mode        = View::MODE_WIDGET;
if ($this->getWidgetInstanceModel()) {
$this->_view->baseurl = $this->getWidgetInstanceModel()->get('baseurl');
}
else {
$back = debug_backtrace();
if(isset($back[1]['class'])){
$cls  = $back[1]['class'];
if (strpos($cls, 'Widget') !== false) $cls = substr($cls, 0, -6);
$mth                  = $back[1]['function'];
$this->_view->baseurl = $cls . '/' . $mth;
}
}
}
return $this->_view;
}
public function getRequest(){
if($this->_request === null){
$this->_request = new WidgetRequest();
}
return $this->_request;
}
public function getWidgetInstanceModel() {
return $this->_instance;
}
public function getWidgetModel(){
$wi = $this->getWidgetInstanceModel();
return $wi ? $wi->getLink('Widget') : null;
}
public function getFormSettings(){
return [];
}
public function getPreviewImage(){
return '';
}
protected function setAccess($accessstring) {
$this->getWidgetInstanceModel()->set('access', $accessstring);
return (\Core\user()->checkAccess($accessstring));
}
protected function setTemplate($template) {
$this->getView()->templatename = $template;
}
protected function getParameter($param) {
if($this->_params !== null){
$parameters = $this->_params;
}
else{
$dat = $this->getWidgetInstanceModel()->splitParts();
$parameters = $dat['parameters'];
}
return (isset($parameters[$param])) ? $parameters[$param] : null;
}
protected function getSetting($key){
return $this->getWidgetModel()->getSetting($key);
}
public static function Factory($name) {
if(class_exists($name) && is_subclass_of($name, 'Widget_2_1')){
return new $name();
}
else{
return null;
}
}
public static function HookPageRender(){
$viewer = \Core\user()->checkAccess('p:/core/widgets/manage');
$manager = \Core\user()->checkAccess('p:/core/widgets/manage');
if(!($viewer || $manager)){
return true;
}
$request  = \Core\page_request();
$view     = \Core\view();
$page     = $request->getPageModel();
$template = \Core\Templates\Template::Factory($page->get('last_template'));
$areas    = $template->getWidgetAreas();
if(!sizeof($areas)){
return true;
}
$view->addControl('Page Widgets', '/admin/widgets?baseurl=' . $page->get('baseurl'), 'cog');
return true;
}
}
class WidgetRequest{
public $parameters = [];
public function getParameters() {
return $this->parameters;
}
public function getParameter($key) {
return (array_key_exists($key, $this->parameters)) ? $this->parameters[$key] : null;
}
}


### REQUIRE_ONCE FROM core/libs/core/Form.class.php
class FormGroup {
protected $_elements;
protected $_attributes;
protected $_validattributes = array();
public $requiresupload = false;
public $persistent = true;
public function __construct($atts = null) {
$this->_attributes = array();
$this->_elements   = array();
if ($atts) $this->setFromArray($atts);
}
public function set($key, $value) {
$this->_attributes[strtolower($key)] = $value;
}
public function get($key) {
$key = strtolower($key);
return (isset($this->_attributes[$key])) ? $this->_attributes[$key] : null;
}
public function setFromArray($array) {
foreach ($array as $k => $v) {
$this->set($k, $v);
}
}
public function hasError() {
foreach ($this->_elements as $e) {
if ($e->hasError()) return true;
}
return false;
}
public function getErrors() {
$err = array();
foreach ($this->_elements as $e) {
if ($e instanceof FormGroup) $err = array_merge($err, $e->getErrors());
elseif ($e->hasError()) $err[] = $e->getError();
}
return $err;
}
public function addElement($element, $atts = null) {
if ($element instanceof FormElement || is_a($element, 'FormElement')) {
if ($atts) $element->setFromArray($atts);
$this->_elements[] = $element;
}
elseif ($element instanceof FormGroup) {
if ($atts) $element->setFromArray($atts);
$this->_elements[] = $element;
}
else {
if (!isset(Form::$Mappings[$element])) $element = 'text'; // Default.
$this->_elements[] = new Form::$Mappings[$element]($atts);
}
}
public function addElementAfter($newelement, $currentelement){
if(is_string($currentelement)){
$currentelement = $this->getElement($currentelement);
if(!$currentelement){
return false;
}
}
foreach ($this->_elements as $k => $el) {
if($el == $currentelement){
array_splice($this->_elements, $k+1, 0, [$newelement]);
return true;
}
if ($el instanceof FormGroup) {
if ($el->addElementAfter($newelement, $currentelement)) return true;
}
}
return false;
}
public function switchElement(FormElement $oldelement, FormElement $newelement) {
foreach ($this->_elements as $k => $el) {
if ($el == $oldelement) {
$this->_elements[$k] = $newelement;
return true;
}
if ($el instanceof FormGroup) {
if ($el->switchElement($oldelement, $newelement)) return true;
}
}
return false;
}
public function removeElement($name){
foreach ($this->_elements as $k => $el) {
if($el->get('name') == $name){
unset($this->_elements[$k]);
return true;
}
if ($el instanceof FormGroup) {
if ($el->removeElement($name)) return true;
}
}
return false;
}
public function getTemplateName() {
return 'forms/groups/default.tpl';
}
public function render() {
$out = '';
foreach ($this->_elements as $e) {
$out .= $e->render();
}
$file = $this->getTemplateName();
if (!$file) return $out;
\Core\view()->disableCache();
$tpl = \Core\Templates\Template::Factory($file);
$tpl->assign('group', $this);
$tpl->assign('elements', $out);
return $tpl->fetch();
}
public function getClass() {
$classnames = [];
if($this->get('class')){
$classnames = explode(' ', $this->get('class'));
}
if($this->get('required')){
$classnames[] = 'formrequired';
}
if($this->hasError()){
$classnames[] = 'formerror';
}
if($this->get('orientation')){
$classnames[] = 'form-orientation-' . $this->get('orientation');
}
$classnames = array_unique($classnames);
sort($classnames);
return implode(' ', $classnames);
}
public function getID(){
if (!empty($this->_attributes['id'])){
return $this->_attributes['id'];
}
else{
$n = str_replace(['/', '[', ']'], '-', $this->get('name'));
$n = \Core\str_to_url($n);
$c = strtolower(get_class($this));
$id = $c . '-' . $n;
$id = str_replace('[]', '', $id);
$id = preg_replace('/\[([^\]]*)\]/', '-$1', $id);
return $id;
}
}
public function getGroupAttributes() {
$out = '';
foreach ($this->_validattributes as $k) {
if (($v = $this->get($k))) $out .= " $k=\"" . str_replace('"', '\\"', $v) . "\"";
}
return $out;
}
public function getElements($recursively = true, $includegroups = false) {
$els = array();
foreach ($this->_elements as $e) {
if (
$e instanceof FormElement ||
($e instanceof FormGroup && ($includegroups || !$recursively))
) {
$els[] = $e;
}
if ($recursively && $e instanceof FormGroup) $els = array_merge($els, $e->getElements($recursively));
}
return $els;
}
public function getElementsByName($nameRegex){
$ret = [];
$els = $this->getElements(true, true);
if(strpos($nameRegex, '#') === false){
$nameRegex = '#' . $nameRegex . '#';
}
else{
$nameRegex = '#' . str_replace('#', '\#', $nameRegex) . '#';
}
foreach ($els as $el) {
if(preg_match($nameRegex, $el->get('name')) === 1){
$ret[] = $el;
}
}
return $ret;
}
public function getElement($name) {
return $this->getElementByName($name);
}
public function getElementByName($name) {
$els = $this->getElements(true, true);
foreach ($els as $el) {
if ($el->get('name') == $name) return $el;
}
return false;
}
public function getElementValue($name){
$el = $this->getElement($name);
if(!$el){
return null;
}
return $el->get('value');
}
}
class FormElement {
protected $_attributes = array();
protected $_error;
protected $_validattributes = array();
public $requiresupload = false;
public $validation = null;
public $validationmessage = null;
public $persistent = true;
public $classnames = array();
public function __construct($atts = null) {
if ($atts) $this->setFromArray($atts);
}
public function set($key, $value) {
$key = strtolower($key);
switch ($key) {
case 'class':
$this->classnames[] = $value;
break;
case 'value': // Drop into special logic.
$this->setValue($value);
break;
case 'label': // This is an alias for title.
$this->_attributes['title'] = $value;
break;
case 'options':
if (!is_array($value)) {
$this->_attributes[$key] = $value;
}
elseif(\Core\is_numeric_array($value)) {
$o = array();
foreach ($value as $v) {
$o[$v] = $v;
}
$this->_attributes[$key] = $o;
}
else{
$this->_attributes[$key] = $value;
}
break;
case 'autocomplete':
if($value === false || $value === '0' | $value === 0 || $value === 'off'){
$this->_attributes[$key] = 'off';
}
elseif($value === true || $value === '1' || $value === 1 || $value === 'on' || $value === ''){
$this->_attributes[$key] = 'on';
}
else{
$this->_attributes[$key] = \Core\resolve_link($value);
}
break;
case 'persistent':
$this->persistent = $value;
break;
default:
$this->_attributes[$key] = $value;
break;
}
}
public function get($key) {
$key = strtolower($key);
switch ($key) {
case 'label': // Special case, returns either title or name, whichever is set.
if (!empty($this->_attributes['title'])) return $this->_attributes['title'];
else return $this->get('name');
break;
case 'id': // ID is also a special case, it casn use the name if not defined otherwise.
return $this->getID();
break;
default:
return (isset($this->_attributes[$key])) ? $this->_attributes[$key] : null;
}
}
public function getAsArray() {
$ret            = array();
$ret['__class'] = get_class($this);
foreach ($this->_attributes as $k => $v) {
$ret[$k] = (isset($this->_attributes[$k])) ? $this->_attributes[$k] : null;
}
return $ret;
}
public function setFromArray($array) {
foreach ($array as $k => $v) {
$this->set($k, $v);
}
}
public function setValue($value) {
if(isset($this->_attributes['validation']) && $this->_attributes['validation'] == Model::VALIDATION_URL_WEB){
if(trim($value) != '' && strpos($value, '://') === false){
$value = 'http://' . $value;
}
}
$valid = $this->validate($value);
if($valid !== true){
$this->_error = $valid;
return false;
}
$this->_attributes['value'] = $value;
return true;
}
public function validate($value){
if ($this->get('required') && !$value) {
return $this->get('label') . ' is required.';
}
if ($value && $this->validation) {
$vmesg = $this->validationmessage ? $this->validationmessage : $this->get('label') . ' does not validate correctly, please double check it.';
$v     = $this->validation;
if (strpos($v, '::') !== false && ($out = call_user_func($v, $value)) !== true) {
if ($out !== false) $vmesg = $out;
return $vmesg;
}
elseif (
($v{0} == '/' && !preg_match($v, $value)) ||
($v{0} == '#' && !preg_match($v, $value))
) {
if (DEVELOPMENT_MODE) $vmesg .= ' validation used: ' . $v;
return $vmesg;
}
}
return true;
}
public function getValueTitle(){
$v = $this->get('value');
if($v === '' || $v === null) return null;
if($this->get('options') && isset($this->_attributes['options'][$v])) return $this->_attributes['options'][$v];
else return $v;
}
public function hasError() {
return ($this->_error);
}
public function getError() {
return $this->_error;
}
public function setError($err, $displayMessage = true) {
$this->_error = $err;
if ($err && $displayMessage) Core::SetMessage($err, 'error');
}
public function clearError() {
$this->setError(false);
}
public function getTemplateName() {
return 'forms/elements/' . strtolower(get_class($this)) . '.tpl';
}
public function render() {
if ($this->get('multiple') && !preg_match('/.*\[.*\]/', $this->get('name'))) $this->_attributes['name'] .= '[]';
$file = $this->getTemplateName();
$tpl = \Core\Templates\Template::Factory($file);
$tpl->assign('element', $this);
return $tpl->fetch();
}
public function getClass() {
$classes = array_merge($this->classnames, explode(' ', $this->get('class')));
if($this->get('required')) $classes[] = 'formrequired';
if($this->hasError()) $classes[] = 'formerror';
return implode(' ', array_unique($classes));
}
public function getID(){
if (!empty($this->_attributes['id'])){
return $this->_attributes['id'];
}
else{
$n = str_replace(['/', '[', ']'], '-', $this->get('name'));
$n = \Core\str_to_url($n);
$c = strtolower(get_class($this));
$id = $c . '-' . $n;
$id = str_replace('[]', '', $id);
$id = preg_replace('/\[([^\]]*)\]/', '-$1', $id);
return $id;
}
}
public function getInputAttributes() {
$out = '';
if(isset($this->_attributes['source']) && !isset($this->_attributes['options'])){
$source = $this->_attributes['source'];
if(
(is_array($source) && sizeof($source) == 2) ||
strpos($source, '::') !== false
){
$this->_attributes['options'] = call_user_func($source);
}
}
foreach ($this->_validattributes as $k) {
if (
$k == 'required' ||
$k == 'disabled' || $k == 'checked'
) {
if(!$this->get($k)) {
continue;
}
else {
$out .= sprintf(' %s="%s"', $k, $k);
}
}
elseif(($v = $this->get($k)) !== null) {
$out .= " $k=\"" . str_replace('"', '&quot;', $v) . "\"";
}
}
foreach($this->_attributes as $k => $v){
if(strpos($k, 'data-') === 0){
$out .= " $k=\"" . str_replace('"', '&quot;', $v) . "\"";
}
}
return $out;
}
public function lookupValueFrom(&$src) {
$n = $this->get('name');
if (strpos($n, '[') !== false) {
$base = substr($n, 0, strpos($n, '['));
if (!isset($src[$base])) return null;
$t = $src[$base];
preg_match_all('/\[(.+?)\]/', $n, $m);
foreach ($m[1] as $k) {
if (!isset($t[$k])) return null;
$t = $t[$k];
}
return $t;
}
else {
if (!isset($src[$n])) return null;
else return $src[$n];
}
}
public static function Factory($type, $attributes = array()) {
if (!isset(Form::$Mappings[$type])) $type = 'text'; // Default.
return new Form::$Mappings[$type]($attributes);
}
}
class Form extends FormGroup {
public $originalurl = '';
public $referrer = '';
public static $Mappings = array(
'access'           => 'FormAccessStringInput',
'button'           => 'FormButtonInput',
'checkbox'         => 'FormCheckboxInput',
'checkboxes'       => 'FormCheckboxesInput',
'date'             => 'FormDateInput',
'datetime'         => 'FormDateTimeInput',
'file'             => 'FormFileInput',
'hidden'           => 'FormHiddenInput',
'license'          => 'FormLicenseInput',
'markdown'         => 'FormMarkdownInput',
'pageinsertables'  => 'FormPageInsertables',
'pagemeta'         => 'FormPageMeta',
'pagemetas'        => 'FormPageMetasInput',
'pagemetaauthor'   => 'FormPageMetaAuthorInput',
'pagemetakeywords' => 'FormPageMetaKeywordsInput',
'pageparentselect' => 'FormPageParentSelectInput',
'pagerewriteurl'   => 'FormPageRewriteURLInput',
'pagethemeselect'  => 'FormPageThemeSelectInput',
'pagepageselect'   => 'FormPagePageSelectInput',
'password'         => 'FormPasswordInput',
'radio'            => 'FormRadioInput',
'reset'            => 'FormResetInput',
'select'           => 'FormSelectInput',
'state'            => 'FormStateInput',
'submit'           => 'FormSubmitInput',
'system'           => 'FormSystemInput',
'text'             => 'FormTextInput',
'textarea'         => 'FormTextareaInput',
'time'             => 'FormTimeInput',
'user'             => 'FormUserInput',
'wysiwyg'          => 'FormTextareaInput',
);
public static $GroupMappings = array(
'tabs'             => 'FormTabsGroup',
);
private $_models = array();
public function  __construct($atts = null) {
if($atts === null){
$atts = [];
}
if(!isset($atts['method'])) $atts['method'] = 'POST';
if(!isset($atts['orientation'])) $atts['orientation'] = 'horizontal';
parent::__construct($atts);
$this->_validattributes = array('accept', 'accept-charset', 'action', 'enctype', 'id', 'method', 'name', 'target', 'style');
$this->persistent = false;
}
public function getTemplateName() {
return 'forms/form.tpl';
}
public function generateUniqueHash(){
$hash = '';
$set = false;
$hash .= $this->get('callsmethod') . ';';
foreach($this->_models as $m => $model){
$i = $model->GetIndexes();
if(isset($i['primary'])){
if(is_array($i['primary'])){
foreach($i['primary'] as $k){
$hash .= $m . '.' . $k . ':' . $model->get($k) . ';';
}
}
else{
$hash .= $m . '.' . $i['primary'] . ':' . $model->get( $i['primary'] ) . ';';
}
}
}
foreach ($this->getElements() as $el) {
if($el->get('name') == '___formid') continue;
if($el instanceof FormSystemInput){
$set = true;
$hash .= get_class($el) . ':' . $el->get('name') . ':' . json_encode($el->get('value')) . ';';
}
}
if(!$set){
foreach ($this->getElements() as $el) {
if($el->get('name') == '___formid') continue;
if(!($el instanceof FormSystemInput)){
$hash .= get_class($el) . ':' . $el->get('name') . ';';
}
}
}
$hash = md5($hash);
return $hash;
}
public function  render($part = null) {
foreach ($this->getElements() as $e) {
if ($e->requiresupload) {
$this->set('enctype', 'multipart/form-data');
break;
}
}
$ignoreerrors = false;
if (($part === null || $part == 'body') && $this->get('callsmethod')) {
$hash = ($this->get('uniqueid') ? $this->get('uniqueid') : $this->generateUniqueHash());
if (($savedform = \Core\Session::Get('FormData/' . $hash)) !== null) {
if (($savedform = unserialize($savedform))) {
if($savedform->persistent){
foreach($this->_elements as $k => $element){
if($element->persistent){
$this->_elements[$k] = $savedform->_elements[$k];
}
}
}
}
else {
$ignoreerrors = true;
}
}
else {
$ignoreerrors = true;
}
}
if(($part == null || $part == 'foot') && $this->get('callsmethod')){
if (!$this->get('uniqueid')) {
$hash = $this->generateUniqueHash();
$this->set('uniqueid', $hash);
}
}
if ($ignoreerrors) {
foreach ($this->getElements(true) as $el) {
$el->setError(false);
}
}
$tpl = \Core\Templates\Template::Factory('forms/form.tpl');
$tpl->assign('group', $this);
if ($part === null || $part == 'body') {
$els = '';
foreach ($this->_elements as $e) {
$els .= $e->render();
}
$tpl->assign('elements', $els);
}
switch ($part) {
case null:
$out = $tpl->fetch('forms/form.tpl');
break;
case 'head':
$out = $tpl->fetch('forms/form.head.tpl');
break;
case 'body':
$out = $tpl->fetch('forms/form.body.tpl');
break;
case 'foot':
$out = $tpl->fetch('forms/form.foot.tpl');
break;
default:
if(($el = $this->getElement($part)) !== false){
$out = $el->render();
}
}
if(!$this->referrer && isset($_SERVER['HTTP_REFERER'])){
$this->referrer = $_SERVER['HTTP_REFERER'];
}
$this->originalurl = CUR_CALL;
$this->persistent = false;
if (($part === null || $part == 'foot') && $this->get('callsmethod')) {
$this->saveToSession();
}
return $out;
}
public function getGroup($name, $type = 'default'){
$element = $this->getElement($name);
if(!$element){
if(isset(self::$GroupMappings[$type])) $class = self::$GroupMappings[$type];
else $class = 'FormGroup'; // Default.
$ref = new ReflectionClass($class);
$element = $ref->newInstance(['name' => $name, 'title' => $name]);
$this->addElement($element);
}
return $element;
}
public function getModel($prefix = 'model') {
if(!isset($this->_models[$prefix])){
return null;
}
$model = $this->_models[$prefix];
$model->setFromForm($this, $prefix);
return $model;
}
public function getModels(){
return $this->_models;
}
public function loadFrom($src, $quiet = false) {
$els = $this->getElements(true, false);
foreach ($els as $e) {
$e->clearError();
if($e->get('disabled')){
continue;
}
$e->set('value', $e->lookupValueFrom($src));
if ($e->hasError() && !$quiet){
Core::SetMessage($e->getError(), 'error');
}
}
}
public function addModel(Model $model, $prefix = 'model'){
if(isset($this->_models[$prefix])) return;
$groups = array();
$this->_models[$prefix] = $model;
$s = $model->getKeySchemas();
$i = $model->GetIndexes();
if (!isset($i['primary'])) $i['primary'] = array();
$i18nKey = '_MODEL_' . strtoupper(get_class($model)) . '_';
$new = $model->isnew();
foreach ($s as $k => $v) {
$title       = t('STRING' . $i18nKey . strtoupper($k));
$description = t('MESSAGE' . $i18nKey . strtoupper($k));
if(!$title && isset($dat['formtitle'])){
$title = $dat['formtitle'];
}
if(!$description && isset($dat['formdescription'])){
$description = $dat['formdescription'];
}
$formatts = array(
'type' => null,
'title' => $title,
'description' => $description,
'required' => false,
'value' => $model->get($k),
'name' => $prefix . '[' . $k . ']',
);
$defaults = [];
if($formatts['value'] === null && isset($v['default'])) $formatts['value'] = $v['default'];
if(isset($v['formtype']))        $formatts['type'] = $v['formtype'];
if(isset($v['required']))        $formatts['required'] = $v['required'];
if(isset($v['maxlength']))       $formatts['maxlength'] = $v['maxlength'];
if(isset($v['form'])){
$formatts = array_merge($formatts, $v['form']);
}
if($formatts['type'] == 'disabled'){
continue;
}
elseif ($v['type'] == Model::ATT_TYPE_ID){
$el = FormElement::Factory('system');
$formatts['required'] = false;
}
elseif ($v['type'] == Model::ATT_TYPE_ID_FK){
$el = FormElement::Factory('system');
$formatts['required'] = false;
}
elseif($v['type'] == Model::ATT_TYPE_UUID){
$el = FormElement::Factory('system');
$formatts['required'] = false;
}
elseif($v['type'] == Model::ATT_TYPE_UUID_FK && $formatts['type'] === null){
$el = FormElement::Factory('system');
$formatts['required'] = false;
}
elseif($formatts['type'] == 'datetime' && $v['type'] == Model::ATT_TYPE_INT){
$defaults['datetimepicker_dateformat'] = 'yy-mm-dd';
$defaults['datetimepicker_timeformat'] = 'HH:mm';
$defaults['displayformat'] = 'Y-m-d H:i';
$defaults['saveformat'] = 'U';
$el = FormElement::Factory('datetime');
}
elseif ($formatts['type'] !== null) {
$el = FormElement::Factory($formatts['type']);
}
elseif ($v['type'] == Model::ATT_TYPE_BOOL) {
$el = FormElement::Factory('radio');
$el->set('options', ['yes' => t('STRING_YES'), 'no' => t('STRING_NO')]);
if ($formatts['value']) $formatts['value'] = 'yes';
elseif ($formatts['value'] === null && $v['default']) $formatts['value'] = 'yes';
elseif ($formatts['value'] === null && !$v['default']) $formatts['value'] = 'no';
else $formatts['value'] = 'no';
}
elseif ($v['type'] == Model::ATT_TYPE_SITE) {
$el = FormElement::Factory('system');
}
elseif ($v['type'] == Model::ATT_TYPE_STRING) {
$el = FormElement::Factory('text');
}
elseif ($v['type'] == Model::ATT_TYPE_INT) {
$el = FormElement::Factory('text');
}
elseif ($v['type'] == Model::ATT_TYPE_FLOAT) {
$el = FormElement::Factory('text');
}
elseif ($v['type'] == Model::ATT_TYPE_TEXT) {
$el = FormElement::Factory('textarea');
}
elseif ($v['type'] == Model::ATT_TYPE_CREATED) {
continue;
}
elseif ($v['type'] == Model::ATT_TYPE_UPDATED) {
continue;
}
elseif ($v['type'] == Model::ATT_TYPE_DELETED) {
continue;
}
elseif ($v['type'] == Model::ATT_TYPE_ALIAS) {
continue;
}
elseif ($v['type'] == Model::ATT_TYPE_ENUM) {
$el   = FormElement::Factory('select');
$opts = $v['options'];
if ($v['null']) $opts = array_merge(array('' => '-Select One-'), $opts);
$el->set('options', $opts);
if ($v['default']) $el->set('value', $v['default']);
}
elseif($v['type'] == Model::ATT_TYPE_ISO_8601_DATE){
$defaults['datepicker_dateformat'] = 'yy-mm-dd';
$el = FormElement::Factory('date');
}
elseif($v['type'] == Model::ATT_TYPE_ISO_8601_DATETIME){
$defaults['datetimepicker_dateformat'] = 'yy-mm-dd';
$defaults['datetimepicker_timeformat'] = 'HH:mm';
$defaults['saveformat'] = 'Y-m-d H:i:00';
$el = FormElement::Factory('datetime');
}
else {
die('Unsupported model attribute type for Form Builder [' . $v['type'] . ']');
}
unset($formatts['type']);
foreach($defaults as $k => $v){
if(!isset($formatts[$k])) $formatts[$k] = $v;
}
if(isset($formatts['source']) && strpos($formatts['source'], 'this::') === 0){
$formatts['source'] = [$model, substr($formatts['source'], 6)];
}
$el->setFromArray($formatts);
$model->setToFormElement($k, $el);
$this->addElement($el);
}
$model->addToFormPost($this, $prefix);
}
public function addElement($element, $atts = []){
if(isset($atts['group'])){
$grouptype = isset($atts['grouptype']) ? $atts['grouptype'] : 'default';
$this->getGroup( $atts['group'], $grouptype )->addElement($element, $atts);
}
elseif($element instanceof FormElement && $element->get('group')){
$grouptype = $element->get('grouptype') ? $element->get('grouptype') : 'default';
$this->getGroup( $element->get('group'), $grouptype )->addElement($element, $atts);
}
else{
parent::addElement($element, $atts);
}
}
public function switchElementType($elementname, $newtype) {
$el = $this->getElement($elementname);
if (!$el) return false;
if (!isset(self::$Mappings[$newtype])) $newtype = 'text';
$cls = self::$Mappings[$newtype];
if (get_class($el) == $cls) return false;
$atts = $el->getAsArray();
unset($atts['__class']);
$newel = new $cls();
$newel->setFromArray($atts);
$this->switchElement($el, $newel);
return true;
}
public function saveToSession() {
if (!$this->get('callsmethod')) return; // Don't save anything if there's no method to call.
$this->set('expires', (int)Time::GetCurrent() + 1800); // 30 minutes
\Core\Session::Set('FormData/' . $this->get('uniqueid'), serialize($this));
}
public function clearFromSession(){
$hash = $this->get('uniqueid') ? $this->get('uniqueid') : $this->generateUniqueHash();
\Core\Session::UnsetKey('FormData/' . $hash);
}
public static function CheckSavedSessionData() {
if(preg_match('#^/form/(.*)\.ajax$#', REL_REQUEST_PATH)) return;
$forms = \Core\Session::Get('FormData/*');
$formid = (isset($_REQUEST['___formid'])) ? $_REQUEST['___formid'] : false;
$form   = false;
foreach ($forms as $k => $v) {
if (!($el = unserialize($v))) {
\Core\Session::UnsetKey('FormData/' . $k);
continue;
}
if ($el->get('expires') <= Time::GetCurrent()) {
\Core\Session::UnsetKey('FormData/' . $k);
continue;
}
if ($k == $formid) {
$form = $el;
}
}
if (!$form) return;
if (strtoupper($form->get('method')) != $_SERVER['REQUEST_METHOD']) {
\Core\set_message('MESSAGE_ERROR_FORM_SUBMISSION_TYPE_DOES_NOT_MATCH');
return;
}
if($_SERVER['HTTP_REFERER'] != $form->originalurl){
SystemLogModel::LogInfoEvent(
'Form Referrer Mismatch',
'Form referrer does not match!  Submitted: [' . $_SERVER['HTTP_REFERER'] . '] Expected: [' . $form->originalurl . ']'
);
}
if (strtoupper($form->get('method')) == 'POST') $src =& $_POST;
else $src =& $_GET;
$form->loadFrom($src);
try{
$form->getModel();
if (!$form->hasError()) $status = call_user_func($form->get('callsmethod'), $form);
else $status = false;
}
catch(ModelValidationException $e){
Core::SetMessage($e->getMessage(), 'error');
$status = false;
}
catch(GeneralValidationException $e){
Core::SetMessage($e->getMessage(), 'error');
$status = false;
}
catch(Exception $e){
if(DEVELOPMENT_MODE){
Core::SetMessage($e->getMessage(), 'error');
}
else{
\Core\set_message('MESSAGE_ERROR_FORM_SUBMISSION_UNHANDLED_EXCEPTION');
}
Core\ErrorManagement\exception_handler($e);
$status = false;
}
$form->persistent = true;
\Core\Session::Set('FormData/' . $formid, serialize($form));
if ($status === false) return;
if ($status === null) return;
\Core\Session::UnsetKey('FormData/' . $formid);
if ($status === 'die'){
exit;
}
elseif($status === 'back'){
if($form->referrer && $form->referrer != REL_REQUEST_PATH){
\Core\redirect($form->referrer);
}
else{
\Core\go_back();
}
}
elseif ($status === true){
\Core\reload();
}
elseif($status === REL_REQUEST_PATH){
\Core\reload();
}
else{
\core\redirect($status);
}
}
public static function BuildFromModel(Model $model) {
$f = new Form();
$f->addModel($model);
return $f;
}
}


### REQUIRE_ONCE FROM core/libs/core/PageRequest.class.php
class PageRequest {
const METHOD_HEAD   = 'HEAD';
const METHOD_GET    = 'GET';
const METHOD_POST   = 'POST';
const METHOD_PUT    = 'PUT';
const METHOD_PUSH   = 'PUSH';
const METHOD_DELETE = 'DELETE';
public $contentTypes = array();
public $acceptLanguages = array();
public $method = null;
public $useragent;
public $uri;
public $uriresolved;
public $protocol;
public $parameters = array();
public $ctype = View::CTYPE_HTML;
public $ext = 'html';
public $host;
private $_pagemodel = null;
private $_pageview = null;
private $_cached = false;
public function __construct($uri = '') {
if (!$uri) $uri = ROOT_WDIR;
$uri = substr($uri, strlen(ROOT_WDIR));
if ($uri{0} != '/') $uri = '/' . $uri;
$pagedat = PageModel::SplitBaseURL($uri);
$this->host = SERVERNAME;
$this->uri = $uri;
$this->uriresolved = $pagedat['rewriteurl'];
$this->protocol    = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
$this->ext         = $pagedat['extension'];
$this->ctype       = $pagedat['ctype'];
$this->parameters  = ($pagedat['parameters'] === null) ? [] : $pagedat['parameters'];
$this->_resolveMethod();
$this->_resolveAcceptHeader();
$this->_resolveUAHeader();
$this->_resolveLanguageHeader();
}
public function prefersContentType($type) {
$current     = 0;
$currentmain = substr($this->ctype, 0, strpos($this->ctype, '/'));
foreach ($this->contentTypes as $t) {
if ($t['type'] == $this->ctype || ($t['type'] == $t['group'] . '/*' && $t['group'] == $currentmain)) {
$current = max($current, $t['weight']);
}
}
$typeweight = 0;
$typemain   = substr($type, 0, strpos($type, '/'));
foreach ($this->contentTypes as $t) {
if ($t['type'] == $type || ($t['type'] == $t['group'] . '/*' && $t['group'] == $typemain)) {
$typeweight = max($typeweight, $t['weight']);
}
}
return ($typeweight > $current);
}
public function splitParts() {
return PageModel::SplitBaseURL($this->uriresolved);
}
public function getBaseURL() {
$parts = $this->splitParts();
return isset($parts['baseurl']) ? $parts['baseurl'] : null;
}
public function getView(){
if($this->_pageview === null){
$this->_pageview = new View();
}
return $this->_pageview;
}
public function execute() {
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Starting PageRequest->execute()');
if($this->isCacheable()){
$uakey = \Core\UserAgent::Construct()->getPseudoIdentifier();
$urlkey = $this->host . $this->uri;
$expires = $this->getPageModel()->get('expires');
$key = 'page-cache-' . md5($urlkey . '-' . $uakey);
$cached = \Core\Cache::Get($key, $expires);
if($cached && $cached instanceof View){
$this->_pageview = $cached;
$this->_cached = true;
return;
}
}
HookHandler::DispatchHook('/core/page/preexecute');
$pagedat   = $this->splitParts();
$view = $this->getView();
if (!(isset($pagedat['controller']) && $pagedat['controller'])) {
$view->error = View::ERROR_NOTFOUND;
return;
}
$component = Core::GetComponentByController($pagedat['controller']);
if (!$component) {
$view->error = View::ERROR_NOTFOUND;
return;
}
elseif(!is_a($component, 'Component_2_1')) {
$view->error = View::ERROR_NOTFOUND;
return;
}
if ($pagedat['method']{0} == '_') {
$view->error = View::ERROR_NOTFOUND;
return;
}
if (!method_exists($pagedat['controller'], $pagedat['method'])) {
$view->error = View::ERROR_NOTFOUND;
return;
}
$controller = Controller_2_1::Factory($pagedat['controller']);
$view->baseurl = $this->getBaseURL();
$controller->setView($view);
$controller->setPageRequest($this);
$page = $this->getPageModel();
if ($controller->accessstring !== null) {
$page->set('access', $controller->accessstring);
if (!\Core\user()->checkAccess($controller->accessstring)) {
$view->error = View::ERROR_ACCESSDENIED;
return;
}
}
if($page->get('password_protected')) {
if(\Core\Session::Get('page-password-protected/' . $page->get('baseurl')) !== $page->get('password_protected')){
$view->templatename = '/pages/page/passwordprotected.tpl';
$form = new Form();
$form->set('callsmethod', 'PageRequest::PasswordProtectHandler');
$form->addElement(
'system', [
'name'  => 'page',
'value' => $page
]
);
$form->addElement(
'password', [
'name'      => 'passinput',
'title'     => 'Password',
'required'  => 'required',
'maxlength' => 128
]
);
$form->addElement(
'submit', [
'value' => 'Submit'
]
);
$view->assign('form', $form);
return;
}
}
foreach(get_class_methods('Controller_2_1') as $parentmethod){
$parentmethod = strtolower($parentmethod);
if($parentmethod == $pagedat['method']){
$view->error = View::ERROR_BADREQUEST;
return;
}
}
if(!$page->exists() && Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$site = MultiSiteHelper::GetCurrentSiteID();
$anypage = PageModel::Find(['baseurl = ' . $page->get('baseurl')], 1);
if($anypage){
if($anypage->get('site') == -1){
$page = $anypage;
}
elseif($anypage->get('site') == $site){
$page = $anypage;
}
else{
\Core\redirect($anypage->getResolvedURL());
}
}
}
$return = call_user_func(array($controller, $pagedat['method']));
if (is_int($return)) {
$view->error = $return;
}
elseif(is_a($return, 'View') && $return != $view){
$this->_pageview = $view = $return;
}
elseif ($return === null) {
$return = $controller->getView();
if($return != $view){
$this->_pageview = $view = $return;
}
}
elseif(!is_a($return, 'View')){
if(DEVELOPMENT_MODE){
var_dump('Controller method returned', $return);
die('Sorry, but this controller did not return a valid object.  Please ensure that your method returns either an integer, null, or a View object!');
}
else{
$view->error = View::ERROR_SERVERERROR;
return;
}
}
if($view->error == View::ERROR_NOERROR){
$controls = $controller->getControls();
if(is_array($controls)){
foreach($controls as $control){
$view->addControl($control);
}
}
}
if($view->error == View::ERROR_NOERROR){
if ($page->exists()) {
$defaultpage = $page;
} else {
$defaultpage = null;
$url         = $view->baseurl;
while ($url != '') {
$url = substr($url, 0, strrpos($url, '/'));
$p   = PageModel::Find(array('baseurl' => $url, 'fuzzy' => 1), 1);
if ($p === null) continue;
if ($p->exists()) {
$defaultpage = $p;
break;
}
}
if ($defaultpage === null) {
$defaultpage = $page;
}
}
$defaultmetas = $defaultpage->getLink('PageMeta');
$currentmetas = array();
foreach($view->meta as $k => $meta){
$currentmetas[] = $k;
}
foreach($defaultmetas as $meta){
$key = $meta->get('meta_key');
$viewmeta = $meta->getViewMetaObject();
if ($meta->get('meta_value_title') && !in_array($key, $currentmetas)) {
$view->meta[$key] = $viewmeta;
}
}
if ($view->title === null){
$view->title = $defaultpage->get('title');
}
$isadmin = ($page->get('admin') == '1');
$parents = array();
$parenttree = $page->getParentTree();
foreach ($parenttree as $parent) {
$parents[] = array(
'title' => $parent->get('title'),
'link'  => $parent->getResolvedURL()
);
if($parent->get('admin')){
$isadmin = true;
}
}
$view->breadcrumbs = array_merge($parents, $view->breadcrumbs);
if($isadmin && $view->baseurl != '/admin'){
$adminlink = \Core\resolve_link('/admin');
if(!isset($view->breadcrumbs[0])){
$view->breadcrumbs[] = ['title' => 'Administration', 'link' => $adminlink];
}
elseif($view->breadcrumbs[0]['link'] != $adminlink){
$view->breadcrumbs = array_merge([['title' => 'Administration', 'link' => $adminlink]], $view->breadcrumbs);
}
}
}
else{
$defaultpage = null;
$isadmin = false;
}
if(
$view->mode == View::MODE_PAGEORAJAX &&
$this->isAjax() &&
$view->jsondata !== null &&
$view->templatename === null
){
$view->contenttype = View::CTYPE_JSON;
}
if(
$view->error == View::ERROR_NOERROR &&
$view->contenttype == View::CTYPE_HTML &&
$view->templatename === null
){
$cnameshort           = (strpos($pagedat['controller'], 'Controller') == strlen($pagedat['controller']) - 10) ? substr($pagedat['controller'], 0, -10) : $pagedat['controller'];
$view->templatename = strtolower('/pages/' . $cnameshort . '/' . $pagedat['method'] . '.tpl');
}
elseif(
$view->error == View::ERROR_NOERROR &&
$view->contenttype == View::CTYPE_XML &&
$view->templatename === null
){
$cnameshort           = (strpos($pagedat['controller'], 'Controller') == strlen($pagedat['controller']) - 10) ? substr($pagedat['controller'], 0, -10) : $pagedat['controller'];
$view->templatename = Template::ResolveFile(strtolower('pages/' . $cnameshort . '/' . $pagedat['method'] . '.xml.tpl'));
}
if($defaultpage && $defaultpage->get('page_template')){
$base     = substr($view->templatename, 0, -4);
$override = $defaultpage->get('page_template');
if($base && strpos($override, $base) === 0){
$view->templatename = $override;
}
elseif($base){
$view->templatename = $base . '/' . $override;
}
}
if($view->mastertemplate == 'admin'){
$view->mastertemplate = ConfigHandler::Get('/theme/default_admin_template');
}
elseif($view->mastertemplate){
}
elseif($view->mastertemplate === false){
}
elseif($isadmin){
$view->mastertemplate = ConfigHandler::Get('/theme/default_admin_template');
}
elseif ($defaultpage && $defaultpage->get('theme_template')) {
$view->mastertemplate = $defaultpage->get('theme_template');
}
elseif($defaultpage && $defaultpage->exists() && $defaultpage->get('admin')){
$view->mastertemplate = ConfigHandler::Get('/theme/default_admin_template');
}
elseif(sizeof($view->breadcrumbs) && $view->breadcrumbs[0]['title'] == 'Administration'){
$view->mastertemplate = ConfigHandler::Get('/theme/default_admin_template');
}
else{
$view->mastertemplate = ConfigHandler::Get('/theme/default_template');
}
if(!($theme = ThemeHandler::GetTheme())){
$theme = ThemeHandler::GetTheme('base-v2');
$view->mastertemplate = 'basic.tpl';
\Core\set_message('MESSAGE_ERROR_INVALID_THEME_SELECTED');
}
if($view->mastertemplate !== false){
$themeskins = $theme->getSkins();
$mastertplgood = false;
foreach($themeskins as $skin){
if($skin['file'] == $view->mastertemplate){
$mastertplgood =true;
break;
}
}
if($view->mastertemplate == 'blank.tpl'){
$mastertplgood =true;
}
if(!$mastertplgood){
trigger_error('Invalid skin [' . $view->mastertemplate . '] selected for this page, skin is not located within the selected theme!  Using first available instead.', E_USER_NOTICE);
$view->mastertemplate = $themeskins[0]['file'];
}
}
if(\ConfigHandler::Get('/core/page/indexable') == 'deny'){
$view->addMetaName('robots', 'noindex');
}
elseif(!$page->get('indexable')){
$view->addMetaName('robots', 'noindex');
}
if(!isset($view->meta['title'])){
$view->meta['title'] = $page->getSEOTitle();
}
HookHandler::DispatchHook('/core/page/postexecute');
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Completed PageRequest->execute()');
}
public function render(){
\Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->record('Starting PageRequest->render()');
$view = $this->getView();
$page = $this->getPageModel();
if ($view->error == View::ERROR_ACCESSDENIED || $view->error == View::ERROR_NOTFOUND) {
HookHandler::DispatchHook('/core/page/error-' . $view->error, $view);
}
try {
if(\Core\user()->checkAccess('p:user_activity_list') && $page && $page->exists()){
$view->addControl(
'User Activity Details',
'/useractivity/details?filter[baseurl]=' . $page->get('baseurl'),
'eye'
);
}
$view->fetch();
}
catch (Exception $e) {
$view->error   = View::ERROR_SERVERERROR;
$view->baseurl = '/error/error/500';
$view->setParameters(array());
$view->templatename   = '/pages/error/error500.tpl';
$view->mastertemplate = ConfigHandler::Get('/theme/default_template');
$view->assignVariable('exception', $e);
\Core\ErrorManagement\exception_handler($e);
$view->fetch();
}
if($this->isCacheable()){
$uakey = \Core\UserAgent::Construct()->getPseudoIdentifier();
$urlkey = $this->host . $this->uri;
$expires = $page->get('expires'); // Number of seconds.
$key = 'page-cache-' . md5($urlkey . '-' . $uakey);
$d = new \Core\Date\DateTime();
$d->modify('+' . $expires . ' seconds');
$view->headers['Cache-Control'] = 'max-age=' . $expires;
$view->headers['Expires'] = $d->format('r', \Core\Date\Timezone::TIMEZONE_GMT);
$view->headers['Vary'] = 'Accept-Encoding,User-Agent,Cookie';
$view->headers['X-Core-Cached-Date'] = \Core\Date\DateTime::NowGMT('r');
$view->headers['X-Core-Cached-Server'] = 1; // @todo Implement multi-server support.
$view->headers['X-Core-Cached-Render-Time'] = \Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->getTimeFormatted();
\Core\Cache::Set($key, $view, $expires);
$indexkey = $page->getIndexCacheKey();
$index = \Core\Cache::Get($indexkey, SECONDS_ONE_DAY);
if(!$index){
$index = [];
}
$index[] = $key;
\Core\Cache::Set($indexkey, $index, SECONDS_ONE_DAY);
}
elseif(($reason = $this->isNotCacheableReason()) !== null){
$view->headers['X-Core-NotCached-Reason'] = $reason;
}
$view->headers['X-Core-Render-Time'] = \Core\Utilities\Profiler\Profiler::GetDefaultProfiler()->getTimeFormatted();
$view->render();
if ($page && $page->exists() && $view->error == View::ERROR_NOERROR) {
if(!\Core\UserAgent::Construct()->isBot()){
$page->set('pageviews', $page->get('pageviews') + 1);
}
$page->set('last_template', $view->templatename);
$page->set('body', $view->fetchBody());
$page->save();
}
HookHandler::DispatchHook('/core/page/postrender');
}
public function isNotCacheableReason(){
$cacheable = null;
if(DEVELOPMENT_MODE){
$cacheable = 'Site is in development mode';
}
if(!\ConfigHandler::Get('/core/performance/anonymous_user_page_cache')){
$cacheable = 'Anonymous user cache disabled in the system';
}
elseif(\Core\user()->exists()){
$cacheable = 'Logged in users do not get cached pages';
}
elseif($this->method != PageRequest::METHOD_GET){
$cacheable = 'Request is not a GET';
}
elseif(!$this->getView()->isCacheable()){
$cacheable = 'Page explicitly set as not cacheable';
}
elseif($this->getPageModel()->get('expires') == 0){
$cacheable = 'Page expire set to 0, cache disabled';
}
elseif($this->getView()->mode != View::MODE_PAGE){
$cacheable = 'Request is not a PAGE type';
}
return $cacheable;
}
public function isCacheable(){
if($this->_cached){
return false;
}
return ($this->isNotCacheableReason() === null);
}
public function setParameters($params) {
$this->parameters = $params;
}
public function setParameter($key, $value){
$this->parameters[$key] = $value;
}
public function getParameters() {
$data = $this->splitParts();
if($data['parameters'] === null){
return [];
}
else{
return $data['parameters'];
}
}
public function getParameter($key) {
$data = $this->splitParts();
if($data['parameters'] === null){
return null;
}
elseif(array_key_exists($key, $data['parameters'])){
return $data['parameters'][$key];
}
else{
return null;
}
}
public function getPost($key){
$src = &$_POST;
if(strpos($key, '[') !== false){
$k1 = substr($key, 0, strpos($key, '['));
$key = substr($key, strlen($k1) + 1, -1);
$src = &$_POST[$k1];
}
return (isset($src[$key])) ? $src[$key] : null;
}
public function getPageModel() {
if ($this->_pagemodel === null) {
$uri = $this->uriresolved;
$pagefac = new ModelFactory('PageModel');
$pagefac->where('rewriteurl = ' . $uri);
$pagefac->limit(1);
if(Core::IsComponentAvailable('enterprise') && MultiSiteHelper::IsEnabled()){
$pagefac->whereGroup('OR', array('site = -1', 'site = ' . MultiSiteHelper::GetCurrentSiteID()));
}
$p = $pagefac->get();
$pagedat = $this->splitParts();
if ($p) {
$this->_pagemodel = $p;
}
elseif ($pagedat && isset($pagedat['baseurl'])) {
$p = new PageModel($pagedat['baseurl']);
if(!$p->exists()){
$p->set('rewriteurl', $pagedat['rewriteurl']);
}
$this->_pagemodel = $p;
}
else {
$this->_pagemodel = new PageModel();
}
if ($pagedat && $pagedat['parameters']) {
foreach ($pagedat['parameters'] as $k => $v) {
$this->_pagemodel->setParameter($k, $v);
}
}
if (is_array($_GET)) {
foreach ($_GET as $k => $v) {
if (is_numeric($k)) continue;
$this->_pagemodel->setParameter($k, $v);
}
}
}
return $this->_pagemodel;
}
public function isPost() {
return ($this->method == PageRequest::METHOD_POST);
}
public function isGet() {
return ($this->method == PageRequest::METHOD_GET);
}
public function isJSON(){
return ($this->ctype == View::CTYPE_JSON);
}
public function isAjax(){
return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}
public function getUserAgent(){
return new \Core\UserAgent($this->useragent);
}
public function getReferrer(){
if(isset($_SERVER['HTTP_REFERER'])){
return $_SERVER['HTTP_REFERER'];
}
else{
return ROOT_URL;
}
}
private function _resolveMethod() {
$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
switch ($method) {
case self::METHOD_DELETE:
case self::METHOD_GET:
case self::METHOD_HEAD:
case self::METHOD_POST:
case self::METHOD_PUSH:
case self::METHOD_PUT:
$this->method = $method;
break;
default:
$this->method = self::METHOD_GET;
}
}
private function _resolveAcceptHeader() {
$header = (isset($_SERVER['HTTP_ACCEPT'])) ? $_SERVER['HTTP_ACCEPT'] : 'text/html';
$header = explode(',', $header);
$this->contentTypes = array();
if ($this->ctype == View::CTYPE_JSON) {
if (ALLOW_NONXHR_JSON || $this->isAjax()) {
$this->contentTypes[] = array(
'type'   => View::CTYPE_JSON,
'weight' => 1.0
);
}
else {
$this->ctype = View::CTYPE_HTML;
}
}
foreach ($header as $h) {
if (strpos($h, ';') === false) {
$weight  = 1.0; // Do 1.0 to ensure it's parsed as a float and not an int.
$content = $h;
}
else {
list($content, $weight) = explode(';', $h);
$weight = floatval(substr($weight, 3));
}
$this->contentTypes[] = array(
'type'   => $content,
'weight' => $weight
);
}
foreach ($this->contentTypes as $k => $v) {
$this->contentTypes[$k]['group'] = substr($v['type'], 0, strpos($v['type'], '/'));
}
}
private function _resolveLanguageHeader() {
$header = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en';
$header = explode(',', $header);
$this->acceptLanguages = array();
$langs = [];
$langs['en'] = 0.0;
foreach ($header as $h) {
if (strpos($h, ';') === false) {
$weight  = 1.0; // Do 1.0 to ensure it's parsed as a float and not an int.
$content = $h;
}
else {
list($content, $weight) = explode(';', $h);
$weight = floatval(substr($weight, 3));
}
$content = str_replace('-', '_', $content);
$langs[$content] = $weight;
}
if(isset($_COOKIE['LANG'])){
$langs[ $_COOKIE['LANG'] ] = 2;
}
arsort($langs);
foreach($langs as $l => $w){
if(preg_match('/^[a-z]{2,3}(_[A-Z]{2})?$/', $l) === 1){
$this->acceptLanguages[] = $l;
}
}
}
private function _resolveUAHeader() {
$ua              = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
$this->useragent = $ua;
}
public static function GetSystemRequest() {
static $instance = null;
if ($instance === null) {
$instance = new PageRequest(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null);
}
return $instance;
}
public static function PasswordProtectHandler(Form $form){
$page = $form->getElementValue('page');
$val  = $form->getElementValue('passinput');
if( $val !== $page->get('password_protected') ){
\Core\set_message('MESSAGE_ERROR_INCORRECT_PASSWORD');
return false;
}
else {
\Core\Session::Set('page-password-protected/' . $page->get('baseurl'), $val);
return true;
}
}
}


### REQUIRE_ONCE FROM core/libs/core/Controller_2_1.class.php
class Controller_2_1 {
private $_request = null;
private $_model = null;
private $_view = null;
public $accessstring = null;
protected function getPageRequest() {
if ($this->_request === null) {
$this->_request = PageRequest::GetSystemRequest();
}
return $this->_request;
}
public function setPageRequest(PageRequest $request){
$this->_request = $request;
}
public function setView(View $view){
$this->_view = $view;
}
public function getView() {
if ($this->_view === null) {
$this->_view          = new View();
$this->_view->baseurl = $this->getPageRequest()->getBaseURL();
}
return $this->_view;
}
public function getControls(){
return null;
}
protected function overwriteView($newview) {
$newview->error = View::ERROR_NOERROR;
$this->_view = $newview;
}
public function getPageModel() {
return $this->getPageRequest()->getPageModel();
}
public function sendJSONError($code, $message, $redirect){
$view    = $this->getView();
$request = $this->getPageRequest();
if($request->isAjax()){
$view->mode = View::MODE_PAGEORAJAX;
$view->jsondata = ['status' => $code, 'message' => $message];
$view->error = $code;
}
else{
Core::SetMessage($message, 'error');
if($redirect){
\Core\redirect($redirect);
}
else{
\Core\go_back();
}
}
}
protected function setAccess($accessstring) {
$this->getPageModel()->set('access', $accessstring);
return (\Core\user()->checkAccess($accessstring));
}
protected function setContentType($ctype) {
$this->getView()->contenttype = $ctype;
}
protected function setTemplate($template) {
$this->getView()->templatename = $template;
}
public static function Factory($name) {
return new $name();
}
}


### REQUIRE_ONCE FROM core/libs/core/CoreDateTime.php
class CoreDateTime {
private $_dt;
public function __construct($datetime = null){
if($datetime){
$this->setDate($datetime);
}
else{
$this->_dt = new DateTime();
}
}
public function setDate($datetime){
if(is_numeric($datetime)){
$this->_dt = new DateTime(null, self::_GetTimezone('GMT'));
$this->_dt->setTimestamp($datetime);
}
else{
$this->_dt = new DateTime($datetime, self::_GetTimezone(TIME_DEFAULT_TIMEZONE));
}
}
public function getTimezoneName(){
if(!$this->_dt) return null;
return $this->_dt->getTimezone()->getName();
}
public function isGMT(){
if(!$this->_dt) return false;
return ($this->_dt->getTimezone()->getName() == 'UTC');
}
public function getFormatted($format, $desttimezone = Time::TIMEZONE_USER){
if($format == 'RELATIVE'){
return $this->getRelative();
}
else{
$tzto = self::_GetTimezone($desttimezone);
if($tzto->getName() == $this->_dt->getTimezone()->getName()){
return $this->_dt->format($format);
}
$clone = clone $this->_dt;
$clone->setTimezone($tzto);
return $clone->format($format);
}
}
public function getRelative($dateformat = 'M j, Y', $timeformat = 'g:ia', $accuracy = 3, $timezone = Time::TIMEZONE_DEFAULT) {
$now = new DateTime();
$now->setTimezone(self::_GetTimezone($timezone));
$nowStamp = $now->format('Ymd');
$cStamp   = $this->getFormatted('Ymd', $timezone);
if ($nowStamp - $cStamp == 0) return 'Today at ' . $this->getFormatted($timeformat, $timezone);
elseif ($nowStamp - $cStamp == 1) return 'Yesterday at ' . $this->getFormatted($timeformat, $timezone);
elseif ($nowStamp - $cStamp == -1) return 'Tomorrow at ' . $this->getFormatted($timeformat, $timezone);
if ($accuracy <= 2) return $this->getFormatted($dateformat, $timezone);
if (abs($nowStamp - $cStamp) > 6) return $this->getFormatted($dateformat, $timezone);
return $this->getFormatted('l \a\t ' . $timeformat, $timezone);
}
public function modify($modify){
return ($this->_dt->modify($modify));
}
public static function Now($format = 'Y-m-d', $timezone = Time::TIMEZONE_DEFAULT){
$d = new CoreDateTime();
return $d->getFormatted($format, $timezone);
}
private static function _GetTimezone($timezone) {
static $timezones = array();
if ($timezone == Time::TIMEZONE_USER) {
$timezone = \Core\user()->get('timezone');
if($timezone === null) $timezone = date_default_timezone_get();
if (is_numeric($timezone)) $timezone = Time::TIMEZONE_DEFAULT;
}
if($timezone === Time::TIMEZONE_GMT || $timezone === 'GMT'){
$timezone = 'UTC';
}
elseif($timezone == Time::TIMEZONE_DEFAULT){
$timezone = TIME_DEFAULT_TIMEZONE;
}
if (!isset($timezones[$timezone])) {
$timezones[$timezone] = new DateTimeZone($timezone);
}
return $timezones[$timezone];
}
}




if(Core::IsComponentAvailable('geographic-codes') && class_exists('GeoIp2\\Database\\Reader')){
try{
if(REMOTE_IP == '127.0.0.1'){
$geocity     = 'Columbus';
$geoprovince = 'OH';
$geocountry  = 'US';
$geotimezone = 'America/New_York';
$geopostal   = '43215';
}
else{
$reader = new GeoIp2\Database\Reader(ROOT_PDIR . 'components/geographic-codes/libs/maxmind-geolite-db/GeoLite2-City.mmdb');
$profiler->record('Initialized GeoLite Database');
$geo = $reader->cityIspOrg(REMOTE_IP);
$profiler->record('Read GeoLite Database');
$reader->close();
$profiler->record('Closed GeoLite Database');
$geocity = $geo->city->name;
if(isset($geo->subdivisions[0]) && $geo->subdivisions[0] !== null){
$geoprovinceobj = $geo->subdivisions[0];
$geoprovince = $geoprovinceobj->isoCode;
}
else{
$geoprovince = '';
}
$geocountry  = $geo->country->isoCode;
$geotimezone = $geo->location->timeZone;
$geopostal   = $geo->postal->code;
unset($geoprovinceobj, $geo, $reader);
}
}
catch(Exception $e){
$geocity     = 'McMurdo Base';
$geoprovince = '';
$geocountry  = 'AQ'; // Yes, AQ is Antarctica!
$geotimezone = 'CAST';
$geopostal   = null;
}
}
else{
$geocity     = 'McMurdo Base';
$geoprovince = '';
$geocountry  = 'AQ'; // Yes, AQ is Antarctica!
$geotimezone = 'CAST';
$geopostal   = null;
}
define('REMOTE_CITY', $geocity);
define('REMOTE_PROVINCE', $geoprovince);
define('REMOTE_COUNTRY', $geocountry);
define('REMOTE_TIMEZONE', $geotimezone);
define('REMOTE_POSTAL', $geopostal);
unset($geocity, $geoprovince, $geocountry, $geotimezone, $geopostal);
HookHandler::DispatchHook('/core/components/ready');
unset($profiler);
} // ENDING GLOBAL NAMESPACE
