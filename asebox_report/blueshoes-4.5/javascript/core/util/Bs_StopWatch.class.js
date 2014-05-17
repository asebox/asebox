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
function Bs_StopWatch () {
this.reset = function () {
this.lastTakeTime = this.lastDeltaTime = this.startTime = new Date().getTime();this.stops = new Array();}
this.takeTime = function (info) {
var now   = new Date().getTime();this.stops[this.stopCnt] = new Array();this.stops[this.stopCnt]['INFO'] = info;this.stops[this.stopCnt]['TOT']  = now - this.startTime;this.stops[this.stopCnt]['DELTA']= now - this.lastTakeTime;this.lastTakeTime = now;this.stopCnt++;}
this.getTime = function () {
var now   = new Date().getTime();return (now - this.startTime);}
this.getDelta = function () {
var now    = new Date().getTime();var delta  = now - this.lastDeltaTime;this.lastDeltaTime = now;return delta;}
this.toHtml = function(title) {
var ret = new Array();var ii = 0;if (title != '') ret[ii++] = "<B>" + title + "</B><br>";this._weightIt();ret[ii++] = '<table cellspacing="0" cellpadding="2">';ret[ii++] = '<tr>';ret[ii++] = '<th bgcolor="Aqua">Nr.</th>';ret[ii++] = '<th bgcolor="Silver">INFO</th>';ret[ii++] = '<th bgcolor="Aqua">DELTA<br>(ms)</th>';ret[ii++] = '<th bgcolor="Silver">TOT<br>(ms)</th>';ret[ii++] = '<th bgcolor="Aqua">-</th>';ret[ii++] = '</td>';var stopSize = this.stops.length;for (var i=0; i<stopSize; i++) {
var stop = this.stops[i];var weight = '';for (var j=stop['weight']; j>=0; j--) weight += '*';ret[ii++] = '<tr>';ret[ii++] = ' <td align="center" bgcolor="Aqua">' + i + '</td>';ret[ii++] = '	<td bgcolor="Silver">' + stop['INFO'] + '</td>';ret[ii++] = '	<td align="right" bgcolor="Aqua">' + stop['DELTA'] + '</td>';ret[ii++] = '	<td align="right" bgcolor="Silver">' + stop['TOT'] + '</td>';ret[ii++] = '	<td align="left" bgcolor="Aqua">' + weight + '</td>';ret[ii++] = '</tr>';}
ret[ii++] = '</table>';return ret.join('');}
this.draw = function(title) {
var body = document.getElementsByTagName('body').item(0);try {
document.body.insertAdjacentHTML('beforeEnd', this.toHtml(title));} catch (e) {
body.innerHTML = this.toHtml(title);}
}
this._weightIt = function () {
var stopSize = this.stops.length;if (stopSize<=0) return;var totalTime = this.stops[stopSize-1]['TOT'];totalTime = (totalTime==0 || totalTime=='') ? 1 : totalTime;for (var i=0; i<stopSize; i++) {
this.stops[i]['weight'] = parseInt(60 * this.stops[i]['DELTA'] / totalTime);}
}
this.stopCnt       = 0;this.startTime     = null;this.stops         = null;this.lastTakeTime  = null;this.lastDeltaTime = null;this.reset();}
