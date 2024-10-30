<?php
/*
 * Copyright (C) 2014 octopush
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * Template for settings page.
 */
if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly
}
//var_dump($_POST);
$yesimage = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAB4klEQVQ4y2P4//8/AyWYZA0ckxlYyDaAcy4Dt0SZ0FnBWP7tJBvAOZuBW7xC4MLe67v+x84L+8/ky7CFaAM4pgE1lwtc3Hl92/+o+UH/HWda/Q+c7Pmf0ZVxE1yRyFJWYZElbKbomnnnM3CLlPNd3H5ny/+oTYH/Reax/Refyv1fKFjgLrcHiwBYEftkBhGrfpO3LpPsf3LNZ3CEaZZcy8UjWS94cfvjzf8TjoT+V90u+l9hsfB/Vi+m22xuDJxgL4i2cop59ru82XV36/91t1b+N+3X/ya+mt1Gaaswm1yr+MVVj5b+z7qQ8N/8pMZ//Q1K/1l9mO+weTBwwAORPYh1SdqW2H+VF/P/L74/9//8+zP+azQrf1GtVXi04OnM/yW3M/973rD8b3vI8D9bOPNdjjSIzSixwOHPvsdgocrP+OvB/yc+6fg/7Wnf/6Wv5v+veV74P/K513+fa/b/uZM47grOZOLEmZCY/Bl3G2xU+Rn30v9/y/uK/y2fKv+nf434HwY0QCCH9672XhkOgimRJYRxr8N+sx/5/xL+V/zP/J/6IeK/WJHQXc9bVpxEJ2X2CJbd4af9fja+K/+vVKJwp+hbCgfJeYE3nmufcoP82Tn/J3CSnZnSf0Sy0iQ3omMAFlyLENN9tK8AAAAASUVORK5CYII=";
$noimage = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAABuVBMVEUAAACAAACmAAB4AAC3KTGRCw4AAAAAAwMHAAAJAAAAAADeCAIAAADEAgCnEBKkDQ+1EAm6Dgl6JSl0GRwVAADgBQMAAAAWAADlBQHkBwQAAADoAQAEEBgBBQyGAACIAACNAACQAACWAACZAACZMzeaQ0ibAACcAACeAAChAACiAAClFhemEBCmEg+mFhWmFximKCitTkuuAACvAACwQD6zAAC0AAC0Dg+1AAC2AgO5AAC6GBm6HB+7AAC7ERO8Cwu/AADAAADAEw/BAADBAQDCAADCFA7CFRfCHB/DAADDDQzEAADEFhfGAADGAwXHAADIFhrJAADKAADKAQHMGRjOMjPPAADPOTnQAADQBAbQMjfSAADSWG/UAADUMTTWLy3YAADZAADZDgvZQT/bAADcAADcRkneAADeEhPgHR/mVlrqBQbqBwrqDBDqERXrGRnsUlfub3PwAADwJSnzAAD2AAD3PDb4AAD5AAD5Tl75rMP6j5D8AAD8S13+s7r/Cg7/DQ//Fxr/HyP/ICP/KCn/QEv/Tlr/bmn/dIP/eo3/gJH/jJz/oqT/uLj/ydj/zt3/0OD/0d//4OmFVcHvAAAAHXRSTlMABAUKExoiKi81ODo9QWdpoq24w8nO1NXX2uHh9giyYs4AAADMSURBVBiVY2DAAvh4ITQ3P4TmmNTLBGb01HGCKC6VvonJIEbahH5VHgYGtqru1tqueAaGqM7Kto4YdgYGmbj67NzmwqymvJyGTFmQUrGMckfn4nxX79IEKYipIl6mahoamhaW4jB7hTyUlZSU3IXhDrG2k1dQlDO3gfEDXNSNvf2NtPx8wFxG+2AL38TQ8BRPs0gnZgYGluogq9giHUEB3ZII27ACVgYGt+jUCj1JBgZR/Zr0pBCQHocWA2kQLaHdGAgx1LAdQpeZAAkAxmgq2w7J8t0AAAAASUVORK5CYII=";
?>
<script>

