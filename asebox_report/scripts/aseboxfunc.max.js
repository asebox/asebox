/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/* jsDate.js */
/*----------------------------------------------------------------------------------------------------*/
/*
Name: jsDate
Desc: VBScript native Date functions emulated for Javascript
Author: Rob Eberhardt, Slingshot Solutions - http://slingfive.com/
Note: see jsDate.txt for more info
*/

// constants
vbGeneralDate=0; vbLongDate=1; vbShortDate=2; vbLongTime=3; vbShortTime=4;  // NamedFormat
vbUseSystemDayOfWeek=0; vbSunday=1; vbMonday=2; vbTuesday=3; vbWednesday=4; vbThursday=5; vbFriday=6; vbSaturday=7;	// FirstDayOfWeek
vbUseSystem=0; vbFirstJan1=1; vbFirstFourDays=2; vbFirstFullWeek=3;	// FirstWeekOfYear

// arrays (1-based)
Date.MonthNames = [null,'January','February','March','April','May','June','July','August','September','October','November','December'];
Date.WeekdayNames = [null,'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];


Date.IsDate = function(p_Expression){
	return !isNaN(new Date(p_Expression));		// <-- review further
}

Date.CDate = function(p_Date){
	if(Date.IsDate(p_Date)){ return new Date(p_Date); }

	var strTry = p_Date.replace(/\-/g, '/').replace(/\./g, '/').replace(/ /g, '/');	// fix separators
	strTry = strTry.replace(/pm$/i, " pm").replace(/am$/i, " am");	// and meridian spacing
	if(Date.IsDate(strTry)){ return new Date(strTry); }

	var strTryYear = strTry + '/' + new Date().getFullYear();	// append year
	if(Date.IsDate(strTryYear)){ return new Date(strTryYear); }
	

	if(strTry.indexOf(":")){	// if appears to have time
		var strTryYear2 = strTry.replace(/ /, '/' + new Date().getFullYear() + ' ');	// insert year
		if(Date.IsDate(strTryYear2)){ return new Date(strTryYear2); }

		var strTryDate = new Date().toDateString() + ' ' + p_Date;	// pre-pend current date
		if(Date.IsDate(strTryDate)){ return new Date(strTryDate); }
	}
	
	return false;	// double as looser IsDate
	//throw("Error #13 - Type mismatch");	// or is this better? 
}


Date.DateAdd = function(p_Interval, p_Number, p_Date){
	if(!Date.CDate(p_Date)){	return "invalid date: '" + p_Date + "'";	}
	if(isNaN(p_Number)){	return "invalid number: '" + p_Number + "'";	}	

	p_Number = new Number(p_Number);
	var dt = Date.CDate(p_Date);
	
	switch(p_Interval.toLowerCase()){
		case "yyyy": {
			dt.setFullYear(dt.getFullYear() + p_Number);
			break;
		}
		case "q": {
			dt.setMonth(dt.getMonth() + (p_Number*3));
			break;
		}
		case "m": {
			dt.setMonth(dt.getMonth() + p_Number);
			break;
		}
		case "y":			// day of year
		case "d":			// day
		case "w": {		// weekday
			dt.setDate(dt.getDate() + p_Number);
			break;
		}
		case "ww": {	// week of year
			dt.setDate(dt.getDate() + (p_Number*7));
			break;
		}
		case "h": {
			dt.setHours(dt.getHours() + p_Number);
			break;
		}
		case "n": {		// minute
			dt.setMinutes(dt.getMinutes() + p_Number);
			break;
		}
		case "s": {
			dt.setSeconds(dt.getSeconds() + p_Number);
			break;
		}
		case "ms": {	// JS extension
			dt.setMilliseconds(dt.getMilliseconds() + p_Number);
			break;
		}
		default: {
			return "invalid interval: '" + p_Interval + "'";
		}
	}
	return dt;
}



Date.DateDiff = function(p_Interval, p_Date1, p_Date2, p_FirstDayOfWeek){
	if(!Date.CDate(p_Date1)){	return "invalid date: '" + p_Date1 + "'";	}
	if(!Date.CDate(p_Date2)){	return "invalid date: '" + p_Date2 + "'";	}
	p_FirstDayOfWeek = (isNaN(p_FirstDayOfWeek) || p_FirstDayOfWeek==0) ? vbSunday : parseInt(p_FirstDayOfWeek);	// set default & cast

	var dt1 = Date.CDate(p_Date1);
	var dt2 = Date.CDate(p_Date2);

	// correct DST-affected intervals ("d" & bigger)
	if("h,n,s,ms".indexOf(p_Interval.toLowerCase())==-1){
		if(p_Date1.toString().indexOf(":") ==-1){ dt1.setUTCHours(0,0,0,0) };	// no time, assume 12am
		if(p_Date2.toString().indexOf(":") ==-1){ dt2.setUTCHours(0,0,0,0) };	// no time, assume 12am
	}


	// get ms between UTC dates and make into "difference" date
	var iDiffMS = dt2.valueOf() - dt1.valueOf();
	var dtDiff = new Date(iDiffMS);

	// calc various diffs
	var nYears  = dt2.getUTCFullYear() - dt1.getUTCFullYear();
	var nMonths = dt2.getUTCMonth() - dt1.getUTCMonth() + (nYears!=0 ? nYears*12 : 0);
	var nQuarters = parseInt(nMonths / 3);	//<<-- different than VBScript, which watches rollover not completion
	
	var nMilliseconds = iDiffMS;
	var nSeconds = parseInt(iDiffMS / 1000);
	var nMinutes = parseInt(nSeconds / 60);
	var nHours = parseInt(nMinutes / 60);
	var nDays  = parseInt(nHours / 24);	// <-- now fixed for DST switch days
	var nWeeks = parseInt(nDays / 7);


	if(p_Interval.toLowerCase()=='ww'){
			// set dates to 1st & last FirstDayOfWeek
			var offset = Date.DatePart("w", dt1, p_FirstDayOfWeek)-1;
			if(offset){	dt1.setDate(dt1.getDate() +7 -offset);	}
			var offset = Date.DatePart("w", dt2, p_FirstDayOfWeek)-1;
			if(offset){	dt2.setDate(dt2.getDate() -offset);	}
			// recurse to "w" with adjusted dates
			var nCalWeeks = Date.DateDiff("w", dt1, dt2) + 1;
	}
	// TODO: similar for 'w'?
	
	
	// return difference
	switch(p_Interval.toLowerCase()){
		case "yyyy": return nYears;
		case "q": return nQuarters;
		case "m":	return nMonths;
		case "y":			// day of year
		case "d": return nDays;
		case "w": return nWeeks;
		case "ww":return nCalWeeks; // week of year	
		case "h": return nHours;
		case "n": return nMinutes;
		case "s": return nSeconds;
		case "ms":return nMilliseconds;	// not in VBScript
		default : return "invalid interval: '" + p_Interval + "'";
	}
}




Date.DatePart = function(p_Interval, p_Date, p_FirstDayOfWeek){
	if(!Date.CDate(p_Date)){	return "invalid date: '" + p_Date + "'";	}

	var dtPart = Date.CDate(p_Date);
	
	switch(p_Interval.toLowerCase()){
		case "yyyy": return dtPart.getFullYear();
		case "q": return parseInt(dtPart.getMonth() / 3) + 1;
		case "m": return dtPart.getMonth() + 1;
		case "y": return Date.DateDiff("y", "1/1/" + dtPart.getFullYear(), dtPart) + 1;	// day of year
		case "d": return dtPart.getDate();
		case "w": return Date.Weekday(dtPart.getDay()+1, p_FirstDayOfWeek);		// weekday
		case "ww":return Date.DateDiff("ww", "1/1/" + dtPart.getFullYear(), dtPart, p_FirstDayOfWeek) + 1;	// week of year
		case "h": return dtPart.getHours();
		case "n": return dtPart.getMinutes();
		case "s": return dtPart.getSeconds();
		case "ms":return dtPart.getMilliseconds();	// <-- JS extension, NOT in VBScript
		default : return "invalid interval: '" + p_Interval + "'";
	}
}



Date.MonthName = function(p_Month, p_Abbreviate){
	if(isNaN(p_Month)){	// v0.94- compat: extract real param from passed date
		if(!Date.CDate(p_Month)){	return "invalid month: '" + p_Month + "'";	}
		p_Month = DatePart("m", Date.CDate(p_Month));
	}

	var retVal = Date.MonthNames[p_Month];
	if(p_Abbreviate==true){	retVal = retVal.substring(0, 3)	}	// abbr to 3 chars
	return retVal;
}


Date.WeekdayName = function(p_Weekday, p_Abbreviate, p_FirstDayOfWeek){
	if(isNaN(p_Weekday)){	// v0.94- compat: extract real param from passed date
		if(!Date.CDate(p_Weekday)){	return "invalid weekday: '" + p_Weekday + "'";	}
		p_Weekday = DatePart("w", Date.CDate(p_Weekday));
	}
	p_FirstDayOfWeek = (isNaN(p_FirstDayOfWeek) || p_FirstDayOfWeek==0) ? vbSunday : parseInt(p_FirstDayOfWeek);	// set default & cast

	var nWeekdayNameIdx = ((p_FirstDayOfWeek-1 + parseInt(p_Weekday)-1 +7) % 7) + 1;	// compensate nWeekdayNameIdx for p_FirstDayOfWeek
	var retVal = Date.WeekdayNames[nWeekdayNameIdx];
	if(p_Abbreviate==true){	retVal = retVal.substring(0, 3)	}	// abbr to 3 chars
	return retVal;
}


// adjusts weekday for week starting on p_FirstDayOfWeek
Date.Weekday=function(p_Weekday, p_FirstDayOfWeek){	
	p_FirstDayOfWeek = (isNaN(p_FirstDayOfWeek) || p_FirstDayOfWeek==0) ? vbSunday : parseInt(p_FirstDayOfWeek);	// set default & cast

	return ((parseInt(p_Weekday) - p_FirstDayOfWeek +7) % 7) + 1;
}





Date.FormatDateTime = function(p_Date, p_NamedFormat){
	if(p_Date.toUpperCase().substring(0,3) == "NOW"){	p_Date = new Date()	};
	if(!Date.CDate(p_Date)){	return "invalid date: '" + p_Date + "'";	}
	if(isNaN(p_NamedFormat)){	p_NamedFormat = vbGeneralDate	};

	var dt = Date.CDate(p_Date);

	switch(parseInt(p_NamedFormat)){
		case vbGeneralDate: return dt.toString();
		case vbLongDate:		return Format(p_Date, 'DDDD, MMMM D, YYYY');
		case vbShortDate:		return Format(p_Date, 'MM/DD/YYYY');
		case vbLongTime:		return dt.toLocaleTimeString();
		case vbShortTime:		return Format(p_Date, 'HH:MM:SS');
		default:	return "invalid NamedFormat: '" + p_NamedFormat + "'";
	}
}


Date.Format = function(p_Date, p_Format, p_FirstDayOfWeek, p_firstweekofyear) {
	if(!Date.CDate(p_Date)){	return "invalid date: '" + p_Date + "'";	}
	if(!p_Format || p_Format==''){	return dt.toString()	};

	var dt = Date.CDate(p_Date);

	// Zero-padding formatter
	this.pad = function(p_str){
		if(p_str.toString().length==1){p_str = '0' + p_str}
		return p_str;
	}

	var ampm = dt.getHours()>=12 ? 'PM' : 'AM'
	var hr = dt.getHours();
	if (hr == 0){hr = 12};
	if (hr > 12) {hr -= 12};
	var strShortTime = hr +':'+ this.pad(dt.getMinutes()) +':'+ this.pad(dt.getSeconds()) +' '+ ampm;
	var strShortDate = (dt.getMonth()+1) +'/'+ dt.getDate() +'/'+ new String( dt.getFullYear() ).substring(2,4);
	var strLongDate = Date.MonthName(dt.getMonth()+1) +' '+ dt.getDate() +', '+ dt.getFullYear();		//

	var retVal = p_Format;
	
	// switch tokens whose alpha replacements could be accidentally captured
	retVal = retVal.replace( new RegExp('C', 'gi'), 'CCCC' ); 
	retVal = retVal.replace( new RegExp('mmmm', 'gi'), 'XXXX' );
	retVal = retVal.replace( new RegExp('mmm', 'gi'), 'XXX' );
	retVal = retVal.replace( new RegExp('dddddd', 'gi'), 'AAAAAA' ); 
	retVal = retVal.replace( new RegExp('ddddd', 'gi'), 'AAAAA' ); 
	retVal = retVal.replace( new RegExp('dddd', 'gi'), 'AAAA' );
	retVal = retVal.replace( new RegExp('ddd', 'gi'), 'AAA' );
	retVal = retVal.replace( new RegExp('timezone', 'gi'), 'ZZZZ' );
	retVal = retVal.replace( new RegExp('time24', 'gi'), 'TTTT' );
	retVal = retVal.replace( new RegExp('time', 'gi'), 'TTT' );

	// now do simple token replacements
	retVal = retVal.replace( new RegExp('yyyy', 'gi'), dt.getFullYear() );
	retVal = retVal.replace( new RegExp('yy', 'gi'), new String( dt.getFullYear() ).substring(2,4) );
	retVal = retVal.replace( new RegExp('y', 'gi'), Date.DatePart("y", dt) );
	retVal = retVal.replace( new RegExp('q', 'gi'), Date.DatePart("q", dt) );
	retVal = retVal.replace( new RegExp('mm', 'gi'), this.pad(dt.getMonth() + 1) );	
	retVal = retVal.replace( new RegExp('m', 'gi'), (dt.getMonth() + 1) );	
	retVal = retVal.replace( new RegExp('dd', 'gi'), this.pad(dt.getDate()) );
	retVal = retVal.replace( new RegExp('d', 'gi'), dt.getDate() );
	retVal = retVal.replace( new RegExp('hh', 'gi'), this.pad(dt.getHours()) );
	retVal = retVal.replace( new RegExp('h', 'gi'), dt.getHours() );
	retVal = retVal.replace( new RegExp('nn', 'gi'), this.pad(dt.getMinutes()) );
	retVal = retVal.replace( new RegExp('n', 'gi'), dt.getMinutes() );
	retVal = retVal.replace( new RegExp('ss', 'gi'), this.pad(dt.getSeconds()) ); 
	retVal = retVal.replace( new RegExp('s', 'gi'), dt.getSeconds() ); 
	retVal = retVal.replace( new RegExp('t t t t t', 'gi'), strShortTime ); 
	retVal = retVal.replace( new RegExp('am/pm', 'g'), dt.getHours()>=12 ? 'pm' : 'am');
	retVal = retVal.replace( new RegExp('AM/PM', 'g'), dt.getHours()>=12 ? 'PM' : 'AM');
	retVal = retVal.replace( new RegExp('a/p', 'g'), dt.getHours()>=12 ? 'p' : 'a');
	retVal = retVal.replace( new RegExp('A/P', 'g'), dt.getHours()>=12 ? 'P' : 'A');
	retVal = retVal.replace( new RegExp('AMPM', 'g'), dt.getHours()>=12 ? 'pm' : 'am');
	// (always proceed largest same-lettered token to smallest)

	// now finish the previously set-aside tokens 
	retVal = retVal.replace( new RegExp('XXXX', 'gi'), Date.MonthName(dt.getMonth()+1, false) );	//
	retVal = retVal.replace( new RegExp('XXX',  'gi'), Date.MonthName(dt.getMonth()+1, true ) );	//
	retVal = retVal.replace( new RegExp('AAAAAA', 'gi'), strLongDate ); 
	retVal = retVal.replace( new RegExp('AAAAA', 'gi'), strShortDate ); 
	retVal = retVal.replace( new RegExp('AAAA', 'gi'), Date.WeekdayName(dt.getDay()+1, false, p_FirstDayOfWeek) );	// 
	retVal = retVal.replace( new RegExp('AAA',  'gi'), Date.WeekdayName(dt.getDay()+1, true,  p_FirstDayOfWeek) );	// 
	retVal = retVal.replace( new RegExp('TTTT', 'gi'), dt.getHours() + ':' + this.pad(dt.getMinutes()) );
	retVal = retVal.replace( new RegExp('TTT',  'gi'), hr +':'+ this.pad(dt.getMinutes()) +' '+ ampm );
	retVal = retVal.replace( new RegExp('CCCC', 'gi'), strShortDate +' '+ strShortTime ); 

	// finally timezone
	tz = dt.getTimezoneOffset();
	timezone = (tz<0) ? ('GMT-' + tz/60) : (tz==0) ? ('GMT') : ('GMT+' + tz/60);
	retVal = retVal.replace( new RegExp('ZZZZ', 'gi'), timezone );

	return retVal;
}

Date.getPreviousSunday=function(p_Date) {
	retVal = Date.DateAdd("d", - p_Date.getDay() - 7, p_Date );
	return retVal;
}

// ====================================

/* if desired, map new methods to direct functions
*/
IsDate = Date.IsDate;
CDate = Date.CDate;
DateAdd = Date.DateAdd;
DateDiff = Date.DateDiff;
DatePart = Date.DatePart;
MonthName = Date.MonthName;
WeekdayName = Date.WeekdayName;
Weekday = Date.Weekday;
FormatDateTime = Date.FormatDateTime;
Format = Date.Format;
getPreviousSunday = Date.getPreviousSunday;


/* and other capitalizations for easier porting
isDate = IsDate;
dateAdd = DateAdd;
dateDiff = DateDiff;
datePart = DatePart;
monthName = MonthName;
weekdayName = WeekdayName;
formatDateTime = FormatDateTime;
format = Format;

isdate = IsDate;
dateadd = DateAdd;
datediff = DateDiff;
datepart = DatePart;
monthname = MonthName;
weekdayname = WeekdayName;
formatdatetime = FormatDateTime;

ISDATE = IsDate;
DATEADD = DateAdd;
DATEDIFF = DateDiff;
DATEPART = DatePart;
MONTHNAME = MonthName;
WEEKDAYNAME = WeekdayName;
FORMATDATETIME = FormatDateTime;
FORMAT = Format;
*/

 	  	 
/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/* json2.js */
/*----------------------------------------------------------------------------------------------------*/
/*
    http://www.JSON.org/json2.js
    2009-04-16

    Public Domain.

    NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.

    See http://www.JSON.org/js.html

    This file creates a global JSON object containing two methods: stringify
    and parse.

        JSON.stringify(value, replacer, space)
            value       any JavaScript value, usually an object or array.

            replacer    an optional parameter that determines how object
                        values are stringified for objects. It can be a
                        function or an array of strings.

            space       an optional parameter that specifies the indentation
                        of nested structures. If it is omitted, the text will
                        be packed without extra whitespace. If it is a number,
                        it will specify the number of spaces to indent at each
                        level. If it is a string (such as '\t' or '&nbsp;'),
                        it contains the characters used to indent at each level.

            This method produces a JSON text from a JavaScript value.

            When an object value is found, if the object contains a toJSON
            method, its toJSON method will be called and the result will be
            stringified. A toJSON method does not serialize: it returns the
            value represented by the name/value pair that should be serialized,
            or undefined if nothing should be serialized. The toJSON method
            will be passed the key associated with the value, and this will be
            bound to the object holding the key.

            For example, this would serialize Dates as ISO strings.

                Date.prototype.toJSON = function (key) {
                    function f(n) {
                        // Format integers to have at least two digits.
                        return n < 10 ? '0' + n : n;
                    }

                    return this.getUTCFullYear()   + '-' +
                         f(this.getUTCMonth() + 1) + '-' +
                         f(this.getUTCDate())      + 'T' +
                         f(this.getUTCHours())     + ':' +
                         f(this.getUTCMinutes())   + ':' +
                         f(this.getUTCSeconds())   + 'Z';
                };

            You can provide an optional replacer method. It will be passed the
            key and value of each member, with this bound to the containing
            object. The value that is returned from your method will be
            serialized. If your method returns undefined, then the member will
            be excluded from the serialization.

            If the replacer parameter is an array of strings, then it will be
            used to select the members to be serialized. It filters the results
            such that only members with keys listed in the replacer array are
            stringified.

            Values that do not have JSON representations, such as undefined or
            functions, will not be serialized. Such values in objects will be
            dropped; in arrays they will be replaced with null. You can use
            a replacer function to replace those with JSON values.
            JSON.stringify(undefined) returns undefined.

            The optional space parameter produces a stringification of the
            value that is filled with line breaks and indentation to make it
            easier to read.

            If the space parameter is a non-empty string, then that string will
            be used for indentation. If the space parameter is a number, then
            the indentation will be that many spaces.

            Example:

            text = JSON.stringify(['e', {pluribus: 'unum'}]);
            // text is '["e",{"pluribus":"unum"}]'


            text = JSON.stringify(['e', {pluribus: 'unum'}], null, '\t');
            // text is '[\n\t"e",\n\t{\n\t\t"pluribus": "unum"\n\t}\n]'

            text = JSON.stringify([new Date()], function (key, value) {
                return this[key] instanceof Date ?
                    'Date(' + this[key] + ')' : value;
            });
            // text is '["Date(---current time---)"]'


        JSON.parse(text, reviver)
            This method parses a JSON text to produce an object or array.
            It can throw a SyntaxError exception.

            The optional reviver parameter is a function that can filter and
            transform the results. It receives each of the keys and values,
            and its return value is used instead of the original value.
            If it returns what it received, then the structure is not modified.
            If it returns undefined then the member is deleted.

            Example:

            // Parse the text. Values that look like ISO date strings will
            // be converted to Date objects.

            myData = JSON.parse(text, function (key, value) {
                var a;
                if (typeof value === 'string') {
                    a =
/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2}(?:\.\d*)?)Z$/.exec(value);
                    if (a) {
                        return new Date(Date.UTC(+a[1], +a[2] - 1, +a[3], +a[4],
                            +a[5], +a[6]));
                    }
                }
                return value;
            });

            myData = JSON.parse('["Date(09/09/2001)"]', function (key, value) {
                var d;
                if (typeof value === 'string' &&
                        value.slice(0, 5) === 'Date(' &&
                        value.slice(-1) === ')') {
                    d = new Date(value.slice(5, -1));
                    if (d) {
                        return d;
                    }
                }
                return value;
            });


    This is a reference implementation. You are free to copy, modify, or
    redistribute.

    This code should be minified before deployment.
    See http://javascript.crockford.com/jsmin.html

    USE YOUR OWN COPY. IT IS EXTREMELY UNWISE TO LOAD CODE FROM SERVERS YOU DO
    NOT CONTROL.
*/

/*jslint evil: true */

/*global JSON */

/*members "", "\b", "\t", "\n", "\f", "\r", "\"", JSON, "\\", apply,
    call, charCodeAt, getUTCDate, getUTCFullYear, getUTCHours,
    getUTCMinutes, getUTCMonth, getUTCSeconds, hasOwnProperty, join,
    lastIndex, length, parse, prototype, push, replace, slice, stringify,
    test, toJSON, toString, valueOf
*/

// Create a JSON object only if one does not already exist. We create the
// methods in a closure to avoid creating global variables.

if (!this.JSON) {
    JSON = {};
}
(function () {

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return this.getUTCFullYear()   + '-' +
                 f(this.getUTCMonth() + 1) + '-' +
                 f(this.getUTCDate())      + 'T' +
                 f(this.getUTCHours())     + ':' +
                 f(this.getUTCMinutes())   + ':' +
                 f(this.getUTCSeconds())   + 'Z';
        };

        String.prototype.toJSON =
        Number.prototype.toJSON =
        Boolean.prototype.toJSON = function (key) {
            return this.valueOf();
        };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {

// If the string contains no control characters, no quote characters, and no
// backslash characters, then we can safely slap some quotes around it.
// Otherwise we must also replace the offending characters with safe escape
// sequences.

        escapable.lastIndex = 0;
        return escapable.test(string) ?
            '"' + string.replace(escapable, function (a) {
                var c = meta[a];
                return typeof c === 'string' ? c :
                    '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
            }) + '"' :
            '"' + string + '"';
    }


    function str(key, holder) {

// Produce a string from holder[key].

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];

// If the value has a toJSON method, call it to obtain a replacement value.

        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }

// If we were called with a replacer function, then call the replacer to
// obtain a replacement value.

        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }

