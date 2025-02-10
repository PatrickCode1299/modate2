<template>
    <div class="skeleton-loader">
      <div class="skeleton-avatar"></div>
      <div class="skeleton-text"></div>
      <div class="skeleton-text short"></div>
      <div class="skeleton-text"></div>
    </div>
  </template>
  
  <style scoped>
  .skeleton-loader {
    display: flex;
    flex-direction:column;
    align-items: center;
    margin-bottom: 20px;
  }
  .skeleton-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0e0e0;
    margin-right: 10px;
  }
  .skeleton-text {
    width: 100%;
    height: 15px;
    background: #e0e0e0;
    border-radius: 4px;
    margin-bottom: 8px;
  }
  .skeleton-text.short {
    width: 60%;
  }
  </style>