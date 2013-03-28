$(document).ready(function(){$(".admin_nav li.admin_drop").hover(function(){$(this).find("ul").show();},function(){$(this).find("ul").hide();});if($.cookie('open')){$(".open").css('display',$.cookie('open'));}if($.cookie('close')){$(".close").css('display',$.cookie('close'));}if($.cookie('admin_panel')){$(".admin_panel").css('position',$.cookie('admin_panel'));}else{$(".close").css('display','none');}$(".open").click(function(){$(this).css('display','none');$(".close").css('display','block');$(".admin_panel").css('position','fixed');var open=$(".open").css('display');var close=$(".close").css('display');var admin_panel=$(".admin_panel").css('position');$.cookie('open',open,{expires:666,path:'/',domain:'rdn-team.com'});$.cookie('close',close,{expires:666,path:'/',domain:'rdn-team.com'});$.cookie('admin_panel',admin_panel,{expires:666,path:'/',domain:'rdn-team.com'});});$(".close").click(function(){$(this).css('display','none');$(".open").css('display','block');$(".admin_panel").css('position','absolute');$.cookie('open',null);$.cookie('close',null);$.cookie('admin_panel',null);});});;function show_full(id){$('#show_full').remove();ShowLoading('');$(function(){$.ajax({type:"POST",url:dle_root+"engine/ajax/fastview.php",data:"id="+id,dataType:"xml",success:function(xml){jQuery(xml).find('site').each(function(){title=jQuery(this).find('title').text(),desc=jQuery(this).find('des').text();rate=jQuery(this).find('rate').text();fulllink=jQuery(this).find('fulllink').text();$('body').prepend('<div id="show_full" style="overflow: auto; width:100%; resize:vertical;"></div>');$('#show_full').html(desc);$('#show_full').dialog({zIndex:900,autoOpen:true,show:'fade',hide:'fade',width:$(window).width()*0.9,height:$(window).height()*0.8,buttons:[{text:"Закрыть",click:function(){$(this).dialog("close");}}],close:function(event,ui){$('#show_full').remove();},title:title});$('.ui-dialog-buttonset').html('<p align="right"><input class="bbcodes" style="width:150px;" onclick="close_previw(); return false;"" type="button" value="Закрыть..." /> <input class="bbcodes" style="width:150px;" onclick="location.href=\''+fulllink+'\'" type="button" value="Подробнее..." /> <input class="bbcodes" style="width:150px;" onclick="window.open(\''+fulllink+'\')" type="button" value="В новом окне..." /> '+rate+'</p>');});}});HideLoading('');});return false;}
function close_previw(){$('#show_full').remove();}
function fastRate(rate,id){$.get(dle_root+"engine/ajax/rating.php",{go_rate:rate,news_id:id,mode:"short",skin:dle_skin},function(data){$("#fratig-layer-"+id).html(data);});};function OnlineUsers(){$(function(){$('#OnlineUsers').dialog({zIndex:900,autoOpen:true,show:'fade',hide:'fade',width:$(window).width()*0.4,height:$(window).height()*0.3,});});}
function Showpopup(id){$(function(){$('#popup-'+id).dialog({zIndex:900,autoOpen:true,show:'fade',hide:'fade',width:'auto',});});};
/*!
 reflection.js for jQuery v1.1
 (c) 2006-2011 Christophe Beyls <http://www.digitalia.be>
 MIT-style license.
*/
(function(a){a.fn.extend({reflect:function(b){b=a.extend({height:1/3,opacity:0.5},b);return this.unreflect().each(function(){var c=this;if(/^img$/i.test(c.tagName)){function d(){var g=c.width,f=c.height,l,i,m,h,k;i=Math.floor((b.height>1)?Math.min(f,b.height):f*b.height);l=a("<canvas />")[0];if(l.getContext){h=l.getContext("2d");try{a(l).attr({width:g,height:i});h.save();h.translate(0,f-1);h.scale(1,-1);h.drawImage(c,0,0,g,f);h.restore();h.globalCompositeOperation="destination-out";k=h.createLinearGradient(0,0,0,i);k.addColorStop(0,"rgba(255, 255, 255, "+(1-b.opacity)+")");k.addColorStop(1,"rgba(255, 255, 255, 1.0)");h.fillStyle=k;h.rect(0,0,g,i);h.fill()}catch(j){return}}else{if(!a.browser.msie){return}l=a("<img />").attr("src",c.src).css({width:g,height:f,marginBottom:i-f,filter:"FlipV progid:DXImageTransform.Microsoft.Alpha(Opacity="+(b.opacity*100)+", FinishOpacity=0, Style=1, StartX=0, StartY=0, FinishX=0, FinishY="+(i/f*100)+")"})[0]}a(l).css({border:0});m=a(/^a$/i.test(c.parentNode.tagName)?"<span />":"<div />").insertAfter(c).append([c,l])[0];m.className=c.className;a.data(c,"reflected",m.style.cssText=c.style.cssText);a(m).css({width:g,height:f+i,overflow:"hidden",display:"inline-block"});c.style.cssText="border: 0px";c.className="reflected"}if(c.complete){d()}else{a(c).load(d)}}})},unreflect:function(){return this.unbind("load").each(function(){var c=this,b=a.data(this,"reflected"),d;if(b!==undefined){d=c.parentNode;c.className=d.className;c.style.cssText=b;a.removeData(c,"reflected");d.parentNode.replaceChild(c,d)}})}})})(jQuery);jQuery(function($){$(".reflect img").reflect({});});;var isIE=(navigator.appVersion.indexOf("MSIE")!=-1)?true:false;var isWin=(navigator.appVersion.toLowerCase().indexOf("win")!=-1)?true:false;var isOpera=(navigator.userAgent.indexOf("Opera")!=-1)?true:false;function ControlVersion()
{var version;var axo;var e;try{axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");version=axo.GetVariable("$version");}catch(e){}
if(!version)
{try{axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");version="WIN 6,0,21,0";axo.AllowScriptAccess="always";version=axo.GetVariable("$version");}catch(e){}}
if(!version)
{try{axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");version=axo.GetVariable("$version");}catch(e){}}
if(!version)
{try{axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");version="WIN 3,0,18,0";}catch(e){}}
if(!version)
{try{axo=new ActiveXObject("ShockwaveFlash.ShockwaveFlash");version="WIN 2,0,0,11";}catch(e){version=-1;}}
return version;}
function GetSwfVer(){var flashVer=-1;if(navigator.plugins!=null&&navigator.plugins.length>0){if(navigator.plugins["Shockwave Flash 2.0"]||navigator.plugins["Shockwave Flash"]){var swVer2=navigator.plugins["Shockwave Flash 2.0"]?" 2.0":"";var flashDescription=navigator.plugins["Shockwave Flash"+swVer2].description;var descArray=flashDescription.split(" ");var tempArrayMajor=descArray[2].split(".");var versionMajor=tempArrayMajor[0];var versionMinor=tempArrayMajor[1];var versionRevision=descArray[3];if(versionRevision==""){versionRevision=descArray[4];}
if(versionRevision[0]=="d"){versionRevision=versionRevision.substring(1);}else if(versionRevision[0]=="r"){versionRevision=versionRevision.substring(1);if(versionRevision.indexOf("d")>0){versionRevision=versionRevision.substring(0,versionRevision.indexOf("d"));}}
var flashVer=versionMajor+"."+versionMinor+"."+versionRevision;}}
else if(navigator.userAgent.toLowerCase().indexOf("webtv/2.6")!=-1)flashVer=4;else if(navigator.userAgent.toLowerCase().indexOf("webtv/2.5")!=-1)flashVer=3;else if(navigator.userAgent.toLowerCase().indexOf("webtv")!=-1)flashVer=2;else if(isIE&&isWin&&!isOpera){flashVer=ControlVersion();}
return flashVer;}
function DetectFlashVer(reqMajorVer,reqMinorVer,reqRevision)
{versionStr=GetSwfVer();if(versionStr==-1){return false;}else if(versionStr!=0){if(isIE&&isWin&&!isOpera){tempArray=versionStr.split(" ");tempString=tempArray[1];versionArray=tempString.split(",");}else{versionArray=versionStr.split(".");}
var versionMajor=versionArray[0];var versionMinor=versionArray[1];var versionRevision=versionArray[2];if(versionMajor>parseFloat(reqMajorVer)){return true;}else if(versionMajor==parseFloat(reqMajorVer)){if(versionMinor>parseFloat(reqMinorVer))
return true;else if(versionMinor==parseFloat(reqMinorVer)){if(versionRevision>=parseFloat(reqRevision))
return true;}}
return false;}}
function AC_AddExtension(src,ext)
{if(src.indexOf('?')!=-1)
return src.replace(/\?/,ext+'?');else
return src+ext;}
function AC_Generateobj(objAttrs,params,embedAttrs)
{var str='';if(isIE&&isWin&&!isOpera)
{str+='<object ';for(var i in objAttrs)
{str+=i+'="'+objAttrs[i]+'" ';}
str+='>';for(var i in params)
{str+='<param name="'+i+'" value="'+params[i]+'" /> ';}
str+='</object>';}
else
{str+='<embed ';for(var i in embedAttrs)
{str+=i+'="'+embedAttrs[i]+'" ';}
str+='> </embed>';}
document.write(str);}
function AC_FL_RunContent(){var ret=AC_GetArgs
(arguments,".swf","movie","clsid:d27cdb6e-ae6d-11cf-96b8-444553540000","application/x-shockwave-flash");AC_Generateobj(ret.objAttrs,ret.params,ret.embedAttrs);}
function AC_SW_RunContent(){var ret=AC_GetArgs
(arguments,".dcr","src","clsid:166B1BCA-3F9C-11CF-8075-444553540000",null);AC_Generateobj(ret.objAttrs,ret.params,ret.embedAttrs);}
function AC_GetArgs(args,ext,srcParamName,classid,mimeType){var ret=new Object();ret.embedAttrs=new Object();ret.params=new Object();ret.objAttrs=new Object();for(var i=0;i<args.length;i=i+2){var currArg=args[i].toLowerCase();switch(currArg){case"classid":break;case"pluginspage":ret.embedAttrs[args[i]]=args[i+1];break;case"src":case"movie":args[i+1]=AC_AddExtension(args[i+1],ext);ret.embedAttrs["src"]=args[i+1];ret.params[srcParamName]=args[i+1];break;case"onafterupdate":case"onbeforeupdate":case"onblur":case"oncellchange":case"onclick":case"ondblclick":case"ondrag":case"ondragend":case"ondragenter":case"ondragleave":case"ondragover":case"ondrop":case"onfinish":case"onfocus":case"onhelp":case"onmousedown":case"onmouseup":case"onmouseover":case"onmousemove":case"onmouseout":case"onkeypress":case"onkeydown":case"onkeyup":case"onload":case"onlosecapture":case"onpropertychange":case"onreadystatechange":case"onrowsdelete":case"onrowenter":case"onrowexit":case"onrowsinserted":case"onstart":case"onscroll":case"onbeforeeditfocus":case"onactivate":case"onbeforedeactivate":case"ondeactivate":case"type":case"codebase":case"id":ret.objAttrs[args[i]]=args[i+1];break;case"width":case"height":case"align":case"vspace":case"hspace":case"class":case"title":case"accesskey":case"name":case"tabindex":ret.embedAttrs[args[i]]=ret.objAttrs[args[i]]=args[i+1];break;default:ret.embedAttrs[args[i]]=ret.params[args[i]]=args[i+1];}}
ret.objAttrs["classid"]=classid;if(mimeType)ret.embedAttrs["type"]=mimeType;return ret;}