<template>
  <div>
    <h2 class="timeline-title">Histórico de Atualizações</h2>
    <div class="timeline-blocks">
      <template v-if="items.length">
        <div
          v-for="(item, index) in items"
          :key="index"
          class="timeline-block altered"
        >
          <div class="block-header">
            <div
              class="block-title"
              v-html="getTitle(item.type, item.payload)"
            ></div>
            <div v-if="item.createdAt" class="block-date">
              {{ formatDate(item.createdAt) }}
            </div>
          </div>
          <div
            v-if="item.payload.before && item.payload.after"
            class="block-changes"
          >
            <ul class="block-diff-list">
              <li v-for="(value, field) in item.payload.before" :key="field">
                <span class="diff-field">{{ field }}:</span>
                <div class="diff-content">
                  <div class="diff-text-row">
                    <div
                      class="diff-text"
                      :class="{
                        'is-expanded': expandedStates[`${index}-${field}`],
                      }"
                      :title="isTextLong(value) ? value : ''"
                    >
                      <span
                        v-if="
                          value !== null && value !== undefined && value !== ''
                        "
                        >{{ value }}</span
                      >
                      <span v-else class="text-muted">Vazio</span>
                    </div>
                    <button
                      v-if="isTextLong(value)"
                      class="expand-button"
                      @click="toggleExpand(`${index}-${field}`)"
                    >
                      <i
                        class="expand-icon"
                        :class="{
                          'is-expanded': expandedStates[`${index}-${field}`],
                        }"
                        >▼</i
                      >
                    </button>
                  </div>
                </div>
                <span class="diff-arrow">→</span>
                <div class="diff-content">
                  <div class="diff-text-row">
                    <div
                      class="diff-text diff-text-after"
                      :class="{
                        'is-expanded':
                          expandedStates[`${index}-${field}-after`],
                      }"
                      :title="
                        isTextLong(item.payload.after[field])
                          ? item.payload.after[field]
                          : ''
                      "
                    >
                      <span
                        v-if="
                          item.payload.after[field] !== null &&
                          item.payload.after[field] !== undefined &&
                          item.payload.after[field] !== ''
                        "
                        >{{ item.payload.after[field] }}</span
                      >
                      <span v-else class="text-muted">Vazio</span>
                    </div>
                    <button
                      v-if="isTextLong(item.payload.after[field])"
                      class="expand-button"
                      @click="toggleExpand(`${index}-${field}-after`)"
                    >
                      <i
                        class="expand-icon"
                        :class="{
                          'is-expanded':
                            expandedStates[`${index}-${field}-after`],
                        }"
                        >▼</i
                      >
                    </button>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </template>
      <template v-else>
        <div class="timeline-empty">Nenhum histórico encontrado.</div>
      </template>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

export interface TimelineItem {
  type: string;
  createdAt?: Date;
  payload: {
    before?: Record<string, string>;
    after?: Record<string, string>;
    [key: string]: unknown;
  };
}

defineProps<{
  items: TimelineItem[];
}>();

const expandedStates = ref<Record<string, boolean>>({});

const getTitle = (type: string, payload: object): string => {
  const userName = `<span class='user-name'>${
    payload?.user?.name || 'Sistema'
  }</span>`;
  switch (type) {
    case 'student-responsible-address-updated':
      return `<strong>${userName}</strong> atualizou o <strong>endereço</strong> do responsável`;
    case 'preregistration-external-system-address-updated':
      return `<strong>${userName}</strong> atualizou o <strong>endereço</strong> no i-Educar`;
    case 'preregistration-external-system-phones-updated':
      return `<strong>${userName}</strong> atualizou os <strong>telefones</strong> no i-Educar`;
    case 'preregistration-external-system-individual-updated':
      return `<strong>${userName}</strong> atualizou o <strong>aluno</strong> no i-Educar`;
    case 'preregistration-external-system-documents-updated':
      return `<strong>${userName}</strong> atualizou os <strong>documentos</strong> no i-Educar`;
    case 'preregistration-external-system-name-updated':
      return `<strong>${userName}</strong> atualizou o <strong>nome</strong> no i-Educar`;
    case 'preregistration-updated':
      return `<strong>${userName}</strong> atualizou a <strong>pré-matrícula</strong>`;
    case 'preregistration-student-responsible-updated':
      return `<strong>${userName}</strong> atualizou o <strong>responsável</strong>`;
    case 'preregistration-status-updated':
      return `<strong>${userName}</strong> atualizou o <strong>status</strong> da pré-matricula`;
    case 'preregistration-student-updated':
      return `<strong>${userName}</strong> atualizou os dados do <strong>aluno</strong>`;
    case 'preregistration-status-auto-rejected':
      return `Foi atualizado o <strong>status</strong> da pré-matricula através do indeferimento automático`;
    default:
      return '';
  }
};

