<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import OldLikeShareComment from "../component/OldLikeShareComment.vue";
import UserPostSettings from "../component/UserPostSettings.vue";
import VideoPlayerComponent from "../component/VideoPlayerComponent.vue";
import moment from 'moment';
import store from "../store";
import { ref,reactive, onMounted } from "vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
const user_mail=localStorage.getItem('USER_MAIL');
let info=reactive({
    info_value:"false",
    coverbgVal:"false",
    bgUrl:"",
    cover_text:""
});
let personal_info=reactive({
        u_first_name:first_name,
        u_last_name:last_name,
        u_location:location,
        u_phone_number:phone,
        u_incomplete:incomplete,
        u_profile_pic:user_profile_pic
     });
let coverbgVal=false;
var cover_bg;
var cover_photo;
var first_name;
var last_name;
var location;
var phone;
var incomplete;
var user_profile_pic;
axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
       info.info_value="true";
       localStorage.setItem('INCOMPLETE',info.info_value);
       incomplete=localStorage.getItem('INCOMPLETE');
       personal_info.u_incomplete=localStorage.getItem('INCOMPLETE');
    }else{
       info.info_value="false";
       
        localStorage.setItem('FIRSTNAME',response.data.first_name);
        localStorage.setItem('LASTNAME',response.data.last_name);
        localStorage.setItem('LOCATION',response.data.location);
        localStorage.setItem('PHONE',response.data.phone_number);
        localStorage.setItem('PICTURE',response.data.profile_picture);
        localStorage.setItem('COVER_PHOTO',response.data.coverPhoto);
        localStorage.setItem('COVER_TEXT',response.data.coverText);
        if(response.data.coverPhoto===null){
            info.coverbgVal="false";
            cover_bg="";
            cover_photo="";
            info.bgUrl='';
            
        }else{
            info.coverbgVal="true";
            cover_bg=localStorage.getItem('COVER_PHOTO');
            cover_photo=`https://res.cloudinary.com/fishfollowers/image/upload/${cover_bg}`;
            info.bgUrl='url('+cover_photo+')';
            if(response.data.coverText==null){
               info.cover_text="I am a Uhangout member...";
               
            }else{
                
               info.cover_text=localStorage.getItem('COVER_TEXT');
            }
            
            
           
        }
        personal_info.u_first_name=localStorage.getItem('FIRSTNAME');
        personal_info.u_last_name=localStorage.getItem('LASTNAME');
        personal_info.u_location=localStorage.getItem('LOCATION');
        personal_info.u_phone_number=localStorage.getItem('PHONE');
        personal_info.u_incomplete=localStorage.getItem('INCOMPLETE');
        user_profile_pic=localStorage.getItem('PICTURE');
        
        
        
     

    }
    
})).catch((error =>{
    alert('Network Error...');
}))
       
      
const goto=useRouter();
function gotoEdit(){
    goto.push({
        name:"Edit"
    });
}
function gotoProfileHeader(){
    goto.push({
        name:"EditHeader"
    })
}
function gotoSupport(){
    alert("Please Contact Support");
}
const router=useRouter();
function gotoChannels(){
    router.push({
        name:"Channel"
    });
}
let profile_post=reactive({
thoughts:"",
replies:""
});
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
document.title='Profile';
});
function reduceNameLength(name){
    if(name.length > 14){
        let reduced_name=name.slice(0,14) + "..";
        return reduced_name;
    }else{
        return name;
    }
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
<template>h
    <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
   <div  class="container user-profile">
    <div :style="{backgroundImage:info.bgUrl}" class="images p-4">
        <span style="word-wrap: break-word;" class="fs-5">{{ info.cover_text }}</span>
        <div class="edit-profile d-flex justify-content-flex-start">
            <button @click="gotoProfileHeader" class="btn edit-btn btn-sm btn-success customize-button font-bold">Customize</button>
        </div>
        <div class="user-pic-name">

            <img v-if="personal_info.u_incomplete === 'true'" src="../landing/blondie.jpg" class="user-profile-img" />
            <img v-else-if="user_profile_pic === 'null' || user_profile_pic === null || user_profile_pic===undefined" src="../pictures/profile.png" class="user-profile-img" />
            <img v-else :src="`https://res.cloudinary.com/fishfollowers/image/upload/v1722105000/${user_profile_pic}`" class="user-profile-img" />
            <span style="text-shadow: none; text-align:center;" class="fs-4  text-black text-bold bold">{{ personal_info.u_first_name }}</span>
        </div>  
    </div>
    <div class="user-info-card">        
        <div class="bg-white shadow-sm user-info-card">
            <div class="heading-edit-btn">
                <div class="heading p-2">
                <!--<h2 class="fs-5 p-2 user-info-title">Your information 
                    <small style="display: block;" class="helper-text ">Update your  details ere.</small></h2>
                -->
                </div>
                <div style="margin-top:50px;" class="edit-btn p-2">
                    <button v-if="personal_info.u_incomplete === 'true'" class="btn btn-default font-bold fs-5 edit btn-sm" @click="gotoEdit">Edit</button>
                    <button v-else class="btn btn-default edit btn-sm font-bold" @click="gotoSupport">Modify</button>
                </div>
            </div>
            <div class="all-user-info p-2">
                <div v-if="personal_info.u_incomplete === 'true'" class="incomplete-profile">
                    <h2 class="fs-4 font-bold text-bold text-center">Your profile is not visible to other users.</h2>
                    <p class="text-center m-3">Click on Edit at the right side to add your info and the green Customize button to update your photo and cover text</p>
                </div>
                <div v-else class="complete-profile" style="margin-top:0px;">
                   <!-- <ul>
                    <li class="d-flex justify-content-flex-start"><span class="title">First Name</span><span>{{personal_info.u_first_name}}</span></li>
                    <li class="d-flex justify-content-flex-start"><span class="title">Last Name</span><span>{{personal_info.u_last_name}}</span></li>
                    <li class="d-flex justify-content-flex-start"><span class="title">Location</span><span>{{personal_info.u_location}}</span></li>
                    <li class="d-flex justify-content-flex-start"><span class="title">Phone Number</span><span>{{personal_info.u_phone_number}}</span></li>
                </ul>-->
                </div>
            </div>
        </div>
        <div style="border-radius: 5px;" class="user-activity shadow-sm card">
        <ul class="activity-link">
            <li @click="hideReplies" id="active_menu" class="list-unstyled thought-link">Thoughts</li>
            <li @click="showReplies" id="reply_menu" class="list-unstyled">Replies</li>
            <li @click="gotoChannels" class="list-unstyled">Channel</li>
        </ul>
        <div v-if="profile_post.thoughts === ''" class="spinner p-2"></div>
        <div v-else  class="all-activity-info-div">
            <div id="thoughts" class='thoughts'>
            <div :id="'post'+x.postid" style="border:none;" class="card p-2 card-default m-2" v-for="x in profile_post.thoughts">
            <div  style="background-color: rgba(255, 255, 255, 0.634);" class='card-header inline-flex  panel-header'>
        <span class="d-flex" style="margin-right: auto; "><img v-if="x.profile_picture === null" loading="lazy" src="../pictures/profile.png" class='img-circle small-thumbnail'><img v-else loading="lazy" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`" class='img-circle small-thumbnail'><span class='m-2'>{{reduceNameLength(x.name)}}</span></span><UserPostSettings :post_id="x.postid"  class="m-2 cursor-pointer"  />
            </div>
   <p style="white-space:pre-wrap;" v-html="url_to_link(x.caption)" class='p-2 fs-6'></p>
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
    <ul class='inline-flex'>
       <li class='list-unstyled'>{{moment(x.created_at).fromNow()}}</li>
    </ul>
    </div>
    </div>
    <div id="replies" class='replies'>
        <div v-if="profile_post.replies === ''" class='spinner p-2'></div>
        <div v-else v-for="i in profile_post.replies"  style='border: none; border-radius: 5px;' class='m-2 card p-2 post-container card-default' :id="'post'+i.postid">
      <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto; display: flex;"><RouterLink :to='`/user/${i.email_of_user_who_shared}`'><img v-if="i.profile_picture === null" loading="lazy" src="../pictures/profile.png" class="img-circle small-thumbnail"/><img v-else loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' class='img-circle small-thumbnail'></RouterLink><span class='m-2'>{{reduceNameLength(i.name_of_user_who_shared)}}</span></span><UserPostSettings :post_id="i.postid"  class="m-2 cursor-pointer"  />
                    <span :id="i.id" style="position: absolute; top: 40%; visibility: hidden; font-size: 12px; right: 45%; word-wrap: break-word;  z-index: 1; display: block; width: 120px; background-color: black; border-radius: 6px; padding: 5px 0; color: white; text-align: center;">{{ i.channel_bio }} <br /><br /><i>"This user makes money from channels, launch your channel and get paid like them.."</i>   </span>
                   </div>
                   <RouterLink :to='`/status/${i.postid}`'><p style="word-wrap: break-word; white-space:pre-wrap;"   class='p-2 fs-6' v-html="checkIfFriendPostIsLong(url_to_link(i.quote))"></p></RouterLink>
                    <div class="card">
                    <RouterLink :to='`/user/${i.email}`'><h5 class="m-2">{{reduceNameLength(i.name)}}</h5></RouterLink>
                    <RouterLink :to='`/status/${i.postid}`'><p class="m-2" style="word-wrap: break-word; white-space: pre-wrap;" v-html="checkIfFriendPostIsLong(url_to_link(i.caption))"></p></RouterLink>
                    <div class="flex-img">
                        <img v-if="i.post_img1 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img1}`' />
                        <img v-if="i.post_img2 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img2}`' />
                        <img v-if="i.post_img3 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img3}`' />
                        <img v-if="i.post_img4 != null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.post_img4}`' />
                    </div>
                    <div v-if="i.video != null" class="flex-video">
                        <VideoPlayerComponent style="max-width:100%;" :video_info="{
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
    </div>
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
    margin-top:0px;
    padding:0px;
    position: relative;
    
}
.images{
    border-radius: 0px;
    background-image: linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
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
    border:3px solid rgb(254, 213, 238);
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
    font-size:12px;
    font-weight:400;
    margin-left:5px;
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
    top:80%;
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
.replies{
    display:none;
}
.customize-button{
    position:absolute;
    top:145px;
    right:10px;
    z-index:1;
    border-radius:20px;
    padding:5px 5px;
    width:100px;
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
    margin-top:80px;
}
.images{
    border-radius: 10px;
    background-image: linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
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
    font-size:12px;
    font-weight:400;
    margin-left:5px;
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
    margin-top:0px;
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
.replies{
    display:none;
}
.customize-button{
    position:absolute;
    top:145px;
    right:10px;
    z-index:1;
    border-radius:20px;
    padding:5px 5px;
    width:100px;
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
    margin-top:80px;
    position: relative;
}
.images{
    border-radius: 10px;
    background-image: linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
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
    font-size:12px;
    font-weight:400;
    margin-left:5px;
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
    height:100%;
   
    
}
.customize-button{
    position:absolute;
    top:145px;
    right:10px;
    z-index:1;
    border-radius:20px;
    padding:5px 5px;
    width:100px;
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