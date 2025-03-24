<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import OldLikeShareComment from "../component/OldLikeShareComment.vue";
import BlockReportUserComponent from "../component/BlockReportUserComponent.vue";
import ProfileSkeletonLoader from "../component/ProfileSkeletonLoader.vue";
import PostSkeletonLoader from "../component/PostSkeletonLoader.vue";
import ImageSliderForPost from "../component/ImageSliderForPost.vue";
import store from "../store";
import { ref,reactive,onMounted } from "vue";
import axiosClient from "../axios";
import {useRoute, useRouter } from "vue-router";
import { nanoid } from 'nanoid'
import moment from 'moment'
const route=useRoute();
const router=useRouter();
const user_mail=atob(route.params.info);
const user_id=route.params.info;
const current_user=localStorage.getItem('USER_MAIL');
let info=reactive({
    info_value:true,
    coverbgVal:"false",
    bgUrl:"",
    cover_text:"",
    isBlocked:"",
    isUserFollowed:"",
    isFollowPending:""
});
onMounted(()=>{
let formData=new FormData();
formData.append("current_user",current_user);
formData.append("user_who_blocked",user_mail);
axiosClient.post("/isUserBlocked",formData).then(response=>{
info.isBlocked=response.data.reply;
}).catch(error=>{
console.log(error);
});
});
onMounted(()=>{
let formData=new FormData();
formData.append("current_user",current_user);
formData.append("user_to_follow",user_mail);
axiosClient.post("/isUserFollowed",formData).then(response=>{
info.isUserFollowed=response.data.reply;
info.isFollowPending=response.data.isPending;
console.log(info.isFollowPending);
}).catch(error=>{
console.log(error);
});
});
let personal_info=reactive({
        u_first_name:"",
        u_last_name:"",
        u_location:"",
        u_phone_number:"",
        u_profile_pic:"",
        u_followers_count:"",
        u_isPrivate:""
     });

let coverbgVal=false;
var cover_bg;
var cover_photo;