// What happens next depends on the value's type.

        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

// JSON numbers must be finite. Encode non-finite numbers as null.

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':

// If the value is a boolean or null, convert it to a string. Note:
// typeof null does not produce 'null'. The case is included here in
// the remote chance that this gets fixed someday.

            return String(value);

// If the type is 'object', we might be dealing with an object or an array or
// null.

        case 'object':

// Due to a specification blunder in ECMAScript, typeof null is 'object',
// so watch out for that case.

            if (!value) {
                return 'null';
            }

// Make an array to hold the partial results of stringifying this object value.

            gap += indent;
            partial = [];

// Is the value an array?

            if (Object.prototype.toString.apply(value) === '[object Array]') {

// The value is an array. Stringify every element. Use null as a placeholder
// for non-JSON values.

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

// Join all of the elements together, separated with commas, and wrap them in
// brackets.

                v = partial.length === 0 ? '[]' :
                    gap ? '[\n' + gap +
                            partial.join(',\n' + gap) + '\n' +
                                mind + ']' :
                          '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

// If the replacer is an array, use it to select the members to be stringified.

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    k = rep[i];
                    if (typeof k === 'string') {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

// Otherwise, iterate through all of the keys in the object.

                for (k in value) {
                    if (Object.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

// Join all of the member texts together, separated with commas,
// and wrap them in braces.

            v = partial.length === 0 ? '{}' :
                gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' +
                        mind + '}' : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }

// If the JSON object does not yet have a stringify method, give it one.

    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

// The stringify method takes a value and an optional replacer, and an optional
// space parameter, and returns a JSON text. The replacer can be a function
// that can replace values, or an array of strings that will select the keys.
// A default replacer method can be provided. Use of the space parameter can
// produce text that is more easily readable.

            var i;
            gap = '';
            indent = '';

// If the space parameter is a number, make an indent string containing that
// many spaces.

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

// If the space parameter is a string, it will be used as the indent string.

            } else if (typeof space === 'string') {
                indent = space;
            }

// If there is a replacer, it must be a function or an array.
// Otherwise, throw an error.

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                     typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }

// Make a fake root object containing our value under the key of ''.
// Return the result of stringifying the value.

            return str('', {'': value});
        };
    }


