<?php

        $query="";
        if ($ArchSrvType=="Adaptive Server Enterprise") {
                $query = "set forceplan on ";
        }

        $filter_clause ="
        and (comment     like '%".$filtername."%'     or '".$filtername."'     = '')";

        $query = $query . "
        select
          Timestamp=convert(varchar,Timestamp,100),
          Configname=comment,
          Value=value
        from ".$ServerName."_SysConf,
             (select maxTimestamp=max(SL.Timestamp) from ".$ServerName."_SysConf SL
               where Timestamp <'".$EndTimestamp."') dt
        where datediff(hh,Timestamp,dt.maxTimestamp) = 0
          and parent <> 0
          and parent <> 1 -- Parent
          and parent <> 10
          and parent <> 19 -- Cache Manager
          and Timestamp <'".$EndTimestamp."'
          ".$filter_clause."
        --  group by Timestamp, comment, value
        -- having Timestamp=max(Timestamp)
        order by ".$orderPrc;

        if ($ArchSrvType=="Adaptive Server Enterprise") {
                $query = $query . "
          set forceplan off";
        }

  $query_name = "sysconf_list";

?>
