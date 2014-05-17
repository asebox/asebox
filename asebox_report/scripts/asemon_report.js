
window.name = "asemonreport";


//----------------------------------------------------------------------------------------------------
function setMainDivSize(displayLeftPane){
  if (displayLeftPane) {
      reduceSize = 220;                               //was 240;  
      var w=document.body.clientWidth-reduceSize+20;  //was 0
      var h=document.body.clientHeight-160;           //was 160             
      //changecss('.maindiv','top','0px');    //was not here
  }
  else
  {
      var reduceSize = 30;    //was 30
      var w=document.body.clientWidth-reduceSize+2;  //was 0
      var h=document.body.clientHeight-160;             
  }   

   changecss('.boxinmain','maxWidth', w+'px');
   changecss('.maindiv','left', reduceSize-35+'px');    //was 20
   changecss('.maindiv','width', w+'px');
   changecss('.maindiv','height', h+'px');
}

//----------------------------------------------------------------------------------------------------
function setStatMainTableSize(reduceSize){
   var h=document.body.clientHeight-170-reduceSize;
   changecss('.statMainTable','height', h-80+'px');
}

//----------------------------------------------------------------------------------------------------
function setLeftPaneHeight() {
   changecss('.leftmaindiv','height', (getDocHeight()-125)+'px');
}

//----------------------------------------------------------------------------------------------------
function getDocHeight() {
    var D = document;
    return Math.max(
        Math.max(D.body.scrollHeight, D.documentElement.scrollHeight),
        Math.max(D.body.offsetHeight, D.documentElement.offsetHeight),
        Math.max(D.body.clientHeight, D.documentElement.clientHeight)
    );
}

//----------------------------------------------------------------------------------------------------
function changecss(theClass,element,value) {
        //Last Updated on October 10, 1020
        //documentation for this script at
        //http://www.shawnolson.net/a/503/altering-css-class-attributes-with-javascript.html
         var cssRules;

         var added = false;
         for (var S = 0; S < document.styleSheets.length; S++){

    if (document.styleSheets[S]['rules']) {
          cssRules = 'rules';
         } else if (document.styleSheets[S]['cssRules']) {
          cssRules = 'cssRules';
         } else {
          //no rules found... browser unknown
         }

          for (var R = 0; R < document.styleSheets[S][cssRules].length; R++) {
           if (document.styleSheets[S][cssRules][R].selectorText == theClass) {
            if(document.styleSheets[S][cssRules][R].style[element]){
            document.styleSheets[S][cssRules][R].style[element] = value;
            added=true;
                break;
            }
           }
          }
          if(!added){
          try{
                document.styleSheets[S].insertRule(theClass+' { '+element+': '+value+'; }',document.styleSheets[S][cssRules].length);

          } catch(err){
                try{document.styleSheets[S].addRule(theClass,element+': '+value+';');}catch(err){}

          }

          //if(document.styleSheets[S].insertRule){
                          //document.styleSheets[S].insertRule(theClass+' { '+element+': '+value+'; }',document.styleSheets[S][cssRules].length);
                        //} else if (document.styleSheets[S].addRule) {
                                //document.styleSheets[S].addRule(theClass,element+': '+value+';');
        //}
          }
         }
        }

//----------------------------------------------------------------------------------------------------
function genDFormatOut() {
        var format=document.getElementsByName('DFormat')[0].value;
  if (format=='mdy')
      return "mm/dd/yyyy hh:nn";
  else
          return "dd/mm/yyyy hh:nn";
}

//----------------------------------------------------------------------------------------------------
function trim(s) {
        return s.replace(/^\s+/g,'').replace(/\s+$/g,'');
}


//----------------------------------------------------------------------------------------------------
function getDateFromDFormat(theDateStr) {
        var sTrimmed = trim(theDateStr);
        var date_time = sTrimmed.split(" ");
        var dateStr = date_time[0];
        var timeStr = date_time[1];
        var format=document.getElementsByName('DFormat')[0].value;
  if (format=='mdy')
      d=getDateFromFormat(dateStr,"M/d/y");
  else
          d=getDateFromFormat(dateStr,"d/M/y");
        var d = new Date(d);
  // Add time if necessary
  var hms = timeStr.split(":");
  if (hms[0]!=null) d.setHours(parseInt(hms[0])); else d.setHours(0);
  if (hms[1]!=null) d.setMinutes(parseInt(hms[1])); else d.setMinutes(0);
  if (hms[2]!=null) d.setSeconds(parseInt(hms[2])); else d.setSeconds(0);
  return d;
}

