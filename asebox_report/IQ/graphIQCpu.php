<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");



		$result=sybase_query(
"select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108), 
UserCPU=  ( (case when d_ProcessCPUUser   <0 then 0 else d_ProcessCPUUser   end) * 100000.)/(NumProcessorsAvail*Interval), 
SystemCPU=( (case when d_ProcessCPUSystem <0 then 0 else d_ProcessCPUSystem end) * 100000.)/(NumProcessorsAvail*Interval)
from ".$ServerName."_IQAsaStat
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
order by Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$UserCPU[] = $row["UserCPU"];
			$SystemCPU[] = $row["SystemCPU"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("CPU (system + user accumulated)");
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
		  		
		// Create the user cpu line plot
		$user_gr = new LinePlot($UserCPU);
		$user_gr ->SetLegend("User Cpu (%)");
		
		// Create the system cpu line plot
		$system_gr = new LinePlot($SystemCPU);
		$system_gr ->SetLegend("System Cpu (%)");

		$acc_gr = new AccLinePlot(array($system_gr, $user_gr));
		

		$graph->Add($acc_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

                
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
