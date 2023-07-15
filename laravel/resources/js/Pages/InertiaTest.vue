<script setup>
  import { Link } from '@inertiajs/inertia-vue3';
  import { ref } from 'vue';
  
  const newTitle = ref('')
  const newContent = ref('')
</script>

<template>
  <div class="mb-8">
    <input type="text" name="newTitle" id="" v-model="newTitle"><br>
    <input type="text" name="newContent" id="" v-model="newContent"><br>
    <Link method="post" as="button" :href="route('inertia.store')" 
    :data="{
      title: newTitle,
      content: newContent
    }">DB保存テスト</Link>
  </div>

</template>