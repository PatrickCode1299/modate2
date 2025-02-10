<template>
    <div class="skeleton-loader">
      <div v-for="n in skeletonCount" :key="n" class="skeleton-video-container">
        <div class="skeleton-video"></div>
        <div class="skeleton-overlay">
          <div class="skeleton-profile">
            <div class="skeleton-avatar"></div>
            <div class="skeleton-text"></div>
          </div>
          <div class="skeleton-caption"></div>
          <div class="skeleton-actions">
            <div class="skeleton-action"></div>
            <div class="skeleton-action"></div>
            <div class="skeleton-action"></div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const skeletonCount = ref(3);
  </script>
  
  <style scoped>
  .skeleton-loader {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100vh;
    overflow-y: hidden;
  }
  
  .skeleton-video-container {
    position: relative;
    width: 100%;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #2c2c2c;
    overflow: hidden;
  }
  
  .skeleton-video {
    width: 100%;
    height: 100%;
    background-color: #444444;
    animation: pulse 1.5s infinite ease-in-out;
  }
  
  .skeleton-overlay {
    position: absolute;
    bottom: 50px;
    left: 0;
    color: white;
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
  }
  
  .skeleton-profile {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
  }
  
  .skeleton-avatar {
    width: 40px;
    height: 40px;
    background-color: #555555;
    border-radius: 50%;
    margin-right: 10px;
    animation: pulse 1.5s infinite ease-in-out;
  }
  
  .skeleton-text {
    width: 100px;
    height: 15px;
    background-color: #555555;
    animation: pulse 1.5s infinite ease-in-out;
  }
  
  .skeleton-caption {
    width: 80%;
    height: 15px;
    background-color: #555555;
    margin-bottom: 20px;
    animation: pulse 1.5s infinite ease-in-out;
  }
  
  .skeleton-actions {
    display: flex;
    justify-content: flex-start;
  }
  
  .skeleton-action {
    width: 50px;
    height: 50px;
    background-color: #555555;
    border-radius: 50%;
    margin-right: 10px;
    animation: pulse 1.5s infinite ease-in-out;
  }
  
  @keyframes pulse {
    0% {
      background-color: #555555;
    }
    50% {
      background-color: #666666;
    }
    100% {
      background-color: #555555;
    }
  }
  </style>