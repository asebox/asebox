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
        "select Ts=convert(varchar,T.Timestamp,3)+' '+convert(varchar,T.Timestamp,108),
         avgCmdSize=isnull(avgCmdSize,0)
         from 
         (select Timestamp from ".$ServerName."_RSStats 
           where counter_id=18000
            and Timestamp >='".$StartTimestamp."'
            and Timestamp <='".$EndTimestamp."'
         ) T 
         left outer join 
         (
          select Timestamp,
          avgCmdSize=case when sum(CmdsWritten) =0 then 0 else sum(BytesWritten)/sum(CmdsWritten) end
          from
          (
              select
              S.Timestamp,
              CmdsWritten = isnull(case when counter_id=6000 then counter_obs else null end, 0),
              BytesWritten = isnull(case when counter_id=6004 then counter_total else null end, 0)
              from ".$ServerName."_RSStats S, ".$ServerName."_Instances I 
              where S.ID=I.ID
              and S.Timestamp >='".$StartTimestamp."'
              and S.Timestamp <='".$EndTimestamp."'
              and S.ID=".$ID."
              and counter_id in (6000, 6004)
          ) A
          group by Timestamp
         ) B
         on T.Timestamp=B.Timestamp
         order by T.Timestamp",
        $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$avgCmdSize[] = $row["avgCmdSize"];
			$cnt++;

		}

                if ($cnt == 0) {
                	$Timestamp[0]=$StartTimestamp;
                	$Timestamp[1]=$EndTimestamp;
                	$avgCmdSize[0]=0;
                	$avgCmdSize[1]=0;

                }
	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

		// Set title and subtitle
		$graph->title->Set("Write queue : Avg Cmds size");
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
		$cmds_gr = new LinePlot($avgCmdSize);
		$cmds_gr ->SetLegend("avgCmdSize (bytes)");
		$graph->Add($cmds_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
