<script setup>
import store from '../store';
import axiosClient from '../axios';
import { ref } from 'vue';
import { reactive,onMounted,computed } from 'vue';
import { defineProps } from 'vue';
import moment from 'moment'
import { RouterLink, useRoute } from 'vue-router';
import LikeShareComment from "./LikeShareComment.vue";
import Header from "./Header.vue";
import OldLikeShareComment from "./OldLikeShareComment.vue";
import BlockReportUserComponent from './BlockReportUserComponent.vue';
import VideoPlayerComponent from './VideoPlayerComponent.vue';
const latest_post=defineProps(['latest']);
const route=useRoute();
let post_id=route.params.postid;
const user_mail=localStorage.getItem('USER_MAIL');
var post_date;
var poster_name;
var post_avatar;
var post_caption;
let store_all_post=reactive({
    post_date:"",
    poster_name:"",
    post_avatar:"",
    post_caption:"",
    isLoading:"true",


});
let newest_post=reactive({
    date:"",
    name:"",
    avatar:"",
    caption:"",
    postid:"",
    isLong:"",
    isLongBtn:"",
    textShortener:"",
    expandLongPost:""

});

function checkIfUserPostIsLong(text){
    if(text==null){
        return;
    }else if(text.length < 128){
        return newest_post.caption;
    }
    else if(text.length > 128){
        newest_post.isLong="true";
        newest_post.isLongBtn="true";
        newest_post.textShortener=newest_post.caption.slice(0,128) + "............";
        return newest_post.textShortener;
    }
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
    }else if(text.length < 400){
        return text;
    }
    else if(text.length > 400){
        let caption_new=text.slice(0,400) + "....See More";
        return caption_new;
    }
}


function expandText(){
   newest_post.expandLongPost=newest_post.caption.slice(0);
   newest_post.isLong='false';
   newest_post.isLongBtn='false';

   //return newest_post.textShortener;
}
function reduceText(){
        newest_post.expandLongPost=newest_post.caption.slice(0,128)+ "...............";
        newest_post.isLong='true';
        newest_post.isLongBtn='true';
}

let all_post=reactive({
    first_five_post:"",
    new_five_post:"",
    first_post_name:"",
    one_channel_post:"",
    shared_post:"",
    every_one_post:"",
    post_id:["patrick"]
})
let keep_all_post=ref('');