//========================================================================================================================
// Date Math
function settoday() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 document.inputparam.StartTimestamp.value = Date.Format(dat, genDFormatOut());
 dat = Date.DateAdd("d", 1, dat); // add 1 day
 dat = Date.DateAdd("n", -1, dat); // substract 1 minute to preserve OLD behavior
 document.inputparam.EndTimestamp.value = Date.Format(dat, genDFormatOut());
}
function setyesterday() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 dat = Date.DateAdd("d", -1, dat);  // substract 1 day
 document.inputparam.StartTimestamp.value = Date.Format(dat, genDFormatOut());
 dat = Date.DateAdd("d", 1, dat); // add 1 day
 dat = Date.DateAdd("n", -1, dat); // substract 1 minute to preserve OLD behavior
 document.inputparam.EndTimestamp.value = Date.Format(dat, genDFormatOut());
}
function setlastweek() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 dat = Date.DateAdd("d", -7, dat); // substract 7 days
 document.inputparam.StartTimestamp.value = Date.Format(dat, genDFormatOut());
 dat = Date.DateAdd("d", 7, dat); // add 7 days
 dat = Date.DateAdd("n", -1, dat); // substract 1 minute to preserve OLD behavior
 document.inputparam.EndTimestamp.value = Date.Format(dat, genDFormatOut());
}

function setlasthour() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy hh:nn"));
 var datstart = Date.DateAdd("h", -1, dat); // substract 1 hour
 document.inputparam.StartTimestamp.value = Date.Format(datstart, genDFormatOut());

 var datend = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 datend = Date.DateAdd("d", 1, datend); // add 1 day
 datend = Date.DateAdd("n", -1, datend); // substract 1 minute

 document.inputparam.EndTimestamp.value = Date.Format(datend, genDFormatOut());
}

function setlastweekworkdays(firstweekworkday, lastweekworkday ) {
        // from monday to friday
 var fwwd=0;
 if ( (firstweekworkday >= 0) && (firstweekworkday <= 6) )
    fwwd = firstweekworkday;

 var lwwd=6;
 if ( (lastweekworkday >= 0) && (lastweekworkday < 6) )
    lwwd = lastweekworkday+1;

 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 dat = Date.getPreviousSunday(dat);
 document.inputparam.StartTimestamp.value = Date.Format(Date.DateAdd("d", fwwd , dat), genDFormatOut());
 document.inputparam.EndTimestamp.value = Date.Format(Date.DateAdd("d", lwwd, dat), genDFormatOut());
}

function setlastmonth() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));                                                    
 dat = Date.DateAdd("m", -1, dat); // substract 1 month
 document.inputparam.StartTimestamp.value = Date.Format(dat, genDFormatOut());   
 dat = Date.DateAdd("m", 1, dat); // add 1 month
 dat = Date.DateAdd("n", -1, dat); // substract 1 minute to preserve OLD behavior
 document.inputparam.EndTimestamp.value = Date.Format(dat, genDFormatOut());     
}

function setplusoneday() {
 var dat = new Date(getDateFromDFormat(document.inputparam.StartTimestamp.value));
 document.inputparam.StartTimestamp.value = Date.Format(Date.DateAdd("d", 1, dat), genDFormatOut());
 
 var dat = new Date(getDateFromDFormat(document.inputparam.EndTimestamp.value)); 
 document.inputparam.EndTimestamp.value = Date.Format(Date.DateAdd("d", 1, dat), genDFormatOut());
}

function setminusoneday() {
 var dat = new Date(getDateFromDFormat(document.inputparam.StartTimestamp.value));
 document.inputparam.StartTimestamp.value = Date.Format(Date.DateAdd("d", -1, dat), genDFormatOut());
 
 var dat = new Date(getDateFromDFormat(document.inputparam.EndTimestamp.value)); 
 document.inputparam.EndTimestamp.value = Date.Format(Date.DateAdd("d", -1, dat), genDFormatOut());
}

function addXhours(nbh) {
 var dat = new Date(getDateFromDFormat(document.inputparam.StartTimestamp.value));
 document.inputparam.StartTimestamp.value = Date.Format(Date.DateAdd("h", nbh, dat), genDFormatOut());
 
 var dat = new Date(getDateFromDFormat(document.inputparam.EndTimestamp.value)); 
 document.inputparam.EndTimestamp.value = Date.Format(Date.DateAdd("h", nbh, dat), genDFormatOut());
}

