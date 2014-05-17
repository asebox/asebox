<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");



		$result=sybase_query(
"select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108), 
Bytes_Rcv_kps=(case when d_BytesReceived<0 then 0 else d_BytesReceived end *1000.)/(Interval*1024), 
Bytes_Snt_kps=(case when d_BytesSent<0 then 0 else d_BytesSent end *1000.)/(Interval*1024)
from ".$ServerName."_IQAsaStat
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
order by Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$Bytes_Rcv_kps[] = $row["Bytes_Rcv_kps"];
			$Bytes_Snt_kps[] = $row["Bytes_Snt_kps"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Network activity (Kb sent + Kb received accumulated)");
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
		  		
		// Create the bytes sent line plot
		$kbyte_snt_gr = new LinePlot($Bytes_Snt_kps);
		$kbyte_snt_gr ->SetLegend("Bytes sent Kb/s");

		// Create the bytes received line plot
		$kbyte_rcv_gr = new LinePlot($Bytes_Rcv_kps);
		$kbyte_rcv_gr ->SetLegend("Bytes Received Kb/s");
		

		$acc_gr = new AccLinePlot(array($kbyte_snt_gr, $kbyte_rcv_gr));
		

		$graph->Add($acc_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