axiosClient.post("/profile",{email:user_mail}).then((response=>{
  
       info.info_value=false;
     
       
            info.coverbgVal="true";
            cover_bg=response.data.coverPhoto;
            if(cover_bg === null){
            }else{
                cover_photo=`https://res.cloudinary.com/fishfollowers/image/upload/${cover_bg}`;
            
                info.bgUrl='url('+cover_photo+')';
            }
            
            if(response.data.coverText==null){
               info.cover_text="I am an Hexarex.com User...";
               
            }else{
                
               info.cover_text=response.data.coverText;
            }
            
            
           
        
        personal_info.u_first_name=response.data.first_name;
        personal_info.u_last_name=response.data.last_name;
        personal_info.u_location=response.data.location;
        personal_info.u_profile_pic=response.data.profile_picture;
        personal_info.u_isPrivate=response.data.isPrivate;
        
        
     

    
    
})).catch((error =>{
    alert('Network Error...');
}));
onMounted(()=>{
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findUserPost",formData).then(response=>{
profile_post.thoughts=response.data.reply;
}).catch(error=>{
console.log(error);
});
});
onMounted(()=>{
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findFollowersCount",formData).then(response=>{
    personal_info.u_followers_count=response.data.followers;
}).catch(error=>{
    console.log(error);
});
});
let profile_post=reactive({
thoughts:"",
replies:""
});
function gotoChat(){
    let $reciever=atob(route.params.info);
    let $sender=localStorage.getItem('USER_MAIL');
    let $unique_id = nanoid();
    let formData=new FormData();
    console.log($sender);
    formData.append("sender",$sender);
    formData.append("reciever",$reciever);
    formData.append("unique_id",$unique_id);
    axiosClient.post("/convo",formData).then(response=>{
        let $id=response.data.reply;
        
       window.location.href='/chat/'+$id;
     
    }).catch(e=>{
        alert("Something wrong happened");
    })
    
  
}
function showReplies(){
    document.getElementById("thoughts").style.display="none";
    document.getElementById("replies").style.display="block";
    document.getElementById("active_menu").style.borderBottom="0px";
    document.getElementById("reply_menu").style.borderBottom="2px solid magenta";
    
    let formData=new FormData();
    formData.append("user_email",user_mail);
    axiosClient.post("/findAllUserReply",formData).then(response=>{
       profile_post.replies=response.data.reply;
    }).catch(error=>{
        console.log(error);
    });
    
}
function hideReplies(){
    document.getElementById("thoughts").style.display="block";
    document.getElementById("replies").style.display="none";
    document.getElementById("active_menu").style.borderBottom="2px solid magenta";
    document.getElementById("reply_menu").style.borderBottom="0px";
}
function reduceNameLength(name){
    if(name.length > 14){
        let reduced_name=name.slice(0,14) + "..";
        return reduced_name;
    }else{
        return name;
    }
}
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
function followUser(){
    axiosClient.post("/choice",{
        user:current_user,
        choice:user_mail
    }).then(response=>{
        info.isUserFollowed='true';
        document.getElementById("follow-btn").setAttribute("disabled","true");
    }).catch(e=>{
        let error="You already tried following this user, we have notified them";
        alert(error);
    });
}
function requestToFollowUser(){
    axiosClient.post("/request",{
        current_user:current_user,
        choice:user_mail
    }).then(response=>{
        info.isFollowPending="true";
    }).catch(e=>{
        let error="You already tried following this user, we have notified them";
        alert(error);
    });
}
function unfollowUser(){
    axiosClient.post("/removeChoice",{
        user:current_user,
        choice:user_mail
    }).then(response=>{
        info.isUserFollowed='false';
    }).catch(e=>{
        let error="You already  unfollowed this user";
        alert(error);
    });
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
function formatNumber(num) {
  if (num >= 1e9) {
    return (num / 1e9).toFixed(1) + 'B'; // Billions
  } else if (num >= 1e6) {
    return (num / 1e6).toFixed(1) + 'M'; // Millions
  } else if (num >= 1e3) {
    return (num / 1e3).toFixed(1) + 'K'; // Thousands
  } else {
    return num.toString(); // Less than 1000
  }
}
</script>
<template>
     <Header class="shadow-sm" style="background-color:white; position:fixed; padding-bottom:10px;  width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" v-if="store.state.user.token != null" />
    <ProfileSkeletonLoader v-if="info.info_value" />
    <div v-else-if="info.isBlocked === 'true'" class="isBlocked"  > 
        <h2  class="m-4 text-center">You are Blocked from Seeing <span style="font-weight:bold;">{{ personal_info.u_first_name}} {{ personal_info.u_last_name }}</span> Posts,  this user has prevent you from reaching them.</h2>
    </div>
   <div style="position: relative;" v-else class="container user-profile">
    <div :style="{backgroundImage:info.bgUrl}" class="images p-4">
        <span style="word-wrap: break-word;" class="fs-5">{{ info.cover_text }}</span>
        <div v-if="store.state.user.token != null" style="position:absolute; z-index:1; top:155px; right:20px;" class="d-flex justify-content-flex-start">
            <button style="margin-right:10px; color:black; border:1px solid black; border-radius:50px;"  @click="gotoChat" class="btn btn-default btn-sm "><i style="border-radius:5px;" class="fs-4 fa-light fa-envelope"></i></button>
            <button id="follow-btn" v-if="info.isUserFollowed === 'false' && user_mail!=current_user && personal_info.u_isPrivate != 'true' && info.isFollowPending==''" @click="followUser" style="border-radius:40px; width:100px; padding-top:0px; padding-bottom:0px; margin-bottom:5px; margin-top:2px;"  class="btn edit-btn btn-sm btn-success  font-bold">Follow</button>
            <button id="follow-btn" v-else-if="info.isUserFollowed === 'false' && user_mail!=current_user && personal_info.u_isPrivate == 'true' && info.isFollowPending==''" @click="requestToFollowUser" style="border-radius:40px; width:100px; padding-top:0px; padding-bottom:0px; margin-bottom:5px; margin-top:2px;"  class="btn edit-btn btn-sm btn-primary  font-bold">Request</button>
            <button v-else-if="info.isUserFollowed === 'false' && user_mail!=current_user && personal_info.u_isPrivate == 'true' && info.isFollowPending=='true'" @click="requestToFollowUser" style="border-radius:40px; width:100px; padding-top:0px; padding-bottom:0px; margin-bottom:5px; margin-top:2px; background-color:grey; border:grey; color:white;"  class="btn edit-btn btn-sm btn-default  font-bold">Pending</button>
            <button   v-else v-if="user_mail !=current_user" @click="unfollowUser" style="border-radius:40px; width:100px; padding-top:0px; padding-bottom:0px; margin-bottom:5px; margin-top:2px;"  class="btn edit-btn btn-sm btn-danger font-bold">Unfollow</button>

        </div>
        <div class="user-pic-name">
            <img v-if="personal_info.u_profile_pic === null" src="../pictures/profile.png" class="user-profile-img" />
            <img v-else :src="`https://res.cloudinary.com/fishfollowers/image/upload/${personal_info.u_profile_pic}`" class="user-profile-img" />
            <span style="text-shadow: none;" class="fs-4 text-black text-bold bold">{{ personal_info.u_first_name }}</span>
        </div>
       
    </div>
    <div class="user-info-card">
        <div class=" shadow-sm user-info-card ">
            <div class="all-user-info ">
                <div class="heading">
                
                </div>
                <div    class="complete-profile">
                <ul class="d-flex" style="margin-top:75px; cursor: pointer;">
                    <li class="m-2"><span class="title m-2"><i class="fa-light fa-location-dot"></i></span><span>{{personal_info.u_location}}</span></li>
                    <li class="m-2"><span class="title m-2"><i class="fa-solid fa-user"></i></span><RouterLink :to="`/followers/${user_id}`"><span class="font-bold text-black">{{formatNumber(personal_info.u_followers_count)}} </span>{{personal_info.u_followers_count < 2 ? "Follower" : "Followers"}}</RouterLink></li>
                </ul>
                </div>
            </div>
        <div v-if="personal_info.u_isPrivate != 'true' || info.isUserFollowed == 'true' " style="border-radius: 5px;" class="user-activity shadow-sm card">
        <ul class="activity-link">
            <li @click="hideReplies" id="active_menu" class="list-unstyled thought-link">Thoughts</li>
            <li @click="showReplies" id="reply_menu" class="list-unstyled">Replies</li>
            <li class="list-unstyled"><RouterLink :to="`/channel/${user_id}`">Channel</RouterLink></li>
        </ul>
        <div class="spinner" v-if="profile_post.thoughts === ''"></div>
        <div v-else  class="all-activity-info-div">
            <div id="thoughts" class='thoughts'>
            <div style="border:none;" class="card  card-default m-2" v-for="x in profile_post.thoughts">
            <div  style="background-color: rgba(255, 255, 255, 0.634);" class='card-header inline-flex  panel-header'>
        <span class="d-flex" style="margin-right: auto; "><img v-if="x.profile_picture === null" loading="lazy" src="../pictures/profile.png" class="img-circle small-thumbnail" /><img v-else loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`' class='img-circle small-thumbnail'><span class='m-2'>{{reduceNameLength(x.name)}} 
            <ul class='inline-flex' style="display:block;">
            <li class='list-unstyled' style="font-size:12px; color:gray;">{{moment(x.created_at).fromNow()}}</li>
            </ul>
</span></span><BlockReportUserComponent style="cursor: pointer;" :post_owner="x.email" :post_id="x.postid" />
            </div>
   <p v-html="url_to_link(x.caption)" style="white-space:pre-wrap;" class='p-2 fs-6'></p>
   <OldLikeShareComment :post_content="{
                    post_caption:x.caption,
                    post_owner_name:x.name,
                    post_owner_email:x.email,
                    post_owner_avatar:x.profile_picture,
                    post_is_comment_status:x.isReply,
                    post_likes_count:x.likes,
                    post_comments_count:x.comments,
                    post_shares_count:x.shares
                  }" :post_owner="x.email"    :post_id="x.postid" /> 
    </div>
    </div>
    <div id="replies" class='replies'>
        <PostSkeletonLoader v-if="profile_post.replies === ''" />
        <div v-else v-for="i in profile_post.replies"  style='border: none; border-radius: 5px;' class='m-2 card  post-container card-default'>
      <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto; display: flex;"><RouterLink :to='`/user/${i.email_of_user_who_shared}`'><img v-if="i.profile_picture === null" class="img-circle small-thumbnail" loading="lazy" src="../pictures/profile.png" /><img v-else loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' class='img-circle small-thumbnail'></RouterLink><span class='m-2'>{{reduceNameLength(i.name_of_user_who_shared)}}  
                    <ul class='inline-flex' style="display:block;">
                    <li style="font-size: 12px; color:gray; display:block;" class='list-unstyled'>{{moment(i.created_at).fromNow()}}</li>
                    </ul></span></span><BlockReportUserComponent style="cursor:pointer;" :post_owner="i.email" :post_id="i.postid" />
                    <span :id="i.id" style="position: absolute; top: 40%; visibility: hidden; font-size: 12px; right: 45%; word-wrap: break-word;  z-index: 1; display: block; width: 120px; background-color: black; border-radius: 6px; padding: 5px 0; color: white; text-align: center;">{{ i.channel_bio }} <br /><br /><i>"This user makes money from channels, launch your channel and get paid like them.."</i>   </span>
                   </div>
                   <RouterLink :to='`/status/${i.postid}`'><p style="word-wrap: break-word; white-space:pre-wrap;"   class='p-2 fs-6' v-html="checkIfFriendPostIsLong(url_to_link(i.quote))"></p></RouterLink>
                    <div class="card">
                    <RouterLink :to='`/user/${i.email}`'><h5 class="m-2 d-flex"><img v-if="i.avatar_of_original_poster==='' || i.avatar_of_original_poster===null" class="img-circle small-thumbnail" style="width:25px; height:25px;" src="../pictures/profile.png"/><img v-else class="img-circle small-thumbnail" style="width:25px; height:25px;" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.avatar_of_original_poster}`'/><span style="margin-top:2px; margin-left:5px;">{{reduceNameLength(i.name)}}</span></h5></RouterLink>
                    <RouterLink :to='`/status/${i.prev_id}`'><p class="m-2" style="word-wrap: break-word; white-space: pre-wrap;" v-html="checkIfFriendPostIsLong(url_to_link(i.caption))"></p></RouterLink>
                    <ImageSliderForPost
                        style="margin-top:0px;"
                        v-if="i.video === null && i.post_img1 !== null"
                        :user_email="i.email"
                        :postid="i.postid"
                        :images="[
                            i.post_img1 && `https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img1}`,
                            i.post_img2 && `https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img2}`,
                            i.post_img3 && `https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img3}`,
                            i.post_img4 && `https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img4}`
                        ].filter(Boolean)"
                    />
                    <div v-if="i.video != null" class="flex-video">
                        <video controls>
                            <source :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.video}#t=0.0010`' />
                        </video>
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
   </div>
   <div v-else class="d-flex justify-content-center  shadow-sm p-4 align-items-center">
    <h1 class="fs-6 font-bold"><i class="fa-light fs-4 fa-lock"></i> Only users who follow can see their contents..</h1>
   </div>
   </div>
