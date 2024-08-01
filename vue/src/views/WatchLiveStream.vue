<template>
  <div class="video-container" style="margin-top:80px;">
    <video id="liveVideo" ref="liveVideo" controls style="width:100%;" autoplay muted></video>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useRoute } from "vue-router";
import axiosClient from "../axios.js";

const liveVideo = ref(null);
const route = useRoute();
let streamId = route.params.streamId;
let intervalId;
let mediaSource;
let sourceBuffer;
let latestChunkIndex = 0;

onMounted(() => {
  initializeMediaSource();
  intervalId = setInterval(fetchLatestVideoChunk, 1000); // Fetch video chunk every 1 second
});

onBeforeUnmount(() => {
  clearInterval(intervalId);
  if (mediaSource) {
    mediaSource.endOfStream();
  }
});

function initializeMediaSource() {
  mediaSource = new MediaSource();
  liveVideo.value.src = URL.createObjectURL(mediaSource);

  mediaSource.addEventListener('sourceopen', () => {
    console.log('MediaSource opened');
    try {
      sourceBuffer = mediaSource.addSourceBuffer('video/webm; codecs="vp8"');
      sourceBuffer.addEventListener('error', (e) => console.error('SourceBuffer error:', e));
      sourceBuffer.addEventListener('updateend', () => {
        console.log('Update end');
        if (liveVideo.value.paused) {
          liveVideo.value.play().catch(error => console.log('Error playing video:', error));
        }
      });
      fetchLatestVideoChunk(); // Fetch the first chunk
    } catch (error) {
      console.error('Error adding SourceBuffer:', error);
    }
  });
}

function fetchLatestVideoChunk() {
  axiosClient.post('/live-video/chunk', {
    streamId: streamId,
    chunkIndex:""
  })
  .then(response => {
    const chunk = response.data.chunk;
    if (chunk) {
      appendVideoChunk(chunk);
      latestChunkIndex++;
    }
  })
  .catch(error => console.log('Error fetching latest video chunk:', error));
}

function appendVideoChunk(chunkUrl) {
  const xhr = new XMLHttpRequest();
  xhr.open('GET', chunkUrl.replace('http://', 'https://'), true); // Ensure the URL is HTTPS
  xhr.responseType = 'arraybuffer';

  xhr.onload = () => {
    if (xhr.status === 200) {
      const chunk = xhr.response;

      if (sourceBuffer && mediaSource.readyState === 'open' && !sourceBuffer.updating) {
        console.log('Appending chunk to SourceBuffer');
        sourceBuffer.appendBuffer(chunk);
      } else if (sourceBuffer && mediaSource.readyState === 'open') {
        sourceBuffer.addEventListener('updateend', () => {
          console.log('Appending chunk to SourceBuffer after updateend');
          sourceBuffer.appendBuffer(chunk);
        }, { once: true });
      }
    }
  };

  xhr.onerror = () => {
    console.error('XHR error:', xhr.statusText);
  };

  xhr.send();
}
</script>

<style scoped>
.video-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}
</style>