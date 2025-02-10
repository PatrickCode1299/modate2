<script setup>
import store from '../store';
import axiosClient from '../axios';
import { ref } from 'vue';
import { reactive,onMounted,computed } from 'vue';
import { defineProps } from 'vue';
import moment from 'moment'
import { RouterLink } from 'vue-router';
import LikeShareComment from "./LikeShareComment.vue";
import OldLikeShareComment from "./OldLikeShareComment.vue";
import BlockReportUserComponent from './BlockReportUserComponent.vue';
import VideoPlayerComponent from './VideoPlayerComponent.vue';
const latest_post=defineProps(['latest']);

const user_mail=localStorage.getItem('USER_MAIL');
let bookmark_post_holder=reactive({
user_posts:"",
channel_posts:"",
shared_posts:"",
});
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findAllBookMarkedPosts",formData).then(response=>{
bookmark_post_holder.user_posts=response.data.user_post_reply;
bookmark_post_holder.channel_posts=response.data.channel_post_reply;
bookmark_post_holder.shared_posts=response.data.shared_post_reply;

}).catch(error=>{
console.log(error);
});
let friend_post=reactive({
    post_key:[],
    short_post:[],
    caption:'',
    isLong:'',
    isLongBtn:'',
    isShortBtn:'',
    expandText:'',
    show_current_key:'',
    current_key_is_enabled:''
});
let     isLoading=ref("true");
let     Loader=ref("true");
let new_friend_post=reactive({
    post_date:"",
    poster_name:"",
    post_avatar:"",
    post_caption:"",
    fresh_new_post:[],
    post_key:[],
    short_post:[],
    caption:'',
    isLong:'',
    isLongBtn:'',
    isShortBtn:'',
    expandText:'',
    show_current_key:'',
    current_key_is_enabled:'',
    loader:''
});
let new_channel_post=reactive({
    fresh_channel_post:[],
});
function checkIfFriendPostIsLong(text, key){
     if(text==null){
        return;
    }else if(text.length < 128){
        friend_post.short_post.push(key);
        return text;
    }
    else if(text.length > 128){
        let caption_new=text.slice(0,128) + "...........................................";
        friend_post.post_key.push(key);
        
        return caption_new;
    }
}
function showChanneInfo(containerID){
    let channel_info_holder=containerID;
    document.getElementById(channel_info_holder).style.visibility="visible";
}
function hideChannelInfo(containerID){
    let channel_info_holder=containerID;
    document.getElementById(channel_info_holder).style.visibility="hidden";
}
function reduceNameLength(name){
    if(name.length > 14){
        let reduced_name=name.slice(0,14) + "..";
        return reduced_name;
    }else{
        return name;
    }
}
function url_to_link(text) {
    const urlPattern = /(?:https?:\/\/|www\.)?(?:[\w-]+\.)+(?:[a-z]{2,})(\/\S*)?/gi;
    if (!urlPattern.test(text)) {
        return text; // No URLs found, return original text
      }else{
        return text.replace(urlPattern, match => {
        const href = match.match(/^https?:\/\//i) ? match : `http://${match}`;
        return `<a style='font-weight:bold; color:purple;' href="${href}" target="_blank">${match}</a>`;
      });
      }
}
</script>
<template>
<div class="stories-and-div-container">
<div class="spinner" style="margin-top:40px;" v-if="bookmark_post_holder.channel_posts ===''">
    
</div>
<div v-else-if="bookmark_post_holder.user_posts==''&&bookmark_post_holder.channel_posts==''&&bookmark_post_holder.shared_posts==''">
<h4 style="margin-top: 200px;" class="fs-4 font-bold text-center">When you save a post, it would appear here..</h4>
</div>
<div v-else class="user-post-holder" style="margin-top:60px;">
    <div   v-for="x in bookmark_post_holder.user_posts" style='border: none; border-radius: 5px;' class='m-2 card p-2 post-container card-default'>
    <div style="background-color: rgba(255, 255, 255, 0.634);" class='card-header inline-flex p-2 panel-header'>
        <span style="margin-right: auto; display:flex;"><RouterLink :to='`/user/${x.email}`'><img v-if="x.profile_picture === null" class="img-circle small-thumbnail" src="../pictures/profile.png" /><img v-else  loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`' class='img-circle small-thumbnail'></RouterLink><span class='m-2'>{{reduceNameLength(x.name)}}</span></span><BlockReportUserComponent :post_owner="x.email" :post_id="x.postid" />
    </div>
    <p style="white-space:pre-wrap;" v-if="friend_post.current_key_is_enabled != x.created_at" v-html="url_to_link(x.caption)"  class='p-2 fs-6'></p>
    <OldLikeShareComment :post_content="{
                    post_caption:x.caption,
                    post_owner_name:x.name,
                    post_owner_email:x.email,
                    post_owner_avatar:x.profile_picture,
                    post_image_one:null,
                    post_image_two:null,
                    post_image_three:null,
                    post_image_four:null,
                    post_video:null,
                    post_is_comment_status:x.isReply,
                    post_likes_count:x.likes,
                    post_comments_count:x.comments,
                    post_shares_count:x.shares
                  }" :post_owner="x.email"    :post_id="x.postid" />
    <ul class='inline-flex'>
        <li style="font-size: 12px;" class='list-unstyled'>{{moment(x.created_at).fromNow()}}</li>
    </ul>
   </div> 
   <div  v-for="i in bookmark_post_holder.shared_posts"  style='border: none; border-radius: 5px;' class='m-2 card p-2 post-container card-default'>
      <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto; display: flex;"><RouterLink :to='`/user/${i.email_of_user_who_shared}`'><img v-if="i.profile_picture === null" class="img-circle small-thumbnail" src="../pictures/profile.png" /><img v-else loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' class='img-circle small-thumbnail'></RouterLink><span class="fs-6 m-2">Repost by</span><span class='m-2'>{{reduceNameLength(i.name_of_user_who_shared)}}</span></span><BlockReportUserComponent :post_owner="i.email" :post_id="i.postid" />
                    <span :id="i.id" style="position: absolute; top: 40%; visibility: hidden; font-size: 12px; right: 45%; word-wrap: break-word;  z-index: 1; display: block; width: 120px; background-color: black; border-radius: 6px; padding: 5px 0; color: white; text-align: center;">{{ i.channel_bio }} <br /><br /><i>"This user makes money from channels, launch your channel and get paid like them.."</i>   </span>
                   </div>
                    <p style="word-wrap: break-word; white-space:pre-wrap;" v-if="friend_post.current_key_is_enabled != i.created_at"  class='p-2 fs-6'>{{i.quote}}</p>
                    <div class="card">
                    <RouterLink :to='`/user/${i.email}`'><h5 class="m-2">{{reduceNameLength(i.name)}}</h5></RouterLink>
                    <p class="m-2" style="word-wrap: break-word; white-space: pre-wrap;" v-html="url_to_link(i.caption)"></p>
                    <div class="flex-img">
                        <img v-if="i.post_img1 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img1}`' />
                        <img v-if="i.post_img2 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img2}`' />
                        <img v-if="i.post_img3 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img3}`' />
                        <img v-if="i.post_img4 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img4}`' />
                    </div>
                    <div v-if="i.video != null" class="flex-video">
                        <VideoPlayerComponent style="width:100%;" :video_info="{
                            source:i.video
                        }"/>
                    </div>
                </div>
                  <OldLikeShareComment :post_content="{
                    post_caption:i.caption,
                    post_owner_name:i.name,
                    post_owner_email:i.email,
                    post_owner_avatar:i.profile_picture,
                    post_image_one:i.post_img1,
                    post_image_two:i.post_img2,
                    post_image_three:i.post_img3,
                    post_image_four:i.post_img4,
                    post_video:i.video,
                    post_is_comment_status:i.isReply,
                    post_likes_count:i.likes,
                    post_comments_count:i.comments,
                    post_shares_count:i.shares
                  }" :post_owner="i.email" :post_id="i.postid" />
                    <ul class='inline-flex'>
                    <li style="font-size: 12px;" class='list-unstyled'>{{moment(i.created_at).fromNow()}}</li>
                    </ul>
         
    </div>
