import{g as q,b as E,a as D,k as M,s as z,X as k,_ as F,q as L,Y as T,W as j,V as G,a0 as U}from"./common.dca1500igi.js";import{d as X,r as l,j as _,B as I,o as $,c as O,a as t,s as n,x as d,U as P,q as Y,t as h,l as K}from"./vendor.dca1500igi.js";const W=()=>q({query:`
      {
        processes(
          first: 100
          active: true
        ) {
          data {
            id
            name
            stages {
              id
              type
              name
              startAt
              endAt
              status
            }
            schoolYear {
              year
            }
            grades {
              id
              name
            }
          }
        }
      }
    `}).then(u=>u.data.data.processes.data.filter(r=>r.stages.length)),H=v=>q({variables:v,query:`
      query vacancies(
        $processes: [ID!]
      ) {
        vacancies(
          processes: $processes
        ) {
          process
          grade
          school
          period
        }
        schools(
          first: 200
          processes: $processes
        ) {
          data {
            id
            name
            lat: latitude
            lng: longitude
            area_code
            phone
          }
        }
      }
    `}).then(r=>({vacancies:r.data.data.vacancies,schools:r.data.data.schools.data.map(i=>({...i,position:{lat:i.lat,lng:i.lng}}))})),J={class:"row mt-3"},Q={class:"row"},R={class:"col-12"},Z={class:"mt-2"},ee={class:"d-flex justify-content-between"},ae={class:"d-flex align-items-center mt-3"},se={class:"row"},te={class:"col-12 mt-5"},oe={class:"card"},le={class:"col-12"},ne={class:"col-12"},de=X({__name:"SchoolFind",setup(v){const{loader:u,data:r}=E([]),{loader:i}=E(),y=l([]),b=l([]),c=D(),A=_(()=>c.map.config),x=l(c.map.center.lat),w=l(c.map.center.lng),N=l(c.map.zoom),f=l(!1),m=l(null),p=l(U()),g=l(),V=l(),S=_(()=>{const s=[],e=[];return r.value.forEach(a=>{a.grades.forEach(o=>{e.indexOf(o.id)===-1&&(e.push(o.id),s.push({key:o.id,label:o.name}))})}),s.sort((a,o)=>a.label>o.label?1:-1)}),C=_(()=>b.value.filter(s=>s.id).filter(s=>{const e=y.value.filter(a=>Number(a.school)===Number(s.id));return g.value?e.find(a=>Number(a.grade)===Number(g.value)):!0}).filter(s=>s.lat&&s.lng).map(s=>({...s,title:s.name,position:new google.maps.LatLng(s.lat,s.lng)})));(()=>{u(()=>W()).then(s=>{i(()=>H({processes:s.map(e=>e.id)})).then(e=>{y.value=e.vacancies,b.value=e.schools})})})();const B=()=>{var e;if(!m.value)return;const s=[m.value,c.entity.city,c.entity.state].filter(a=>a).join(", ");f.value=!0,(e=V.value)==null||e.geocode({address:s},(a,o)=>{o==="OK"&&a&&(x.value=a[0].geometry.location.lat(),w.value=a[0].geometry.location.lng(),p.value.position=a[0].geometry.location)}).finally(()=>{f.value=!1})};return I(()=>{V.value=new google.maps.Geocoder}),(s,e)=>($(),O("div",null,[t("div",J,[n(M)]),t("div",Q,[t("div",R,[e[2]||(e[2]=t("h2",{class:"title-find-school"},"Consultar escola",-1)),n(L,{flat:"",class:"bg-primary mt-4"},{default:d(()=>[n(z,null,{default:d(()=>[t("form",{onSubmit:P(B,["prevent"])},[n(k,{modelValue:m.value,"onUpdate:modelValue":e[0]||(e[0]=a=>m.value=a),label:"Endere\xE7o","label-class":"form-label-bg-blue",name:"address",type:"TEXT",placeholder:"Digite o seu endere\xE7o (rua e n\xFAmero)","container-class":"w-100"},null,8,["modelValue"]),t("div",Z,[t("div",ee,[n(k,{id:"grade",modelValue:g.value,"onUpdate:modelValue":e[1]||(e[1]=a=>g.value=a),label:"S\xE9rie","label-class":"form-label-bg-blue",name:"grade",type:"SELECT",options:S.value,placeholder:"Selecione a s\xE9rie","container-class":"mr-3 w-100",searchable:""},null,8,["modelValue","options"]),t("div",ae,[n(F,{"data-test":"btn-search-school",loading:f.value,type:"submit",icon:"fa-search",class:"w-100 bg-primary-light text-primary flex-row",style:{height:"45px"},"loading-normal":""},null,8,["loading"])])])])],32)]),_:1})]),_:1})])]),t("div",se,[t("div",te,[t("div",oe,[n(G,{config:A.value,lat:x.value,lng:w.value,zoom:N.value,style:{height:"400px"}},{default:d(({map:a})=>[p.value.position?($(),Y(T,{key:0,map:a,marker:p.value},{default:d(()=>[t("strong",null,h(p.value.title),1)]),_:2},1032,["map","marker"])):K("",!0),n(j,{markers:C.value,map:a},{default:d(({marker:o})=>[t("div",null,[e[3]||(e[3]=t("div",{class:"col-12 small text-uppercase"},"Escola",-1)),t("div",le,h(o.name),1),e[4]||(e[4]=t("div",{class:"col-12 small text-uppercase mt-2"},"Telefone",-1)),t("div",ne,h(`(${o.area_code}) ${o.phone}`),1)])]),_:2},1032,["markers","map"])]),_:1},8,["config","lat","lng","zoom"])])])])]))}});export{de as default};
