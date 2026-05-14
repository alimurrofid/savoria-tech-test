<script setup lang="ts">
import ProgressSpinner from 'primevue/progressspinner';

defineProps<{
  visible: boolean;
}>();
</script>

<template>
  <div v-if="visible" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <ProgressSpinner />
  </div>
</template>
