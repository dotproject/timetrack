<?php /* $Id$ */
// check permissions
$denyRead = getDenyRead( $m );
$denyEdit = getDenyEdit( $m );

if ($denyRead) {
	$AppUI->redirect( "m=help&a=access_denied" );
}
$AppUI->savePlace();

require "$root_dir/classdefs/date.php";

// tab stuff
if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'TtrackIdxTab', $_GET['tab'] );
}

$tab = $AppUI->getState( 'TtrackIdxTab' ) !== NULL ? $AppUI->getState( 'TtrackIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'TtrackIdxTab' ) );

// cannot review active timesheets as manager
if ($tab == '0' && $AppUI->getState( 'TtrackIdxFilter' ) != 'my') {
	$AppUI->setState( 'TtrackIdxFilter', 'my' );
	$f = 'my';
}

// get date format
$df = $AppUI->getPref('SHDATEFORMAT');

// default SQL
$sql = "SELECT * FROM timetrack_idx WHERE tt_user_id = $AppUI->user_id AND tt_active = $active ORDER BY tt_end_date";

// get any display filters

if ((isset($f)) && ($AppUI->user_type < 7) ) { // keep people from playing around...
	switch ($f) {
		case 'project':
			$sql = "
			SELECT DISTINCT tt_id, user_last_name,user_first_name,tt_start_date,tt_end_date,tt_note_id
			FROM timetrack_data 
			LEFT JOIN timetrack_idx ON tt_id = tt_data_timesheet_id
			LEFT JOIN projects ON project_id = tt_data_project_id 
			LEFT JOIN users ON user_id = tt_user_id
			WHERE tt_active = '0' 
			AND project_owner = $AppUI->user_id 
			ORDER BY tt_end_date";
			$tab = "1";
			$review = "1";
			break;
		case 'employee':
			$sql = "
			SELECT DISTINCT tt_id, user_last_name,user_first_name,tt_start_date,tt_end_date,tt_note_id 
			FROM timetrack_idx 
			LEFT JOIN users ON user_id = tt_user_id 
			WHERE tt_active = '0' 
			ORDER BY tt_end_date";
			$tab = "1";
			$review = "1";
			break;
		default: // my
			$sql = "SELECT * FROM timetrack_idx WHERE tt_user_id = $AppUI->user_id AND tt_active = $active ORDER BY tt_end_date";
			break;
	}
	$AppUI->setState( 'TtrackIdxFilter', $f );
}

$f = $AppUI->getState( 'TtrackIdxFilter' ) !== NULL ? $AppUI->getState( 'TtrackIdxFilter' ) : 'my';

// get timesheets 

$timesheets =  db_loadList( $sql );

// grab last timesheet date
$tsld_sql = "SELECT MAX(tt_end_date) AS end_date FROM timetrack_idx WHERE tt_user_id = $AppUI->user_id";
db_loadHash( $tsld_sql, $tsld_data );

if ($tsld_data['end_date']) {
	$last_sheet_end = db_dateTime2unix( $tsld_data["end_date"] );
} else {
	$time_set = new CDate ();
	$today_weekday = $time_set -> getWeekday();
	
	$rollover_day = '0';
	$new_start_offset = $rollover_day - $today_weekday;
	
	$time_set -> addDays($new_start_offset);
	
	$last_sheet_end = $time_set -> getTimestamp();
}	
	


$filters = array(
	'my' => 'My TimeSheets',
	'project' => 'Project TimeSheets',
	'employee' => 'Employee TimeSheets',
);
?>


<table width="98%" border="0" cellpadding="0" cellspacing="1">
<tr>
	<td><img src="./images/icons/projects.gif" alt="" border="0" width=42 height=42></td>
	<td nowrap><span class="title"><?PHP echo $AppUI->getState( 'TtrackIdxFilter' ) !== NULL ? $filters[$AppUI->getState( 'TtrackIdxFilter' )] : 'My TimeSheets' . ' Overview'?></span></td>
	<td align="right" width="100%">
	<?php 
	if ($AppUI->user_type < 7) { ?>
		<form action="<?PHP echo $_SERVER['REQUEST_URI'];?>" method="post" name="pickTimeSheet">
		<?php echo arraySelect( $filters, 'f', 'onChange="document.pickTimeSheet.submit()" class="text"', $f ); ?>
		</form>
	<?php } ?>
	</td>
	<td nowrap="nowrap" width="20" align="right"><?php echo contextHelp( '<img src="./images/obj/help.gif" width="14" height="16" border="0" alt="'.$AppUI->_( 'Help' ).'">' );?></td>
</tr>
</table>


<?php	
// echo "tab setting = [$tab]";
// tabbed information boxes
$tabBox = new CTabBox( "?m=timetrack", "$root_dir/modules/timetrack/", $tab );
$tabBox->add( 'vw_idx_active', 'Active TimeSheets' );
$tabBox->add( 'vw_idx_archived', 'Submitted TimeSheets' );
$tabBox->show();

?>
