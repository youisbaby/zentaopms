var E=(p,a,s)=>new Promise((c,n)=>{var _=t=>{try{r(s.next(t))}catch(e){n(e)}},f=t=>{try{r(s.throw(t))}catch(e){n(e)}},r=t=>t.done?c(t.value):Promise.resolve(t.value).then(_,f);r((s=s.apply(p,a)).next())});import{M as F}from"./index-4edefa72.js";import{_ as L,o as A}from"./index-a2a728e7.js";import{u as I,E as v}from"./chartEditStore-ac923661.js";import{u as K,a as b}from"./chartLayoutStore-9d5c33b6.js";import{j as O,d as R,v as M,a5 as N,R as H,am as V,r as D,o as h,m as g,z as l,F as $,A as z,e as o,f as d,w as m,p as S,t as B,ad as U,ck as j,c1 as G}from"./index.js";import{a as J,b as Y,l as q}from"./plugin-b83a6fcc.js";import{c as u}from"./index-44e84cf4.js";import{f as k,b as w,h as P}from"./index-760f71cb.js";import"./icon-5c6b1271.js";import"./index-0abf7ae9.js";import"./index-d59ca57a.js";import"./index-d6d7fe8e.js";import"./tables_list-3a60c537.js";import"./SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-3bf74432.js";import"./useTargetData.hook-da2b6121.js";const Q={class:"go-content-charts-item-animation-patch"},W=["onDragstart","onDblclick"],X={class:"list-header"},Z={class:"list-center go-flex-center go-transition"},tt={class:"list-bottom"},et=R({__name:"index",props:{menuOptions:{type:Array,default:()=>[]}},setup(p){const a=I(),s=K(),c=M(),n=N(()=>s.getChartType),_=(t,e)=>{u(e.chartKey,k(e)),u(e.conKey,w(e)),t.dataTransfer.setData(j.DRAG_KEY,G(A(e,["image"]))),a.setEditCanvas(v.IS_CREATE,!0)},f=()=>{a.setEditCanvas(v.IS_CREATE,!1)},r=t=>E(this,null,function*(){try{J(),u(t.chartKey,k(t)),u(t.conKey,w(t));let e=yield P(t);a.addComponentList(e,!1,!0),a.setTargetSelectChart(e.id),Y()}catch(e){q(),window.$message.warning("\u56FE\u8868\u6B63\u5728\u7814\u53D1\u4E2D, \u656C\u8BF7\u671F\u5F85...")}});return H(()=>n.value,t=>{t===b.DOUBLE&&V(()=>{c.value.classList.add("miniAnimation")})}),(t,e)=>{const C=D("n-ellipsis"),x=D("n-text");return h(),g("div",Q,[l("div",{ref_key:"contentChartsItemBoxRef",ref:c,class:U(["go-content-charts-item-box",[d(n)===d(b).DOUBLE?"double":"single"]])},[(h(!0),g($,null,z(p.menuOptions,(i,T)=>(h(),g("div",{class:"item-box",key:T,draggable:"",onDragstart:y=>_(y,i),onDragend:f,onDblclick:y=>r(i)},[l("div",X,[o(d(F),{class:"list-header-control-btn",mini:!0,disabled:!0}),o(x,{class:"list-header-text",depth:"3"},{default:m(()=>[o(C,null,{default:m(()=>[S(B(i.title),1)]),_:2},1024)]),_:2},1024)]),l("div",Z,[o(d(L),{class:"list-img",chartConfig:i},null,8,["chartConfig"])]),l("div",tt,[o(x,{class:"list-bottom-text",depth:"3"},{default:m(()=>[o(C,{style:{"max-width":"90%"}},{default:m(()=>[S(B(i.title),1)]),_:2},1024)]),_:2},1024)])],40,W))),128))],2)])}}});var Ct=O(et,[["__scopeId","data-v-3bb6960e"]]);export{Ct as default};
