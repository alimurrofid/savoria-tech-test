<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import AppTable from '@/components/common/AppTable.vue';
import AppButton from '@/components/common/AppButton.vue';
import AppModal from '@/components/common/AppModal.vue';
import AppFormInput from '@/components/common/AppFormInput.vue';
import Column from 'primevue/column';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';

const toast = useToast();
const confirm = useConfirm();

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

const isModalVisible = ref(false);
const isEditing = ref(false);
const formLoading = ref(false);
const form = ref({ id: null, name: '', sku: '', price: 0, stock: 0 });

const openCreateModal = () => {
  form.value = { id: null, name: '', sku: '', price: 0, stock: 0 };
  isEditing.value = false;
  isModalVisible.value = true;
};

const openEditModal = (product: any) => {
  form.value = { ...product };
  isEditing.value = true;
  isModalVisible.value = true;
};

const saveProduct = async () => {
  formLoading.value = true;
  try {
    if (isEditing.value) {
      await api.put(`/products/${form.value.id}`, form.value);
      toast.add({
        severity: 'success',
        summary: 'Updated',
        detail: 'Product updated successfully',
        life: 3000,
      });
    } else {
      await api.post('/products', form.value);
      toast.add({
        severity: 'success',
        summary: 'Created',
        detail: 'Product created successfully',
        life: 3000,
      });
    }
    isModalVisible.value = false;
    fetchProducts();
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'Failed to save product',
      life: 3000,
    });
  } finally {
    formLoading.value = false;
  }
};

const isShowVisible = ref(false);
const showData = ref({ name: '', sku: '', price: 0, stock: 0 });

const openShowModal = (product: any) => {
  showData.value = { ...product };
  isShowVisible.value = true;
};

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
        @click="openCreateModal"
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
                @click="openShowModal(slotProps.data)"
              />
              <AppButton
                icon="pi pi-pencil"
                severity="secondary"
                variant="text"
                rounded
                @click="openEditModal(slotProps.data)"
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

    <AppModal
      v-model:visible="isModalVisible"
      :header="isEditing ? 'Update Product' : 'Create New Product'"
      class="max-w-md w-full"
    >
      <div class="space-y-5 pt-2">
        <AppFormInput
          id="name"
          label="Product Name"
          placeholder="e.g. Mechanical Keyboard"
          v-model="form.name"
          required
        />
        <AppFormInput
          id="sku"
          label="SKU / Barcode"
          placeholder="e.g. KB-MECH-01"
          v-model="form.sku"
          required
        />
        <div class="grid grid-cols-2 gap-4">
          <AppFormInput
            id="price"
            label="Price (IDR)"
            type="number"
            v-model="form.price"
            required
          />
          <AppFormInput
            id="stock"
            label="Initial Stock"
            type="number"
            v-model="form.stock"
            required
          />
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end gap-3 mt-4 border-t border-slate-50 pt-5">
          <button
            @click="isModalVisible = false"
            class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors"
          >
            Cancel
          </button>
          <AppButton
            :label="isEditing ? 'Update Product' : 'Save Product'"
            :loading="formLoading"
            @click="saveProduct"
          />
        </div>
      </template>
    </AppModal>

    <AppModal v-model:visible="isShowVisible" header="Product Details" class="max-w-sm w-full">
      <div class="flex flex-col gap-5 text-slate-700 pt-2">
        <div class="flex flex-col">
          <span class="font-bold text-xs text-slate-400 tracking-wider mb-1">PRODUCT NAME</span>
          <span class="font-medium text-lg">{{ showData.name }}</span>
        </div>
        <div class="flex flex-col">
          <span class="font-bold text-xs text-slate-400 tracking-wider mb-1">SKU / BARCODE</span>
          <span
            class="font-mono text-sm bg-slate-50 px-3 py-1.5 rounded border border-slate-100 self-start"
            >{{ showData.sku }}</span
          >
        </div>
        <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 mt-2">
          <div class="flex flex-col">
            <span class="font-bold text-xs text-slate-400 tracking-wider mb-1">PRICE</span>
            <span class="text-slate-800 font-semibold">{{ formatCurrency(showData.price) }}</span>
          </div>
          <div class="flex flex-col">
            <span class="font-bold text-xs text-slate-400 tracking-wider mb-1">CURRENT STOCK</span>
            <span class="text-slate-700 font-medium"> {{ showData.stock }} Units </span>
          </div>
        </div>
      </div>
      <template #footer>
        <div class="flex justify-end mt-2 pt-2">
          <AppButton label="Close" severity="secondary" @click="isShowVisible = false" />
        </div>
      </template>
    </AppModal>
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