var QRCode;!function(){function a(a){this.mode=c.MODE_8BIT_BYTE,this.data=a,this.parsedData=[];for(var b=[],d=0,e=this.data.length;e>d;d++){var f=this.data.charCodeAt(d);f>65536?(b[0]=240|(1835008&f)>>>18,b[1]=128|(258048&f)>>>12,b[2]=128|(4032&f)>>>6,b[3]=128|63&f):f>2048?(b[0]=224|(61440&f)>>>12,b[1]=128|(4032&f)>>>6,b[2]=128|63&f):f>128?(b[0]=192|(1984&f)>>>6,b[1]=128|63&f):b[0]=f,this.parsedData=this.parsedData.concat(b)}this.parsedData.length!=this.data.length&&(this.parsedData.unshift(191),this.parsedData.unshift(187),this.parsedData.unshift(239))}function b(a,b){this.typeNumber=a,this.errorCorrectLevel=b,this.modules=null,this.moduleCount=0,this.dataCache=null,this.dataList=[]}function i(a,b){if(void 0==a.length)throw new Error(a.length+"/"+b);for(var c=0;c<a.length&&0==a[c];)c++;this.num=new Array(a.length-c+b);for(var d=0;d<a.length-c;d++)this.num[d]=a[d+c]}function j(a,b){this.totalCount=a,this.dataCount=b}function k(){this.buffer=[],this.length=0}function m(){return"undefined"!=typeof CanvasRenderingContext2D}function n(){var a=!1,b=navigator.userAgent;return/android/i.test(b)&&(a=!0,aMat=b.toString().match(/android ([0-9]\.[0-9])/i),aMat&&aMat[1]&&(a=parseFloat(aMat[1]))),a}function r(a,b){for(var c=1,e=s(a),f=0,g=l.length;g>=f;f++){var h=0;switch(b){case d.L:h=l[f][0];break;case d.M:h=l[f][1];break;case d.Q:h=l[f][2];break;case d.H:h=l[f][3]}if(h>=e)break;c++}if(c>l.length)throw new Error("Too long data");return c}function s(a){var b=encodeURI(a).toString().replace(/\%[0-9a-fA-F]{2}/g,"a");return b.length+(b.length!=a?3:0)}a.prototype={getLength:function(){return this.parsedData.length},write:function(a){for(var b=0,c=this.parsedData.length;c>b;b++)a.put(this.parsedData[b],8)}},b.prototype={addData:function(b){var c=new a(b);this.dataList.push(c),this.dataCache=null},isDark:function(a,b){if(0>a||this.moduleCount<=a||0>b||this.moduleCount<=b)throw new Error(a+","+b);return this.modules[a][b]},getModuleCount:function(){return this.moduleCount},make:function(){this.makeImpl(!1,this.getBestMaskPattern())},makeImpl:function(a,c){this.moduleCount=4*this.typeNumber+17,this.modules=new Array(this.moduleCount);for(var d=0;d<this.moduleCount;d++){this.modules[d]=new Array(this.moduleCount);for(var e=0;e<this.moduleCount;e++)this.modules[d][e]=null}this.setupPositionProbePattern(0,0),this.setupPositionProbePattern(this.moduleCount-7,0),this.setupPositionProbePattern(0,this.moduleCount-7),this.setupPositionAdjustPattern(),this.setupTimingPattern(),this.setupTypeInfo(a,c),this.typeNumber>=7&&this.setupTypeNumber(a),null==this.dataCache&&(this.dataCache=b.createData(this.typeNumber,this.errorCorrectLevel,this.dataList)),this.mapData(this.dataCache,c)},setupPositionProbePattern:function(a,b){for(var c=-1;7>=c;c++)if(!(-1>=a+c||this.moduleCount<=a+c))for(var d=-1;7>=d;d++)-1>=b+d||this.moduleCount<=b+d||(this.modules[a+c][b+d]=c>=0&&6>=c&&(0==d||6==d)||d>=0&&6>=d&&(0==c||6==c)||c>=2&&4>=c&&d>=2&&4>=d?!0:!1)},getBestMaskPattern:function(){for(var a=0,b=0,c=0;8>c;c++){this.makeImpl(!0,c);var d=f.getLostPoint(this);(0==c||a>d)&&(a=d,b=c)}return b},createMovieClip:function(a,b,c){var d=a.createEmptyMovieClip(b,c),e=1;this.make();for(var f=0;f<this.modules.length;f++)for(var g=f*e,h=0;h<this.modules[f].length;h++){var i=h*e,j=this.modules[f][h];j&&(d.beginFill(0,100),d.moveTo(i,g),d.lineTo(i+e,g),d.lineTo(i+e,g+e),d.lineTo(i,g+e),d.endFill())}return d},setupTimingPattern:function(){for(var a=8;a<this.moduleCount-8;a++)null==this.modules[a][6]&&(this.modules[a][6]=0==a%2);for(var b=8;b<this.moduleCount-8;b++)null==this.modules[6][b]&&(this.modules[6][b]=0==b%2)},setupPositionAdjustPattern:function(){for(var a=f.getPatternPosition(this.typeNumber),b=0;b<a.length;b++)for(var c=0;c<a.length;c++){var d=a[b],e=a[c];if(null==this.modules[d][e])for(var g=-2;2>=g;g++)for(var h=-2;2>=h;h++)this.modules[d+g][e+h]=-2==g||2==g||-2==h||2==h||0==g&&0==h?!0:!1}},setupTypeNumber:function(a){for(var b=f.getBCHTypeNumber(this.typeNumber),c=0;18>c;c++){var d=!a&&1==(1&b>>c);this.modules[Math.floor(c/3)][c%3+this.moduleCount-8-3]=d}for(var c=0;18>c;c++){var d=!a&&1==(1&b>>c);this.modules[c%3+this.moduleCount-8-3][Math.floor(c/3)]=d}},setupTypeInfo:function(a,b){for(var c=this.errorCorrectLevel<<3|b,d=f.getBCHTypeInfo(c),e=0;15>e;e++){var g=!a&&1==(1&d>>e);6>e?this.modules[e][8]=g:8>e?this.modules[e+1][8]=g:this.modules[this.moduleCount-15+e][8]=g}for(var e=0;15>e;e++){var g=!a&&1==(1&d>>e);8>e?this.modules[8][this.moduleCount-e-1]=g:9>e?this.modules[8][15-e-1+1]=g:this.modules[8][15-e-1]=g}this.modules[this.moduleCount-8][8]=!a},mapData:function(a,b){for(var c=-1,d=this.moduleCount-1,e=7,g=0,h=this.moduleCount-1;h>0;h-=2)for(6==h&&h--;;){for(var i=0;2>i;i++)if(null==this.modules[d][h-i]){var j=!1;g<a.length&&(j=1==(1&a[g]>>>e));var k=f.getMask(b,d,h-i);k&&(j=!j),this.modules[d][h-i]=j,e--,-1==e&&(g++,e=7)}if(d+=c,0>d||this.moduleCount<=d){d-=c,c=-c;break}}}},b.PAD0=236,b.PAD1=17,b.createData=function(a,c,d){for(var e=j.getRSBlocks(a,c),g=new k,h=0;h<d.length;h++){var i=d[h];g.put(i.mode,4),g.put(i.getLength(),f.getLengthInBits(i.mode,a)),i.write(g)}for(var l=0,h=0;h<e.length;h++)l+=e[h].dataCount;if(g.getLengthInBits()>8*l)throw new Error("code length overflow. ("+g.getLengthInBits()+">"+8*l+")");for(g.getLengthInBits()+4<=8*l&&g.put(0,4);0!=g.getLengthInBits()%8;)g.putBit(!1);for(;;){if(g.getLengthInBits()>=8*l)break;if(g.put(b.PAD0,8),g.getLengthInBits()>=8*l)break;g.put(b.PAD1,8)}return b.createBytes(g,e)},b.createBytes=function(a,b){for(var c=0,d=0,e=0,g=new Array(b.length),h=new Array(b.length),j=0;j<b.length;j++){var k=b[j].dataCount,l=b[j].totalCount-k;d=Math.max(d,k),e=Math.max(e,l),g[j]=new Array(k);for(var m=0;m<g[j].length;m++)g[j][m]=255&a.buffer[m+c];c+=k;var n=f.getErrorCorrectPolynomial(l),o=new i(g[j],n.getLength()-1),p=o.mod(n);h[j]=new Array(n.getLength()-1);for(var m=0;m<h[j].length;m++){var q=m+p.getLength()-h[j].length;h[j][m]=q>=0?p.get(q):0}}for(var r=0,m=0;m<b.length;m++)r+=b[m].totalCount;for(var s=new Array(r),t=0,m=0;d>m;m++)for(var j=0;j<b.length;j++)m<g[j].length&&(s[t++]=g[j][m]);for(var m=0;e>m;m++)for(var j=0;j<b.length;j++)m<h[j].length&&(s[t++]=h[j][m]);return s};for(var c={MODE_NUMBER:1,MODE_ALPHA_NUM:2,MODE_8BIT_BYTE:4,MODE_KANJI:8},d={L:1,M:0,Q:3,H:2},e={PATTERN000:0,PATTERN001:1,PATTERN010:2,PATTERN011:3,PATTERN100:4,PATTERN101:5,PATTERN110:6,PATTERN111:7},f={PATTERN_POSITION_TABLE:[[],[6,18],[6,22],[6,26],[6,30],[6,34],[6,22,38],[6,24,42],[6,26,46],[6,28,50],[6,30,54],[6,32,58],[6,34,62],[6,26,46,66],[6,26,48,70],[6,26,50,74],[6,30,54,78],[6,30,56,82],[6,30,58,86],[6,34,62,90],[6,28,50,72,94],[6,26,50,74,98],[6,30,54,78,102],[6,28,54,80,106],[6,32,58,84,110],[6,30,58,86,114],[6,34,62,90,118],[6,26,50,74,98,122],[6,30,54,78,102,126],[6,26,52,78,104,130],[6,30,56,82,108,134],[6,34,60,86,112,138],[6,30,58,86,114,142],[6,34,62,90,118,146],[6,30,54,78,102,126,150],[6,24,50,76,102,128,154],[6,28,54,80,106,132,158],[6,32,58,84,110,136,162],[6,26,54,82,110,138,166],[6,30,58,86,114,142,170]],G15:1335,G18:7973,G15_MASK:21522,getBCHTypeInfo:function(a){for(var b=a<<10;f.getBCHDigit(b)-f.getBCHDigit(f.G15)>=0;)b^=f.G15<<f.getBCHDigit(b)-f.getBCHDigit(f.G15);return(a<<10|b)^f.G15_MASK},getBCHTypeNumber:function(a){for(var b=a<<12;f.getBCHDigit(b)-f.getBCHDigit(f.G18)>=0;)b^=f.G18<<f.getBCHDigit(b)-f.getBCHDigit(f.G18);return a<<12|b},getBCHDigit:function(a){for(var b=0;0!=a;)b++,a>>>=1;return b},getPatternPosition:function(a){return f.PATTERN_POSITION_TABLE[a-1]},getMask:function(a,b,c){switch(a){case e.PATTERN000:return 0==(b+c)%2;case e.PATTERN001:return 0==b%2;case e.PATTERN010:return 0==c%3;case e.PATTERN011:return 0==(b+c)%3;case e.PATTERN100:return 0==(Math.floor(b/2)+Math.floor(c/3))%2;case e.PATTERN101:return 0==b*c%2+b*c%3;case e.PATTERN110:return 0==(b*c%2+b*c%3)%2;case e.PATTERN111:return 0==(b*c%3+(b+c)%2)%2;default:throw new Error("bad maskPattern:"+a)}},getErrorCorrectPolynomial:function(a){for(var b=new i([1],0),c=0;a>c;c++)b=b.multiply(new i([1,g.gexp(c)],0));return b},getLengthInBits:function(a,b){if(b>=1&&10>b)switch(a){case c.MODE_NUMBER:return 10;case c.MODE_ALPHA_NUM:return 9;case c.MODE_8BIT_BYTE:return 8;case c.MODE_KANJI:return 8;default:throw new Error("mode:"+a)}else if(27>b)switch(a){case c.MODE_NUMBER:return 12;case c.MODE_ALPHA_NUM:return 11;case c.MODE_8BIT_BYTE:return 16;case c.MODE_KANJI:return 10;default:throw new Error("mode:"+a)}else{if(!(41>b))throw new Error("type:"+b);switch(a){case c.MODE_NUMBER:return 14;case c.MODE_ALPHA_NUM:return 13;case c.MODE_8BIT_BYTE:return 16;case c.MODE_KANJI:return 12;default:throw new Error("mode:"+a)}}},getLostPoint:function(a){for(var b=a.getModuleCount(),c=0,d=0;b>d;d++)for(var e=0;b>e;e++){for(var f=0,g=a.isDark(d,e),h=-1;1>=h;h++)if(!(0>d+h||d+h>=b))for(var i=-1;1>=i;i++)0>e+i||e+i>=b||(0!=h||0!=i)&&g==a.isDark(d+h,e+i)&&f++;f>5&&(c+=3+f-5)}for(var d=0;b-1>d;d++)for(var e=0;b-1>e;e++){var j=0;a.isDark(d,e)&&j++,a.isDark(d+1,e)&&j++,a.isDark(d,e+1)&&j++,a.isDark(d+1,e+1)&&j++,(0==j||4==j)&&(c+=3)}for(var d=0;b>d;d++)for(var e=0;b-6>e;e++)a.isDark(d,e)&&!a.isDark(d,e+1)&&a.isDark(d,e+2)&&a.isDark(d,e+3)&&a.isDark(d,e+4)&&!a.isDark(d,e+5)&&a.isDark(d,e+6)&&(c+=40);for(var e=0;b>e;e++)for(var d=0;b-6>d;d++)a.isDark(d,e)&&!a.isDark(d+1,e)&&a.isDark(d+2,e)&&a.isDark(d+3,e)&&a.isDark(d+4,e)&&!a.isDark(d+5,e)&&a.isDark(d+6,e)&&(c+=40);for(var k=0,e=0;b>e;e++)for(var d=0;b>d;d++)a.isDark(d,e)&&k++;var l=Math.abs(100*k/b/b-50)/5;return c+=10*l}},g={glog:function(a){if(1>a)throw new Error("glog("+a+")");return g.LOG_TABLE[a]},gexp:function(a){for(;0>a;)a+=255;for(;a>=256;)a-=255;return g.EXP_TABLE[a]},EXP_TABLE:new Array(256),LOG_TABLE:new Array(256)},h=0;8>h;h++)g.EXP_TABLE[h]=1<<h;for(var h=8;256>h;h++)g.EXP_TABLE[h]=g.EXP_TABLE[h-4]^g.EXP_TABLE[h-5]^g.EXP_TABLE[h-6]^g.EXP_TABLE[h-8];for(var h=0;255>h;h++)g.LOG_TABLE[g.EXP_TABLE[h]]=h;i.prototype={get:function(a){return this.num[a]},getLength:function(){return this.num.length},multiply:function(a){for(var b=new Array(this.getLength()+a.getLength()-1),c=0;c<this.getLength();c++)for(var d=0;d<a.getLength();d++)b[c+d]^=g.gexp(g.glog(this.get(c))+g.glog(a.get(d)));return new i(b,0)},mod:function(a){if(this.getLength()-a.getLength()<0)return this;for(var b=g.glog(this.get(0))-g.glog(a.get(0)),c=new Array(this.getLength()),d=0;d<this.getLength();d++)c[d]=this.get(d);for(var d=0;d<a.getLength();d++)c[d]^=g.gexp(g.glog(a.get(d))+b);return new i(c,0).mod(a)}},j.RS_BLOCK_TABLE=[[1,26,19],[1,26,16],[1,26,13],[1,26,9],[1,44,34],[1,44,28],[1,44,22],[1,44,16],[1,70,55],[1,70,44],[2,35,17],[2,35,13],[1,100,80],[2,50,32],[2,50,24],[4,25,9],[1,134,108],[2,67,43],[2,33,15,2,34,16],[2,33,11,2,34,12],[2,86,68],[4,43,27],[4,43,19],[4,43,15],[2,98,78],[4,49,31],[2,32,14,4,33,15],[4,39,13,1,40,14],[2,121,97],[2,60,38,2,61,39],[4,40,18,2,41,19],[4,40,14,2,41,15],[2,146,116],[3,58,36,2,59,37],[4,36,16,4,37,17],[4,36,12,4,37,13],[2,86,68,2,87,69],[4,69,43,1,70,44],[6,43,19,2,44,20],[6,43,15,2,44,16],[4,101,81],[1,80,50,4,81,51],[4,50,22,4,51,23],[3,36,12,8,37,13],[2,116,92,2,117,93],[6,58,36,2,59,37],[4,46,20,6,47,21],[7,42,14,4,43,15],[4,133,107],[8,59,37,1,60,38],[8,44,20,4,45,21],[12,33,11,4,34,12],[3,145,115,1,146,116],[4,64,40,5,65,41],[11,36,16,5,37,17],[11,36,12,5,37,13],[5,109,87,1,110,88],[5,65,41,5,66,42],[5,54,24,7,55,25],[11,36,12],[5,122,98,1,123,99],[7,73,45,3,74,46],[15,43,19,2,44,20],[3,45,15,13,46,16],[1,135,107,5,136,108],[10,74,46,1,75,47],[1,50,22,15,51,23],[2,42,14,17,43,15],[5,150,120,1,151,121],[9,69,43,4,70,44],[17,50,22,1,51,23],[2,42,14,19,43,15],[3,141,113,4,142,114],[3,70,44,11,71,45],[17,47,21,4,48,22],[9,39,13,16,40,14],[3,135,107,5,136,108],[3,67,41,13,68,42],[15,54,24,5,55,25],[15,43,15,10,44,16],[4,144,116,4,145,117],[17,68,42],[17,50,22,6,51,23],[19,46,16,6,47,17],[2,139,111,7,140,112],[17,74,46],[7,54,24,16,55,25],[34,37,13],[4,151,121,5,152,122],[4,75,47,14,76,48],[11,54,24,14,55,25],[16,45,15,14,46,16],[6,147,117,4,148,118],[6,73,45,14,74,46],[11,54,24,16,55,25],[30,46,16,2,47,17],[8,132,106,4,133,107],[8,75,47,13,76,48],[7,54,24,22,55,25],[22,45,15,13,46,16],[10,142,114,2,143,115],[19,74,46,4,75,47],[28,50,22,6,51,23],[33,46,16,4,47,17],[8,152,122,4,153,123],[22,73,45,3,74,46],[8,53,23,26,54,24],[12,45,15,28,46,16],[3,147,117,10,148,118],[3,73,45,23,74,46],[4,54,24,31,55,25],[11,45,15,31,46,16],[7,146,116,7,147,117],[21,73,45,7,74,46],[1,53,23,37,54,24],[19,45,15,26,46,16],[5,145,115,10,146,116],[19,75,47,10,76,48],[15,54,24,25,55,25],[23,45,15,25,46,16],[13,145,115,3,146,116],[2,74,46,29,75,47],[42,54,24,1,55,25],[23,45,15,28,46,16],[17,145,115],[10,74,46,23,75,47],[10,54,24,35,55,25],[19,45,15,35,46,16],[17,145,115,1,146,116],[14,74,46,21,75,47],[29,54,24,19,55,25],[11,45,15,46,46,16],[13,145,115,6,146,116],[14,74,46,23,75,47],[44,54,24,7,55,25],[59,46,16,1,47,17],[12,151,121,7,152,122],[12,75,47,26,76,48],[39,54,24,14,55,25],[22,45,15,41,46,16],[6,151,121,14,152,122],[6,75,47,34,76,48],[46,54,24,10,55,25],[2,45,15,64,46,16],[17,152,122,4,153,123],[29,74,46,14,75,47],[49,54,24,10,55,25],[24,45,15,46,46,16],[4,152,122,18,153,123],[13,74,46,32,75,47],[48,54,24,14,55,25],[42,45,15,32,46,16],[20,147,117,4,148,118],[40,75,47,7,76,48],[43,54,24,22,55,25],[10,45,15,67,46,16],[19,148,118,6,149,119],[18,75,47,31,76,48],[34,54,24,34,55,25],[20,45,15,61,46,16]],j.getRSBlocks=function(a,b){var c=j.getRsBlockTable(a,b);if(void 0==c)throw new Error("bad rs block @ typeNumber:"+a+"/errorCorrectLevel:"+b);for(var d=c.length/3,e=[],f=0;d>f;f++)for(var g=c[3*f+0],h=c[3*f+1],i=c[3*f+2],k=0;g>k;k++)e.push(new j(h,i));return e},j.getRsBlockTable=function(a,b){switch(b){case d.L:return j.RS_BLOCK_TABLE[4*(a-1)+0];case d.M:return j.RS_BLOCK_TABLE[4*(a-1)+1];case d.Q:return j.RS_BLOCK_TABLE[4*(a-1)+2];case d.H:return j.RS_BLOCK_TABLE[4*(a-1)+3];default:return void 0}},k.prototype={get:function(a){var b=Math.floor(a/8);return 1==(1&this.buffer[b]>>>7-a%8)},put:function(a,b){for(var c=0;b>c;c++)this.putBit(1==(1&a>>>b-c-1))},getLengthInBits:function(){return this.length},putBit:function(a){var b=Math.floor(this.length/8);this.buffer.length<=b&&this.buffer.push(0),a&&(this.buffer[b]|=128>>>this.length%8),this.length++}};var l=[[17,14,11,7],[32,26,20,14],[53,42,32,24],[78,62,46,34],[106,84,60,44],[134,106,74,58],[154,122,86,64],[192,152,108,84],[230,180,130,98],[271,213,151,119],[321,251,177,137],[367,287,203,155],[425,331,241,177],[458,362,258,194],[520,412,292,220],[586,450,322,250],[644,504,364,280],[718,560,394,310],[792,624,442,338],[858,666,482,382],[929,711,509,403],[1003,779,565,439],[1091,857,611,461],[1171,911,661,511],[1273,997,715,535],[1367,1059,751,593],[1465,1125,805,625],[1528,1190,868,658],[1628,1264,908,698],[1732,1370,982,742],[1840,1452,1030,790],[1952,1538,1112,842],[2068,1628,1168,898],[2188,1722,1228,958],[2303,1809,1283,983],[2431,1911,1351,1051],[2563,1989,1423,1093],[2699,2099,1499,1139],[2809,2213,1579,1219],[2953,2331,1663,1273]],o=function(){var a=function(a,b){this._el=a,this._htOption=b};return a.prototype.draw=function(a){function g(a,b){var c=document.createElementNS("http://www.w3.org/2000/svg",a);for(var d in b)b.hasOwnProperty(d)&&c.setAttribute(d,b[d]);return c}var b=this._htOption,c=this._el,d=a.getModuleCount();Math.floor(b.width/d),Math.floor(b.height/d),this.clear();var h=g("svg",{viewBox:"0 0 "+String(d)+" "+String(d),width:"100%",height:"100%",fill:b.colorLight});h.setAttributeNS("http://www.w3.org/2000/xmlns/","xmlns:xlink","http://www.w3.org/1999/xlink"),c.appendChild(h),h.appendChild(g("rect",{fill:b.colorDark,width:"1",height:"1",id:"template"}));for(var i=0;d>i;i++)for(var j=0;d>j;j++)if(a.isDark(i,j)){var k=g("use",{x:String(i),y:String(j)});k.setAttributeNS("http://www.w3.org/1999/xlink","href","#template"),h.appendChild(k)}},a.prototype.clear=function(){for(;this._el.hasChildNodes();)this._el.removeChild(this._el.lastChild)},a}(),p="svg"===document.documentElement.tagName.toLowerCase(),q=p?o:m()?function(){function a(){this._elImage.src=this._elCanvas.toDataURL("image/png"),this._elImage.style.display="block",this._elCanvas.style.display="none"}function d(a,b){var c=this;if(c._fFail=b,c._fSuccess=a,null===c._bSupportDataURI){var d=document.createElement("img"),e=function(){c._bSupportDataURI=!1,c._fFail&&_fFail.call(c)},f=function(){c._bSupportDataURI=!0,c._fSuccess&&c._fSuccess.call(c)};return d.onabort=e,d.onerror=e,d.onload=f,d.src="data:image/gif;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==",void 0}c._bSupportDataURI===!0&&c._fSuccess?c._fSuccess.call(c):c._bSupportDataURI===!1&&c._fFail&&c._fFail.call(c)}if(this._android&&this._android<=2.1){var b=1/window.devicePixelRatio,c=CanvasRenderingContext2D.prototype.drawImage;CanvasRenderingContext2D.prototype.drawImage=function(a,d,e,f,g,h,i,j){if("nodeName"in a&&/img/i.test(a.nodeName))for(var l=arguments.length-1;l>=1;l--)arguments[l]=arguments[l]*b;else"undefined"==typeof j&&(arguments[1]*=b,arguments[2]*=b,arguments[3]*=b,arguments[4]*=b);c.apply(this,arguments)}}var e=function(a,b){this._bIsPainted=!1,this._android=n(),this._htOption=b,this._elCanvas=document.createElement("canvas"),this._elCanvas.width=b.width,this._elCanvas.height=b.height,a.appendChild(this._elCanvas),this._el=a,this._oContext=this._elCanvas.getContext("2d"),this._bIsPainted=!1,this._elImage=document.createElement("img"),this._elImage.style.display="none",this._el.appendChild(this._elImage),this._bSupportDataURI=null};return e.prototype.draw=function(a){var b=this._elImage,c=this._oContext,d=this._htOption,e=a.getModuleCount(),f=d.width/e,g=d.height/e,h=Math.round(f),i=Math.round(g);b.style.display="none",this.clear();for(var j=0;e>j;j++)for(var k=0;e>k;k++){var l=a.isDark(j,k),m=k*f,n=j*g;c.strokeStyle=l?d.colorDark:d.colorLight,c.lineWidth=1,c.fillStyle=l?d.colorDark:d.colorLight,c.fillRect(m,n,f,g),c.strokeRect(Math.floor(m)+.5,Math.floor(n)+.5,h,i),c.strokeRect(Math.ceil(m)-.5,Math.ceil(n)-.5,h,i)}this._bIsPainted=!0},e.prototype.makeImage=function(){this._bIsPainted&&d.call(this,a)},e.prototype.isPainted=function(){return this._bIsPainted},e.prototype.clear=function(){this._oContext.clearRect(0,0,this._elCanvas.width,this._elCanvas.height),this._bIsPainted=!1},e.prototype.round=function(a){return a?Math.floor(1e3*a)/1e3:a},e}():function(){var a=function(a,b){this._el=a,this._htOption=b};return a.prototype.draw=function(a){for(var b=this._htOption,c=this._el,d=a.getModuleCount(),e=Math.floor(b.width/d),f=Math.floor(b.height/d),g=['<table style="border:0;border-collapse:collapse;">'],h=0;d>h;h++){g.push("<tr>");for(var i=0;d>i;i++)g.push('<td style="border:0;border-collapse:collapse;padding:0;margin:0;width:'+e+"px;height:"+f+"px;background-color:"+(a.isDark(h,i)?b.colorDark:b.colorLight)+';"></td>');g.push("</tr>")}g.push("</table>"),c.innerHTML=g.join("");var j=c.childNodes[0],k=(b.width-j.offsetWidth)/2,l=(b.height-j.offsetHeight)/2;k>0&&l>0&&(j.style.margin=l+"px "+k+"px")},a.prototype.clear=function(){this._el.innerHTML=""},a}();QRCode=function(a,b){if(this._htOption={width:256,height:256,typeNumber:4,colorDark:"#000000",colorLight:"#ffffff",correctLevel:d.H},"string"==typeof b&&(b={text:b}),b)for(var c in b)this._htOption[c]=b[c];"string"==typeof a&&(a=document.getElementById(a)),this._android=n(),this._el=a,this._oQRCode=null,this._oDrawing=new q(this._el,this._htOption),this._htOption.text&&this.makeCode(this._htOption.text)},QRCode.prototype.makeCode=function(a){this._oQRCode=new b(r(a,this._htOption.correctLevel),this._htOption.correctLevel),this._oQRCode.addData(a),this._oQRCode.make(),this._el.title=a,this._oDrawing.draw(this._oQRCode),this.makeImage()},QRCode.prototype.makeImage=function(){"function"==typeof this._oDrawing.makeImage&&(!this._android||this._android>=3)&&this._oDrawing.makeImage()},QRCode.prototype.clear=function(){this._oDrawing.clear()},QRCode.CorrectLevel=d}();
</script>

