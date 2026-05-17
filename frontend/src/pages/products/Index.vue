<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import AppTable from '@/components/common/AppTable.vue';
import AppButton from '@/components/common/AppButton.vue';
import Column from 'primevue/column';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

const toast = useToast();
const confirm = useConfirm();
const router = useRouter();

const products = ref([]);
const loading = ref(false);
const totalRecords = ref(0);
const lazyParams = ref({ page: 1, rows: 10 });

const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/products?page=${lazyParams.value.page}`);
    products.value = response.data.data;
    totalRecords.value = response.data.meta.total;
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to load products',
      life: 3000,
    });
  } finally {
    loading.value = false;
  }
};

const onPage = (event: any) => {
  lazyParams.value.page = event.page + 1;
  fetchProducts();
};

onMounted(() => {
  fetchProducts();
});

const deleteProduct = (id: number) => {
  confirm.require({
    message: 'Are you sure you want to delete this product?',
    header: 'Confirm Deletion',
    icon: 'pi pi-exclamation-triangle',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        await api.delete(`/products/${id}`);
        toast.add({
          severity: 'success',
          summary: 'Deleted',
          detail: 'Product deleted successfully',
          life: 3000,
        });
        fetchProducts();
      } catch (error) {
        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: 'Failed to delete product',
          life: 3000,
        });
      }
    },
  });
};

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(value);
};
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Product Management</h1>
        <p class="text-slate-500 text-sm mt-1">Manage your inventory, prices, and stock levels.</p>
      </div>
      <AppButton
        label="Add New Product"
        icon="pi pi-plus"
        class="shadow-sm shadow-primary-200"
        @click="router.push({ name: 'products.create' })"
      />
    </div>

    <div
      class="bg-white rounded-2xl shadow-[0_2px_10px_0_rgba(0,0,0,0.02)] border border-slate-100 overflow-hidden"
    >
      <AppTable
        :value="products"
        :loading="loading"
        :totalRecords="totalRecords"
        lazy
        @page="onPage"
        :rows="10"
        class="p-datatable-sm"
      >
        <Column
          header="NO."
          headerClass="px-6 py-4 text-xs font-bold text-slate-400 tracking-wider"
          bodyClass="px-6 py-4 text-slate-500 text-sm"
        >
          <template #body="slotProps">
            {{ (lazyParams.page - 1) * lazyParams.rows + slotProps.index + 1 }}
          </template>
        </Column>

        <Column
          field="name"
          header="PRODUCT NAME"
          headerClass="px-6 py-4 text-xs font-bold text-slate-400 tracking-wider"
          bodyClass="px-6 py-4"
        >
          <template #body="{ data }">
            <span class="font-semibold text-slate-700">{{ data.name }}</span>
          </template>
        </Column>

        <Column
          field="sku"
          header="SKU"
          headerClass="px-6 py-4 text-xs font-bold text-slate-400 tracking-wider"
          bodyClass="px-6 py-4"
        >
          <template #body="{ data }">
            <span
              class="text-slate-500 font-mono text-sm bg-slate-50 px-2 py-1 rounded border border-slate-100"
              >{{ data.sku }}</span
            >
          </template>
        </Column>

        <Column
          field="price"
          header="PRICE"
          headerClass="px-6 py-4 text-xs font-bold text-slate-400 tracking-wider"
          bodyClass="px-6 py-4"
        >
          <template #body="{ data }">
            <span class="text-slate-600 font-medium">{{ formatCurrency(data.price) }}</span>
          </template>
        </Column>

        <Column
          field="stock"
          header="STOCK"
          headerClass="px-6 py-4 text-xs font-bold text-slate-400 tracking-wider"
          bodyClass="px-6 py-4"
        >
          <template #body="{ data }">
            <span class="text-slate-600 font-medium">{{ data.stock }}</span>
          </template>
        </Column>

        <Column
          headerClass="px-6 py-4 text-xs font-bold text-slate-400 tracking-wider"
          bodyClass="px-6 py-4"
        >
          <template #header>
            <div class="w-full text-center">ACTIONS</div>
          </template>
          <template #body="slotProps">
            <div class="flex justify-center items-center gap-1">
              <AppButton
                icon="pi pi-eye"
                severity="info"
                variant="text"
                rounded
                @click="router.push({ name: 'products.show', params: { id: slotProps.data.id } })"
              />
              <AppButton
                icon="pi pi-pencil"
                severity="secondary"
                variant="text"
                rounded
                @click="router.push({ name: 'products.edit', params: { id: slotProps.data.id } })"
              />
              <AppButton
                icon="pi pi-trash"
                severity="danger"
                variant="text"
                rounded
                @click="deleteProduct(slotProps.data.id)"
              />
            </div>
          </template>
        </Column>
      </AppTable>
    </div>
  </div>
</template>

<style scoped>
:deep(.p-datatable-thead > tr > th) {
  background-color: white !important;
  border-bottom: 1px solid #f1f5f9 !important;
  padding: 1rem 1.5rem !important;
}
:deep(.p-datatable-tbody > tr) {
  background-color: white !important;
  transition: all 0.2s;
}
:deep(.p-datatable-tbody > tr:hover) {
  background-color: #f8fafc !important;
}
:deep(.p-datatable-tbody > tr > td) {
  border-bottom: 1px solid #f8fafc !important;
  padding: 1rem 1.5rem !important;
}
:deep(.p-datatable-tbody > tr > td:last-child) {
  padding-right: 1.5rem !important;
}
</style>
