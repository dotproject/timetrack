<?php /* $Id: timetrack.class.php,v 1.1 2003/01/21 05:43:02 eddieajau Exp $ */
##
## TimeTrack Classes
##

class CTimeSheet {
	var $tt_id = NULL;
	var $tt_user_id = NULL;
	var $tt_week = NULL;
	var $tt_active = NULL;
	var $tt_note_id = NULL;
	var $tt_year = NULL;
	var $tt_submitted = NULL;
	var $tt_start_date = NULL;
	var $tt_end_date = NULL;
	var $tt_supervisor_approval = NULL;
	var $tt_pm_approval = NULL;
	var $tt_approve_note = NULL;
	var $tt_approve_note_date = NULL;

	function CTimeSheet() {
		// empty constructor
	}

	function load( $oid ) {
		$sql = "SELECT * FROM timetrack_idx WHERE tt_id = $oid";
		return db_loadObject( $sql, $this );
	}

	function bind( $hash ) {
		if (!is_array( $hash )) {
			return get_class( $this )."::bind failed";
		} else {
			bindHashToObject( $hash, $this );
			return NULL;
		}
	}

	function check() {
		if ($this->tt_id === NULL) {
			return 'timsheet id is NULL';
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed";
		}
		if( $this->tt_id ) {
			$ret = db_updateObject( 'timetrack_idx', $this, 'tt_id' );
		} else {
			$ret = db_insertObject( 'timetrack_idx', $this, 'tt_id' );
		}
		if( !$ret ) {
			return get_class( $this )."::store failed <br>" . db_error();
		} else {
			return NULL;
		}
	}
	function delete() {
		$sql = "SELECT tt_data_timesheet_id FROM timetrack_data WHERE tt_data_timesheet_id = $this->tt_id";

		$res = db_exec( $sql );
		if (db_num_rows( $res )) {
			return "You cannot delete a timesheet that has entries associated with it.";
		} else{
			$sql = "DELETE FROM timetrack_idx WHERE tt_id = $this->tt_id";
			if (!db_exec( $sql )) {
				return db_error();
			} else {
				return NULL;
			}
		}
	}
}

class CTimeData {
	var $tt_data_id = NULL;
	var $tt_data_timesheet_id = NULL;
	var $tt_data_date = NULL;
	var $tt_data_client_id = NULL;
	var $tt_data_project_id = NULL;
	var $tt_data_task_id = NULL;
	var $tt_data_description = NULL;
	var $tt_data_hours = NULL;
	var $tt_data_change_date = NULL;
	var $tt_data_note = NULL;

	function CTimeData() {
		// empty constructor
	}

	function load( $oid ) {
		$sql = "SELECT * FROM timetrack_data WHERE tt_data_id = $oid";
		return db_loadObject( $sql, $this );
	}

	function bind( $hash ) {
		if (!is_array( $hash )) {
			return get_class( $this )."::bind failed";
		} else {
			bindHashToObject( $hash, $this );
			return NULL;
		}
	}

	function check() {
		if ($this->tt_data_id === NULL) {
			return 'tt_data id is NULL';
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store() {
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed";
		}
		if( $this->tt_data_id ) {
			$ret = db_updateObject( 'timetrack_data', $this, 'tt_data_id', false );
		} else {
			$ret = db_insertObject( 'timetrack_data', $this, 'tt_data_id' );
		}
		if( !$ret ) {
			return get_class( $this )."::store failed <br>" . db_error();
		} else {
			return NULL;
		}
	}
	function delete() {
		$sql = "DELETE FROM timetrack_data WHERE tt_data_id = $this->tt_data_id";
		if (!db_exec( $sql )) {
			return db_error();
		} else {
			return NULL;
		}
	}
}

?>
