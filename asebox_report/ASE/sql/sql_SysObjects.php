<?php

$query = "
select dbname, type, name, crdate
from ".$ServerName."_SysObjects
where type not in ('S')
and (dbname like '".$filterdbname."' or '".$filterdbname."'='')
and (type   like '".$filtertype  ."' or '".$filtertype  ."'='')
and (name   like '".$filtername  ."' or '".$filtername  ."'='')
and (crdate like '".$filtercrdate."' or '".$filtercrdate."'='')
order by ".$orderObj;

  $query_name = "sysobjects";

?>
