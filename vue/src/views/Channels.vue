<script setup>
import axios from "axios";
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import StoriesandPost from "../component/StoriesandPost.vue";
import store from "../store";
import moment from 'moment';
import axiosClient from "../axios.js";
import { useRouter } from "vue-router";
import { onMounted,onBeforeUpdate, onBeforeMount, watch, ref, reactive, onUpdated } from "vue";
import CreateChannelPost from "../component/CreateChannelPost.vue";

let user_mail;
const router=useRouter();
let info=reactive({
    info_value:"false",
    user_has_channel:"",
});
let channel_data=reactive({
    channel_name:"",
    channel_owner:"",
    channel_bio:"",
    channel_avatar:"",
    channel_category:"",
    channel_cover:""
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
    current_key_is_enabled:""
});
let new_channel_post=reactive({
    fresh_new_post:[

    ]
});
function setUpChannel(){
    router.push({
        name:"channel_create"
    });
}
onMounted(() =>{
    user_mail=store.state.user.data;
   axiosClient.post('/checkIfUserHasPaid', {data:user_mail})
        .then(response => {
         const payment_status=response.data.user_status;
         if(payment_status=='true'){
            return;
         }else{
     /** 
          router.push({
            name:'Payment'
           }); */
       } 
        }).catch(error => {
            console.error('Error',error);
        });
});
onMounted(()=>{
    user_mail=store.state.user.data;
    axiosClient.post("/checkIfUserHasChannel",{email:user_mail}).then((response=>{
    if(response.data.reply==="false"){
       info.user_has_channel="false";
      
       
    }else{
        info.user_has_channel="true";
        channel_data.channel_name=response.data.channel_data.channel_name;
        channel_data.channel_category=response.data.channel_data.channel_category;
        channel_data.channel_bio=response.data.channel_data.channel_bio;

    }

})).catch((error =>{
    console.log(error);
}))
});