<div  v-for="i in bookmark_post_holder.channel_posts"  style='border: none; border-radius: 5px;' class='m-2 card p-2 post-container card-default'>
      <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto; display: flex;"><RouterLink :to='`/channel/${i.email}`'><img v-if="i.profile_picture === null" loading="lazy" class="img-circle small-thumbnail" src="../pictures/profile.png"/><img v-else loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' class='img-circle small-thumbnail'></RouterLink><span @mouseenter="showChanneInfo(i.id)"  @mouseleave="hideChannelInfo(i.id)" class="fs-6 m-2">From Channel</span><span class='m-2'><i style="height: 15px; width:15px; background-color: rgb(28, 121, 252); font-weight: bold; color: white; border-radius: 50%;" class="fa-solid fa-check"></i>{{reduceNameLength(i.name)}}</span></span><BlockReportUserComponent :post_owner="i.email" :post_id="i.postid" />
                    <span :id="i.id" style="position: absolute; top: 40%; visibility: hidden; font-size: 12px; right: 45%; word-wrap: break-word;  z-index: 1; display: block; width: 120px; background-color: black; border-radius: 6px; padding: 5px 0; color: white; text-align: center;">{{ i.channel_bio }} <br /><br /><i>"This user makes money from channels, launch your channel and get paid like them.."</i>   </span>
                   </div>
                    <p style="word-wrap: break-word; white-space:pre-wrap;" v-if="friend_post.current_key_is_enabled != i.created_at" v-html="url_to_link(i.caption)"  class='fs-6'></p>
   
                    <p style="word-wrap: break-word;" v-if="friend_post.show_current_key === i.created_at">{{friend_post.expandText }}</p>
                    <div class="flex-img">
                        <img v-if="i.post_img1 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img1}`' />
                        <img v-if="i.post_img2 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img2}`' />
                        <img v-if="i.post_img3 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img3}`' />
                        <img v-if="i.post_img4 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img4}`' />
                    </div>
                    <div v-if="i.video != null" class="flex-video">
                        <VideoPlayerComponent style="width:100%;" :video_info="{
                            source:i.video
                        }"/>
                    </div>
                  <OldLikeShareComment :post_content="{
                    post_caption:i.caption,
                    post_owner_name:i.name,
                    post_owner_email:i.email,
                    post_owner_avatar:i.profile_picture,
                    post_image_one:i.post_img1,
                    post_image_two:i.post_img2,
                    post_image_three:i.post_img3,
                    post_image_four:i.post_img4,
                    post_video:i.video,
                    post_is_comment_status:i.isReply,
                    post_likes_count:i.likes,
                    post_comments_count:i.comments,
                    post_shares_count:i.shares
                  }" :post_owner="i.email" :post_id="i.postid" />
                    <ul class='inline-flex'>
                    <li style="font-size: 12px;" class='list-unstyled'>{{moment(i.created_at).fromNow()}}</li>
                    </ul>
         
    </div>
</div>
</div>

</template>
<style scoped>

@media screen and (min-width:320px) {
    .user-post-holder{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

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
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    border:2px solid magenta;
    opacity: 0.8;
    padding: 0px;
    
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
.post-text:hover{
    outline: none;
  
}
.post-container{
    display: flex;
    flex-direction: column;
    flex: 1;
 
    
}
.card{
    width: 100%;
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
    height: 400px;
   
    
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
    .user-post-holder{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

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
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    border:2px solid magenta;
    opacity: 0.8;
    padding: 0px;
    
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
.post-text:hover{
    outline: none;
  
}
.post-container{
    display: flex;
    flex-direction: column;
    flex: 1;
 
    
}
.card{
    width: 600px;
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
    height: 400px;
   
    
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
    .user-post-holder{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
   
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

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
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    border:2px solid magenta;
    opacity: 0.8;
    padding: 0px;
    
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
.post-text:hover{
    outline: none;
  
}
.post-container{
    display: flex;
    flex-direction: column;
    flex: 1;
    border:1px solid rgba(233, 233, 233, 0.442);
 
    
}
.card{
    width: 600px;
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
    height: 400px;
   
    
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