const isTextLong = (text: string | undefined) => {
  if (!text) return false;
  return text.length > 50;
};

const toggleExpand = (key: string) => {
  expandedStates.value[key] = !expandedStates.value[key];
};

function formatDate(date?: Date) {
  if (!date) return '';
  const d = new Date(date);
  const dia = String(d.getDate()).padStart(2, '0');
  const mes = String(d.getMonth() + 1).padStart(2, '0');
  const ano = d.getFullYear();
  const hora = String(d.getHours()).padStart(2, '0');
  const min = String(d.getMinutes()).padStart(2, '0');
  return `${dia}/${mes}/${ano} ${hora}:${min}`;
}
</script>

<style scoped>
.timeline-title {
  font-size: 20px;
  font-weight: 600;
  color: #333;
  margin-bottom: 0;
}

.timeline-blocks {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.timeline-block.altered {
  border-left: 3px solid #007bff;
}

.block-header {
  padding: 6px 4px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  margin-bottom: 0;
  line-height: 14px;
  padding-left: 3px;
}

.block-title {
  font-weight: 500;
  font-size: 16px;
  margin: 0;
  display: inline-block;
  padding-left: 4px;
}

.block-title strong .user-name {
  color: #003473;
}

.user-name {
  color: #003473;
}

.block-date {
  font-size: 12px;
  color: #b0b0b0;
  margin: 0 0 0 auto;
  white-space: nowrap;
}

.block-changes {
  margin: 0;
}

.block-diff-list {
  list-style: none;
  padding: 0;
  margin: 0;
  overflow: hidden;
}

.block-diff-list li {
  display: grid;
  grid-template-columns: 25% 35% 5% 35%;
  align-items: stretch;
  font-size: 14px;
  line-height: 1.2;
  gap: 0;
}

.block-diff-list li:last-child {
}

.diff-field {
  color: #222;
  font-weight: 500;
  font-size: 14px;
  word-break: break-word;
  padding: 4px 6px;
  display: flex;
  align-items: center;
  min-height: 16px;
}

.diff-content {
  display: flex;
  flex-direction: column;
  min-width: 0;
  width: 100%;
  padding: 0 3px;
  background: #fff;
  min-height: 16px;
  justify-content: center;
}

.diff-arrow {
  color: #007bff;
  font-size: 22px;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff;
  height: 100%;
  padding: 0;
  min-height: 16px;
}

.diff-text-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.diff-text {
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #666;
}

.diff-text.is-expanded {
  white-space: normal;
  text-overflow: initial;
}

.expand-button {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 0;
  width: 12px;
  height: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 1px;
}

.expand-icon {
  font-size: 8px;
  transition: transform 0.2s ease;
  display: inline-block;
}

.expand-icon.is-expanded {
  transform: rotate(180deg);
}

.expand-button:hover {
  color: #333;
}

.text-muted {
  font-style: italic;
  color: #999;
}

.timeline-empty {
  text-align: center;
  color: #888;
  padding: 3px 0;
  font-size: 12px;
}

.bold {
  font-weight: bold;
}
</style>
