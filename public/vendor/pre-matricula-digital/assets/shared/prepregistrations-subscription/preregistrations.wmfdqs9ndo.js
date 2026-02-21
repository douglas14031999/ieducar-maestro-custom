import{g as r,ar as n}from"../../common.wmfdqs9ndo.js";const l=e=>r({variables:e,query:`
      query process(
        $id: ID! 
        $code: String
        $load: Boolean = false
      ) {
        stage: processByStage(id: $id) {
          id
          renewalAtSameSchool
          allowWaitingList
          allowSearch
          radius
          type
          status
          restrictionType
          process {
            id
            name
            messageFooter
            gradeAgeRangeLink
            forceSuggestedGrade
            showPriorityProtocol
            allowResponsibleSelectMapAddress
            blockIncompatibleAgeGroup
            autoRejectByDays
            autoRejectDays
            onePerYear
            waitingListLimit
            minimumAge
            schoolYear {
              year
            }
            grades {
              id
              name
              startBirth
              endBirth
            }
            periods {
              id
              name
            }
            schools {
              id
              name
              latitude
              longitude
            }
            fields {
              id
              order
              field {
                id
                name
                description
                group
                type
                internal
                options {
                  id
                  key: id
                  name
                  label: name
                }
              }
              required
              weight
            }
            vacancies {
              school
              period
              grade
              total
              available
            }
          }
        }
        preregistration: preregistrationByCode(code: $code) @include(if: $load) {
          id
          protocol
          student {
            student_name: name
            student_date_of_birth: dateOfBirth
            student_cpf: cpf
            student_rg: rg
            student_marital_status: maritalStatus
            student_birth_certificate: birthCertificate
            student_gender: gender
            student_email: email
            student_phone: phone
            student_mobile: mobile
          }
          responsible {
            responsible_name: name
            responsible_date_of_birth: dateOfBirth
            responsible_cpf: cpf
            responsible_rg: rg
            responsible_marital_status: maritalStatus
            responsible_gender: gender
            responsible_email: email
            responsible_phone: phone
            responsible_mobile: mobile
          }
        }
      }
    `}).then(t=>t.data.data),p=async e=>{const a={variables:{stage:e.stage.id,cpf:e.student.student_cpf,rg:e.student.student_rg,birthCertificate:e.student.student_birth_certificate,name:e.student.student_name,dateOfBirth:e.student.student_date_of_birth},query:`
      query matches(
        $stage: ID!
        $name: String
        $dateOfBirth: Date
        $cpf: String
        $rg: String
        $birthCertificate: String
      ) {
        matches: getStudentMatches(
          stage: $stage
          name: $name
          dateOfBirth: $dateOfBirth
          cpf: $cpf
          rg: $rg
          birthCertificate: $birthCertificate
        ) {
          id
          initials
          dateOfBirth
          type
          registration {
            year
            school {
              id
              name
            }
            grade {
              id
              name
            }
          }
        }
      }
    `};return(await r(a)).data.data.matches},c=e=>r({variables:{input:e},query:`
      mutation NewPreRegistration(
        $input: PreRegistrationInput!
      ) {
        preregistrations: newPreRegistration(
         input: $input
        ) {
          id
          protocol
          code
          type
          date
          position
          school {
            id
            name
            area_code
            phone
          }
          period {
            id
            name
          }
          grade {
            id
            name
          }
        }
      }
    `}).then(t=>({errors:t.data.errors,preregistrations:t.data.data.preregistrations})),u=e=>r({variables:e,query:`
      mutation acceptPreRegistrations(
        $ids: [ID!]!,
        $classroom: ID!
      ) {
        acceptPreRegistrations(ids: $ids, classroom: $classroom) {
          id
          student {
            name
          }
        }
      }
    `}).then(t=>({errors:t.data.errors,acceptPreRegistrations:t.data.data.acceptPreRegistrations})),m=e=>r({variables:e,query:`
      mutation rejectPreRegistrations(
        $ids: [ID!]!
        $justification: String
      ) {
        rejectPreRegistrations(
          ids: $ids
          justification: $justification
        ) {
          id
        }
      }
    `}).then(t=>({errors:t.data.errors,rejectPreRegistrations:t.data.data.rejectPreRegistrations})),g=e=>r({variables:e,query:`
      mutation summonPreRegistrations(
        $ids: [ID!]!
        $justification: String
      ) {
        summonPreRegistrations(
          ids: $ids
          justification: $justification
        ) {
          id
        }
      }
    `}).then(t=>({errors:t.data.errors,summonPreRegistrations:t.data.data.summonPreRegistrations})),y=e=>r({variables:e,query:`
      query classroomsByPreregistration(
        $school: ID!
        $grade: ID!
        $year: Int!
      ) {
        classrooms: classroomsByPreregistration(
          school: $school
          grade: $grade
          year: $year
          first: 100
        ) {
          data {
            key:id
            label:name
            period {
              name
            }
            available
          }
        }
      }
    `}).then(t=>t.data.data.classrooms.data.map(o=>({key:o.key,label:`${o.label}/${o.period.name} (vagas: ${o.available})`}))),h=e=>r({variables:e,query:`
      mutation sendProtocolsByEmail(
        $preregistrations: [ID!]!
        $email: String!
      ) {
        success: sendProtocolsByEmail(
          preregistrations: $preregistrations
          email: $email
        )
      }
    `}).then(t=>t.data.data.success),$=e=>r({variables:e,query:`
      query preregistrations(
        $first: Int
        $page: Int
        $search: String
        $process: ID
        $processes: [ID!]
        $school: ID
        $schools: [ID!]
        $grade: ID
        $grades: [ID]
        $period: ID
        $type: PreRegistrationType
        $status: PreRegistrationStatus
        $sort: PreRegistrationSort
        $year: Int
      ) {
        processes(
          year: $year
        ) {
          data {
            id
            vacancies: totalVacancies
            total: totalPreRegistrations
            accepted: totalAcceptedPreRegistrations
            rejected: totalRejectedPreRegistrations
            waiting: totalWaitingPreRegistrations
          }
        }
        preregistrations(
          first: $first
          page: $page
          search: $search
          process: $process
          processes: $processes
          school: $school
          schools: $schools
          grade: $grade
          grades: $grades
          period: $period
          type: $type
          status: $status
          sort: $sort
          year: $year
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
            protocol
            status
            student {
              name
              initials
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
            position
            waiting {
              id
              type
              status
              position
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
              position
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
    `}).then(t=>{const o=t.data.data.processes.data.reduce((s,i)=>({vacancies:s.vacancies+i.vacancies,total:s.total+i.total,accepted:s.accepted+i.accepted,rejected:s.rejected+i.rejected,waiting:s.waiting+i.waiting}),{vacancies:0,total:0,accepted:0,rejected:0,waiting:0});return{errors:Boolean(t.data.errors),stats:o,preregistrations:t.data.data.preregistrations.data,paginator:t.data.data.preregistrations.paginatorInfo}}),b=e=>r({variables:e,query:`
      query all(
        $schools: [ID!]
      ) {
        grades(first: 100) {
          data {
            id
            name
            key: id
            label: name
          }
        }
        periods(first: 100) {
          data {
            id
            name
          }
        }
        schools(
          first: 200
          schools: $schools
        ) {
          data {
            id
            name
            key: id
            label: name
          }
        }
        processes(first: 100) {
          data {
            id
            name
            key: id
            label: name
            totalPreRegistrations
            schoolYear {
              year
            }
          }
        }
      }
    `}).then(t=>({grades:t.data.data.grades.data,periods:t.data.data.periods.data,processes:t.data.data.processes.data,schools:t.data.data.schools.data})),f=e=>r({variables:e,query:`
      query process($id: ID!) {
        process(id: $id) {
          id
          name
          key: id
          label: name
          totalPreRegistrations
          schoolYear {
            year
          }
          grades {
            id
            name
            key: id
            label: name
          }
          periods {
            id
            name
          }
          schools {
            id
            name
            key: id
            label: name
          }
          fields {
            id
            order
            field {
              id
              name
              group
              type
              options {
                id
                name
                weight
              }
            }
            required
            weight
          }
          criteria
        }
      }
    `}).then(t=>t.data.data.process),_=e=>r({variables:e,query:`
      mutation acceptPreRegistrations($ids: [ID!]!, $classroom: ID!) {
        acceptPreRegistrations(ids: $ids, classroom: $classroom) {
          id
        }
      }
    `}).then(t=>t.data.errors),P=e=>r({variables:e,query:`
      mutation rejectPreRegistrations(
        $ids: [ID!]!
        $justification: String
      ) {
        rejectPreRegistrations(
          ids: $ids
          justification: $justification
        ) {
          id
        }
      }
    `}),I=e=>r({variables:e,query:`
      mutation returnToWaitPreRegistrations(
        $ids: [ID!]!
      ) {
        returnToWaitPreRegistrations(
          ids: $ids
        ) {
          id
        }
      }
    `}),R=e=>r({variables:e,query:`
      mutation summonPreRegistrations(
        $ids: [ID!]!
        $justification: String
      ) {
        summonPreRegistrations(
          ids: $ids
          justification: $justification
        ) {
          id
        }
      }
    `}).then(t=>t.data.errors),B=e=>r({variables:e,query:`
      query preregistrationByProtocol(
          $protocol: String! 
          $withVacancy: Boolean = false
      ) {
          preregistration: preregistrationByProtocol(
              protocol: $protocol
              withVacancy: $withVacancy
          ) {
          id
          date
          protocol
          status
          type
          observation
          grade {
            id
            name
            course {
              name
            }
          }
          period {
            id
            name
          }
          school {
            id
            name
          }
          classroom {
            id
            name
          }
          position
          waiting {
            id
            protocol
            school {
              id
              name
            }
            position
          }
          parent {
            id
            protocol
            school {
              id
              name
            }
            position
          }
          others {
            id
            protocol
            status
            school {
              id
              name
            }
            position
          }
          student {
            student_name: name
            student_date_of_birth: dateOfBirth
            student_cpf: cpf
            student_rg: rg
            student_marital_status: maritalStatus
            student_place_of_birth: placeOfBirth
            student_city_of_birth: cityOfBirth
            student_birth_certificate: birthCertificate
            student_gender: gender
            student_email: email
            student_phone: phone
            student_mobile: mobile
          }
          responsible {
            responsible_name: name
            responsible_date_of_birth: dateOfBirth
            responsible_cpf: cpf
            responsible_rg: rg
            responsible_marital_status: maritalStatus
            responsible_place_of_birth: placeOfBirth
            responsible_city_of_birth: cityOfBirth
            responsible_gender: gender
            responsible_email: email
            responsible_phone: phone
            responsible_mobile: mobile
            responsible_address: addresses {
              postalCode
              address
              number
              complement
              neighborhood
              city
              manualChangeLocation
              lat
              lng
            }
          }
          relationType
          fields {
            id
            value
            field {
              id
              name
              internal
              group
            }
          }
          process {
            id
            name
            fields {
              id
              order
              field {
                id
                name
                group
                type
                internal
                options {
                  id
                  key: id
                  name
                  label: name
                }
              }
              required
              weight
            }
            schoolYear {
              year
            }
            ...ProcessInfo @include(if: $withVacancy)
          }
          inClassroom {
            id
            name
            period {
              name
            }
          }
          external {
            id
            name
            gender
            dateOfBirth
            cpf
            rg
            birthCertificate
            phone
            mobile
            email
            address
            number
            complement
            neighborhood
            postalCode
          }
        }
      }
      
      fragment ProcessInfo on Process {
        grades {
            id
            name
            startBirth
            endBirth
        }
        periods {
            id
            name
        }
        schools {
            id
            name
            lat: latitude
            lng: longitude
        }
        vacancies {
            school
            period
            grade
            total
            available
        }
      }
    `}).then(t=>t.data.data.preregistration),S=e=>r({variables:e,query:`
      mutation updateStudentInExternalSystem(
        $preregistration: ID!
        $cpf: Boolean
        $rg: Boolean
        $birthCertificate: Boolean
        $name: Boolean
        $dateOfBirth: Boolean
        $gender: Boolean
        $phone: Boolean
        $mobile: Boolean
        $address: Boolean
      ) {
        updateStudentInExternalSystem(
          preregistration: $preregistration
          cpf: $cpf
          rg: $rg
          birthCertificate: $birthCertificate
          name: $name
          dateOfBirth: $dateOfBirth
          gender: $gender
          phone: $phone
          mobile: $mobile
          address: $address
        )
      }
    `}).then(t=>t.data.data.updateStudentInExternalSystem),q=e=>r({variables:e,query:`
      query summonNextInLine(
        $process: ID!
        $school: ID!
        $grade: ID!
        $period: ID!
      ) {
        summonNextInLine(
          process: $process
          school: $school
          grade: $grade
          period: $period
        ) {
          id
          protocol
        }
      }
    `}).then(t=>({nextInLine:t.data.data.summonNextInLine})),D=e=>r({variables:e,query:`
      mutation updateAddress(
        $protocol: String!
        $address: AddressInput!
      ) {
        updateAddress(
          protocol: $protocol
          address: $address
        ) {
          id
          protocol
        }
      }
    `}).then(t=>({updateAddress:t.data.data.updateAddress})),v=e=>r({variables:e,query:`
      mutation updateResponsible(
        $protocol: String!
        $fields: [PreRegistrationFieldInput!]!
      ) {
        updateResponsible(
          protocol: $protocol
          fields: $fields
        ) {
          id
          protocol
        }
      }
    `}).then(t=>({updateResponsible:t.data.data.updateResponsible})),j=e=>r({variables:e,query:`
      mutation updateStudent(
        $protocol: String!
        $fields: [PreRegistrationFieldInput!]!
      ) {
        updateStudent(
          protocol: $protocol
          fields: $fields
        ) {
          id
          protocol
        }
      }
    `}).then(t=>({updateStudent:t.data.data.updateStudent})),w=e=>r({variables:e,query:`
      mutation updatePreRegistration(
          $protocol: String!
          $grade: ID!
          $school: ID!
          $period: ID!
      ) {
          updatePreRegistration(
              protocol: $protocol
              grade: $grade
              school: $school
              period: $period
          ) {
              protocol
              grade {
                  id
              }
              school {
                  id
              }
              period {
                  id
              }
          }
      }
    `}).then(t=>({updatePreRegistration:t.data.data.updatePreRegistration})),A=(e,a)=>r({variables:{modelId:e,modelType:a},query:`
      query getTimelines($modelId: ID!, $modelType: String!) {
        getTimelines(modelId: $modelId, modelType: $modelType) {
          id
          modelId
          modelType
          type
          payload
          createdAt
        }
      }
    `}).then(o=>({listTimeline:o.data.data.getTimelines.map(s=>({...s,createdAt:new Date(s.createdAt)}))})),T=e=>n("get","/pre-matricula-export",{params:e,responseType:"blob"}).then(a=>a.data),O=e=>n("get","/pre-matricula-report",{params:e,responseType:"blob"}).then(a=>a.data),C=e=>n("get","/pre-matricula-report",{params:e,responseType:"blob"}).then(a=>a.data);export{p as a,c as b,f as c,y as d,u as e,m as f,$ as g,g as h,q as i,b as j,T as k,l,_ as m,P as n,I as o,h as p,B as q,R as r,C as s,O as t,S as u,A as v,w,j as x,v as y,D as z};
