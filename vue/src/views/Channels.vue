<script setup>
import axios from "axios";
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import OldLikeShareComment from "../component/OldLikeShareComment.vue";
import UserPostSettings from "../component/UserPostSettings.vue";
import store from "../store";
import moment from 'moment';
import axiosClient from "../axios.js";
import { useRouter,useRoute } from "vue-router";
import { onMounted,onBeforeUpdate, onBeforeMount, watch, ref, reactive, onUpdated } from "vue";
import CreateChannelPost from "../component/CreateChannelPost.vue";
import VideoPlayerComponent from "../component/VideoPlayerComponent.vue";
import ImageSliderForPost from "../component/ImageSliderForPost.vue";
import AdvertButtonComponent from "../component/AdvertButtonComponent.vue";
import ChannelVideo from "../component/ChannelVideo.vue";
import ChannelPhoto from "../component/ChannelPhoto.vue";
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
    channel_subscribers:"",
    owner_last_seen:""
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
let user_picture;
function setUpChannel(){
    router.push({
        name:"channel_create"
    });
}

onMounted(()=>{
    if(route.params.uid != null){
        user_mail=atob(route.params.uid);
        axiosClient.post("/checkIfUserHasChannel",{email:user_mail}).then((response=>{
    if(response.data.reply==="false"){
       info.user_has_channel="false";
      
       
    }else{
        info.user_has_channel="true";
        channel_data.channel_name=response.data.channel_data.channel_name;
        channel_data.channel_category=response.data.channel_data.channel_category;
        channel_data.channel_bio=response.data.channel_data.channel_bio;
        if(response.data.subscribers > 1){
            channel_data.channel_subscribers=response.data.subscribers + "\tSubscribers";
        }else{
            channel_data.channel_subscribers=response.data.subscribers + "\tSubscriber";
        }
        

    }

})).catch((error =>{
    console.log(error);
}))
    }else{
        user_mail=localStorage.getItem('USER_MAIL');
    axiosClient.post("/checkIfUserHasChannel",{email:user_mail}).then((response=>{
    if(response.data.reply==="false"){
       info.user_has_channel="false";
      
       
    }else{
        info.user_has_channel="true";
        channel_data.channel_name=response.data.channel_data.channel_name;
        channel_data.channel_category=response.data.channel_data.channel_category;
        channel_data.channel_bio=response.data.channel_data.channel_bio;
        if(response.data.subscribers > 1){
            channel_data.channel_subscribers=response.data.subscribers + "\tSubscribers";
        }else{
            channel_data.channel_subscribers=response.data.subscribers + "\tSubscriber";
        }
        

    }

})).catch((error =>{
    console.log(error);
}))
    }
    
});

