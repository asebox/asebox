<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");



	$query = 
        "select 'Ts' = convert( varchar(5), Timestamp, 108),
        DefCacheSearches_s = sum( convert(float,case when CacheName='default data cache' then CacheSearches else 0 end) / Interval) * 1000,
        OtherCacheSearches_s = sum( convert(float,case when CacheName!='default data cache' then CacheSearches else 0 end) / Interval) * 1000
	from ".$ServerName."_DataCache          
	where Timestamp >='".$StartTimestamp."'        
	and Timestamp <'".$EndTimestamp."'        
	group by Timestamp
	order by Timestamp
	";

	$result=sybase_query( $query, $pid);

        while (($row=sybase_fetch_array($result)))
	{
			$Timestamp[]= $row["Ts"];
			$DefCacheSearches_s[] = $row["DefCacheSearches_s"];
			$OtherCacheSearches_s[] = $row["OtherCacheSearches_s"];

			//print $row["title_id"]." : ".$row["price"]."     ";
	}


if (count($Timestamp)==0) 	exit("No values");

		
		// New graph with a background image and drop shadow
		$graph = new Graph(380,200,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Cache IO             ");
		//$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);

		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		//$graph->img->SetMargin(100,20,30,100);	
		$graph->img->SetMargin(50,10,40,40);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		

		// Create default data cache the line plot
		$defcache = new LinePlot($DefCacheSearches_s);
		// Create others cache the line plot
		$othercache = new LinePlot($OtherCacheSearches_s);
		$acc_gr = new AccLinePlot(array($defcache, $othercache));

		$defcache ->SetLegend("Default");

		$othercache ->SetLegend("Other");



		$graph -> yaxis -> scale -> setAutoMin(0);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");
		
		// The order the plots are added determines who's ontop
		$graph->Add($acc_gr);

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>





















        
	
