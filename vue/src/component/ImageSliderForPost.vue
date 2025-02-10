<template>
    <div class="carousel-container">
      <div class="carousel" :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
        <div
          class="carousel-slide"
          v-for="(image, index) in images"
          :key="index"
        >
          <img @load="checkforNudity(image, user_email, postid)" :src="image" alt="Carousel Image" />
        </div>
      </div>
  
      
      <div
        v-if="images.length > 1"
        class="carousel-controls"
      >
        <button @click="prevSlide" class="carousel-control prev">‹</button>
        <button @click="nextSlide" class="carousel-control next">›</button>
      </div>
  
      
      <div
        v-if="images.length > 1"
        class="carousel-indicators"
      >
        <span
          v-for="(image, index) in images"
          :key="index"
          :class="{ active: index === currentIndex }"
          @click="goToSlide(index)"
        ></span>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, onUnmounted } from "vue";
  import axiosClient from '../axios';
  const props = defineProps({
    postid:{
        type:String
    },
    user_email:{
        type:String
    },
    images: {
      type: Array,
      required: true,
    },
    interval: {
      type: Number,
      default: 3000, // Auto-slide every 3 seconds
    },
  });

  async function checkforNudity(imageUrl,img_owner,postid) {
  try {
    const response = await fetch(`https://api.sightengine.com/1.0/check.json?models=nudity&api_user=70851785&api_secret=jRxbCQyG8GMsQ9jVddE7EXQc2bZENh2e&url=${encodeURIComponent(imageUrl)}`);
    const result = await response.json();

    if (result.nudity.safe > 0.85) {
        return;
    } else {
      let formData=new FormData();
    formData.append("user",img_owner);
    formData.append("postid",postid);
    axiosClient.post("/deleteUserPost",formData).then(response=>{
        document.getElementById('post'+postid).style.display="none";
    }).catch(error=>{
        console.log("error");
    });
    }
  } catch (error) {
    console.error("Error in nudity detection:", error);
  }
}
  const currentIndex = ref(0);
  
  function nextSlide() {
    currentIndex.value = (currentIndex.value + 1) % props.images.length;
  }
  
  function prevSlide() {
    currentIndex.value =
      (currentIndex.value - 1 + props.images.length) % props.images.length;
  }
  
  function goToSlide(index) {
    currentIndex.value = index;
  }
  
  // Auto-slide functionality
  let autoSlide;
  function startAutoSlide() {
    autoSlide = setInterval(nextSlide, props.interval);
  }
  
  function stopAutoSlide() {
    clearInterval(autoSlide);
  }
  
  // Start the auto-slide on mount

  </script>
  
  
  <style scoped>
.carousel-container {
  position: relative;
  width: 100%;
  max-width: 100%;
  margin-top: 0;
  overflow: hidden;
}

/* Carousel track that holds all slides */
.carousel {
  display: flex;
  transition: transform 0.5s ease-in-out;
  width: 100%;
}

/* Individual slide inside the carousel */
.carousel-slide {
  flex: 0 0 100%; /* Each slide takes up 100% of the container width */
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Styling the images to make them uniform in width and height */
.carousel-slide img {
  width: 100%; /* Ensure the image takes the full width of the slide */
  height: 100%; /* Make sure image height matches the height of the container */
  object-fit: cover; /* Scale and crop the image to fit the container without distortion */
  border-radius:10px;
}

/* Controls (previous/next buttons) */
.carousel-controls {
  position: absolute;
  top: 50%;
  width: 100%;
  display: flex;
  justify-content: space-between;
  transform: translateY(-50%);
}

.carousel-control {
  background: rgba(0, 0, 0, 0.5);
  border: none;
  color: white;
  font-size: 2rem;
  cursor: pointer;
  padding: 5px 5px;
  border-radius: 50px;
  margin-left: 10px;
  margin-right: 10px;
  font-weight: bold;
}

/* Indicator buttons (small dots) */
.carousel-indicators {
  display: flex;
  justify-content: center;
  margin-top: 0;
  position: absolute;
  z-index: 1;
  top: 80%;
}

.carousel-indicators span {
  height: 10px;
  width: 10px;
  background-color: rgba(0, 0, 0, 0.5);
  border-radius: 50%;
  margin: 0 5px;
  cursor: pointer;
}

.carousel-indicators span.active {
  background-color: white;
}
</style>
