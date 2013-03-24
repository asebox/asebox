<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");


	
		$result=sybase_query(
		"select
		Timestamp=convert(varchar,Timestamp,3)+' '+convert(varchar,Timestamp,108),
		sum(case when program_name='ReportMaker' then LogicalReads else  0 end) as 'ReportMaker',
		sum(case when program_name='ala200608_RCT01' then LogicalReads else  0 end) as 'ala200608_RCT01',
		sum(case when program_name='Cispeo_1006MRp02' then LogicalReads else  0 end) as 'Cispeo_1006MRp02',
		sum(case when program_name='CIAAutomate' then LogicalReads else  0 end) as 'CIAAutomate'
		from ".$ServerName."_CnxActiv B, ".$ServerName."_Cnx A
		where B.Timestamp between '".$StartTimestamp."' and '".$EndTimestamp."'
		and B.Loggedindatetime = A.Loggedindatetime 
		and B.Kpid = A.Kpid
		and B.Spid = A.Spid
		group by Timestamp
		having isnull(sum(convert(float,B.LogicalReads)),0)/sum(sum(convert(float,B.LogicalReads))) >0.01
		order by 1",
		 $pid);

/*
		$result=sybase_query(
		"select convert(varchar,StartTime,3)+' '+convert(varchar,StartTime,108) Timestamp,  
		sum(case when Application='DBArtisan' then LogicalReads else  0 end) as 'DBArtisan',
		sum(case when Application='SPOTLIGHT' then LogicalReads else  0 end) as 'SPOTLIGHT',
		sum(case when Application='ADS_Evaluation' then LogicalReads else  0 end) as 'ADS_Evaluation',
		sum(case when Application='asemon_logger' then LogicalReads else  0 end) as 'asemon_logger'
		from ".$ServerName."_StmtStat
		where StartTime between '".$StartTimestamp."' and '".$EndTimestamp."'
		group by StartTime, Application
		having isnull(sum(convert(float,LogicalReads)),0)/sum(sum(convert(float,LogicalReads))) >0.01
		order by 2 desc",
		 $pid);
*/

		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Timestamp"];
			$ap1[] = $row["DBArtisan"];
			$ap2[] = $row["SPOTLIGHT"];
			$ap3[] = $row["ADS_Evaluation"];
			$ap4[] = $row["asemon_logger"];
		}

		if (count($Timestamp)==0) 	exit("pas de valeurs");
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);

		// Set title and subtitle
		$graph->title->Set("Applications I/O");

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
		  		
		$graph->xaxis->SetFont(FF_COURIER,FS_NORMAL,8);
		$graph->xaxis->SetLabelAngle(45);

		// Create the line plot
		$b1 = new LinePlot($ap1);
		$b1 -> setcolor("blue");
		$b1 -> setFillColor("blue");
		$b1 ->SetLegend("AP1");

		// Create the line plot
		$b2 = new LinePlot($ap2);
		$b2 -> setcolor("green");
		$b2 -> setFillColor("green");
		$b2 ->SetLegend("AP2");

		// Create the line plot
		$b3 = new LinePlot($ap3);
		$b3 -> setcolor("yellow");
		$b3 -> setFillColor("yellow");
		$b3 ->SetLegend("AP3");

		// Create the line plot
		$b4 = new LinePlot($ap4);
		$b4 -> setcolor("red");
		$b4 -> setFillColor("red");
		$b4 ->SetLegend("AP4");

		$acc_gr = new AccLinePlot(array($b1, $b2, $b3, $b4));

		$graph -> yaxis->SetFont(FF_COURIER,FS_NORMAL,8);

		$graph -> yaxis -> scale -> setAutoMin(0);
		//$graph -> yaxis -> scale -> setAutoMax(100);
		
		// The order the plots are added determines who's ontop
		$graph->Add($acc_gr);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
