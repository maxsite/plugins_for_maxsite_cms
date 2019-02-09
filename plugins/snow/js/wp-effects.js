/*
Plugin Name: WP-Effects
Plugin URI: http://plugins.wp-themes.ws/wp-effects
Description: Allows you to display effects on your website - Such as snow and leaves!
Version: 1.0.0
Author: WP-Themes.ws
Author URI: http://wp-themes.ws

    Copyright 2010 WP-Themes.ws - support@wp-themes.ws

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// var image="http://localhost/codeigniter/application/maxsite/templates/_a8/js/snow2.gif";  //Image path should be given here
// var no = 10; // No of images should fall
var time = 0; // Configure whether image should disappear after x seconds (0=never):
// var speed = 20; // Fix how fast the image should fall
var i, dwidth = 900, dheight =500; 
var nht = dheight;
var toppos = 0;
var type = "snow1";

if(document.all){
	var ie4up = 1;
}else{
	var ie4up = 0;
}

if(document.getElementById && !document.all){
	var ns6up = 1;
}else{
	var ns6up = 0;
}

function getScrollXY() {
  var scrOfX = 10, scrOfY = 10;
  if( typeof( window.pageYOffset ) == 'number' ) {
 
    scrOfY =window.pageYOffset;
    scrOfX = window.pageXOffset;
  } else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {

    scrOfY = document.body.scrollTop;
    scrOfX = document.body.scrollLeft;
  } else if( document.documentElement &&
      ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {

   scrOfY = document.documentElement.scrollTop;
   scrOfX = document.documentElement.scrollLeft;
  }
  return [ scrOfX, scrOfY ];
}

var timer;

function ranrot()
{

var a = getScrollXY()
if(timer)
{
	clearTimeout(timer);
}
toppos = a[1];
dheight = nht+a[1];
//alert(dheight);

timer = setTimeout('ranrot()',2000);
}
 	
ranrot();
 	
function iecompattest()
{
	if(document.compatMode && document.compatMode!="BackCompat")
	{
		return document.documentElement;
	}else{
		return document.body;
	}
	
}
if (ns6up) {
	dwidth = window.innerWidth;
	dheight = window.innerHeight;
} 
else if (ie4up) {
	dwidth = iecompattest().clientWidth;
	dheight = iecompattest().clientHeight;
}

nht = dheight;

var cv = new Array();
var px = new Array();     
var py = new Array();    
var am = new Array();    
var sx = new Array();   
var sy = new Array();  

for (i = 0; i < no; ++ i) {  
	cv[i] = 0;
	px[i] = Math.random()*(dwidth-100); 
	py[i] = Math.random()*dheight;   
	am[i] = Math.random()*20; 
	sx[i] = 0.02 + Math.random()/10;
	sy[i] = 0.7 + Math.random();
	/*
	if (type=="Custom") {
	var randomnumber=Math.floor(Math.random()*11)
	
	if (randomnumber <= 3) {
	imagee=1;
	} else if (randomnumber <= 6 && randomnumber > 3) {
	imagee=2;
	} else if (randomnumber <= 10 && randomnumber > 6) {
	imagee=3;
	}
	
	if (imagee==1) {
	image="http://localhost/codeigniter/application/maxsite/templates/_a8/js/snow2.gif";
	} else if (imagee==2) {
	image="http://localhost/codeigniter/application/maxsite/templates/_a8/js/snow2.gif";
	} else if (imagee==3) {
	image="http://localhost/codeigniter/application/maxsite/templates/_a8/js/snow2.gif";
	}
	
    if (imagee=="") {
	image="http://localhost/codeigniter/application/maxsite/templates/_a8/js/snow2.gif";
	}
	}
	*/
	if (image=="") {
	} else {
	document.write("<div id=\"dot"+ i +"\" style=\"POSITION: absolute; Z-INDEX: "+ 900+i +"; VISIBILITY: visible; TOP: 15px;LEFT: 15px;\"><img src='"+image+"' border=\"0\"><\/div>");
	}
}

function animation() {
	for (i = 0; i < no; ++ i) {
		py[i] += sy[i];
      		if (py[i] > dheight-50) {
        		px[i] = Math.random()*(dwidth-am[i]-100);
        		py[i] = toppos;
        		sx[i] = 0.02 + Math.random()/10;
        		sy[i] = 0.7 + Math.random();
      		}
      		cv[i] += sx[i];
      		document.getElementById("dot"+i).style.top=py[i]+"px";
      		document.getElementById("dot"+i).style.left=px[i] + am[i]*Math.sin(cv[i])+"px";  
    	}
    	atime=setTimeout("animation()", speed);

}

function hideimage(){
	if (window.atime) clearTimeout(atime)
		for (i=0; i<no; i++) 
			document.getElementById("dot"+i).style.visibility="hidden"
}
if (ie4up||ns6up){
animation();
if (time>0)
	setTimeout("hideimage()", time*1000)
}
animation();