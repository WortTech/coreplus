<?php
/**
 * Class file for the controller CronController
 *
 * @package Cron
 */
class CronController extends Controller_2_1 {
	// Each controller can have many views, each defined by a different method.
	// These methods should be regular public functions that DO NOT begin with an underscore (_).
	// Any method that begins with an underscore or is static will be assumed as an internal method
	// and cannot be called externally via a url.

	/**
	 * Execute the hourly cron
	 */
	public function hourly(){
		$request = $this->getPageRequest();
		$view    = $this->getView();
		$view->mode = View::MODE_NOOUTPUT;

		// Check and see if I need to run this cron already, ie: don't run an hourly log twice in the same hour.
		$last = CronLogModel::Find(array('cron' => 'hourly'), 1, 'created DESC');
		if($last && (Time::GetCurrentGMT() - $last->get('created') < 3540) ){
			// No run needed, already ran within the past 59 minutes.

			// Report this.
			$logmsg = "Not re-running, already executed hourly cron " . floor((Time::GetCurrentGMT() - $last->get('created')) / 60) . ' minutes ago';

			SystemLogModel::LogInfoEvent('/cron/hourly', $logmsg);
			return;
		}

		$log = $this->_performcron('hourly');
		if($log->get('status') != 'pass'){
			echo 'failed, check the logs';
		}
	}

	/**
	 * Execute the daily cron
	 */
	public function daily(){
		$request = $this->getPageRequest();
		$view    = $this->getView();
		$view->mode = View::MODE_NOOUTPUT;

		// Check and see if I need to run this cron already, ie: don't run an hourly log twice in the same hour.
		$last = CronLogModel::Find(array('cron' => 'daily'), 1, 'created DESC');
		if($last && (Time::GetCurrentGMT('Ymd') == Time::FormatGMT($last->get('created'), Time::TIMEZONE_GMT, 'Ymd')) ){
			// No run needed, already ran today.

			// Report this.
			$timelast = floor((Time::GetCurrentGMT() - $last->get('created')) / 60);
			if($timelast >= 120) $timelast = floor((Time::GetCurrentGMT() - $last->get('created')) / 3600) . ' hours';
			else $timelast .= ' minutes';

			$logmsg = "Not re-running, already executed daily cron " . $timelast . ' ago';
			SystemLogModel::LogInfoEvent('/cron/daily', $logmsg);
			return;
		}

		$log = $this->_performcron('daily');
		if($log->get('status') != 'pass'){
			echo 'failed, check the logs';
		}
	}

	/**
	 * Execute the weekly cron
	 */
	public function weekly(){
		$request = $this->getPageRequest();
		$view    = $this->getView();
		$view->mode = View::MODE_NOOUTPUT;

		// Check and see if I need to run this cron already, ie: don't run an hourly log twice in the same hour.
		$last = CronLogModel::Find(array('cron' => 'weekly'), 1, 'created DESC');
		// 3600 seconds in an hour * 24 hours in a day * 7 days in a week - 10 seconds.
		if($last && (Time::GetCurrentGMT() - $last->get('created') < (3600 * 24 * 7 - 10) ) ){
			// No run needed, already ran recently.

			// Report this.
			$timelast = floor((Time::GetCurrentGMT() - $last->get('created')) / 60);
			if($timelast >= (3600*48)) $timelast = floor((Time::GetCurrentGMT() - $last->get('created')) / 3600*24) . ' days';
			elseif($timelast >= 120) $timelast = floor((Time::GetCurrentGMT() - $last->get('created')) / 3600) . ' hours';
			else $timelast .= ' minutes';

			$logmsg = "Not re-running, already executed weekly cron " . $timelast . ' ago';
			SystemLogModel::LogInfoEvent('/cron/weekly', $logmsg);
			return;
		}

		$log = $this->_performcron('weekly');
		if($log->get('status') != 'pass'){
			echo 'failed, check the logs';
		}
	}

