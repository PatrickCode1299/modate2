<template>
    <div class="community-header">
      <!-- Skeleton Loader for Community Title -->
      <div class="skeleton skeleton-title"></div>
  
      <!-- Skeleton Loader for Cover Photo -->
      <div class="skeleton skeleton-cover"></div>
  
      <!-- Skeleton Loader for Community Details -->
      <div class="skeleton skeleton-details">
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-buttons"></div>
      </div>
    </div>
  
    <!-- Skeleton Loader for Posts -->
    <div class="posts-frame">
      <div class="skeleton skeleton-post"></div>
      <div class="skeleton skeleton-post"></div>
      <div class="skeleton skeleton-post"></div>
    </div>
  </template>
  
  <style scoped>
  /* Skeleton Loader Styles */
  .skeleton {
    background-color: #ddd;
    border-radius: 4px;
    animation: shimmer 1.5s infinite;
  }
  
  @keyframes shimmer {
    0% {
      background-position: -200px 0;
    }
    100% {
      background-position: 200px 0;
    }
  }
  
  .skeleton-title {
    width: 200px;
    height: 24px;
    margin-bottom: 20px;
  }
  
  .skeleton-cover {
    width: 100%;
    height: 300px;
    margin-bottom: 20px;
  }
  
  .skeleton-details {
    width: 100%;
    padding: 10px;
  }
  
  .skeleton-text {
    width: 100%;
    height: 20px;
    margin-bottom: 10px;
  }
  
  .skeleton-buttons {
    width: 100px;
    height: 35px;
    margin-top: 10px;
  }
  
  /* Posts Skeleton Frame */
  .posts-frame {
    margin-top: 20px;
  }
  
  .skeleton-post {
    width: 100%;
    height: 150px;
    margin-bottom: 20px;
  }
  </style>