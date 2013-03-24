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
function Bs_Wddx () {
this.serialize = function(obj) {
var ret = "<wddxPacket version='1.0'><header /><data>" + this._recursiveSerialize(obj) + '</data></wddxPacket>';return ret;}
this._recursiveSerialize = function(obj) {
var status = false;var cr = '';var ret = '';var tmpArray = new Array();var ii = 0;do {
if (('undefined' == typeof(obj)) || (null == obj)) {
ret = '<null />';status = true;break;}
var value = obj.valueOf();switch(typeof(value)) {
case 'boolean':
ret = "<boolean value='"+value+"'/>";break;case 'number':
if (obj instanceof Date) {
var tmp = 0;var Y  = 1000 < (tmp = obj.getYear())  ? tmp : tmp+1900;var M  = 10 < (tmp = obj.getMonth()+1) ? tmp : '0'+tmp;var D  = 10 < (tmp = obj.getDate())    ? tmp : '0'+tmp;var H  = 10 < (tmp = obj.getHours())   ? tmp : '0'+tmp;var mm = 10 < (tmp = obj.getMinutes()) ? tmp : '0'+tmp;var s  = 10 < (tmp = obj.getSeconds()) ? tmp : '0'+tmp;ret = '<dateTime>'+Y+'-'+M+'-'+D+'T'+H+':'+mm+':'+s+'</dateTime>';} else {
ret = '<number>'+value+'</number>';}
break;case 'string':
          value = value.replace(/&/g,'&amp;').replace(/'/g,'&#039;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
          value = value.replace(/\r/g,'&#x0D;').replace(/\f/g,'&#x0C;').replace(/\n/g,'&#x0A;').replace(/\t/g,'&#x09;');
ret = '<string>'+ value + '</string>';break;case 'object':
if (typeof(obj.wddxSerialize) == 'function') {
ret = obj.wddxSerialize(this);}
else if (obj instanceof Array) {
tmpArray[ii++] = cr + "<array length='"+obj.length+"'>";for (var i=0; i<obj.length; i++) {tmpArray[ii++] = this._recursiveSerialize(obj[i]);}
tmpArray[ii++] =  cr + '</array>' + cr;ret = tmpArray.join('');}
else {
tmpArray[ii++] = ('string' == typeof(obj.wddxSerializationType)) ?  cr + "<struct type='"+obj.wddxSerializationType+"'>" :  cr + '<struct>';for (var prop in obj) {
if ('wddxSerializationType' == prop) continue;if ('function' == typeof(prop)) continue;tmpArray[ii++] = cr + "<var name='"+prop+"'>" + this._recursiveSerialize(obj[prop]) + '</var>';}
tmpArray[ii++] = cr + '</struct>';ret = tmpArray.join('');}
break;default :
}
status = true;} while(false);return ret;}
this.deserialize = function(wddxPacket) {
var ret = null;var xmlParser = new Bs_XmlParser();var xmlRoot = xmlParser.parse(wddxPacket);for (var i=0; i<xmlRoot.index.length; i++) {
if ('data' == xmlRoot.index[i].name) {
ret = this._recursiveDeserialize(xmlRoot.index[i].children[0]);break;}
}
return ret;}
this._recursiveDeserialize = function(wddxElement) {
var i=0; var leng=0;var ret = null;switch (wddxElement.name) {
case 'array':
ret = new Array();leng = parseInt(wddxElement.attributes["length"]);for (i=0; i<leng; i++) {
ret[i] = this._recursiveDeserialize(wddxElement.children[i]);}
break;case 'struct':
leng = wddxElement.children.length;var constructorFound = false;if (typeof(wddxElement.attributes['type']) == 'string') {
var constructorCheck = 'typeof(' + wddxElement.attributes['type']+ ')';if ( eval(constructorCheck) == 'function' ) constructorFound = true;}
ret = (constructorFound) ? eval('new '+ wddxElement.attributes['type']+'()') : new Object();var varName = '';for (i=0; i<leng; i++) {
varName = wddxElement.children[i].attributes['name'];ret[varName] = this._recursiveDeserialize(wddxElement.children[i].children[0]);}
break;case 'recordset':
break;case 'binary':
break;default:
return this._parseSimpleType(wddxElement);}
return ret;}
this._parseSimpleType = function(wddxElement) {
var ret = ''; var value;switch (wddxElement.name) {
case 'boolean':
ret = (wddxElement.attributes['value']=='true');break;case 'string':
if (wddxElement.children.length == 0) {
ret = '';break;}
var tmp = new Array();var ii = 0;for (var i=0; i<wddxElement.children.length; i++) {
if (wddxElement.children[i].type == 'chardata') {
tmp[ii++] =  wddxElement.children[i].value;} else if (wddxElement.children[i].name == 'char') {
var code = wddxElement.children[i].attributes['code'];tmp[ii++] =  (1 == code.indexOf('x')) ? String.fromCharCode(code) : String.fromCharCode('0x'+ code);}
}
ret = tmp.join('');        ret = ret.replace(/&quot;/g,'"').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
        ret = ret.replace(/&#(\w+);/g, this._unEntityNummeric);
break;case 'number':
value = wddxElement.children[0].value
ret = parseFloat(value);break;case 'null':
ret = null;break;case 'datetime':
value = wddxElement.children[0].value
        var parts = value.match(/(\w+)-(\w+)-(\w+)T(\w+):(\w+):(\w+)(.*)/);
if (null != parts) {
ret = new Date(parts[1], parts[2]-1, parts[3], parts[4], parts[5], parts[6]);} else {
ret = new Date();}
break;default :
ret = null;}
return ret;}
this._unEntityNummeric = function(str) {
if (0 == str.indexOf('x')) str = '0'+ str;if (isNaN(parseInt(str))) {
return '';} else {
return String.fromCharCode(str);}
}
}
