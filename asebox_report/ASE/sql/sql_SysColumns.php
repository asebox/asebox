<?php

//if (($filterdbname != '') and ($filterobjname != '') and ($filtercolname = '')) {
if ($filterdbname . $filterobjname . $filtercolname != '') {
	
$query = "select dbname,Objname,Colname,Type,Length,Prec,Scale,Nulls,Default_name,Rule_name,Access_Rule,Computed_Col,Ident,Colid
from ".$ServerName."_SysColumns
where 1=1";

if ($filterdbname != '')
  $query = $query . " and _dbname  = '".$filterdbname."'";
if ($filterobjname != '')
  $query = $query . " and _objname  = '".$filterobjname."'";
if ($filtercolname != '')
  $query = $query . " and _colname  = '".$filtercolname."'";

$query = $query . " order by ".$orderCol;

} else {	

	$query = "select 'none' where 1=0";
	
}


$query_name = "sysobjects";

?>


