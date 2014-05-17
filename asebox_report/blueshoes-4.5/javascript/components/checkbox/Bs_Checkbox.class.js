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
if (!Bs_Objects) {var Bs_Objects = [];};function Bs_Checkbox() {
this._id;this._tagId;this.checkboxName;this.value = 0;this.noPartly = false;this.disabled = false;this.guiNochange = false;this.caption;this.imgDir = '/_bsJavascript/components/checkbox/img/win2k/';this.imgWidth  = '20';this.imgHeight = '20';this.imgStyle = '';this.useMouseover = false;this.eventOnClick;this.eventOnChange;this._constructor = function() {
this._id = Bs_Objects.length;Bs_Objects[this._id] = this;this._tagId = "Bs_Checkbox_"+this._id+"_";}
this.render = function(tagId) {
if (this.noPartly && (this.value == 1)) this.value = 2;if (!bs_isEmpty(tagId)) {
this._tagId = tagId;}
var out  = new Array();var outI = 0;var img = '';img += (this.disabled) ? 'disabled' : 'enabled';img += '_' + this.value;if (!this.disabled) {
out[outI++] = '<span';if (!this.guiNochange) {
out[outI++] = ' onClick="Bs_Objects['+this._id+'].onClick(\'' + this._tagId + '\');"';}
out[outI++] = ' style="cursor:hand;"';if (this.useMouseover && !this.guiNochange) {
out[outI++] = ' onMouseOver="Bs_Objects['+this._id+'].onMouseOver(\'' + this._tagId + '\');"';out[outI++] = ' onMouseOut="Bs_Objects['+this._id+'].onMouseOut(\'' + this._tagId + '\');"';}
out[outI++] = '>';}
out[outI++] = '<img id="' + this._tagId + 'icon" src="' + this.imgDir + img + '.gif" border="0" width="' + this.imgWidth + '" height="' + this.imgHeight + '"';if (!bs_isEmpty(this.imgStyle)) out[outI++] = ' style="' + this.imgStyle + '"';out[outI++] = '>';if (this.caption) {
out[outI++] = '&nbsp;' + this.caption;}
if (!this.disabled) {
out[outI++] = '</span>';}
if (!this.checkboxName) {
this.checkboxName = 'checkbox' + this._tagId;}
out[outI++] = '<input value="' + this.value + '" type=checkbox name="' + this.checkboxName + '" id="' + this.checkboxName + '" style="display:none; visibility:hidden;"';if (this.value) out[outI++] = ' checked';out[outI++] = '>';return out.join('');}
this.drawInto = function(tagId) {
if (!bs_isEmpty(tagId)) {
this._tagId = tagId;}
document.getElementById(this._tagId).innerHTML = this.render(this._tagId);}
this.draw = function(tagId) {
this.drawInto(tagId);}
this.write = function() {
document.write(this.render(this._tagId));}
this.convertField = function(fieldId) {
document.getElementById(fieldId).outerHTML = this.render(this._tagId);}
this.onMouseOver = function() {
var img = document.getElementById(this._tagId + 'icon');if (!img.swapOver0) {
img.swapOver0 = new Image();img.swapOver0.src = this.imgDir + 'enabled_0_over.gif';img.swapOver1 = new Image();img.swapOver1.src = this.imgDir + 'enabled_1_over.gif';img.swapOver2 = new Image();img.swapOver2.src = this.imgDir + 'enabled_2_over.gif';img.swapOut0 = new Image();img.swapOut0.src = this.imgDir + 'enabled_0.gif';img.swapOut1 = new Image();img.swapOut1.src = this.imgDir + 'enabled_1.gif';img.swapOut2 = new Image();img.swapOut2.src = this.imgDir + 'enabled_2.gif';}
img.src = img['swapOver' + this.value].src;}
this.onMouseOut = function() {
var img = document.getElementById(this._tagId + 'icon');img.src = img['swapOut' + this.value].src;}
this.onClick = function() {
switch (this.value) {
case 0:
this.value = 2;break;case 1:
case 2:
this.value = 0;this.value = 0;break;default:
this.value = 0;}
this._updateHiddenField();this._updateIcon();if (this.eventOnClick)  this._fireEvent(this.eventOnClick);if (this.eventOnChange) this._fireEvent(this.eventOnChange);}
this.setTo = function(value, cancelEventOnChange) {
this.value = value;this._updateHiddenField();this._updateIcon();if (!cancelEventOnChange) {
if (this.eventOnChange) this._fireEvent(this.eventOnChange);}
}
this.attachOnClick = function(globalFunctionName) {
this.eventOnClick = globalFunctionName;}
this.attachOnChange = function(globalFunctionName) {
this.eventOnChange = globalFunctionName;}
this._fireEvent = function(e) {
if (e) {
if (typeof(e) != 'array') {
e = new Array(e);}
for (var i=0; i<e.length; i++) {
if (typeof(e[i]) == 'function') {
e[i](this);} else if (typeof(e[i]) == 'string') {
eval(e[i]);}
}
}
}
this._updateIcon = function() {
var iconElm = document.getElementById(this._tagId + 'icon');if (iconElm != null) {
var img = '';img += (this.disabled) ? 'disabled' : 'enabled';img += '_' + this.value;iconElm.src = this.imgDir + img + '.gif';}
}
this._updateHiddenField = function() {
var elm = document.getElementById(this.checkboxName);elm.value = this.value;elm.checked = (this.value);}
this._constructor();}
