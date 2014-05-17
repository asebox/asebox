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
function makesoundex() {
this.a = -1
this.b =  1
this.c =  2
this.d =  3
this.e = -1
this.f =  1
this.g =  2
this.h = -1
this.i = -1
this.j =  2
this.k =  2
this.l =  4
this.m =  5
this.n =  5
this.o = -1
this.p =  1
this.q =  2
this.r =  6
this.s =  2
this.t =  3
this.u = -1
this.v =  1
this.w = -1
this.x =  2
this.y = -1
this.z =  2
}
var sndx=new makesoundex()
function isSurname(name) {
if (name=="" || name==null) {
return false;} else {
}
return true
}
function collapse(surname) {
if (surname.length <= 1) {
return surname;}
var right=collapse(surname.substring(1,surname.length))
if (sndx[surname.charAt(0)]==sndx[right.charAt(0)]) {
return surname.charAt(0)+right.substring(1,right.length)
}
return surname.charAt(0)+right
}
function soundex(form) {
form.result.value=""
if (!isSurname(form.surname.value)) {
return
}
var stage1=collapse(form.surname.value.toLowerCase())
form.result.value+=stage1.charAt(0).toUpperCase()
form.result.value+="-"
var stage2=stage1.substring(1,stage1.length)
var count=0
for (var i=0; i<stage2.length && count<3; i++) {
if (sndx[stage2.charAt(i)]>0) {
form.result.value+=sndx[stage2.charAt(i)]
count++
}
}
for (; count<3; count++) {
form.result.value+="0"
}
}