onMounted(() =>{
    user_mail=store.state.user.data;
    axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
       info.info_value="true";
       sessionStorage.setItem('INCOMPLETE',info.info_value);
       
    }

})).catch((error =>{
    console.log(error);
}))
});
onMounted(()=>{
    user_mail=sessionStorage.getItem('USER_MAIL');
    let formData=new FormData();
    formData.append('email',user_mail);
    axiosClient.post("/findChannelPost",formData).then((response=>{
        response.data.reply.forEach(post => {
            channel_post.old_channel_post.push(post);
          
        });
   
   
})).catch((error =>{
    console.log(error);
}))
window.onscroll=function (){

       if(window.scrollY + window.innerHeight >= document.body.scrollHeight){
        axiosClient.post('/fetchRandomChannelPost',{email:user_mail}).then(response=>{
        let raw_data=response.data.reply;
        raw_data.forEach(x => {
            new_channel_post.fresh_new_post.push(x);
           
        });
       // console.log(raw_data);
      //  new_friend_post.fresh_new_post.push(response.data.reply);
        
        
        }).catch(e=>{
        console.log(e);
        });
       }
}
});
const user_picture=sessionStorage.getItem('PICTURE');
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
function deletePost(post_id, post_caption){
    var ask_if_user_wants_to_delete_post=confirm("Do you want to Delete This Post.");
    if(ask_if_user_wants_to_delete_post){
        user_mail=sessionStorage.getItem('USER_MAIL');
    let formData=new FormData();
    formData.append('email',user_mail);
    formData.append('post_id',post_id);
    formData.append('caption',post_caption);
    axiosClient.post("/deleteChannelPost",formData).then(response=>{
        console.log(response.data.reply);
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

</script>
<template>
    <Header />
    <SideNav />
    
    
    <div v-if="info.info_value==='true'" class="incomplete d-flex justify-content-center">
        <h2 class="fs-3 m-4 text-black font-bold">You need to complete your profile to use channels</h2>
    </div>
    <div v-else  class="channel-container">
        <div v-if="info.user_has_channel==='true'">
            <div class="d-flex justify-content-flex-start">
                <img class="channel-img" :src='`http://localhost:8000/storage/${user_picture}`' />
                <div class="block-channel-info">
                    <h2 class="m-2 fs-2 font-bold">{{ channel_data.channel_name }} </h2>
                    <small class="m-2">{{ channel_data.channel_category }}</small>
                    <h6 class="m-2 fs-6"><span class="text-grey">1000 subscribers</span></h6>
                    <p class="m-2">{{ channel_data.channel_bio }}</p>
                </div>
            </div>
            <div class="m-2 btn-group">
                <button class="btn btn-secondary fs-6 btn-sm rounded m-2">Customize Channel</button>
                <button class="btn btn-secondary fs-6 btn-sm rounded m-2">Manage Content</button>
            </div>
            <div class="channel-contents">
                <ul class="d-flex justify-content-space-between">
                    <li class="m-2 fs-6 text-black font-bold">Posts</li>
                    <li class="m-2 fs-6 text-black font-bold">Photos</li>
                    <li class="m-2 fs-6 text-black font-bold">Videos</li>
                </ul>
                <div id="text-content" class="display-content">
                    <CreateChannelPost :channel_name="channel_data.channel_name"/>
                </div>
                <div v-if="channel_post.old_channel_post.length===0">
                <h2 class="text-center font-bold">You don't have any content on your channel, try creating.</h2>
                </div>
                <div v-else class="m-2 card card-default" v-for="x in channel_post.old_channel_post" style=" margin:0px auto; margin-top: 20px;">
                   <div style=" background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto;"><RouterLink to='/profile'><img :src='`http://localhost:8000/storage/${x.profile_picture}`' class='img-circle small-thumbnail'></RouterLink></span><span class='m-2'>{{x.name}}</span>
                   </div>
                    <p style="word-wrap: break-word;" v-if="channel_post.current_key_is_enabled != x.created_at"  class='p-4 fs-6'>{{checkIfPostIsLong(x.caption, x.created_at)}}</p>
   
                    <p style="word-wrap: break-word;" v-if="channel_post.show_current_key === x.created_at">{{channel_post.expandText }}</p>

                   <button :id="x.created_at" v-if="!channel_post.short_post.includes(x.created_at) " @click="expandChannelText(x.created_at, x.caption)">Show More</button>
                    <button  v-if="channel_post.isShortBtn === x.created_at " @click="reduceChannelText(x.created_at, x.caption)">Show Less</button>
                    <ul class='inline-flex'>
                    <li style=" width: 100%;" class='d-flex justify-content-space-between list-unstyled'><span style="margin-right: auto;">{{moment(x.created_at).fromNow()}}</span><span style="margin-left: auto; " @click="deletePost(x.id,x.caption)"><i class="fa fa-trash"></i></span></li>
                    </ul>
                </div>
                </div>
                <div class="m-2 card card-default" v-for="k in new_channel_post.fresh_new_post">
                    <div style=" background-color: rgba(255, 255, 255, 0.634);" class="card-header inline-flex p-2 panel-header">
                    <span style="margin-right: auto;"><RouterLink to='/profile'><img :src='`http://localhost:8000/storage/${k.avatar}`' class='img-circle small-thumbnail'></RouterLink></span><span class='m-2'>{{k.name}}</span>
                   </div>
                    <p style="word-wrap: break-word;" v-if="channel_post.current_key_is_enabled != k.date"  class='p-4 fs-6'>{{checkIfPostIsLong(k.caption, k.date)}}</p>
   
                    <p style="word-wrap: break-word;" v-if="channel_post.show_current_key === k.date">{{channel_post.expandText }}</p>

                   <button :id="k.created_at" v-if="!channel_post.short_post.includes(k.date) " @click="expandChannelText(k.date, k.caption)">Show More</button>
                    <button  v-if="channel_post.isShortBtn === k.date " @click="reduceChannelText(k.date, k.caption)">Show Less</button>
                    <ul class='inline-flex'>
                    <li style=" width: 100%;" class='d-flex justify-content-space-between list-unstyled'><span style="margin-right: auto;">{{moment(k.date).fromNow()}}</span><span style="margin-left: auto; " @click="deletePost(k.id,k.caption)"><i class="fa fa-trash"></i></span></li>
                    </ul>
                </div>
         
                </div>
               
        <div v-else class=" channel-info justify-content-center align-items-center">
        
        <div>
            <img width="100%" height="auto" class="img-responsive" src="../pictures/hand 2.jpg">
        </div>
        <div>
        <h2 class="text-center fs-2 font-bold">Welcome to Channels</h2>
        <p class="p-2">With Channels you can make money by serving users your content, 
            how does this work?
            Create any content you like and we would convert your followers / matches to your subscribers..<br />
            We would allow you charge your subscribers, let say you have a 100 followers / match we automatically
            turn them into your subscribers and then show your content to only those who have paid..
            
        </p>
        <div class="m-2 w-full"><button style="width: 100%;" @click="setUpChannel" class="btn btn-md btn-block btn-success">Launch Channel</button></div>
        </div>
        </div>
        <!--
        <div class="active-channel-container">
            <h2 class="fs-2 font-bold">Channels you follow</h2>
            <p>You automatically get subscribed to channels of people you matched with unless you unmatched</p>
        </div>
        <div class="active-channel-container">
            <h2 class="fs-2 font-bold">Active Channels</h2>
            <p>This channels just added a recent post..</p>
        </div> -->
    </div>

</template>
<style scoped>
@media screen and (min-width:320px) {
    .channel-container{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    width:97%;
    cursor: pointer;
   
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
}
.create_text{
    resize: none;
    color: black;
    font-weight: 400;
}
.display-content{
    
    
  
    z-index: 1;
    padding-right: 10px;
    border-radius: 10px;
    background-color: rgba(255,255,255,0.4);
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
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
    padding-left: 5px;
    padding-right: 5px;
    width: 100%;
}
}
@media screen and (min-width:620px) {
    .channel-container{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
}
.channel-info{
    margin: 0px auto; 
    width: 60%;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
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
}
.create_text{
    resize: none;
    color: black;
    font-weight: 400;
}
.display-content{
    
    
 
    z-index: 1;
    top: 30%;
    padding-top: 20px;
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 20px;
    border-radius: 10px;
    background-color: rgba(255,255,255,0.4);
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
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
    width: 400px;
}
}
@media screen and (min-width:1224px) {
    .channel-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
    margin:0 auto;
}
.channel-info{
    margin: 0px auto; 
    width: 60%;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
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
}
.create_text{
    resize: none;
    color: black;
    font-weight: 400;
}
.display-content{
    
   
    padding-top: 20px;
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 20px;
    border-radius: 10px;
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
    width: 600px;
}
}

</style>