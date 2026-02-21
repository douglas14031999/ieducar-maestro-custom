export default {
  preregistrations: {
    all: `
      query preregistrations(
        $first: Int
        $page: Int
      ) {
        resources: preregistrations(
          first: $first
          page: $page
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
            type
            status
            student {
              name
            }
            grade {
              name
            }
            period {
              name
            }
            school {
              name
            }
            schoolYear {
              year
            }
            position {
              position
            }
            waiting {
              id
              type 
              status 
              position {
                position
              }
              student {
                name
              }
              period {
                name
              }
              school {
                name
              }
              schoolYear {
                year
              }
            }
            parent {
              id
              type 
              status
              position {
                position
              }
              student {
                name
              }
              period {
                name
              }
              school {
                name
              }
              schoolYear {
                year
              }
            }
          }
        }
      }
    `,
    browse: `
      query field(
        $id: ID!
      ) {
        resource: field(
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
      mutation createField(
        $type: FieldType!
        $group: GroupType!
        $name: String!
        $options: [FieldOptionInput!]
      ) {
        resource: createField(
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
      mutation updateField(
        $id: ID!
        $type: FieldType
        $group: GroupType
        $name: String
        $options: [FieldOptionInput!]
        $deleteOptions: [ID!]
      ) {
        resource: updateField(
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
      mutation deleteField(
        $id: ID!
      ) {
        resource: deleteField(
          id: $id
        ) {
          id
          name
        }
      }
    `,
  },
};
