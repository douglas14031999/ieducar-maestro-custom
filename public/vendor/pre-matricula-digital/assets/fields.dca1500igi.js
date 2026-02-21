import{g as O,b as ee,d as oe,_ as $,X as B,e as te,f as ae,i as le,j as ne}from"./common.dca1500igi.js";import{d as se,r as p,M as ie,j as k,B as re,k as de,o as l,c as n,a as t,C as w,q as X,l as h,F as x,p as C,v as A,t as M,s as u,x as R,w as ce}from"./vendor.dca1500igi.js";const T=()=>O({query:`
      query fields {
        fields(first: 100) {
          data {
            id
            name
            description
            internal
            required
            group
            type
            options {
              id
              name
              weight
            }
          }
        }
      }
    `}).then(i=>i.data.data.fields.data),pe=y=>O({variables:y,query:`
      mutation createField(
        $type: FieldType!
        $group: GroupType!
        $name: String!
        $description: String
        $options: [FieldOptionInput!]
      ) {
        resource: createField(
          input: {
            type: $type
            group: $group
            name: $name
            description: $description
            options: $options
          }
        ) {
          id
          name
        }
      }
    `}).then(m=>m.data),ue=y=>O({variables:y,query:`
      mutation updateField(
        $id: ID!
        $type: FieldType
        $group: GroupType
        $name: String
        $description: String
        $options: [FieldOptionInput!]
        $deleteOptions: [ID!]
      ) {
        resource: updateField(
          input: {
            id: $id
            type: $type
            group: $group
            name: $name
            description: $description
            options: $options
            deleteOptions: $deleteOptions
          }
        ) {
          id
          name
        }
      }
    `}).then(m=>m.data),me={key:1,class:"row mt-5"},ve={class:"col-12 col-md-6"},ge={class:"row"},ye=["onClick"],fe={class:"toggle-checkbox"},be=["value"],he={key:0,class:"col-12 mb-3"},_e={class:"col-12"},$e={class:"col-12 col-md-6 mt-5 mt-md-0"},Ee={class:"row"},ke=["onClick"],we={class:"toggle-checkbox"},xe=["value"],Ce={class:"col-12"},Te={key:0,class:"col-12"},Oe={class:"w-100 mr-2"},Se={class:"mr-2"},qe={class:"d-flex align-items-start"},Fe=se({__name:"FieldsPage",setup(y){var U;const{loader:i,data:m,loading:S}=ee([]),E=p(),j=(U=ie())==null?void 0:U.appContext,{loader:q,loading:V}=oe(j,E),f=p(),D=p({name:null,weight:0}),F=p({id:null,name:null,description:null,group:null,type:null,internal:!1,options:[],deleteOptions:[]}),v=p({id:null,name:null,description:null,group:null,type:null,internal:!1,options:[],deleteOptions:[]}),r=p(!1),z=p(le()),I=k(()=>m.value.filter(e=>e.group==="RESPONSIBLE")),P=k(()=>m.value.filter(e=>e.group==="STUDENT")),G=k(()=>({fields:[{label:"T\xEDtulo da pergunta",name:"name",type:"TEXT",containerClass:"col-12",rules:"required"},{label:"Descri\xE7\xE3o da pergunta",name:"description",type:"TEXT",containerClass:"col-12",rules:""},{label:"Tipo da resposta",name:"type",type:"SELECT",options:z.value,containerClass:"col-12",disabled:v.value.id?!0:void 0,rules:"required",searchable:!1}],buttons:[{type:"submit",label:"Salvar",class:"btn btn-block btn-primary",containerClass:"",block:!0,bind:{"data-test":"btn-save-field"}}],buttonsContainer:{class:""}})),L=e=>{v.value={...F.value,group:e,options:[],type:"TEXT"},r.value=!0},H=(e,a)=>{const{options:s}=e;let o=[{...D.value}];s&&(s.push({...D.value}),o=s),a({...e,options:o})},N=e=>{v.value={...F.value,...e},r.value=!0},J=e=>{E.value={title:"Erro ao criar o campo",description:"Aconteceu um erro ao criar o campo, por favor tente novamente em alguns instantes."},q(()=>pe(e)).then(()=>{r.value=!1,i(T)})},K=e=>{E.value={title:"Erro ao editar o campo",description:"Aconteceu um erro ao editar o campo, por favor tente novamente em alguns instantes."},q(()=>ue(e)).then(()=>{r.value=!1,i(T)}),V.value=!0},Q=e=>{e.options&&e.options.forEach(a=>{const s=a;s.weight=Number(a.weight)}),e.id?K(e):J(e)},W=(e,a,s)=>{const o=e.options[a],{options:d,deleteOptions:c}=e;d.splice(a,1),o.id&&(c==null||c.push(o.id)),s({...e,options:d,deleteOptions:c})},Y=()=>{var e;v.value.options.length<2&&f.value&&((e=f.value)==null||e.setOverflowVisible(!0))},Z=()=>{var e;f.value&&((e=f.value)==null||e.setOverflowVisible(!1))};return re(()=>i(T)),(e,a)=>{const s=de("tooltip");return l(),n("main",null,[a[7]||(a[7]=t("h1",null,"Campos",-1)),w(S)?(l(),X(ne,{key:0,class:"mt-5",items:20})):h("",!0),w(S)?h("",!0):(l(),n("div",me,[t("div",ve,[t("div",ge,[a[4]||(a[4]=t("div",{class:"col-12"},[t("p",null,"Dados do(a) respons\xE1vel pelo(a) aluno(a)"),t("hr")],-1)),(l(!0),n(x,null,C(I.value,o=>(l(),n("div",{key:o.id,class:"col-12 col-sm-6 col-md-12 col-lg-6 mb-3","data-test":"wrapper-checkbox",onClick:d=>N(o)},[t("label",fe,[t("input",{type:"checkbox",disabled:"",value:o.id},null,8,be),t("div",{class:A([{"toggle-required":o.required},"toggle-text"])},M(o.name),3)])],8,ye))),128)),I.value.length?(l(),n("div",he,a[3]||(a[3]=[t("label",{class:"toggle-checkbox"},[t("input",{type:"checkbox",disabled:""}),t("div",{class:"toggle-text toggle-required"},"Endere\xE7o")],-1)]))):h("",!0),t("div",_e,[u($,{"data-test":"btn-add-field-responsible",label:"Adicionar novo campo","no-caps":"","no-wrap":"",color:"primary",class:"w-100",icon:"mdi-plus",size:"lg",onClick:a[0]||(a[0]=o=>L("RESPONSIBLE"))})])])]),t("div",$e,[t("div",Ee,[a[5]||(a[5]=t("div",{class:"col-12"},[t("p",null,"Dados do(a) aluno(a)"),t("hr")],-1)),(l(!0),n(x,null,C(P.value,o=>(l(),n("div",{key:o.id,class:"col-12 col-sm-6 col-md-12 col-lg-6 mb-3",onClick:d=>N(o)},[t("label",we,[t("input",{type:"checkbox",disabled:"",value:o.id},null,8,xe),t("div",{class:A([{"toggle-required":o.required},"toggle-text"])},M(o.name),3)])],8,ke))),128)),t("div",Ce,[u($,{"data-test":"btn-add-field-student",label:"Adicionar novo campo","no-caps":"","no-wrap":"",color:"primary",class:"w-100",icon:"mdi-plus",size:"lg",onClick:a[1]||(a[1]=o=>L("STUDENT"))})])])])])),u(ae,{ref_key:"fieldsModal",ref:f,modelValue:r.value,"onUpdate:modelValue":a[2]||(a[2]=o=>r.value=o),"no-footer":"",title:(v.value.id?"Editar ":"Novo ")+"Campo"},{body:R(()=>[r.value?(l(),X(te,{key:0,loading:w(V),schema:G.value,"initial-values":{...v.value},onSubmit:Q,"onOpen:options":Y,"onClose:options":Z},{default:R(({values:o,errors:d,setValues:c})=>[o.type==="SELECT"||o.type==="MULTI_SELECT"||o.type==="RADIO"?(l(),n("div",Te,[a[6]||(a[6]=t("h3",null,"Op\xE7\xF5es",-1)),(l(!0),n(x,null,C(o.options,(b,g)=>(l(),n("div",{key:g,class:"d-flex justify-content-between"},[t("div",Oe,[u(B,{modelValue:b.name,"onUpdate:modelValue":_=>b.name=_,name:`options[${g}].name`,"container-class":"form-group",type:"TEXT",rules:"required",errors:!!d[`options[${g}].name`]},null,8,["modelValue","onUpdate:modelValue","name","errors"])]),t("div",Se,[u(B,{modelValue:b.weight,"onUpdate:modelValue":_=>b.weight=_,name:`options[${g}].weight`,"container-class":"form-group",type:"TEXT",rules:"required|numeric",errors:!!d[`options[${g}].weight`]},null,8,["modelValue","onUpdate:modelValue","name","errors"])]),t("div",qe,[ce(u($,{"no-caps":"","no-wrap":"",class:"w-100 h-75 flex-row text-danger border-danger",icon:"mdi-trash-can-outline",onClick:_=>W(o,g,c)},null,8,["onClick"]),[[s,"Remover Op\xE7\xE3o",void 0,{"start-bottom":!0}]])])]))),128)),u($,{"no-caps":"","no-wrap":"",outline:"",color:"primary",class:"w-100 mb-4",label:"Adicionar op\xE7\xE3o",onClick:b=>H(o,c)},null,8,["onClick"])])):h("",!0)]),_:1},8,["loading","schema","initial-values"])):h("",!0)]),_:1},8,["modelValue","title"])])}}});export{Fe as default};
