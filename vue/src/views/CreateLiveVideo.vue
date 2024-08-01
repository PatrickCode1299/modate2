<template>
  <Header style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
  <SideNav />
  <div v-if="info.info_value==='true'" style="margin-top: 100px;" class=" incomplete d-flex justify-content-center">
    <h2 class="fs-5 font-bold m-4">Click on profile icon above and complete your profile to be visible</h2>
  </div>
  <div v-else class="story-and-post" style="margin-top:80px;">
    <div class="live-streaming-camera-container p-4">
      <div class="camera-shutter"></div>
      <div>
        <video id="camera" ref="camera" controls style="margin-bottom:0px; height:200px; width:100%;" autoplay></video>
      </div>
      <h2 class="fs-2 font-bold m-2">Start your Live Video</h2>
      <div class="camera-button btn-group">
        <button v-if="!camera_event.isCameraOpen" @click="toggleCamera" class="btn btn-success btn-md m-2">Launch Camera</button>
        <button @click="stopCameraStream" v-else class="btn btn-danger btn-md m-2">Close Camera</button>
      </div>
      <div class="loading-camera" v-if="camera_event.isLoading"><img height="auto" width="100px" src="../landing/loading-loader.gif" /></div>
      <div class="camera-box d-flex">
        <div class="camera-shoot">
          <button class="btn btn-md btn-default fs-2" @click="takePhoto"><i class="fa fa-camera"></i></button>
        </div>
        <div class="camera-download">
          <a id="downloadPhoto" download="" class="btn btn-md btn-default fs-2">Capture</a>
          <canvas ref="canvas"></canvas>
        </div>
      </div>
      <div v-if="camera_event.link">
        <h3>Share this link with friends to watch the live video:</h3>
        <p>{{ camera_event.link }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { defineAsyncComponent } from "vue";
import axiosClient from "../axios.js";
import Pusher from 'pusher-js';

const Header = defineAsyncComponent(() => import("../component/Header.vue"));
const SideNav = defineAsyncComponent(() => import("../component/SideNav.vue"));

const camera = ref(null);
const canvas = ref(null);
const router = useRouter();
let peer;
let pusher;
let channel;

let info = reactive({ info_value: "" });
let camera_event = reactive({
  isCameraOpen: false,
  isPhotoTaken: false,
  isShotClick: false,
  isLoading: false,
  link: ""
});

const user_mail = ref(null);

function checkIfUserHasCompleteProfile() {
  user_mail.value = store.state.user.data;
  axiosClient
    .post("/profile", { email: user_mail.value })
    .then(response => {
      if (response.data.info === "false") {
        info.info_value = "true";
        localStorage.setItem("INCOMPLETE", info.info_value);
      }
    })
    .catch(error => console.log(error));
}

document.title = "Start Live Stream";

function toggleCamera() {
  if (camera_event.isCameraOpen) {
    stopCameraStream();
  } else {
    createCameraElement();
  }
}

function createCameraElement() {
  camera_event.isLoading = true;
  const constraints = {
    audio: true,
    video: { width: { ideal: 1280 }, height: { ideal: 720 } }
  };
  navigator.mediaDevices
    .getUserMedia(constraints)
    .then(stream => {
      camera_event.isLoading = false;
      camera.value.srcObject = stream;
      camera_event.isCameraOpen = true;
      generateLink();
      startBroadcast(stream);
    })
    .catch(error => {
      alert("Your browser doesn't support streaming");
      camera_event.isLoading = false;
    });
}

function stopCameraStream() {
  camera_event.isCameraOpen = false;
  const tracks = camera.value.srcObject.getTracks();
  tracks.forEach(track => track.stop());
  axiosClient.delete(`/delete-stream-videos/${camera_event.link.split('/').pop()}`)
    .then(response => console.log(response.data.message))
    .catch(error => console.error('Error deleting stream videos:', error));
}

function takePhoto() {
  camera_event.isPhotoTaken = true;
  setTimeout(() => {
    camera_event.isPhotoTaken = false;
  }, 50);
  const context = canvas.value.getContext("2d");
  context.drawImage(camera.value, 0, 0, 450, 337.5);
}

function generateLink() {
  axiosClient.post('/generate-link')
    .then(response => {
      camera_event.link = response.data.link;
      initializePusher(camera_event.link);
    })
    .catch(error => console.log("Error generating link:", error));
}

function initializePusher(link) {
  pusher = new Pusher('d81e8911650d34cdc928', { cluster: 'eu', encrypted: true });
  channel = pusher.subscribe(`live-stream.${link.split('/').pop()}`);
  channel.bind('client-signal', handleSignal);
  let info=new FormData();
  info.append("streamId",link.split('/').pop());
  axiosClient.post('/signalPusher',info).then(response=>{
    console.log(response);
  }).catch(error=>{
    console.log("Unable to signal pusher");
  })
}

function startBroadcast(stream) {
  const mediaRecorder = new MediaRecorder(stream);

  mediaRecorder.ondataavailable = (event) => {
    if (event.data.size > 0) {
      const formData = new FormData();
      formData.append('videoChunk', event.data);
      formData.append('streamId', camera_event.link.split('/').pop());
      
      axiosClient.post('/broadcast-signal', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).catch(error => console.log("Error broadcasting chunk:", error));
    }
  };

  mediaRecorder.start(1000); // send chunks every second
  

}

function handleSignal(data) {
  peer.signal(data.signalData);
}
</script>

<style scoped>
@media screen and (min-width:320px) {
  .story-and-post {
    display: flex;
    justify-content: flex-start;
    width: 97%;
    cursor: pointer;
  }
  .fetch-user-post-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
    margin: 0px auto;
  }
}
@media screen and (min-width:620px) {
  .story-and-post {
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
  }
  .fetch-user-post-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 50%;
    margin: 0px auto.
  }
}
@media screen and (min-width:1224px) {
  .story-and-post {
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
  }
  .fetch-user-post-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 50%;
    margin: 0px auto.
  }
}
</style>