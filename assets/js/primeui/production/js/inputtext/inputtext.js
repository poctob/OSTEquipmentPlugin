$(function(){$.widget("primeui.puiinputtext",{_create:function(){var a=this.element,b=a.prop("disabled");
a.addClass("pui-inputtext ui-widget ui-state-default ui-corner-all");
if(b){a.addClass("ui-state-disabled")
}else{this._enableMouseEffects()
}a.attr("role","textbox").attr("aria-disabled",b).attr("aria-readonly",a.prop("readonly")).attr("aria-multiline",a.is("textarea"))
},_destroy:function(){},_enableMouseEffects:function(){var a=this.element;
a.hover(function(){a.toggleClass("ui-state-hover")
}).focus(function(){a.addClass("ui-state-focus")
}).blur(function(){a.removeClass("ui-state-focus")
})
},_disableMouseEffects:function(){var a=this.element;
a.off("mouseenter mouseleave focus blur")
},disable:function(){this.element.prop("disabled",true);
this.element.attr("aria-disabled",true);
this.element.addClass("ui-state-disabled");
this.element.removeClass("ui-state-focus ui-state-hover");
this._disableMouseEffects()
},enable:function(){this.element.prop("disabled",false);
this.element.attr("aria-disabled",false);
this.element.removeClass("ui-state-disabled");
this._enableMouseEffects()
}})
});