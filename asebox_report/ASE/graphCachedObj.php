<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	




$param_list=array(
	'CacheID',
	'DBID',
	'ObjectID',
	'IndexID',
	'CacheName'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	
                // Read the data values
		$result=sybase_query(
                   
                    "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108), 
		    CachedKB
                    from (select distinct Timestamp from ".$ServerName."_CachedObj 
                          where Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
                          ) ListTimestamp  
                          left outer join ".$ServerName."_CachedObj B on ListTimestamp.Timestamp = B.Timestamp and DBID=".$DBID." and B.ObjectID=".$ObjectID." and B.IndexID=".$IndexID."
                    order by ListTimestamp.Timestamp",
                $pid);
                if ($result==false){ 
		  sybase_close($pid); 
		  $pid=0;
		  include ("../connectArchiveServer.php");	
		  echo "Error";
		  return(0);
                }
		while (($row=sybase_fetch_array($result)))
		{
			$Timestamp[]= $row["Ts"];
			$CachedKB[] = $row["CachedKB"];

			//print  $row["Timestamp"]."  ".$row["LogicalReads"]."     ";
		}

if (count($Timestamp)==0) { 
   JpGraphError::Raise("No value to graph");
}

		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


        if ($IndexID==0) $Iname="DATA";
        else $Iname="INDEX ".$IndexID;

		// Set title and subtitle
		$title=$Iname." cached KB in '".$CacheName."'";
		$graph->title->Set($title);

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
		  		

		// Create the BAR plot
		$b1 = new BarPlot($CachedKB);
		$b1 -> SetLegend("Cached KB");
        $b1 -> SetWidth(1); 


		$graph -> yaxis -> scale -> setAutoMin(0);
//		$graph -> yaxis -> scale -> setAutoMax(100);
		$graph -> yaxis -> SetTitle("CachedKB", "middle");
		
		// The order the plots are added determines who's ontop
		$graph->Add($b1);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
