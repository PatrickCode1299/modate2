<script setup>
import { onMounted,reactive } from 'vue';
import axiosClient from '../axios';
import { useRouter,useRoute } from "vue-router";

const route=useRoute();
const router=useRouter();
let channel_gallery=reactive({
    videos:"",
    isPhotoActive:true,
    isVideoActive:false
});
let user_mail;
onMounted(()=>{
if(route.params.uid != null){
user_mail=atob(route.params.uid);
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findAllChannelVideos",formData).then(response=>{
channel_gallery.videos=response.data.reply;
}).catch(error=>{
console.log(error);
});
   
}else{
user_mail=localStorage.getItem('USER_MAIL');
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findAllChannelVideos",formData).then(response=>{
channel_gallery.videos=response.data.reply;
}).catch(error=>{
console.log(error);
});
}

});
function gotoPost(postid){
    router.push(`/status/${postid}`);
    
}
</script>
<template>
<div id="channel_photo_gallery" class="channel_photo_gallery">
<div  v-for="p in channel_gallery.videos" style="position:relative;">
<button @click="()=>{document.getElementById('video'+p.postid).play()}" style="width:50px; height:50px; position:absolute; top:40px; left:40px; border-radius:50px;" class="btn btn-success  btn-md"><i class="fa-solid fa-play"></i></button>
<video  @click="gotoPost(p.postid)" :id="'video'+p.postid" style="width:150px; object-fit:cover; border-radius:5px; height: 150px;">
<source :src='`https://res.cloudinary.com/fishfollowers/video/upload/${p.video}#t=0.0010`'/>
</video>
</div>
</div>
</template>
<style scoped>
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
</style>