// If the JSON object does not yet have a parse method, give it one.

    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

// The parse method takes a text and an optional reviver function, and returns
// a JavaScript value if the text is a valid JSON text.

            var j;

            function walk(holder, key) {

// The walk method is used to recursively walk the resulting structure so
// that modifications can be made.

                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }


// Parsing happens in four stages. In the first stage, we replace certain
// Unicode characters with escape sequences. JavaScript handles many characters
// incorrectly, either silently deleting them, or treating them as line endings.

            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }

// In the second stage, we run the text against regular expressions that look
// for non-JSON patterns. We are especially concerned with '()' and 'new'
// because they can cause invocation, and '=' because it can cause mutation.
// But just to be safe, we want to reject all unexpected forms.

// We split the second stage into 4 regexp operations in order to work around
// crippling inefficiencies in IE's and Safari's regexp engines. First we
// replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
// replace all simple value tokens with ']' characters. Third, we delete all
// open brackets that follow a colon or comma or that begin the text. Finally,
// we look to see that the remaining characters are only whitespace or ']' or
// ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.

            if (/^[\],:{}\s]*$/.
test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').
replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {

// In the third stage we use the eval function to compile the text into a
// JavaScript structure. The '{' operator is subject to a syntactic ambiguity
// in JavaScript: it can begin a block or an object literal. We wrap the text
// in parens to eliminate the ambiguity.

                j = eval('(' + text + ')');

// In the optional fourth stage, we recursively walk the new structure, passing
// each name/value pair to a reviver function for possible transformation.

                return typeof reviver === 'function' ?
                    walk({'': j}, '') : j;
            }

