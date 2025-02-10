<script setup>
import store from '../store';
import axiosClient from '../axios';
import { ref } from 'vue';
import { reactive,onMounted,computed } from 'vue';
import Header from "../component/Header.vue";
import { defineProps } from 'vue';
import moment from 'moment'
import { RouterLink, useRoute, useRouter } from 'vue-router';
import LikeShareComment from "../component/LikeShareComment.vue";
import OldLikeShareComment from "../component/OldLikeShareComment.vue";
import BlockReportUserComponent from '../component/BlockReportUserComponent.vue';
import VideoPlayerComponent from '../component/VideoPlayerComponent.vue';
const route=useRoute();
let loading_icon=ref('true');
let postid=route.params.tag;
let all_post=reactive({
    first_five_post:"",
    new_five_post:"",
    first_post_name:"",
    related_post:"",
    shared_post:"",
    every_one_post:"",
    post_id:["patrick"]
});
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
function showlatest(){
    newest_post.date=latest_post.latest.date;
    newest_post.name=latest_post.latest.name;
    newest_post.avatar=latest_post.latest.avatar;
    newest_post.caption=latest_post.latest.caption;
    newest_post.postid=latest_post.latest.postid;
    newest_post.post_owner=latest_post.latest.email;
    
    
}
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

let new_channel_post=reactive({
    fresh_channel_post:[],
});
function checkIfFriendPostIsLong(text, key){
     if(text==null){
        return;
    }else if(text.length < 400){
        return text;
    }
    else if(text.length > 400){
        let caption_new=text.slice(0,400) + "....See More";
        return caption_new;
    }
}



onMounted(()=>{
    axiosClient.post("/related",{data:postid}).then(response=>{
    all_post.related_post=response.data.reply;
}).catch(error=>{
    console.log(error);
});
});


function showChanneInfo(containerID){
    let channel_info_holder=containerID;
    document.getElementById(channel_info_holder).style.visibility="visible";
}
function hideChannelInfo(containerID){
    let channel_info_holder=containerID;
    document.getElementById(channel_info_holder).style.visibility="hidden";
}
function reduceNameLength(name){
    if(name.length > 20){
        let reduced_name=name.slice(0,14) + "..";
        return reduced_name;
    }else{
        return name;
    }
}
function deleteUserPost(postid){
    let ask_if_user_wants_to_delete_post=confirm("Do you want to delete this Post?");
    if(ask_if_user_wants_to_delete_post){
        let formData=new FormData();
        formData.append("postid",postid);
        axiosClient.post("/deleteUserPost",formData).then(response=>{
            console.log(response.data.reply);
        }).catch(error=>{
            console.log(error);
        });
    }else{
        return;
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
function replaceHashTagWithLink(text) {
    return (text || '').replace(/#(\w+)/g, function (match, tag) {
  return `<a style='color:#1DA1F2;' href="/related/${tag}">${match}</a>`;
});
}
document.title="Related";
</script>
<template>
<Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
<span v-if="all_post.related_post===''" class="text-bold spinner cursor-pointer fs-4"></span>
<div v-else class="stories-and-div-container">
<div class="user-post-holder">
<div v-if="all_post.related_post != null" v-for="i in all_post.related_post"  style='border: none; border-radius: 5px;' class='card p-2 post-container card-default'>
      <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto; display: flex;"><RouterLink :to='`/user/${i.email}`'><img v-if="i.profile_picture === null" loading="lazy" src="../pictures/profile.png" class="img-circle small-thumbnail" /><img v-else loading="lazy" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${i.profile_picture}`" class='img-circle small-thumbnail'></RouterLink><span @mouseenter="showChanneInfo(i.id)"  @mouseleave="hideChannelInfo(i.id)" class="fs-6 m-2">{{reduceNameLength(i.name)}}<i style="height: 15px; display:none; width:15px; background-color: rgb(28, 121, 252); font-weight: bold; color: white; border-radius: 50%;" class="far fa-check-circle"></i></span> <ul class='inline-flex'>
                    <li style="font-size: 10px;color:lightslategrey; margin-top:12px;" class='list-unstyled'>{{moment(i.created_at).fromNow()}}</li>
                    </ul></span><BlockReportUserComponent :post_owner="i.email" :post_id="i.postid" />
                   </div>
                   <RouterLink :to='`/status/${i.postid}`'><p style="word-wrap: break-word; white-space:pre-wrap;" v-html="url_to_link(checkIfFriendPostIsLong(replaceHashTagWithLink(i.caption)))"  class='p-2 fs-6'></p></RouterLink> 
   
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
                   
         
    </div>
  
    <span v-if="new_friend_post.loader==='true'" class="text-bold cursor-pointer fs-4"><img style="margin:0px auto;" width="100px" height="100px" src="../landing/loading-loader.gif"></span>
<div  v-for="k in new_channel_post.fresh_channel_post" style='border: none; border-radius: 5px;' class='card  post-container card-default'>
    <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex  panel-header">
                    <span style="margin-right: auto; display: flex;"><RouterLink :to='`/channel/${k.email}`'><img v-if="k.avatar===null" loading="lazy" src="../pictures/profile.png" class="img-circle small-thumbnail"/><img v-else loading="lazy" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${k.avatar}`" class='img-circle small-thumbnail'></RouterLink><span class='m-2'>{{reduceNameLength(k.name)}}<i style="height: 15px; width:15px; background-color: rgb(28, 121, 252); font-weight: bold; color: white; border-radius: 50%;" class="far fa-check-circle"></i><p style='font-size:12px;'>{{k.first_name + '\t' + k.last_name}}</p></span>   <ul class='inline-flex'>
                    <li style="font-size: 10px; margin-top:12px; color:lightslategray;" class='list-unstyled'>{{moment(k.date).fromNow()}}</li>
                    </ul></span><BlockReportUserComponent :post_owner="k.email" :post_id="k.postid" />
                   </div>
                   <RouterLink :to='`/status/${k.postid}`'><p style="white-space:pre-wrap; word-wrap: break-word;" v-html="url_to_link(checkIfFriendPostIsLong(replaceHashTagWithLink(k.caption)))"  class='p-2 fs-6'></p></RouterLink>
   
                    <div class="flex-img">
                        <img v-if="k.img_1 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${k.img_1}`' />
                        <img v-if="k.img_2 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${k.img_2}`' />
                        <img v-if="k.img_3 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${k.img_3}`' />
                        <img v-if="k.img_4 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${k.img_4}`' />
                    </div>
                    <div v-if="k.video != null" class="flex-video">
                        <VideoPlayerComponent style="width:100%;" :video_info="{
                            source:k.video
                        }"/>
                    </div>
                

                 <LikeShareComment :post_content="{
                    post_caption:k.caption,
                    post_owner_name:k.name,
                    post_owner_email:k.email,
                    post_owner_avatar:k.avatar,
                    post_image_one:k.img_1,
                    post_image_two:k.img_2,
                    post_image_three:k.img_3,
                    post_image_four:k.img_4,
                    post_video:k.video,
                    post_is_comment_status:k.isReply,
                    post_likes_count:k.likes,
                    post_comments_count:k.comments,
                    post_shares_count:k.shares
                    
                  }" :post_owner="k.email"  :post_id="k.postid"  />
                 
         
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
    margin-top: 40px;
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
    height:100%;
   
    
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
    margin-top: 20px;
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
    height:100%;
   
    
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
    margin-top: 40px;
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
    height: 100%;
   
    
}
.card{
    padding:0px;
    margin-top:10px;
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
