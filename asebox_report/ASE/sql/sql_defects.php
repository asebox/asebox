<?php
    $query="";

if ($filterDefLevel = '') {
	filterDefLevelNUM=0
} else {
	filterDefLevelNUM=$filterDefLevel
}
    
    $filter_clause ="
and (dbname   like '".$filterdbname."'   or '".$filterdbname."'   = '')
and (objname  like '".$filterobjname."'  or '".$filterobjname."'  = '')
and (defect   like '".$filterdefect."'   or '".$filterdefect."'   = '')
and (DefLevel =".$filterDefLevelNUM." or ".$filterDefLevelNUM." = 0)
";

        
//$orderPrc=$order_by;
    

$query = $query . "
select distinct dbname, objname, defect
into   #defects
from ".$ServerName."_Defects

select 
     d.dbname
    ,d.objname
    ,d.defect
    ,r.DefLevel
    ,r.DefDesc
from ".$ServerName."_DefectsRef r, #defects d
where r.DefName = d.defect
".$filter_clause."
order by ".$orderPrc;


$query_name = "defects";
?>