onMounted(() =>{
    user_mail=localStorage.getItem('USER_MAIL');
    axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
       info.info_value="true";
       localStorage.setItem('INCOMPLETE',info.info_value);
       
    }else{
        channel_data.owner_last_seen=response.data.last_seen;
        
    }

})).catch((error =>{
    console.log(error);
}))
});
onMounted(()=>{
    if(route.params.uid != null){
    user_mail=atob(route.params.uid);
    let formData=new FormData();
    formData.append('email',user_mail);
    axiosClient.post("/findChannelPost",formData).then((response=>{
        response.data.reply.forEach(post => {
            channel_post.old_channel_post.push(post);
          
        });
      user_picture=channel_post.old_channel_post[0].profile_picture;
      let last_post = channel_post.old_channel_post.at(-1);
      channel_post.last_post_date=last_post.created_at;
        
   
})).catch((error =>{
    console.log(error);
    const interval = setInterval(() => {
        if (navigator.onLine) {
            clearInterval(interval);
            location.reload();
        }
    }, 5000);
}))
    }else{
    user_picture=localStorage.getItem('PICTURE');
    user_mail=localStorage.getItem('USER_MAIL');
    let formData=new FormData();
    formData.append('email',user_mail);
    axiosClient.post("/findChannelPost",formData).then((response=>{
        response.data.reply.forEach(post => {
            channel_post.old_channel_post.push(post);
       
          
        });
        let last_post = channel_post.old_channel_post.at(-1);
        channel_post.last_post_date=last_post.created_at;
        console.log(channel_post.last_post_date);
})).catch((error =>{
    console.log(error);
}))
    }
    


});
onUpdated(() =>{
    let isFetching = false;
    const x=channel_post.old_channel_post.length;
    if(x  < 9){
     return;
 
}else{
if(channel_gallery.isPhotoActive===true){
window.onscroll=function (){

const threshold = 0.5;
if (!isFetching && (window.scrollY + window.innerHeight) / document.body.scrollHeight >= threshold) {
isFetching = true;
 axiosClient.post('/fetchRandomChannelPost',{email:user_mail,recent_date:channel_post.last_post_date}).then(response=>{
 let raw_data=response.data.reply;
 raw_data.forEach(x => {
     new_channel_post.fresh_new_post.push(x);
    
 });
let recent_post=new_channel_post.fresh_new_post.at(-1);
channel_post.last_post_date=recent_post.date;
 
 }).catch(e=>{
 console.log(e);
 }).finally(()=>{
        isFetching = false;
});
}
}
}
}
});

let channel_text=ref('');



