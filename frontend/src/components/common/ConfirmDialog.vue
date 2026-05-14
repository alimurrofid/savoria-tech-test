<script setup lang="ts">
import ConfirmDialog from 'primevue/confirmdialog';

defineOptions({
  inheritAttrs: false
});
</script>

<template>
  <ConfirmDialog v-bind="$attrs">
    <template v-for="(_, slotName) in $slots" #[slotName]="slotProps">
      <slot :name="slotName" v-bind="slotProps || {}" />
    </template>
  </ConfirmDialog>
</template>