<style>
 #yesImg,#noImg,.loader{
	vertical-align: middle;
	display:none;
 }
 
 .loader {
	border: 2px solid #f3f3f3;
	border-radius: 50%;
	border-top: 2px solid #acd7ec;
	width: 10px;
	height: 10px;
	-webkit-animation: spin 2s linear infinite; /* Safari */
	animation: spin 2s linear infinite;
  }
  
  /* Safari */
  @-webkit-keyframes spin {
	0% { -webkit-transform: rotate(0deg); }
	100% { -webkit-transform: rotate(360deg); }
  }
  
  @keyframes spin {
	0% { transform: rotate(0deg); }
	100% { transform: rotate(360deg); }
  }

/* style for steps  */

.accordion-item{
	border: 1px solid #dee2e6;
    border-radius: 6px;
    /* padding: 10px; */
    margin-bottom: 20px;
    background-color: #f6f7f7;
}

.accordion-content {
    display: none;

}

.accordion-content div {
	/* padding-left: 20px; */
	border-top: 1px solid #dee2e6;
    padding: 20px 20px;
    border-bottom-left-radius: 5px;
    background: white;
    border-bottom-right-radius: 5px;
}
.accordion-title {
    cursor: pointer;
    /* font-size: 1rem!important; */
    font-weight: 300;
    color: #1eacba;
    text-decoration: underline;
	margin: 15px;
}

