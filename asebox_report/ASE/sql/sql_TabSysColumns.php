<?php

if ($filterdbname . $filtertabobjname . $filtercolname != '') {
	
$query = "select Colname,Type,Length,Prec,Scale,Nulls,Default_name,Rule_name,Access_Rule,Computed_Col,Ident,Colid
from ".$ServerName."_SysColumns
where 1=1";

if ($filterdbname != '')
  $query = $query . " and _dbname  = '".$filterdbname."'";
if ($filterobjname != '')
  $query = $query . " and _objname  = '".$filtertabobjname."'";
if ($filtercolname != '')
  $query = $query . " and _colname  = '".$filtercolname."'";

$query = $query . " order by ".$orderCol;

} else {	

	$query = "select 'none' where 1=0";
	
}


$query_name = "tabsysobjects";

?>