	/**
	 * Execute the monthly cron
	 */
	public function monthly(){
		$request = $this->getPageRequest();
		$view    = $this->getView();
		$view->mode = View::MODE_NOOUTPUT;

		// Check and see if I need to run this cron already, ie: don't run an hourly log twice in the same hour.
		$last = CronLogModel::Find(array('cron' => 'monthly'), 1, 'created DESC');
		if($last && (Time::GetCurrentGMT('Ym') == Time::FormatGMT($last->get('created'), Time::TIMEZONE_GMT, 'Ym')) ){
			// No run needed, already ran this month.

			// Report this.
			$timelast = floor((Time::GetCurrentGMT() - $last->get('created')) / 60);
			if($timelast >= (3600*48)) $timelast = floor((Time::GetCurrentGMT() - $last->get('created')) / 3600*24) . ' days';
			elseif($timelast >= 120) $timelast = floor((Time::GetCurrentGMT() - $last->get('created')) / 3600) . ' hours';
			else $timelast .= ' minutes';

			$logmsg = "Not re-running, already executed monthly cron " . $timelast . ' ago';
			SystemLogModel::LogInfoEvent('/cron/monthly', $logmsg);
			return;
		}

		$log = $this->_performcron('monthly');
		if($log->get('status') != 'pass'){
			echo 'failed, check the logs';
		}
	}


	/**
	 * Display a list of cron jobs that have ran.
	 * @return int
	 */
	public function admin(){
		$view = $this->getView();
		$request = $this->getPageRequest();

		if(!\Core\user()->checkAccess('p:/cron/viewlog')){
			return View::ERROR_ACCESSDENIED;
		}

		$filters = new FilterForm();
		$filters->setName('cron-admin');
		$filters->hassort = true;
		$filters->haspagination = true;
		$filters->addElement(
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
		);
		$filters->setSortkeys(array('cron', 'created', 'duration', 'status'));
		$filters->load($request);


		$cronfac = new ModelFactory('CronLogModel');
		$filters->applyToFactory($cronfac);
		$listings = $cronfac->get();

		$view->mastertemplate = 'admin';
		$view->title = 't:STRING_CRON_RESULTS';
		$view->assign('filters', $filters);
		$view->assign('listings', $listings);
		$view->assign('sortkey', $filters->getSortKey());
		$view->assign('sortdir', $filters->getSortDirection());

		//var_dump($listings); die();
	}

	/**
	 * View a specific cron execution and its details.
	 */
	public function view(){
		$view = $this->getView();
		$request = $this->getPageRequest();
		$view->mode = View::MODE_PAGEORAJAX;

		if(!\Core\user()->checkAccess('p:/cron/viewlog')){
			return View::ERROR_ACCESSDENIED;
		}

		$logid = $request->getParameter(0);
		$log = CronLogModel::Construct($logid);
		if(!$log->exists()){
			return View::ERROR_NOTFOUND;
		}

		$view->mastertemplate = 'admin';
		$view->addBreadcrumb('t:STRING_CRON_RESULTS', '/cron/admin');
		$view->title = 'Log Execution Details';
		$view->assign('entry', $log);
	}

	/**
	 * Provide the admins with a general "howto" page for setting up cron jobs.
	 *
	 * This is linked to from the "your cron hasn't run recently" error.
	 */
	public function howto(){
		$view = $this->getView();
		$request = $this->getPageRequest();
		$view->mode = View::MODE_PAGEORAJAX;

		if(!\Core\user()->checkAccess('p:/cron/viewlog')){
			return View::ERROR_ACCESSDENIED;
		}

		$view->mastertemplate = 'admin';
		$view->title = 'Cron Howto';
		$view->assign('url', ROOT_URL_NOSSL);
		$view->assign('sitename', SITENAME);
	}