// If the text is not JSON parseable, then a SyntaxError is thrown.

            throw new SyntaxError('JSON.parse');
        };
    }
}());




/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/* calendrier.js */
/*----------------------------------------------------------------------------------------------------*/
var timer = null;
var OldDiv = "";
var TimerRunning = false;
// ## PARAMETRE D'AFFICHAGE du CALENDRIER ## //
//si enLigne est a true , le calendrier s'affiche sur une seule ligne,
//sinon il prend la taille spécifié par défaut;
 
var largeur = "210";
var separateur = "/";
 
// ##################### CONFIGURATION #####################
 
// ##- INITIALISATION DES VARIABLES -##
var calendrierSortie = '';
//Date actuelle
var today = '';
//Mois actuel
var current_month = '';
//Année actuelle
var current_year = '' ;
//Jours actuel
var current_day = '';
//Nombres de jours depuis le début de la semaine
var current_day_since_start_week = '';

//On initialise le nom des mois et le nom des jours en VF :)
var    month_name_FRE = new Array('Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre');
var    day_name_FRE = new Array('L','M','M','J','V','S','D');
var    month_name_ENG = new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
var    day_name_ENG = new Array('M','T','W','T','F','S','S');
var   month_name = null;
var   day_name = null;

//permet de récupèrer l'input sur lequel on a clické et de le remplir avec la date formatée
var myObjectClick = null;
//Classe qui sera détecté pour afficher le calendrier
var classMove = "moncalendrier";
//Variable permettant de savoir si on doit garder en mémoire le champs input clické
var lastInput = null;
//Div du calendrier
var div_calendar = "";
var year, month, day = "";
// ##################### FIN DE LA CONFIGURATION ##################### 

var timeDuChamps = 0;

var dformat = null;
 
