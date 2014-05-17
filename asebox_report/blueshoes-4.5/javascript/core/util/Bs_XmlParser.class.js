/********************************************************************************************
* BlueShoes Framework; This file is part of the php application framework.
* NOTE: This code is stripped (obfuscated). To get the clean documented code goto 
*       www.blueshoes.org and register for the free open source *DEVELOPER* version or 
*       buy the commercial version.
*       
*       In case you've already got the developer version, then this is one of the few 
*       packages/classes that is only available to *PAYING* customers.
*       To get it go to www.blueshoes.org and buy a commercial version.
* 
* @copyright www.blueshoes.org
* @author    Samuel Blume <sam at blueshoes dot org>
* @author    Andrej Arn <andrej at blueshoes dot org>
*/
function Bs_XmlParser () {
this._index;this._debugOn = false;this._debug = new Array();this._stackStr = '';this._stackPos = 0;this._expandFromStack = function(xmlFragment) {
if (this._stackPos < this._stackStr.length) {
var numChars = 1000;xmlFragment.str += this._stackStr.substr(this._stackPos, numChars);this._stackPos += numChars;}
return xmlFragment;}
this.parse = function(xmlInput) {
this._index = new Array();  	var xml = xmlInput.replace(/\r(\n)?/g,"\n");
var xmlFragment = new _Bs_XmlParserStrFragment();this._stackStr  = this._stripXmlHeader(xml);this._stackPos  = 2000;xmlFragment.str = this._stackStr.substr(0, 2000);xmlFragment = this._parseRecursive(xmlFragment);this.root = new _Bs_XmlParserElement();this.root.name = 'ROOT';this.root.children = xmlFragment.list;this.root.index = this._index;return this.root;}
this._stripXmlHeader = function(xml) {
var start_p = -1;var end_p = -1;start_p = xml.indexOf("<");if('<?x' == xml.substring(start_p, start_p +3).toLowerCase()) {
end_p = xml.indexOf("?>");xml = xml.substring(end_p +2, xml.length);}
start_p = xml.indexOf("<!DOCTYPE");if(start_p != -1) {
end_p = xml.indexOf(">", start_p) +1;var dp = xml.indexOf("[", start_p);if(dp < end_p && dp != -1) {
end_p = xml.indexOf("]>", start_p) +2;}
xml = xml.substring(end_p, xml.length);}
return this._trim(xml);}
this._parseRecursive = function(xmlFragment) {
var regExStartTag = new RegExp("^\s*<", "i");do {
if (this._debugOn) {
this._debug[this._debug.length] = '<hr>' + this._entity(xmlFragment.str);}
var lastFoundPos = 0;var lastPos      = 0;for (var i=0; i<3; i++) {
lastPos = xmlFragment.str.indexOf("<", lastFoundPos);if (lastPos == -1) {
xmlFragment = this._expandFromStack(xmlFragment);i--;} else {
lastFoundPos = lastPos;}
}
for (var i=0; i>=0; i++) {
lastPos = xmlFragment.str.indexOf(">", lastFoundPos);if (lastPos == -1) {
var lastLength = xmlFragment.str.length;xmlFragment = this._expandFromStack(xmlFragment);if (lastLength >= xmlFragment.str.length) {
break;}
} else {
break;}
}
var start_p = xmlFragment.str.search(regExStartTag);if (start_p != -1) {
if (xmlFragment.str.substring(start_p+1,2) == "?") {
xmlFragment = this._tag_pi(xmlFragment);} else if (xmlFragment.str.substring(start_p+1,4) == "!--") {
xmlFragment = this._tag_comment(xmlFragment);} else if (xmlFragment.str.substring(start_p+1,9) == "![CDATA[") {
xmlFragment = this._tag_cdata(xmlFragment);}	else {
var regEx = new RegExp("^\s*</"+xmlFragment.end+"\s*>", "ig");          var result = xmlFragment.str.match(regEx);
if (this._debugOn) {
var strResult = (null != result) ?  "Found:"+ this._entity(result[0]) : '[Not Found]';this._debug[this._debug.length] = "<hr><b>94: Looking for " + this._entity('^\s*</'+xmlFragment.end+'\s*>')  +" Result is " + strResult + '</b><br>';}
if (null != result) {
xmlFragment.str = xmlFragment.str.substring(result[0].length);xmlFragment.end = "";return xmlFragment;}	else {
xmlFragment = this._tag_element(xmlFragment);}
}
} else {
var start_p = xmlFragment.str.indexOf("<");var tmpObj = new _Bs_XmlParserElement();tmpObj.type = 'chardata';if (start_p == -1) {
tmpObj.value = this._trimL(xmlFragment.str);xmlFragment.str = "";} else {
tmpObj.value = this._trimL(xmlFragment.str.substring(0,start_p));xmlFragment.str = xmlFragment.str.substring(start_p);}
xmlFragment.list[xmlFragment.list.length] = tmpObj;this._index[this._index.length] = tmpObj;}
if (xmlFragment.str.length == 0) {
var lastLength = xmlFragment.str.length;xmlFragment = this._expandFromStack(xmlFragment);if (lastLength >= xmlFragment.str.length) {
break;}
}
} while (true);return xmlFragment;}
this._tag_element = function(xmlFragment) {
var endMatch = ">";var end_p = xmlFragment.str.indexOf(endMatch);var isShortTag = (xmlFragment.str.substring(end_p-1,end_p) == "/");var xmlTag = '';if (isShortTag) {
xmlTag = this._normalize(xmlFragment.str.substring(1, end_p-1));} else {
xmlTag = this._normalize(xmlFragment.str.substring(1, end_p));}
    var parts = xmlTag.match(/(\w+)(.*)/);
var tmpObj = new _Bs_XmlParserElement();tmpObj.type = 'element';tmpObj.name = parts[1].toLowerCase();tmpObj.attributes = this._extractAttributes(parts[2]);var currentPos = xmlFragment.list.length;xmlFragment.list[currentPos] = tmpObj;this._index[this._index.length] = tmpObj;if (!isShortTag) {
switch (tmpObj.name.toLowerCase()) {
case 'br':
case 'img':
case 'hr':
case 'link':
case 'meta':
isShortTag = true;break;}
}
if (isShortTag) {
xmlFragment.str = xmlFragment.str.substring(end_p+1);} else {
var nextFragment = new _Bs_XmlParserStrFragment();if (this._debugOn) {
this._debug[this._debug.length] = "<hr><b>160:Processing:"+ tmpObj.name + '</b>';}
nextFragment.str = xmlFragment.str.substring(end_p+1);nextFragment.end = tmpObj.name;nextFragment = this._parseRecursive(nextFragment);xmlFragment.list[currentPos].children = nextFragment.list;xmlFragment.str = nextFragment.str;}
return xmlFragment;}
this._tag_comment = function(xmlFragment) {
var endMatch = "-->";var end_p = xmlFragment.str.indexOf(endMatch);var tmpObj = new _Bs_XmlParserElement();tmpObj.type = 'comment';tmpObj.value = xmlFragment.str.substring(4, end_p);xmlFragment.list[xmlFragment.list.length] = tmpObj;this._index[this._index.length] = tmpObj;xmlFragment.str = xmlFragment.str.substring(end_p + endMatch.length);return xmlFragment;}
this._tag_pi = function(xmlFragment) {
var endMatch = "?>";var end_p = xmlFragment.str.indexOf(endMatch);var tmpObj = new _Bs_XmlParserElement();tmpObj.type = 'pi';tmpObj.value = xmlFragment.str.substring(2, end_p);this._index[this._index.length] = tmpObj;xmlFragment.list[xmlFragment.list.length] = tmpObj;xmlFragment.str = xmlFragment.str.substring(end_p + endMatch.length);return xmlFragment;}
this._tag_cdata = function(xmlFragment) {
var endMatch = "]]>";var end_p = xmlFragment.str.indexOf(endMatch);var tmpObj = new _Bs_XmlParserElement();tmpObj.type = 'chardata';tmpObj.value = xmlFragment.str.substring(9, end_p);xmlFragment.list[xmlFragment.list.length] = tmpObj;this._index[this._index.length] = tmpObj;xmlFragment.str = xmlFragment.str.substring(end_p + endMatch.length);return xmlFragment;}
this._extractAttributes = function(str) {
var tmp = '';var retObj = new Object();var attrStr = this._trim(str);if (0 == attrStr.length) return retObj;    attrStr = attrStr.replace(/\s*=\s*/g, '=');
    attrStr = attrStr.replace(/\=(')[^']*/g, this._spaceReplacer); // second param is a function call !
    attrStr = attrStr.replace(/\=(")[^"]*/g, this._spaceReplacer);
var parts = attrStr.split(/\s+/);if (0 == parts.length) return null;for (var i=0; i<parts.length; i++) {
if (-1 == parts[i].indexOf('=')) {
retObj[parts[i]] = true;} else {
var p = parts[i].split('=');        p[1] = p[1].match(/^(['"]?)(.*)/)[2];
retObj[p[0].toLowerCase()] = this._trim(this._unspaceReplacer(p[1]));}
}
return retObj;}
this._trim = function(input) {
    var ret = input.replace(/^\s*/, '');
    return ret.replace(/\s*$/, '');
}
this._trimL = function(input) {
    return input.replace(/^\s*/, '');
}
this._normalize = function(input) {
    return input.replace(/[\n\t]/g, ' ');
}
this._strip = function(input) {
    return input.replace(/\s*/g, '');
}
this._entity  = function (input) {
    return input.replace(/&/g,'&amp;').replace(/'/g,'&#039;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); 
}
this._spaceReplacer = function(input) {
    return input.replace(/\t/g, 'xzAzx').replace(/\n/g, 'xzBzx').replace(/ /g, 'xzCzx');
}
this._unspaceReplacer = function(input) {
    return input.replace(/xzAzx/g, "\t").replace(/xzBzx/g, "\n").replace(/xzCzx/g, ' ');
}
this.toHtml = function() {
return this._recursivViewStruct(this.root);}
this._recursivViewStruct = function(item) {
var color = '';switch (item.type) {
case 'element' : color = 'red'; break;case 'comment' : color = 'green'; break;case 'cdata'   : color = 'lime'; break;case 'chardata': color = 'mangenta'; break;default: color = 'blue';}
var out = new Array();var i = 0;var ii = 0;out[ii++] = '<fieldset style="border:solid thin '+ color +'; padding:5"><legend><b>' + item.type + ': ' + item.name +'</b></legend>';out[ii++] = 'Value: [' + item.value + "]<br \>\n";for (x in item.attributes) {
out[ii++] = x + '=' + item.attributes[x] +"<br \>\n"
}
for (i=0; i<item.children.length; i++) {
out[ii++] = this._recursivViewStruct(item.children[i]);}
out[ii++] =  "</fieldset>\n";if (this._debugOn) {
return this._debug.join('');}
return out.join('');}
}
function _Bs_XmlParserElement() {
this.type = "";this.name = "";this.value = "";this.attributes = new Object();this.children = new Array();}
function _Bs_XmlParserStrFragment() {
this.str  = '';this.list = new Array();this.end  = '';}
