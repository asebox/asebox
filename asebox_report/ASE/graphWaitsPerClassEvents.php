<?php

  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_pie.php");


	$param_list=array(
		'Kpid',
		'Loggedindatetime',
		'CPUTime'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];


    $ClassName[] = "CPU";
    $WaitTime_ms[] = $CPUTime;
    
	$query = "select top 10 ClassName=C.Description, WaitTime_ms=sum(WaitTime)
                  from ".$ServerName."_CnxWaits W,".$ServerName."_WEvInf E, ".$ServerName."_WClassInf C
                  where W.WaitEventID = E.WaitEventID
                    and E.WaitClassID = C.WaitClassID
	            and Kpid=".$Kpid."
                    and W.Timestamp >= '".$Loggedindatetime."'
                    and W.Timestamp < (select isnull(min(Loggedindatetime), '1/1/3000')
                                       from ".$ServerName."_Cnx cnx
                                       where cnx.Kpid = W.Kpid
                                         and cnx.Spid = W.Spid
                                         and cnx.Loggedindatetime > '".$Loggedindatetime."'
                                       )
	            and W.Timestamp >='".$StartTimestamp."'        
	            and W.Timestamp <'".$EndTimestamp."' 
                  group by C.Description
                  order by sum(WaitTime) desc";
//JpGraphError::Raise("SQL=".$query);

	$result=sybase_query($query, $pid);

		while (($row=sybase_fetch_array($result)))
		{
			$ClassName[] = $row["ClassName"];
			$WaitTime_ms[] = $row["WaitTime_ms"];
		}

		// Create the Pie Graph. 
		$graph = new PieGraph(650,300,'auto');
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);
		//$graph->SetShadow();
		
		// Set A title for the plot
		$graph->title->Set("CPU time / Wait time per class of wait events");
		$graph->subtitle->Set("From: ".$StartTimestamp." To: ".$EndTimestamp);
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		
		// Create
		$p1 = new PiePlot($WaitTime_ms);
		$graph->Add($p1);
		$p1->SetCenter(0.3,0.5);
		$p1->SetLegends($ClassName);
				
		// Send back the HTML page which will call this script again
		// to retrieve the image.
        $graph->graph_theme=null; // This fix bottom margin bad computation
		$graph->Stroke();


	?>