//########################## Fonction permettant de remplacer "document.getElementById"  ##########################
function $get(element){
	return document.getElementById(element);
}
 
 
//Permet de faire glisser une div de la gauche vers la droite
function slideUp(bigMenu,smallMenu){
	//Si le timer n'est pas finit on détruit l'ancienne div
	if(parseInt($get(bigMenu).style.left) < 0){
		$get(bigMenu).style.left = parseInt($get(bigMenu).style.left) + 10 + "px";
		$get(smallMenu).style.left  =parseInt($get(smallMenu).style.left) + 10 + "px";
		timer = setTimeout('slideUp("'+bigMenu+'","'+smallMenu+'")',10);
	}
	else{
		clearTimeout(timer);
		TimerRunning = false;
		$get(smallMenu).parentNode.removeChild($get(smallMenu));
		//alert("timer up bien kill");
	}
}
 
//Permet de faire glisser une div de la droite vers la gauche
function slideDown(bigMenu,smallMenu){
	if(parseInt($get(bigMenu).style.left) > 0){
		$get(bigMenu).style.left = parseInt($get(bigMenu).style.left) - 10 + "px";
		$get(smallMenu).style.left =parseInt($get(smallMenu).style.left) - 10 + "px";
		timer = setTimeout('slideDown("'+bigMenu+'","'+smallMenu+'")',10);
	}
	else{
		clearTimeout(timer);
		TimerRunning = false;		
		//delete de l'ancienne
		$get(smallMenu).parentNode.removeChild($get(smallMenu));
		//alert("timer down bien kill");
	}
}
 
//Création d'une nouvelle div contenant les jours du calendrier
function CreateDivTempo(From){
	if(!TimerRunning){
	var DateTemp = new Date();
	IdTemp = DateTemp.getMilliseconds();
	var  NewDiv = document.createElement('DIV');
		 NewDiv.style.position = "absolute";
		 NewDiv.style.top = "0px";
		 NewDiv.style.width = "100%";
		 NewDiv.className = "ListeDate";
		 NewDiv.id = IdTemp;
		 //remplissage
		 NewDiv.innerHTML = CreateDayCalandar(year, month, day);
		 
	$get("Contenant_Calendar").appendChild(NewDiv);
	
		if(From == "left"){
			TimerRunning = true;
			NewDiv.style.left = "-"+largeur+"px";
			slideUp(NewDiv.id,OldDiv);
		}
		else if(From == "right"){
			TimerRunning = true;
			NewDiv.style.left = largeur+"px";
			slideDown(NewDiv.id,OldDiv);
		}
		else{
			"";
			NewDiv.style.left = 0+"px";
		}
		$get('Contenant_Calendar').style.height = NewDiv.offsetHeight+"px";
		$get('Contenant_Calendar').style.zIndex = "200";
		OldDiv = NewDiv.id;
	}
}
 

function drop(){
		 myObjectClick = null;
}
//########################## Fonction permettant de récupèrer la liste des classes d'un objet ##########################//
function getClassDrag(myObject){
	with(myObject){
		var x = className;
		listeClass = x.split(" ");
		//On parcours le tableau pour voir si l'objet est de type calendrier
		for(var i = 0 ; i < listeClass.length ; i++){
			if(listeClass[i] == classMove){
				myObjectClick = myObject;
				break;
			}
		}
	}
}
 
//########################## Pour combler un bug d'ie 6 on masque les select ########################## //
function masquerSelect(){
        var ua = navigator.userAgent.toLowerCase();
        var versionNav = parseFloat( ua.substring( ua.indexOf('msie ') + 5 ) );
        var isIE        = ( (ua.indexOf('msie') != -1) && (ua.indexOf('opera') == -1) && (ua.indexOf('webtv') == -1) );
 
        if(isIE && (versionNav < 7)){
	         svn=document.getElementsByTagName("SELECT");
             for (a=0;a<svn.length;a++){
                svn[a].style.visibility="hidden";
             }
        }
}
 
function montrerSelect(){
       var ua = navigator.userAgent.toLowerCase();
        var versionNav = parseFloat( ua.substring( ua.indexOf('msie ') + 5 ) );
        var isIE        = ( (ua.indexOf('msie') != -1) && (ua.indexOf('opera') == -1) && (ua.indexOf('webtv') == -1) );
        if(isIE && versionNav < 7){
	         svn=document.getElementsByTagName("SELECT");
             for (a=0;a<svn.length;a++){
                svn[a].style.visibility="visible";
             }
         }
}
 
function createFrame(){
	var newFrame = document.createElement('iframe');
	newFrame.style.width = largeur+"px";
	newFrame.style.height = div_calendar.offsetHeight+"px";
	newFrame.style.zIndex = "0";
	newFrame.frameBorder="0";
	newFrame.style.position = "absolute";
	newFrame.style.visibility = "hidden";
	newFrame.style.top = 0+"px";
	newFrame.style.left = 0+"px";
	div_calendar.appendChild(newFrame);
}
 
//######################## FONCTIONS PROPRE AU CALENDRIER ########################## //
//Fonction permettant de passer a l'annee précédente
function annee_precedente(){
 
	//On récupère l'annee actuelle puis on vérifit que l'on est pas en l'an 1 :-)
	if(current_year == 1){
		current_year = current_year;
	}
	else{
		current_year = current_year - 1 ;
	}
	//et on appel la fonction de génération de calendrier
	CreateDivTempo('left');
	//calendrier(	current_year , current_month, current_day);
}
 
//Fonction permettant de passer à l'annee suivante
function annee_suivante(){
	//Pas de limite pour l'ajout d'année
	current_year = current_year +1 ;
	//et on appel la fonction de génération de calendrier
	//calendrier(	current_year , current_month, current_day);
	CreateDivTempo('right');
}
 
//Fonction permettant de passer au mois précédent
function mois_precedent(){
 
	//On récupère le mois actuel puis on vérifit que l'on est pas en janvier sinon on enlève une année
	if(current_month == 0){
		current_month = 11;
		current_year = current_year - 1;
	}
	else{
		current_month = current_month - 1 ;
	}
	//et on appel la fonction de génération de calendrier
	CreateDivTempo('left');
	//calendrier(	current_year , current_month, current_day);
}
 
//Fonction permettant de passer au mois suivant
function mois_suivant(){
	//On récupère le mois actuel puis on vérifit que l'on est pas en janvier sinon on ajoute une année
	if(current_month == 11){
		current_month = 0;
		current_year = current_year  + 1;
	}
	else{
		current_month = current_month + 1;
	}
	//et on appel la fonction de génération de calendrier
	//calendrier(	current_year , current_month, current_day);
	CreateDivTempo('right');
}
 
