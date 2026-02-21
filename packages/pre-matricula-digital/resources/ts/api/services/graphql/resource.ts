import { Indexable } from '@/types';
import { graphql } from '@/api';

export const load = (data: {
  params: Indexable;
  query: Indexable;
}) => {
  const payload = {
    variables: data.params,
    query: data.query,
  };

  return graphql(payload);
};
