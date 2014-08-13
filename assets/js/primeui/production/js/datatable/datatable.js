$(function(){$.widget("primeui.puidatatable",{options:{columns:null,datasource:null,paginator:null,selectionMode:null,rowSelect:null,rowUnselect:null,caption:null,sortField:null,sortOrder:null,keepSelectionInLazyMode:false,scrollable:false},_create:function(){this.id=this.element.attr("id");
if(!this.id){this.id=this.element.uniqueId().attr("id")
}this.element.addClass("pui-datatable ui-widget");
if(this.options.scrollable){this._createScrollableDatatable()
}else{this._createRegularDatatable()
}if(this.options.datasource){if($.isArray(this.options.datasource)){this.data=this.options.datasource;
this._initialize()
}else{if($.type(this.options.datasource)==="function"){if(this.options.lazy){this.options.datasource.call(this,this._onDataInit,{first:0,sortField:this.options.sortField,sortOrder:this.options.sortOrder})
}else{this.options.datasource.call(this,this._onDataInit)
}}}}},_createRegularDatatable:function(){this.tableWrapper=$('<div class="pui-datatable-tablewrapper" />').appendTo(this.element);
this.table=$("<table><thead></thead><tbody></tbody></table>").appendTo(this.tableWrapper);
this.thead=this.table.children("thead");
this.tbody=this.table.children("tbody").addClass("pui-datatable-data")
},_createScrollableDatatable:function(){var a=this;
this.element.append('<div class="ui-widget-header pui-datatable-scrollable-header"><div class="pui-datatable-scrollable-header-box"><table><thead></thead></table></div></div>').append('<div class="pui-datatable-scrollable-body"><table><colgroup></colgroup><tbody></tbody></table></div></div>');
this.thead=this.element.find("> .pui-datatable-scrollable-header thead");
this.tbody=this.element.find("> .pui-datatable-scrollable-body tbody");
this.colgroup=this.tbody.prev();
if(this.options.columns){$.each(this.options.columns,function(c,b){$("<col></col>").appendTo(a.colgroup)
})
}},_initialize:function(){var a=this;
if(this.options.columns){$.each(this.options.columns,function(c,b){var d=$('<th class="ui-state-default"></th>').data("field",b.field).appendTo(a.thead);
if(b.headerClass){d.addClass(b.headerClass)
}if(b.headerStyle){d.attr("style",b.headerStyle)
}if(b.headerText){d.text(b.headerText)
}if(b.sortable){d.addClass("pui-sortable-column").data("order",0).append('<span class="pui-sortable-column-icon ui-icon ui-icon-carat-2-n-s"></span>')
}})
}if(this.options.caption){this.element.prepend('<div class="pui-datatable-caption ui-widget-header">'+this.options.caption+"</div>")
}if(this.options.paginator){this.options.paginator.paginate=function(b,c){a.paginate()
};
this.options.paginator.totalRecords=this.options.paginator.totalRecords||this.data.length;
this.paginator=$("<div></div>").insertAfter(this.tableWrapper).puipaginator(this.options.paginator)
}if(this._isSortingEnabled()){this._initSorting()
}if(this.options.selectionMode){this._initSelection()
}if(this.options.sortField&&this.options.sortOrder){this._indicateInitialSortColumn();
this.sort(this.options.sortField,this.options.sortOrder)
}else{this._renderData()
}if(this.options.scrollable){this._initScrolling()
}},_indicateInitialSortColumn:function(){var a=this.thead.children("th.pui-sortable-column"),b=this;
$.each(a,function(c,d){var f=$(d),e=f.data();
if(b.options.sortField===e.field){var g=f.children(".pui-sortable-column-icon");
f.data("order",b.options.sortOrder).removeClass("ui-state-hover").addClass("ui-state-active");
if(b.options.sortOrder===-1){g.removeClass("ui-icon-triangle-1-n").addClass("ui-icon-triangle-1-s")
}else{if(b.options.sortOrder===1){g.removeClass("ui-icon-triangle-1-s").addClass("ui-icon-triangle-1-n")
}}}})
},_onDataInit:function(a){this.data=a;
if(!this.data){this.data=[]
}this._initialize()
},_onDataUpdate:function(a){this.data=a;
if(!this.data){this.data=[]
}this._renderData()
},_onLazyLoad:function(a){this.data=a;
if(!this.data){this.data=[]
}this._renderData()
},_initSorting:function(){var b=this,a=this.thead.children("th.pui-sortable-column");
a.on("mouseover.puidatatable",function(){var c=$(this);
if(!c.hasClass("ui-state-active")){c.addClass("ui-state-hover")
}}).on("mouseout.puidatatable",function(){var c=$(this);
if(!c.hasClass("ui-state-active")){c.removeClass("ui-state-hover")
}}).on("click.puidatatable",function(){var f=$(this),d=f.data("field"),c=f.data("order"),e=(c===0)?1:(c*-1),g=f.children(".pui-sortable-column-icon");
f.siblings().filter(".ui-state-active").data("order",0).removeClass("ui-state-active").children("span.pui-sortable-column-icon").removeClass("ui-icon-triangle-1-n ui-icon-triangle-1-s");
b.options.sortField=d;
b.options.sortOrder=e;
b.sort(d,e);
f.data("order",e).removeClass("ui-state-hover").addClass("ui-state-active");
if(e===-1){g.removeClass("ui-icon-triangle-1-n").addClass("ui-icon-triangle-1-s")
}else{if(e===1){g.removeClass("ui-icon-triangle-1-s").addClass("ui-icon-triangle-1-n")
}}})
},paginate:function(){if(this.options.lazy){if(this.options.selectionMode&&!this.options.keepSelectionInLazyMode){this.selection=[]
}this.options.datasource.call(this,this._onLazyLoad,this._createStateMeta())
}else{this._renderData()
}},sort:function(b,a){if(this.options.selectionMode){this.selection=[]
}if(this.options.lazy){this.options.datasource.call(this,this._onLazyLoad,this._createStateMeta())
}else{this.data.sort(function(d,g){var f=d[b],e=g[b],c=(f<e)?-1:(f>e)?1:0;
return(a*c)
});
if(this.options.selectionMode){this.selection=[]
}if(this.paginator){this.paginator.puipaginator("option","page",0)
}this._renderData()
}},sortByField:function(d,c){var f=d.name.toLowerCase();
var e=c.name.toLowerCase();
return((f<e)?-1:((f>e)?1:0))
},_renderData:function(){if(this.data){this.tbody.html("");
var l=this._getFirst(),e=this.options.lazy?0:l,n=this._getRows();
for(var d=e;
d<(e+n);
d++){var b=this.data[d];
if(b){var m=$('<tr class="ui-widget-content" />').appendTo(this.tbody),g=(d%2===0)?"pui-datatable-even":"pui-datatable-odd",h=d;
m.addClass(g);
if(this.options.lazy){h+=l
}if(this.options.selectionMode&&PUI.inArray(this.selection,h)){m.addClass("ui-state-highlight")
}for(var c=0;
c<this.options.columns.length;
c++){var a=$("<td />").appendTo(m),k=this.options.columns[c];
if(k.bodyClass){a.addClass(k.bodyClass)
}if(k.bodyStyle){a.attr("style",k.bodyStyle)
}if(k.content){var f=k.content.call(this,b);
if($.type(f)==="string"){a.html(f)
}else{a.append(f)
}}else{a.text(b[k.field])
}}}}}},_getFirst:function(){if(this.paginator){var b=this.paginator.puipaginator("option","page"),a=this.paginator.puipaginator("option","rows");
return(b*a)
}else{return 0
}},_getRows:function(){return this.paginator?this.paginator.puipaginator("option","rows"):this.data.length
},_isSortingEnabled:function(){var b=this.options.columns;
if(b){for(var a=0;
a<b.length;
a++){if(b[a].sortable){return true
}}}return false
},_initSelection:function(){var a=this;
this.selection=[];
this.rowSelector="#"+this.id+" tbody.pui-datatable-data > tr.ui-widget-content:not(.ui-datatable-empty-message)";
if(this._isMultipleSelection()){this.originRowIndex=0;
this.cursorIndex=null
}$(document).off("mouseover.puidatatable mouseout.puidatatable click.puidatatable",this.rowSelector).on("mouseover.datatable",this.rowSelector,null,function(){var b=$(this);
if(!b.hasClass("ui-state-highlight")){b.addClass("ui-state-hover")
}}).on("mouseout.datatable",this.rowSelector,null,function(){var b=$(this);
if(!b.hasClass("ui-state-highlight")){b.removeClass("ui-state-hover")
}}).on("click.datatable",this.rowSelector,null,function(b){a._onRowClick(b,this)
})
},_onRowClick:function(f,e){if(!$(f.target).is(":input,:button,a")){var h=$(e),d=h.hasClass("ui-state-highlight"),g=f.metaKey||f.ctrlKey,b=f.shiftKey;
if(d&&g){this.unselectRow(h)
}else{if(this._isSingleSelection()||(this._isMultipleSelection()&&!g&&!b)){if(this._isMultipleSelection()){var c=this.getSelection();
for(var a=0;
a<c.length;
a++){this._trigger("rowUnselect",null,c[a])
}}this.unselectAllRows()
}this.selectRow(h,false,f)
}PUI.clearSelection()
}},_isSingleSelection:function(){return this.options.selectionMode==="single"
},_isMultipleSelection:function(){return this.options.selectionMode==="multiple"
},unselectAllRows:function(){this.tbody.children("tr.ui-state-highlight").removeClass("ui-state-highlight").attr("aria-selected",false);
this.selection=[]
},unselectRow:function(b,a){var c=this._getRowIndex(b);
b.removeClass("ui-state-highlight").attr("aria-selected",false);
this._removeSelection(c);
if(!a){this._trigger("rowUnselect",null,this.data[c])
}},selectRow:function(d,a,b){var e=this._getRowIndex(d),c=this.data[e];
d.removeClass("ui-state-hover").addClass("ui-state-highlight").attr("aria-selected",true);
this._addSelection(e);
if(!a){if(this.options.lazy){c=this.data[e-this._getFirst()]
}this._trigger("rowSelect",b,c)
}},getSelection:function(){var c=this.options.lazy?this._getFirst():0,b=[];
for(var a=0;
a<this.selection.length;
a++){if(this.data.length>this.selection[a]-c&&this.selection[a]-c>0){b.push(this.data[this.selection[a]-c])
}}return b
},_removeSelection:function(a){this.selection=$.grep(this.selection,function(b){return b!==a
})
},_addSelection:function(a){if(!this._isSelected(a)){this.selection.push(a)
}},_isSelected:function(a){return PUI.inArray(this.selection,a)
},_getRowIndex:function(b){var a=b.index();
return this.options.paginator?this._getFirst()+a:a
},_createStateMeta:function(){var a={first:this._getFirst(),rows:this._getRows(),sortField:this.options.sortField,sortOrder:this.options.sortOrder};
return a
},_updateDatasource:function(a){this.options.datasource=a;
this.reset();
if($.isArray(this.options.datasource)){this.data=this.options.datasource;
this._renderData()
}else{if($.type(this.options.datasource)==="function"){if(this.options.lazy){this.options.datasource.call(this,this._onDataUpdate,{first:0,sortField:this.options.sortField,sortorder:this.options.sortOrder})
}else{this.options.datasource.call(this,this._onDataUpdate)
}}}},_setOption:function(a,b){if(a==="datasource"){this._updateDatasource(b)
}else{$.Widget.prototype._setOption.apply(this,arguments)
}},reset:function(){if(this.options.selectionMode){this.selection=[]
}if(this.paginator){this.paginator.puipaginator("setPage",0,true)
}this.thead.children("th.pui-sortable-column").data("order",0).filter(".ui-state-active").removeClass("ui-state-active").children("span.pui-sortable-column-icon").removeClass("ui-icon-triangle-1-n ui-icon-triangle-1-s")
},_initScrolling:function(){this.scrollHeader=this.element.children(".pui-datatable-scrollable-header");
this.scrollBody=this.element.children(".pui-datatable-scrollable-body");
this.scrollHeaderBox=this.scrollHeader.children("div.pui-datatable-scrollable-header-box");
this.headerTable=this.scrollHeaderBox.children("table");
this.bodyTable=this.scrollBody.children("table");
this.percentageScrollHeight=this.options.scrollHeight&&(this.options.scrollHeight.indexOf("%")!==-1);
this.percentageScrollWidth=this.options.scrollWidth&&(this.options.scrollWidth.indexOf("%")!==-1);
var c=this,b=this.getScrollbarWidth()+"px";
if(this.options.scrollHeight){this.scrollBody.height(this.options.scrollHeight);
this.scrollHeaderBox.css("margin-right",b);
if(this.percentageScrollHeight){this.adjustScrollHeight()
}}this.fixColumnWidths();
if(this.options.scrollWidth){if(this.percentageScrollWidth){this.adjustScrollWidth()
}else{this.setScrollWidth(this.options.scrollWidth)
}}this.scrollBody.on("scroll.dataTable",function(){var d=c.scrollBody.scrollLeft();
c.scrollHeaderBox.css("margin-left",-d)
});
this.scrollHeader.on("scroll.dataTable",function(){c.scrollHeader.scrollLeft(0)
});
var a="resize."+this.id;
$(window).unbind(a).bind(a,function(){if(c.element.is(":visible")){if(c.percentageScrollHeight){c.adjustScrollHeight()
}if(c.percentageScrollWidth){c.adjustScrollWidth()
}}})
},adjustScrollHeight:function(){var c=this.element.parent().innerHeight()*(parseInt(this.options.scrollHeight)/100),e=this.element.children(".pui-datatable-caption").outerHeight(true),b=this.scrollHeader.outerHeight(true),d=this.paginator?this.paginator.getContainerHeight(true):0,a=(c-(b+d+e));
this.scrollBody.height(a)
},adjustScrollWidth:function(){var a=parseInt((this.element.parent().innerWidth()*(parseInt(this.options.scrollWidth)/100)));
this.setScrollWidth(a)
},setScrollWidth:function(a){this.scrollHeader.width(a);
this.scrollBody.css("margin-right",0).width(a);
this.element.width(a)
},getScrollbarWidth:function(){if(!this.scrollbarWidth){this.scrollbarWidth=PUI.browser.webkit?"15":PUI.calculateScrollbarWidth()
}return this.scrollbarWidth
},fixColumnWidths:function(){var a=this;
if(!this.columnWidthsFixed){if(this.options.scrollable){this.thead.children("th").each(function(){var f=$(this),c=f.index(),e=f.width(),b=f.innerWidth(),d=b+1;
f.width(e);
a.colgroup.children().eq(c).width(d)
})
}else{this.element.find("> .pui-datatable-tablewrapper > table > thead > tr > th").each(function(){var b=$(this);
b.width(b.width())
})
}this.columnWidthsFixed=true
}}})
});