//Fonction principale qui génère le calendrier
//Elle prend en paramètre, l'année , le mois , et le jour
//Si l'année et le mois ne sont pas renseignés , la date courante est affecté par défaut
function calendrier(year, month, day){
 	//Aujourd'hui si month et year ne sont pas renseignés
	if(month == null || year == null){
		today = new Date();
	}
	else{
		//month = month - 1;
		//Création d'une date en fonction de celle passée en paramètre
		today = new Date(year, month , day);
	}
 
	//Mois actuel
	current_month = today.getMonth()
	
	//Année actuelle
	current_year = today.getFullYear();
	
	//Jours actuel
	current_day = today.getDate();
	
	
	//######################## ENTETE ########################//
	//Ligne permettant de changer l'année et de mois
	var month_bef = "<a href=\"javascript:mois_precedent()\" style=\"position:absolute;left:30px;z-index:200;\" > < </a>";
	var month_next = "<a href=\"javascript:mois_suivant()\" style=\"position:absolute;right:30px;z-index:200;\"> > </a>";
	var year_next = "<a href=\"javascript:annee_suivante()\" style=\"position:absolute;right:5px;z-index:200;\" >&nbsp;&nbsp; > > </a>";
	var year_bef = "<a href=\"javascript:annee_precedente()\" style=\"position:absolute;left:5px;z-index:200;\"  > < < &nbsp;&nbsp;</a>";
	calendrierSortie = "<p class=\"titleMonth\" style=\"position:relative;z-index:200;\"> <a href=\"javascript:alimenterChamps('')\" style=\"float:left;margin-left:3px;color:#cccccc;font-size:10px;z-index:200;\"> Clear </a><a href=\"javascript:masquerCalendrier()\" style=\"float:right;margin-right:3px;color:red;font-weight:bold;font-size:12px;z-index:200;\">X</a>&nbsp;</p>";
	//On affiche le mois et l'année en titre

                if (dformat=='dmy') {
                    month_name = month_name_FRE;
                    day_name = day_name_FRE;
                } else {
                    month_name = month_name_ENG;
                    day_name = day_name_ENG;
                }

        calendrierSortie += "<p class=\"titleMonth\" style=\"float:left;position:relative;z-index:200;\">" + year_next + year_bef+  month_bef + "<span id=\"curentDateString\">" + month_name[current_month]+ " "+ current_year +"</span>"+ month_next+"</p><div id=\"Contenant_Calendar\">";
	//######################## FIN ENTETE ########################//
	
	//Si aucun calendrier n'a encore été crée :
	if(!document.getElementById("calendrier")){
		//On crée une div dynamiquement, en absolute, positionné sous le champs input
		div_calendar = document.createElement("div");
		
		//On lui attribut un id
		div_calendar.setAttribute("id","calendrier");
		
		//On définit les propriétés de cette div ( id et classe ) 
		div_calendar.className = "calendar";
		
		//Pour ajouter la div dans le document
		var mybody = document.getElementsByTagName("body")[0];
		
		//Pour finir on ajoute la div dans le document
		mybody.appendChild(div_calendar);
	}
	else{
			div_calendar = document.getElementById("calendrier");
	}
	
	//On insèrer dans la div, le contenu du calendrier généré
	//On assigne la taille du calendrier de façon dynamique ( on ajoute 10 px pour combler un bug sous ie )
	var width_calendar = largeur+"px";
 	//Ajout des éléments dans le calendrier
	calendrierSortie = calendrierSortie + "</div><div class=\"separator\"></div>";
	div_calendar.innerHTML = calendrierSortie;
	div_calendar.style.width = width_calendar;
	//On remplit le calendrier avec les jours
//	alert(CreateDayCalandar(year, month, day));
	CreateDivTempo('');
}
 
function CreateDayCalandar(){
	
	// On récupère le premier jour de la semaine du mois
	var dateTemp = new Date(current_year, current_month,1);
	
	//test pour vérifier quel jour était le prmier du mois
	current_day_since_start_week = (( dateTemp.getDay()== 0 ) ? 6 : dateTemp.getDay() - 1);
	
	//variable permettant de vérifier si l'on est déja rentré dans la condition pour éviter une boucle infinit
	var verifJour = false;
	
	//On initialise le nombre de jour par mois
	var nbJoursfevrier = (current_year % 4) == 0 ? 29 : 28;
	//Initialisation du tableau indiquant le nombre de jours par mois
	var day_number = new Array(31,nbJoursfevrier,31,30,31,30,31,31,30,31,30,31);
	
	var x = 0
	
	//On initialise la ligne qui comportera tous les noms des jours depuis le début du mois
	var list_day = '';
	var day_calendar = '';
	//On remplit le calendrier avec le nombre de jour, en remplissant les premiers jours par des champs vides
	for(var nbjours = 0 ; nbjours < (day_number[current_month] + current_day_since_start_week) ; nbjours++){
		
		// On boucle tous les 7 jours pour créer la ligne qui comportera le nom des jours en fonction des<br />
		// paramètres d'affichage
		if(verifJour == false){
			for(x = 0 ; x < 7 ; x++){
				if(x == 6){
					list_day += "<span>" + day_name[x] + "</span>";
				}
				else{
					list_day += "<span>" + day_name[x] + "</span>";
				}
			}
			verifJour = true;
		}
		//et enfin on ajoute les dates au calendrier
		//Pour gèrer les jours "vide" et éviter de faire une boucle on vérifit que le nombre de jours corespond bien au
		//nombre de jour du mois
		if(nbjours < day_number[current_month]){
			if(current_day == (nbjours+1)){
				day_calendar += "<span onclick=\"alimenterChamps(this.innerHTML)\" class=\"currentDay DayDate\">" + (nbjours+1) + "</span>";
			}
			else{
				day_calendar += "<span class=\"DayDate\" onclick=\"alimenterChamps(this.innerHTML)\">" + (nbjours+1) + "</span>";
			}
		}
	}
 
	//On ajoute les jours "vide" du début du mois
	for(i  = 0 ; i < current_day_since_start_week ; i ++){
		day_calendar = "<span>&nbsp;</span>" + day_calendar;
	}
	//On met également a jour le mois et l'année
	$get('curentDateString').innerHTML = month_name[current_month]+ " "+ current_year;
	return (list_day  + day_calendar);
}
 
function initialiserCalendrier(objetClick){
		//on affecte la variable définissant sur quel input on a clické
		myObjectClick = objetClick;
		
		if(myObjectClick.disabled != true){
		    //On vérifie que le champs n'est pas déja remplit, sinon on va se positionner sur la date du champs
		    if(myObjectClick.value != ''){
                dformat=getDateFormat();
                if (dformat=='mdy') {
                    month_name = month_name_FRE;
                    day_name = day_name_FRE;
                } else {
                    month_name = month_name_ENG;
                    day_name = day_name_ENG;
                }

          // trim blanks
          var s = trim (myObjectClick.value); 

          var date_time = s.split(" ");
        	var dateDuChamps = date_time[0];
        	if (date_time[1] != null)
	            timeDuChamps = date_time[1];

			    //On utilise la chaine de separateur
					var reg=new RegExp("/", "g");
					var tableau=dateDuChamps.split(reg);
					if (dformat=='mdy')
                        calendrier(	tableau[2] ,tableau[0] - 1 , tableau[1]);
					else
                        calendrier(	tableau[2] , tableau[1] - 1 , tableau[0]);
		    }
		    else{
			    //on créer le calendrier
			    calendrier(objetClick);
				
 
		    }
		    //puis on le positionne par rapport a l'objet sur lequel on a clické
		    //positionCalendar(objetClick);
		    positionCalendar(objetClick);
		    //fadePic();
		    masquerSelect();
			createFrame();
		}
 
}
 
 //Fonction permettant de trouver la position de l'élément ( input ) pour pouvoir positioner le calendrier
function ds_getleft(el) {
	var tmp = el.offsetLeft;
	el = el.offsetParent
	while(el) {
		tmp += el.offsetLeft;
		el = el.offsetParent;
	}
	return tmp;
}
 
function ds_gettop(el) {
	var tmp = el.offsetTop;
	el = el.offsetParent
	while(el) {
		tmp += el.offsetTop;
		el = el.offsetParent;
	}
	return tmp;
}
 
