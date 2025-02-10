<script setup>
import store from '../store';
import axiosClient from '../axios';
import { computed, onMounted,onBeforeUnmount, onUpdated, reactive, ref, watch } from 'vue';
import FetchUserPost from './FetchUserPost.vue';
import { useRouter } from 'vue-router';
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import VideoPlayerComponent from "../component/VideoPlayerComponent.vue";
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
watch(caption, ()=>{
if(caption.value.trim().length > 0){
   let post_button=document.getElementById("post-button");
   post_button.removeAttribute("disabled");
}else{
    let post_button=document.getElementById("post-button");
   post_button.setAttribute("disabled","true");
}
});
let taglist=reactive({
tagged_users:[],
tagged_users_result:{}
});
let all_tagged_users=reactive({
info:[],
value:[]
}); 
watch(caption, ()=>{
let str = caption.value;
let pattern = /\B@[a-z0-9_-]+/gi;
let user_match=str.match(pattern);
if(user_match === null)
console.log(null);
else{
    user_match.forEach(x => {
    taglist.tagged_users=x;
    
});
document.getElementById("tag_box").style.display="block";
let formData=new FormData();
let tagged_user=taglist.tagged_users.substring(1);
formData.append('search_info',tagged_user);
axiosClient.post("/Search",formData).then(response=>{
taglist.tagged_users_result=response.data.reply;
}).catch(e=>{
    console.log(e);
})

}

});
function sendUserPost(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    if(all_tagged_users.info.length==0){
    axiosClient.post("/createUserPost",{email:user_mail, user_caption:caption.value, tagged_users:""}).then(response=>{
        findNewestPost();
    }).catch(e=>{
        alert("Network Error");
    });
    }else{
        axiosClient.post("/createUserPost",{email:user_mail, user_caption:caption.value, tagged_users:all_tagged_users.info}).then(response=>{
        findNewestPost();
    }).catch(e=>{
        alert("Network Error");
    });
    }
   
    caption.value="";
}
let from_user_sent=reactive({
    date:"",
    name:"",
    avatar:"",
    caption:"",
    email:"",
    postid:""
});

function findNewestPost(){
    axiosClient.post("/fetchNewPost",{email:user_mail}).then(response=>{
    from_user_sent.date=response.data.reply.created_at;
    from_user_sent.name=response.data.reply.name;
    from_user_sent.avatar=response.data.reply.avatar;
    from_user_sent.caption=response.data.reply.caption;
    from_user_sent.email=response.data.reply.email;
    from_user_sent.postid=response.data.reply.postid;


    }).catch(err=>{
        console.log(err);
    });
}




watch(from_user_sent, ()=>{
    sessionStorage.removeItem('NEW_POST_DATE');
    sessionStorage.removeItem('NEW_POSTER_NAME');
    sessionStorage.removeItem('NEW_POST_AVATAR');
    sessionStorage.removeItem('NEW_POST_CAPTION');
    sessionStorage.setItem('NEW_POST_DATE',from_user_sent.date);
    sessionStorage.setItem('NEW_POSTER_NAME',from_user_sent.name);
    sessionStorage.setItem('NEW_POST_AVATAR',from_user_sent.avatar);
    sessionStorage.setItem('NEW_POST_CAPTION',from_user_sent.caption);
    sessionStorage.setItem('NEW_POST_POSTID',from_user_sent.postid);
    sessionStorage.setItem('NEW_POST_EMAIL',from_user_sent.email);
    location.reload();
});
const latest_post_data=reactive({
    date:sessionStorage.getItem('NEW_POST_DATE'),
    name:sessionStorage.getItem('NEW_POSTER_NAME'),
    avatar:sessionStorage.getItem('NEW_POST_AVATAR'),
    caption:sessionStorage.getItem('NEW_POST_CAPTION'),
    postid:sessionStorage.getItem('NEW_POST_POSTID'),
    email:sessionStorage.getItem('NEW_POST_EMAIL'),
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
    pagination:"",
    friend_src:"",
    number_of_story:"",
    user_friends:"",
    friends_picture:"",
});
let currentSlide = ref(0);
const currentStorySlide = ref(0);
function findmyStory(){
    axiosClient.post("/findUserStory",{email:user_mail}).then(response=>{
        if(response.data.reply==''){
            router.push({
            name:"create_story"
            });
        }else{
            user_story.post=response.data.reply;
            setStoryContainer();
        }
      
        
    }).catch(error=>{
        alert("Network Error");
    });

}
var display_user_story;
function setStoryContainer(){
     user_story.active="m-2 story-preview-img magenta";
     let set_story_container=document.getElementById("set-story");
     set_story_container.style.display="block";
      showUserStory();
      
      
}
var count=0;
var loadingISDone=ref('false');
function showUserStory(){
        loadingISDone.value="true";
        user_story.story_src=user_story.post;
       user_story.number_of_story=user_story.post.length;
       
        
        
   
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
        user_story.pagination=response.data.pagination;
        

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
      showFriendStory();
}
var friend_count=0;
function showFriendStory(){
        loadingISDone.value="true";
        user_story.number_of_story=user_story.friend_post.length;
   /*    
        user_story.friend_src=user_story.friend_post[friend_count++];
        if(friend_count==user_story.friend_post.length) friend_count=0; **/
        user_story.friend_src=user_story.friend_post
        
}
function hideFriendStory(){
    let set_story_container=document.getElementById("friend-story");
     set_story_container.style.display="none";
     loadingISDone.value="false";
}
function left(){
var scrollImage=document.getElementById("scroll-images");
scrollImage.scrollBy({
left: -200,
behavior:"smooth"
});
}
function right(){
var scrollImage=document.getElementById("scroll-images");
scrollImage.scrollBy({
left: 200,
behavior:"smooth"
});
}
get_All_User_Friend();


