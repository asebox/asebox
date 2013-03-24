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
if (!Bs_Objects) {var Bs_Objects = [];};function bs_radio_onClickLabel() {
var srcElm = event.srcElement;if (typeof(srcElm.forValue) == 'undefined') {
srcElm = srcElm.parentElement;if (typeof(srcElm.forValue) == 'undefined') {
srcElm = srcElm.parentElement;if (typeof(srcElm.forValue) == 'undefined') {
return;}
}
}
srcElm.bsRadioObj.onClick(srcElm.forValue);}
function Bs_Radio() {
this._objectId;this.options = new Object();this.value = null;this.disabled = false;this.guiNochange = false;this.allowNoSelection = false;this.radioFieldName;this.imgDir = '/_bsJavascript/components/radio/img/bobby/';this.imgType = 'gif';this.imgWidth  = '12';this.imgHeight = '12';this.captionAsTitleText = false;this.cssClass;this.cssStyle;this.useMouseover = false;this.pixelate = 0;this.invertActive = false;this.iconType = 'image';this._attachedEvents;this.constructor = function() {
this._id = Bs_Objects.length;Bs_Objects[this._id] = this;this._objectId = "Bs_Radio_"+this._id;}
this.initByField = function(fieldName) {
this.radioFieldName = fieldName;this.options        = new Object;var elms = document.getElementsByName(fieldName);for (var i=0; i<elms.length; i++) {
var elm = elms[i];var activateable   = (elm.disabled) ? 'no' : 'yes';var deactivateable = (elm.disabled) ? 'no' : 'yes';this.addOption(elm.value, '', false, activateable, deactivateable);if (elm.checked) this.value = elm.value;}
}
this.convertField = function(fieldName) {
this.initByField(fieldName);var allLabels = document.getElementsByTagName('label');var elms = document.getElementsByName(fieldName);for (var i=elms.length; i>0; i--) {
var elm = elms[i -1];var elmValue = elm.value;elm.outerHTML = this.render(elmValue);var forId = fieldName + '_' + elmValue;for (var j=0; j<allLabels.length; j++) {
if (allLabels[j]['htmlFor'] == forId) {
allLabels[j].forValue   = elmValue;allLabels[j].bsRadioObj = this;allLabels[j].attachEvent('onclick', bs_radio_onClickLabel);break;}
}
}
}
this.drawInto = function(value, tagId) {
if (bs_isNull(value)) {
var out = this.renderAsTable();} else {
var out = this.render(value);}
var tag = document.getElementById(tagId);if (tag != null) {
tag.innerHTML = out;return true;}
return false;}
this.draw = function(value) {
document.write(this.render(value));}
this.renderAsTable = function(direction, tableTag) {
var ret = new Array();if (bs_isNull(tableTag)) {
ret[ret.length] = '<table border="0">';} else {
ret[ret.length] = tableTag;}
if (direction == 'horizontal') {
ret[ret.length] = '<tr>';var i = 0;for (var value in this.options) {
ret[ret.length] = '<td>';if (i > 0) ret[ret.length] = '&nbsp;&nbsp;';ret[ret.length] = this.render(value) + '</td>';i++;}
ret[ret.length] = '</tr>';} else {
for (var value in this.options) {
ret[ret.length] = '<tr><td>' + this.render(value) + '</td></tr>';}
}
ret[ret.length] = '</table>';return ret.join('');}
this.render = function(value) {
if (typeof(this.options[value]) == 'undefined') return '';if (this.options[value]['hide']) return '';var out  = new Array();var outI = 0;var valueId = this._objectId + value;if (!bs_isNull(this.options[value]['furtherOptions']) && !bs_isNull(this.options[value]['furtherOptions']['captionAsTitleText'])) {
var captionAsTitleText = this.options[value]['furtherOptions']['captionAsTitleText'];} else {
if (typeof(this.captionAsAltText) != 'undefined') {
var captionAsTitleText = this.captionAsAltText;} else {
var captionAsTitleText = this.captionAsTitleText;}
}
var userSelectable = !(this.disabled || this.guiNochange || (this.options[value]['activateable'] != 'yes'));var isSelected = (this.value == value);if (userSelectable) {
out[outI++] = '<span';if (typeof(this.cssClass) != 'undefined') {
out[outI++] = ' class="' + this.cssClass + '"';}
if (typeof(this.cssStyle) != 'undefined') {
out[outI++] = ' style="' + this.cssStyle + '"';}
if (!this.guiNochange) {
out[outI++] = ' onClick="Bs_Objects['+this._id+'].onClick(\'' + value + '\');"';}
out[outI++] = ' style="cursor:hand;"';out[outI++] = ' onMouseOver="Bs_Objects['+this._id+'].onMouseOver(\'' + value + '\');"';out[outI++] = ' onMouseOut="Bs_Objects['+this._id+'].onMouseOut(\'' + value + '\');"';out[outI++] = '>';}
var img         = '';var imgCssStyle = '';img += this._getIconPrefix(value);if (this.iconType == 'css') {
if (!userSelectable) {
imgCssStyle += 'filter:progid:DXImageTransform.Microsoft.BasicImage(grayScale=1), progid:DXImageTransform.Microsoft.BasicImage(opacity=.5);';}
if (isSelected) {
if (this.invertActive) imgCssStyle += 'filter:progid:DXImageTransform.Microsoft.BasicImage(invert=1);';imgCssStyle += 'border:2px solid gray;';} else {
if ((this.pixelate > 0) && userSelectable) {
imgCssStyle += 'filter:progid:DXImageTransform.Microsoft.Pixelate(maxsquare=' + this.pixelate + ');';}
}
} else {
img += (userSelectable) ? 'enabled' : 'disabled';img += '_';img += (isSelected) ? '1' : '0';img += '.' + this.imgType;}
out[outI++] = '<img id="' + valueId + 'Icon" src="' + this.imgDir + img + '" border="0"';if (!bs_isNull(this.imgWidth))  out[outI++] = ' width="'  + this.imgWidth  + '"';if (!bs_isNull(this.imgHeight)) out[outI++] = ' height="' + this.imgHeight + '"';out[outI++] = ' style="' + imgCssStyle + '"';if (this.options[value]['caption'].indexOf('<') == -1) {
out[outI++] = ' alt="' + this.options[value]['caption'] + '"';}
if (captionAsTitleText) {
out[outI++] = ' title="' + this.options[value]['caption'] + '"';}
out[outI++] = '>';if (!captionAsTitleText && (this.options[value]['caption'] != '')) {
out[outI++] = '&nbsp;' + this.options[value]['caption'];}
if (userSelectable) {
out[outI++] = '</span>';}
if (typeof(this.radioFieldName) != 'undefined') {
var radioFieldName = this.radioFieldName;} else {
var radioFieldName = valueId + 'Field';this.radioFieldName = radioFieldName;}
out[outI++] = '<input type="radio" name="' + radioFieldName + '" value="' + value + '" style="display:none; visibility:hidden;"';if (isSelected) out[outI++] = ' checked';out[outI++] = '>';return out.join('');}
this.addOption = function(value, caption, hide, activateable, deactivateable, furtherOptions) {
if (bs_isNull(hide))           hide             = false;if (bs_isNull(activateable))   activateable     = 'yes';if (bs_isNull(deactivateable)) deactivateable   = 'yes';value += '';this.options[value] = new Object;this.options[value]['caption']        = caption;this.options[value]['hide']           = hide;this.options[value]['activateable']   = activateable;this.options[value]['deactivateable'] = deactivateable;if (typeof(furtherOptions) == 'object') {
this.options[value]['furtherOptions'] = furtherOptions;}
}
this.getValue = function() {
return this.value;}
this.onClick = function(value) {
var newValueReal = value;if (this.value == value) {
if (this.allowNoSelection) {
newValueReal = null;} else {
return;}
}
if (typeof(this.value) != 'undefined') {
var oldSelectedImg = document.getElementById(this._objectId + this.value + 'Icon');if (oldSelectedImg) {
if (this.iconType == 'css') {
oldSelectedImg.style.border = 'none';if (this.pixelate > 0) {
oldSelectedImg.style.filter = 'progid:DXImageTransform.Microsoft.Pixelate(maxsquare=' + this.pixelate + ')';} else {
oldSelectedImg.style.filter = '';}
} else {
oldSelectedImg.src = this.imgDir + this._getIconPrefix(this.value) + 'enabled_0.' + this.imgType;}
}
}
if (newValueReal != null) {
var newSelectedImg = document.getElementById(this._objectId  + value + 'Icon');if (newSelectedImg) {
if (this.iconType == 'css') {
imgCssStyle  = '';if (this.invertActive) newSelectedImg.style.filter = 'progid:DXImageTransform.Microsoft.BasicImage(invert=1)';newSelectedImg.style.border = '2px solid gray';} else {
newSelectedImg.src = this.imgDir + this._getIconPrefix(value) + 'enabled_1.' + this.imgType;}
}
}
this.value = newValueReal;var col = document.getElementsByName(this.radioFieldName);for (var i=0; i<col.length; i++) {
if (col[i].value == this.value) {
col[i].checked = true;} else {
col[i].checked = false;}
}
if (this.eventOnClick)  this._fireEvent(this.eventOnClick);if (this.eventOnChange) this._fireEvent(this.eventOnChange);if (this.hasEventAttached('onChange')) this.fireEvent('onChange');}
this.onMouseOver = function(value) {
if (this.useMouseover && (this.iconType == 'image')) {
var img = document.getElementById(this._objectId  + value + 'Icon');if (!img.swapOver0) {
img.swapOver0 = new Image();img.swapOver0.src = this.imgDir + 'enabled_0_over.gif';img.swapOver1 = new Image();img.swapOver1.src = this.imgDir + 'enabled_1_over.gif';img.swapOver2 = new Image();img.swapOver2.src = this.imgDir + 'enabled_2_over.gif';img.swapOut0 = new Image();img.swapOut0.src = this.imgDir + 'enabled_0.gif';img.swapOut1 = new Image();img.swapOut1.src = this.imgDir + 'enabled_1.gif';img.swapOut2 = new Image();img.swapOut2.src = this.imgDir + 'enabled_2.gif';}
img.src = img['swapOver' + this.value].src;}
if (this.pixelate > 0) {
var img = document.getElementById(this._objectId  + value + 'Icon');img.style.filter = '';}
}
this.onMouseOut = function(value) {
if (this.useMouseover && (this.iconType == 'image')) {
var img = document.getElementById(this._objectId  + value + 'Icon');img.src = img['swapOut' + this.value].src;}
if ((this.pixelate > 0) && (this.value != value)) {
var img = document.getElementById(this._objectId  + value + 'Icon');img.style.filter = 'progid:DXImageTransform.Microsoft.Pixelate(maxsquare=' + this.pixelate + ')';}
}
this.attachEvent = function(trigger, yourEvent) {
if (typeof(this._attachedEvents) == 'undefined') {
this._attachedEvents = new Array();}
if (typeof(this._attachedEvents[trigger]) == 'undefined') {
this._attachedEvents[trigger] = new Array(yourEvent);} else {
this._attachedEvents[trigger][this._attachedEvents[trigger].length] = yourEvent;}
}
this.hasEventAttached = function(trigger) {
return (this._attachedEvents && this._attachedEvents[trigger]);}
this.fireEvent = function(trigger) {
if (this._attachedEvents && this._attachedEvents[trigger]) {
var e = this._attachedEvents[trigger];if ((typeof(e) == 'string') || (typeof(e) == 'function')) {
e = new Array(e);}
for (var i=0; i<e.length; i++) {
if (typeof(e[i]) == 'function') {
e[i](this);} else if (typeof(e[i]) == 'string') {
eval(e[i]);}
}
}
}
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
this._getIconPrefix = function(value) {
if (!bs_isNull(this.options[value]['furtherOptions']) && !bs_isNull(this.options[value]['furtherOptions']['iconPrefix'])) {
return this.options[value]['furtherOptions']['iconPrefix'];}
return '';}
this.constructor();}
