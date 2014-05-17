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
"select Ts=convert(varchar,S.Timestamp,3)+' '+convert(varchar,S.Timestamp,108),
SegsActive=counter_last
from ".$ServerName."_RSStats S, ".$ServerName."_Instances I 
where S.ID=I.ID
and S.Timestamp >='".$StartTimestamp."'
and S.Timestamp <='".$EndTimestamp."'
and S.ID=".$ID."
and counter_id = 6020
order by S.Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$SegsActive[] = $row["SegsActive"];
			$cnt++;

		}

                if ($cnt == 0) {
                	$Timestamp[0]=$StartTimestamp;
                	$Timestamp[1]=$EndTimestamp;
                	$SegsActive[0]=0;
                	$SegsActive[1]=0;

                }
	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Queue size (1 seg = 1 Mb)");
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
		  		

		// Create the BlocksRead plot
		$sa_gr = new LinePlot($SegsActive);
		$sa_gr ->SetLegend("SegsActive");
		$graph->Add($sa_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
