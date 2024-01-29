<script setup>
import store from '../store';
import axiosClient from '../axios';
import { computed, reactive, ref, watch } from 'vue';
import FetchUserPost from './FetchUserPost.vue';
import { useRouter } from 'vue-router';
const router=useRouter();
const user_mail=sessionStorage.getItem('USER_MAIL');
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
        sessionStorage.setItem('FIRSTNAME',response.data.first_name);
        sessionStorage.setItem('LASTNAME',response.data.last_name);
        sessionStorage.setItem('LOCATION',response.data.location);
        sessionStorage.setItem('PHONE',response.data.phone_number);
        sessionStorage.setItem('PICTURE',response.data.profile_picture);
        sessionStorage.setItem('COVER_PHOTO',response.data.coverPhoto);
        hold_picture.picture=sessionStorage.getItem('PICTURE');
    }
}));
function sendUserPost(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    axiosClient.post("/createUserPost",{email:user_mail, user_caption:caption.value}).then(response=>{
        findNewestPost();
    }).catch(e=>{
        alert("Network Error");
    });
    caption.value="";
}
let from_user_sent=reactive({
    date:"",
    name:"",
    avatar:"",
    caption:""
});

function findNewestPost(){
    axiosClient.post("/fetchNewPost",{email:user_mail}).then(response=>{
    from_user_sent.date=response.data.reply.created_at;
    from_user_sent.name=response.data.reply.name;
    from_user_sent.avatar=response.data.reply.avatar;
    from_user_sent.caption=response.data.reply.caption;


    }).catch(err=>{
        console.log(err);
    });
}

watch(caption, ()=>{
if(caption.value.length > 5){
   let post_button=document.getElementById("post-button");
   post_button.removeAttribute("disabled");
}else{
    let post_button=document.getElementById("post-button");
   post_button.setAttribute("disabled","true");
}
});
let new_var;
watch(from_user_sent, ()=>{
    sessionStorage.removeItem('NEW_POST_DATE');
    sessionStorage.removeItem('NEW_POSTER_NAME');
    sessionStorage.removeItem('NEW_POST_AVATAR');
    sessionStorage.removeItem('NEW_POST_CAPTION');
    sessionStorage.setItem('NEW_POST_DATE',from_user_sent.date);
    sessionStorage.setItem('NEW_POSTER_NAME',from_user_sent.name);
    sessionStorage.setItem('NEW_POST_AVATAR',from_user_sent.avatar);
    sessionStorage.setItem('NEW_POST_CAPTION',from_user_sent.caption);
    location.reload();
});
const latest_post_data=reactive({
    date:sessionStorage.getItem('NEW_POST_DATE'),
    name:sessionStorage.getItem('NEW_POSTER_NAME'),
    avatar:sessionStorage.getItem('NEW_POST_AVATAR'),
    caption:sessionStorage.getItem('NEW_POST_CAPTION')
});
function createStories(){
    router.push({
        name:"create_story"
    });
}
let user_story=reactive({
    post:"",
    user_story_is_shown:"",
    friend_post:"",
    active:"m-2 story-preview-img",
    story_src:"",
    friend_src:"",
    number_of_story:"",
    user_friends:"",
    friends_picture:""
});
function findmyStory(){
    axiosClient.post("/findUserStory",{email:user_mail}).then(response=>{
        if(response.data.reply==''){
            alert("It happens you haven't posted a story..")
        }else{
            user_story.post=response.data.reply;
            setStoryContainer();
        }
      
        
    }).catch(error=>{
        alert("Network Error");
    });

}
let display_user_story;
function setStoryContainer(){
     user_story.active="m-2 story-preview-img magenta";
     let set_story_container=document.getElementById("set-story");
     set_story_container.style.display="block";
      display_user_story=setInterval(showUserStory,7000);
}
var count=0;
var loadingISDone=ref('false');
function showUserStory(){
        console.log("Yes");
        loadingISDone.value="true";
        user_story.number_of_story=user_story.post.length;
        user_story.story_src=user_story.post[count++];
        if(count==user_story.post.length) count=0;
        
        
   
}
function hideStory(){
    let set_story_container=document.getElementById("set-story");
     set_story_container.style.display="none";
     loadingISDone.value="false";
    
}
function get_All_User_Friend(){
    axiosClient.post("/findUserFriend",{email:user_mail}).then(response=>{
      let friends_who_posted_story_count=response.data.reply;
      user_friends_story_length.value=friends_who_posted_story_count.length;
      user_story.user_friends=response.data.reply;
   

    }).catch(err=>{
        alert("Some errors occured");            
    });
}
function findmyfriendStory(friend_email){
    axiosClient.post("/findUserStory",{email:friend_email}).then(response=>{
        user_story.friend_post=response.data.reply;
        setFriendStoryContainer();
      
        
    }).catch(error=>{
        alert("Network Error");
    });
}
function setFriendStoryContainer(){
        user_story.active="m-2 story-preview-img magenta";
     let set_story_container=document.getElementById("friend-story");
     set_story_container.style.display="block";
      setInterval(showFriendStory,12000);
}
var friend_count=0;
function showFriendStory(){
        loadingISDone.value="true";
       user_story.number_of_story=user_story.friend_post.length;
        user_story.friend_src=user_story.friend_post[friend_count++];
        if(friend_count==user_story.friend_post.length) friend_count=0;
        
}
function hideFriendStory(){
    let set_story_container=document.getElementById("friend-story");
     set_story_container.style.display="none";
     var friends_story=setInterval(showFriendStory,12000);
     clearInterval(friends_story);
     loadingISDone.value="false";
}
get_All_User_Friend();


