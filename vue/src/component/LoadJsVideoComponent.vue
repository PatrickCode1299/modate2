<template>
    <div class="video-container">
      <video @click="playVideoWhenClick" ref="video" class="video">
        <source :src="`${media.video_info.source}#t=0.0010`">
      </video>
      <button @click="playVideoWhenClick" ref="playPauseBtn" class="play-pause">
        <i class="fa-solid fa-play"></i>
      </button>
      <div class="controls">
        <span class="time-display">{{ currentTime }}</span>
        <input type="range" ref="timeSlider" class="time" min="0" value="0">
        <span class="time-display">{{ duration }}</span>
        <button @click="muteVideo" ref="muteUnmuteBtn" class="mute-unmute">
          <i class="fa-solid fa-volume-xmark"></i>
        </button>
        <button @click="fullscreenVideo" ref="fullscreenBtn" class="fullscreen">
          <i class="fa-solid fa-expand"></i>
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted, computed } from 'vue';
  import { defineProps } from 'vue';
  
  const media = defineProps(['video_info']);
  const video = ref(null);
  const playPauseBtn = ref(null);
  const muteUnmuteBtn = ref(null);
  const timeSlider = ref(null);
  const fullscreenBtn = ref(null);
  
  const videoFunctions = reactive({
    isPaused: true,
    isPlayed: false,
    currentTime: 0,
    duration: 0
  });
  
  onMounted(() => {
    video.value.addEventListener('timeupdate', updateVideoTime);
    video.value.addEventListener('loadedmetadata', () => {
      timeSlider.value.max = video.value.duration;
      videoFunctions.duration = video.value.duration;
    });
  
    timeSlider.value.addEventListener('input', () => {
      if (video.value.duration) {
        video.value.currentTime = (timeSlider.value.value / 100) * video.value.duration;
      }
    });
  });
  
  function playVideo() {
    if (videoFunctions.isPaused) {
      video.value.play();
      videoFunctions.isPaused = false;
      playPauseBtn.value.innerHTML = '<i class="fa-solid fa-pause"></i>';
      playPauseBtn.value.style.opacity = 0;
    } else {
      video.value.pause();
      videoFunctions.isPaused = true;
      playPauseBtn.value.innerHTML = '<i class="fa-solid fa-play"></i>';
      playPauseBtn.value.style.opacity = 1;
    }
  }
  
  function playVideoWhenClick() {
    playVideo();
  }
  
  function muteVideo() {
    video.value.muted = !video.value.muted;
    muteUnmuteBtn.value.innerHTML = video.value.muted ? '<i class="fa-solid fa-volume-up"></i>' : '<i class="fa-solid fa-volume-xmark"></i>';
  }
  
  function updateVideoTime() {
    videoFunctions.currentTime = video.value.currentTime;
    timeSlider.value.value = (video.value.currentTime / video.value.duration) * 100;
  }
  
  function fullscreenVideo() {
    if (video.value.requestFullscreen) {
      video.value.requestFullscreen();
    } else if (video.value.mozRequestFullScreen) { // Firefox
      video.value.mozRequestFullScreen();
    } else if (video.value.webkitRequestFullscreen) { // Chrome, Safari and Opera
      video.value.webkitRequestFullscreen();
    } else if (video.value.msRequestFullscreen) { // IE/Edge
      video.value.msRequestFullscreen();
    }
  }
  
  const currentTime = computed(() => formatTime(videoFunctions.currentTime));
  const duration = computed(() => formatTime(videoFunctions.duration));
  
  function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    seconds = Math.floor(seconds % 60);
    return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
  }
  </script>
  
  <style scoped>
  body {
    font-family: Arial, sans-serif;
    background: #000;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
  }
  
  .video-container {
    position: relative;
    width: 100%;
    max-width: 640px;
    height: auto;
    aspect-ratio: 16 / 9;
    background: #000;
  }
  
  .video {
    width: 100%;
    height: 100%;
    background: #000;
    object-fit: contain;
  }
  
  .controls {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    width: 90%;
    background: rgba(0, 0, 0, 0.5);
    padding: 10px;
    border-radius: 5px;
  }
  
  .controls button,
  .controls input[type='range'],
  .controls .time-display {
    margin: 0 10px;
    background: transparent;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 16px;
  }
  
  .controls input[type='range'] {
    flex: 1;
    appearance: none;
    height: 5px;
    background: #444;
    outline: none;
    border-radius: 5px;
  }
  
  .controls input[type='range']::-webkit-slider-thumb {
    appearance: none;
    width: 10px;
    height: 10px;
    background: #fff;
    cursor: pointer;
    border-radius: 50%;
  }
  
  .controls input[type='range']::-moz-range-thumb {
    width: 10px;
    height: 10px;
    background: #fff;
    cursor: pointer;
    border-radius: 50%;
  }
  
  .controls .time-display {
    font-size: 14px;
    color: #fff;
  }
  
  .play-pause {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 24px;
    background: rgba(0, 0, 0, 0.5);
    padding: 10px;
    width:80px;
    height:80px;
    border-radius: 50px;
    color: #fff;
    cursor: pointer;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .video-container:hover .play-pause {
    opacity: 1;
  }
  
  .play-pause i {
    pointer-events: none;
  }
  </style>