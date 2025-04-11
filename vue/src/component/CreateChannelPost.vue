<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import {ref} from "vue";
import { watch } from 'vue';
import { reactive,defineProps } from 'vue';
import axios from "axios";
import LoadJsVideoComponent from './LoadJsVideoComponent.vue'
import ProgressBar from './ProgressBar.vue';
import axiosClient from '../axios';
let channel_text=ref('');
let user_mail;
const user_picture=localStorage.getItem('PICTURE');
const {channel_name}=defineProps(['channel_name']);
let channel_picture=reactive({
    first_image:"",
    first_image_url:"",
    second_image:"",
    second_image_url:"",
    third_image:"",
    third_image_url:"",
    fourth_image:"",
    fourth_image_url:""
});
let channel_video=reactive({
    video_url:"",
    video:"",
    progress:0,
});
function createPost(e){
    e.preventDefault();
    user_mail=localStorage.getItem('USER_MAIL');
    let formData=new FormData();
    formData.append('email',user_mail);
    formData.append('name',channel_name);
    formData.append('caption',channel_text.value);
    formData.append('avatar',user_picture);
    formData.append('image1',channel_picture.first_image);
    formData.append('image2',channel_picture.second_image);
    formData.append('image3',channel_picture.third_image);
    formData.append('image4',channel_picture.fourth_image);

    axiosClient.post("/createUserChannelPost",formData).then((response=>{
    location.reload();
    channel_text.value="";
})).catch((error =>{
    console.log(error);
}))

} 
watch(channel_text, ()=>{
if(channel_text.value.trim().length > 0){
    console.log(channel_text.value.length);
    let post_button=document.getElementById("create-text-btn");
   post_button.removeAttribute("disabled");
}else{
        let post_button=document.getElementById("create-text-btn");
        post_button.setAttribute("disabled","true");
}
}); 
function createPhoto(){
   let picture=document.getElementById("pictures");
   picture.click();
}
function createVideo(){
    let form_video=document.getElementById("form-video");
    form_video.style.display="block";
    let video=document.getElementById("videos");
   video.click();

}

function setPicture(e){
    if(channel_picture.first_image === ''){
        channel_picture.first_image=e.target.files[0];
        const reader=new FileReader();
        reader.onload=() =>{
        channel_picture.first_image_url=reader.result;
  };
  reader.readAsDataURL(channel_picture.first_image);
        
    }else if(channel_picture.second_image === '' && channel_picture.first_image != ''){
        channel_picture.second_image=e.target.files[0];
        const reader=new FileReader();
        reader.onload=() =>{
        channel_picture.second_image_url=reader.result;
  };
  reader.readAsDataURL(channel_picture.second_image);
        
    }else if(channel_picture.third_image === '' && channel_picture.second_image != ''){
        channel_picture.third_image=e.target.files[0];
        const reader=new FileReader();
        reader.onload=() =>{
        channel_picture.third_image_url=reader.result;
  };
  reader.readAsDataURL(channel_picture.third_image);
        
    }else if(channel_picture.fourth_image === '' && channel_picture.third_image !=''){
        channel_picture.fourth_image=e.target.files[0];
        const reader=new FileReader();
        reader.onload=() =>{
        channel_picture.fourth_image_url=reader.result;
  };
  reader.readAsDataURL(channel_picture.fourth_image);
    }
  

}
function cancelVideo(){
    let form_video=document.getElementById("form-video");
    form_video.style.display="none";
    let video=document.getElementById("video");
    video.pause();
    video.src='';
    channel_video.video='';
    
}
let video_text=ref('');
function setVideo(e){
        let video=document.getElementById("video");
        channel_video.video=e.target.files[0];
        let file = e.target.files[0];

            if (!file) return;

            // Create a URL for the selected file
            let url = URL.createObjectURL(file);

            // Append the fragment to the URL to load the first 10 seconds
            url += '#t=0,10';

            video.src = url;
            
            // Play the video after setting the source to ensure it starts buffering
            video.play();
        
}
function uploadVideo(e){
    e.preventDefault();
    user_mail=localStorage.getItem('USER_MAIL');
    let formData=new FormData();
    formData.append('email',user_mail);
    formData.append('name',channel_name);
    formData.append('caption',video_text.value);
    formData.append('avatar',user_picture);
    formData.append('video',channel_video.video);
    document.getElementById("create-video-btn").setAttribute("disabled","true");
    axiosClient.post("/createUserChannelVideo",formData,{onUploadProgress:(event)=>{
            channel_video.progress = Math.round((event.loaded * 100) / event.total);
        }}).then((response=>{
    location.reload();
    video_text.value="";
})).catch((error =>{
    alert("Video Size is too large or You did not upload a video file");
})).finally(()=>{
    channel_video.progress=0;
})
}
</script>
<template>