//========================================================================================================================
// Date Math 2
function settoday2() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 document.inputparam.StartTimestamp2.value = Date.Format(dat, genDFormatOut());
 dat = Date.DateAdd("d", 1, dat); // add 1 day
 dat = Date.DateAdd("n", -1, dat); // substract 1 minute to preserve OLD behavior
 document.inputparam.EndTimestamp2.value = Date.Format(dat, genDFormatOut());
}
function setyesterday2() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 dat = Date.DateAdd("d", -1, dat);  // substract 1 day
 document.inputparam.StartTimestamp2.value = Date.Format(dat, genDFormatOut());
 dat = Date.DateAdd("d", 1, dat); // add 1 day
 dat = Date.DateAdd("n", -1, dat); // substract 1 minute to preserve OLD behavior
 document.inputparam.EndTimestamp2.value = Date.Format(dat, genDFormatOut());
}
function setlastweek2() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 dat = Date.DateAdd("d", -7, dat); // substract 7 days
 document.inputparam.StartTimestamp2.value = Date.Format(dat, genDFormatOut());
 dat = Date.DateAdd("d", 7, dat); // add 7 days
 dat = Date.DateAdd("n", -1, dat); // substract 1 minute to preserve OLD behavior
 document.inputparam.EndTimestamp2.value = Date.Format(dat, genDFormatOut());
}

function setlasthour2() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy hh:nn"));
 var datstart = Date.DateAdd("h", -1, dat); // substract 1 hour
 document.inputparam.StartTimestamp2.value = Date.Format(datstart, genDFormatOut());

 var datend = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 datend = Date.DateAdd("d", 1, datend); // add 1 day
 datend = Date.DateAdd("n", -1, datend); // substract 1 minute

 document.inputparam.EndTimestamp2.value = Date.Format(datend, genDFormatOut());
}

function setlastweekworkdays2(firstweekworkday, lastweekworkday ) {
        // from monday to friday
 var fwwd=0;
 if ( (firstweekworkday >= 0) && (firstweekworkday <= 6) )
    fwwd = firstweekworkday;

 var lwwd=6;
 if ( (lastweekworkday >= 0) && (lastweekworkday < 6) )
    lwwd = lastweekworkday+1;

 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));
 dat = Date.getPreviousSunday(dat);
 document.inputparam.StartTimestamp2.value = Date.Format(Date.DateAdd("d", fwwd , dat), genDFormatOut());
 document.inputparam.EndTimestamp2.value = Date.Format(Date.DateAdd("d", lwwd, dat), genDFormatOut());
}

function setlastmonth2() {
 var dat = new Date(Date.Format(Date(), "mm/dd/yyyy"));                                                    
 dat = Date.DateAdd("m", -1, dat); // substract 1 month
 document.inputparam.StartTimestamp2.value = Date.Format(dat, genDFormatOut());   
 dat = Date.DateAdd("m", 1, dat); // add 1 month
 dat = Date.DateAdd("n", -1, dat); // substract 1 minute to preserve OLD behavior
 document.inputparam.EndTimestamp2.value = Date.Format(dat, genDFormatOut());     
}

function setplusoneday2() {
 var dat = new Date(getDateFromDFormat(document.inputparam.StartTimestamp2.value));
 document.inputparam.StartTimestamp2.value = Date.Format(Date.DateAdd("d", 1, dat), genDFormatOut());
 
 var dat = new Date(getDateFromDFormat(document.inputparam.EndTimestamp2.value)); 
 document.inputparam.EndTimestamp2.value = Date.Format(Date.DateAdd("d", 1, dat), genDFormatOut());
}

function setminusoneday2() {
 var dat = new Date(getDateFromDFormat(document.inputparam.StartTimestamp2.value));
 document.inputparam.StartTimestamp2.value = Date.Format(Date.DateAdd("d", -1, dat), genDFormatOut());
 
 var dat = new Date(getDateFromDFormat(document.inputparam.EndTimestamp2.value)); 
 document.inputparam.EndTimestamp2.value = Date.Format(Date.DateAdd("d", -1, dat), genDFormatOut());
}

function addXhours2(nbh) {
 var dat = new Date(getDateFromDFormat(document.inputparam.StartTimestamp2.value));
 document.inputparam.StartTimestamp2.value = Date.Format(Date.DateAdd("h", nbh, dat), genDFormatOut());
 
 var dat = new Date(getDateFromDFormat(document.inputparam.EndTimestamp2.value)); 
 document.inputparam.EndTimestamp2.value = Date.Format(Date.DateAdd("h", nbh, dat), genDFormatOut());
}


//========================================================================================================================
// Clear functions
function clearSRVlist() {
 //document.inputparam.ServerName.value = ""
 document.inputparam.ServerName.length = 0
 document.inputparam.ServerName_temp.length = 0
 document.inputparam.selector.value = "Summary";
 document.inputparam.submit()
}

function clearTimestamp() {
 document.inputparam.StartTimestamp.length = 0
 document.inputparam.EndTimestamp.length = 0
}

