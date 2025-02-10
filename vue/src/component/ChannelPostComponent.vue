<script setup>
import store from '../store';
import axiosClient from '../axios';
import { computed, onMounted,onBeforeUnmount, onUpdated, reactive, ref, watch } from 'vue';
import FetchChannelPost from './FetchChannelPost.vue';
import { useRouter } from 'vue-router';
import StoriesComponent from './StoriesComponent.vue';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
const router=useRouter();
const user_mail=localStorage.getItem('USER_MAIL');
const user_friends_story_length=ref('');
let caption=ref();
let hold_picture=reactive({
    picture:"",
    isLoadingStoryPic:"true",
});
axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
      return;
    }else{
        hold_picture.isLoadingStoryPic="false";
        localStorage.setItem('FIRSTNAME',response.data.first_name);
        localStorage.setItem('LASTNAME',response.data.last_name);
        localStorage.setItem('LOCATION',response.data.location);
        localStorage.setItem('PHONE',response.data.phone_number);
        localStorage.setItem('PICTURE',response.data.profile_picture);
        localStorage.setItem('COVER_PHOTO',response.data.coverPhoto);
        hold_picture.picture=localStorage.getItem('PICTURE');
    }
}));
const latest_post_data=reactive({
    date:sessionStorage.getItem('NEW_POST_DATE'),
    name:sessionStorage.getItem('NEW_POSTER_NAME'),
    avatar:sessionStorage.getItem('NEW_POST_AVATAR'),
    caption:sessionStorage.getItem('NEW_POST_CAPTION'),
    postid:sessionStorage.getItem('NEW_POST_POSTID'),
    email:sessionStorage.getItem('NEW_POST_EMAIL'),
});
</script>
<template>
<div class="stories-and-div-container">
<div style="position: relative;" class="user-post">
    <FetchChannelPost style="width: 100%; margin-top:0px;" :latest="latest_post_data" />
</div>
</div>

