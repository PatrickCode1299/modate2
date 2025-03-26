<script setup>
import axiosClient from '../axios';
import { onMounted,reactive } from 'vue';
import { useRouter,useRoute } from "vue-router";
const route=useRoute();
let channel_gallery=reactive({
    photos:"",
    isPhotoActive:true,
    isVideoActive:false
});
let user_mail;
onMounted(()=>{
if(route.params.uid != null){
user_mail=atob(route.params.uid);
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findAllChannelPhotos",formData).then(response=>{
channel_gallery.photos=response.data.reply;
}).catch(error=>{
console.log(error);
});
}else{
user_mail=localStorage.getItem('USER_MAIL');
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findAllChannelPhotos",formData).then(response=>{
channel_gallery.photos=response.data.reply;
}).catch(error=>{
console.log(error);
});
}
});
</script>
<template>
<div  id="channel_photo_gallery" class="channel_photo_gallery">
 <div  v-for="p in channel_gallery.photos">
<img @click="gotoPost(p.postid)" style="width:150px; border-radius:5px; height: 150px;" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${p.post_img1}`' />
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