//fonction permettant de positioner le calendrier
function positionCalendar(objetParent){
	//document.getElementById('calendrier').style.left = ds_getleft(objetParent) + "px";
	document.getElementById('calendrier').style.left = ds_getleft(objetParent) + "px";
	//document.getElementById('calendrier').style.top = ds_gettop(objetParent) + 20 + "px" ;
	document.getElementById('calendrier').style.top = ds_gettop(objetParent) + 20 + "px" ;
	// et on le rend visible
	document.getElementById('calendrier').style.visibility = "visible";
}
//Fonction permettant d'alimenter le champs
function alimenterChamps(daySelect){
		if(daySelect != ''){
            var dformat=getDateFormat();
            if (timeDuChamps!=0)
                tmStr = ' ' + timeDuChamps;
            else tmStr = '';
            if (dformat=='mdy')
			    lastInput.value= formatInfZero((current_month+1)) + separateur + formatInfZero(daySelect) + separateur +current_year + tmStr;
		    else
			    lastInput.value= formatInfZero(daySelect) + separateur + formatInfZero((current_month+1)) + separateur +current_year + tmStr;
		}
		else{
			lastInput.value = '';
		}
		masquerCalendrier();
}
function masquerCalendrier(){
		//fadePic();
		document.getElementById('calendrier').style.visibility = "hidden";
		montrerSelect();
}
 
function formatInfZero(numberFormat){
		if(parseInt(numberFormat) < 10){
				numberFormat = "0"+numberFormat;
		}
		
		return numberFormat;
}
 
function CreateSpan(){
	var spanTemp = document.createElement("span");
		spanTemp.className = "";
		spanTemp.innerText = "";
		spanTemp.onClick = "";
	return spanTemp;
}
 
//######################## FONCTION PERMETTANT D'AFFICHER LE CALENDRIER DE FA9ON PROGRESSIVE ########################//
var max = 100;
var min = 0;
var opacite=min;
up=true;
var IsIE=!!document.all;
 
 
function fadePic(){
try{		
				var ThePic=document.getElementById("calendrier");
				if (opacite < max && up){opacite+=5;}
				if (opacite>min && !up){opacite-=5;}
				IsIE?ThePic.filters[0].opacity=opacite:document.getElementById("calendrier").style.opacity=opacite/100;
				
				if(opacite<max && up){
					timer = setTimeout('fadePic()',10);
				}
				else if(opacite>min && !up){
					timer = setTimeout('fadePic()',10);
				}
				else{
					if (opacite==max){up=false;}
					if (opacite<=min){up=true;}
					clearTimeout(timer);
				}
}
catch(error){
	alert(error.message);
}
}

function getDateFormat () {
	var format=document.getElementsByName('DFormat')[0].value;
	return format;
}


function dispcalend(field) {
	var elem=document.getElementsByName(field)[0];
	initialiserCalendrier(elem);
	lastInput=elem;
}





/*>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>*/
/* parsedate.js */
/*----------------------------------------------------------------------------------------------------*/
// ===================================================================
// Author: Matt Kruse <matt@mattkruse.com>
// WWW: http://www.mattkruse.com/
//
// NOTICE: You may use this code for any purpose, commercial or
// private, without any further permission from the author. You may
// remove this notice from your final code if you wish, however it is
// appreciated by the author if at least my web site address is kept.
//
// You may *NOT* re-distribute this code in any way except through its
// use. That means, you can include it in your product, or your web
// site, or any other form where the code is actually being used. You
// may not put the plain javascript up on your site for download or
// include it in your javascript libraries for download. 
// If you wish to share this code with others, please just point them
// to the URL instead.
// Please DO NOT link directly to my .js files from your site. Copy
// the files to your server and use them there. Thank you.
// ===================================================================

// HISTORY
// ------------------------------------------------------------------
// May 17, 2003: Fixed bug in parseDate() for dates <1970
// March 11, 2003: Added parseDate() function
// March 11, 2003: Added "NNN" formatting option. Doesn't match up
//                 perfectly with SimpleDateFormat formats, but 
//                 backwards-compatability was required.

// ------------------------------------------------------------------
// These functions use the same 'format' strings as the 
// java.text.SimpleDateFormat class, with minor exceptions.
// The format string consists of the following abbreviations:
// 
// Field        | Full Form          | Short Form
// -------------+--------------------+-----------------------
// Year         | yyyy (4 digits)    | yy (2 digits), y (2 or 4 digits)
// Month        | MMM (name or abbr.)| MM (2 digits), M (1 or 2 digits)
//              | NNN (abbr.)        |
// Day of Month | dd (2 digits)      | d (1 or 2 digits)
// Day of Week  | EE (name)          | E (abbr)
// Hour (1-12)  | hh (2 digits)      | h (1 or 2 digits)
// Hour (0-23)  | HH (2 digits)      | H (1 or 2 digits)
// Hour (0-11)  | KK (2 digits)      | K (1 or 2 digits)
// Hour (1-24)  | kk (2 digits)      | k (1 or 2 digits)
// Minute       | mm (2 digits)      | m (1 or 2 digits)
// Second       | ss (2 digits)      | s (1 or 2 digits)
// AM/PM        | a                  |
//
// NOTE THE DIFFERENCE BETWEEN MM and mm! Month=MM, not mm!
// Examples:
//  "MMM d, y" matches: January 01, 2000
//                      Dec 1, 1900
//                      Nov 20, 00
//  "M/d/yy"   matches: 01/20/00
//                      9/2/00
//  "MMM dd, yyyy hh:mm:ssa" matches: "January 01, 2000 12:30:45AM"
// ------------------------------------------------------------------

