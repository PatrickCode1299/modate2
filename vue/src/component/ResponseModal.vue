<template>
    <div v-if="visible" class="modal-overlay" @click.self="$emit('close')">
      <div class="modal-box" :class="statusClass">
        <div v-if="status === 'success'" class="balloon-container">
          <div class="balloon"></div>
        </div>
        <div class="icon">
          <span v-if="status === 'success'">✅</span>
          <span v-else>❌</span>
        </div>
        <h2 class="modal-title">{{ title }}</h2>
        <p class="modal-message">{{ message }}</p>
        <button class="modal-btn" @click="$emit('close')">Close</button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  
  const props = defineProps({
    status: {
      type: String,
      required: true,
      validator: (value) => ['success', 'failure'].includes(value),
    },
    visible: {
      type: Boolean,
      required: true,
    }
  });
  
  const title = computed(() =>
    props.status === 'success' ? 'Success!' : 'Oops!'
  );
  
  const message = computed(() =>
    props.status === 'success' ? 'Ad created successfully!' : 'Unable to create ad.'
  );
  
  const statusClass = computed(() =>
    props.status === 'success' ? 'success' : 'failure'
  );
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1rem;
    box-sizing: border-box;
  }
  
  .modal-box {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    max-width: 400px;
    width: 100%;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }
  
  .icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
  }
  
  .modal-title {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
  }
  
  .modal-message {
    font-size: 1rem;
    margin-bottom: 1.5rem;
    color: #444;
  }
  
  .modal-btn {
    padding: 0.6rem 1.2rem;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    background-color: #4a90e2;
    color: white;
    cursor: pointer;
    transition: background-color 0.2s ease;
  }
  
  .modal-btn:hover {
    background-color: #357ab7;
  }
  
  /* Status styling */
  .success .icon {
    color: #28a745;
  }
  .failure .icon {
    color: #dc3545;
  }
  
  /* Balloon animation for success */
  .balloon-container {
    position: relative;
    margin-bottom: 1rem;
  }
  
  .balloon-container {
  position: relative;
  margin-bottom: 1rem;
}

.balloon {
  width: 60px;
  height: 80px;
  background-color: #28a745;
  border-radius: 50%;
  position: absolute;
  top: -50px;
  left: 50%;
  margin-left: -30px;
  animation: balloonAnimation 3s ease-in-out infinite;
}

/* Smooth floating effect */
@keyframes balloonAnimation {
  0% {
    transform: translateY(0) rotate(0deg);
  }
  30% {
    transform: translateY(-20px) rotate(10deg);
  }
  60% {
    transform: translateY(-40px) rotate(20deg);
  }
  90% {
    transform: translateY(-30px) rotate(5deg);
  }
  100% {
    transform: translateY(0) rotate(0deg);
  }
}

@media (max-width: 480px) {
  .modal-box {
    padding: 1.5rem;
  }

  .modal-title {
    font-size: 1.3rem;
  }

  .modal-message {
    font-size: 0.95rem;
  }
}
  </style>
  