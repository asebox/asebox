<?php
  include ("../ARContext_restore.php");
  include ("../connectArchiveServer.php");	
	include ("../".$jpgraph_home."/src/jpgraph.php");
	include ("../".$jpgraph_home."/src/jpgraph_line.php");
	include ("../".$jpgraph_home."/src/jpgraph_bar.php");	

	$param_list=array(
	  'CacheName'
	);
	foreach ($param_list as $param)
		@$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
  





        $param_list=array(
        	'CacheName'
        );
        foreach ($param_list as $param)
        @$$param=${"_$_SERVER[REQUEST_METHOD]"}[$param];
	
         
                // Find top 5 objects during this interval
                $query="select top 5
                        DBID,ObjectID,DBName, OwnerN=isnull(OwnerName,'dbo'), ObjectN=case when ObjectID=99 then 'ALLOCPAGE' else ObjectName end, IndexID,maxCachedKB=max(CachedKB)
                        from ".$ServerName."_CachedObj
      	                where Timestamp >='".$StartTimestamp."'        
      	                and Timestamp <'".$EndTimestamp."'        
      	                and CacheName         = '".$CacheName."'
      	                group by DBID,ObjectID,DBName, isnull(OwnerName,'dbo'), ObjectName, IndexID
      	                order by max(CachedKB) desc";
      	        
                $result=sybase_query($query, $pid);
                if ($result==false){ 
      	             sybase_close($pid); 
      	             $pid=0;
      	             include ("../connectArchiveServer.php");	
      	             echo "Error";
      	             return(0);
                }
      	        while (($row=sybase_fetch_array($result)))
      	        {
      	             $DBID[]      = $row["DBID"];
      	             $ObjectID[]  = $row["ObjectID"];
      	             $DBName[]    = $row["DBName"];
      	             $OwnerName[] = $row["OwnerN"];
      	             $ObjectName[]= $row["ObjectN"];
      	             $IndexID[]   = $row["IndexID"];
                }
                $cnt = count($DBID);
                //if ($cnt==1) exit("cnt=2");
                if ($cnt<5) {
                	$DBID      [4] = -1;    
                        $ObjectID  [4] = -1;
                        $DBName    [4] = "";
                        $OwnerName [4] = "";
                        $ObjectName[4] = "";
                        $IndexID   [4] = -1;
                }
                if ($cnt<4) {
                	$DBID      [3] = -1;    
                        $ObjectID  [3] = -1;
                        $DBName    [3] = "";
                        $OwnerName [3] = "";
                        $ObjectName[3] = "";
                        $IndexID   [3] = -1;
                }
                if ($cnt<3) {
                	$DBID      [2] = -1;    
                        $ObjectID  [2] = -1;
                        $DBName    [2] = "";
                        $OwnerName [2] = "";
                        $ObjectName[2] = "";
                        $IndexID   [2] = -1;
                }
                if ($cnt<2) {
                	$DBID      [1] = -1;    
                        $ObjectID  [1] = -1;
                        $DBName    [1] = "";
                        $OwnerName [1] = "";
                        $ObjectName[1] = "";
                        $IndexID   [1] = -1;
                }
                if ($cnt<1) {
                	$DBID      [0] = -1;    
                        $ObjectID  [0] = -1;
                        $DBName    [0] = "";
                        $OwnerName [0] = "";
                        $ObjectName[0] = "";
                        $IndexID   [0] = -1;
                }

                // Find CachedMB for each object
		$result=sybase_query(
                    "select Ts=convert(varchar,ListTimestamp.Timestamp,3)+' '+convert(varchar,ListTimestamp.Timestamp,108), 
		    CachedMB0=isnull(B.CachedKB/1024,0),
		    CachedMB1=isnull(C.CachedKB/1024,0),
		    CachedMB2=isnull(D.CachedKB/1024,0),
		    CachedMB3=isnull(E.CachedKB/1024,0),
		    CachedMB4=isnull(F.CachedKB/1024,0)
                    from (select distinct Timestamp from ".$ServerName."_CachedObj 
                          where Timestamp >='".$StartTimestamp."'
                          and Timestamp <='".$EndTimestamp."'
                          ) ListTimestamp  
                          left outer join ".$ServerName."_CachedObj B on ListTimestamp.Timestamp = B.Timestamp and B.DBID=".$DBID[0]." and B.ObjectID=".$ObjectID[0]." and B.IndexID=".$IndexID[0]."
                          left outer join ".$ServerName."_CachedObj C on ListTimestamp.Timestamp = C.Timestamp and C.DBID=".$DBID[1]." and C.ObjectID=".$ObjectID[1]." and C.IndexID=".$IndexID[1]."
                          left outer join ".$ServerName."_CachedObj D on ListTimestamp.Timestamp = D.Timestamp and D.DBID=".$DBID[2]." and D.ObjectID=".$ObjectID[2]." and D.IndexID=".$IndexID[2]."
                          left outer join ".$ServerName."_CachedObj E on ListTimestamp.Timestamp = E.Timestamp and E.DBID=".$DBID[3]." and E.ObjectID=".$ObjectID[3]." and E.IndexID=".$IndexID[3]."
                          left outer join ".$ServerName."_CachedObj F on ListTimestamp.Timestamp = F.Timestamp and F.DBID=".$DBID[4]." and F.ObjectID=".$ObjectID[4]." and F.IndexID=".$IndexID[4]."
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
			$CachedMB_0[] = $row["CachedMB0"];
			$CachedMB_1[] = $row["CachedMB1"];
			$CachedMB_2[] = $row["CachedMB2"];
			$CachedMB_3[] = $row["CachedMB3"];
			$CachedMB_4[] = $row["CachedMB4"];
		}
		$cntSample=count($Timestamp);