<form @submit="createPost" class="create_content_form">
    <div class="form-group">
        <textarea v-model="channel_text"  placeholder="Say something..." class="form-control create_text"></textarea>
        <div class="d-flex channel_img_post" style="overflow-x: hidden; margin-bottom: 5px;">
            <img v-if="channel_picture.first_image != '' " :src="channel_picture.first_image_url"  />
            <img v-if="channel_picture.second_image != '' " :src="channel_picture.second_image_url"  />
            <img v-if="channel_picture.third_image != '' " :src="channel_picture.third_image_url"  />
            <img v-if="channel_picture.fourth_image != '' " :src="channel_picture.fourth_image_url" />

        </div>
        <input accept="image/*" id="pictures" v-on:change="setPicture" type="file" name="picture" />
        <div class="d-flex justify-content-space-around">
            <button type="button" @click="createPhoto"  class="btn btn-sm btn-default"><i style="color: magenta;" class="fa fa-image fs-4"></i></button>
            <button type="button" @click="createVideo" class="btn btn-sm btn-default" style="margin-right: auto;"><i style="color: magenta;" class="fa fa-video fs-4"></i></button>
            <div  class="d-flex justify-content-space-around"><button style="border-radius: 20px; padding-left: 20px; padding-right: 20px;"  disabled="" id="create-text-btn" class="btn font-bold border-20px btn-sm btn-success">Create</button></div>
        </div>
        
    </div>
    
</form>
<form style="border-radius:5px;" id="form-video" @submit="uploadVideo" class="p-4 form-video shadow-md" >
<span @click="cancelVideo" style="color: black; margin-bottom:0px;" class="cancel-video font-bold m-2 fs-1">&times;</span>
    <div style="margin-top:0px;" class="d-flex justify-content-center align-items-center video-img-post">
        <video style="width:100%;  height:250px;" id="video" controls>
            
        </video>
    </div>
    <textarea class="form-control create_text m-2" v-model="video_text" placeholder="Write something about the video.."></textarea>
    <input accept="video/*" id="videos" v-on:change="setVideo" type="file" name="video" />
    <ProgressBar :progress="channel_video.progress"/>
    <div class="d-flex m-2 justify-content-center align-items-center"><button id="create-video-btn" style="border-radius: 20px; padding-left: 20px; padding-right: 20px;" class="btn font-bold border-20px btn-block video-upload-btn btn-md  btn-success">Upload the Video</button></div>
</form>


</template>

<style scoped>
@media screen and (min-width:320px) {
    .create_text{
        resize: none;
    }
        input[type="file"]{
    display: none;
}
.channel_img_post > img{
    width: 100px;
    height: 100px;
    border-radius: 20px;
    margin-left: 5px;
    flex: 1;
    object-fit: cover;
}
.form-video{
    z-index: 1; 
     position: fixed; 
     width:100%; 
     display: none; 
     background-color:white; 
    border:1px solid grey;
     top:0px; 
     left:0%; 
     right:0%;
     height:100%;
}
.video-upload-btn{
    width:100%;
}
}
@media screen and (min-width:620px) {
    .create_text{
        resize: none;
    }
        input[type="file"]{
    display: none;
}
.channel_img_post > img{
    width: 200px;
    height: 200px;
    border-radius: 20px;
    margin-left: 5px;
    flex: 1;
    object-fit: cover;
}
.form-video{
    z-index: 1; 
    margin: 0px auto; 
    position: fixed; 
    width:50%; 
    display: none; 
    background-color:white; 
    border:1px solid grey;
    top:10%; 
    left:20%; 
    right:20%;
    height: auto;
}
.video-upload-btn{
    width:100%;
}
}
@media screen and (min-width:1224px) {
    .create_text{
        resize: none;
    }
    input[type="file"]{
    display: none;
}
.channel_img_post > img{
    width: 200px;
    height: 200px;
    border-radius: 20px;
    margin-left: 5px;
    flex: 1;
    object-fit: cover;
}
.form-video{
    z-index: 1; 
    margin: 0px auto; 
    position: fixed; 
    width:50%; 
    display: none; 
    background-color:white; 
    border:1px solid grey;
    z-index:1;
    top:10%; 
    left:20%; 
    right:20%;
    height: auto;
}
.video-upload-btn{
    width:100%;
}
}
</style>