.accordion-title:hover {
    color: grey;
}

.arrow1 {
	display: inline-block;
    margin-left: 8px;
    transition: transform 0.3s ease-in-out;
    font-size: medium;
    float: right;
    margin-right: 10px;
    margin-top: 13px;
	border: 1px solid #1eacba;
    padding: 5px 10px;
    border-radius: 50%;
	margin-top: 5px;
}

.expanded .arrow1 {
    transform: rotate(180deg);
}
  </style>
  <style>
	.tablink {
		margin-top: 20px;
	background-color: #555;
	color: white;
	float: left;
	border: none;
	outline: none;
	cursor: pointer;
	padding: 14px 16px;
	font-size: 17px;
	width: 370px;
	/* margin-left: 20px; */
	}

	/* Change background color of buttons on hover */
	.tablink:hover {
	background-color: #777;
	}

	/* Set default styles for tab content */
	.tabcontent {
	color: white;
	display: none;
	padding: 50px;
	text-align: center;
	}

	/* Style each tab content individually */
	#London {background-color:red;}
	#Paris {background-color:green;}
	#Tokyo {background-color:blue;}
	#Oslo {background-color:orange;}

</Style>
<div class="accordion">
	<div class="wrap woocommerce os_row">
		<form method="post" id="mainform"  enctype="multipart/form-data">
		
			<div class="header">
				<?php
				// $notice_text = "<p>";
				// $notice_text.=__('Sim To Shop extension for Woocomerce is a text-messaging service for sending
				// notifications, alerts, reminders, confirmations and SMS marketing campaigns.
				// ', 'insim');
				// $notice_text.='<b><a href="https://ardary-sms.com/solo" target="_blank"> Download to the documentation </a></b>';
				// $notice_text.=__(' here for more information.', 'insim');
				// $notice_text.="</p><p>";
				// $notice_text.=__("To use this extension you'll need to create a Solo account (click here to watch
				// the tutorial).", 'insim');
				// $notice_text.="</p><p>";
				// $notice_text.='Contact us to  <b><a href="mailto:contact@ardary-sms.com" target="_blank">contact@ardary-sms.com  </a></b> for any questions or suggestions regarding';
				// $notice_text.="</p>";
				// $notice_text.="<p>&nbsp</p>";
				// $notice_text.="<p>&nbsp</p>";
				// echo $notice_text;
				?>
			</div>
			<div class="ows" >
				<?php
				if (!$this->bAuth)
				{
					
					echo '
					<div style= "margin-top: 30px; margin-bottom: 50px; padding: 20px;
					" id="message" class="updated"><div style =" margin-left: 20px;"><h2 style="margin-right: 5px;"><strong>' . __('How it works
					','insim') . '<br/></strong></h2>
					<p style="font-size: 1.23rem;"><b> This plug-in allows you to send SMS to your store customers using your mobile SIM.</b></p>
					</div></div>';
				}
				// else
				// {
				// 	echo '<h3>' . __('Octopush Balance', 'octopush-sms') . '</h3><br><h3>' . number_format($this->balance, 0, ',', ' ') . ' SMS</h3>';
				// }
				?>
			</div>
			<div class="accordion-item">
				<div class="accordion-title expanded"><strong style="font-size: 2rem;"><?php _e('STEP 1: Install inSIM on mobile'); ?></strong><span class="arrow1">&#9660;</span></div>
				<div class="accordion-content" style="display: block; background-color:white;">
					<div style="padding-left: 20px;">On your android smartphone, install <a href ="https://play.google.com/store/apps/details?id=com.wstechnologies.ardarysolo" target ="_blank">Ardary inSIM app</a> from Google Playstore</div>
					<div style="border:none; display: flex; justify-content: space-between; width:100%; max-width:780px;">
						<div style="width:45%;"><p style="font-family: bold; text-align: center;">Get inSIM app on Google Playstore</p><p style=" text-align: center;"><a href ="https://play.google.com/store/apps/details?id=com.wstechnologies.ardarysolo" target ="_blank" style="outline: none;"><img src="/wp_services/wp-content/plugins/insim/admin/partials/../img/get-insim-from-playstore.png" style="width:80%; cursor: pointer;" /></a></div>
						<div style="width:45%;"><p style="font-family: bold; text-align: center;">QR link to inSIM app</p><p  style=" text-align: center;"><div style =" border: none; padding-top: 5px; margin-left: 23%;" id="qrcode_playstore" valign="top"></div></p></div>
					</div>
				</div>
			</div>
			<div class="accordion-item">
				<div class="accordion-title"><strong style="font-size: 2rem;"><?php _e('STEP 2: Connect my store to my mobile SIM'); ?></strong><span class="arrow1">&#9660;</span></div>
				<div class="accordion-content" style="display: none; width:100%!important;">
					<div style="border: none;  width:100%;">
					
						<button onclick ="document.getElementById('qr').style.display ='flex';
						document.getElementById('qrcode').style.display ='block';
						document.getElementById('qr-text').style.display ='';
						document.getElementById('qrcode_add').style.backgroundColor= '#777';
						document.getElementById('manually_add').style.backgroundColor= '#555';
						document.getElementById('ln1').style.display ='none';
						document.getElementById('ln2').style.display ='none';
						document.getElementById('title_manually').style.display ='none';
						" style ="border-radius: 15px 0 0 0; background-color: #777;" class="tablink" id="qrcode_add"><div style ="font-size:20px;color:#765f5f;text-align:center;line-height:0; 0;border-radius:50%;background:white; margin-right: 10px; padding: 7px 14px; display: inline;">1</div>With QR CODE</button>

						<button onclick ="document.getElementById('ln1').style.display ='inline-block';
						document.getElementById('qrcode_add').style.backgroundColor= '#555';
						document.getElementById('manually_add').style.backgroundColor= '#777';
						document.getElementById('ln2').style.display ='inline-block';
						document.getElementById('title_manually').style.display ='block';
						document.getElementById('qrcode').style.display ='none';
						document.getElementById('qr-text').style.display ='none';
						document.getElementById('qr').style.display ='none';

						"
						class="tablink" id="manually_add"><div style ="font-size:20px;color:#765f5f;text-align:center;line-height:0; 0;border-radius:50%;background:white; margin-right: 10px; padding: 7px 14px; display: inline;  margin-left: 20px;">2</div>Manually</button>


				
						<script>
							document.getElementById('qrcode_add').addEventListener('click', (e) => {
								e.preventDefault();
							});
							document.getElementById('manually_add').addEventListener('click', (e) => {
								e.preventDefault();
							});
						</script>		
						<div  style ="width:100%;   margin-left: 10px; border: none;" class="ows">
							<table class="form-table" style="margin-top: 25px; display: block;">
								<tr>
									<td>		<!-- (B) GENERATE QR CODE HERE -->
										<div id="qr" style="border: none; display: flex; justify-content: space-between; width:100%; max-width:700px">
											<div style="width: 30%; margin: auto; border: none;">
												<img style="height: 90%; width: 170px;" src="/wp_services/wp-content/plugins/insim/admin/partials/../img/scan-qr.gif" alt="steps of scanning QR CODE"/>
											</div>
											<div style="width: 60%; margin: 20px 20px; padding: 0px 20px 20px 0px; border: none;">
												<div style ="z-index: 1000; display: block; border: none;" ><b id = "qr-text" style ="padding-left: 10px;"></b></div>
												<div style ="margin: 0px 15px 0px 24% !important; border: none;" id="qrcode" valign="top"></div>
											</div>
										</div>
										<!-- (C) CREATE QR CODE ON PAGE LOAD -->
										<script>
										window.addEventListener("load", () => {
										var qrc = new QRCode(document.getElementById("qrcode"), {text: "<?php echo (get_bloginfo('url').'/wp-admin/admin-ajax.php?action=get-data') ?>", width: 120, height: 120});
										//document.getElementById('qrcode').style.marginLeft ="200px";
										});
										window.addEventListener("load", () => {
										var qrc = new QRCode(document.getElementById("qrcode_playstore"), {text: "https://play.google.com/store/apps/details?id=com.wstechnologies.ardarysolo", width: 120, height: 120});
										//document.getElementById('qrcode').style.marginLeft ="200px";
										});
										document.getElementById('qr-text').innerHTML = "<p style='text-align: center; margin: 0;padding: 0;line-height: 1; font-weight:bold'><strong style='font-size: 1rem;'>Scan the QR CODE with your mobile from inSIM app in the e-Commerce section.</strong></p>";
										</script>
									</td>
								</tr>
								<tr>
									<td>
										<div style ="display: none; line-height: 1; font-weight:bold; font-size: 1.15rem;  padding-top: 25px; width: 95%; max-width: 700px; border: none; text-align: center;" id ="title_manually" ><strong>Get the credentials needed bellow by clicking on the WooCommerce icon in inSIM app.</strong></div>
									</td>
								</tr>
								<tr id ="ln1" style ="display: none;   margin-left: 20; width: 95%;" valign="top">
										<td style="display:flex; justify-content: left; margin-left: 100px; padding: 20px 10px 0px 0;" scope="row" class="titledesc">
											<label for="solo_sms_email"><?php _e('inSIM Login :', 'insim'); ?></label>
										</td>
										<td class="forminp forminp-email" style="display:flex; justify-content: left; margin-left: 100px;">
											<input
												name="solo_sms_email"
												id="solo_sms_email"
												type="email"
												style="min-width:400px;"
												value="<?php echo (esc_html(get_option('solo_sms_email'))); ?>"
												class=""
												/> 
												<!-- <span class="description"><?php _e('You can find it on your Solo Account', 'insim'); ?></span>						 -->
										</td>
								</tr>

								<tr id ="ln2" style ="display: none;   margin-left: 0; width: 95%;" valign="top">
									<td style="display:flex; justify-content:  left; margin-left: 100px; padding: 20px 10px 0px 0;" scope="row" class="titledesc">
										<label for="solo_sms_key"><?php _e('inSIM API Key :', 'insim'); ?></label>
									</td>
									<td class="forminp forminp-phone" style="display:flex; justify-content:  left; margin-left: 100px; boder: none;">
										<input
											name="solo_sms_key"
											id="solo_sms_key"
											type="text"
											style="min-width:400px;"
											maxlength="255"
											value="<?php echo (esc_html(get_option('solo_sms_key'))); ?>"
											class=""
											/> 
									</td>
									<td >
										<p style="margin-left: 100px; ">You can find your access key in the app, in the API section, </br>or on <a href='https://insim.app' target='_blank'>your inSIM Account</a>, in settings > API.</p>	
										</br>
										<p class="submit">
											<button  type="button" id="saveCredentials" name="saveCredentials" class="testcredentials button-primary" style = "cursor: pointer; margin-left:84%;" onclick="document.getElementById('mainform').submit();">Save Credentials</button>
										</p>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
				<div class="accordion-item">
					<div class="accordion-title">
						<strong style="font-size: 2rem;"><?php _e('STEP 3: Verify your credentials'); ?></strong><span class="arrow1">&#9660;</span>
					</div>
					    <div  class="accordion-content">
							<div style="display: flex;justify-content: left;">
								<div style="border: none;">
								In order to ensure your e-store is synchronized with your mobile :
								</div>
								<div  style="border: none;">
									<button  type="button" name="testcredentials" class="testcredentials button-primary" style = "cursor: pointer;" >
										<span style="display:inline-block;">
											Test connection <span  class="loader" id="loader">&nbsp</span>
										</span> 
										<img id="yesImg" src="<?php echo (esc_html($yesimage));?>"  alt="inSIM"/>
										<img id="noImg" src="<?php echo (esc_html($noimage));?>"  alt="inSIM"/>
									</button>			
								</div>
							</div>
						</div>
				</div>
					<div  class="accordion-item">
						<div  class="accordion-title">
							<strong style="font-size: 2rem;"><?php _e('STEP 4: Set up a mobile number for testing'); ?></strong><span class="arrow1">&#9660;</span>
						</div>
					<div  class="accordion-content">
						<div style=" border:none;">
							<div scope="row" class="titledesc" style="margin-left: 20px; border: none; padding: 0px 0px 0px 0px;">
								<label for="phone_admin"><?php _e('Ex.: your phone number', 'insim'); ?></label>
							</div>
							<div class="forminp forminp-phone" style="padding-top: 10px; border: none; padding-bottom: 0;">
								<input
								placeholder="+1xxxxxxxxx (international format)"
									name="phone_admin"
									id="phone_admin"
									type="text"
									style="min-width:400px;"
									maxlength="255"
									value="<?php echo (esc_html(get_option('phone_admin'))); ?>"
									class=""
									/> 
							</div>
						<div style=" border:none; padding-top: 0;">
						<div style="margin-left: 20px; display: none!important; border:none;">
							<div id="message" style ="color: green; display: none; font-size: small; width: 185%;" ></div>	
						</div>
						<div style="border:none; width: 400px; padding-right: 0;">
							<button type="button" name="testsms" class="testsms button-primary float-right" >
								<span style="display:inline-block;">
								Send test SMS to my phone <span  class="loader" id="loader">&nbsp</span>
								</span> 
								<img id="yesImg" src="<?php echo (esc_html($yesimage));?>"  alt="inSIM"/>
								<img id="noImg" src="<?php echo (esc_html($noimage));?>"  alt="inSIM"/>
							</button>
						</div>
					</div>
				</div>
				</div>
				</div>
				<p class="submit">
					<input id ="save" style = "background-color: green; border-color: green;" name="save" class="btn btn-lg btn-primary"
					type="submit" value="Save changes">	
					<i id ="savemsg" style="display: none; color: green;">Your changes have been saved successfully.</i>		
				</p>
				
		</form>
	</div>
