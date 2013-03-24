<?php
  include ("../ARContext_restore.php");
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");	
  include ("../connectArchiveServer.php");	


$param_list=array(
	'Loggedindatetime',
	'Spid'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];

                // Read the data values
		$result=sybase_query(
		   "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108), 
		    BytesSent=isnull(convert(float,B.BytesSent)*1000/Interval,0),
		    BytesReceived =isnull(convert(float,B.BytesReceived) *1000/Interval,0)
                    from (select distinct Timestamp from ".$ServerName."_CnxActiv 
                          where Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
                          ) ListTimestamp  
                          left outer join ".$ServerName."_CnxActiv B on ListTimestamp.Timestamp = B.Timestamp and Loggedindatetime between dateadd(ss,-1,'".$Loggedindatetime."') and dateadd(ss,+1,'".$Loggedindatetime."') and B.Spid=".$Spid."
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
			$BytesSent[] = $row["BytesSent"];
			$BytesReceived[] = $row["BytesReceived"];
		}

if (count($Timestamp)==0) { 
   JpGraphError::Raise("No value to graph");
}

		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		$graph->title->Set("Network bytes I/O");

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
		  		
		// Create the Physical reads BAR plot
		$b1 = new BarPlot($BytesSent);
		$b1 -> SetLegend("BytesSent");
        //$b1 -> SetWidth(1); 

		// Create the Physical Writes BAR plot
		$b2 = new BarPlot($BytesReceived);
		$b2 -> SetLegend("BytesReceived");
        //$b2 -> SetWidth(1); 
                
        $accbar = new AccBarPlot(array($b1,$b2)); 
        $accbar -> SetWidth(1); 

		// The order the plots are added determines who's ontop
		$graph->Add($accbar);



		$graph -> yaxis -> scale -> setAutoMin(0);
		$graph -> yaxis -> SetTitle("Bytes / s", "middle");
		
		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();


	?>
