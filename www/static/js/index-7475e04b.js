import{ah as M,ai as B,aj as t,a1 as a,v as U,ak as C,al as V,am as F,j as N,d as G,r as E,o as $,m as j,z as k,e as u,w as m,f as l,F as z,an as d}from"./index.js";import{_ as K}from"./index-b19e069c.js";import{u as I,a as W}from"./chartEditStore-4e92d8bb.js";import{l as Y}from"./plugin-93d20baa.js";import{i as b}from"./icon-3165ab42.js";const Ie=(n,o)=>{!window.$vue.component(n)&&o&&window.$vue.component(n,o)},_=n=>M({loader:n,loadingComponent:B,delay:20}),{CopyIcon:X,CutIcon:q,ClipboardOutlineIcon:J,TrashIcon:Q,ChevronDownIcon:Z,ChevronUpIcon:ee,LockOpenOutlineIcon:ne,LockClosedOutlineIcon:te,EyeOutlineIcon:oe,EyeOffOutlineIcon:ae}=b.ionicons5,{UpToTopIcon:le,DownToBottomIcon:ie,PaintBrushIcon:se,Carbon3DSoftwareIcon:re,Carbon3DCursorIcon:ue}=b.carbon,e=I(),O=(n=3)=>({type:"divider",key:`d${n}`}),f=[{label:"\u9501\u5B9A",key:t.LOCK,icon:a(te),fnHandle:e.setLock},{label:"\u89E3\u9501",key:t.UNLOCK,icon:a(ne),fnHandle:e.setUnLock},{label:"\u9690\u85CF",key:t.HIDE,icon:a(ae),fnHandle:e.setHide},{label:"\u663E\u793A",key:t.SHOW,icon:a(oe),fnHandle:e.setShow},{type:"divider",key:"d0"},{label:"\u590D\u5236",key:t.COPY,icon:a(X),fnHandle:e.setCopy},{label:"\u526A\u5207",key:t.CUT,icon:a(q),fnHandle:e.setCut},{label:"\u7C98\u8D34",key:t.PARSE,icon:a(J),fnHandle:e.setParse},{type:"divider",key:"d1"},{label:"\u7F6E\u9876",key:t.TOP,icon:a(le),fnHandle:e.setTop},{label:"\u7F6E\u5E95",key:t.BOTTOM,icon:a(ie),fnHandle:e.setBottom},{label:"\u4E0A\u79FB",key:t.UP,icon:a(ee),fnHandle:e.setUp},{label:"\u4E0B\u79FB",key:t.DOWN,icon:a(Z),fnHandle:e.setDown},{type:"divider",key:"d2"},{label:"\u6E05\u7A7A\u526A\u8D34\u677F",key:t.CLEAR,icon:a(se),fnHandle:e.setRecordChart},{label:"\u5220\u9664",key:t.DELETE,icon:a(Q),fnHandle:e.removeComponentList}],y=[{label:"\u521B\u5EFA\u5206\u7EC4",key:t.GROUP,icon:a(re),fnHandle:e.setGroup},{label:"\u89E3\u9664\u5206\u7EC4",key:t.UN_GROUP,icon:a(ue),fnHandle:e.setUnGroup}],ce=[t.PARSE,t.CLEAR],S=(n,o)=>{if(!o)return n;const i=[];return o.forEach(c=>{i.push(...n.filter(s=>s.key===c))}),i},de=(n,o)=>o?n.filter(i=>o.findIndex(c=>c!==i.key)!==-1):n,r=U([]),_e=(n,o,i,c,s)=>{n.stopPropagation(),n.preventDefault();let p=n.target;for(;p instanceof SVGElement;)p=p.parentNode;e.setTargetSelectChart(o&&o.id),e.setRightMenuShow(!1),e.getTargetChart.selectId.length>1?r.value=y:r.value=f,o||(r.value=S(C(r.value),ce)),c&&(r.value=de([...y,O(),...f],c)),s&&(r.value=S([...y,O(),...f],s)),i&&(r.value=i(V(C(r.value)),[...y,...f],o)),F().then(()=>{e.setMousePosition(n.clientX,n.clientY),e.setRightMenuShow(!0)})},fe=()=>(r.value=f,{menuOptions:r,defaultOptions:f,defaultMultiSelectOptions:y,handleContextMenu:_e,onClickOutSide:()=>{e.setRightMenuShow(!1)},handleMenuSelect:i=>{e.setRightMenuShow(!1);const c=r.value.filter(s=>s.key===i);r.value.forEach(s=>{if(s.key===i){if(s.fnHandle){s.fnHandle();return}c||Y()}})},mousePosition:e.getMousePosition});const pe={class:"go-chart"},me={style:{overflow:"hidden",display:"flex"}},ye=G({__name:"index",setup(n){const o=W(),i=I();o.canvasInit(i.getEditCanvas);const c=_(()=>d(()=>import("./index-43a4982a.js"),["static/js/index-43a4982a.js","static/js/index.js","static/css/index-0098e772.css"])),s=_(()=>d(()=>import("./index-1a62a93d.js"),["static/js/index-1a62a93d.js","static/css/index-1c901762.css","static/js/index.js","static/css/index-0098e772.css","static/js/chartEditStore-4e92d8bb.js","static/js/plugin-93d20baa.js","static/js/icon-3165ab42.js"])),p=_(()=>d(()=>import("./index-a12ddc99.js"),["static/js/index-a12ddc99.js","static/css/index-03eb36ad.css","static/js/index.js","static/css/index-0098e772.css","static/js/icon-3165ab42.js","static/js/chartEditStore-4e92d8bb.js","static/js/plugin-93d20baa.js","static/js/chartLayoutStore-c2304649.js","static/js/index-3b00be53.js","static/css/index-0134c4e2.css"])),g=_(()=>d(()=>import("./index-0361e873.js"),["static/js/index-0361e873.js","static/css/index-43917ffd.css","static/css/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-809680a9.css","static/js/index.js","static/css/index-0098e772.css","static/js/index-5e30488f.js","static/css/index-a42fa8df.css","static/js/chartLayoutStore-c2304649.js","static/js/chartEditStore-4e92d8bb.js","static/js/plugin-93d20baa.js","static/js/icon-3165ab42.js","static/js/index-5ebe4b04.js","static/css/index-1f116f4d.css","static/js/table_scrollboard-3667f4b7.js","static/js/SettingItemBox-b0b9493a.js","static/js/CollapseItem-b1a0585d.js","static/js/useTargetData.hook-8749a194.js","static/js/index-b19e069c.js","static/js/index-37a7b589.js","static/css/index-19141fc2.css"])),w=_(()=>d(()=>import("./index-5b9dfd41.js").then(function(v){return v.i}),["static/js/index-5b9dfd41.js","static/css/index-d8f032e8.css","static/js/index.js","static/css/index-0098e772.css","static/js/index-5e30488f.js","static/css/index-a42fa8df.css","static/js/index-5ebe4b04.js","static/css/index-1f116f4d.css","static/css/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-809680a9.css","static/js/chartEditStore-4e92d8bb.js","static/js/plugin-93d20baa.js","static/js/icon-3165ab42.js","static/js/chartLayoutStore-c2304649.js","static/js/table_scrollboard-3667f4b7.js","static/js/SettingItemBox-b0b9493a.js","static/js/CollapseItem-b1a0585d.js","static/js/useTargetData.hook-8749a194.js"])),H=_(()=>d(()=>import("./index-a347281b.js"),["static/js/index-a347281b.js","static/css/index-9e9c6d93.css","static/js/index.js","static/css/index-0098e772.css","static/js/index-5e30488f.js","static/css/index-a42fa8df.css","static/js/chartLayoutStore-c2304649.js","static/js/chartEditStore-4e92d8bb.js","static/js/plugin-93d20baa.js","static/js/icon-3165ab42.js","static/js/index-b19e069c.js","static/js/index-37a7b589.js","static/css/index-19141fc2.css"])),P=_(()=>d(()=>import("./index-74a4ee3e.js"),["static/js/index-74a4ee3e.js","static/js/chartLayoutStore-c2304649.js","static/js/index.js","static/css/index-0098e772.css","static/js/chartEditStore-4e92d8bb.js","static/js/plugin-93d20baa.js","static/js/icon-3165ab42.js"])),{menuOptions:A,onClickOutSide:T,mousePosition:h,handleMenuSelect:R}=fe();return(v,he)=>{const D=E("n-layout-content"),x=E("n-layout"),L=E("n-dropdown");return $(),j(z,null,[k("div",pe,[u(x,null,{default:m(()=>[u(l(K),null,{left:m(()=>[u(l(c))]),center:m(()=>[u(l(p))]),"ri-left":m(()=>[u(l(s))]),_:1}),u(D,{"content-style":"overflow:hidden; display: flex"},{default:m(()=>[k("div",me,[u(l(w)),u(l(g))]),u(l(H))]),_:1})]),_:1})]),u(L,{placement:"bottom-start",trigger:"manual",size:"small",x:l(h).x,y:l(h).y,options:l(A),show:l(i).getRightMenuShow,"on-clickoutside":l(T),onSelect:l(R)},null,8,["x","y","options","show","on-clickoutside","onSelect"]),u(l(P))],64)}}});var Ee=N(ye,[["__scopeId","data-v-ddb094b8"]]),be=Object.freeze(Object.defineProperty({__proto__:null,default:Ee},Symbol.toStringTag,{value:"Module"}));export{Ie as c,O as d,be as i,_ as l,fe as u};
