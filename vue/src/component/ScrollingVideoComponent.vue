<template>
  <div class="video-feed">
    <SkeletonLoader v-if="replies.length === 0" />
    <div
      v-for="(item, index) in replies"
      :key="item.id"
      class="video-container"
      :class="{ 
        'active': activeIndex === index,
        'exiting': exitingIndex === index 
      }"
      v-show="index === activeIndex || index === exitingIndex"
      @touchstart="handleTouchStart"
      @touchend="handleTouchEnd"
    >
      <!-- Video element -->
      <video
        ref="video"
        v-if="item.video"
        :src="getVideoUrl(item.video)"
        loop
        playsinline
        oncontextmenu="return false;"
        :muted="index !== activeIndex"
        @canplay="handleCanPlay($event, index)"
        @waiting="handleWaiting(index)"
        @playing="handlePlaying(index)"
        @click="togglePlayPause(index)"
        @loadedmetadata="setDuration(index)" 
        @timeupdate="handleTimeUpdate($event, index)"
        @mousemove="handleMouseLeave"
        
      ></video>
      
      <!-- Loading Spinner -->
      <div v-if="loading[index]" class="loading-spinner">
        <div class="spinner"></div>
      </div>
      <div @mouseenter="handleMouseEnter" @mouseleave="handleMouseLeave" v-if="activeIndex === index" class="play-pause-controls">
        <button @click="togglePlayPause(index)" class="play-btn">
          <span id="play" style="display:none;"><i class="fas fa-play"></i></span>
          <span style="display:block;" id="pause"><i class="fas fa-pause"></i></span>
        </button>
      </div>
     
      <!-- Overlay and action buttons -->
      <div class="overlay p-2">
        <div class="profile">
          <img v-if="item.profile_picture === null" style="object-fit:cover;"  src="../pictures/profile.png" alt="profile" />
          <img v-else  :src="getProfilePictureUrl(item.profile_picture)" alt="profile" />
          <p><RouterLink :to="`/channel/${item.email}`">{{ item.name }}</RouterLink></p>
        </div>
        <p class="caption">{{ checkIfFriendPostIsLong(item.caption) }}</p>
        <input 
      type="range" 
      class="seek-slider"
      min="0" 
      :max="duration" 
      step="0.1" 
      v-model="currentTime" 
      @input="seekVideo"
      @change="seekVideo"
    />
    <div class="time-display">
      <span>{{ formatTime(currentTime) }}</span> / 
      <span>{{ formatTime(duration) }}</span>
    </div>
        <!-- Modern Action Bar -->
        <div class="actions">
          <button @click="previousVideo(index)"  class="btn video-control-btn btn-md font-bold m-2"><i class="fa-solid fa-arrow-up"></i></button>
          <button @click="nextVideo(index)" class="btn  video-control-btn btn-md font-bold m-2"><i class="fa-solid fa-arrow-down"></i></button>
          <ScrollVideoLikeShareCommentComponent :post_content="{
                  post_likes_count: item.likes,
                  post_comments_count: item.comments,
                  post_shares_count: item.shares
                }" :post_owner="item.email" :post_id="item.postid" />
        </div>
      </div>
    </div>

    <!-- Comments Modal -->
    <div v-if="showComments" class="comments-modal" @click="closeComments">
      <div class="comments-content" @click.stop>
        <h3>Comments</h3>
        <div v-for="comment in selectedReply.commentsList" :key="comment.id" class="comment-item">
          <p><strong>{{ comment.author }}</strong>: {{ comment.text }}</p>
        </div>
        <input
          type="text"
          v-model="newComment"
          placeholder="Add a comment..."
          @keyup.enter="addComment"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted,  nextTick, defineEmits, onUnmounted, onBeforeUnmount } from 'vue';
import axiosClient from '../axios';
import SkeletonLoader from "./ScrollingVideoSkeletonLoader.vue";
import ScrollVideoLikeShareCommentComponent from "./ScrollVideoLikeShareCommentComponent.vue"
import { RouterLink } from 'vue-router';
const user_mail = localStorage.getItem('USER_MAIL');
const replies = ref([]);

const currentTime = ref(0);
const duration = ref(0);

const activeIndex = ref(0);
const exitingIndex = ref(null);
const showComments = ref(false);
const selectedReply = ref(null);
const newComment = ref("");
const loading = ref([]);
const playing=ref();
const getVideoUrl = (path) => `https://res.cloudinary.com/fishfollowers/video/upload/${path}`;
const getProfilePictureUrl = (path) => `https://res.cloudinary.com/fishfollowers/image/upload/${path}`;

const handleCanPlay = (event, index) => {
  if (index === activeIndex.value) {   
    event.target.play().catch(error => {
      console.error('Play failed:', error);
    });
    loading.value[index] = false;
  }
};

const handleWaiting = (index) => {
  loading.value[index] = true;
};

const handlePlaying = (index) => {
  loading.value[index] = false;
};

