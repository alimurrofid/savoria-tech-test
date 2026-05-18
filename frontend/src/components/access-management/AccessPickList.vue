<script setup lang="ts">
/**
 * AccessPickList — Dumb component
 *
 * Renders a two-panel list: Available (left) vs Assigned (right).
 * Emits the updated list of assigned application IDs on save.
 */
import { ref, watch, computed } from 'vue';
import Button from 'primevue/button';
import type { Application } from '@/types/api';

const props = defineProps<{
  all: Application[];
  assigned: Application[];
  saving?: boolean;
}>();

const emit = defineEmits<{
  (e: 'save', applicationIds: number[]): void;
}>();

// ─── Internal lists (mirrored from props so we can mutate them) ───────────────
const target = ref<Application[]>([]);
const source = computed<Application[]>(() => {
  const assignedIds = new Set(target.value.map((a) => a.id));
  return props.all.filter((a) => !assignedIds.has(a.id));
});

watch(
  () => props.assigned,
  (val) => {
    target.value = [...val];
  },
  { immediate: true },
);

// ─── Move helpers ─────────────────────────────────────────────────────────────
const moveToTarget = (app: Application) => {
  if (!target.value.find((a) => a.id === app.id)) {
    target.value.push(app);
  }
};

const moveAllToTarget = () => {
  source.value.forEach((app) => {
    if (!target.value.find((a) => a.id === app.id)) {
      target.value.push(app);
    }
  });
};

const removeFromTarget = (app: Application) => {
  target.value = target.value.filter((a) => a.id !== app.id);
};

const removeAllFromTarget = () => {
  target.value = [];
};

const handleSave = () => {
  emit('save', target.value.map((a) => a.id));
};
</script>

<template>
  <div class="space-y-4">
    <div class="grid grid-cols-[1fr_auto_1fr] gap-4 items-stretch">
      <!-- Available (source) panel -->
      <div class="flex flex-col border border-slate-200 rounded-xl overflow-hidden">
        <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
          <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Available</span>
          <span class="text-xs text-slate-400 font-medium">{{ source.length }}</span>
        </div>
        <ul class="flex-1 overflow-y-auto divide-y divide-slate-50 min-h-48 max-h-72">
          <li
            v-for="app in source"
            :key="app.id"
            class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 cursor-pointer transition-colors group"
            @dblclick="moveToTarget(app)"
          >
            <div class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 text-xs shrink-0 group-hover:bg-primary-50 group-hover:text-primary-500 transition-colors">
              <i :class="app.icon ?? 'pi pi-th-large'"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-700 truncate">{{ app.name }}</p>
              <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ app.category }}</p>
            </div>
            <i class="pi pi-angle-right text-slate-300 text-xs opacity-0 group-hover:opacity-100 transition-opacity"></i>
          </li>
          <li v-if="source.length === 0" class="py-8 text-center text-slate-400 text-sm italic">
            All applications assigned
          </li>
        </ul>
      </div>

      <!-- Controls column -->
      <div class="flex flex-col items-center justify-center gap-2 py-4">
        <Button
          icon="pi pi-angle-double-right"
          severity="secondary"
          size="small"
          rounded
          outlined
          v-tooltip.top="'Assign all'"
          @click="moveAllToTarget"
          :disabled="source.length === 0"
        />
        <Button
          icon="pi pi-angle-double-left"
          severity="secondary"
          size="small"
          rounded
          outlined
          v-tooltip.top="'Remove all'"
          @click="removeAllFromTarget"
          :disabled="target.length === 0"
        />
      </div>

      <!-- Assigned (target) panel -->
      <div class="flex flex-col border border-primary-200 rounded-xl overflow-hidden">
        <div class="px-4 py-2.5 bg-primary-50 border-b border-primary-100 flex items-center justify-between">
          <span class="text-xs font-bold text-primary-600 uppercase tracking-wider">Assigned</span>
          <span class="text-xs text-primary-400 font-medium">{{ target.length }}</span>
        </div>
        <ul class="flex-1 overflow-y-auto divide-y divide-slate-50 min-h-48 max-h-72">
          <li
            v-for="app in target"
            :key="app.id"
            class="flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 cursor-pointer transition-colors group"
            @dblclick="removeFromTarget(app)"
          >
            <div class="w-7 h-7 rounded-lg bg-primary-100 flex items-center justify-center text-primary-500 text-xs shrink-0 group-hover:bg-red-100 group-hover:text-red-500 transition-colors">
              <i :class="app.icon ?? 'pi pi-th-large'"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-semibold text-slate-700 truncate">{{ app.name }}</p>
              <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ app.category }}</p>
            </div>
            <i class="pi pi-times text-slate-300 text-xs opacity-0 group-hover:opacity-100 group-hover:text-red-400 transition-opacity"></i>
          </li>
          <li v-if="target.length === 0" class="py-8 text-center text-slate-400 text-sm italic">
            No applications assigned yet
          </li>
        </ul>
      </div>
    </div>

    <p class="text-xs text-slate-400">
      <i class="pi pi-info-circle mr-1"></i>
      Double-click an item to move it, or use the arrow buttons to move all.
    </p>

    <!-- Save -->
    <div class="flex justify-end pt-2">
      <Button
        label="Save Access Rights"
        icon="pi pi-save"
        :loading="saving"
        @click="handleSave"
      />
    </div>
  </div>
</template>
