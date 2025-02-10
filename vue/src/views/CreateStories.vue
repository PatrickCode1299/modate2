<script setup>
import { ref,reactive, onMounted } from 'vue';
import axiosClient from '../axios';
import 'text-image/dist/text-image.js';
import Header from '../component/Header.vue';
import LoadJsVideoComponent from "../component/LoadJsVideoComponent.vue";
import ProgressBar from "../component/ProgressBar.vue";
import { RouterLink } from 'vue-router';
const user_mail=localStorage.getItem('USER_MAIL');
const user_picture=localStorage.getItem('PICTURE');
const user_firstname=localStorage.getItem('FIRSTNAME');
const user_last_name=localStorage.getItem('LASTNAME');
function selectFile(){
    const picture=document.getElementById('picture');
    picture.click();
}

let   picture1=ref('');
const user_pictures=reactive({
    story_img_url:"",
    story_img:"",
    story_video:"",
    story_video_url:"",
    progress:0

});
let user_text=reactive({
    text_area_class:"user_text_story",
    story_text_img:""
});
function addPictures(e){
    const story_img=e.target.files[0];
    let pattern=new RegExp('[^.]+$');
    let check_extension=story_img.name.match(pattern);
    let file_extension=check_extension[0];
   let reader=new FileReader();
    switch(file_extension.toLowerCase()){
        case 'jpg':
        picture1.value=story_img;
        user_pictures.story_img=story_img;
        reader.onload=() =>{
        user_pictures.story_img_url=reader.result;
        user_pictures.story_video_url="";
        };
        reader.readAsDataURL(story_img);
        break;
        case 'jpeg':
        picture1.value=story_img;
        user_pictures.story_img=story_img;
        reader.onload=() =>{
        user_pictures.story_img_url=reader.result;
        user_pictures.story_video_url="";
        };
        reader.readAsDataURL(story_img);
        break;
        case 'png':
        picture1.value=story_img;
        user_pictures.story_img=story_img;
        reader.onload=() =>{
        user_pictures.story_img_url=reader.result;
        user_pictures.story_video_url="";
        };
        reader.readAsDataURL(story_img);
        break;
        case 'gif':
        picture1.value=story_img;
        user_pictures.story_img=story_img;
        reader.onload=() =>{
        user_pictures.story_img_url=reader.result;
        user_pictures.story_video_url="";
        };
        reader.readAsDataURL(story_img);
        break;
        case 'jfif':
        picture1.value=story_img;
        user_pictures.story_img=story_img;
        reader.onload=() =>{
        user_pictures.story_img_url=reader.result;
        user_pictures.story_video_url="";
        };
        reader.readAsDataURL(story_img);
        break;
        case 'mp4':
        user_pictures.story_img="";
        user_pictures.story_img=story_img;
        let video= document.getElementById("video");
        if (!story_img) return;
        let url = URL.createObjectURL(story_img);
        url += '#t=0,10';
        video.src = url;
    }
  let story_holder=document.getElementById("story_img_preview");
  story_holder.style.display="block";
}
function hidePreview(){
    let story_holder=document.getElementById("story_img_preview");
  story_holder.style.display="none";
  if(document.getElementById("video"))
  document.getElementById("video").pause();
else
return;
}
function uploadUserStory(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    let formData=new FormData();
    formData.append('email',user_mail);
    formData.append('story_post',user_pictures.story_img);
    axiosClient.post('/uploadStory',formData,{onUploadProgress:(event)=>{
            user_pictures.progress= Math.round((event.loaded * 100) / event.total);
        }}).then(response=>{
       alert("You added a new story");
       let  story_holder=document.getElementById("story_img_preview");
            story_holder.style.display="none";
    }).catch(err=>{
        alert("We cannot accomodate this file or network error");
    }).finally(()=>{
        user_pictures.progress=0;
    })
   
}
function postUserStory(e){
    let user_story_form=document.getElementById("story-form");
   user_story_form.addEventListener("submit", uploadUserStory);
   uploadUserStory(e);
}
function openTextDialogue(){
let story_text_preview=document.getElementById('story_text_preview');
story_text_preview.style.display="block";
}
function hideText(){
    let story_holder=document.getElementById("story_text_preview");
  story_holder.style.display="none";
}
function textColor(color){
    user_text.text_area_class=color;
}
let user_text_type=ref('');
function createImage(color){
    let formData=new FormData();
    formData.append('email',user_mail);
    formData.append('story_post',user_text_type.value);
    formData.append('bgColor',color);
    axiosClient.post('/uploadTextStory',formData).then(response=>{
       alert("You added a new story");
    }).catch(err=>{
        alert("We cannot accomodate this file or network error");
    });
let story_holder=document.getElementById("story_text_preview");
story_holder.style.display="none";
user_text_type.value="";
/**
switch(color){
    case 'user_text_purple':
    var style = {
    font: 'sans-serif',
    align: 'left',
    color: 'white',
    size: 12,
    background: 'purple',
    fontWeight:'normal',
    lineHeight:'15px',
    height:'100%',
    width:'400px'
};
    var newTextImage=TextImage();
    var img=newTextImage.toImage(user_text_type.value);
    newTextImage.setStyle(style);
    user_text.story_text_img=newTextImage.toDataURL(user_text_type.value);


    story_holder.style.display="none";
        break;
    case 'user_text_maroon':
    var style = {
    font: 'sans-serif',
    align: 'left',
    color: 'white',
    size: 12,
    background: 'maroon',
    fontWeight:'normal',
    lineHeight:'15px',
    height:'100%',
    width:'400px'
};
    var newTextImage=TextImage();
    var img=newTextImage.toImage(user_text_type.value);
    newTextImage.setStyle(style);
    user_text.story_text_img=newTextImage.toDataURL(user_text_type.value);

    story_holder=document.getElementById("story_text_preview");
    story_holder.style.display="none";
        break;
        case 'user_text_green':
    var style = {
    font: 'sans-serif',
    align: 'left',
    color: 'white',
    size: 12,
    background: 'forestgreen',
    fontWeight:'normal',
    lineHeight:'15px',
    height:'100%',
    width:'400px'
};
    var newTextImage=TextImage();
    var img=newTextImage.toImage(user_text_type.value);
    newTextImage.setStyle(style);
    user_text.story_text_img=newTextImage.toDataURL(user_text_type.value);

     story_holder=document.getElementById("story_text_preview");
    story_holder.style.display="none";
        break;
default:
var style = {
    font: 'sans-serif',
    align: 'center',
    color: 'white',
    size: 12,
    background: 'magenta',
    fontWeight:'normal',
    lineHeight:'15px',
    height:'100%',
    width:'400px'
};
    var newTextImage=TextImage();
    var img=newTextImage.toImage(user_text_type.value);
    newTextImage.setStyle(style);
    user_text.story_text_img=newTextImage.toDataURL(user_text_type.value);
    
    //user_text.story_text_img=img;
    story_holder=document.getElementById("story_text_preview");
    story_holder.style.display="none";
}
    let rand=Math.random(10,1000);
    let name=rand+"modate";
    dataURLtoFile(user_text.story_text_img,name);
**/

} 
  
   /**  function dataURLtoFile(dataurl, filename) {
    var arr = dataurl.split(","),
      mime = arr[0].match(/:(.*?);/)[1],
      bstr = atob(arr[arr.length - 1]),
      n = bstr.length,
      u8arr = new Uint8Array(n);
    while (n--) {
      u8arr[n] = bstr.charCodeAt(n);
    }
  
    let formData=new FormData();
    formData.append('email',user_mail);
    formData.append('story_post',new File([u8arr], filename, { type: mime }));
    axiosClient.post('/uploadStory',formData).then(response=>{
       alert("You added a new story");
    }).catch(err=>{
        alert("We cannot accomodate this file or network error");
    });
    
    
} */
  onMounted(()=>{
    let text_area=document.getElementById("text");
    text_area.setAttribute("placeholder","Pour out your mind!");
  });
