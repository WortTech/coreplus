<?php
/**
 * Upgrade file for Core 2.8.0 to 3.0.0 (Security 1.2.0 to 1.3.0)
 *
 * The security model has been migrated into Core as of 3.0, so this script
 * migrates the data from the security component into Core.
 * 
 * @author Charlie Powell <charlie@evalagency.com>
 * @date 20131229.2033
 */

// Handle conversion of the security entries to system log entries.
// These are now contained in that table.

$secfac = new ModelFactory('SecurityLogModel');
$stream = new \Core\Datamodel\DatasetStream($secfac->getDataset());
while($row = $stream->getRecord()){
	//$model = SystemLogModel::Construct($row['id']);
	$model = new SystemLogModel($row['id']);

	// Standard keys
	$model->setFromArray(
		[
			'datetime'         => $row['datetime'],
			'session_id'       => $row['session_id'],
			'user_id'          => $row['user_id'],
			'ip_addr'          => $row['ip_addr'],
			'useragent'        => $row['useragent'],
			'code'             => $row['action'],
			'affected_user_id' => $row['affected_user_id'],
			'message'          => $row['details'],
		]
	);

	$model->set('type', ($row['status'] == 'fail') ? 'security' : 'info');
	$model->save();
}
