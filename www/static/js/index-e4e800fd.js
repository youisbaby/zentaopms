var Y=(e,t,a)=>new Promise((l,o)=>{var u=d=>{try{h(a.next(d))}catch(I){o(I)}},r=d=>{try{h(a.throw(d))}catch(I){o(I)}},h=d=>d.done?l(d.value):Promise.resolve(d.value).then(u,r);h((a=a.apply(e,t)).next())});import{d as U,v as y,R as Z,ca as ue,X as _e,o as c,m as v,cb as de,cc as pe,cd as ee,ce as ve,a as me,cf as ge,cg as fe,ch as he,ci as ye,j as te,an as Ce,y as be,r as S,e as f,f as s,z as m,F as R,A as $,t as E,ab as b,w as L,D as ke,E as Oe,cj as Se,c0 as Ee,u as Ie,a3 as xe,aA as De,a1 as w,bR as Ae,c as T,b_ as Le,ad as we,L as Te}from"./index.js";import{C as Ne}from"./index-03ff197a.js";import{a as Pe,f as Re,b as $e,p as ze}from"./index-99916e8f.js";import{u as Fe,E as q,P as N,i as P}from"./chartEditStore-1895a273.js";import{i as ae}from"./icon-06761fe6.js";import{l as Be,c as J}from"./index-d70c4942.js";import{u as ne}from"./chartLayoutStore-4789a483.js";const Ue={class:"list-img",alt:"\u56FE\u8868\u56FE\u7247"},X=U({__name:"index",props:{chartConfig:{type:Object,required:!0}},setup(e){const t=e,a=y(""),l=()=>Y(this,null,function*(){a.value=yield Pe(t.chartConfig)});return Z(()=>t.chartConfig.key,()=>l(),{immediate:!0}),(o,u)=>{const r=ue("lazy");return _e((c(),v("img",Ue,null,512)),[[r,a.value]])}}});function Ke(e){var t=e==null?0:e.length;return t?e[t-1]:void 0}var Ge=Ke;function Ve(e,t,a){var l=-1,o=e.length;t<0&&(t=-t>o?0:o+t),a=a>o?o:a,a<0&&(a+=o),o=t>a?0:a-t>>>0,t>>>=0;for(var u=Array(o);++l<o;)u[l]=e[l+t];return u}var He=Ve,Me=de,je=He;function Ye(e,t){return t.length<2?e:Me(e,je(t,0,-1))}var qe=Ye,Je=ee,Xe=Ge,Qe=qe,We=pe;function Ze(e,t){return t=Je(t,e),e=Qe(e,t),e==null||delete e[We(Xe(t))]}var et=Ze,tt=ve;function at(e){return tt(e)?void 0:e}var nt=at,st=me,ot=ye,ct=et,lt=ee,rt=ge,it=nt,ut=fe,_t=he,dt=1,pt=2,vt=4,mt=ut(function(e,t){var a={};if(e==null)return a;var l=!1;t=st(t,function(u){return u=lt(u,e),l||(l=u.length>1),u}),rt(e,_t(e),a),l&&(a=ot(a,dt|pt|vt,it));for(var o=t.length;o--;)ct(a,t[o]);return a}),gt=mt;const ft=e=>(ke("data-v-3c8a37de"),e=e(),Oe(),e),ht={class:"go-chart-common"},yt={key:0,class:"charts"},Ct={class:"tree-top"},bt=ft(()=>m("span",null,"\u6240\u6709\u5206\u7EC4",-1)),kt={class:"chapter chapter-first"},Ot={class:"label-count"},St=["onDragstart"],Et={key:1,class:"chapter chapter-last"},It=["onDragstart"],xt={key:1,class:"no-charts"},Dt={class:"chart-content-list"},At=U({__name:"index",props:{selectOptions:{type:Object,default:()=>{}},menu:{type:String,default:()=>{}}},setup(e){var M,j;const t=e,a=Be(()=>Ce(()=>import("./index-d9e44086.js"),["static/js/index-d9e44086.js","static/css/index-edd8c06a.css","static/js/index-4b0d0f6e.js","static/css/index-1fb675e6.css","static/js/index.js","static/css/index-0098e772.css","static/js/icon-06761fe6.js","static/js/chartEditStore-1895a273.js","static/js/plugin-655db539.js","static/js/chartLayoutStore-4789a483.js","static/js/index-d70c4942.js","static/css/index-3cd64027.css","static/js/index-e2b15f0b.js","static/js/index-e53b905d.js","static/css/index-c496491e.css","static/js/index-99916e8f.js","static/css/index-02bb4650.css","static/js/table_scrollboard-f08235e7.js","static/js/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-2a228e35.js","static/css/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-a753658f.css","static/js/useTargetData.hook-e92909f9.js","static/js/index-03ff197a.js","static/css/index-d494d603.css"])),{ChevronUpOutlineIcon:l,ChevronDownOutlineIcon:o}=ae.ionicons5,u=(M=window.dimensions)!=null?M:[],r=y(u[0]?u[0].value:""),h=(j=window.treeData)!=null?j:[],d=h.chart?y(t.menu=="Charts"?h.chart[r.value]:h.pivot[r.value]):y(),I=()=>{d.value=h.chart?t.menu=="Charts"?h.chart[r.value]:h.pivot[r.value]:""},x=y(!1),C=y([]),se=()=>{x.value=!x.value;for(const i in d.value){C.value[d.value[i].id]=x.value;for(const n in d.value[i].child)C.value[d.value[i].child[n].id]=x.value}},K=i=>{C.value[i]=!C.value[i]};let _=be({menuOptions:[],selectOptions:{},categorys:{all:[]},categoryNames:{all:"\u6240\u6709"},categorysNum:0,saveSelectOptions:{}});const z=y(),oe=i=>{for(const n in i){_.selectOptions=i[n];break}};Z(()=>t.selectOptions,i=>{if(_.categorysNum=0,!!i){i.list.forEach(n=>{const D=_.categorys[n.category];_.categorys[n.category]=D&&D.length?[...D,n]:[n],_.categoryNames[n.category]=n.categoryName,_.categorys.all.push(n)});for(const n in _.categorys)_.categorysNum+=1,_.menuOptions.push({key:n,label:_.categoryNames[n]});oe(_.categorys),z.value=_.menuOptions[0].key}},{immediate:!0});const ce=i=>{_.selectOptions=_.categorys[i]},G=Fe(),V=(i,n)=>{J(n.chartKey,Re(n)),J(n.conKey,$e(n)),i.dataTransfer.setData(Se.DRAG_KEY,Ee(gt(n,["image"]))),G.setEditCanvas(q.IS_CREATE,!0)},H=()=>{G.setEditCanvas(q.IS_CREATE,!1)};return(i,n)=>{const D=S("n-select"),F=S("n-icon"),le=S("n-menu"),re=S("n-scrollbar");return c(),v("div",ht,[e.menu=="Charts"||e.menu=="Tables"?(c(),v("div",yt,[f(D,{class:"dimension-btn",value:r.value,"onUpdate:value":[n[0]||(n[0]=g=>r.value=g),I],options:s(u)},null,8,["value","options"]),m("div",Ct,[bt,f(F,{size:"16",component:x.value?s(l):s(o),onClick:se},null,8,["component"])]),(c(!0),v(R,null,$(s(d),(g,Mt)=>(c(),v("div",{class:"tree-body",key:g.id},[m("div",null,[m("div",kt,[f(F,{size:"16",component:C.value[g.id]?s(l):s(o),onClick:p=>K(g.id)},null,8,["component","onClick"]),m("label",null,E(g.title),1),m("label",Ot,E(g.count),1)]),g.child&&C.value[g.id]?(c(!0),v(R,{key:0},$(g.child,p=>(c(),v("div",{class:"tree-child",key:p.id},[p.type!="chapter"?(c(),v("div",{key:0,class:"item-box",draggable:"",onDragstart:O=>V(O,p.chartConfig),onDragend:H},[f(s(X),{class:"list-img",chartConfig:p.chartConfig},null,8,["chartConfig"]),m("span",null,E(p.title),1)],40,St)):b("",!0),p.type=="chapter"?(c(),v("div",Et,[f(F,{size:"16",component:C.value[p.id]?s(l):s(o),onClick:O=>K(p.id)},null,8,["component","onClick"]),m("label",null,E(p.title),1)])):b("",!0),p.child&&C.value[p.id]?(c(!0),v(R,{key:2},$(p.child,O=>(c(),v("div",{class:"tree-child",key:p.id},[O.type!="chapter"?(c(),v("div",{key:0,class:"item-box",draggable:"",onDragstart:ie=>V(ie,O.chartConfig),onDragend:H},[f(s(X),{class:"list-img",chartConfig:O.chartConfig},null,8,["chartConfig"]),m("span",null,E(O.title),1)],40,It)):b("",!0)]))),128)):b("",!0)]))),128)):b("",!0)])]))),128))])):b("",!0),e.menu!="Charts"&&e.menu!="Tables"?(c(),v("div",xt,[f(le,{class:"chart-menu-width",value:z.value,"onUpdate:value":[n[1]||(n[1]=g=>z.value=g),ce],options:s(_).menuOptions,"icon-size":16,indent:18},null,8,["value","options"]),m("div",Dt,[f(re,null,{default:L(()=>[f(s(a),{menuOptions:s(_).selectOptions},null,8,["menuOptions"])]),_:1})])])):b("",!0)])}}});var Lt=te(At,[["__scopeId","data-v-3c8a37de"]]);const wt=Ie(),Tt=y(wt.getAppTheme);ne();const{getCharts:Nt}=xe(ne()),Pt=De({id:"usePackagesStore",state:()=>({packagesList:Object.freeze(ze)}),getters:{getPackagesList(){return this.packagesList}}}),{TableSplitIcon:Rt,RoadmapIcon:$t,SpellCheckIcon:zt,GraphicalDataFlowIcon:Ft}=ae.carbon,{getPackagesList:Q}=Pt(),k=[],W={[N.CHARTS]:{icon:w($t),label:P.CHARTS},[N.INFORMATIONS]:{icon:w(zt),label:P.INFORMATIONS},[N.TABLES]:{icon:w(Rt),label:P.TABLES},[N.DECORATES]:{icon:w(Ft),label:P.DECORATES}},Bt=()=>{for(const e in Q)k.push({key:e,icon:W[e].icon,label:W[e].label,list:Q[e]})};Bt();k[0].key;const A=y(k[0].key),B=y(k[0]),Ut=e=>{for(const t in k)k[t].key==e&&(B.value=k[t])};const Kt={class:"menu-width-box"},Gt={class:"menu-component-box"},Vt=U({__name:"index",setup(e){return Ae(t=>({"5deb1ea8":s(Tt)})),(t,a)=>{const l=S("n-icon"),o=S("n-tab-pane"),u=S("n-tabs");return c(),T(s(Ne),{class:we(["go-content-charts",{scoped:!s(Nt)}]),backIcon:!1},{default:L(()=>[m("aside",null,[m("div",Kt,[f(u,{value:s(A),"onUpdate:value":[a[0]||(a[0]=r=>Le(A)?A.value=r:null),s(Ut)],class:"tabs-box",size:"small",type:"segment"},{default:L(()=>[(c(!0),v(R,null,$(s(k),r=>(c(),T(o,{key:r.key,name:r.key,size:"small","display-directive":"show:lazy"},{tab:L(()=>[m("span",null,E(r.label),1),f(l,{size:"16",class:"icon-position"},{default:L(()=>[(c(),T(Te(r.icon)))]),_:2},1024)]),_:2},1032,["name"]))),128))]),_:1},8,["value","onUpdate:value"]),m("div",Gt,[s(B)?(c(),T(s(Lt),{selectOptions:s(B),menu:s(A),key:s(A)},null,8,["selectOptions","menu"])):b("",!0)])])])]),_:1},8,["class"])}}});var Ht=te(Vt,[["__scopeId","data-v-2901031b"]]),ea=Object.freeze(Object.defineProperty({__proto__:null,default:Ht},Symbol.toStringTag,{value:"Module"}));export{X as _,ea as i,gt as o};
