import{g as A,b as E,a as D,k as M,s as B,X as k,_ as F,q as L,Y as T,W as j,V as G,a0 as X}from"./common.wmfdqs9ndo.js";import{d as I,r as l,g as _,z as O,o as $,c as P,a as t,p as n,s as d,R as U,m as Y,t as h,j as K}from"./vendor.wmfdqs9ndo.js";const R=()=>A({query:`
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
    `}).then(u=>u.data.data.processes.data.filter(r=>r.stages.length)),W=v=>A({variables:v,query:`
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
    `}).then(r=>({vacancies:r.data.data.vacancies,schools:r.data.data.schools.data.map(i=>({...i,position:{lat:i.lat,lng:i.lng}}))})),H={class:"row mt-3"},J={class:"row"},Q={class:"col-12"},Z={class:"mt-2"},ee={class:"d-flex justify-content-between"},ae={class:"d-flex align-items-center mt-3"},se={class:"row"},te={class:"col-12 mt-5"},oe={class:"card"},le={class:"col-12"},ne={class:"col-12"},de=I({__name:"SchoolFind",setup(v){const{loader:u,data:r}=E([]),{loader:i}=E(),y=l([]),b=l([]),c=D(),N=_(()=>c.map.config),x=l(c.map.center.lat),w=l(c.map.center.lng),S=l(c.map.zoom),f=l(!1),m=l(null),p=l(X()),g=l(),V=l(),q=_(()=>{const s=[],e=[];return r.value.forEach(a=>{a.grades.forEach(o=>{e.indexOf(o.id)===-1&&(e.push(o.id),s.push({key:o.id,label:o.name}))})}),s.sort((a,o)=>a.label>o.label?1:-1)}),C=_(()=>b.value.filter(s=>s.id).filter(s=>{const e=y.value.filter(a=>Number(a.school)===Number(s.id));return g.value?e.find(a=>Number(a.grade)===Number(g.value)):!0}).filter(s=>s.lat&&s.lng).map(s=>({...s,title:s.name,position:new google.maps.LatLng(s.lat,s.lng)})));(()=>{u(()=>R()).then(s=>{i(()=>W({processes:s.map(e=>e.id)})).then(e=>{y.value=e.vacancies,b.value=e.schools})})})();const z=()=>{var e;if(!m.value)return;const s=[m.value,c.entity.city,c.entity.state].filter(a=>a).join(", ");f.value=!0,(e=V.value)==null||e.geocode({address:s},(a,o)=>{o==="OK"&&a&&(x.value=a[0].geometry.location.lat(),w.value=a[0].geometry.location.lng(),p.value.position=a[0].geometry.location)}).finally(()=>{f.value=!1})};return O(()=>{V.value=new google.maps.Geocoder}),(s,e)=>($(),P("div",null,[t("div",H,[n(M)]),t("div",J,[t("div",Q,[e[2]||(e[2]=t("h2",{class:"title-find-school"},"Consultar escola",-1)),n(L,{flat:"",class:"bg-primary mt-4"},{default:d(()=>[n(B,null,{default:d(()=>[t("form",{onSubmit:U(z,["prevent"])},[n(k,{modelValue:m.value,"onUpdate:modelValue":e[0]||(e[0]=a=>m.value=a),label:"Endere\xE7o","label-class":"form-label-bg-blue",name:"address",type:"TEXT",placeholder:"Digite o seu endere\xE7o (rua e n\xFAmero)","container-class":"w-100"},null,8,["modelValue"]),t("div",Z,[t("div",ee,[n(k,{id:"grade",modelValue:g.value,"onUpdate:modelValue":e[1]||(e[1]=a=>g.value=a),label:"S\xE9rie","label-class":"form-label-bg-blue",name:"grade",type:"SELECT",options:q.value,placeholder:"Selecione a s\xE9rie","container-class":"mr-3 w-100",searchable:""},null,8,["modelValue","options"]),t("div",ae,[n(F,{"data-test":"btn-search-school",loading:f.value,type:"submit",icon:"fa-search",class:"w-100 bg-primary-light text-primary flex-row",style:{height:"45px"},"loading-normal":""},null,8,["loading"])])])])],32)]),_:1})]),_:1})])]),t("div",se,[t("div",te,[t("div",oe,[n(G,{config:N.value,lat:x.value,lng:w.value,zoom:S.value,style:{height:"400px"}},{default:d(({map:a})=>[p.value.position?($(),Y(T,{key:0,map:a,marker:p.value},{default:d(()=>[t("strong",null,h(p.value.title),1)]),_:2},1032,["map","marker"])):K("",!0),n(j,{markers:C.value,map:a},{default:d(({marker:o})=>[t("div",null,[e[3]||(e[3]=t("div",{class:"col-12 small text-uppercase"},"Escola",-1)),t("div",le,h(o.name),1),e[4]||(e[4]=t("div",{class:"col-12 small text-uppercase mt-2"},"Telefone",-1)),t("div",ne,h(`(${o.area_code}) ${o.phone}`),1)])]),_:2},1032,["markers","map"])]),_:1},8,["config","lat","lng","zoom"])])])])]))}});export{de as default};
