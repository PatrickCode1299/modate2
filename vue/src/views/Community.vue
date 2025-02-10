<script setup>
import axios from "axios";
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import CommunityHeaderComponent from "../component/CommunityHeaderComponent.vue";
import store from "../store";
import moment from 'moment';
import axiosClient from "../axios.js";
import { useRouter,useRoute } from "vue-router";
import { onMounted,onBeforeUpdate, onBeforeMount, watch, ref, reactive, onUpdated } from "vue";
import CommunityPostComponent from "../component/CommunityPostComponent.vue";
import ShowUserCommunity from "./ShowUserCommunities.vue";

let user_mail;
const router=useRouter();
const route=useRoute();
let info=reactive({
    info_value:"false",
    user_has_channel:"",
});
let other=reactive({
    info_value:"false",
    user_has_channel:"",
});
let channel_data=reactive({
    channel_name:"",
    channel_owner:"",
    channel_bio:"",
    channel_avatar:"",
    channel_category:"",
    channel_cover:"",
    channel_subscribers:""
});
let channel_post=reactive({
    all_post:[

    ],
    old_channel_post:[

    ],
    short_post:[],
    post_key:[],
    show_current_key:"",
    expandText:"",
    isShortBtn:"",
    current_key_is_enabled:"",
    last_post_date:""
});
let new_channel_post=reactive({
    fresh_new_post:[

    ]
});
function setUpChannel(){
    router.push({
        name:"channel_create"
    });
}


onMounted(() =>{
    user_mail=localStorage.getItem('USER_MAIL');
    axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
       info.info_value="true";
       localStorage.setItem('INCOMPLETE',info.info_value);
       
    }

})).catch((error =>{
    console.log(error);
}))
});

onMounted(()=>{
document.title='Community';
});
</script>
<template>
   <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
    
    
    <div style="margin-top: 100px;" v-if="info.info_value==='true'" class="incomplete d-flex justify-content-center">
        <h2 class="fs-5 m-4 text-black font-bold">You need to complete your profile to use communities.</h2>
    </div>
    <div v-else style="margin-top:80px;" class="channel-container">
        <CommunityHeaderComponent />
        <CommunityPostComponent v-if="route.params.uid === undefined" />
        <ShowUserCommunity v-if="route.params.uid !==undefined" />
    </div>


</template>
<style scoped>
@media screen and (min-width:320px) {
    .channel-container{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    width:100%;
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

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}

</style>