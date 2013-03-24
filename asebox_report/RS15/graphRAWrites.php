<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");
// include ("../".$jpgraph_home."/src/jpgraph_bar.php"); 


  $param_list=array(
   'instance_id'
  );
  foreach ($param_list as $param)
  @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
 


  $cnt=0;
  $result=sybase_query(
  "select Ts=convert(varchar,timetable.Timestamp,3)+' '+convert(varchar,timetable.Timestamp,108),
   AvgRAWriteWaitsTime_ms = isnull(AvgRAWriteWaitsTime_ms,0)
   from 
     (
       select Timestamp from ".$ServerName."_RSStats
                  where counter_id=18000
                    and Timestamp >='".$StartTimestamp."'
                    and Timestamp <='".$EndTimestamp."'
     ) timetable left outer join 
     (
       Select A.Timestamp,
       AvgRAWriteWaitsTime_ms = avg(AvgRAWriteWaitsTime_ms)
       from (
           select S.Timestamp, Interval=avg(Interval),
       
           AvgRAWriteWaitsTime_ms         = case when counter_id=58019 then sum(convert(numeric(14,0),avg_ttl_obs)) else null end
           
           from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
           where I.ID=S.ID
           and instance_id = ".$instance_id."
           and S.Timestamp >='".$StartTimestamp."'
           and S.Timestamp <='".$EndTimestamp."'
           and counter_id in (58019)
           group by S.Timestamp, counter_id
           ) A
       group by A.Timestamp, A.Interval
       ) data
   on timetable.Timestamp=data.Timestamp
   order by timetable.Timestamp",
  $pid);

        while (($row=sybase_fetch_array($result)))
        {
         $Timestamp[]= $row["Ts"];
         $AvgRAWriteWaitsTime_ms[] = $row["AvgRAWriteWaitsTime_ms"];
         $cnt++;
        
        }
        if ($cnt == 0) {
            $Timestamp[0]=$StartTimestamp;
            $Timestamp[1]=$EndTimestamp;
            $AvgRAWriteWaitsTime_ms[0]=0;
            $AvgRAWriteWaitsTime_ms[1]=0;
        }
 
  
        // New graph with a background image and drop shadow
        $graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


        // Set title and subtitle
        $graph->title->Set("Rep Agent write wait time AVG (ms)");
        $graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);
        
        // Use built in font
        $graph->title->SetFont(FF_FONT1,FS_BOLD);
        
        // Make the margin around the plot a little bit bigger
        // then default
        $graph->img->SetMargin(95,20,40,100); 
        
        
        // Display every 10:th datalabel
        $graph->xaxis->SetTickLabels($Timestamp);
        $nbVal=sizeof($Timestamp);
        if ( $nbVal>20 )
          $graph->xaxis->SetTextTickInterval($nbVal/20);
        else
          $graph->xaxis->SetTextTickInterval(1);
            
        // Create the AvgRAWriteWaitsTime_ms plot
        $writeWaits_gr = new LinePlot($AvgRAWriteWaitsTime_ms);
        $writeWaits_gr ->SetLegend("AvgRAWriteWaitsTime_ms");
        $graph->Add($writeWaits_gr);
        
        $graph -> legend  -> SetLayout(LEGEND_HOR);
        $graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

  
        $graph->graph_theme=null; // This fix bottom margin bad computation
        // Finally output the  image
        $graph->Stroke();

 ?>
