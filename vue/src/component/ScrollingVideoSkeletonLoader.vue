<template>
  <div class="skeleton-loader">
    <div
      v-for="n in skeletonCount"
      :key="n"
      class="skeleton-video-container"
    >
      <div class="skeleton-video"></div>

      <!-- Main content skeleton -->
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

      <!-- ✨ Loading bounce overlay -->
      <div class="loading-overlay">
        <div class="loading-inner">
          <div class="loading-dots">
            <span class="dot"></span>
            <span class="dot delay"></span>
            <span class="dot delay-2"></span>
          </div>
          <p class="loading-text">
            🎬 Your videos are now loading...<br />
            <span class="sub-text">Checking videos from creators like you.</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const skeletonCount = ref(3);
</script>

<style scoped>
/* Layout */
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
  top:60px;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #2c2c2c;
  overflow: hidden;
  z-index:-1;
}

.skeleton-video {
  width: 100%;
  height: 100%;
  background-color: #ff0000;
  animation: pulse 1.5s infinite ease-in-out;
}

/* Regular skeleton elements */
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

/* Pulse animation */
@keyframes pulse {
  0% {
    background-color: #ff7ba7;
  }
  50% {
    background-color: #666666;
  }
  100% {
    background-color: #555555;
  }
}

/* === ✨ Loading overlay === */
.loading-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(5px);
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  text-align: center;
  z-index: 10;
}

.loading-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.loading-dots {
  display: flex;
  gap: 6px;
  margin-bottom: 10px;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: linear-gradient(180deg, #60a5fa, #2563eb);
  box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
  animation: bounceDot 0.9s infinite cubic-bezier(0.2, 0.9, 0.3, 0.95);
}

.dot.delay {
  animation-delay: 0.1s;
}

.dot.delay-2 {
  animation-delay: 0.2s;
}

@keyframes bounceDot {
  0% {
    transform: translateY(0);
    opacity: 0.9;
  }
  30% {
    transform: translateY(-8px);
    opacity: 1;
  }
  60% {
    transform: translateY(0);
    opacity: 0.95;
  }
  100% {
    transform: translateY(0);
    opacity: 0.9;
  }
}

.loading-text {
  color: #ffffff;
  font-weight: 600;
  font-size: 14px;
  line-height: 1.4;
}

.sub-text {
  font-size: 12px;
  color: #dbeafe;
  font-weight: 400;
  opacity: 0.8;
}
</style>
