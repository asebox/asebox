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
function Bs_CsvUtil()  {
this.foo = '';this.Bs_CsvUtil = function() {
}
this.csvStringToArray = function(string, separator, trim, removeHeader, removeEmptyLines, checkMultiline) {
if (typeof(separator)        == 'undefined') separator        = ';';if (typeof(trim)             == 'undefined') trim             = 'none';if (typeof(removeHeader)     == 'undefined') removeHeader     = false;if (typeof(removeEmptyLines) == 'undefined') removeEmptyLines = false;if (typeof(checkMultiline)   == 'undefined') checkMultiline   = false;if (string.length == 0) return new Array;var array = string.split("\n");for (var i=0; i<array.length; i++) {
if (array[i].substr(array[i].length -1) == "\r") {
array[i] = array[i].substr(0, array[i].length -1);}
}
if ((typeof(array) != 'object') || (array.length == 0)) return new Array;if (checkMultiline) array = this._checkMultiline(array);if (separator == 'auto') separator = this.guessSeparator(array);return this.csvArrayToArray(array, separator, trim, removeHeader, removeEmptyLines);}
this.csvArrayToArray = function(array, separator, trim, removeHeader, removeEmptyLines) {
if (typeof(separator)        == 'undefined') separator        = ';';if (typeof(trim)             == 'undefined') trim             = 'none';if (typeof(removeHeader)     == 'undefined') removeHeader     = false;if (typeof(removeEmptyLines) == 'undefined') removeEmptyLines = false;switch (trim) {
case 'none':
var trimFunction = false;break;case 'left':
var trimFunction = 'ltrim';break;case 'right':
var trimFunction = 'rtrim';break;default:
var trimFunction = 'trim';break;}
var sepLength = separator.length;if (removeHeader) {
array.shift();}
var ret = new Array;for (var i=0; i<array.length; i++) {
var line = array[i];var offset    = 0;var lastPos   = 0;var lineArray = new Array;for (var j=0; j<1; j--) {
var pos = line.indexOf(separator, offset);if (pos == -1) {
lineArray[lineArray.length] = line.substr(lastPos);break;}
var currentSnippet = line.substr(lastPos, pos-lastPos);var numQuotes = currentSnippet.split('"').length -1;if ((numQuotes % 2) == 0) {
lineArray[lineArray.length] = line.substr(lastPos, pos-lastPos);lastPos = pos + sepLength;} else {
}
offset = pos + sepLength;}
if (trimFunction != false) {
try {
for (var j=0; j<lineArray.length; j++) {
if (trimFunction == 'trim') {
lineArray[j] = bs_trim(lineArray[j]);} else if (trimFunction == 'ltrim') {
lineArray[j] = bs_ltrim(lineArray[j]);} else if (trimFunction == 'rtrim') {
lineArray[j] = bs_rtrim(lineArray[j]);}
}
} catch (e) {
}
}
for (var j=0; j<lineArray.length; j++) {
if ((lineArray[j].substr(0, 1) == '"') && (lineArray[j].substr(1, 1) != '"') && (lineArray[j].substr(lineArray[j].length -1) == '"')) {
lineArray[j] = lineArray[j].substring(1, lineArray[j].length -1);}
        lineArray[j] = lineArray[j].replace(/""/, '"');
}
var addIt = true;if (removeEmptyLines) {
var addIt = false;for (var j=0; j<lineArray.length; j++) {
try {
var tmp = bs_trim(lineArray[j]);} catch (e) {
var tmp = lineArray[j];}
if (tmp != '') {
addIt = true;break;}
}
}
if (addIt) {
ret[ret.length] = lineArray;}
}
return ret;}
this.guessSeparator = function(cvsArray) {
if (cvsArray[0].indexOf(';')  >= 0) return ';';if (cvsArray[0].indexOf("\t") >= 0) return "\t";return false;}
this._checkMultiline = function(input) {
return input;}
}
