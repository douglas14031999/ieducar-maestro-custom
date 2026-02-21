import{g as o}from"../../common.wmfdqs9ndo.js";const i=()=>o({query:`
      query{
        notices {
          data {
            id
            text
          }
        }
      }
    `}).then(e=>e.data.data.notices.data[0]),n=t=>o({query:`
      mutation save(
        $input: NoticeInput!
      ) {
        notice: saveNotice(
          input: $input
        ) {
          id
        }
      }
    `,variables:{input:t}}).then(a=>({errors:Boolean(a.data.errors),id:a.data.data.notice.id})),d=t=>o({query:`
      mutation delete(
        $id: ID!
      ) {
        notice: deleteNotice(
          id: $id
        ) {
          id
        }
      }
    `,variables:t}).then(a=>({errors:Boolean(a.data.errors)}));export{i as l,n as p,d as r};