</div>
</div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .user-profile{
    width: 100%;
    background-color: rgb(252, 252, 252);
    height:100%;
    padding:0px;
    position: relative;
    margin-top:60px;
    
}
.images{
    border-radius: 0px;
    background-color:rgba(147, 148, 148, 1);
    opacity: 0.8;
    color: white;
    height: 150px;
    font-size:12px;
    background-position: center center;
    background-repeat: no-repeat;
    background-size:100% 150px;
    background-attachment: scroll;
    text-shadow: 2px 2px 2px rgb(21, 21, 21);
    font-weight: bold;
    position:relative;
}
.user-profile-img{
    border-radius: 50%;
    height: 100px;
    width: 100px;
    object-position: center center;
    object-fit: cover;
    background-color:white;
    border:3px solid rgb(255, 255, 255);
}
.bold{
    font-weight: bold;
}
.edit-btn{
    margin-left: auto;
}
.edit-profile{
    box-sizing: border-box;
    position:absolute;
    bottom:0px;
    right:5%;
}
.user-info-card{
    margin-top: 0px;
    border-radius: 0px;
    

}
.heading-edit-btn{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.user-info-title{
    font-weight: bold;
    margin-top: 45px;
}
.helper-text{
    color:rgb(174, 174, 174);
}
.heading{
    display: flex;
    flex-direction: column;
}
.edit{
    color: blueviolet;
}
.title{
    margin-right: auto;
}
.complete-profile > ul > li{
    margin-top: 15px;
}
.user-activity{
    margin-top: 10px;
    border: none;
}
.activity-link{
    display: flex;
    justify-content:space-around;
    font-weight: bold;
    font-size:15px;
    cursor: pointer;

}

.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}
#active_menu{
    border-bottom: 2px solid rgb(255, 132, 255);
}
.user-info-title{
    margin-top: 100px;
}
.all-user-info{
    margin-top:0px;
}
.user-pic-name{
    position:absolute;
    top:100px;
}
.replies{
    display:none;
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
.isBlocked{
    margin-top:200px;
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
    .user-profile{
    width: 80%;
    background-color: rgb(252, 252, 252);
    height: 100%;
    margin:0 auto;
    margin-top:70px;
}
.images{
    border-radius: 10px;
    background-color:rgba(147, 148, 148, 1);
    opacity: 0.8;
    color: white;
    height: 150px;
    font-size: 15px;
    background-position: center center;
    background-repeat: no-repeat;
    background-size:100% 150px;
    background-attachment: scroll;
    text-shadow: 2px 2px 2px rgb(21, 21, 21);
    font-weight: bold;
}
.user-profile-img{
    border-radius: 50%;
    height: 150px;
    width: 150px;
    object-position: center center;
    object-fit: cover;
}
.bold{
    font-weight: bold;
}
.edit-btn{
    margin-left: auto;
    margin-top: 10px;
}
.edit-profile{
    box-sizing: border-box;
}
.user-info-card{
    margin-top: 50px;
    border-radius: 5px;
    

}
.heading-edit-btn{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.user-info-title{
    font-weight: bold;
    margin-top:155px;
}
.helper-text{
    color:rgb(174, 174, 174);
}
.heading{
    display: flex;
    flex-direction: column;
}
.edit{
    color: blueviolet;
}
.title{
    margin-right: auto;
}
.complete-profile > ul > li{
    margin-top: 15px;
}
.user-activity{
    margin-top: 10px;
    border: none;
}
.activity-link{
    display: flex;
    justify-content:space-around;
    font-weight: bold;
    font-size:25px;
    cursor: pointer;

}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}
#active_menu{
    border-bottom: 2px solid rgb(255, 132, 255);
}
.user-info-title{
    margin-top: 100px;
}
.all-user-info{
    margin-top:30px;
}
.replies{
    display:none;
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
.isBlocked{
    margin-top:200px;
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
    .user-profile{
    width: 60%;
    background-color: rgb(252, 252, 252);
    height: 100%;
    margin:0 auto;
    margin-top:70px;
}
.images{
    border-radius: 10px;
   background-color:rgba(147, 148, 148, 1);
    opacity: 0.8;
    color: white;
    height: 150px;
    background-position: center center;
    background-repeat: no-repeat;
    background-size:100% 400px;
    background-attachment: scroll;
    font-size: 15px;
    
}
.user-profile-img{
    border-radius: 50%;
    height:150px;
    width:150px;
    object-position: center center;
    object-fit: cover;
}
.bold{
    font-weight: bold;
}
.edit-btn{
    margin-left: auto;
}
.edit-profile{
    box-sizing: border-box;
}
.user-info-card{
    margin-top: 50px;
    border-radius: 5px;
    

}
.heading-edit-btn{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.user-info-title{
    font-weight: bold;
    margin-top:155px;
}
.helper-text{
    color:rgb(174, 174, 174);
}
.heading{
    display: flex;
    flex-direction: column;
}
.edit{
    color: blueviolet;
}
.title{
    margin-right: auto;
}
.complete-profile > ul > li{
    margin-top: 15px;
}
.user-activity{
    margin-top: 10px;
    border:none;
}
.activity-link{
    display: flex;
    justify-content:space-around;
    font-weight: bold;
    font-size:25px;
    cursor: pointer;

}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}
#active_menu{
    border-bottom: 2px solid rgb(255, 132, 255);
}
.user-info-title{
    margin-top: 100px;
}
.all-user-info{
    margin-top:0px;
}
.replies{
    display:none;
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
.isBlocked{
    margin-top:200px;
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