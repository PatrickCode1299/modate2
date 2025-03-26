<script setup>
import { RouterLink } from 'vue-router';
import { ref, computed, watch, reactive } from 'vue';
import { useRouter } from 'vue-router';
import Header from '../component/Header.vue';
import VideoPlayerComponent from '../component/VideoPlayerComponent.vue';
import ImageSliderForPost from '../component/ImageSliderForPost.vue';
import AdPayButton from '../component/AdPayButton.vue';

// Utility functions
function checkIfUserPostIsLong(text) {
  if (!text) return;
  return text.length < 400 ? text : text.slice(0, 400) + "....See More";
}

function url_to_link(text) {
  const urlPattern = /(?:https?:\/\/|www\.)?(?:[\w-]+\.)+(?:[a-z]{2,})(\/\S*)?/gi;
  if (!urlPattern.test(text)) return text;
  return text.replace(urlPattern, match => {
    const href = match.match(/^https?:\/\//i) ? match : `http://${match}`;
    return `<a style='font-weight:bold; color:purple;' href="${href}" target="_blank">${match}</a>`;
  });
}

function replaceHashTagWithLink(text) {
  return (text || '').replace(/#(\w+)/g, match => `<a style='color:#1DA1F2;' href="/related/${match.slice(1)}">${match}</a>`);
}

// Props
let post_detail = defineProps({
  post_id: String,
  post_data: Object,
});
let post = post_detail.post_data;
let current_user_email=localStorage.getItem('USER_MAIL');

// Router and state
const router = useRouter();
const budget = ref(1); // Default budget
const current_rate=1496.00;

// Watcher to enforce minimum budget


// Reactive store for ad info
const store_current_ad_info = reactive({
  ad_duration: computed(() => Math.min(Math.floor(budget.value * 5), 365)),
  ad_amount: current_rate * 100, // Computed based on budget
  minReach: computed(() => Math.floor(budget.value * 10)),
  maxReach: computed(() => Math.floor(budget.value * 50)),
  day_count: computed(() => Math.min(Math.floor(budget.value * 5), 365)),
  payer_email:current_user_email
});

watch(budget, (newVal) => {
  if (newVal < 0.2) budget.value = 0.2; // Ensure min budget is 0.2
  store_current_ad_info.ad_amount=budget.value * current_rate * 100;
  console.log(store_current_ad_info.ad_amount);
});

// Utility to shorten names
function reduceNameLength(name) {
  return name.length > 14 ? name.slice(0, 14) + ".." : name;
}

function create_user_advert() {
  console.log(`
    This is the amount spent on running this ad: ${store_current_ad_info.ad_amount}, 
    This is the duration spent on running the ad: ${store_current_ad_info.ad_duration}`);
}
</script>

<template>
  <Header class="shadow-sm" style="background-color:white; position: fixed; width: 100%; z-index: 1; top: 0px;" />
  <div class="container-fluid  ad-preview-container">
   
    <AdPayButton :postid="post.postid"/>
    <div class="ad-post-preview shadow-md">
      <h2 class="font-bold m-4">Ad Preview</h2>
      <div class="card all_channel_content card-default">
        <div style="position: relative; width:100%; background-color: rgba(255, 255, 255, 0.634);" class="card-header d-flex inline-flex p-2 panel-header">
          <span style="margin-right: auto;" class="d-flex">
            <RouterLink to='/profile'>
              <img v-if="post.profile_picture === null" class="img-circle small-thumbnail" src="../pictures/profile.png" />
              <img v-else :src='`https://res.cloudinary.com/fishfollowers/image/upload/${post.profile_picture}`' class='img-circle small-thumbnail'>
            </RouterLink>
          </span>
          <span class="m-2">
            {{reduceNameLength(post.name)}}
            <small style="display:block; color:lightslategray;">Sponsored</small>
          </span>
        </div>
        <p style="word-wrap: break-word; white-space:pre-wrap;" class='p-2 fs-6' v-html="url_to_link(checkIfUserPostIsLong(replaceHashTagWithLink(post.caption)))"></p>
        <ImageSliderForPost
          style="margin-top:0px;"
          v-if="post.video === null && post.post_img1 !== null"
          :postid="post.postid"
          :images="[post.post_img1 && `https://res.cloudinary.com/fishfollowers/image/upload/${post.post_img1}`,
                   post.post_img2 && `https://res.cloudinary.com/fishfollowers/image/upload/${post.post_img2}`,
                   post.post_img3 && `https://res.cloudinary.com/fishfollowers/image/upload/${post.post_img3}`,
                   post.post_img4 && `https://res.cloudinary.com/fishfollowers/image/upload/${post.post_img4}`].filter(Boolean)"
        />
        <div v-if="post.video != null" class="flex-video">
          <VideoPlayerComponent style="max-width:100%;" :video_info="{ source: post.video }" />
        </div>
      </div>
    </div>
  </div>
</template>


<style scoped>
@media screen and (min-width:320px) {
  .ad-preview-container{
  display:block;
  width:100%;
}
}
@media screen and (min-width:420px) {
  .ad-preview-container{
  display:block;
  width:100%;
}
}
@media screen and (min-width:620px) {
  .ad-preview-container{
  display:flex;
  width:100%;
}
}
@media screen and (min-width:1224px) {
  .ad-preview-container{
  display:flex;
  width:100%;
}
}
.slider-container {
  max-width: 500px;
  margin: auto;
  text-align: center;
  font-family: Arial, sans-serif;
}

h2 {
  margin-bottom: 10px;
}

.budget-display {
  margin-bottom: 20px;
  font-size: 24px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.budget-input {
  width: 80px;
  margin-left: 5px;
  font-size: 18px;
  text-align: right;
}

.slider {
  width: 100%;
}
.channel-container{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 50%;
    margin:0px auto;
    margin-top:40px;
}
.channel-info{
    margin: 0px auto; 
    margin-top:50px;
    width: 80%;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
}
.channel-info-holder{
    margin-top:40px;
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 50%; 
    margin: 0px auto;
}
.channel-img{
    border-radius: 50%;
    width: 150px;
    height: 150px;
    object-fit: cover;
}
.create_text{
    resize: none;
    color: black;
    font-weight: 400;
}
.display-content{
    padding-top: 0px;
    padding-bottom: 20px;
    border-radius: 10px;
    margin-top:0px;
    background-color: rgba(255,255,255,0.8);
}
.border-20px{
    border-radius: 20px;
    padding-left: 10px;
    padding-right: 10px;
}
.inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.user-post{
    width: 100%;
   
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-radius: 5px;
}
.card{
    border: none;
    width:100%;
}
.text-grey{
    color: rgb(158, 156, 156);
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    
}
.flex-img > img{
    margin-left: 2px;
    object-fit: cover;
    width: 100px;
    height: 100px;
    
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    justify-content: center;
    align-items: center;
    padding: 0px;
    
}
.flex-img > img{
    margin-left: 2px;
    margin-top: 2px;
    object-fit: cover;
    flex: 1;
    flex-basis: 40%;
    height: 100%;
   
    
}
.channel_photo_gallery {
  display: none;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px; /* Adjust the gap between the grid items as needed */
}

.channel_photo_gallery img {
  width: 100%; /* Make sure images take up the full width of the grid cell */
  height: auto; /* Maintain the aspect ratio of the images */
}
.channel_photo_gallery > div > img{
   object-fit: cover;
   
}
.spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid #f3f3f3; /* Light grey */
  border-top: 8px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}
.ad-preview-container{
   flex-wrap:wrap;
   justify-content:center;
   align-items:center;
   width:80%;
   margin-top:50px;
}
.ad-setup-info{
    flex:2;
    margin-right:50px;

}
.ad-post-preview{
    flex:1;
    height:400px;
    margin-top:0px;
}
</style>
