import{h as s,u as n}from"./common.wmfdqs9ndo.js";import{d as c,u as i,r as u,o as d,c as l,a as m}from"./vendor.wmfdqs9ndo.js";const g=()=>s.get("/auth/login",{withCredentials:!0}).then(e=>e.data),h=["innerHTML"],_=c({__name:"Login",setup(e){const a=i(),o=u("Logando.."),{loader:t}=n();return t(g).then(r=>{if(!r)throw new Error;a.push({name:"preregistrations"})}).catch(()=>{o.value=`
      O login de acesso no <strong>Pr\xE9-matr\xEDcula Digital</strong> \xE9 exclusivo para secret\xE1rios e diretores escolares,
      ou gestores da Secretaria de Educa\xE7\xE3o.
      <br>
      No momento, para acessar o sistema voc\xEA deve estar logado no i-Educar.
    `}),(r,p)=>(d(),l("main",null,[m("div",{class:"text-center",innerHTML:o.value},null,8,h)]))}});export{_ as default};