const updateVideoPlayState = () => {
  nextTick(() => {
    const videoElements = document.querySelectorAll('video');
    videoElements.forEach((video, index) => {
      if (index === activeIndex.value) {
        video.play().catch(error => {
          console.error('Play failed:', error);
        });
        document.getElementById("pause").style.display = "block";
        document.getElementById("play").style.display = "none";
      } else {
        video.pause();
        document.getElementById("pause").style.display = "none";
        document.getElementById("play").style.display = "block";
      }
    });
  });
};

const togglePlayPause = (index) => {
  const videoElement = document.querySelectorAll('video')[index];
  if (videoElement.paused) {
    //Video is Played
    document.getElementById("play").style.display = "none";
    document.getElementById("pause").style.display = "block";
    videoElement.play().catch(error => {
      console.error('Play failed:', error);
    });
    playing.value=false;
  } else {
    document.getElementById("pause").style.display = "none";
    document.getElementById("play").style.display = "block";
    videoElement.pause();
    playing.value=true;
  }
};

function setDuration(index){
  const videoElement = document.querySelectorAll('video')[index];
  duration.value = videoElement.duration;
  
}

function nextVideo(index) {
  let end_of_array = replies.value.length - 1;
  exitingIndex.value = activeIndex.value; // Mark the current index as exiting
  if (end_of_array === index) {
    activeIndex.value = 0; // Loop back to the first video
  } else {
    activeIndex.value = index + 1; // Move to the next video
  }
  updateVideoPlayState();
  setTimeout(() => {
    exitingIndex.value = null; // Clear the exiting state after the transition
  }, 500); // Same as the CSS transition duration
}

function previousVideo(index) {
  //exitingIndex.value = activeIndex.value; // Mark the current index as exiting
  if (index === 0) {
    let last_elem_in_array=replies.value.length -1;
    activeIndex.value=last_elem_in_array;
   // Already at the first video, do nothing
  } else {
    activeIndex.value = index - 1; // Move to the previous video
  }
  updateVideoPlayState();
  setTimeout(() => {
    exitingIndex.value = null; // Clear the exiting state after the transition
  }, 500); // Same as the CSS transition duration
}

const handleTimeUpdate = (event, index) => {
  const videoElement = event.target;
  if (videoElement.currentTime >= videoElement.duration - 0.5) {
    playNextVideo(index); 
  }
  currentTime.value = videoElement.currentTime;
};

function playNextVideo(index) {
  let end_of_array = replies.value.length - 1;
  exitingIndex.value = activeIndex.value; // Mark the current index as exiting
  if (end_of_array === index) {
    activeIndex.value = 0; // Loop back to the first video
  } else {
    activeIndex.value = index + 1; // Move to the next video
  }
  updateVideoPlayState();
  setTimeout(() => {
    exitingIndex.value = null; // Clear the exiting state after the transition
  }, 500); // Same as the CSS transition duration
}

let touchstartX = 0;
let touchendX = 0;

const handleSwipe = () => {
 /* if (touchendX < touchstartX - 50) {
    previousVideo(activeIndex.value);
  } **/
  if (touchendX > touchstartX + 50) {
    nextVideo(activeIndex.value);
  }
};

const handleTouchStart = (event) => {
  touchstartX = event.changedTouches[0].screenX;
};

const handleTouchEnd = (event) => {
  touchendX = event.changedTouches[0].screenX;
  handleSwipe();
};

const openComments = (index) => {
  selectedReply.value = replies.value[index];
  showComments.value = true;
};

const closeComments = () => {
  showComments.value = false;
};

const addComment = () => {
  if (newComment.value.trim()) {
    selectedReply.value.commentsList.push({
      id: selectedReply.value.commentsList.length + 1,
      author: "CurrentUser",
      text: newComment.value
    });
    newComment.value = "";
  }
};

const checkIfFriendPostIsLong = (text) => {
  if (text == null) {
    return;
  } else if (text.length < 400) {
    return text;
  } else {
    return text.slice(0, 400) + "....See More";
  }
};

const isPlaying = (index) => {
  const videoElement = document.querySelectorAll('video')[index];
  return videoElement && !videoElement.paused;
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
  updateVideoPlayState();
});
const handleKeyDown = (event) => {
  if (event.key === 'ArrowUp') {
    previousVideo(activeIndex.value); // Navigate to previous video
  } else if (event.key === 'ArrowDown') {
    nextVideo(activeIndex.value); // Navigate to next video
  }
};

const handleScroll = () => {
  const containers = document.querySelectorAll('.video-container');
  containers.forEach((container, index) => {
    const rect = container.getBoundingClientRect();
    const inView = rect.top >= 0 && rect.bottom <= window.innerHeight;
    if (inView) {
      activeIndex.value = index;
      updateVideoPlayState();
    }
  });
};

onBeforeUnmount(() => {
  // Pause all video elements when navigating away
  const videoElements = document.querySelectorAll('video');
  videoElements.forEach(video => {
    if (!video.paused) {
      video.pause();
    }
  });
});


