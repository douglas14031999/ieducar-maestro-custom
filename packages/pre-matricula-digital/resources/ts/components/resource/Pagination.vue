<template>
  <nav class="paginator">
    <ul v-if="paginatorInfo" class="d-flex list-unstyled">
      <li
        :class="{ disabled: pagination.current === pagination.firstPage }"
        class="page-item"
      >
        <a
          class="btn btn-outline-primary"
          href="javascript:void(0)"
          @click.prevent="$emit('page', pagination.firstPage)"
          >«</a
        >
      </li>
      <li
        :class="{ disabled: pagination.current === pagination.prev }"
        class="page-item"
      >
        <a
          class="btn btn-outline-primary"
          href="javascript:void(0)"
          @click.prevent="$emit('page', pagination.prev)"
          >‹</a
        >
      </li>
      <li v-for="page in pagination.pages" :key="page" class="page-item">
        <a
          :class="{ active: pagination.current === page }"
          class="btn btn-outline-primary"
          href="javascript:void(0)"
          @click.prevent="$emit('page', page)"
          >{{ page }}</a
        >
      </li>
      <li
        :class="{ disabled: pagination.current === pagination.next }"
        class="page-item"
      >
        <a
          class="btn btn-outline-primary"
          href="javascript:void(0)"
          @click.prevent="$emit('page', pagination.next)"
          >›</a
        >
      </li>
      <li
        :class="{ disabled: pagination.current === pagination.lastPage }"
        class="page-item"
      >
        <a
          class="btn btn-outline-primary"
          href="javascript:void(0)"
          @click.prevent="$emit('page', pagination.lastPage)"
          >»</a
        >
      </li>
    </ul>
  </nav>
</template>

<script setup lang="ts">
import { Nullable, PaginatorInfo } from '@/types';
import { computed } from 'vue';

defineEmits<{
  (action: 'page', payload: Nullable<number>): void;
}>();

const props = defineProps<{
  paginatorInfo?: PaginatorInfo;
}>();

const pagination = computed(() => {
  const pagination: {
    firstPage: number;
    lastPage: number;
    current: number;
    first: Nullable<number>;
    last: Nullable<number>;
    prev: Nullable<number>;
    next: Nullable<number>;
    pages: number[];
  } = {
    firstPage: 1,
    lastPage: props.paginatorInfo?.lastPage || 1,
    current: props.paginatorInfo?.currentPage || 1,
    first: null,
    last: null,
    prev: null,
    next: null,
    pages: [],
  };

  pagination.first = pagination.current - 3;
  pagination.last = pagination.current + 3;
  pagination.prev = pagination.current - 1;
  pagination.next = pagination.current + 1;

  if (pagination.first < pagination.firstPage) {
    pagination.first = pagination.firstPage;
  }

  if (pagination.last > pagination.lastPage) {
    pagination.last = pagination.lastPage;
  }

  if (pagination.prev < pagination.firstPage) {
    pagination.prev = pagination.firstPage;
  }

  if (pagination.next > pagination.lastPage) {
    pagination.next = pagination.lastPage;
  }

  for (let i = pagination.first; i <= pagination.last; i += 1) {
    pagination.pages.push(i);
  }

  return pagination;
});
</script>