function insertLineBreak(event){
  const text = event.target.value;
  user_text_type.value = text.replace(/(\S{50})(?=\S)/g, "$1\n"); // 50 represents the column width
}
</script>
<template>
<Header class="shadow-sm" style="background-color:white; position:fixed; padding-bottom:10px;  width: 100%; z-index: 1; top: 0px;" />
<div class="container-fluid two-flex-div">
    <div class="fixed-side-story-div p-4 shadow-lg">
        <h2 class="fs-5 font-weight-bold text-black" style="margin-top:50px;">Your Story</h2>
    <div class="user-info"><img v-if="user_picture === null || user_picture === 'null'" class="circle-thumbnail" src="../pictures/profile.png"><img v-else class="circle-thumbnail" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${user_picture}`'><RouterLink to="/profile"><span class="names fs-5">{{ user_firstname}}</span><span class="names fs-5">{{ user_last_name }}</span></RouterLink></div>
    </div>
    <div class="all-forms-div">
        <div class="block-div">
        <form id="story-form" class="for-pictures-video">
            <label style="flex-direction:column;" class="d-flex justify-content-center align-items-center">
                <span @click="selectFile" for="picture" class="fs-70 cursor-pointer">&plus;</span>
                <p>Add Media</p>
            </label>
            <input accept="image/*,  video/*" v-on:change="addPictures" id="picture" type="file" name="file" />
            
        </form>
        <form class="for-pictures-video">
            <label>
                <span @click="openTextDialogue" class="fs-2 cursor-pointer">Text Story</span> 
            </label>
        </form>
        
    </div>
    <div id="story_img_preview" class="story_preview_holder">
    <div  class="story-img-preview " style="position:relative;">
        <span @click="hidePreview" style="color: white; margin-top:50px; font-size: 75px; cursor: pointer; font-weight: bold;" class="fs-1">&times;</span>
       <img v-if="user_pictures.story_img_url !=''" style="border-radius: 5px; cursor: pointer;" width="300px" height="100vh" alt="preview-story" :src="user_pictures.story_img_url" class="img-responsive">
        <video id="video" style="width:300px; border-radius:5px; height:300px;" autoplay controls  v-else>
            
        </video>
        <ProgressBar :progress="user_pictures.progress"/>
       <div class="d-flex" style="position:fixed; justify-content:flex-end; bottom:0px;"><button @click="postUserStory" style="font-weight: bold;" type="submit" class="m-2 btn btn-primary btn-md">Add Story</button></div> 
    </div>
    </div>
    <div id="story_text_preview" class="story_preview_holder">
    <div  class="story-img-preview">
        <textarea @input="insertLineBreak" rows="10" cols="50" style="text-align:left; white-space:pre-wrap; padding-top:80px;" v-model="user_text_type" id="text" :class="user_text.text_area_class">

        </textarea>
        <div class="colors-btn p-2"> 
        <span @click="hideText" style="color: white; margin-right:auto;  font-size: 80px; cursor: pointer; font-weight: bold;" class="cursor-pointer fs-1">&times;</span>
        <span style="margin-right:auto; margin-top:0px;" class="text-white fs-5 cursor-pointer font-bold" @click="createImage(user_text.text_area_class)">Create Text Story.</span>
    <div class="text-white d-flex justify-content-center align-items-center btn-group font-weight-bold">
            <button @click="textColor('user_text_purple')" class="btn-md purple"></button>
            <button @click="textColor('user_text_maroon')" class="btn-md maroon"></button>
            <button @click="textColor('user_text_green')" class="btn-md green"></button>
            <button @click="textColor('user_text_story')" class="btn-md magenta"></button>
        </div>
    </div>
    </div>
    </div>
    </div>
    
</div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    input[type="file"]{
    visibility: hidden;
}
.story_preview_holder{
    display: none;
    position: fixed;
    top: 0px;
    background-color: rgba(0, 0, 0, 0.596);
    width: 100%;
    left: 0px;
    flex-direction: column;
    align-items: center;
    height: 100vh;
    padding-top: 0px;
}

.fs-70{
    font-size: 70px;
    font-weight: bold;
    color: black;
}
.two-flex-div{
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
}
.block-div{
    width: 100%;
    margin: 0px auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 200px;
}
.block-div > form{
    margin-left: 0px;
}
.two-flex-div > .all-forms-div{
 display: flex;
 flex-direction: column;
 justify-content: center;
 align-items: center;
}
.fixed-side-story-div{
    width: 300px;
    height: 100vh;
    position: fixed;
    display: none;
    left: 0px;
    z-index: -1;
    padding:0px;
}
.user-info{
    display: flex;
}
.circle-thumbnail{
    border-radius: 50px;
    width: 50px;
    height: 50px;
    margin-right: auto;
    object-fit: cover;
    
}
.names{
    margin-left: 5px;
    margin-top: 10px;
    font-weight: 600;
}
.font-weight-bold{
    font-weight: bold;
    margin-bottom: 20px;
}
.user_text_story{
    resize: none;
    height: 100vh;
    width: 100%;
    border: none;
    outline: none;
    background-color: rgb(244, 69, 209);
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_purple{
    resize: none;
    height: 100vh;
    width: 100%;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: rgb(126, 0, 114);
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_maroon{
    resize: none;
    height: 100vh;
    width: 100%;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: maroon;
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_green{
    resize: none;
    height: 100vh;
    width: 100%;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: forestgreen;
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.purple{
    background-color: purple;
    border-radius: 50%;
    width: 20px;
    height: 20px;
}
.maroon{
    background-color: maroon;
    border-radius: 50%;
    width: 20px;
    height: 20px;
}
.green{
    background-color: forestgreen;
    border-radius: 50%;
    width: 20px;
    height: 20px;
}
.magenta{
    background-color:rgb(244, 69, 209);
    border-radius: 50%;
    width: 20px;
    height: 20px;
}
.colors-btn{
    position:fixed; 
    display:flex;
    z-index:1;
    justify-content:flex-start;
    background-color:rgba(0, 0, 0, 0.672);
    left:0px;
    width:100%;
    right: 0px; 
    bottom:0px;
}
.story-img-preview{
    width:100%;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding:0px;


    
}
::placeholder{
    color: white;
}
}
@media screen and (min-width:620px) {
    input[type="file"]{
    visibility: hidden;
}
.story-img-preview{
    width:100%;
    border-radius: 10px;
    display: flex;
    flex-direction: column;

    
}
.fs-70{
    font-size: 70px;
    font-weight: bold;
    color: black;
}
.two-flex-div{
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
}
.block-div{
    width: 80%;
    margin: 0px auto;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    margin-top: 200px;
}
.block-div > form{
    margin-left: 50px;
}
.two-flex-div > .all-forms-div{
 display: flex;
 flex-direction: column;
 justify-content: center;
 align-items: center;
}
.fixed-side-story-div{
    width: 300px;
    height: 100vh;
    position: fixed;
    left: 0px;
    z-index: -1;
    display:block;
}
.user-info{
    display: flex;
}
.circle-thumbnail{
    border-radius: 50px;
    width: 50px;
    height: 50px;
    margin-right: auto;
    object-fit: cover;
    
}
.names{
    margin-left: 5px;
    margin-top: 10px;
    font-weight: 600;
}
.font-weight-bold{
    font-weight: bold;
    margin-bottom: 20px;
}
.story_preview_holder{
    display: none;
    position: fixed;
    top: 0px;
    background-color: rgba(0, 0, 0, 0.596);
    width: 100%;
    left: 0px;
    flex-direction: column;
    align-items: center;
    height: 100vh;
    padding-top: 0px;
}
.user_text_story{
    resize: none;
    height: 100vh;
    width: 400px;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: rgb(244, 69, 209);
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_purple{
    resize: none;
    height: 100vh;
    width: 400px;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: rgb(126, 0, 114);
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_maroon{
    resize: none;
    height: 100vh;
    width: 400px;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: maroon;
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_green{
    resize: none;
    height: 100vh;
    width: 400px;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: forestgreen;
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.purple{
    background-color: purple;
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
.maroon{
    background-color: maroon;
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
.green{
    background-color: forestgreen;
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
.magenta{
    background-color:rgb(244, 69, 209);
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
::placeholder{
    color: white;
}

}
@media screen and (min-width:1224px) {
    input[type="file"]{
    visibility: hidden;
}
.story-img-preview{
    width:100%;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    
}
.fs-70{
    font-size: 70px;
    font-weight: bold;
    color: black;
}
.two-flex-div{
    display: flex;
    flex-direction: row;
    justify-content: space-evenly;
}
.block-div{
    width: 80%;
    margin: 0px auto;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    margin-top: 200px;
}
.block-div > form{
    margin-left: 50px;
}
.two-flex-div > .all-forms-div{
 display: flex;
 flex-direction: column;
 justify-content: center;
 align-items: center;
}
.fixed-side-story-div{
    width: 300px;
    height: 100vh;
    position: fixed;
    left: 0px;
    z-index: -1;
    display: block;
}
.user-info{
    display: flex;
}
.circle-thumbnail{
    border-radius: 50px;
    width: 50px;
    height: 50px;
    margin-right: auto;
    object-fit: cover;
    
}
.names{
    margin-left: 5px;
    margin-top: 10px;
    font-weight: 600;
}
.font-weight-bold{
    font-weight: bold;
    margin-bottom: 20px;
}
.story_preview_holder{
    display: none;
    position: fixed;
    top: 0px;
    background-color: rgba(0, 0, 0, 0.596);
    width: 100%;
    left: 0px;
    flex-direction: column;
    align-items: center;
    height: 100vh;
    padding-top: 0px;
}
.user_text_story{
    resize: none;
    height: 100vh;
    width: 400px;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: rgb(244, 69, 209);
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_purple{
    resize: none;
    height: 100vh;
    width: 400px;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: rgb(126, 0, 114);
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_maroon{
    resize: none;
    height: 100vh;
    width: 400px;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: maroon;
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.user_text_green{
    resize: none;
    height: 100vh;
    width: 400px;
    margin: 0px auto;
    border: none;
    outline: none;
    background-color: forestgreen;
    color: white;
    font-weight: bold;
    font-size: 20px;
    line-height: 34px;

}
.purple{
    background-color: purple;
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
.maroon{
    background-color: maroon;
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
.green{
    background-color: forestgreen;
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
.magenta{
    background-color:rgb(244, 69, 209);
    border-radius: 50%;
    width: 50px;
    height: 50px;
}
.colors-btn{
    position: fixed; 
    z-index:1;
}
::placeholder{
    color: white;
}
}

</style>