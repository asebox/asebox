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
function GiveDec(Hex) {
if (Hex == "A") Value = 10;else if(Hex == "B") Value = 11;else if(Hex == "C") Value = 12;else if(Hex == "D") Value = 13;else if(Hex == "E") Value = 14;else if(Hex == "F") Value = 15;else Value = eval(Hex);return Value;}
function GiveHex(Dec) {
if(Dec == 10) Value = "A";else if(Dec == 11) Value = "B";else if(Dec == 12) Value = "C";else if(Dec == 13) Value = "D";else if(Dec == 14) Value = "E";else if(Dec == 15) Value = "F";else Value = "" + Dec;return Value;}
function HexToDec(Input) {
Input = Input.toUpperCase();a = GiveDec(Input.substring(0, 1));b = GiveDec(Input.substring(1, 2));c = GiveDec(Input.substring(2, 3));d = GiveDec(Input.substring(3, 4));e = GiveDec(Input.substring(4, 5));f = GiveDec(Input.substring(5, 6));outRed   = (a * 16) + b;outGreen = (c * 16) + d;outBlue  = (e * 16) + f;out = new Array(outRed, outGreen, outBlue);return out;}
function DecToHex(Red, Green, Blue) {
a = GiveHex(Math.floor(Red / 16));b = GiveHex(Red % 16);c = GiveHex(Math.floor(Green / 16));d = GiveHex(Green % 16);e = GiveHex(Math.floor(Blue / 16));f = GiveHex(Blue % 16);out = a + b + c + d + e + f;return out;}
function filterForHtml(str) {
return bs_filterForHtml(str);}
function bs_filterForHtml(str) {
  str = str.replace(/&/g,'&amp;').replace(/'/g,'&#039;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  str = str.replace(/\r/g,'&#x0D;').replace(/\f/g,'&#x0C;').replace(/\n/g,'&#x0A;').replace(/\t/g,'&#x09;');
return str;}
function filterForHtml2(str) {
str = filter("&lt;",   "<", str)
str = filter("&gt;",   ">", str)
  str = str.replace("<>", ""); //bug in filter function?
return str;}
function filter(inTag, outTag, inString) {
split = inString.split(inTag)
var outString = '';if (split.length > 0) {
for(i=0; i<split.length; i++) {
if (i==split.lenth) {
outString += split[i];} else {
outString += split[i] + outTag;}
}
return outString;} else {
return inString;}
}
function bs_stripTags(str) {
var hackyTagName = 'bs_stripTags_helperTag';var hackyTag = document.getElementById(hackyTagName);if (hackyTag == null) {
var tags    = document.getElementsByTagName('body');var bodyTag = tags[0];bodyTag.insertAdjacentHTML('BeforeEnd', '<div id="' + hackyTagName + '" style="display:none;"></div>');hackyTag = document.getElementById(hackyTagName);}
hackyTag.innerHTML = str;return hackyTag.innerText;}
function bs_parseSimpleTagProps(tagStr) {
var hackyTagName = 'bs_parseSimpleTagProps_helperTag';var hackyTag = document.getElementById(hackyTagName);if (hackyTag == null) {
var tags    = document.getElementsByTagName('body');var bodyTag = tags[0];bodyTag.insertAdjacentHTML('BeforeEnd', '<div id="' + hackyTagName + '" style="display:none;"></div>');hackyTag = document.getElementById(hackyTagName);}
hackyTag.style.display = 'block';hackyTag.innerHTML     = tagStr;var myTag              = hackyTag.children[0];var ret = new Array;for (var prop in myTag.attributes) {
try {
var x = 'style';if (prop == x) alert('prop: ' + prop + ' ' + typeof(myTag.attributes[prop]));if (myTag.attributes[prop]['name'] == x) {
alert(myTag.attributes[prop]['name']);alert(myTag.attributes[prop]['value']);}
} catch (e) {}
switch (typeof(myTag.attributes[prop])) {
case 'object':
if (myTag.attributes[prop]['specified']) {
ret[myTag.attributes[prop]['name']] = myTag.attributes[prop]['value'];}
break;case 'string':
ret[prop] = myTag.attributes[prop];break;case 'undefined':
break;}
}
hackyTag.style.display = 'none';return ret;}
function findWrappingElement(tagName, obj) {
if (obj.tagName == tagName) return obj;if (obj.tagName == 'BODY') return false;try {
if (obj.parentElement) {
var newObj = obj.parentElement();} else {
var newObj = obj.item(0).parentElement;}
} catch (e) {
return false;}
return findWrappingElement(tagName, newObj);}
function expandSelectionToSimpleTag(textRange, tagName) {
tagName        = tagName.toLowerCase();var r2         = textRange.duplicate();var weArInside = false;for (var i=0; i>-1; i++) {
if (r2.text.substr(0,1) == '<') {
if (r2.text.length < (tagName.length +1)) {
r2.moveEnd('character', tagName.length + 1 - r2.text.length);}
if (r2.text.substr(0, tagName.length +1).toLowerCase() == ('<' + tagName)) {
for (var j=0; j>-1; j++) {
if (r2.text.substr(r2.text.length -1, 1) == '>') {
weArInside = true;break;}
var moved = r2.moveEnd('character', 1);if (moved != 1) break;if (j > 1000) break;}
}
break;}
var moved = r2.moveStart('character', -1);if (moved != -1) break;if (i > 10000) break;}
if (weArInside) return r2;return textRange;}
