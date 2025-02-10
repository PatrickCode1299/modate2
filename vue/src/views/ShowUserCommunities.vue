<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import {ref} from "vue";
import { watch } from 'vue';
import { reactive,defineProps,onMounted } from 'vue';
import axiosClient from "../axios";
const router=useRouter();
const route=useRoute();
let user_community_info=reactive({
all_user_community:"",
});
onMounted(()=>{
let user_mail=route.params.uid;
let formData=new FormData();
formData.append("user_mail",user_mail);
axiosClient.post("/findCommunitiesOwnedByUser",formData).then(response=>{
user_community_info.all_user_community=response.data.reply;
}).catch(error=>{
console.log(error);
});
});
function gotoCommunity(name){
router.push({
    path:`/showcommunity/${name}/top`
});
}
</script>
<template>
<div class="channel-container">
<ul>
    <li class="d-flex m-4" v-for="i in user_community_info.all_user_community"><img class="community-avatar" @click="gotoCommunity(i.community_name)" v-if="i.community_avatar==='' || i.community_avatar===null" src='../landing/hexarex.png' />
        <img @click="gotoCommunity(i.community_name)" class="community-avatar" v-else :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.community_avatar}`' />
        <span @click="gotoCommunity(i.community_name)" class="m-2">
            <h2 class="font-bold">{{i.community_name }}</h2>
            <h3 class="font-bold">{{i.followers_count > 0 ? i.followers_count+'Members':i.followers_count+'Member'}}</h3>
            <small style="color:grey;">{{i.community_category }}</small>
        </span>
    </li>
</ul>
</div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .channel-container{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    width:97%;
    cursor: pointer;
   
}
.channel-info{
    margin: 0px auto; 
    width: 100%;
    display: flex;
    flex-direction: column;
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 100%; 
    margin: 0px auto;
}
.channel-img{
    border-radius: 50%;
    width: 80px;
    height: 80px;
    object-fit: cover;
}
.create_text{
    resize: none;
    color: black;
    font-weight: 400;
}
.display-content{
    z-index: 1;
    padding-right: 0px;
    border-radius: 10px;
    margin-top:0px;
    background-color: rgba(255,255,255,0.4);
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
    padding-left: 5px;
    padding-right: 5px;
    width: 100%;
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
.channel-info-holder{
    margin-top:10px;
    margin-left:10px;
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
.community-avatar{
  width:90px;
  height:90px;
  object-fit:cover;  
  border-radius:10px;
  background-color:rgb(240, 240, 240);
  border:2px solid rgb(255, 180, 218);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}
@media screen and (min-width:620px) {
    .channel-container{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
}
.channel-info{
    margin: 0px auto; 
    width: 60%;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
}
.channel-info-holder{
    margin-top:50px;
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
    width: 100px;
    height: 100px;
    object-fit: cover;
}
.create_text{
    resize: none;
    color: black;
    font-weight: 400;
}
.display-content{
    z-index: 1;
    padding-top: 0px;
    padding-bottom: 20px;
    border-radius: 10px;
    margin-top:0px;
    background-color: rgba(255,255,255,0.4);
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
    padding-left: 2px;
    padding-right: 2px;
    width: 400px;
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
.community-avatar{
  width:90px;
  height:90px;
  object-fit:cover;  
  border-radius:10px;
  background-color:rgb(240, 240, 240);
  border:2px solid rgb(255, 180, 218);
}


@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}
@media screen and (min-width:1224px) {
    .channel-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
    margin:0 auto;
  
}
.channel-info{
    margin: 0px auto; 
    margin-top:50px;
    width: 60%;
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
    padding-left: 2px;
    padding-right: 2px;
    width: 600px;
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
.community-avatar{
  width:90px;
  height:90px;
  object-fit:cover;  
  border-radius:10px;
  background-color:rgb(240, 240, 240);
  border:2px solid rgb(255, 180, 218);
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}

</style>