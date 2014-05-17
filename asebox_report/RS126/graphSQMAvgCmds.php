<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");


$param_list=array(
	'Instance_ID',
	'Instance_Val'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	

                $cnt=0;
		$result=sybase_query(
"select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
avgCmdSize=case when CmdsWritten =0 then 0 else BytesWritten/CmdsWritten end
from ".$ServerName."_SQM
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
and Instance_ID=".$Instance_ID." and Instance_Val =".$Instance_Val."
order by Timestamp",
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
