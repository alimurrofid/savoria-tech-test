<script setup lang="ts">
import Button, { type ButtonProps } from 'primevue/button';

interface Props extends /* @vue-ignore */ ButtonProps {
  label?: string;
  icon?: string;
}

defineProps<Props>();
</script>

<template>
  <Button v-bind="$props" :label="label" :icon="icon">
    <template v-for="(_, slotName) in $slots" v-slot:[slotName]="slotProps">
      <slot :name="slotName" v-bind="slotProps || {}"></slot>
    </template>
  </Button>
</template>
