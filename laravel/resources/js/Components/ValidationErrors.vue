<script setup>
import { computed } from 'vue';

const props = defineProps({
  errors: Object
})
const hasErrors = computed(() => Object.keys(props.errors).length > 0);
</script>

<template>
  <div v-if="hasErrors">
    <div class="font-midium text-red-600">問題が発生しました。</div>
    <ul class="mt-3 list-disc list-indside text-sm text-red-600">
      <li v-for="(error, key) in props.errors" :key="key">{{ error }}</li>
    </ul>
  </div>
</template>