	/**
	 * Execute the actual cron for the requested type.
	 *
	 * @param string $cron Cron type to execute.
	 *
	 * @return CronLogModel
	 * @throws Exception
	 */
	private function _performcron($cron){
		switch($cron){
			case 'hourly':
			case 'daily':
			case 'weekly':
			case 'monthly':
				break;
			default:
				throw new Exception('Unsupported cron type: [' . $cron . ']');
		}

		if(!ConfigHandler::Get('/cron/enabled')){
			$msg = 'Cron execution is globally disabled via the site configuration, not executing cron!';
			SystemLogModel::LogInfoEvent('/cron/' . $cron, $msg);

			// It needs to return something.
			$log = new CronLogModel();
			$log->set('status', 'fail');
			return $log;
		}

		// First, check and see if there's one that's still running.
		$runninglogs = CronLogModel::Find(array('cron' => $cron, 'status' => 'running'));
		if(sizeof($runninglogs)){
			foreach($runninglogs as $log){
				/** @var $log CronLogModel */
				$log->set('status', 'fail');
				$log->set('log', $log->get('log') . "\n------------\nTIMED OUT!");
				$log->save();
			}
		}



		// Start recording.
		$log = new CronLogModel();
		$log->set('cron', $cron);
		$log->set('status', 'running');
		$log->set('memory', memory_get_usage());
		$log->set('ip', REMOTE_IP);
		$log->save();
		$start = microtime(true) * 1000;

		$sep = '==========================================' . "\n";
		$contents = "Starting cron execution for $cron\n$sep";

		// This uses the hook system, but will be slightly different than most things.
		$overallresult = true;
		$hook = HookHandler::GetHook('/cron/' . $cron);
		$hookcount = 0;
		$hooksuccesses = 0;

		if($hook){
			if($hook->getBindingCount()){
				$hookcount = $hook->getBindingCount();
				$bindings = $hook->getBindings();
				foreach($bindings as $b){
					$contents .= sprintf(
						"\nExecuting Binding %s...\n",
						$b['call']
					);
					// Since these systems will just be writing to STDOUT, I'll need to capture that.
					try{
						ob_start();
						$execution = $hook->callBinding($b, array());
						$executiondata = ob_get_clean();
					}
					catch(Exception $e){
						$execution     = false;
						$executiondata = $e->getMessage();
					}


					if($executiondata == '' && $execution){
						$contents .= "Cron executed successfully with no output\n";
						++$hooksuccesses;
					}
					elseif($execution){
						$contents .= $executiondata . "\n";
						++$hooksuccesses;
					}
					else{
						$contents .= $executiondata . "\n!!FAILED\n";
						$overallresult = false;
					}
				}
			}
			else{
				$contents = 'No bindings located for requested cron';
				$overallresult = true;
			}
		}
		else{
			$contents = 'Invalid hook requested: ' . $cron;
			$overallresult = false;
		}


		// Just in case the contents are returning html... (they should be plain text).
		$contents = str_replace(array('<br>', '<br/>', '<br />'), "\n", $contents);
		// And standardize line endings.
		$contents = str_replace(array("\r\n", "\r"), "\n", $contents);

		// Save the results.
		$log->set('completed', Time::GetCurrentGMT());
		$log->set('duration', ( (microtime(true) * 1000) - $start ) );
		$log->set('log', $contents);
		$log->set('status', ($overallresult ? 'pass' : 'fail') );
		$log->save();

		// Make a copy of this in the system log too if applicable.
		// This time is listed in ms
		$time = ( (microtime(true) * 1000) - $start );
		// 0.01 = 10 ns
		// 1    = 1 ms
		// 1000 = 1 second
		if($time < 1){
			// TIME is less than 1, which means it executed faster than 1ms, display in nanoseconds.
			$time = (round($time, 4) * 1000) . ' ns';
		}
		elseif($time < 1000){
			// TIME is less than 1000, which means it executed faster than 1 second, display in milliseconds.
			$time = round($time, 0) . ' ms';
		}
		else{
			// TIME is at least 1 second or longer... Display in minutes:seconds, (no need to display 1.453 seconds!)
			// First, convert the milliseconds to seconds; they are more manageable for what I need to do.
			// This will change time from 12345(ms) to 13(seconds)
			$time = ceil($time / 1000);
			$minutes = floor($time / 60);
			$seconds = $time - ($minutes * 60);
			if($minutes > 0){
				$time = $minutes . 'm ' . str_pad($seconds, 2, '0', STR_PAD_LEFT) . 's';
			}
			else{
				$time = $seconds . ' seconds';
			}
		}

		if($hookcount > 0){
			$msg = 'Cron ' . $cron . ' completed in ' . $time . '.  ' . $hooksuccesses . ' out of ' . $hookcount . ' hooks called successfully.';
			SystemLogModel::LogInfoEvent('/cron/' . $cron, $msg, $contents);
		}

		// Just to notify the calling function.
		return $log;
	}
}