</div>


<style>
	.form-table td {
		padding: 0px;
	}
</style>
<script type="text/javascript" >
	window.onload = function () {
					var reloading = sessionStorage.getItem("save");
					if (reloading) {
						sessionStorage.removeItem("save");
						document.getElementById('savemsg').style.display= 'inline-block';
					} else{
						document.getElementById('savemsg').style.display= 'none';
					}
				}
	jQuery(document).ready(function() {
		jQuery(".accordion-title").click(function() {
			jQuery(this).next(".accordion-content").slideToggle();
			jQuery(this).toggleClass("expanded");

		});
	});
	jQuery(document).ready(function($) {
		var form = document.getElementById('save')
				form.addEventListener('click', function () {
					
					sessionStorage.setItem("save", "true");
				})

				


		$('.testcredentials').click(function(event){
			event.preventDefault();
			
		

			urlSend = "https://www.ardary-sms.com/api/verif_plug_version_wooc.php";
			dataSend = {
				'versionPlug' : '1.0.0',
			};
			$.post(urlSend, JSON.stringify(dataSend), function(response) {
				response = JSON.parse(response);
				if(response.isTrue == "false" && response.isOblg =="true") {
					alert('Vous devez utiliser la dernière version de notre plug-in !');
					return -1;
				} else if(response.isTrue == "false" && response.isOblg =="false")  {
					alert('Vous devez utiliser la dernière version de notre plug-in !');
					return false;
					document.getElementById("loader").style.display = "inline-block";
					document.getElementById("yesImg").style.display = "none";
					document.getElementById("noImg").style.display = "none";
					var data = {
						action: 'test_connection',
						login: document.getElementById("solo_sms_email").value,
						acces_key: document.getElementById("solo_sms_key").value				
					};
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					let sendUrl = 'https://ardary-sms.com:3008/client/getClientDetailsByKey';
					$.post(sendUrl, data, function(response) {
						response = response.data.go;
						//console.log('rslt: '+response);
						if(response==true){
							document.getElementById("loader").style.display = "none";
							document.getElementById("yesImg").style.display = "inline-block";
							document.getElementById("noImg").style.display = "none";
							<?php if($_POST['solo_sms_key'] != '' && $_POST['solo_sms_key'] != null) {
								update_option('solo_sms_key', sanitize_user($_POST['solo_sms_key'])); 
								update_option('isConnectedToInsim', true);
								
								// function httpPost2($url, $data)
								// {
								// 	$curl = curl_init($url);
								// 	curl_setopt($curl, CURLOPT_POST, true);
								// 	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
								// 	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 'false');
								// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
								// 	curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
								// 	curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
								// 	curl_setopt($curl, CURLOPT_TIMEOUT_MS, 0.1);
								// 	$response = curl_exec($curl);
								// 	curl_close($curl);
								// 	return $response;
								// }

								$urlAdd = "https://www.ardary-sms.com/api/add_shop_to_crm.php";
								$dataAdd = [
									'header' => [
										'key' => get_option('solo_sms_key'),
										'email' =>  get_option('solo_sms_email'),
										'frompluguin' => true
									],
									'versionPlug' =>'1.0.0',
									'shop_name' =>get_bloginfo(),
									'shop_url' =>get_permalink( wc_get_page_id( 'shop' )),
									'domain' => get_site_url(),
									'type' =>'wooc',
									'crm_url_profile' =>admin_url('user-edit.php?user_id=[id_customer]'),
									'crm_url_orders' =>admin_url('edit.php?post_status=all&post_type=shop_order&_customer_user=[id_customer]')
										
								];
								$args = array(
									'body'        => json_encode($dataAdd),
									'timeout'     => 45,
        							'sslverify'   => false,
								);

								$res = wp_remote_post( $urlAdd, $args );
							
									//$res = httpPost2($urlAdd, json_encode($dataAdd));


							}?>
							<?php if($_POST['solo_sms_email']) update_option('solo_sms_email', sanitize_email($_POST['solo_sms_email'])); ?>
							<?php if($_POST['phone_admin'] != '' && $_POST['phone_admin'] != null) update_option('phone_admin', sanitize_key($_POST['phone_admin'])); ?>
							<?php if ( ($_POST['solo_sms_email']) && $_POST['phone_admin'] != '' && $_POST['phone_admin'] != null) update_option('isConnectedToInsim', true)?>

							//console.log(response);
						}
						else{
							document.getElementById("loader").style.display = "none";
							document.getElementById("yesImg").style.display = "none";
							document.getElementById("noImg").style.display = "inline-block";
							return false;
						}
						
					});
					return false;
				} else {
					
					document.getElementById("loader").style.display = "inline-block";
					document.getElementById("yesImg").style.display = "none";
					document.getElementById("noImg").style.display = "none";
					var dataVerif = {
						action: 'get-access',
								
					};
					const currentUrlVerif = window.location.href;
					// Extract the base URL (excluding any query parameters or path)
					const baseUrlV = currentUrlVerif.split('/wp-admin')[0];
					let baseUrlVerif = baseUrlV+'/wp-admin/admin-ajax.php?action=get-access';
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					$.post(baseUrlVerif, dataVerif, function(responseVerif) {
						responseVerif = JSON.parse(responseVerif);
						if (responseVerif == false) {
							document.getElementById("loader").style.display = "none";
							document.getElementById("yesImg").style.display = "none";
							document.getElementById("noImg").style.display = "inline-block";
							return false;
						}
						document.getElementById('solo_sms_key').value = responseVerif.key;
						document.getElementById('solo_sms_email').value = responseVerif.email;
						if(document.getElementById("solo_sms_email").value==''){
							alert("Solo login should not be empty.");
							return false;
						}
						else if(document.getElementById("solo_sms_key").value==''){
							alert("Solo API key should not be empty.");
							return false;
						}
						var data = {
							action: 'test_connection',
							login: responseVerif.email,
							acces_key: responseVerif.key				
						};
						// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
						let sendUrl = 'https://ardary-sms.com:3008/client/getClientDetailsByKey';
						$.post(sendUrl, data, function(response) {
							response = response.data.go;
							//console.log('rslt: '+response);
							if(response==true){
								document.getElementById("loader").style.display = "none";
								document.getElementById("yesImg").style.display = "inline-block";
								document.getElementById("noImg").style.display = "none";
								<?php if($_POST['solo_sms_key'] != '' && $_POST['solo_sms_key'] != null) {
									update_option('solo_sms_key', sanitize_user($_POST['solo_sms_key'])); 
									
									// function httpPost2($url, $data)
									// {
									// 	$curl = curl_init($url);
									// 	curl_setopt($curl, CURLOPT_POST, true);
									// 	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
									// 	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 'false');
									// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
									// 	curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
									// 	curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
									// 	curl_setopt($curl, CURLOPT_TIMEOUT_MS, 0.1);
									// 	$response = curl_exec($curl);
									// 	curl_close($curl);
									// 	return $response;
									// }

									$urlAdd = "https://www.ardary-sms.com/api/addcontactWooc.php";
									$dataAdd = [
										'header' => [
											'key' => get_option('solo_sms_key'),
											'email' =>  get_option('solo_sms_email'),
											'frompluguin' => true
										],
										'versionPlug' =>'1.0.0',
										'shop_name' =>get_bloginfo(),
										'shop_url' =>get_permalink( wc_get_page_id( 'shop' )),
										'type' =>'wooc'
									];

									$args = array(
										'body'        => json_encode(array( 'data' => base64_encode(gzcompress(json_encode($dataAdd))))),
										'timeout'     => 45,
										'sslverify'   => false,
									);

									$res = wp_remote_post( $urlAdd, $args );
								
										//$res = httpPost2($urlAdd, json_encode(array( 'data' => base64_encode(gzcompress(json_encode($dataAdd))))));


								}?>
								<?php if($_POST['solo_sms_email'] != '' && $_POST['solo_sms_email'] != null) update_option('solo_sms_email', sanitize_email($_POST['solo_sms_email'])); ?>
								<?php if($_POST['phone_admin'] != '' && $_POST['phone_admin'] != null) update_option('phone_admin', sanitize_key($_POST['phone_admin'])); ?>

								const currentUrl = window.location.href;

								// Extract the base URL (excluding any query parameters or path)
								const baseUrl = currentUrl.split('/wp-admin')[0];
								let sendUrl = baseUrl+'/wp-admin/admin-ajax.php?action=change-setting';
								//event.preventDefault();
								var data = {
									op: true,
									

								};
								//console.log(data);
								$.post(sendUrl, data, function(response) {
								})
								//console.log(response);
							}
							else{
								document.getElementById("loader").style.display = "none";
								document.getElementById("yesImg").style.display = "none";
								document.getElementById("noImg").style.display = "inline-block";
								return false;
							}
							
						});
					return true;
				});
				}
			});

			
		});


	});
