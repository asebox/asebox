<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");
//	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	


$param_list=array(
	'ID'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	


        $cnt=0;
        $result=sybase_query(
        "select Ts=convert(varchar,timetable.Timestamp,3)+' '+convert(varchar,timetable.Timestamp,108),
         CmdsWritten_s
         from 
           (
             select Timestamp from ".$ServerName."_RSStats
                        where counter_id=18000
                          and Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
           ) timetable left outer join 
           (
             Select A.Timestamp,
             CmdsWritten_s = (sum(CmdsWritten)*1000.) / A.Interval
             from (
                 select S.Timestamp, Interval=avg(Interval),
             
                 CmdsWritten         = case when counter_id=6000 then sum(convert(numeric(14,0),counter_obs)) else null end
                 
                 from ".$ServerName."_Instances I, ".$ServerName."_RSStats S
                 where I.ID=S.ID
                 and S.ID = ".$ID."
                 and S.Timestamp >='".$StartTimestamp."'
                 and S.Timestamp <='".$EndTimestamp."'
                 and counter_id in (6000)
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
			$CmdsWritten_s[] = $row["CmdsWritten_s"];
			$cnt++;

		}

                if ($cnt == 0) {
                	$Timestamp[0]=$StartTimestamp;
                	$Timestamp[1]=$EndTimestamp;
                	$CmdsWritten_s[0]=0;
                	$CmdsWritten_s[1]=0;

                }
	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Write queue : Cmds Written / s");
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
		  		
		// Create the CmdsWritten plot
		$cmds_gr = new LinePlot($CmdsWritten_s);
		$cmds_gr ->SetLegend("CmdsWritten / s");
		$graph->Add($cmds_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
