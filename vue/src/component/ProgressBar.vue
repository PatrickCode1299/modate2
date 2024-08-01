<template>
    <div v-if="progress > 0" class="progress-bar">
      <div class="progress" :style="{ width: progress + '%' }">{{ progress }}%</div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  
  const props = defineProps({
    progress: {
      type: Number,
      required: true,
    },
  });
  </script>
  
  <style scoped>
  .progress-bar {
    width: 100%;
    background-color: #f3f3f3;
    border-radius: 5px;
    overflow: hidden;
    position: relative;
  }
  .progress {
    height: 20px;
    background-color: #4caf50;
    text-align: center;
    color: white;
    line-height: 20px;
    transition: width 0.4s ease;
  }
  </style>