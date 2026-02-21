export default {
  processes: {
    all: `
      query processes(
        $first: Int
        $page: Int
        $search: String
      ) {
        resources: processes(
          first: $first
          page: $page
          search: $search
        ) {
          paginatorInfo {
            count
            currentPage
            lastPage
            perPage
            total
          }
          data {
            id
            name
            steps:stages {
              id
              type:process_stage_type_id
              name
              startAt
              endAt
            }
          }
        }
      }
    `,
    browse: `
      query process(
        $id: ID!
      ) {
        resource: process(
          id: $id
        ) {
          id
          name
          type
          group
          options {
            id
            name
          }
        }
      }
    `,
    create: `
      mutation createProcess(
        $type: ProcessStageType!
        $group: GroupType!
        $name: String!
        $options: [ProcessOptionInput!]
      ) {
        resource: createProcess(
          input: {
            type: $type
            group: $group
            name: $name
            options: $options
          }
        ) {
          id
          name
        }
      }
    `,
    update: `
      mutation updateProcess(
        $id: ID!
        $type: ProcessStageType
        $group: GroupType
        $name: String
        $options: [ProcessOptionInput!]
        $deleteOptions: [ID!]
      ) {
        resource: updateProcess(
          input: {
            id: $id
            type: $type
            group: $group
            name: $name
            options: $options
            deleteOptions: $deleteOptions
          }
        ) {
          id
          name
        }
      }
    `,
    delete: `
      mutation deleteProcess(
        $id: ID!
      ) {
        resource: deleteProcess(
          id: $id
        ) {
          id
          name
        }
      }
    `,
  },
};
