<template>
  <div>
    <h1>Viewing Live Broadcast</h1>
    <video ref="remoteVideo" autoplay></video>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import Pusher from 'pusher-js';
import axiosClient from "../axios";
import { useRoute } from 'vue-router';

const remoteVideo = ref(null);
let peerConnection;
const route = useRoute();
const broadcastId = route.params.broadcastId;
const viewerId = Math.random().toString(36).substring(7); // Random viewer ID
const pusher = new Pusher('d81e8911650d34cdc928', { cluster: 'eu' });

const startViewing = async () => {
  peerConnection = new RTCPeerConnection({
    iceServers: [{ urls: 'stun:stun.l.google.com:19302' }],
  });

  peerConnection.ontrack = (event) => {
    remoteVideo.value.srcObject = event.streams[0];
  };

  peerConnection.onicecandidate = (event) => {
    if (event.candidate) {
      sendSignal('candidate', { candidate: event.candidate });
    }
  };

  const offer = await peerConnection.createOffer();
  await peerConnection.setLocalDescription(offer);
  sendSignal('offer', { offer });
};

const sendSignal = (type, data) => {
  axiosClient.post('/send-signal', {
    type,
    data: { ...data, viewerId },
    broadcastId,
  });
};

const handleBroadcastSignal = (type, data) => {
  if (type === 'answer') {
    peerConnection.setRemoteDescription(new RTCSessionDescription(data));
  } else if (type === 'candidate') {
    peerConnection.addIceCandidate(new RTCIceCandidate(data));
  }
};

onMounted(() => {
  const channel = pusher.subscribe(`broadcast-${broadcastId}`);
  channel.bind('answer', data => handleBroadcastSignal('answer', data.data));
  channel.bind('candidate', data => handleBroadcastSignal('candidate', data.data));

  startViewing();
});

onBeforeUnmount(() => {
  pusher.unsubscribe(`broadcast-${broadcastId}`);
  if (peerConnection) {
    peerConnection.close();
  }
});
</script>

<style scoped>
video {
  width: 100%;
}
</style>