import{c as B,d as F,l as c,R as I,aV as w,r as n,o as T,G as k,w as s,g as o,u as i,h as A,a5 as _,q as D,t as R,a6 as p,cg as b,ch as N,av as P}from"./index-ed17d07a.js";import{i as S}from"./icon-c5f6d281.js";import{u as V,E as z}from"./chartEditStore-066de5d6.js";import"./plugin-15ab4596.js";const K={class:"title"},M=F({__name:"index",setup(q){const{FishIcon:d}=S.ionicons5,f=V(),a=c(!1),u=c(null),t=c((()=>{const e=N();return e.length?e[0]:""})()||""),v=I(()=>{t.value=t.value.replace(/\s/g,"");const e=t.value.length?t.value:"\u65B0\u9879\u76EE";return w(`\u5DE5\u4F5C\u7A7A\u95F4-${e}`),f.setEditCanvasConfig(z.PROJECT_NAME,e),e}),m=()=>{a.value=!0,P(()=>{u.value&&u.value.focus()})},l=()=>{a.value=!1};return(e,r)=>{const h=n("n-icon"),x=n("n-button"),E=n("n-text"),y=n("n-input"),C=n("n-space");return T(),k(C,null,{default:s(()=>[o(h,{size:"20",depth:3},{default:s(()=>[o(i(d))]),_:1}),o(E,{onClick:m},{default:s(()=>[A(" \u5DE5\u4F5C\u7A7A\u95F4 - "),_(o(x,{secondary:"",round:"",size:"tiny"},{default:s(()=>[D("span",K,R(i(v)),1)]),_:1},512),[[p,!a.value]])]),_:1}),_(o(y,{ref_key:"inputInstRef",ref:u,size:"small",type:"text",maxlength:"16","show-count":"",round:"",placeholder:"\u8BF7\u8F93\u5165\u9879\u76EE\u540D\u79F0",value:t.value,"onUpdate:value":r[0]||(r[0]=g=>t.value=g),valueModifiers:{trim:!0},onKeyup:b(l,["enter"]),onBlur:l},null,8,["value","onKeyup"]),[[p,a.value]])]),_:1})}}});var $=B(M,[["__scopeId","data-v-a798b4b0"]]);export{$ as default};
