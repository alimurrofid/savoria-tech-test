<script setup lang="ts">
import Dialog from 'primevue/dialog';

defineOptions({
  inheritAttrs: false,
});

defineProps<{
  visible: boolean;
}>();

const emit = defineEmits(['update:visible']);
</script>

<template>
  <Dialog
    :visible="visible"
    @update:visible="emit('update:visible', $event)"
    modal
    class="w-full max-w-lg md:w-[32rem]"
    v-bind="$attrs"
  >
    <template #header v-if="$slots.header">
      <slot name="header"></slot>
    </template>

    <div class="py-4">
      <slot></slot>
    </div>

    <template #footer v-if="$slots.footer">
      <slot name="footer"></slot>
    </template>
  </Dialog>
</template>
