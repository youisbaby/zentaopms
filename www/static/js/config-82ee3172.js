var h=Object.defineProperty;var r=Object.getOwnPropertySymbols;var d=Object.prototype.hasOwnProperty,l=Object.prototype.propertyIsEnumerable;var a=(i,t,o)=>t in i?h(i,t,{enumerable:!0,configurable:!0,writable:!0,value:o}):i[t]=o,e=(i,t)=>{for(var o in t||(t={}))d.call(t,o)&&a(i,o,t[o]);if(r)for(var o of r(t))l.call(t,o)&&a(i,o,t[o]);return i};import{al as s}from"./index.js";import{e as m}from"./chartEditStore-1895a273.js";import{g as n}from"./index-99916e8f.js";import"./plugin-655db539.js";import"./icon-06761fe6.js";import"./table_scrollboard-f08235e7.js";import"./SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-2a228e35.js";import"./useTargetData.hook-e92909f9.js";var c=[["\u884C1\u52171","\u884C1\u52172","\u884C1\u52173"],["\u884C2\u52171","\u884C2\u52172","\u884C2\u52173"],["\u884C3\u52171","\u884C3\u52172","\u884C3\u52173"],["\u884C4\u52171","\u884C4\u52172","\u884C4\u52173"],["\u884C5\u52171","\u884C5\u52172","\u884C5\u52173"],["\u884C6\u52171","\u884C6\u52172","\u884C6\u52173"],["\u884C7\u52171","\u884C7\u52172","\u884C7\u52173"],["\u884C8\u52171","\u884C8\u52172","\u884C8\u52173"],["\u884C9\u52171","\u884C9\u52172","\u884C9\u52173"],["\u884C10\u52171","\u884C10\u52172","\u884C10\u52173"]];const g={header:["\u52171","\u52172","\u52173"],dataset:c,index:!0,columnWidth:[30,100,100],align:["center","right","right","right"],rowNum:5,waitTime:2,headerHeight:35,carousel:"single",headerBGC:"#00BAFF",oddRowBGC:"#003B51",evenRowBGC:"#0A2732"};class v extends m{constructor(t){super(),this.key=n.key,this.chartConfig=s(n),this.option=s(g);const{dataset:o,tableInfo:p}=t;this.option.dataset=o,this.option=e(e({},this.option),p)}}export{v as default,g as option};