if ($cntSample==0) 	exit("pas de valeurs");

                // Get pool size
                $result=sybase_query("select IOBufferSize, AllocatedMB=max(AllocatedKB/1024)
                                      from ".$ServerName."_CachePool
                                      where Timestamp >='".$StartTimestamp."'
                                      and Timestamp <'".$EndTimestamp."'
                                      and CacheName='".$CacheName."'
                                      group by IOBufferSize
                                      order by IOBufferSize desc",
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
                   $IOBufferSize[]= $row["IOBufferSize"];
                   $AllocatedMB[] = $row["AllocatedMB"];
                }
                $nbPools = count($IOBufferSize);
                
                // Generate points for each timestamp
                for ($i=0; $i<$cntSample; $i++) {
                	$allocMB_0[]= $AllocatedMB[0];
                	if ($nbPools > 1) $allocMB_1[]= $AllocatedMB[1]+$AllocatedMB[0];
                	if ($nbPools > 2) $allocMB_2[]= $AllocatedMB[2]+$AllocatedMB[1]+$AllocatedMB[0];
                	if ($nbPools > 3) $allocMB_3[]= $AllocatedMB[3]+$AllocatedMB[2]+$AllocatedMB[1]+$AllocatedMB[0];
                }


		
		// New graph with a background image and drop shadow
		$graph = new Graph(1000,500,"auto");
        $graph->SetScale("textlin");
        $theme_class = new AsemonTheme;
        $graph->SetTheme($theme_class);


		// Set title and subtitle
		//$graph->title->Set("Top 5 objects in 'default data cache'");

		$graph->title->Set("Top 5 objects in '".$CacheName."'");
		// Use built in font
		$graph->title->SetFont(FF_FONT1,FS_BOLD);

		// Make the margin around the plot a little bit bigger
		// then default
		$graph->img->SetMargin(95,20,120,100);	


		// Display every 10:th datalabel
		$graph->xaxis->SetTickLabels($Timestamp);
		$nbVal=sizeof($Timestamp);
		if ( $nbVal>20 )
		  $graph->xaxis->SetTextTickInterval($nbVal/20);
		else
		  $graph->xaxis->SetTextTickInterval(1);
		  		

//'bluegreen','lightblue','purple','blue','green','pink','red','yellow'
		// Create the BAR plots
		$b0 = new BarPlot($CachedMB_0);
                $b0 -> SetWidth(1); 
		$leg0 = $DBName[0].".".$OwnerName[0].".".$ObjectName[0].'('.$IndexID[0].")";
		$b0 -> SetLegend($leg0);

		$b1 = new BarPlot($CachedMB_1);
                $b1 -> SetWidth(1); 
		$leg1 = $DBName[1].".".$OwnerName[1].".".$ObjectName[1].'('.$IndexID[1].")";
		$b1 -> SetLegend($leg1);

		$b2 = new BarPlot($CachedMB_2);
                $b2 -> SetWidth(1); 
		$leg2 = $DBName[2].".".$OwnerName[2].".".$ObjectName[2].'('.$IndexID[2].")";
		$b2 -> SetLegend($leg2);

		$b3 = new BarPlot($CachedMB_3);
                $b3 -> SetWidth(1); 
		$leg3 = $DBName[3].".".$OwnerName[3].".".$ObjectName[3].'('.$IndexID[3].")";
		$b3 -> SetLegend($leg3);

		$b4 = new BarPlot($CachedMB_4);
                $b4 -> SetWidth(1); 
		$leg4 = $DBName[4].".".$OwnerName[4].".".$ObjectName[4].'('.$IndexID[4].")";
		$b4 -> SetLegend($leg4);


		$graph -> yaxis -> scale -> setAutoMin(0);
//		$graph -> yaxis -> scale -> setAutoMax(100);
		$graph -> yaxis -> SetTitle("CachedMB", "middle");
		
		$acc_gr = new AccBarPlot(array($b0, $b1, $b2, $b3, $b4));
		$graph->Add($acc_gr);
		
		
		
		// Graph a line for each pool
		$grPool_0 = new LinePlot($allocMB_0);
		$grPool_0 -> setcolor("red");
		$graph->Add($grPool_0);
        
        if ($nbPools > 1) {
            $grPool_1 = new LinePlot($allocMB_1);
            $grPool_1 -> setcolor("red");
            $graph->Add($grPool_1);
        }
        if ($nbPools > 2) {
            $grPool_2 = new LinePlot($allocMB_2);
            $grPool_2 -> setcolor("red");
            $graph->Add($grPool_2);
            }
        if ($nbPools > 3) {
            $grPool_3 = new LinePlot($allocMB_3);
            $grPool_3 -> setcolor("red");
            $graph->Add($grPool_3);
        }

		$graph -> legend  -> SetLayout(LEGEND_VERT);
		$graph -> legend  -> Pos(0.01, 0.01, "right", "top");

        $graph->graph_theme=null; // This fix bottom margin bad computation
		// Finally output the  image
		$graph->Stroke();

	?>
