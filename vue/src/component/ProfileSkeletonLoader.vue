<template>
    
    <div class="container skeleton-loader">
      <!-- Skeleton for Header -->
      <div class="skeleton-cover"></div>
  
      <!-- Skeleton for Profile Picture and Name -->
      <div class="skeleton-profile">
        <div class="skeleton-profile-pic"></div>
        <div class="skeleton-profile-name"></div>
      </div>
  
      <!-- Skeleton for Buttons -->
      <div class="skeleton-buttons">
        <div class="skeleton-button"></div>
        <div class="skeleton-button"></div>
      </div>
  
      <!-- Skeleton for User Info -->
      <div class="skeleton-user-info">
        <div class="skeleton-user-info-item"></div>
        <div class="skeleton-user-info-item"></div>
      </div>
  
      <!-- Skeleton for Posts -->
      <div class="skeleton-posts">
        <div class="skeleton-post" v-for="n in 3" :key="n">
          <div class="skeleton-post-header"></div>
          <div class="skeleton-post-content"></div>
          <div class="skeleton-post-footer">
            <div class="skeleton-icon"></div>
            <div class="skeleton-icon"></div>
            <div class="skeleton-icon"></div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
 
  </script>
  
  <style scoped>
  .skeleton-loader {
    width: 100%;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  
  .skeleton-header,
  .skeleton-sidenav,
  .skeleton-cover,
  .skeleton-profile-pic,
  .skeleton-profile-name,
  .skeleton-button,
  .skeleton-user-info-item,
  .skeleton-post-header,
  .skeleton-post-content,
  .skeleton-post-footer .skeleton-icon {
    background: linear-gradient(90deg, #ececec 25%, #f3f3f3 50%, #ececec 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
    border-radius: 4px;
  }
  
  .skeleton-header {
    height: 50px;
    width: 100%;
  }
  
  .skeleton-sidenav {
    height: 100vh;
    width: 250px;
    position: fixed;
  }
  
  .skeleton-cover {
    height: 150px;
    width: 100%;
    margin-top: 60px;
  }
  
  .skeleton-profile {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .skeleton-profile-pic {
    height: 100px;
    width: 100px;
    border-radius: 50%;
  }
  
  .skeleton-profile-name {
    height: 20px;
    width: 150px;
  }
  
  .skeleton-buttons {
    display: flex;
    gap: 10px;
  }
  
  .skeleton-button {
    height: 30px;
    width: 100px;
  }
  
  .skeleton-user-info {
    display: flex;
    gap: 20px;
  }
  
  .skeleton-user-info-item {
    height: 20px;
    width: 100px;
  }
  
  .skeleton-posts {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  
  .skeleton-post {
    padding: 10px;
    border-radius: 8px;
    background-color: #fff;
  }
  
  .skeleton-post-header {
    height: 20px;
    width: 150px;
    margin-bottom: 10px;
  }
  
  .skeleton-post-content {
    height: 50px;
    width: 100%;
    margin-bottom: 10px;
  }
  
  .skeleton-post-footer {
    display: flex;
    gap: 10px;
  }
  
  .skeleton-icon {
    height: 20px;
    width: 20px;
    border-radius: 50%;
  }
  
  @keyframes shimmer {
    0% {
      background-position: 200% 0;
    }
    100% {
      background-position: -200% 0;
    }
  }
  </style>