</template>
<style scoped>
@media screen and (min-width:320px) {
    .story-posts-container{
    position: fixed; 
    background-color: rgba(0, 0, 0, 0.979); 
    height: 100%; 
    left: 0px;
    top: 0px;
    display: none;
    width: 100%;
    z-index: 1;
}
.magenta{
    border:2px solid rgb(255, 110, 255);
}
.story-keeper{
    display:block;
    width: 100%;
    position: relative;
    height: 100%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 100%;
    height:auto;
    border-radius: 0px;
    margin-left: 0px;
    
}
.border-50px{
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    position: absolute;
    top:5px;
    font-weight: bold;
    background-color: rgb(255, 211, 211);
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

}
.user-post{
    width: 100%;
    margin-top:0px;
   
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 100%; 
    margin: 0px auto;
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-radius: 10px;
}
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    opacity: 0.8;
    padding: 0px;
    position: relative;
    
}
.stories-and-div-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    width: 100%;
    padding:0px 0px;

}
.stories-and-div-container > div{
    margin-top: 10px;
}
.post-text{
    border-radius: 5px;
    resize: none;
    padding: 10px 10px;
    color: rgb(17, 17, 17);
    font-weight: 400;
    width:100%;
    height:auto;
    border: none;
    outline: none;
}
textarea:focus{
    outline: none;
    outline-style: none;
}
.post-text:hover{
    outline: none;
    border: none;
}
.post-text:hover{
    outline: none;
  
}
.position-to-right{
    display: flex;
    justify-content: flex-end;
    
}
.border-20px{
    border-radius: 20px;
    font-weight: bold;
    width: 100px;
}
.btn-default{
background-color:rgba(0,0,0,0.8);
}
.story-count-indicator{
    position: fixed;
    max-width:100%;
    z-index:1;
}
.user-posting-form{
    width:100%;
    padding:0px 0px;
}
#left{
    position: absolute; 
   top: 40%; 
    z-index: 1; 
    color:white; 
    font-weight:bold; 
    font-size:35px; 
    left: 40px;
}
#right{
   position: absolute; 
   top: 40%; 
   z-index: 1; 
   color:white; 
   font-weight:bold; 
   font-size:35px; 
   right: 40px;
}
.tag_users_box{
    display:none;
}
.prev{
    position:absolute;
    top:100px;
    left:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:50px;
    width:50px;
}
.next{
    position:absolute;
    top:100px;
    right:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:50px;
    width:50px;
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
.tag_box{
    display:none;
}
.user-text-post{
    background:rgb(255, 255, 255);
    position:fixed;
    width:100%;
    height:100vh;
    border-radius:10px;
    z-index:1;
    top:0px;
    padding-top:0px;
    padding-left:5px;
    padding-right:5px;
   display:none;
}
.create_new_post{
    position:fixed; 
    background-color:rgba(255, 22, 255, 0.607); 
    color:white; 
    right:0px; 
    border-radius:50px; 
    height:auto; 
    width:40px; 
    z-index:1;
    bottom:300px;
}
}
@media screen and (min-width:620px) {
    .story-posts-container{
    position: fixed; 
    background-color: rgba(0, 0, 0, 0.979); 
    height: 100%; 
    left: 0px;
    top: 0px;
    display: none;
    width: 100%;
    z-index: 1;
}
.magenta{
    border:2px solid rgb(255, 110, 255);
}
.story-keeper{
    display: flex;
    flex-direction:column;
    justify-content: center;
    align-items: center;
    width:400px;
    position: relative;
    height: 100%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 100%;
    height:auto;
    border-radius: 5px;
    margin-left: 5px;
    object-fit:cover;
}
.border-50px{
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    position: absolute;
    top:5px;
    font-weight: bold;
    background-color: rgb(255, 211, 211);
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

}
.user-post{
    width: 100%;
   
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 100%; 
    margin: 0px auto;
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-radius: 10px;
}
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    opacity: 0.8;
    padding: 0px;
    position: relative;
    
}
.stories-and-div-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 50px;
    width: 50%;

}
.stories-and-div-container > div{
    margin-top: 10px;
}
.post-text{
    border-radius: 5px;
    resize: none;
    padding: 10px 10px;
    color: rgb(17, 17, 17);
    font-weight: 400;
    width:100%;
    height:auto;
    border: none;
    outline: none;
}
textarea:focus{
    outline: none;
    outline-style: none;
}
.post-text:hover{
    outline: none;
    border: none;
}
.post-text:hover{
    outline: none;
  
}
.position-to-right{
    display: flex;
    justify-content: flex-end;
    
}
.border-20px{
    border-radius: 20px;
    font-weight: bold;
    width: 100px;
}
.btn-default{
background-color:rgba(0,0,0,0.8);
}
.story-count-indicator{
    position: fixed;
    max-width:100%;
    z-index:1;
}
.user-posting-form{
    width:100%;
}
#left{
    position: absolute; 
   top: 40%; 
    z-index: 1; 
    color:white; 
    font-weight:bold; 
    font-size:35px; 
    left: 0px;
}
#right{
   position: absolute; 
   top: 40%; 
   z-index: 1; 
   color:white; 
   font-weight:bold; 
   font-size:35px; 
   right: 0px;
}
.tag_users_box{
    display:none;
}
.prev{
    position:absolute;
    top:100px;
    left:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:80px;
    width:80px;
}
.next{
    position:absolute;
    top:100px;
    right:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:80px;
    width:80px;
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
.tag_box{
    display:none;
}
.user-text-post{
    background:rgb(250, 249, 249);
    position:fixed;
    width:50%;
    height:400px;
    margin:0px auto;
    border-radius:10px;
    z-index:1;
    top:120px;
    padding-top:0px;
    display:none;
}
.create_new_post{
    position:fixed; 
    background-color:rgba(255, 22, 255, 0.65); 
    color:white; 
    right:0px; 
    border-radius:50px; 
    height:80px; 
    width:80px; 
    bottom:150px;
}
}
@media screen and (min-width:1224px) {
    .story-posts-container{
    position: fixed; 
    background-color: rgba(0, 0, 0, 0.979); 
    height: 100%; 
    left: 0px;
    top: 0px;
    display: none;
    width: 100%;
    z-index: 1;
}
.magenta{
    border:2px solid rgb(255, 110, 255);
}
.story-keeper{
    display: flex;
    flex-direction:column;
    justify-content: center;
    align-items: center;
    width:400px;
    position: relative;
    height: 100%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 100%;
    height:auto;
    border-radius: 0px;
    margin-left: 0px;
    object-fit:cover;
}
.border-50px{
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    position: absolute;
    top:5px;
    font-weight: bold;
    background-color: rgb(255, 211, 211);
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom:0px;
}
.user-post{
    width: 100%;
    margin-top:0px;
   
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 100%; 
    margin: 0px auto;
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-top-left-radius: 10px;
border-top-right-radius:10px;
border-bottom-left-radius: 5px;
border-bottom-right-radius: 5px;
}
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    opacity: 0.8;
    padding: 0px;
    position: relative;
    
}
.stories-and-div-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 50px;
    width: 50%;

}
.stories-and-div-container > div{
    margin-top: 10px;
}
.post-text{
    border-radius: 5px;
    resize: none;
    padding: 10px 10px;
    color: rgb(17, 17, 17);
    font-weight: 400;
    width:100%;
    height:auto;
    border: none;
    outline: none;
}
textarea:focus{
    outline: none;
    outline-style: none;
}
.post-text:hover{
    outline: none;
    border: none;
}
.post-text:hover{
    outline: none;
  
}
.position-to-right{
    display: flex;
    justify-content: flex-end;
    
}
.border-20px{
    border-radius: 20px;
    font-weight: bold;
    width: 100px;
}
.btn-default{
background-color:rgba(0,0,0,0.8);
}
.story-count-indicator{
    position:fixed;
    max-width:100%;
    z-index:1;
}
.user-posting-form{
    width:80%;
}
#left{
    position: absolute; 
   top: 40%; 
    z-index: 1; 
    color:white; 
    font-weight:bold; 
    font-size:35px; 
    left: 0px;
}
#right{
   position: absolute; 
   top: 40%; 
   z-index: 1; 
   color:white; 
   font-weight:bold; 
   font-size:35px; 
   right: 0px;
}
.tag_users_box{
    display:none;
}
.prev{
    position:absolute;
    top:100px;
    left:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:80px;
    width:80px;
}
.next{
    position:absolute;
    top:100px;
    right:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:80px;
    width:80px;
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
.tag_box{
    display:none;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.user-text-post{
    background:rgb(255, 255, 255);
    position:fixed;
    width:50%;
    height:50vh;
    margin:0px auto;
    border-radius:10px;
    z-index:1;
    top:120px;
    padding-top:0px;
    display:none;
}
.create_new_post{
    position:fixed; 
    background-color:rgba(255, 22, 255, 0.631); 
    color:white; 
    right:0px; 
    border-radius:50px; 
    height:80px; 
    width:80px; 
    bottom:100px;
}
}
</style>