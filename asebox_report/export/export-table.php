<table  border="0" cellpadding="0" cellspacing="0">
	<tr>
    <INPUT type="HIDDEN" name="<?php echo $query_name; ?>" value="<?php echo $query;?>" >
<<<<<<< HEAD
		<td ><a href='<?php echo $HomeUrl; ?>export/export-to.php?t=1&QUERY=<?php echo urlencode($query); ?>&ARContextJSON=<?php echo $ARContextJSON; ?>'><img src="<?php echo $HomeUrl; ?>images/excel32.png" width="32" height="32" border="0" /></a></td>
=======
		<td ><a href='<?php echo $HomeUrl; ?>export/export-to.php?t=1&title=<?php echo urlencode($Title); ?>&QUERY=<?php echo urlencode($query); ?>&ARContextJSON=<?php echo $ARContextJSON; ?>'><img src="<?php echo $HomeUrl; ?>images/excel32.png" width="32" height="32" border="0" /></a></td>
>>>>>>> 3.1.0
		<td ><a href='javascript:getSql(800,500,<?php echo '"'.$HomeUrl.'"'; ?>,<?php echo '"'.$query_name.'"'; ?>)'><img src="<?php echo $HomeUrl; ?>images/sql32.png" width="32" height="32" border="0" /></a></td>
	</tr>
</table>