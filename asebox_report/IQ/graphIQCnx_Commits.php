<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");

  
  $ConnCreateTime = $_GET['ConnCreateTime'];
  $IQconnID = $_GET['IQconnID'];




		$result=sybase_query(
		   "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108), 
                           commits_per_s=isnull((Commits*1000.)/(Interval),0)
                           from (select distinct Timestamp from ".$ServerName."_IQCnx 
                          where Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
                          ) ListTimestamp  
                          left outer join ".$ServerName."_IQCnx B on ListTimestamp.Timestamp = B.Timestamp and ConnCreateTime='".$ConnCreateTime."'and B.IQconnID=".$IQconnID."
                    order by ListTimestamp.Timestamp",
                    $pid);
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$commits_per_s[] = $row["commits_per_s"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Commits per seconds");
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
		  		

		// Create the satoiq_count bar plot
		$commits_per_s_gr = new BarPlot($commits_per_s);
		$commits_per_s_gr ->SetLegend("Commits / s");
			

		$graph->Add($commits_per_s_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
