<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
import FlashMessage from '@/Components/FlashMessage.vue'
import Pagination from '@/Components/Pagination.vue'
import { Inertia } from '@inertiajs/inertia'
import { onMounted, ref } from 'vue'
import dayjs from 'dayjs'

const props = defineProps({
  orders: Object,
  query: Object,
  request: Object
})

onMounted(() => {
  // console.log(props.orders.data)
})
</script>

<template>
  <Head title="購買履歴" />

  <AuthenticatedLayout>
      <template #header>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">購買履歴</h2>
      </template>

      <div class="py-12">
          <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <section class="text-gray-600 body-font">
                  <FlashMessage />
                    <!-- <div class="flex pl-4 my-4 lg:w-2/3 w-full mx-auto">
                      <div>
                        <input type="text" name="search" v-model="search">
                        <button class="bg-blue-300 text-white pyu-2 px-2" @click="searchCustomers">検索</button>
                      </div>
                    </div> -->
                  <table class="table-auto w-full text-left whitespace-no-wrap">
                    <thead>
                      <tr>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">ID</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">氏名</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">合計金額</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">ステータス</th>
                        <th class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">購入履歴</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="order in props.orders.data" :key="order.id">
                        <td class="border-b-2 border-gray-200 px-4 py-3 text-blue-500"><Link :href="route('purchases.show', {purchase: order.id})" class="color-blue-400">{{ order.id }}</Link></td>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ order.customer_name }}</td>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ order.total }}</td>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ order.status }}</td>
                        <td class="border-b-2 border-gray-200 px-4 py-3">{{ dayjs(order.created_at).format('YYYY/MM/DD HH:MM:ss') }}</td>
                      </tr>
                    </tbody>
                  </table>
                  <Pagination class="mt-6" :links="props.orders.links"></Pagination>
                </section>
              </div>
          </div>
      </div>
  </AuthenticatedLayout>
</template>