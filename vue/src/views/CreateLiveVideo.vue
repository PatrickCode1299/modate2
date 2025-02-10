<template>
  <div>
    <div id="local-stream" style="width: 100%; height: 400px; background: #000;"></div>
    <div id="remote-stream" style="width: 100%; height: 400px; background: #000;"></div>
    <button @click="startStreaming">Start Streaming</button>
  </div>
</template>

<script setup>
/*import { ref } from 'vue';
import AgoraRTC from 'agora-rtc-sdk-ng'; 
import { RtcTokenBuilder, RtcRole } from 'agora-access-token'; // For generating tokens on the frontend

const client = ref(null);
const localTracks = ref([]);
const remoteTracks = ref([]);

const appId = '0690a11cd2964546986e6754d89369aa'; // Your Agora App ID
const appCertificate = '0af290dbd4d142efa6e9a01a3c936f71'; // Your Agora App Certificate
const channelName = 'test-channel'; // Example channel name
const uid = 0; // User ID (can be unique for each user)
const role = RtcRole.PUBLISHER; // Publisher role
const expireTimeInSeconds = 3600; // Token expiration (1 hour)

// Function to generate token
const generateToken = (appId, appCertificate, channelName, uid) => {
  const currentTimestamp = Math.floor(Date.now() / 1000);
  const expirationTimestamp = currentTimestamp + expireTimeInSeconds;

  // Generate token using Agora's token builder
  return RtcTokenBuilder.buildTokenWithUid(
    appId, 
    appCertificate, 
    channelName, 
    uid, 
    RtcRole.PUBLISHER, 
    expirationTimestamp
  );
};

// Function to start streaming
const startStreaming = async () => {
  try {
    // Generate token on the frontend
    const token = generateToken(appId, appCertificate, channelName, uid);

    // Initialize Agora client
    client.value = AgoraRTC.createClient({ mode: 'live', codec: 'vp8' });

    // Join the channel
    await client.value.join(appId, channelName, token, uid);

    // Create local tracks
    const [audioTrack, videoTrack] = await Promise.all([
      AgoraRTC.createMicrophoneAudioTrack(),
      AgoraRTC.createCameraVideoTrack()
    ]);

    // Ensure the tracks are valid
    if (!audioTrack || !videoTrack) {
      throw new Error('Failed to create audio or video track.');
    }

    // Store local tracks
    localTracks.value = [audioTrack, videoTrack];

    // Publish the local tracks to the client
    await client.value.publish(localTracks.value);

    // Play the video track locally
    videoTrack.play('local-stream');

    // Handle remote streams
    client.value.on('user-published', async (user, mediaType) => {
      await client.value.subscribe(user, mediaType);
      
      if (mediaType === 'video') {
        const remoteVideoTrack = user.videoTrack;
        
        // Ensure the remote track is valid before playing
        if (remoteVideoTrack) {
          remoteTracks.value.push(remoteVideoTrack);
          remoteVideoTrack.play('remote-stream');
        }
      }
    });

    client.value.on('user-unpublished', (user) => {
      console.log(`User ${user.uid} unpublished`);
    });

    // Optional: Get stats for the video track
    if (videoTrack && videoTrack.getMediaStreamTrack()) {
      const stats = await videoTrack.getMediaStreamTrack().getStats();
      console.log(stats);
    }

  } catch (error) {
    console.error('Error starting stream:', error);
  }
};**/
</script>