onMounted(async () => {
  const response = await axiosClient.post('/fetchAllChannelsVideo', { email: user_mail }).catch(e => {
    alert("You are not connected to Internet");
    const interval = setInterval(() => {
        if (navigator.onLine) {
            clearInterval(interval);
            location.reload();
        }
    }, 5000);
  });
  response.data.reply.forEach(data => {
    replies.value.push(data);
  });
});
const handleNetworkChange = () => {
  if (navigator.onLine) {
    const videoElement = document.querySelector(`.video-container.active video`);
    if (videoElement) {
      videoElement.play().catch((error) => {
        console.error('Error playing video:', error);
      });
    }
  }
};
const initializeVideo = () => {
  duration.value = videoPlayer.value.duration;
};



// Seek the video to a specific time
const seekVideo = () => {
  const videoElement = document.querySelectorAll('video')[activeIndex.value];
  if (videoElement) {
    videoElement.currentTime = currentTime.value;
  }
};

// Format time (MM:SS or HH:MM:SS)
const formatTime = (seconds) => {
  const hrs = Math.floor(seconds / 3600);
  const mins = Math.floor((seconds % 3600) / 60);
  const secs = Math.floor(seconds % 60);
  return hrs > 0
    ? `${hrs}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`
    : `${mins}:${secs.toString().padStart(2, '0')}`;
};
const stopVideo = () =>{
  const videoElement = document.querySelector(`.video-container.active video`);
if (videoElement) {
  videoElement.pause();
  videoElement.src = '';
  videoElement.currentTime = 0;
  }
}
onMounted(() => {
  window.addEventListener('online', handleNetworkChange);
  window.addEventListener('offline', handleNetworkChange);
  isHovering.value = false;
  emit('handle_header', false); 
});

onUnmounted(() => {
  window.removeEventListener('online', handleNetworkChange);
  window.removeEventListener('offline', handleNetworkChange);
  const videoElement = document.querySelector(`.video-container.active video`);
  if (videoElement) {
  videoElement.pause();
  videoElement.src = '';
  videoElement.currentTime = 0;
  }
});

onBeforeUnmount(() => {
  stopVideo();
});
const emit= defineEmits(['handle_header']);
const isHovering = ref(false);

// Function to handle hover events
const handleMouseEnter = () => {
  isHovering.value = playing.value;
  emit('handle_header', playing.value); 

};

const handleMouseLeave = () => {

  isHovering.value = playing.value;
  emit('handle_header', playing.value); 
  
};
</script>
  
<style scoped>
.video-feed {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100vh;
  scroll-snap-type: y mandatory;
}

.video-container {
  position: relative;
  width: 100%;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: black;
  scroll-snap-align: start;
  transition: transform 0.5s ease-in-out;
}

.video-container.active {
  transform: translateX(0);
}

.video-container.exiting {
  transform: translateX(-100%);
}

.video-container video {
  width: 100%;
  max-height: 100%;
  object-fit: contain;
  background-color: black;
}

.video-container video.portrait {
  width: 100%;
  height: auto;
  object-fit: cover;
}

.video-container video.landscape {
  width: auto;
  height: 100%;
  object-fit: contain;
}

.play-pause-controls {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.play-btn {
  background: rgba(0, 0, 0, 0.5);
  border: none;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 10px;
  transition: background-color 0.3s;
  color: white;
  font-size: 20px;
}

.play-btn i {
  color: white;
  font-size: 36px;
}

.play-btn:hover {
  background-color: rgba(0, 0, 0, 0.7);
}

.overlay {
  background-color: rgba(0, 0, 0, 0.016);
  position: absolute;
  bottom: 90px;
  left: 0px;
  color: white;
  width: 100%;
}

.profile {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.profile img {
  border-radius: 50%;
  width: 40px;
  height: 40px;
  margin-right: 10px;
}

.caption {
  margin-bottom: 20px;
}

.actions {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: absolute;
  right: 20px;
  bottom: 10px;
  border-radius:5px;
  background-color:rgba(0, 0, 0, 0.164);
}

.action-item {
  margin: 10px 0;
  text-align: center;
  font-size: 20px;
}

.action-item i {
  font-size: 24px;
  cursor: pointer;
  transition: color 0.3s;
}

.action-item span {
  display: block;
  margin-top: 5px;
}

.action-item .liked {
  color: red;
}

.no-video {
  color: white;
}

/* Comments Modal */
.comments-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
}

.comments-content {
  background: white;
  border-radius: 8px;
  padding: 20px;
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
}

.comment-item {
  margin-bottom: 10px;
}

input[type="text"] {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.video-control-btn{
  background-color:rgba(61, 32, 229, 0.539);
  color:white;
  width:50px;
  height:50px;
  border-radius:50px;
}

.spinner {
  border: 8px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top: 8px solid #ffffff;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
}

.seek-slider{
  cursor: pointer;
  background: #ddd;
  height: 8px;
  border-radius: 5px;
  width:100%;
}
.seek-slider::-webkit-slider-runnable-track {
  background: #444;
  height: 8px;
  border-radius: 5px;
}

.time-display {
  font-size: 14px;
  color: #555;
}
.loading-spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
}
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>