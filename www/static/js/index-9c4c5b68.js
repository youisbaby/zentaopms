import{j as w,d as L,v as R,R as y,c4 as e,c5 as a,r as p,o as _,c as I,w as c,e as s,z as o,f as H,m as i,A as x,t as C,p as E,F as A,D,E as k}from"./index.js";import{i as K}from"./icon-06761fe6.js";const m=u=>(D("data-v-b7bd4f6a"),u=u(),k(),u),g=m(()=>o("th",null,"\u529F\u80FD",-1)),M=m(()=>o("th",null,"Win \u5FEB\u6377\u952E",-1)),V=m(()=>o("span",null," Mac \u5FEB\u6377\u952E ",-1)),z={key:0},G={key:1},O=L({__name:"ShortcutKeyModal",props:{modelShow:Boolean},emits:["update:modelShow"],setup(u,{emit:l}){const U=u,{CloseIcon:n}=K.ionicons5,r=R(!1);y(()=>U.modelShow,F=>{r.value=F});const d=[{label:"\u62D6\u62FD\u753B\u5E03",win:`${e.SPACE.toUpperCase()} + \u{1F5B1}\uFE0F `,mac:`${a.SPACE.toUpperCase()} + \u{1F5B1}\uFE0F `,macSource:!0},{label:"\u5411 \u4E0A/\u53F3/\u4E0B/\u5DE6 \u79FB\u52A8",win:`${e.CTRL.toUpperCase()} + \u2191 \u6216 \u2192 \u6216 \u2193 \u6216 \u2190`,mac:`${a.CTRL.toUpperCase()} + \u2191 `},{label:"\u9501\u5B9A",win:`${e.CTRL.toUpperCase()} + L `,mac:`${a.CTRL.toUpperCase()} + L `},{label:"\u89E3\u9501",win:`${e.CTRL.toUpperCase()} + ${e.SHIFT.toUpperCase()}+ L `,mac:`${a.CTRL.toUpperCase()} + ${a.SHIFT.toUpperCase()} + L `},{label:"\u5C55\u793A",win:`${e.CTRL.toUpperCase()} + H `,mac:`${a.CTRL.toUpperCase()} + H `},{label:"\u9690\u85CF",win:`${e.CTRL.toUpperCase()} + ${e.SHIFT.toUpperCase()} + H `,mac:`${a.CTRL.toUpperCase()} + ${a.SHIFT.toUpperCase()} + H `},{label:"\u5220\u9664",win:"Delete".toUpperCase(),mac:`${a.CTRL.toUpperCase()} + Backspace `},{label:"\u590D\u5236",win:`${e.CTRL.toUpperCase()} + C `,mac:`${a.CTRL.toUpperCase()} + C `},{label:"\u526A\u5207",win:`${e.CTRL.toUpperCase()} + X `,mac:`${a.CTRL.toUpperCase()} + X `},{label:"\u7C98\u8D34",win:`${e.CTRL.toUpperCase()} + V `,mac:`${a.CTRL.toUpperCase()} + V `},{label:"\u540E\u9000",win:`${e.CTRL.toUpperCase()} + Z `,mac:`${a.CTRL.toUpperCase()} + Z `},{label:"\u524D\u8FDB",win:`${e.CTRL.toUpperCase()} + ${e.SHIFT.toUpperCase()} + Z `,mac:`${a.CTRL.toUpperCase()} + ${a.SHIFT.toUpperCase()} + Z `},{label:"\u591A\u9009",win:`${e.CTRL.toUpperCase()} + \u{1F5B1}\uFE0F `,mac:`${a.CTRL_SOURCE_KEY.toUpperCase()} + \u{1F5B1}\uFE0F `},{label:"\u521B\u5EFA\u5206\u7EC4",win:`${e.CTRL.toUpperCase()} + G / \u{1F5B1}\uFE0F `,mac:`${a.CTRL_SOURCE_KEY.toUpperCase()} + G / \u{1F5B1}\uFE0F`},{label:"\u89E3\u9664\u5206\u7EC4",win:`${e.CTRL.toUpperCase()} + ${e.SHIFT.toUpperCase()} + G `,mac:`${a.CTRL_SOURCE_KEY.toUpperCase()} + ${e.SHIFT.toUpperCase()} + G `}],$=()=>{l("update:modelShow",!1)};return(F,T)=>{const S=p("n-icon"),b=p("n-space"),h=p("n-gradient-text"),f=p("n-table"),v=p("n-modal");return _(),I(v,{show:r.value,"onUpdate:show":T[0]||(T[0]=t=>r.value=t),"mask-closable":!0,onAfterLeave:$},{default:c(()=>[s(f,{class:"model-content",bordered:!1,"single-line":!1},{default:c(()=>[o("thead",null,[o("tr",null,[g,M,o("th",null,[s(b,{justify:"space-between"},{default:c(()=>[V,s(S,{size:"20",class:"go-cursor-pointer",onClick:$},{default:c(()=>[s(H(n))]),_:1})]),_:1})])])]),o("tbody",null,[(_(),i(A,null,x(d,(t,B)=>o("tr",{key:B},[o("td",null,C(t.label),1),o("td",null,C(t.win),1),t.macSource?(_(),i("td",z,C(t.mac),1)):(_(),i("td",G,[s(h,{size:22},{default:c(()=>[E(C(t.mac.substr(0,1)),1)]),_:2},1024),E(" + "+C(t.mac.substr(3)),1)]))])),64))])]),_:1})]),_:1},8,["show"])}}});var Z=w(O,[["__scopeId","data-v-b7bd4f6a"]]);const N={class:"go-edit-shortcut"},Y=L({__name:"index",setup(u){const l=R(!1);return(U,n)=>{const r=p("n-select");return _(),i("div",N,[s(Z,{modelShow:l.value,"onUpdate:modelShow":n[0]||(n[0]=d=>l.value=d)},null,8,["modelShow"]),s(r,{class:"scale-btn",value:"\u5FEB\u6377\u952E",size:"mini",onClick:n[1]||(n[1]=d=>l.value=!0)})])}}});var W=w(Y,[["__scopeId","data-v-74fcd649"]]);export{W as E};
