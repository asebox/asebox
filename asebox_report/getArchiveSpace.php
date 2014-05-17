<?php 

class myGauge { 

   // Color 
   var $BgColor = "#FFFFFF", $FgColor = "#990000"; 
   // Dimensions. 
   var $Width = 100, $Height = 8; 
   // Values. 
   var $MinVal = 0, $MaxVal = 100, $CurVal = 77; 

   // Set values 
   function setValues($fgc, $bgc, $wid, $hei, $min, $max, $cur) { 
       $this->BgColor = $fgc; 
       $this->FgColor = $bgc; 
       $this->Width   = $wid; 
       $this->Height  = $hei; 
       $this->MinVal  = $min; 
       $this->MaxVal  = $max; 
       $this->CurVal  = $cur; 
   } 

   // Render this into HTML as a table. 
   function display() { 

       // Normalize the properties. 
       if ($this->MinVal > $this->MaxVal) { 
           $temp_val = $this->MinVal; 
           $this->MinVal = $this->MaxVal; 
           $this->MaxVal = $temp_val; 
       } 

       if ($this->CurVal < $this->MinVal) { 
           $this->CurVal = $this->MinVal; 
       } 
       elseif ($this->CurVal > $this->MaxVal) { 
           $this->CurVal = $this->MaxVal; 
       } 

       // Figure out the percentage that the CurVal is within MinVal and MaxVal. 
       $percentage_val = ($this->CurVal - $this->MinVal) / ($this->MaxVal - $this->MinVal); 

       // Compute the first and second widths. 
       $fg_width = Round($this->Width * $percentage_val); 
       $bg_width = $this->Width - $fg_width; 
       $RenderHtml = "<table cellspacing=0 cellpadding=0 width=" . $this->Width . " height=" . $this->Height . "><tr>"; 
       if ($fg_width > 0) { 
           $RenderHtml = $RenderHtml . "<td width=" . $fg_width . " height=" . $this->Height . " bgcolor=" . $this->FgColor . 
               "><img src=\"images/shim.gif\"></td>"; 
       } 
       if ($bg_width > 0) { 
           $RenderHtml = $RenderHtml . "<td width=" . $bg_width . " height=" . 
           $this->Height . " bgcolor=" . $this->BgColor . "><img src=\"images/shim.gif\"></td>"; 
       } 
       $RenderHtml = $RenderHtml . "</tr></table>"; 
        
       print $RenderHtml; 
        
   } 

} 


        $oGauge = new myGauge(); 


        // Initialyze gauge parameters 
        $fc = "#FFFFFF"; 
        $bc = "#990000"; 
        $wi = 145; 
        $hi = 10; 
        $mi = 0; 
        $ma = 100; 
        //$cu = 25;  // Value to set

if ( isset($pid) && ($ArchSrvType=="Adaptive Server Enterprise") ){
	
        $query = "select 
         -- DBsize_dataMb=sum(size)*@@maxpagesize/(1024*1024) ,
         -- dbfree_dataMb=sum(curunreservedpgs(db_id('".$ArchiveDatabase."'),lstart,unreservedpgs))*@@maxpagesize/(1024*1024) ,
         -- no good -- pct_used=(convert(float,sum(size) - sum(curunreservedpgs(db_id('".$ArchiveDatabase."'),lstart,unreservedpgs))) )*100 / sum(size)
         pct_used=(convert(float,sum(size) - sum(convert(float,curunreservedpgs(db_id('".$ArchiveDatabase."'),lstart,unreservedpgs)))) )*100 / sum(size)
        from master..sysusages where dbid=db_id('".$ArchiveDatabase."')
        and segmap&2=2";
	$result = sybase_query($query,$pid);
	if ($result==false){ 
		sybase_close($pid); 
		$pid=0;
		include ("connectArchiveServer.php");	
		echo "<tr><td>Error getting archive space</td></tr></table>";
		return(0);
	}
        $row = sybase_fetch_array($result);
        $cu = $row['pct_used'];
}
else 
	$cu = 0;

$oGauge->setValues($fc, $bc, $wi, $hi, $mi, $ma, $cu ); 


?> 