const swiper = new Swiper('.swiper', {
touchEventsTarget: 'container',
simulateTouch: true,
touchRatio: 1,
touchAngle: 45,
grabCursor: true,
  direction: 'vertical',
  loop: true,
  pagination: {
    el: '.swiper-pagination',
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  scrollbar: {
    el: '.swiper-scrollbar',
  },
});
const isVideo = (item) => {
  const videoExtensions = ['mp4', 'webm', 'ogg','mkv']; // Add more video extensions if needed
  const extension = item.split('.').pop().toLowerCase();
  return videoExtensions.includes(extension);
};
const prevSlide = () => {
  currentSlide.value = (currentSlide.value === 0) ? user_story.friend_src.length - 1 : currentSlide.value - 1;
};

const nextSlide = () => {
  currentSlide.value = (currentSlide.value === user_story.friend_src.length - 1) ? 0 : currentSlide.value + 1;
};
const prevStorySlide = () => {
  currentStorySlide.value = (currentStorySlide.value === 0) ? user_story.story_src.length - 1 : currentStorySlide.value - 1;
};

const nextStorySlide = () => {
  currentStorySlide.value = (currentStorySlide.value === user_story.story_src.length - 1) ? 0 : currentStorySlide.value + 1;
};
function hideTextPost(){
    document.getElementById('text_post').style.display='none';
}
function createNewPost(){
    document.getElementById('text_post').style.display='block';
}
</script>
<template>
<div class="stories-and-div-container">
<div class="user-story">
<span v-if="hold_picture.isLoadingStoryPic==='true'" class="text-bold spinner cursor-pointer fs-4"></span>
<div v-else  class="stories-preview">
<div id="scroll-images"  style="max-width: 100%; position: inherit; height: 200px; scroll-behavior: smooth; padding:0px 0px; overflow:scroll; overflow-x: hidden;  -webkit-overflow-scrolling:touch; overflow-y: hidden;" class="swiper-wrapper">
   <li class="list-unstyled swiper-slide" style="width:50%; height:190px; position: relative;"><span  @click="createStories" class="m-3 border-50px text-success font-bold fs-1">&plus;</span><label class="text-white font-bold fs-6" style="position: absolute; text-shadow: 2px 2px 2px black; left:10%; top: 80%; bottom: 0px;">Your Story</label>
    <img @click="findmyStory" style="width: 100%; object-fit: cover; height:185px;" src="../pictures/profile.png" v-if="hold_picture.picture === null || hold_picture.picture === 'null'"  loading="lazy" :class="user_story.active"/><img style="width: 100%; object-fit: cover; height:185px;" v-else loading="lazy" @click="findmyStory" :class="user_story.active" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${hold_picture.picture}`" >
    </li> 
    
    <li class="list-unstyled swiper-slide" style="width:50%; height:190px; position: relative; margin-left:5px;" v-for="i in user_story.user_friends"><img v-if="i.picture === null || i.picture === 'null'" @click="findmyfriendStory(i.email)" style="width:100%; object-fit:cover; height:185px;" src="../pictures/profile.png"  class="m-2 story-preview-img" /><img v-else  style="width: 100%; object-fit: cover; height:185px;"  @click="findmyfriendStory(i.email)" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${i.picture}`"  class="m-2 story-preview-img" />
        <span class="text-white" style="font-weight: bold; font-size: 12px; text-shadow: 2px 2px 2px black; position: absolute; bottom:0px; left:40%;">{{i.first_name }}</span>
    </li>
    </div>
    <div v-if="user_story.pagination === 'true'" style="position: absolute; width:100%; display:flex; flex-direction:row; justify-content:space-around; z-index:1;  top:40%;">
    <button  @click="left"  class="btn btn-md btn-default "><i class="text-white fs-1 fas fa-angle-double-left"></i></button>
    <button style="margin-left:auto;" @click="right" class="btn btn-md btn-default"><i class="text-white fs-1 fas fa-angle-double-right"></i></button>
    </div> 
</div>
</div>
</div>
<div id="set-story" class="story-posts-container">
<div class="story-keeper" style="color: white;">
    <span @click="hideStory" style="font-size:50px; position:fixed; z-index:1; right:0px; top:0px; color: white; margin-top:0px; font-weight: bold;">&times;</span>
<ul style="top: 0px;" class="story-count-indicator d-flex justify-content-flex-start">
    <li v-for="m in user_story.number_of_story" class="fs-2 " style="font-weight: bold; color:white;">&minus;</li>
</ul>
<span v-if="loadingISDone==='false'" class="loading spinner"></span>
<div v-for="(item, index) in user_story.story_src" :key="index" class="slide"  v-show="currentStorySlide === index">
    <img v-if="item.text==''&&item.isVideoFile==''" class="img-responsive story-img" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${item.file}`" />
      <div v-if="item.text!=''&&item.isVideoFile==''" style="word-wrap:break-word;">
        <p class="font-bold text-align-center d-flex justify-content-center align-items-center" v-if="item.color==='user_text_story'"  style="position:absolute; white-space:pre-wrap;       word-wrap:break-word; height:100%; width:100%; left:0px; top:0px; background-color:magenta;">{{ item.text }}</p>
        <p class="font-bold text-align-center d-flex justify-content-center align-items-center" v-else-if="item.color==='user_text_maroon'"  style="position:absolute; white-space:pre-wrap; word-wrap:break-word; height:100%; width:100%; left:0px; top:0px; background-color:maroon;">{{ item.text }}</p>
        <p class="font-bold text-align-center d-flex justify-content-center align-items-center" v-else-if="item.color==='user_text_purple'"  style="position:absolute; white-space:pre-wrap; word-wrap:break-word; height:100%; width:100%; left:0px; top:0px; background-color:purple;">{{ item.text }}</p>
        <p class="font-bold text-align-center d-flex justify-content-center align-items-center" v-else-if="item.color==='user_text_green'"  style="position:absolute; white-space:pre-wrap;  word-wrap:break-word; height:100%; width:100%; left:0px; top:0px; background-color:forestgreen;">{{ item.text }}</p>
      </div>
      <VideoPlayerComponent v-if="item.isVideoFile !=''" style="width:100%; height:80vh;" :video_info="{ source: item.file }" />
    </div>

    <button v-if="user_story.story_src.length > 1" class="prev" @click="prevStorySlide"><i class="fa-solid fa-angles-left"></i></button>
    <button v-if="user_story.story_src.length > 1" class="next" @click="nextStorySlide"><i class="fa-solid fa-angles-right"></i></button>
  </div>

<span id="title" style="color: white;"></span>
</div>
<div id="friend-story" class="story-posts-container">
<div class="story-keeper" style="color: white;">
    <span @click="hideFriendStory"  style="text-align:center; font-size:45px; position:fixed; z-index:1; top:0px; right:0px; color: white; font-weight: bold;">&times;</span>
<ul style="  top: 0px;" class="story-count-indicator d-flex justify-content-center">
    <li v-for="x in user_story.number_of_story" class="fs-3" style="font-weight: bold; color:white;">&minus;</li>
</ul>
<span v-if="loadingISDone==='false'" class="loading spinner"></span>
<div v-if="loadingISDone === 'true'" class="slideshow-container" style="margin-top:0px;">
    <div v-for="(item, index) in user_story.friend_src" :key="index" class="slide" v-show="currentSlide === index">
        <img v-if="item.text==''&&item.isVideoFile==''" class="img-responsive story-img" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${item.file}`" />
      <div v-if="item.text!=''&&item.isVideoFile==''" style="background-color:magenta;">
        <p class="font-bold text-align-center d-flex justify-content-center align-items-center" v-if="item.color==='user_text_story'"  style="position:absolute; white-space:pre-wrap; height:100%; width:100%; left:0px; top:0px; background-color:magenta;">{{ item.text }}</p>
        <p class="font-bold text-align-center d-flex justify-content-center align-items-center" v-else-if="item.color==='user_text_maroon'"  style="position:absolute; white-space:pre-wrap; height:100%; width:100%; left:0px; top:0px; background-color:maroon;">{{ item.text }}</p>
        <p class="font-bold text-align-center d-flex justify-content-center align-items-center" v-else-if="item.color==='user_text_purple'"  style="position:absolute; white-space:pre-wrap; height:100%; width:100%; left:0px; top:0px; background-color:purple;">{{ item.text }}</p>
        <p class="font-bold text-align-center d-flex justify-content-center align-items-center" v-else-if="item.color==='user_text_green'"  style="position:absolute; white-space:pre-wrap; height:100%; width:100%; left:0px; top:0px; background-color:forestgreen;">{{ item.text }}</p>
      </div>
      <VideoPlayerComponent v-if="item.isVideoFile !=''" style="width:100%; height:80vh;" :video_info="{ source: item.file }" />
    </div>

    <button v-if="user_story.friend_src.length > 1" class="prev" @click="prevSlide"><i class="fa-solid fa-angles-left"></i></button>
    <button v-if="user_story.friend_src.length > 1" class="next" @click="nextSlide"><i class="fa-solid fa-angles-right"></i></button>
  </div>
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
    margin-top: 0px;
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
  top: 80%;
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
    flex-direction: row;
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
    flex-direction: row;
    justify-content: center;
    align-items: center;
    margin-top: 0px;
    margin:0px auto;
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