function connect() {
 document.inputparam.ArchiveDatabase.length = 0
 
 document.inputparam.SrvType.value = "ASE"
<<<<<<< HEAD
 document.inputparam.ServerName.value = ""
 document.inputparam.StartTimestamp.value = ""
 document.inputparam.EndTimestamp.value = ""
=======
 
 document.inputparam.ServerName.length = 0
 document.inputparam.ServerName_temp.length = 0 
 
 document.inputparam.ServerName.value = ""
 document.inputparam.StartTimestamp.value = ""
 document.inputparam.EndTimestamp.value = ""
 document.inputparam.ServerName2.value = ""
 document.inputparam.StartTimestamp2.value = ""
 document.inputparam.EndTimestamp2.value = ""
>>>>>>> 3.1.0
 document.inputparam.submit()
}

function reload() {
 document.inputparam.submit()
}


var WindowObjectReference; // global variable

function urlencode(str) {
return escape(str).replace(/\+/g,'%2B').replace(/%20/g, '+').replace(/\*/g, '%2A').replace(/\//g, '%2F').replace(/@/g, '%40');
}

function getSql(w,h, HomeUrl, query_name)
{
  //"resizable=yes,scrollbars=yes,menubar=yes,toolbar=yes,status=no");
  var winl = (screen.width - w) / 2;
  var wint = (screen.height - h) / 2;
  var query = document.getElementsByName(query_name)[0].value;
  WindowObjectReference = window.open(HomeUrl+"show_sql.php?QUERY=" + urlencode(query),
    "SQLText",
    "scrollbars=no,status=no,location=no,toolbar=no,menubar=no,directories=no,resizable=no,width=" + w + ",height=" + h + ",top=" + wint + ",left=" + winl + "");
  //"resizable=yes,scrollbars=yes,menubar=yes,toolbar=yes,status=no");
  WindowObjectReference.focus();
}

function getSrvCollectors(w,h, srvname)
{
  //"resizable=yes,scrollbars=yes,menubar=yes,toolbar=yes,status=no");
  var winl = (screen.width - w) / 2;
  var wint = (screen.height - h) / 2;
  ARContextJSON = document.inputparam.ARContextJSON.value;
  WindowObjectReference = window.open("show_SrvCollectors.php?SrvName=" + srvname+"&ARContextJSON="+ARContextJSON,
    "SrvCollectors",
    "scrollbars=yes,status=no,location=no,toolbar=no,menubar=no,directories=no,resizable=yes,width=" + w + ",height=" + h + ",top=" + wint + ",left=" + winl + "");
  //"resizable=yes,scrollbars=yes,menubar=yes,toolbar=yes,status=no");
  WindowObjectReference.focus();
}

function setSrv(SrvType, SrvName) {
 document.inputparam.SrvType.value = SrvType;
 document.inputparam.ServerName_temp.value = SrvName;
  document.inputparam.submit();
}

function reload () {
  document.inputparam.submit();
}

function setSelector (selector) {
  var previousSelector = document.inputparam.selector.value;	
  var ARContextJSON = document.inputparam.ARContextJSON.value;
  var newwindow = document.inputparam.newwindow.checked;

  if (newwindow==false) {
  	// Save new selector in current windows and refresh it
    document.inputparam.selector.value = selector;
    document.inputparam.submit();
  }  
  
  else {
  	// A new window is requested
  	// setup ARContext and call new window
    var ARContext = JSON.parse(ARContextJSON);
    var idx = document.inputparam.ServerName.selectedIndex;
    ARContext.ServerName_sav = document.inputparam.ServerName.options[idx].text;
    ARContext.StartTimestamp_sav = document.inputparam.StartTimestamp.value;
    ARContext.EndTimestamp_sav = document.inputparam.EndTimestamp.value;
    ARContext.SrvType = document.inputparam.SrvType.value;
    ARContext.selector = selector;
    ARContext.DFormat = document.inputparam.DFormat.value;
    ARContextJSON=JSON.stringify(ARContext);

    if ( document.inputparam.ServerName2 ) {
       ARContext.ServerName2_sav = document.inputparam.ServerName2.options[idx].text;
       ARContext.StartTimestamp2_sav = document.inputparam.StartTimestamp2.value;
       ARContext.EndTimestamp2_sav = document.inputparam.EndTimestamp2.value;
       ARContext.ServerName1_sav = document.inputparam.ServerName.options[idx].text;
       ARContext.StartTimestamp1_sav = document.inputparam.StartTimestamp.value;
       ARContext.EndTimestamp1_sav = document.inputparam.EndTimestamp.value;
       ARContextJSON=JSON.stringify(ARContext);    
       WindowObjectReference = window.open("asemon_report_noLeftPane.php?ARContextJSON="+ARContextJSON+"#top", "_blank");
    } else {    
       WindowObjectReference = window.open("compare_report_noLeftPane.php?ARContextJSON="+ARContextJSON+"#top", "_blank");
    }
    WindowObjectReference.focus();
  }
}

function clearServerName_temp() {
 document.inputparam.ServerName_temp.value = "";
}