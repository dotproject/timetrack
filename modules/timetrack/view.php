
<!-- ################################## [ view section ] ################################ -->

<?php 
$timesheet_id = isset($_GET['timesheet_id']) ? $_GET['timesheet_id'] : 0; 
 
// check permissions 
$denyRead = getDenyRead( $m, $timesheet_id ); 
$denyEdit = getDenyEdit( $m, $timesheet_id ); 
 
if ($denyRead) { 
	$AppUI->redirect( "m=help&a=access_denied" );
}
$AppUI->savePlace();

require_once $AppUI->getSystemClass('date');
$df = $AppUI->getPref( 'SHDATEFORMAT' );

// get timesheet globals
$sql = "
SELECT tt_start_date, tt_end_date, tt_id, tt_active,
	COUNT( tt_data_id ) AS data_count,
	SUM( tt_data_hours ) AS hour_count,
	COUNT( DISTINCT( DAYOFYEAR( tt_data_date ) ) ) AS day_count
FROM timetrack_idx
LEFT JOIN timetrack_data ON tt_data_timesheet_id = tt_id
WHERE tt_id = $timesheet_id
GROUP BY tt_id
";
db_loadHash( $sql, $tg_data );

//pull data for this timesheet
$psql = " 
SELECT timetrack_data.*, DATE_FORMAT(tt_data_date,'%m/%d/%Y') AS tid_date,
	company_name, project_name, task_name
FROM timetrack_data
LEFT JOIN companies ON company_id = tt_data_client_id
LEFT JOIN projects ON project_id = tt_data_project_id
LEFT JOIN tasks ON task_id = tt_data_task_id
WHERE tt_data_timesheet_id = $timesheet_id
ORDER BY tt_data_date
"; 
$tt_data = db_loadList ( $psql ); 

$start_date = new CDate( db_dateTime2unix( $tg_data["tt_start_date"] ) );
$end_date = new CDate( db_dateTime2unix( $tg_data["tt_end_date"] ) );

//echo "[" . $tg_data["tt_start_date"]. "|" . $tg_data["tt_end_date"] . "]<BR>";

$crumbs = array();
$crumbs["?m=timetrack&timesheet_id=$timesheet_id"] = "timesheets list";
#################################### [ view section UI code ] ########################
?>
<script language="JavaScript" type="text/javascript">
function sendIt() {
	if (confirm( "Is all the information and time on this timesheet correct and accurate?\n" )) {
		var form = document.TIDedit;
		form.submit();
	} 
}
</script>
<table width="98%" border="0" cellpadding="0" cellspacing="2"> 
<tr> 
	<td><img src="./images/icons/projects.gif" alt="" border="0" width=42 height=42></td> 
	<td nowrap>
		<span class="title"><?php echo $AppUI->_( 'View My Timesheet' );?></span>
	</td> 
	<td align="right" width="100%"></td> 
	<td nowrap="nowrap" width="20" align="right"><?php echo contextHelp( '<img src="./images/obj/help.gif" width="14" height="16" border="0" alt="'.$AppUI->_( 'Help' ).'">' );?></td> 
</tr> 
</table> 

<table border="0" cellpadding="4" cellspacing="0" width="98%">
<tr>
	<td width="50%" nowrap><?php echo breadCrumbs( $crumbs );?></td>
</tr>
</table>

<table border="0" cellpadding="4" cellspacing="0" width="98%" class="std">
<tr>
	<td width="50%" valign="top">
		<table cellspacing="1" cellpadding="2" border="0" width="50%" align="right">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Timesheet');?>:</td>
			<td class="hilite" width="100%"><?php echo $timesheet_id;?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Start Date');?>:</td>
			<td class="hilite" width="100%"><?php echo $start_date->format($df);?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('End Date');?>:</td>
			<td class="hilite" width="100%"><?php echo $end_date->format($df);?></td>
		</tr>
		</table>
	</td>
	<td width="50%" valign="top">
		<table cellspacing="1" cellpadding="2" border="0" width="50%">
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Number of Entries');?>:</td>
			<td class="hilite" align="right" width="100%"><?php echo $tg_data['data_count'];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Days Entered');?>:</td>
			<td class="hilite" align="right" width="100%"><?php echo $tg_data['day_count'];?></td>
		</tr>
		<tr>
			<td align="right" nowrap><?php echo $AppUI->_('Total Hours');?>:</td>
			<td class="hilite" align="right" width="100%"><?php echo printf( "%.2f", $tg_data['hour_count'] );?></td>
		</tr>
		</table>
	</td>
</tr>
</table>

<form name="TIDedit" action="./index.php?m=timetrack&a=dosql_timesheet" method="post"> 
<input name="sendin" value="1" type="hidden">
<input name="timesheet_id" value="<?php echo $timesheet_id ?>" type="hidden">
<table width="98%" border="0" bgcolor="#f4efe3" cellpadding="3" cellspacing="1" class="tbl"> 
<tr> 
	<td align="right" nowrap="nowrap">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> 
	<th nowrap="nowrap"> Client</th> 
	<th nowrap="nowrap"> Project</th> 
	<th nowrap="nowrap"> Task</th> 
	<th nowrap="nowrap"> Work Description</th> 
	<th nowrap="nowrap"> Hours</th> 
</tr> 
 
<?php
$temp = new CDate( 0 );
$day = new CDate();
foreach ($tt_data as $row) {
	$day->setTime( db_dateTime2unix( $row['tt_data_date'] ) );
	$day->setTime( 0,0,0 );
	if ($day->compareTo( $temp )) {
		$temp = $day;
		echo '<tr><td colspan="6"><table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#DFDFDF"><tr><td bgcolor="#DFDFDF"><B>'.$day->format($df).'</B></td></tr></table></td></tr>';
	}
?> 
<tr> 
	<td align="center"> 
	<? if ($tg_data['tt_active'] > 0 && !$denyEdit ) { ?>
		<a href="?m=timetrack&a=addedit&timesheet_id=<?php echo $timesheet_id ?>&tid=<?php echo $row["tt_data_id"] ?>"><img src="./images/icons/pencil.gif" alt="<?php echo $AppUI->_('Edit');?>" border="0" width="12" height="12"></a>
	<?php } else {
		echo $row["tt_data_id"];
	}?>
	</td> 
	<td nowrap><?php echo @$row["company_name"]?></td> 
	<td nowrap><?php echo @$row["project_name"]?></td> 
	<td nowrap><?php echo @$row["task_name"]?></td> 
	<td width="100%" nowrap> 
		<?php echo $row["tt_data_description"]?>
    </td> 
	<td nowrap> 
		<?php echo $row["tt_data_hours"]?>
    </td> 
</tr> 
<?php 
}
if ($tg_data['tt_active'] > 0 ) { 
?>
<tr> 
	<td colspan="8" align="right">
	<a href="index.php?m=timetrack&a=addedit&timesheet_id=<?php echo $timesheet_id;?>&addrow=1&tid=0">Add New Row</a>
	</td>
</tr> 
<?php 
}
if ($tg_data['tt_active'] > 0 ) { 
?>
<tr bgcolor="#FFFFFF">
	<td colspan="8" bgcolor="#FFFFFF" align="right">
	<input class="button" type="Button" value="Send In" onClick="sendIt();">
	</td>
</tr>
<?php } ?>
</table> 
</form> 

 
<!-- ######################################## [ end view section ] ############################ -->