</script>
<template>
<div class="stories-and-div-container">
    <h2 class="fs-2 bold-text">Stories</h2>
<div class="user-story">
<span v-if="hold_picture.isLoadingStoryPic==='true'" class="text-bold cursor-pointer fs-4">Loading......</span>
<div v-else  class="stories-preview">
    <div style=" height: 200px;" class="d-flex ">
    <img @click="findmyStory" :class="user_story.active" :src="`http://localhost:8000/storage/${hold_picture.picture}`"   width="20%" height="100%">
    <img v-for="i in user_story.user_friends" @click="findmyfriendStory(i.email)" :src="`http://localhost:8000/storage/${i.picture}`"  class="m-2 story-preview-img" width="20%" height="100%">
    </div>  
    <span @click="createStories" class="m-2 border-50px text-success fs-2">&plus;</span>
</div>
</div>
<div class="user-post">
    <form @submit="sendUserPost" class="user-posting-form">
        <textarea style="outline: none; outline: 0; outline-style: none;" v-model="caption" class="post-text" placeholder="Feeling good write something.."></textarea>
        <span class="position-to-right"><button id="post-button" disabled   class="btn border-20px btn-md btn-success">Post</button></span>
    </form>
</div>

    <FetchUserPost :latest="latest_post_data" />

</div>
<div id="set-story" class="story-posts-container">
    <span @click="hideStory" class="m-2" style="text-align:center; font-size:45px; color: white; font-weight: bold;">&times;</span>
<div class="story-keeper" style="color: white;">
<ul style="position: absolute;  top: 0px;" class="d-flex justify-content-flex-start">
    <li v-for="m in user_story.number_of_story" class="fs-3" style="font-weight: bold;">&minus;</li>
</ul>
<span v-if="loadingISDone==='false'" class="loading">Loading.....</span>
<img v-if="loadingISDone==='true'" class="img-responsive story-img" :src="`http://localhost:8000/storage/${user_story.story_src}`" />
<span id="title" style="color: white;"></span>
</div>
</div>
<div id="friend-story" class="story-posts-container">
    <span @click="hideFriendStory" class="m-2" style="text-align:center; font-size:45px; color: white; font-weight: bold;">&times;</span>
<div class="story-keeper" style="color: white;">
<ul style="position: absolute;  top: 0px;" class="d-flex justify-content-flex-start">
    <li v-for="x in user_story.number_of_story" class="fs-3" style="font-weight: bold;">&minus;</li>
</ul>
<span v-if="loadingISDone==='false'" class="loading">Loading.....</span>
<img v-if="loadingISDone==='true'" class="img-responsive story-img" :src="`http://localhost:8000/storage/${user_story.friend_src}`" />
<span id="title" style="color: white;"></span>
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
    display: flex;
    flex-direction:column;
    justify-content: center;
    align-items: center;
    width:400px;
    position: relative;
    height: 80%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 70%;
    height: auto;
    border-radius: 5px;
    margin-left: 5px;
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
    margin-top: 0px;
    width: 100%;

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
    height: 80%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 70%;
    height: auto;
    border-radius: 5px;
    margin-left: 5px;
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
    margin-top: 0px;
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
    height: 80%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 70%;
    height: auto;
    border-radius: 5px;
    margin-left: 5px;
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
    margin-top: 0px;
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
}
</style>