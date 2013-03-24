<?php
include ("../ARContext_restore.php");
include ("../connectArchiveServer.php");	
  include ("../".$jpgraph_home."/src/jpgraph.php");
  include ("../".$jpgraph_home."/src/jpgraph_line.php");
  include ("../".$jpgraph_home."/src/jpgraph_bar.php");	


$param_list=array(
	'DBName',
	'ObjectName',
	'IndexID'
);
foreach ($param_list as $param)
@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	
                // Read the data values
		$result=sybase_query(
                   
                    "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108), 
		    RowsInserted=isnull(RowsInserted,0)*1000./isnull(Interval,1),
		    RowsDeleted=isnull(RowsDeleted,0)*1000./isnull(Interval,1),
		    RowsUpdated=isnull(RowsUpdated,0)*1000./isnull(Interval,1)
                    from (select distinct Timestamp from ".$ServerName."_OpObjAct 
                          where Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
                          ) ListTimestamp  
                          left outer join ".$ServerName."_OpObjAct B on ListTimestamp.Timestamp = B.Timestamp and B.dbname='".$DBName."' and B.objname='".$ObjectName."' and B.IndID=".$IndexID."
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
			$RowsInserted[] = $row["RowsInserted"];
			$RowsDeleted[] = $row["RowsDeleted"];
			$RowsUpdated[] = $row["RowsUpdated"];

			//print  $row["Timestamp"]."  ".$row["LogicalReads"]."     ";
		}

if (count($Timestamp)==0) 	exit("pas de valeurs");
//exit("nbval=".sizeof($Timestamp));


		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,300,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


                if ($IndexID==0) $Iname="DATA";
                else $Iname="INDEX ".$IndexID;
		// Set title and subtitle
		$title=$Iname." DML operations / s";
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
		  		

		// Create the RowsInserted BAR plot
		$b1 = new BarPlot($RowsInserted);
		$b1 -> SetLegend("RowsInserted");

		// Create the RowsUpdated BAR plot
		$b2 = new BarPlot($RowsUpdated);
		$b2 -> SetLegend("RowsUpdated");

		// Create the RowsDeleted BAR plot
		$b3 = new BarPlot($RowsDeleted);
		$b3 -> SetLegend("RowsDeleted");


		$graph -> yaxis -> scale -> setAutoMin(0);
//		$graph -> yaxis -> scale -> setAutoMax(100);
		$graph -> yaxis -> SetTitle("DML oper /s", "middle");
		
		$acc_gr = new AccBarPlot(array( $b1, $b2, $b3));
                $acc_gr -> SetWidth(1); 

		$graph->Add($acc_gr);

		$graph -> legend  -> SetLayout(LEGEND_HOR);
		$graph -> legend  -> Pos(0.05, 0.05, "rigth", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