function checkIfPostIsLong(text, key){
     if(text==null){
        return;
    }else if(text.length < 128){
        channel_post.short_post.push(key);
        return text;
    }
    else if(text.length > 128){
        let caption_new=text.slice(0,128) + "...........................................";
        channel_post.post_key.push(key);
        
        return caption_new;
    }
}
function expandChannelText(key, text){
    channel_post.post_key.filter((x)=>{
        if(x==key){
            channel_post.show_current_key=key;
            channel_post.expandText=text;
            channel_post.isShortBtn=key;
            channel_post.current_key_is_enabled=key;
            let get_current_long_post_btn=document.getElementById(key);
            get_current_long_post_btn.style.display="none";
        }
    })

}
function reduceChannelText(key, text){
    channel_post.post_key.filter((x)=>{
        if(x==key){
            channel_post.show_current_key=key;
            channel_post.expandText=channel_post.expandText.slice(0,128) + "............";
            channel_post.isShortBtn='';
            let get_current_long_post_btn=document.getElementById(key);
            get_current_long_post_btn.style.display="block";
        }
    })

}
function deletePost(post_id, post_caption,post_img1,post_img2,post_img3,post_img4,user_video){
    var ask_if_user_wants_to_delete_post=confirm("Do you want to Delete This Post?");
    if(ask_if_user_wants_to_delete_post){
    console.log(user_video);
        user_mail=localStorage.getItem('USER_MAIL');
    let formData=new FormData();
    formData.append('email',user_mail);
    formData.append('post_id',post_id);
    formData.append('caption',post_caption);
    formData.append('image1',post_img1);
    formData.append('image2',post_img2);
    formData.append('image3',post_img3);
    formData.append('image4',post_img4);
    formData.append('video',user_video);
    axiosClient.post("/deleteChannelPost",formData).then(response=>{
        let deleteContainer=document.getElementById(post_id);
        deleteContainer.style.display="none";
    }).catch(error=>{
        console.log(error);
    });
    }else{
        return;
    }
  
}
let imgLoader=ref('true');
onUpdated(()=>{
imgLoader.value='false';
});
let currentDiv=reactive({
current_div_id:"",
current_div_status:""
});
let channel_gallery=reactive({
    photos:"",
    videos:"",
    isPhotoActive:true,
    isVideoActive:false
});
onMounted(()=>{
if(route.params.uid != null){
user_mail=atob(route.params.uid);
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findAllChannelPhotos",formData).then(response=>{
channel_gallery.photos=response.data.reply;
}).catch(error=>{
console.log(error);
});
axiosClient.post("/findAllChannelVideos",formData).then(response=>{
channel_gallery.videos=response.data.reply;
}).catch(error=>{
console.log(error);
});
   
}else{
user_mail=localStorage.getItem('USER_MAIL');
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/findAllChannelPhotos",formData).then(response=>{
channel_gallery.photos=response.data.reply;
}).catch(error=>{
console.log(error);
});
axiosClient.post("/findAllChannelVideos",formData).then(response=>{
channel_gallery.videos=response.data.reply;
}).catch(error=>{
console.log(error);
});
}

});
function showOptionsList(parent_elem,parent_name){
let elem=parent_elem+parent_name;
let hidden_list=document.getElementById(elem);
hidden_list.style.display="block";


}
function hideDiv(parent_elem,parent_name){
let elem=parent_elem+parent_name;
let hidden_list=document.getElementById(elem);
hidden_list.style.display="none";

}
function showAllPhotos(){
    let text_content=document.getElementById("text-content");
    if(text_content=== null){
    let all_channel_content=document.querySelectorAll(".all_channel_content");
    let channel_photo_gallery=document.getElementById("channel_photo_gallery");
    channel_gallery.isPhotoActive=true;
    channel_photo_gallery.style.display="grid";
    for( var i=0; i<=all_channel_content.length; i++){
       all_channel_content[i].style.display="none";
    }
    }else{
        text_content.style.display="none";
        let all_channel_content=document.querySelectorAll(".all_channel_content");
        let channel_photo_gallery=document.getElementById("channel_photo_gallery");
        channel_gallery.isPhotoActive=true;
        channel_photo_gallery.style.display="grid";
    for( var i=0; i<=all_channel_content.length; i++){
       all_channel_content[i].style.display="none";
    }
    }

    
}
function showAllPosts(){
    let text_content=document.getElementById("text-content");
    if(text_content===null){
    let all_channel_content=document.querySelectorAll(".all_channel_content");
    let channel_photo_gallery=document.getElementById("channel_photo_gallery");
    channel_photo_gallery.style.display="none";
    for( var i=0; i<=all_channel_content.length; i++){
       all_channel_content[i].style.display="block";
    }
    }else{
        text_content.style.display="block";
        let all_channel_content=document.querySelectorAll(".all_channel_content");
        let channel_photo_gallery=document.getElementById("channel_photo_gallery");
    channel_photo_gallery.style.display="none";
    for( var i=0; i<=all_channel_content.length; i++){
       all_channel_content[i].style.display="block";
    }
    }
   
}
function showAllVideos(){
    let text_content=document.getElementById("text-content");
    if(text_content=== null){
    let all_channel_content=document.querySelectorAll(".all_channel_content");
    let channel_photo_gallery=document.getElementById("channel_photo_gallery");
    channel_gallery.isPhotoActive=false;
    console.log(channel_gallery.isPhotoActive);
    channel_photo_gallery.style.display="grid";
    for( var i=0; i<=all_channel_content.length; i++){
       all_channel_content[i].style.display="none";
    }
    }else{
        text_content.style.display="none";
        let all_channel_content=document.querySelectorAll(".all_channel_content");
        let channel_photo_gallery=document.getElementById("channel_photo_gallery");
        channel_gallery.isPhotoActive=false;
        console.log(channel_gallery.isPhotoActive);
        channel_photo_gallery.style.display="grid";
    for( var i=0; i<=all_channel_content.length; i++){
       all_channel_content[i].style.display="none";
    }
    }
}
function reduceNameLength(name){
    if(name.length > 14){
        let reduced_name=name.slice(0,14) + "..";
        return reduced_name;
    }else{
        return name;
    }
}
onMounted(()=>{
document.title='Channel';
});
function gotoPost(postid){
    router.push(`/status/${postid}`);
    
}
function checkIfUserPostIsLong(text){
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
function replaceHashTagWithLink(text) {
    return (text || '').replace(/#(\w+)/g, function (match, tag) {
  return `<a style='color:#1DA1F2;' href="/related/${tag}">${match}</a>`;
});
}
</script>
<template>
   <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
    
    
    <div style="margin-top: 100px;" v-if="info.info_value==='true'" class="incomplete d-flex justify-content-center">
        <h2 class="fs-5 m-4 text-black font-bold">Update your <RouterLink style="color:magenta;" to="/profile">profile</RouterLink> to use channels</h2>
    </div>
    <div v-else  class="channel-container">
        <span v-if="info.user_has_channel ==''" class="spinner"></span>
        <div   v-if="info.user_has_channel==='true'">
            <div class="d-flex justify-content-flex-start channel-info-holder">
                <img class="channel-img" v-if="user_picture === null || user_picture === 'null' || user_picture === undefined" src="../pictures/profile.png"/>
                <img v-else  class="channel-img" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${user_picture}`' />
                <div class="block-channel-info">
                    <h2 class="m-2 fs-2 font-bold">{{ channel_data.channel_name }} </h2>
                    <p style="margin-bottom: 0px;" class="m-2 text-grey"><small>{{ channel_data.channel_category }}</small></p>
                    <h6 class="m-2 fs-6"><span >{{channel_data.channel_subscribers}}</span></h6>
                    <p class="m-2" style='word-wrap:break-word;'>{{ channel_data.channel_bio }}</p>
                </div>
            </div>
            <div v-if="!route.params.uid" class="btn-group">
                <button style="border-radius: 50px;"    class="btn btn-success p-2  fs-6 btn-sm  m-2"><small class="p-2">Customize Channel</small></button>
                <button style="border-radius: 50px;"    class="btn btn-success p-2  fs-6 btn-sm  m-2"><small class="p-2">Advertisement</small></button>
            </div>
            <div class="channel-contents">
                <ul style="margin-bottom:0px;" class="d-flex justify-content-space-between">
                    <li @click="showAllPosts" class="m-2 fs-6 text-black font-bold">Posts</li>
                    <li @click="showAllPhotos" class="m-2 fs-6 text-black font-bold">Photos</li>
                    <li @click="showAllVideos" class="m-2 fs-6 text-black font-bold">Videos</li>
                </ul>
                <div style="margin-top:0px;" v-if="!route.params.uid" id="text-content" class="display-content">
                    <CreateChannelPost :channel_name="channel_data.channel_name"/>
                </div>
                <div v-if="info.user_has_channel==='true'" id="channel_photo_gallery" class="channel_photo_gallery">
                    <div v-if="channel_gallery.isPhotoActive === true" v-for="p in channel_gallery.photos">
                        <img @click="gotoPost(p.postid)" style="width:150px; border-radius:5px; height: 150px;" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${p.post_img1}`' />
                    </div>
                    <div  v-if="channel_gallery.isPhotoActive === false" v-for="p in channel_gallery.videos" style="position:relative;">
                        <button @click="()=>{document.getElementById('video'+p.postid).play()}" style="width:50px; height:50px; position:absolute; top:40px; left:40px; border-radius:50px;" class="btn btn-success  btn-md"><i class="fa-solid fa-play"></i></button>
                        <video  @click="gotoPost(p.postid)" :id="'video'+p.postid" style="width:150px; object-fit:cover; border-radius:5px; height: 150px;">
                            <source :src='`https://res.cloudinary.com/fishfollowers/video/upload/${p.video}#t=0.0010`'/>
                        </video>
                    </div>
                 </div>
                <div  v-if="channel_post.old_channel_post.length===0">
                <h2 class="text-center font-bold">You don't have any content on your channel, try creating.</h2>
                </div>
                <div  v-else class=" card all_channel_content card-default" v-for="x in channel_post.old_channel_post" :id="'post'+x.postid" style=" margin:0px auto; margin-top: 20px;">
                   <div style="position: relative; width:100%; background-color: rgba(255, 255, 255, 0.634);" class="card-header d-flex inline-flex p-2 panel-header">
                    <span style="margin-right: auto;" class="d-flex">
                    <RouterLink to='/profile'>
                    <img v-if="x.profile_picture === null" class="img-circle small-thumbnail" src="../pictures/profile.png" />
                    <img v-else :src='`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`' class='img-circle small-thumbnail'>
                    </RouterLink>
                    <ul style='margin-top:10px; margin-left:10px; color:lightslategray; font-size:13px;' class='inline-flex'>
                    <li style=" width: 100%;" class='d-flex  list-unstyled'><span style="margin-right: auto;">{{moment(x.created_at).fromNow()}}</span></li>
                    </ul></span><span v-if="!route.params.uid" class='m-2'><UserPostSettings :post_id="x.postid" /><span class="m-2">{{reduceNameLength(x.name)}}</span></span>
                   </div>
                    <p style="word-wrap: break-word; white-space:pre-wrap;" v-if="channel_post.current_key_is_enabled != x.created_at"  class='p-2 fs-6' v-html="url_to_link(checkIfUserPostIsLong(replaceHashTagWithLink(x.caption)))"></p>
   
                    <p style="word-wrap: break-word;" v-if="channel_post.show_current_key === x.created_at">{{channel_post.expandText }}</p>
                    <ImageSliderForPost
                        style="margin-top:0px;"
                        v-if="x.video === null && x.post_img1 !== null"
                        :user_email="x.email"
                        :postid="x.postid"
                        :images="[
                            x.post_img1 && `https://res.cloudinary.com/fishfollowers/image/upload/${x.post_img1}`,
                            x.post_img2 && `https://res.cloudinary.com/fishfollowers/image/upload/${x.post_img2}`,
                            x.post_img3 && `https://res.cloudinary.com/fishfollowers/image/upload/${x.post_img3}`,
                            x.post_img4 && `https://res.cloudinary.com/fishfollowers/image/upload/${x.post_img4}`
                        ].filter(Boolean)"
                    />
                    <div v-if="x.video != null" class="flex-video">
                        <VideoPlayerComponent style="max-width:100%;" :video_info="{
                            source:x.video
                        }"/>
                    </div>
                    <OldLikeShareComment :post_content="{
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
                    <AdvertButtonComponent :channel_info="{
                        subscriber_count:channel_data.channel_subscribers,
                        last_seen:channel_data.owner_last_seen,
                        postid:x.postid
                    }
                    " :post_data="{
                        name:x.name,
                        caption:x.caption,
                        profile_picture:x.profile_picture,
                        post_img1:x.post_img1,
                        post_img2:x.post_img2,
                        post_img3:x.post_img3,
                        post_img4:x.post_img4,
                        postid:x.postid,
                        video:x.video
                    }"/>
                </div>
                </div>
                <div :id="'post'+k.postid" class="m-2 card all_channel_content card-default" style="position: relative;" v-for="k in new_channel_post.fresh_new_post">
                    <div style=" background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto;" class="d-flex">
                    <RouterLink to='/profile'>
                    <img v-if="k.avatar === null" class="img-circle small-thumbnail" src="../pictures/profile.png"> 
                    <img v-else :src='`https://res.cloudinary.com/fishfollowers/image/upload/${k.avatar}`' class='img-circle small-thumbnail'>
                    </RouterLink>
                    <ul class='inline-flex' style="margin-top:10px; margin-left:10px; font-size:13px; color:lightslategray;">
                    <li style=" width: 100%;" class='d-flex justify-content-space-between list-unstyled'><span style="margin-right: auto;">{{moment(k.date).fromNow()}}</span></li>
                    </ul></span><span v-if="!route.params.uid" class='m-2'><UserPostSettings :post_id="k.postid" />{{reduceNameLength(k.name)}}</span>
                   </div>
                    <p style="word-wrap: break-word; white-space:pre-wrap;" v-if="channel_post.current_key_is_enabled != k.date"  class='p-2 fs-6' v-html="url_to_link(checkIfUserPostIsLong(replaceHashTagWithLink(k.caption)))"></p>
   
                    <p style="word-wrap: break-word;" v-if="channel_post.show_current_key === k.date">{{channel_post.expandText }}</p>
                    <ImageSliderForPost
                        style="margin-top:0px;"
                        v-if="k.video === null && k.post_img1 !== null"
                        :user_email="k.email"
                        :postid="k.postid"
                        :images="[
                            k.post_img1 && `https://res.cloudinary.com/fishfollowers/image/upload/${k.post_img1}`,
                            k.post_img2 && `https://res.cloudinary.com/fishfollowers/image/upload/${k.post_img2}`,
                            k.post_img3 && `https://res.cloudinary.com/fishfollowers/image/upload/${k.post_img3}`,
                            k.post_img4 && `https://res.cloudinary.com/fishfollowers/image/upload/${k.post_img4}`
                        ].filter(Boolean)"
                    />
                    <div v-if="k.video != null" class="flex-video">
                        <VideoPlayerComponent style="max-width:100%;" :video_info="{
                            source:k.video
                        }"/>
                    </div>
                    <OldLikeShareComment :post_content="{
                    post_caption:k.caption,
                    post_owner_name:k.name,
                    post_owner_email:k.email,
                    post_owner_avatar:k.profile_picture,
                    post_image_one:k.post_img1,
                    post_image_two:k.post_img2,
                    post_image_three:k.post_img3,
                    post_image_four:k.post_img4,
                    post_video:k.video,
                    post_is_comment_status:k.isReply,
                    post_likes_count:k.likes,
                    post_comments_count:k.comments,
                    post_shares_count:k.shares
                  }" :post_owner="k.email"    :post_id="k.postid" />
                    <AdvertButtonComponent :channel_info="{
                        subscriber_count:channel_data.channel_subscribers,
                        last_seen:channel_data.owner_last_seen,
                        postid:k.postid
                    }
                    " :post_data="{
                        name:k.name,
                        caption:k.caption,
                        profile_picture:k.avatar,
                        post_img1:k.post_img1,
                        post_img2:k.post_img2,
                        post_img3:k.post_img3,
                        post_img4:k.post_img4,
                        postid:k.postid,
                        video:k.video
                    }"/>
                </div>
                </div>
        <div   style="margin-top:50px;"  v-else v-if="info.user_has_channel != ''" class="justify-content-center align-items-center">
        <div style="flex:1;" v-if="!route.params.uid">
        <h2 class="text-center fs-2 font-bold">Welcome to Channels</h2>
        <p  class="p-2">With Channels you can make money by serving users your content, 
            how does this work?
            Create any content you like and we would convert your followers or matches to your subscribers..<br />
            Through Channels you gain more engagement by representing your specilization and you are able to post photos and video content..
            We would also disburse you a certain amount based on our Ads revenue sharing Policy..
            
        </p>
        <div class="m-2 w-full"><button style="width: 100%;" @click="setUpChannel" class="btn btn-md btn-block btn-success">Launch Channel</button></div>
        </div>
        <div v-else>
            {{router.push({
                name:"Home"
            })}}
        </div>
        </div>
      
    
    </div>

</template>
<style scoped>
@media screen and (min-width:320px) {
    .channel-container{
    display: flex;
    flex-direction: column;
    justify-content:center;
    width:100%;
    cursor: pointer;
    margin-top:60px;
   
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
    width: 50%;
    margin:0px auto;
    margin-top:40px;
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
    width:100%;
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
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 50%;
    margin:0px auto;
    margin-top:40px;
}
.channel-info{
    margin: 0px auto; 
    margin-top:50px;
    width: 80%;
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
    width:100%;
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