</script>
<script type="text/javascript" >
	let sendUrl = 'https://www.ardary-sms.com/newsite/trialApiSolo.php';
	jQuery(document).ready(function($) {
		$('.testsms').click(function(event){
			event.preventDefault();
			urlSend = "https://www.ardary-sms.com/api/verif_plug_version_wooc.php";
			dataSend = {
				'versionPlug' : '1.0.0',
			};
			$.post(urlSend, JSON.stringify(dataSend), function(response) {
				response = JSON.parse(response);
				if(response.isTrue == "false" && response.isOblg =="true") {
					event.stopPropagation();
					alert('Vous devez utiliser la dernière version de notre plug-in !');
					return -1;
				} else if(response.isTrue == "false" && response.isOblg =="false")  {
					alert('Vous devez utiliser la dernière version de notre plug-in !');
					var myDate = new Date();
					var dateSend = myDate.toISOString().replaceAll('/', '-').replaceAll('T', ' ').substring(0, 19);
					var data = {
						action: 'test_sms',
						message : 'Bonjour, message de test !',
						phonenumber : document.getElementById("phone_admin").value,
						date_to_send: dateSend,
						urltracking: 'https://www.google.com/?h2s23',
						login: document.getElementById("solo_sms_email").value,
						accesskey: document.getElementById("solo_sms_key").value				
					};
					$.post(sendUrl, data, function(response) {
						response = JSON.parse(response)[0];
						if ( response.error != null && response.error != undefined ){
							document.getElementById('message').innerHTML = 'An error has been occurred. Please verify that your phone number is in the international format !';
							document.getElementById('message').style.color = 'red';
							document.getElementById('message').style.display = 'block';
						}
						else if (response.id_sms_api != null && response.id_sms_api != undefined){
							document.getElementById('message').innerHTML = 'A test message has been sent your phone number.';
							document.getElementById('message').style.color = 'green';
							document.getElementById('message').style.display = 'block';
						}
						
					});
				} else {

			var myDate = new Date();
			var dateSend = myDate.toISOString().replaceAll('/', '-').replaceAll('T', ' ').substring(0, 19);
			var data = {
				action: 'test_sms',
				message : 'Bonjour, message de test !',
				phonenumber : document.getElementById("phone_admin").value,
				date_to_send: dateSend,
				urltracking: 'https://www.google.com/?h2s23',
				login: document.getElementById("solo_sms_email").value,
				accesskey: document.getElementById("solo_sms_key").value				
			};
			$.post(sendUrl, data, function(response) {
				response = JSON.parse(response)[0];
				if ( response.error != null && response.error != undefined ){
					document.getElementById('message').innerHTML = 'An error has been occurred. Please verify that your phone number is in the international format !';
					document.getElementById('message').style.color = 'red';
					document.getElementById('message').style.display = 'block';
				}
				else if (response.id_sms_api != null && response.id_sms_api != undefined){
					document.getElementById('message').innerHTML = 'A test message has been sent your phone number. It can take some minutes to receive it, please be patient ...';
					document.getElementById('message').style.color = 'green';
					document.getElementById('message').style.display = 'block';
				}
				
			});
		}
	});
			
		});

	});
</script>