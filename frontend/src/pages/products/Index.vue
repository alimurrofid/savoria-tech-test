<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import AppTable from '@/components/common/AppTable.vue';
import AppButton from '@/components/common/AppButton.vue';
import AppModal from '@/components/common/AppModal.vue';
import AppFormInput from '@/components/common/AppFormInput.vue';
import Column from 'primevue/column';

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
    console.error('Failed to load products', error);
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

// Modal Logic
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

const isShowVisible = ref(false);
const showData = ref({ name: '', sku: '', price: 0, stock: 0 });

const openShowModal = (product: any) => {
  showData.value = { ...product };
  isShowVisible.value = true;
};

const saveProduct = async () => {
  formLoading.value = true;
  try {
    if (isEditing.value) {
      await api.put(`/products/${form.value.id}`, form.value);
    } else {
      await api.post('/products', form.value);
    }
    isModalVisible.value = false;
    fetchProducts();
  } catch (error) {
    console.error('Failed to save product', error);
  } finally {
    formLoading.value = false;
  }
};

const deleteProduct = async (id: number) => {
  if (!window.confirm('Are you sure you want to delete this product?')) return;
  try {
    await api.delete(`/products/${id}`);
    fetchProducts();
  } catch (error) {
    console.error('Failed to delete product', error);
  }
};
</script>

<template>
  <div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-0">Products</h1>
      <AppButton label="+ Add Product" @click="openCreateModal" />
    </div>

    <div class="bg-white dark:bg-surface-900 rounded-lg shadow-sm">
      <AppTable
        :value="products"
        :loading="loading"
        :totalRecords="totalRecords"
        lazy
        @page="onPage"
        :rows="10"
      >
        <Column header="No." style="width: 3rem">
          <template #body="slotProps">
            {{ (lazyParams.page - 1) * lazyParams.rows + slotProps.index + 1 }}
          </template>
        </Column>
        <Column field="name" header="Name"></Column>
        <Column field="sku" header="SKU"></Column>
        <Column field="price" header="Price"></Column>
        <Column field="stock" header="Stock"></Column>
        <Column header="Actions" :exportable="false" style="min-width:10rem">
          <template #body="slotProps">
            <div class="flex gap-2">
              <AppButton icon="pi pi-eye" severity="info" text rounded aria-label="Show" @click="openShowModal(slotProps.data)" />
              <AppButton icon="pi pi-pencil" severity="secondary" text rounded aria-label="Edit" @click="openEditModal(slotProps.data)" />
              <AppButton icon="pi pi-trash" severity="danger" text rounded aria-label="Delete" @click="deleteProduct(slotProps.data.id)" />
            </div>
          </template>
        </Column>
      </AppTable>
    </div>

    <AppModal v-model:visible="isModalVisible" :header="isEditing ? 'Edit Product' : 'Add Product'">
      <form @submit.prevent="saveProduct" class="flex flex-col gap-4">
        <AppFormInput id="name" label="Name" v-model="form.name" required />
        <AppFormInput id="sku" label="SKU" v-model="form.sku" required />
        <AppFormInput id="price" label="Price" type="number" v-model="form.price" required />
        <AppFormInput id="stock" label="Stock" type="number" v-model="form.stock" required />
        
        <div class="flex justify-end gap-2 mt-4">
          <AppButton type="button" label="Cancel" severity="secondary" @click="isModalVisible = false" />
          <AppButton type="submit" label="Save" :loading="formLoading" />
        </div>
      </form>
    </AppModal>

    <AppModal v-model:visible="isShowVisible" header="Product Details">
      <div class="flex flex-col gap-4 text-surface-700 dark:text-surface-100">
        <div class="flex flex-col"><span class="font-bold text-sm text-surface-500">Name</span> <span>{{ showData.name }}</span></div>
        <div class="flex flex-col"><span class="font-bold text-sm text-surface-500">SKU</span> <span>{{ showData.sku }}</span></div>
        <div class="flex flex-col"><span class="font-bold text-sm text-surface-500">Price</span> <span>{{ showData.price }}</span></div>
        <div class="flex flex-col"><span class="font-bold text-sm text-surface-500">Stock</span> <span>{{ showData.stock }}</span></div>
      </div>
      <template #footer>
        <div class="flex justify-end mt-4">
          <AppButton label="Close" severity="secondary" @click="isShowVisible = false" />
        </div>
      </template>
    </AppModal>
  </div>
</template>