store_all_post.post_date=localStorage.getItem('POST_DATE');
store_all_post.poster_name=localStorage.getItem('POSTER_NAME');
store_all_post.post_avatar=localStorage.getItem('POST_AVATAR');
store_all_post.post_caption=localStorage.getItem('POST_CAPTION');
let isFetching = false; // Flag to prevent multiple API calls
const buffer = 300; 
onMounted(async()=>{
   const response= await axiosClient.post('/findSharedStatus',{post_id:post_id}).catch(e=>{
        console.log(e);
    });
    all_post.shared_post=response.data.reply;
});
onMounted(()=>{
    
    let isFetching = false; // Flag to prevent multiple requests

window.onscroll = function() {
    const threshold = 0.5;
    if (!isFetching && (window.scrollY + window.innerHeight) / document.body.scrollHeight >= threshold) {
        new_friend_post.loader='true';
        isFetching = true;
        axiosClient.post('/findSharedStatus',{post_id:post_id}).then(response=>{
        let raw_data=response.data.reply;
        raw_data.forEach(x => {
            new_friend_post.fresh_new_post.push(x);
            
        });
        
        
        }).then(e=>{
            new_friend_post.loader='false';
        }).
        catch(e=>{
        console.log(e);
        }).finally(()=>{
        isFetching = false;
        });
       }
    }

Loader.value="false";
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

</script>
<template>
 <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" /><div class="stories-and-div-container" Ss>
<div class="user-post-holder" style="margin-top:40px;">
   <span v-if="Loader==='true'" class="text-bold spinner cursor-pointer fs-4"></span>
   <div v-if="all_post.shared_post != null" v-for="i in all_post.shared_post"  style=' border-radius: 5px;' class=' card  post-container card-default'>
      <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto; display: flex;"><RouterLink :to='`/user/${i.email_of_user_who_shared}`'><img v-if="i.profile_picture === null" loading="lazy" src="../pictures/profile.png" class="img-circle small-thumbnail"/><img v-else loading="lazy" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${i.profile_picture}`" class='img-circle small-thumbnail'></RouterLink><span class='m-2'>{{reduceNameLength(i.name_of_user_who_shared)}}<ul class='inline-flex'>
                    <li style="font-size: 12px; color:lightslategray;" class='list-unstyled'>{{moment(i.created_at).fromNow()}}</li>
                    </ul></span></span><BlockReportUserComponent :post_owner="i.email" :post_id="i.postid" />
                    <span :id="i.id" style="position: absolute; top: 40%; visibility: hidden; font-size: 12px; right: 45%; word-wrap: break-word;  z-index: 1; display: block; width: 120px; background-color: black; border-radius: 6px; padding: 5px 0; color: white; text-align: center;">{{ i.channel_bio }} <br /><br /><i>"This user makes money from channels, launch your channel and get paid like them.."</i>   </span>
                   </div>
                   <RouterLink :to='`/status/${i.postid}`'><p style="word-wrap: break-word; white-space:pre-wrap;" v-html="url_to_link(checkIfFriendPostIsLong(i.quote))"  class='p-2 fs-6'></p></RouterLink>
                    <div class="card" style="margin-left:5px; margin-right:5px;">
                    <RouterLink :to='`/user/${i.email}`'><h5 class="m-2 d-flex"><img v-if="i.avatar_of_original_poster==='' || i.avatar_of_original_poster===null" class="img-circle small-thumbnail" style="width:25px; height:25px;" src="../pictures/profile.png"/><img v-else class="img-circle small-thumbnail" style="width:25px; height:25px;" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${i.avatar_of_original_poster}`"/><span style="margin-top:2px; margin-left:5px;">{{reduceNameLength(i.name)}}</span></h5></RouterLink>
                    <RouterLink :to='`/status/${i.prev_id}`'><p class="m-2" style="word-wrap: break-word; white-space: pre-wrap;" v-html="url_to_link(checkIfFriendPostIsLong(replaceHashTagWithLink(i.caption)))"></p></RouterLink>
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
                    post_caption:i.quote,
                    post_owner_name:i.name_of_user_who_shared,
                    post_owner_email:i.email_of_user_who_shared,
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
                  }" :post_owner="i.email_of_user_who_shared" :post_id="i.postid" />
                    
         
    </div>
    <div v-if="all_post.shared_post != null" v-for="i in new_friend_post.fresh_new_post"  style=' border-radius: 5px;' class=' card  post-container card-default'>
      <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto; display: flex;"><RouterLink :to='`/user/${i.email_of_user_who_shared}`'><img v-if="i.profile_picture === null" loading="lazy" src="../pictures/profile.png" class="img-circle small-thumbnail"/><img v-else loading="lazy" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${i.profile_picture}`" class='img-circle small-thumbnail'></RouterLink><span class='m-2'>{{reduceNameLength(i.name_of_user_who_shared)}}<ul class='inline-flex'>
                    <li style="font-size: 12px; color:lightslategray;" class='list-unstyled'>{{moment(i.created_at).fromNow()}}</li>
                    </ul></span></span><BlockReportUserComponent :post_owner="i.email" :post_id="i.postid" />
                    <span :id="i.id" style="position: absolute; top: 40%; visibility: hidden; font-size: 12px; right: 45%; word-wrap: break-word;  z-index: 1; display: block; width: 120px; background-color: black; border-radius: 6px; padding: 5px 0; color: white; text-align: center;">{{ i.channel_bio }} <br /><br /><i>"This user makes money from channels, launch your channel and get paid like them.."</i>   </span>
                   </div>
                   <RouterLink :to='`/status/${i.postid}`'><p style="word-wrap: break-word; white-space:pre-wrap;" v-html="url_to_link(checkIfFriendPostIsLong(i.quote))"  class='p-2 fs-6'></p></RouterLink>
                    <div class="card" style="margin-left:5px; margin-right:5px;">
                    <RouterLink :to='`/user/${i.email}`'><h5 class="m-2 d-flex"><img v-if="i.avatar_of_original_poster==='' || i.avatar_of_original_poster===null" class="img-circle small-thumbnail" style="width:25px; height:25px;" src="../pictures/profile.png"/><img v-else class="img-circle small-thumbnail" style="width:25px; height:25px;" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${i.avatar_of_original_poster}`"/><span style="margin-top:2px; margin-left:5px;">{{reduceNameLength(i.name)}}</span></h5></RouterLink>
                    <RouterLink :to='`/status/${i.prev_id}`'><p class="m-2" style="word-wrap: break-word; white-space: pre-wrap;" v-html="url_to_link(checkIfFriendPostIsLong(replaceHashTagWithLink(i.caption)))"></p></RouterLink>
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
                    post_caption:i.quote,
                    post_owner_name:i.name_of_user_who_shared,
                    post_owner_email:i.email_of_user_who_shared,
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
                  }" :post_owner="i.email_of_user_who_shared" :post_id="i.postid" />
                    
         
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
