var u=Object.defineProperty,m=Object.defineProperties;var c=Object.getOwnPropertyDescriptors;var l=Object.getOwnPropertySymbols;var b=Object.prototype.hasOwnProperty,g=Object.prototype.propertyIsEnumerable;var o=(a,e,t)=>e in a?u(a,e,{enumerable:!0,configurable:!0,writable:!0,value:t}):a[e]=t,r=(a,e)=>{for(var t in e||(e={}))b.call(e,t)&&o(a,t,e[t]);if(l)for(var t of l(e))g.call(e,t)&&o(a,t,e[t]);return a},i=(a,e)=>m(a,c(e));import{al as n,ax as p}from"./index.js";import{P as f,b as C,e as d}from"./chartEditStore-1f8105d2.js";import{Y as h}from"./table_scrollboard-3667f4b7.js";import{h as v,i as y}from"./index-5c8ab160.js";import"./plugin-9e9b27b9.js";import"./icon-0617f5fe.js";import"./chartLayoutStore-0f501f0a.js";/* empty css                                                                */import"./SettingItemBox-aedd53df.js";import"./CollapseItem-05ae1c53.js";import"./useTargetData.hook-65515aeb.js";const s={key:"Select",chartKey:"VSelect",conKey:"VCSelect",title:"\u9009\u62E9",category:v.MORE,categoryName:y.MORE,package:f.INFORMATIONS,chartFrame:C.COMMON,image:h};var k=[{label:"\u8BF7\u9009\u62E9",value:""},{label:"\u8363\u6210",value:"26700"},{label:"\u6CB3\u5357",value:"20700",disabled:!0},{label:"\u6CB3\u5317",value:"18700"},{label:"\u5F90\u5DDE",value:"17800"},{label:"\u6F2F\u6CB3",value:"16756"},{label:"\u4E09\u95E8\u5CE1",value:"12343"},{label:"\u90D1\u5DDE",value:"9822"},{label:"\u5468\u53E3",value:"8912"},{label:"\u6FEE\u9633",value:"6834"},{label:"\u4FE1\u9633",value:"5875"},{label:"\u65B0\u4E61",value:"3832"},{label:"\u5927\u540C",value:"1811"}];const E={dataset:k,value:"",borderWidth:1,borderStyle:"solid",borderColor:"#1a77a5",background:"none",borderRadius:6,color:"#ffffff",textAlign:"center",fontWeight:"normal",backgroundColor:"transparent",fontSize:20,onChange(a,e){}};class V extends d{constructor(){super(...arguments),this.key=s.key,this.chartConfig=n(s),this.option=n(E),this.attr=i(r({},p),{w:200,h:36,zIndex:1})}}export{V as default,E as option};
