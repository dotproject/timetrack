<?php
GLOBAL $AppUI, $timesheets, $company_id, $tg_data, $review, $f;
$df = $AppUI->getPref( 'SHDATEFORMAT' );
?>

<table width="100%" border="0" bgcolor="#f4efe3" cellpadding="3" cellspacing="1" class="tbl">
<tr>
	<<?php echo isset($review) ? 'th' : 'td'?> nowrap="nowrap">
	<?php echo isset($review) ? '<font color="white">Employee</font>' : '' ?>
	</<?php echo isset($review) ? 'th' : 'td'?>>
	<th nowrap>
		<font color="white">Dates Valid</font>
	</th>
	<th nowrap>
		<font color="white">PM Notes</font>
	</th>
</tr>

<?php
foreach ($timesheets as $row) {
	$start_date = new CDate( db_dateTime2unix( $row["tt_start_date"] ) );
	$end_date = new CDate( db_dateTime2unix( $row["tt_end_date"] ) );
	$start_date -> setFormat( $df );
	$end_date -> setFormat( $df );
?>
<tr>
	<td nowrap><?php 
	if(isset($review)) { 
		echo $row['user_last_name'] . ", " . $row['user_first_name']; 
	} ?>
	</td>
	<td nowrap>
		<A href="./index.php?m=timetrack&a=<?php echo $review ? "review" : "view" ?>&timesheet_id=<?php echo $row["tt_id"];?>&f=<?php echo $f?>">
		<?php 
			echo $start_date->toString().'-'.$end_date->toString();
		?>
		</a>
	</td>
	<td nowrap width="100%">
		<?php 
		// get the PM note for a timesheet.
		if(isset($row["tt_approve_note"])) {
		    echo $row["tt_approve_note_date"]  . ' - ' . $row["tt_approve_note"];
		}
		else {
			print "none";
		}
		?>
	</td>
</tr>
<?php }?>
<tr>
	<td colspan=6>&nbsp;</td>
</tr>
</table>