var MONTH_NAMES=new Array('January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
var DAY_NAMES=new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sun','Mon','Tue','Wed','Thu','Fri','Sat');
function LZ(x) {return(x<0||x>9?"":"0")+x}

// ------------------------------------------------------------------
// isDate ( date_string, format_string )
// Returns true if date string matches format of format string and
// is a valid date. Else returns false.
// It is recommended that you trim whitespace around the value before
// passing it to this function, as whitespace is NOT ignored!
// ------------------------------------------------------------------
function isDate(val,format) {
	var date=getDateFromFormat(val,format);
	if (date==0) { return false; }
	return true;
	}

// -------------------------------------------------------------------
// compareDates(date1,date1format,date2,date2format)
//   Compare two date strings to see which is greater.
//   Returns:
//   1 if date1 is greater than date2
//   0 if date2 is greater than date1 of if they are the same
//  -1 if either of the dates is in an invalid format
// -------------------------------------------------------------------
function compareDates(date1,dateformat1,date2,dateformat2) {
	var d1=getDateFromFormat(date1,dateformat1);
	var d2=getDateFromFormat(date2,dateformat2);
	if (d1==0 || d2==0) {
		return -1;
		}
	else if (d1 > d2) {
		return 1;
		}
	return 0;
	}

// ------------------------------------------------------------------
// formatDate (date_object, format)
// Returns a date in the output format specified.
// The format string uses the same abbreviations as in getDateFromFormat()
// ------------------------------------------------------------------
function formatDate(date,format) {
	format=format+"";
	var result="";
	var i_format=0;
	var c="";
	var token="";
	var y=date.getYear()+"";
	var M=date.getMonth()+1;
	var d=date.getDate();
	var E=date.getDay();
	var H=date.getHours();
	var m=date.getMinutes();
	var s=date.getSeconds();
	var yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;
	// Convert real date parts into formatted versions
	var value=new Object();
	if (y.length < 4) {y=""+(y-0+1900);}
	value["y"]=""+y;
	value["yyyy"]=y;
	value["yy"]=y.substring(2,4);
	value["M"]=M;
	value["MM"]=LZ(M);
	value["MMM"]=MONTH_NAMES[M-1];
	value["NNN"]=MONTH_NAMES[M+11];
	value["d"]=d;
	value["dd"]=LZ(d);
	value["E"]=DAY_NAMES[E+7];
	value["EE"]=DAY_NAMES[E];
	value["H"]=H;
	value["HH"]=LZ(H);
	if (H==0){value["h"]=12;}
	else if (H>12){value["h"]=H-12;}
	else {value["h"]=H;}
	value["hh"]=LZ(value["h"]);
	if (H>11){value["K"]=H-12;} else {value["K"]=H;}
	value["k"]=H+1;
	value["KK"]=LZ(value["K"]);
	value["kk"]=LZ(value["k"]);
	if (H > 11) { value["a"]="PM"; }
	else { value["a"]="AM"; }
	value["m"]=m;
	value["mm"]=LZ(m);
	value["s"]=s;
	value["ss"]=LZ(s);
	while (i_format < format.length) {
		c=format.charAt(i_format);
		token="";
		while ((format.charAt(i_format)==c) && (i_format < format.length)) {
			token += format.charAt(i_format++);
			}
		if (value[token] != null) { result=result + value[token]; }
		else { result=result + token; }
		}
	return result;
	}
	
// ------------------------------------------------------------------
// Utility functions for parsing in getDateFromFormat()
// ------------------------------------------------------------------
function _isInteger(val) {
	var digits="1234567890";
	for (var i=0; i < val.length; i++) {
		if (digits.indexOf(val.charAt(i))==-1) { return false; }
		}
	return true;
	}
function _getInt(str,i,minlength,maxlength) {
	for (var x=maxlength; x>=minlength; x--) {
		var token=str.substring(i,i+x);
		if (token.length < minlength) { return null; }
		if (_isInteger(token)) { return token; }
		}
	return null;
	}
	
// ------------------------------------------------------------------
// getDateFromFormat( date_string , format_string )
//
// This function takes a date string and a format string. It matches
// If the date string matches the format string, it returns the 
// getTime() of the date. If it does not match, it returns 0.
// ------------------------------------------------------------------
function getDateFromFormat(val,format) {
	val=val+"";
	format=format+"";
	var i_val=0;
	var i_format=0;
	var c="";
	var token="";
	var token2="";
	var x,y;
	var now=new Date();
	var year=now.getYear();
	var month=now.getMonth()+1;
	var date=1;
	var hh=now.getHours();
	var mm=now.getMinutes();
	var ss=now.getSeconds();
	var ampm="";
	
	while (i_format < format.length) {
		// Get next token from format string
		c=format.charAt(i_format);
		token="";
		while ((format.charAt(i_format)==c) && (i_format < format.length)) {
			token += format.charAt(i_format++);
			}
		// Extract contents of value based on format token
		if (token=="yyyy" || token=="yy" || token=="y") {
			if (token=="yyyy") { x=4;y=4; }
			if (token=="yy")   { x=2;y=2; }
			if (token=="y")    { x=2;y=4; }
			year=_getInt(val,i_val,x,y);
			if (year==null) { return 0; }
			i_val += year.length;
			if (year.length==2) {
				if (year > 70) { year=1900+(year-0); }
				else { year=2000+(year-0); }
				}
			}
		else if (token=="MMM"||token=="NNN"){
			month=0;
			for (var i=0; i<MONTH_NAMES.length; i++) {
				var month_name=MONTH_NAMES[i];
				if (val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()) {
					if (token=="MMM"||(token=="NNN"&&i>11)) {
						month=i+1;
						if (month>12) { month -= 12; }
						i_val += month_name.length;
						break;
						}
					}
				}
			if ((month < 1)||(month>12)){return 0;}
			}
		else if (token=="EE"||token=="E"){
			for (var i=0; i<DAY_NAMES.length; i++) {
				var day_name=DAY_NAMES[i];
				if (val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()) {
					i_val += day_name.length;
					break;
					}
				}
			}
		else if (token=="MM"||token=="M") {
			month=_getInt(val,i_val,token.length,2);
			if(month==null||(month<1)||(month>12)){return 0;}
			i_val+=month.length;}
		else if (token=="dd"||token=="d") {
			date=_getInt(val,i_val,token.length,2);
			if(date==null||(date<1)||(date>31)){return 0;}
			i_val+=date.length;}
		else if (token=="hh"||token=="h") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<1)||(hh>12)){return 0;}
			i_val+=hh.length;}
		else if (token=="HH"||token=="H") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<0)||(hh>23)){return 0;}
			i_val+=hh.length;}
		else if (token=="KK"||token=="K") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<0)||(hh>11)){return 0;}
			i_val+=hh.length;}
		else if (token=="kk"||token=="k") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<1)||(hh>24)){return 0;}
			i_val+=hh.length;hh--;}
		else if (token=="mm"||token=="m") {
			mm=_getInt(val,i_val,token.length,2);
			if(mm==null||(mm<0)||(mm>59)){return 0;}
			i_val+=mm.length;}
		else if (token=="ss"||token=="s") {
			ss=_getInt(val,i_val,token.length,2);
			if(ss==null||(ss<0)||(ss>59)){return 0;}
			i_val+=ss.length;}
		else if (token=="a") {
			if (val.substring(i_val,i_val+2).toLowerCase()=="am") {ampm="AM";}
			else if (val.substring(i_val,i_val+2).toLowerCase()=="pm") {ampm="PM";}
			else {return 0;}
			i_val+=2;}
		else {
			if (val.substring(i_val,i_val+token.length)!=token) {return 0;}
			else {i_val+=token.length;}
			}
		}
	// If there are any trailing characters left in the value, it doesn't match
	if (i_val != val.length) { return 0; }
	// Is date valid for month?
	if (month==2) {
		// Check for leap year
		if ( ( (year%4==0)&&(year%100 != 0) ) || (year%400==0) ) { // leap year
			if (date > 29){ return 0; }
			}
		else { if (date > 28) { return 0; } }
		}
	if ((month==4)||(month==6)||(month==9)||(month==11)) {
		if (date > 30) { return 0; }
		}
	// Correct hours value
	if (hh<12 && ampm=="PM") { hh=hh-0+12; }
	else if (hh>11 && ampm=="AM") { hh-=12; }
	var newdate=new Date(year,month-1,date,hh,mm,ss);
	return newdate.getTime();
	}

// ------------------------------------------------------------------
// parseDate( date_string [, prefer_euro_format] )
//
// This function takes a date string and tries to match it to a
// number of possible date formats to get the value. It will try to
// match against the following international formats, in this order:
// y-M-d   MMM d, y   MMM d,y   y-MMM-d   d-MMM-y  MMM d
// M/d/y   M-d-y      M.d.y     MMM-d     M/d      M-d
// d/M/y   d-M-y      d.M.y     d-MMM     d/M      d-M
// A second argument may be passed to instruct the method to search
// for formats like d/M/y (european format) before M/d/y (American).
// Returns a Date object or null if no patterns match.
// ------------------------------------------------------------------
function parseDate(val) {
	var preferEuro=(arguments.length==2)?arguments[1]:false;
	generalFormats=new Array('y-M-d','MMM d, y','MMM d,y','y-MMM-d','d-MMM-y','MMM d');
	monthFirst=new Array('M/d/y','M-d-y','M.d.y','MMM-d','M/d','M-d');
	dateFirst =new Array('d/M/y','d-M-y','d.M.y','d-MMM','d/M','d-M');
	var checkList=new Array('generalFormats',preferEuro?'dateFirst':'monthFirst',preferEuro?'monthFirst':'dateFirst');
	var d=null;
	for (var i=0; i<checkList.length; i++) {
		var l=window[checkList[i]];
		for (var j=0; j<l.length; j++) {
			d=getDateFromFormat(val,l[j]);
			if (d!=0) { return new Date(d); }
			}
		}
	return null;
	}
