var PUI={zindex:1000,scrollInView:function(b,e){var h=parseFloat(b.css("borderTopWidth"))||0,d=parseFloat(b.css("paddingTop"))||0,f=e.offset().top-b.offset().top-h-d,a=b.scrollTop(),c=b.height(),g=e.outerHeight(true);
if(f<0){b.scrollTop(a+f)
}else{if((f+g)>c){b.scrollTop(a+f-c+g)
}}},isIE:function(a){return(this.browser.msie&&parseInt(this.browser.version,10)===a)
},escapeRegExp:function(a){return a.replace(/([.?*+^$[\]\\(){}|-])/g,"\\$1")
},escapeHTML:function(a){return a.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")
},clearSelection:function(){if(window.getSelection){if(window.getSelection().empty){window.getSelection().empty()
}else{if(window.getSelection().removeAllRanges){window.getSelection().removeAllRanges()
}}}else{if(document.selection&&document.selection.empty){document.selection.empty()
}}},inArray:function(a,c){for(var b=0;
b<a.length;
b++){if(a[b]===c){return true
}}return false
},calculateScrollbarWidth:function(){if(!this.scrollbarWidth){if(this.browser.msie){var c=$('<textarea cols="10" rows="2"></textarea>').css({position:"absolute",top:-1000,left:-1000}).appendTo("body"),b=$('<textarea cols="10" rows="2" style="overflow: hidden;"></textarea>').css({position:"absolute",top:-1000,left:-1000}).appendTo("body");
this.scrollbarWidth=c.width()-b.width();
c.add(b).remove()
}else{var a=$("<div />").css({width:100,height:100,overflow:"auto",position:"absolute",top:-1000,left:-1000}).prependTo("body").append("<div />").find("div").css({width:"100%",height:200});
this.scrollbarWidth=100-a.width();
a.parent().remove()
}}return this.scrollbarWidth
},resolveUserAgent:function(c){var d=c.toLowerCase(),b=/(chrome)[ \/]([\w.]+)/.exec(d)||/(webkit)[ \/]([\w.]+)/.exec(d)||/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(d)||/(msie) ([\w.]+)/.exec(d)||c.indexOf("compatible")<0&&/(mozilla)(?:.*? rv:([\w.]+)|)/.exec(d)||[],e={browser:b[1]||"",version:b[2]||"0"},a={};
if(e.browser){a[e.browser]=true;
a.version=e.version
}if(a.chrome){a.webkit=true
}else{if(a.webkit){a.safari=true
}}this.browser=a
}};
PUI.resolveUserAgent(navigator.userAgent);