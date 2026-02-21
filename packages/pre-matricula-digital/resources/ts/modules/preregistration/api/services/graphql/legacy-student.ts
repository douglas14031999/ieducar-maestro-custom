import { graphql } from '@/api';

export const searchLegacyStudentsByName = async (name: string): Promise<{ id: string; name: string; cpf: string; dateOfBirth: string; responsibleName?: string; responsibleCpf?: string; responsibleDateOfBirth?: string; responsiblePhone?: string }[]> => {
  const queryAndVariables = {
    variables: {
      name,
      first: 10
    },
    query: `
      query searchLegacyStudents($name: String!, $first: Int!) {
        legacyStudents: searchLegacyStudentsByName(name: $name, first: $first) {
          id
          name
          cpf
          dateOfBirth
          responsibleName
          responsibleCpf
          responsibleDateOfBirth
          responsiblePhone
        }
      }
    `
  };

  const res = await graphql<{
    data: {
      data: {
        legacyStudents: { id: string; name: string; cpf: string; dateOfBirth: string; responsibleName?: string; responsibleCpf?: string; responsibleDateOfBirth?: string; responsiblePhone?: string }[];
      };
    };
  }>(queryAndVariables);

  if (!res?.data?.data?.legacyStudents) {
    return [];
  }

  return res.data.data.legacyStudents;
};
