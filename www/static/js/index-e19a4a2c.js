import{b as C,d as b,O as c,al as o,r as h,o as t,c as r,F as x,q as z,am as u,an as B,f as F,w,E as q,W as I,u as R}from"./index-67a30bc6.js";import{i as E}from"./icon-99a136c4.js";const g={class:"go-apple-control-btn"},L=["onClick"],M=b({__name:"index",props:{mini:{request:!1,type:Boolean,default:!1},disabled:{request:!1,type:Boolean,default:!1},hidden:{request:!1,type:Array,default(){return[]}},narrow:{request:!1,type:Boolean,default:!1}},emits:["close","remove","resize","fullResize"],setup(s,{emit:d}){const a=s,{CloseIcon:f,RemoveIcon:m,ResizeIcon:_}=E.ionicons5,p=c(()=>y.filter(i=>a.hidden.findIndex(l=>i.key==l)===-1)),v=c(()=>a.narrow&&o(!0)),y=[{title:"\u5173\u95ED",key:"close",icon:f},{title:"\u7F29\u5C0F",key:"remove",icon:m},{title:v.value?"\u7F29\u5C0F":"\u653E\u5927",key:a.narrow?"fullResize":"resize",icon:_}],k=e=>{e==="fullResize"&&o(),e==="remove"&&o(!0)&&o(),d(e)};return(e,i)=>{const l=h("n-icon");return t(),r("div",g,[(t(!0),r(x,null,z(R(p),n=>(t(),r("div",{key:n.key,class:u(["btn",[n.key,s.disabled&&"disabled",s.mini&&"mini"]]),onClick:B(D=>k(n.key),["stop"])},[F(l,{size:"10",class:u(["icon-base",{hover:!s.disabled}])},{default:w(()=>[(t(),q(I(n.icon)))]),_:2},1032,["class"])],10,L))),128))])}}});var N=C(M,[["__scopeId","data-v-56375fab"]]);export{N as M};
