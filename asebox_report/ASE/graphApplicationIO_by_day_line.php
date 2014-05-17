<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");


$param_list=array(
	'Application1',
	'Application2',
	'Application3',
	'Application4'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	

			$result = sybase_query ("select convert(varchar,Timestamp,105) Timestamp, Application, sum(convert(float, LogicalReads))/1000 LogicalReads, sum(convert(float,PhysicalReads)) PhysicalReads
			from ".$ServerName."_ProcActiv
			where Timestamp >='".$StartTimestamp."'        
			and Timestamp <='".$EndTimestamp."' 
			and Application like '%".$Application1."%' 
			group by convert(varchar,Timestamp,105), Application 
			", $pid);
		
			while (($row=sybase_fetch_array($result)))
			{
				$Timestamp[]= $row["Timestamp"];
				$Application[] = $row["Application"];
				$LogicalReads[] = $row["LogicalReads"];
				$PhysicalReads[] = $row["PhysicalReads"];
			}
		
			if ( !empty($Application2) )
			{
				$result = sybase_query ("select convert(varchar,Timestamp,105) Timestamp, Application, sum(convert(float, LogicalReads))/1000 LogicalReads, sum(convert(float,PhysicalReads)) PhysicalReads
				from ".$ServerName."_ProcActiv
				where Timestamp >='".$StartTimestamp."'        
				and Timestamp <='".$EndTimestamp."' 
				and Application like '%".$Application2."%' 
				group by convert(varchar,Timestamp,105), Application 
				", $pid);
			
				while (($row=sybase_fetch_array($result)))
				{
					$aTimestamp2[]= $row["Timestamp"];
					$aApplication2[] = $row["Application"];
					$aLogicalReads2[] = $row["LogicalReads"];
					$aPhysicalReads2[] = $row["PhysicalReads"];
				}
			}
		
			if ( !empty($Application3) )
			{
				$result = sybase_query ("select convert(varchar,Timestamp,105) Timestamp, Application, sum(convert(float, LogicalReads))/1000 LogicalReads, sum(convert(float,PhysicalReads)) PhysicalReads
				from ".$ServerName."_ProcActiv
				where Timestamp >='".$StartTimestamp."'        
				and Timestamp <='".$EndTimestamp."' 
				and Application like '%".$Application3."%' 
				group by convert(varchar,Timestamp,105), Application 
				", $pid);
			
				while (($row=sybase_fetch_array($result)))
				{
					$aTimestamp3[]= $row["Timestamp"];
					$aApplication3[] = $row["Application"];
					$aLogicalReads3[] = $row["LogicalReads"];
					$aPhysicalReads3[] = $row["PhysicalReads"];
				}
			}

			if ( !empty($Application4) )
			{
				$result = sybase_query ("select convert(varchar,Timestamp,105) Timestamp, Application, sum(convert(float, LogicalReads))/1000 LogicalReads, sum(convert(float,PhysicalReads)) PhysicalReads
				from ".$ServerName."_ProcActiv
				where Timestamp >='".$StartTimestamp."'        
				and Timestamp <='".$EndTimestamp."' 
				and Application like '%".$Application4."' 
				group by convert(varchar,Timestamp,105), Application 
				", $pid);
			
				while (($row=sybase_fetch_array($result)))
				{
					$aTimestamp4[]= $row["Timestamp"];
					$aApplication4[] = $row["Application"];
					$aLogicalReads4[] = $row["LogicalReads"];
					$aPhysicalReads4[] = $row["PhysicalReads"];
				}
			}

		
        // New graph with a background image and drop shadow
        $graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);
	
			// Set title and subtitle
			$graph->title->Set("Application IO by date (From: ".$StartTimestamp." To: ".$EndTimestamp.")");
	
			// Use built in font
			$graph->title->SetFont(FF_FONT1,FS_BOLD);
	
			// Make the margin around the plot a little bit bigger
			// then default
			$graph->img->SetMargin(90,20,40,70);	
	
			$graph->xaxis->SetTickLabels($Timestamp);
			$graph->xaxis->SetTextTickInterval(1);
			$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
			$graph->xaxis->SetLabelAngle(45);
	
			// Create the line plot
			
			$b1 = new LinePlot($LogicalReads);

			$b1 -> SetLegend($Application1);
			$b1 -> SetColor('blue');
			
			if ( !empty($Application2) )
			{
				$b2 = new LinePlot($aLogicalReads2);
				$b2->SetLegend($Application2);
				$b2 -> SetColor('red');
			}

			if ( !empty($Application3) )
			{
				$b3 = new LinePlot($aLogicalReads3);
				$b3->SetLegend($Application3);
				$b3 -> SetColor('green');
			}

			if ( !empty($Application4) )
			{
				$b4 = new LinePlot($aLogicalReads4);
				$b4->SetLegend($Application4);
				$b4 -> SetColor('yellow');
			}
			
			$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
	
			$graph -> yaxis -> scale -> setAutoMin(0);
			//$graph -> yaxis -> scale -> setAutoMax(1000000000);
			
			// The order the plots are added determines who's ontop
			$graph->Add($b1);
			if ( !empty($Application2) )
			{
				$graph->Add($b2);
			}
			
			if ( !empty($Application3) )
			{
				$graph->Add($b3);
			}
			
			if ( !empty($Application4) )
			{
				$graph->Add($b4);
			}
			// Finally output the  image
			$graph -> legend  -> SetLayout(LEGEND_HOR);
			$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");
			
        $graph->graph_theme=null; // This fix bottom margin bad computation
        $graph->Stroke();

	?>
