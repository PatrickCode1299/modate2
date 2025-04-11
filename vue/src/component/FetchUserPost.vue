<script setup>
import store from '../store';
import axiosClient from '../axios';
import { ref } from 'vue';
import { reactive,onMounted,computed } from 'vue';
import { defineProps } from 'vue';
import moment from 'moment'
import { RouterLink,useRouter } from 'vue-router';
import LikeShareComment from "./LikeShareComment.vue";
import BlockReportUserComponent from './BlockReportUserComponent.vue';
import PostSkeletonLoader from './PostSkeletonLoader.vue';
import CreateTextPostComponent from './CreateTextPostComponent.vue';
const latest_post=defineProps(['latest']);
const router=useRouter();
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
function showlatest(){
    newest_post.date=latest_post.latest.date;
    newest_post.name=latest_post.latest.name;
    newest_post.avatar=latest_post.latest.avatar;
    newest_post.caption=latest_post.latest.caption;
    newest_post.postid=latest_post.latest.postid;
    newest_post.post_owner=latest_post.latest.email;
    
    
}
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


onMounted(async()=>{
    const response=  await axiosClient.post('/fetchAllPost',{email:user_mail}).catch(e=>{
        console.log(e);
    });
    keep_all_post.value=response.data.reply;
    
    
    
});
onMounted(()=>{
    
    let isFetching = false; // Flag to prevent multiple requests

window.onscroll = function() {
    const threshold = 0.5; // 90% of the page height
    
    if (!isFetching && (window.scrollY + window.innerHeight) / document.body.scrollHeight >= threshold) {
        isFetching = true; // Set the flag to true to indicate a request is in progress
        new_friend_post.loader = 'true';
        
        axiosClient.post('/fetchRandomPost', { email: user_mail })
            .then(response => {
                let raw_data = response.data.reply;
                raw_data.forEach(x => {
                    new_friend_post.fresh_new_post.push(x);
                });
            })
            .then(() => {
                new_friend_post.loader = 'false';
            })
            .catch(e => {
                console.log(e);
            })
            .finally(() => {
                isFetching = false; // Reset the flag once the request is complete
            });
    }
};
Loader.value=true;
});

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
<div class="stories-and-div-container">
<div class="user-post-holder">
    {{showlatest()}}

    <div v-if="newest_post.name != null" id="user-post"  class='card m-2 p-2 post-container card-default'>
    <div style="background-color: rgba(255, 255, 255, 0.634);" class='card-header inline-flex  panel-header'>
        <span style="margin-right: auto; display:flex;"><img loading="lazy" v-if="newest_post.avatar === 'null'" src="../pictures/profile.png" class="img-circle small-thumbnail" /><img v-else loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${newest_post.avatar}`' class='img-circle small-thumbnail'><span class='m-2'>{{reduceNameLength(newest_post.name)}}<ul class='inline-flex'>
        <li class='list-unstyled ' style='font-size:12px; color:lightslategray;'>{{moment(newest_post.date).fromNow()}}</li>
    </ul></span></span><span @click="deleteUserPost(newest_post.postid)">Delete</span>
    </div>
    <p v-if="newest_post.isLong=='true'"   class='p-2 fs-6'>{{ newest_post.caption }}</p>
    <p v-if="newest_post.isLong==''"  class='p-2 fs-6'>{{ newest_post.caption }}</p>
    <button @click="expandText" v-if="newest_post.isLongBtn == 'true'">Show More</button>
    <p v-if="newest_post.isLong=='false'">{{newest_post.caption}}</p>
    <button @click="reduceText" v-if="newest_post.isLongBtn == 'false'">Show Less</button>
    <LikeShareComment :post_content="{
                    post_caption:newest_post.caption,
                    post_owner_name:newest_post.name,
                    post_owner_email:newest_post.post_owner,
                    post_owner_avatar:newest_post.avatar,
                    post_image_one:null,
                    post_image_two:null,
                    post_image_three:null,
                    post_image_four:null,
                    post_video:null,
                    post_likes_count:null,
                    post_comments_count:null,
                    post_shares_count:null,
                    post_is_comment_status:'', 
                  }"  :post_owner="newest_post.post_owner"  :post_id="newest_post.postid" />

    
   </div>
   <PostSkeletonLoader v-if="keep_all_post.length === 0" style="width:80%;"/>

    <div v-if="keep_all_post.length !==0"  v-for="x in keep_all_post"  class=' card  post-container card-default'>
    <div style="background-color: rgba(255, 255, 255, 0.634);" class='card-header inline-flex p-2 panel-header'>
        <span style="margin-right: auto; display:flex;"><RouterLink :to='`/user/${x.email}`'><img loading="lazy" v-if="x.profile_picture === null" src="../pictures/profile.png" class="img-circle small-thumbnail" /><img v-else loading="lazy" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${x.profile_picture}`" class='img-circle small-thumbnail'></RouterLink><span class='m-2'>{{reduceNameLength(x.name)}} <ul class='inline-flex'>
        <li style="font-size: 12px; color:lightslategray;" class='list-unstyled'>{{moment(x.created_at).fromNow()}}</li>
    </ul></span></span><BlockReportUserComponent :post_owner="x.email" :post_id="x.postid" />
    </div>
    <RouterLink :to='`/status/${x.postid}`'><p style="white-space:pre-wrap;" v-html="url_to_link(checkIfFriendPostIsLong(replaceHashTagWithLink(x.caption)))"  class='p-2 fs-6'></p></RouterLink>
   
    <p v-if="friend_post.show_current_key === x.created_at">{{friend_post.expandText }}</p>
    <LikeShareComment :post_content="{
                    post_caption:x.caption,
                    post_owner_name:x.name,
                    post_owner_email:x.email,
                    post_owner_avatar:x.profile_picture,
                    post_image_one:x.post_img1,
                    post_image_two:x.post_img2,
                    post_image_three:x.post_img3,
                    post_image_four:x.post_img4,
                    post_video:x.video,
                    post_is_comment_status:x.isReply,
                    post_likes_count:x.likes,
                    post_comments_count:x.comments,
                    post_shares_count:x.shares
                  }" :post_owner="x.email"    :post_id="x.postid" />
   </div> 

    <span v-if="new_friend_post.loader==='true'" class="text-bold cursor-pointer fs-4"><img style="margin:0px auto;" width="100px" height="100px" src="../landing/loading-loader.gif"></span>
    <div   v-for="j in new_friend_post.fresh_new_post" style='border: none; border-radius: 5px;' class='card  post-container card-default'>
    <div style="background-color: rgba(255, 255, 255, 0.634);" class='card-header inline-flex p-2 panel-header'>
        <span style="margin-right: auto; display: flex;"><RouterLink :to='`/user/${j.email}`'><img v-if="j.avatar===null" loading="lazy" src="../pictures/profile.png" class="img-circle small-thumbnail"><img v-else loading="lazy" :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${j.avatar}`" class='img-circle small-thumbnail'></RouterLink><span class='m-2'>{{reduceNameLength(j.name)}} <ul class='inline-flex'>
        <li style="font-size: 12px; color:lightslategray;" class='list-unstyled'>{{moment(j.date).fromNow()}}</li>
    </ul></span></span><BlockReportUserComponent :post_owner="j.email" :post_id="j.postid" />
    </div>
    <RouterLink :to='`/status/${j.postid}`'><p style="white-space:pre-wrap;" v-html="url_to_link(checkIfFriendPostIsLong(replaceHashTagWithLink(j.caption)))" class='p-2 fs-6'></p></RouterLink>

    
    <LikeShareComment :post_content="{
                    post_caption:j.caption,
                    post_owner_name:j.name,
                    post_owner_email:j.email,
                    post_owner_avatar:j.avatar,
                    post_image_one:j.img1,
                    post_image_two:j.img2,
                    post_image_three:j.img3,
                    post_image_four:j.img4,
                    post_video:j.video,
                    post_is_comment_status:j.isReply,
                    post_likes_count:j.likes,
                    post_comments_count:j.comments,
                    post_shares_count:j.shares
                  }" :post_owner="j.email"  :post_id="j.postid"  />
   
   </div>
</div> 
<CreateTextPostComponent />
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
    border:none;
    border-bottom:1px solid lightgrey;
    border-radius:0px;
}
.card{
    width: 100%;
    margin-top:10px;
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
    border:none;
    border-bottom:1px solid lightgrey;
    border-radius:0px;
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
    border:none;
    border-bottom:1px solid lightgrey;
    border-radius:0px;
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
