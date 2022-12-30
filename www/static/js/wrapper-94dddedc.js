var X=Object.defineProperty,Y=Object.defineProperties;var J=Object.getOwnPropertyDescriptors;var $=Object.getOwnPropertySymbols;var Z=Object.prototype.hasOwnProperty,ee=Object.prototype.propertyIsEnumerable;var E=(t,e,n)=>e in t?X(t,e,{enumerable:!0,configurable:!0,writable:!0,value:n}):t[e]=n,h=(t,e)=>{for(var n in e||(e={}))Z.call(e,n)&&E(t,n,e[n]);if($)for(var n of $(e))ee.call(e,n)&&E(t,n,e[n]);return t},I=(t,e)=>Y(t,J(e));var S=(t,e,n)=>new Promise((o,i)=>{var c=d=>{try{r(n.next(d))}catch(l){i(l)}},s=d=>{try{r(n.throw(d))}catch(l){i(l)}},r=d=>d.done?o(d.value):Promise.resolve(d.value).then(c,s);r((n=n.apply(t,e)).next())});import{g as T}from"./storage-3a897533.js";import{aI as _,b as R,d as C,o as u,c as w,q as O,am as x,u as a,aJ as H,R as f,aK as z,aL as G,aM as q,E as y,aN as B,aO as N,W as j,F as A,O as m,m as K,j as p,aP as g,ab as v,p as te,f as b,Q as k,aQ as W,aR as ne,S as oe}from"./index-3da848c6.js";import{u as ie,f as re}from"./index-5ea60806.js";import{u as D}from"./useLifeHandler.hook-e1dc07ae.js";import{c as se,u as ae,C as ce}from"./chartEditStore-87bc983c.js";import"./_arrayMap-23a2d4b9.js";import"./tables_list-7cb7cb60.js";import"./http-5398a097.js";import"./plugin-3ade9cd9.js";import"./icon-42e1d81a.js";import"./SettingItemBox-6900e67c.js";/* empty css                                                                   */import"./CollapseItem-95d54d26.js";const de=(t,e,n,o)=>{const i=t,c=e,s={width:1,height:1},r=parseFloat((i/c).toFixed(5)),d=()=>{const V=parseFloat((window.innerWidth/window.innerHeight).toFixed(5));n&&(V>r?(s.width=parseFloat((window.innerHeight*r/i).toFixed(5)),s.height=parseFloat((window.innerHeight/c).toFixed(5)),n.style.transform=`scale(${s.width}, ${s.height})`):(s.height=parseFloat((window.innerWidth/r/c).toFixed(5)),s.width=parseFloat((window.innerWidth/i).toFixed(5)),n.style.transform=`scale(${s.width}, ${s.height})`),o&&o(s))},l=_(()=>{d()},200);return{calcRate:d,windowResize:()=>{window.addEventListener("resize",l)},unWindowResize:()=>{window.removeEventListener("resize",l)}}},le=(t,e,n,o)=>{const i=t,c=e,s={width:1,height:1},r=parseFloat((i/c).toFixed(5)),d=()=>{n&&(s.height=parseFloat((window.innerWidth/r/c).toFixed(5)),s.width=parseFloat((window.innerWidth/i).toFixed(5)),n.style.transform=`scale(${s.width}, ${s.height})`,o&&o(s))},l=_(()=>{d()},200);return{calcRate:d,windowResize:()=>{window.addEventListener("resize",l)},unWindowResize:()=>{window.removeEventListener("resize",l)}}},he=(t,e,n,o)=>{const i=t,c=e,s={height:1,width:1},r=parseFloat((i/c).toFixed(5)),d=()=>{n&&(s.width=parseFloat((window.innerHeight*r/i).toFixed(5)),s.height=parseFloat((window.innerHeight/c).toFixed(5)),n.style.transform=`scale(${s.width}, ${s.height})`,o&&o(s))},l=_(()=>{d()},200);return{calcRate:d,windowResize:()=>{window.addEventListener("resize",l)},unWindowResize:()=>{window.removeEventListener("resize",l)}}},ue=(t,e,n,o)=>{const i={width:1,height:1},c=()=>{n&&(i.width=parseFloat((window.innerWidth/t).toFixed(5)),i.height=parseFloat((window.innerHeight/e).toFixed(5)),n.style.transform=`scale(${i.width}, ${i.height})`,o&&o(i))},s=_(()=>{c()},200);return{calcRate:c,windowResize:()=>{window.addEventListener("resize",s)},unWindowResize:()=>{window.removeEventListener("resize",s)}}},M=(t,e)=>({zIndex:e+1,left:`${t.x}px`,top:`${t.y}px`}),Q=(t,e)=>({width:`${e?e*t.w:t.w}px`,height:`${e?e*t.h:t.h}px`}),U=t=>({display:t.hide?"none":"block"}),we=t=>{const e=t.selectColor?{background:t.background}:{background:`url(${t.backgroundImage}) center center / cover no-repeat !important`};return h({position:"relative",width:t.width?`${t.width||100}px`:"100%",height:t.height?`${t.height}px`:"100%"},e)};const pe=C({__name:"index",props:{groupData:{type:Object,required:!0},themeSetting:{type:Object,required:!0},themeColor:{type:Object,required:!0},groupIndex:{type:Number,required:!0}},setup(t){return(e,n)=>(u(!0),w(A,null,O(t.groupData.groupList,o=>(u(),w("div",{class:x(["chart-item",a(H)(o.styles.animations)]),key:o.id,style:f(h(h(h(h(h({},a(M)(o.attr,t.groupIndex)),a(z)(o.styles)),a(G)(o.styles)),a(U)(o.status)),a(q)(o.styles)))},[(u(),y(j(o.chartConfig.chartKey),B({id:o.id,chartConfig:o,themeSetting:t.themeSetting,themeColor:t.themeColor,style:h({},a(Q)(o.attr))},N(a(D)(o))),null,16,["id","chartConfig","themeSetting","themeColor","style"]))],6))),128))}});var ge=R(pe,[["__scopeId","data-v-dd0734b0"]]);const ve=C({__name:"index",props:{localStorageInfo:{type:Object,required:!0}},setup(t){const e=t,{initDataPond:n}=ie(),o=m(()=>e.localStorageInfo.editCanvasConfig.chartThemeSetting),i=m(()=>{const c=e.localStorageInfo.editCanvasConfig.chartThemeColor;return se[c]});return K(()=>{n(e.localStorageInfo.requestGlobalConfig)}),(c,s)=>(u(!0),w(A,null,O(t.localStorageInfo.componentList,(r,d)=>(u(),w("div",{class:x(["chart-item",a(H)(r.styles.animations)]),key:r.id,style:f(h(h(h(h(h({},a(M)(r.attr,d)),a(z)(r.styles)),a(G)(r.styles)),a(U)(r.status)),a(q)(r.styles)))},[r.isGroup?(u(),y(a(ge),{key:0,groupData:r,groupIndex:d,themeSetting:a(o),themeColor:a(i)},null,8,["groupData","groupIndex","themeSetting","themeColor"])):(u(),y(j(r.chartConfig.chartKey),B({key:1,id:r.id,chartConfig:r,themeSetting:a(o),themeColor:a(i),style:h({},a(Q)(r.attr))},N(a(D)(r))),null,16,["id","chartConfig","themeSetting","themeColor","style"]))],6))),128))}});var P=R(ve,[["__scopeId","data-v-535dffee"]]);const fe=t=>{const e=p(!1),n=setInterval(()=>{if(window.$vue.component){clearInterval(n);const o=i=>{window.$vue.component(i.chartConfig.chartKey)||window.$vue.component(i.chartConfig.chartKey,re(i.chartConfig))};t.componentList.forEach(i=>S(void 0,null,function*(){i.isGroup?i.groupList.forEach(c=>{o(c)}):o(i)})),e.value=!0}},200);return{show:e}},ye=t=>{const e=p(),n=p(),o=p(t.editCanvasConfig.width),i=p(t.editCanvasConfig.height);return K(()=>{switch(t.editCanvasConfig.previewScaleType){case g.FIT:(()=>{const{calcRate:c,windowResize:s,unWindowResize:r}=de(o.value,i.value,n.value);c(),s(),v(()=>{r()})})();break;case g.SCROLL_Y:(()=>{const{calcRate:c,windowResize:s,unWindowResize:r}=le(o.value,i.value,n.value,d=>{const l=e.value;l.style.width=`${o.value*d.width}px`,l.style.height=`${i.value*d.height}px`});c(),s(),v(()=>{r()})})();break;case g.SCROLL_X:(()=>{const{calcRate:c,windowResize:s,unWindowResize:r}=he(o.value,i.value,n.value,d=>{const l=e.value;l.style.width=`${o.value*d.width}px`,l.style.height=`${i.value*d.height}px`});c(),s(),v(()=>{r()})})();break;case g.FULL:(()=>{const{calcRate:c,windowResize:s,unWindowResize:r}=ue(o.value,i.value,n.value);c(),s(),v(()=>{r()})})();break}}),{entityRef:e,previewRef:n}},me=t=>{const e=ae();e.requestGlobalConfig=t[ce.REQUEST_GLOBAL_CONFIG]};const _e=C({__name:"index",setup(t){const e=T(),n=m(()=>h(h({},we(e.editCanvasConfig)),z(e.editCanvasConfig))),o=m(()=>{const r=e.editCanvasConfig.previewScaleType;return r===g.SCROLL_Y||r===g.SCROLL_X});me(e);const{entityRef:i,previewRef:c}=ye(e),{show:s}=fe(e);return(r,d)=>(u(),w("div",{class:x(`go-preview ${a(e).editCanvasConfig.previewScaleType}`)},[a(o)?(u(),w("div",{key:0,ref_key:"entityRef",ref:i,class:"go-preview-entity"},[te("div",{ref_key:"previewRef",ref:c,class:"go-preview-scale"},[a(s)?(u(),w("div",{key:0,style:f(a(n))},[b(a(P),{localStorageInfo:a(e)},null,8,["localStorageInfo"])],4)):k("",!0)],512)],512)):(u(),w("div",{key:1,ref_key:"previewRef",ref:c,class:"go-preview-scale"},[a(s)?(u(),w("div",{key:0,style:f(a(n))},[b(a(P),{localStorageInfo:a(e)},null,8,["localStorageInfo"])],4)):k("",!0)],512))],2))}});var Ce=R(_e,[["__scopeId","data-v-559a6a9b"]]);const Oe=C({__name:"wrapper",setup(t){let e=p(Date.now());return[W.JSON,W.CHART].forEach(n=>{!window.opener||window.opener.addEventListener(n,o=>S(this,null,function*(){const i=yield T();ne(oe.GO_CHART_STORAGE_LIST,[I(h({},o.detail),{id:i.id})]),e.value=Date.now()}))}),(n,o)=>(u(),y(Ce,{key:a(e)}))}});export{Oe as default};
