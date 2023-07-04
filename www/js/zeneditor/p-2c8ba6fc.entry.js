import{r as t,h as i}from"./p-fa667546.js";import{P as e,a as s,r as n,q as o,E as r}from"./p-0d1d1366.js";class h{constructor({editor:t,element:i,view:e,tippyOptions:s={},shouldShow:n}){this.preventHide=false;this.shouldShow=({view:t,state:i})=>{const{selection:e}=i;const{$anchor:s,empty:n}=e;const o=s.depth===1;const r=s.parent.isTextblock&&!s.parent.type.spec.code&&!s.parent.textContent;if(!t.hasFocus()||!n||!o||!r||!this.editor.isEditable){return false}return true};this.mousedownHandler=()=>{this.preventHide=true};this.focusHandler=()=>{setTimeout((()=>this.update(this.editor.view)))};this.blurHandler=({event:t})=>{var i;if(this.preventHide){this.preventHide=false;return}if((t===null||t===void 0?void 0:t.relatedTarget)&&((i=this.element.parentNode)===null||i===void 0?void 0:i.contains(t.relatedTarget))){return}this.hide()};this.tippyBlurHandler=t=>{this.blurHandler({event:t})};this.editor=t;this.element=i;this.view=e;if(n){this.shouldShow=n}this.element.addEventListener("mousedown",this.mousedownHandler,{capture:true});this.editor.on("focus",this.focusHandler);this.editor.on("blur",this.blurHandler);this.tippyOptions=s;this.element.remove();this.element.style.visibility="visible"}createTooltip(){const{element:t}=this.editor.options;const i=!!t.parentElement;if(this.tippy||!i){return}this.tippy=n(t,{duration:0,getReferenceClientRect:null,content:this.element,interactive:true,trigger:"manual",placement:"right",hideOnClick:"toggle",...this.tippyOptions});if(this.tippy.popper.firstChild){this.tippy.popper.firstChild.addEventListener("blur",this.tippyBlurHandler)}}update(t,i){var e,s,n;const{state:r}=t;const{doc:h,selection:l}=r;const{from:u,to:c}=l;const d=i&&i.doc.eq(h)&&i.selection.eq(l);if(d){return}this.createTooltip();const a=(e=this.shouldShow)===null||e===void 0?void 0:e.call(this,{editor:this.editor,view:t,state:r,oldState:i});if(!a){this.hide();return}(s=this.tippy)===null||s===void 0?void 0:s.setProps({getReferenceClientRect:((n=this.tippyOptions)===null||n===void 0?void 0:n.getReferenceClientRect)||(()=>o(t,u,c))});this.show()}show(){var t;(t=this.tippy)===null||t===void 0?void 0:t.show()}hide(){var t;(t=this.tippy)===null||t===void 0?void 0:t.hide()}destroy(){var t,i;if((t=this.tippy)===null||t===void 0?void 0:t.popper.firstChild){this.tippy.popper.firstChild.removeEventListener("blur",this.tippyBlurHandler)}(i=this.tippy)===null||i===void 0?void 0:i.destroy();this.element.removeEventListener("mousedown",this.mousedownHandler,{capture:true});this.editor.off("focus",this.focusHandler);this.editor.off("blur",this.blurHandler)}}const l=t=>new e({key:typeof t.pluginKey==="string"?new s(t.pluginKey):t.pluginKey,view:i=>new h({view:i,...t})});r.create({name:"floatingMenu",addOptions(){return{element:null,tippyOptions:{},pluginKey:"floatingMenu",shouldShow:null}},addProseMirrorPlugins(){if(!this.options.element){return[]}return[l({pluginKey:this.options.pluginKey,editor:this.editor,element:this.options.element,tippyOptions:this.options.tippyOptions,shouldShow:this.options.shouldShow})]}});const u="";var c=undefined&&undefined.__rest||function(t,i){var e={};for(var s in t)if(Object.prototype.hasOwnProperty.call(t,s)&&i.indexOf(s)<0)e[s]=t[s];if(t!=null&&typeof Object.getOwnPropertySymbols==="function")for(var n=0,s=Object.getOwnPropertySymbols(t);n<s.length;n++){if(i.indexOf(s[n])<0&&Object.prototype.propertyIsEnumerable.call(t,s[n]))e[s[n]]=t[s[n]]}return e};const d=class{constructor(i){t(this,i);this.menuProps=undefined;this.editor=undefined;this.element=undefined}componentDidLoad(){if(!Boolean(this.element)||this.editor.isDestroyed){return}const{pluginKey:t="floatingMenu",tippyOptions:i={},shouldShow:e=null}=this.menuProps;const s=l({pluginKey:t,editor:this.editor,element:this.element,tippyOptions:Object.assign({placement:"left",getReferenceClientRect:()=>{const t=this.editor.view.state.selection.$anchor;const i=t.pos-t.parentOffset;return o(this.editor.view,i,i)}},i),shouldShow:e!==null&&e!==void 0?e:({view:t,state:i})=>{const{selection:e}=i;const{$anchor:s,empty:n}=e;const o=s.depth===1;return!(!t.hasFocus()||!n||!o)}});this.editor.registerPlugin(s)}render(){const t=[{icon:"ci-plus",title:"Add",action:()=>{}},{icon:"ci-more_vertical",title:"Click to tune",action:()=>{}}];return i("div",{ref:t=>this.element=t,class:"floating-menu",style:{visibility:"hidden"}},t.map(((t,e)=>{const s=t,{isHidden:n,type:o}=s,r=c(s,["isHidden","type"]);if(n&&n()){return}return o==="divider"?i("div",{class:"divider",key:e}):i("tiptap-menu-item",{itemProps:Object.assign({},r),key:e})})))}};d.style=u;export{d as tiptap_floating_menu};
//# sourceMappingURL=p-2c8ba6fc.entry.js.map