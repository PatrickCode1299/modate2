<script setup>
const props = defineProps({
  info: {
    type: String,
    required: true
  }
});
</script>

<template>
  <div v-if="info" class="alert-box">
    <i class="fa-solid fa-circle-exclamation alert-icon"></i>
    <span class="alert-text">{{ info }}</span>
  </div>
</template>

<style scoped>
.alert-box {
  background-color: #fce4ec;
  color: #880e4f;
  padding: 12px 16px;
  border-left: 4px solid #ec407a;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin: 12px 0;
  animation: fadeIn 0.3s ease-out;
  position: fixed;
  left:20%;
  right:20%;
  top:10%;
  opacity:0.7;
}

.alert-icon {
  font-size: 20px;
  color: #ad1457;
}

.alert-text {
  font-size: 16px;
  line-height: 1.4;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
