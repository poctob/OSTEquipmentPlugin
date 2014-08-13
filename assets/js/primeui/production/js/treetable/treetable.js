$(function(){$.widget("primeui.puitreetable",{options:{nodes:null,lazy:false,selectionMode:null},_create:function(){this.id=this.element.attr("id");
if(!this.id){this.id=this.element.uniqueId().attr("id")
}this.element.addClass("pui-treetable ui-widget");
this.tableWrapper=$('<div class="pui-treetable-tablewrapper" />').appendTo(this.element);
this.table=$("<table><thead></thead><tbody></tbody></table>").appendTo(this.tableWrapper);
this.thead=this.table.children("thead");
this.tbody=this.table.children("tbody").addClass("pui-treetable-data");
var a=this;
if(this.options.columns){$.each(this.options.columns,function(c,b){var d=$('<th class="ui-state-default"></th>').data("field",b.field).appendTo(a.thead);
if(b.headerClass){d.addClass(b.headerClass)
}if(b.headerStyle){d.attr("style",b.headerStyle)
}if(b.headerText){d.text(b.headerText)
}})
}if(this.options.header){this.element.prepend('<div class="pui-treetable-header ui-widget-header ui-corner-top">'+this.options.header+"</div>")
}if(this.options.footer){this.element.append('<div class="pui-treetable-footer ui-widget-header ui-corner-bottom">'+this.options.footer+"</div>")
}if($.isArray(this.options.nodes)){this._renderNodes(this.options.nodes,null,true)
}else{if($.type(this.options.nodes)==="function"){this.options.nodes.call(this,{},this._initData)
}else{throw"Unsupported type. nodes option can be either an array or a function"
}}this._bindEvents()
},_initData:function(a){this._renderNodes(a,null,true)
},_renderNodes:function(a,r,l){for(var h=0;
h<a.length;
h++){var d=a[h],c=d.data,n=this.options.lazy?d.leaf:!(d.children&&d.children.length),q=$('<tr class="ui-widget-content"></tr>'),g=r?r.data("depth")+1:0,o=r?r.data("rowkey"):null,b=o?o+"_"+h:h.toString();
q.data({depth:g,rowkey:b,parentrowkey:o,puidata:c,});
if(!l){q.addClass("ui-helper-hidden")
}for(var f=0;
f<this.options.columns.length;
f++){var e=$("<td />").appendTo(q),p=this.options.columns[f];
if(p.bodyClass){e.addClass(p.bodyClass)
}if(p.bodyStyle){e.attr("style",p.bodyStyle)
}if(f===0){var k=$('<span class="pui-treetable-toggler ui-icon ui-icon-triangle-1-e ui-c"></span>');
k.css("margin-left",g*16+"px");
if(n){k.css("visibility","hidden")
}k.appendTo(e)
}if(p.content){var m=p.content.call(this,c);
if($.type(m)==="string"){e.text(m)
}else{e.append(m)
}}else{e.append(c[p.field])
}}if(r){q.insertAfter(r)
}else{q.appendTo(this.tbody)
}if(!n){this._renderNodes(d.children,q,d.expanded)
}}},_bindEvents:function(){var c=this,a="> tr > td:first-child > .pui-treetable-toggler";
this.tbody.off("click.puitreetable",a).on("click.puitreetable",a,null,function(f){var d=$(this),g=d.closest("tr");
if(!g.data("processing")){g.data("processing",true);
if(d.hasClass("ui-icon-triangle-1-e")){c.expandNode(g)
}else{c.collapseNode(g)
}}});
if(this.options.selectionMode){this.selection=[];
var b="> tr";
this.tbody.off("mouseover.puitreetable mouseout.puitreetable click.puitreetable",b).on("mouseover.puitreetable",b,null,function(f){var d=$(this);
if(!d.hasClass("ui-state-highlight")){d.addClass("ui-state-hover")
}}).on("mouseout.puitreetable",b,null,function(f){var d=$(this);
if(!d.hasClass("ui-state-highlight")){d.removeClass("ui-state-hover")
}}).on("click.puitreetable",b,null,function(d){c.onRowClick(d,$(this))
})
}},expandNode:function(a){this._trigger("beforeExpand",null,{node:a,data:a.data("puidata")});
if(this.options.lazy&&!a.data("puiloaded")){this.options.nodes.call(this,{node:a,data:a.data("puidata")},this._handleNodeData)
}else{this._showNodeChildren(a,false);
this._trigger("afterExpand",null,{node:a,data:a.data("puidata")})
}},_handleNodeData:function(b,a){this._renderNodes(b,a,true);
this._showNodeChildren(a,false);
a.data("puiloaded",true);
this._trigger("afterExpand",null,{node:a,data:a.data("puidata")})
},_showNodeChildren:function(d,c){if(!c){d.data("expanded",true).attr("aria-expanded",true).find(".pui-treetable-toggler:first").addClass("ui-icon-triangle-1-s").removeClass("ui-icon-triangle-1-e")
}var b=this._getChildren(d);
for(var a=0;
a<b.length;
a++){var e=b[a];
e.removeClass("ui-helper-hidden");
if(e.data("expanded")){this._showNodeChildren(e,true)
}}d.data("processing",false)
},collapseNode:function(a){this._trigger("beforeCollapse",null,{node:a,data:a.data("puidata")});
this._hideNodeChildren(a,false);
a.data("processing",false);
this._trigger("afterCollapse",null,{node:a,data:a.data("puidata")})
},_hideNodeChildren:function(d,c){if(!c){d.data("expanded",false).attr("aria-expanded",false).find(".pui-treetable-toggler:first").addClass("ui-icon-triangle-1-e").removeClass("ui-icon-triangle-1-s")
}var b=this._getChildren(d);
for(var a=0;
a<b.length;
a++){var e=b[a];
e.addClass("ui-helper-hidden");
if(e.data("expanded")){this._hideNodeChildren(e,true)
}}},onRowClick:function(b,d){if($(b.target).is("td,span:not(.ui-c)")){var a=d.hasClass("ui-state-highlight"),c=b.metaKey||b.ctrlKey;
if(a&&c){this.unselectNode(d)
}else{if(this.isSingleSelection()||(this.isMultipleSelection()&&!c)){this.unselectAllNodes()
}this.selectNode(d)
}PUI.clearSelection()
}},selectNode:function(b,a){b.removeClass("ui-state-hover").addClass("ui-state-highlight").attr("aria-selected",true);
if(!a){this._trigger("nodeSelect",{},{node:b,data:b.data("puidata")})
}},unselectNode:function(b,a){b.removeClass("ui-state-highlight").attr("aria-selected",false);
if(!a){this._trigger("nodeUnselect",{},{node:b,data:b.data("puidata")})
}},unselectAllNodes:function(){var b=this.tbody.children("tr.ui-state-highlight");
for(var a=0;
a<b.length;
a++){this.unselectNode(b.eq(a),true)
}},isSingleSelection:function(){return this.options.selectionMode==="single"
},isMultipleSelection:function(){return this.options.selectionMode==="multiple"
},_getChildren:function(f){var c=f.data("rowkey"),g=f.nextAll(),e=[];
for(var d=0;
d<g.length;
d++){var a=g.eq(d),b=a.data("parentrowkey");
if(b===c){e.push(a)
}}return e
}})
});