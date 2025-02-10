<template>
    <div class="skeleton-container">
      <!-- Simulate multiple notifications -->
      <div class="skeleton-notification" v-for="n in 5" :key="n">
        <!-- Simulate profile picture -->
        <div class="skeleton-img"></div>
        <!-- Simulate text content -->
        <div class="skeleton-text">
          <div class="skeleton-line"></div>
          <div class="skeleton-line short"></div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  </script>
  
  <style scoped>
  .skeleton-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px;
    height:100vh;
  }
  
  .skeleton-notification {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .skeleton-img {
    width: 40px;
    height: 40px;
    background-color: #e0e0e0;
    border-radius: 50%;
  }
  
  .skeleton-text {
    flex: 1;
  }
  
  .skeleton-line {
    height: 12px;
    background-color: #e0e0e0;
    margin-bottom: 8px;
    border-radius: 4px;
  }
  
  .skeleton-line.short {
    width: 60%;
  }
  </style>