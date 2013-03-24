<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");



		$result=sybase_query(
"select Ts=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108), 
ProcMemKB=sum(case when ObjectType !='user memory' and Statement!='Y' then MemUsageKB else 0 end),
StmtMemKB=sum(case when ObjectType !='user memory' and Statement='Y' then MemUsageKB else 0 end),
UserMemKB=sum(case when ObjectType ='user memory' then MemUsageKB else 0 end)
from ".$ServerName."_ProcMem
where Timestamp >='".$StartTimestamp."'
and Timestamp <='".$EndTimestamp."'
group by Timestamp
order by Timestamp",
 $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Ts[]= $row["Ts"];
			$ProcMemKB[] = $row["ProcMemKB"];
			$StmtMemKB[] = $row["StmtMemKB"];
			$UserMemKB[] = $row["UserMemKB"];

		}


if (count($Ts)==0) 	exit("No values");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Procedure cache memory usage");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,40,100);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Ts);
		$nbVal=sizeof($Ts);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);
        $graph->xaxis->SetTickSide(SIDE_DOWN);

		// Create the ProcMemKB line plot
		$proc_gr = new LinePlot($ProcMemKB);
		$proc_gr -> setFillColor("blue");
		$proc_gr ->SetLegend("Procedures KB");

		// Create the StmtMemKB line plot
		$stmt_gr = new LinePlot($StmtMemKB);
		$stmt_gr  -> setFillColor("cyan");
		$stmt_gr  ->SetLegend("Statement Cache KB");
		
		// Create the UserMemKBline plot
		$user_gr = new LinePlot($UserMemKB);
		$user_gr  -> setFillColor("yellow");
		$user_gr  ->SetLegend("User memory KB");
		
		$acc_gr = new AccLinePlot(array($proc_gr, $stmt_gr, $user_gr));

		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);
        $graph->yaxis->SetTickSide(SIDE_LEFT);

		$graph->Add($acc_gr);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");


        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
