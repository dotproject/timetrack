<?php /* $Id: setup.php,v 1.2 2003/01/24 01:08:35 eddieajau Exp $ */
/*
dotProject Module

Name:      TimeTrack
Directory: timetrack
Version:   0.3
Class:     user
UI Name:   Timetrack
UI Icon:

This file does no action in itself.
If it is accessed directory it will give a summary of the module parameters.
*/

// MODULE CONFIGURATION DEFINITION
$config = array();
$config['mod_name'] = 'TimeTrack';
$config['mod_version'] = '0.3';
$config['mod_directory'] = 'timetrack';
$config['mod_setup_class'] = 'CSetupTimeTrack';
$config['mod_type'] = 'user';
$config['mod_ui_name'] = 'TimeTrack';
$config['mod_ui_icon'] = '';
$config['mod_description'] = 'This is a Time Tracking module';

if (@$a == 'setup') {
	echo dPshowModuleConfig( $config );
}

/*
// MODULE SETUP CLASS
	This class must contain the following methods:
	install - creates the required db tables
	remove - drop the appropriate db tables
	upgrade - upgrades tables from previous versions
*/
class CSetupTimeTrack {
/*
	Install routine
*/
	function install() {
		$sql = "
			CREATE TABLE timetrack_data (
			  tt_data_id int(11) NOT NULL auto_increment,
			  tt_data_timesheet_id int(11) unsigned NOT NULL default '0',
			  tt_data_date datetime NOT NULL default '0000-00-00 00:00:00',
			  tt_data_client_id int(6) unsigned default NULL,
			  tt_data_project_id int(11) unsigned default NULL,
			  tt_data_task_id int(11) unsigned default NULL,
			  tt_data_description varchar(44) NOT NULL default '',
			  tt_data_hours float NOT NULL default '0',
			  tt_data_change_date timestamp(14) NOT NULL,
			  tt_data_note varchar(255) default NULL,
			  PRIMARY KEY  (tt_data_id)
			) TYPE=MyISAM
		";
		db_exec( $sql );

		$sql = "
	CREATE TABLE timetrack_idx (
	  tt_id int(11) NOT NULL auto_increment,
	  tt_user_id int(11) NOT NULL default '0',
	  tt_week int(2) NOT NULL default '0',
	  tt_active tinyint(4) default NULL,
	  tt_note_id int(11) default NULL,
	  tt_year int(4) NOT NULL default '0',
	  tt_submitted date default NULL,
	  tt_start_date datetime default NULL,
	  tt_end_date datetime default NULL,
	  tt_supervisor_approval date default NULL,
	  tt_pm_approval date default NULL,
	  tt_approve_note varchar(80) default NULL,
	  tt_approve_note_date date NOT NULL default '0000-00-00',
	  PRIMARY KEY  (tt_id)
	) TYPE=MyISAM
		";
		db_exec( $sql );
		return null;
	}
/*
	Removal routine
*/
	function remove() {
		$sql = "DROP TABLE timetrack_data";
		db_exec( $sql );

		$sql = "DROP TABLE timetrack_idx";
		db_exec( $sql );

		return null;
	}
/*
	Upgrade routine
*/
	function upgrade() {
		return null;
	}
}

?>