<script setup lang="ts">
import DataTable from 'primevue/datatable';

defineOptions({
  inheritAttrs: false,
});

defineProps<{
  value?: Record<string, unknown>[];
  loading?: boolean;
}>();
</script>

<template>
  <DataTable
    :value="value"
    :paginator="true"
    :rows="10"
    :loading="loading"
    dataKey="id"
    emptyMessage="No records found."
    v-bind="$attrs"
  >
    <slot></slot>

    <template #header v-if="$slots.header">
      <slot name="header"></slot>
    </template>

    <template #empty v-if="$slots.empty">
      <slot name="empty"></slot>
    </template>

    <template #footer v-if="$slots.footer">
      <slot name="footer"></slot>
    </template>
  </DataTable>
</template>
