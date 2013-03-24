<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");

  
  $ConnCreateTime = $_GET['ConnCreateTime'];
  $IQconnID = $_GET['IQconnID'];




		$result=sybase_query(
		   "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108), 
                           satoiq_count=isnull((satoiq_count*1000.)/(Interval),0), 
                           iqtosa_count=isnull((iqtosa_count*1000.)/(Interval),0)
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
			$satoiq_count[] = $row["satoiq_count"];
			$iqtosa_count[] = $row["iqtosa_count"];

		}


	
		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Exchanges between catalog and iq (satoiq_count + iqtosa_count accumulated)");
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
		$satoiq_count_gr = new BarPlot($satoiq_count);
		$satoiq_count_gr ->SetLegend("satoiq_count / s");
		
		// Create the iqtosa_count bar plot
		$iqtosa_count_gr = new BarPlot($iqtosa_count);
		$iqtosa_count_gr ->SetLegend("iqtosa_count / s");

		$acc_gr = new AccBarPlot(array($satoiq_count_gr, $iqtosa_count_gr));
        $acc_gr -> SetWidth(1); 
		

		$graph->Add($acc_gr);
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

		
        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
