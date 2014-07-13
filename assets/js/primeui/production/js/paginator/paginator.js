$(function(){var a={"{FirstPageLink}":{markup:'<span class="pui-paginator-first pui-paginator-element ui-state-default ui-corner-all"><span class="ui-icon ui-icon-seek-first">p</span></span>',create:function(c){var b=$(this.markup);
if(c.options.page===0){b.addClass("ui-state-disabled")
}b.on("click.puipaginator",function(){if(!$(this).hasClass("ui-state-disabled")){c.option("page",0)
}});
return b
},update:function(b,c){if(c.page===0){b.addClass("ui-state-disabled").removeClass("ui-state-hover ui-state-active")
}else{b.removeClass("ui-state-disabled")
}}},"{PreviousPageLink}":{markup:'<span class="pui-paginator-prev pui-paginator-element ui-state-default ui-corner-all"><span class="ui-icon ui-icon-seek-prev">p</span></span>',create:function(c){var b=$(this.markup);
if(c.options.page===0){b.addClass("ui-state-disabled")
}b.on("click.puipaginator",function(){if(!$(this).hasClass("ui-state-disabled")){c.option("page",c.options.page-1)
}});
return b
},update:function(b,c){if(c.page===0){b.addClass("ui-state-disabled").removeClass("ui-state-hover ui-state-active")
}else{b.removeClass("ui-state-disabled")
}}},"{NextPageLink}":{markup:'<span class="pui-paginator-next pui-paginator-element ui-state-default ui-corner-all"><span class="ui-icon ui-icon-seek-next">p</span></span>',create:function(c){var b=$(this.markup);
if(c.options.page===(c.getPageCount()-1)){b.addClass("ui-state-disabled").removeClass("ui-state-hover ui-state-active")
}b.on("click.puipaginator",function(){if(!$(this).hasClass("ui-state-disabled")){c.option("page",c.options.page+1)
}});
return b
},update:function(b,c){if(c.page===(c.pageCount-1)){b.addClass("ui-state-disabled").removeClass("ui-state-hover ui-state-active")
}else{b.removeClass("ui-state-disabled")
}}},"{LastPageLink}":{markup:'<span class="pui-paginator-last pui-paginator-element ui-state-default ui-corner-all"><span class="ui-icon ui-icon-seek-end">p</span></span>',create:function(c){var b=$(this.markup);
if(c.options.page===(c.getPageCount()-1)){b.addClass("ui-state-disabled").removeClass("ui-state-hover ui-state-active")
}b.on("click.puipaginator",function(){if(!$(this).hasClass("ui-state-disabled")){c.option("page",c.getPageCount()-1)
}});
return b
},update:function(b,c){if(c.page===(c.pageCount-1)){b.addClass("ui-state-disabled").removeClass("ui-state-hover ui-state-active")
}else{b.removeClass("ui-state-disabled")
}}},"{PageLinks}":{markup:'<span class="pui-paginator-pages"></span>',create:function(j){var f=$(this.markup),c=this.calculateBoundaries({page:j.options.page,pageLinks:j.options.pageLinks,pageCount:j.getPageCount()}),h=c[0],b=c[1];
for(var e=h;
e<=b;
e++){var g=(e+1),d=$('<span class="pui-paginator-page pui-paginator-element ui-state-default ui-corner-all">'+g+"</span>");
if(e===j.options.page){d.addClass("ui-state-active")
}d.on("click.puipaginator",function(k){var i=$(this);
if(!i.hasClass("ui-state-disabled")&&!i.hasClass("ui-state-active")){j.option("page",parseInt(i.text(),10)-1)
}});
f.append(d)
}return f
},update:function(h,b){var k=h.children(),e=this.calculateBoundaries({page:b.page,pageLinks:b.pageLinks,pageCount:b.pageCount}),c=e[0],f=e[1],d=0;
k.filter(".ui-state-active").removeClass("ui-state-active");
for(var j=c;
j<=f;
j++){var l=(j+1),g=k.eq(d);
if(j===b.page){g.addClass("ui-state-active")
}g.text(l);
d++
}},calculateBoundaries:function(d){var e=d.page,i=d.pageLinks,c=d.pageCount,f=Math.min(i,c);
var h=Math.max(0,parseInt(Math.ceil(e-((f)/2)),10)),b=Math.min(c-1,h+f-1);
var g=i-(b-h+1);
h=Math.max(0,h-g);
return[h,b]
}}};
$.widget("primeui.puipaginator",{options:{pageLinks:5,totalRecords:0,page:0,rows:0,template:"{FirstPageLink} {PreviousPageLink} {PageLinks} {NextPageLink} {LastPageLink}"},_create:function(){this.element.addClass("pui-paginator ui-widget-header");
this.paginatorElements=[];
var b=this.options.template.split(/[ ]+/);
for(var c=0;
c<b.length;
c++){var f=b[c],e=a[f];
if(e){var d=e.create(this);
this.paginatorElements[f]=d;
this.element.append(d)
}}this._bindEvents()
},_bindEvents:function(){this.element.find("span.pui-paginator-element").on("mouseover.puipaginator",function(){var b=$(this);
if(!b.hasClass("ui-state-active")&&!b.hasClass("ui-state-disabled")){b.addClass("ui-state-hover")
}}).on("mouseout.puipaginator",function(){var b=$(this);
if(b.hasClass("ui-state-hover")){b.removeClass("ui-state-hover")
}})
},_setOption:function(b,c){if(b==="page"){this.setPage(c)
}else{$.Widget.prototype._setOption.apply(this,arguments)
}},setPage:function(e,b){var c=this.getPageCount();
if(e>=0&&e<c&&this.options.page!==e){var d={first:this.options.rows*e,rows:this.options.rows,page:e,pageCount:c,pageLinks:this.options.pageLinks};
this.options.page=e;
if(!b){this._trigger("paginate",null,d)
}this.updateUI(d)
}},updateUI:function(c){for(var b in this.paginatorElements){a[b].update(this.paginatorElements[b],c)
}},getPageCount:function(){return Math.ceil(this.options.totalRecords/this.options.rows)||1
}})
});