<script setup>
import { ref,reactive } from 'vue';
import axiosClient from '../axios';
import 'text-image/dist/text-image.js';
import Header from '../component/Header.vue';
const user_mail=sessionStorage.getItem('USER_MAIL');
const user_picture=sessionStorage.getItem('PICTURE');
const user_firstname=sessionStorage.getItem('FIRSTNAME');
const user_last_name=sessionStorage.getItem('LASTNAME');
function selectFile(){
    const picture=document.getElementById('picture');
    picture.click();
}

let   picture1=ref('');
const user_pictures=reactive({
    story_img_url:"",
    story_img:""

});
let user_text=reactive({
    text_area_class:"user_text_story",
    story_text_img:""
});
function addPictures(e){
    const story_img=e.target.files[0];
  picture1.value=story_img;
  user_pictures.story_img=story_img;
  const reader=new FileReader();
  reader.onload=() =>{
    user_pictures.story_img_url=reader.result;
  };
  reader.readAsDataURL(story_img);
  let story_holder=document.getElementById("story_img_preview");
  story_holder.style.display="block";
}
function hidePreview(){
    let story_holder=document.getElementById("story_img_preview");
  story_holder.style.display="none";
}
function uploadUserStory(e){
    e.preventDefault();
    let formData=new FormData();
    formData.append('email',user_mail);
    formData.append('story_post',user_pictures.story_img);
    axiosClient.post('/uploadStory',formData).then(response=>{
       alert("You added a new story");
       let story_holder=document.getElementById("story_img_preview");
  story_holder.style.display="none";
    }).catch(err=>{
        alert("We cannot accomodate this file or network error");
    });
   
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
let story_holder=document.getElementById("story_text_preview");
switch(color){
    case 'user_text_purple':
    var style = {
    font: 'sans-serif',
    align: 'left',
    color: 'white',
    size: 15,
    background: 'purple',
    fontWeight:'bold',
    lineHeight:'34px',
    height:'100vh',
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
    size: 15,
    background: 'maroon',
    fontWeight:'bold',
    lineHeight:'34px',
    height:'100vh',
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
    size: 15,
    background: 'forestgreen',
    fontWeight:'bold',
    lineHeight:'34px',
    height:'100vh',
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
    align: 'left',
    color: 'white',
    size: 15,
    background: 'magenta',
    fontWeight:'bold',
    lineHeight:'34px',
    height:'100vh',
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


}
  
    function dataURLtoFile(dataurl, filename) {
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
    //return new File([u8arr], filename, { type: mime });
    
  }
</script>
<template>
<Header />
<div class="container-fluid two-flex-div">
    <div class="fixed-side-story-div p-4 shadow-md">
        <h2 class="fs-5 font-weight-bold text-black">Your Story</h2>
    <div class="user-info"><img class="circle-thumbnail" :src='`http://localhost:8000/storage/${user_picture}`'><span class="names fs-5">{{ user_firstname}}</span><span class="names fs-5">{{ user_last_name }}</span></div>
    </div>
    <div class="all-forms-div">
        <div class="block-div">
        <form id="story-form" class="for-pictures-video">
            <label>
                <span @click="selectFile" for="picture" class="fs-70 cursor-pointer">&plus;</span>
                <p>Add Photo</p>
            </label>
            <input v-on:change="addPictures" id="picture" type="file" name="file" />
            
        </form>
        <form class="for-pictures-video">
            <label>
                <span @click="openTextDialogue" class="fs-2 cursor-pointer">Text Story..</span> 
                <a  :href="user_text.story_text_img" :download="user_text.story_text_img"> Download</a>
            </label>
        </form>
        
    </div>
    <div id="story_img_preview" class="story_preview_holder">
    <div  class="story-img-preview m-2">
        <span @click="hidePreview" style="color: white; font-size: 75px; cursor: pointer; font-weight: bold;" class=" m-2 fs-1">&times;</span>
       <img style="border-radius: 5px; cursor: pointer;" width="300px" height="100vh" alt="preview-story" :src="user_pictures.story_img_url" class="img-responsive">
       <button @click="postUserStory" style="font-weight: bold;" type="submit" class="m-2 btn btn-success btn-md">Add Story</button>
    </div>
    </div>
    <div id="story_text_preview" class="story_preview_holder">
    <div class="m-2 colors-btn"> 
        <h3 style=" font-size: 25px;" class="text-white text-center">Colors</h3>
    <div class="text-white p-4 d-flex justify-content-center align-items-center btn-group font-weight-bold">
            <button @click="textColor('user_text_purple')" class="btn-md purple"></button>
            <button @click="textColor('user_text_maroon')" class="btn-md maroon"></button>
            <button @click="textColor('user_text_green')" class="btn-md green"></button>
            <button @click="textColor('user_text_story')" class="btn-md magenta"></button>
        </div>
    </div>
    <div  class="story-img-preview m-2">
        <span @click="hideText" style="color: white; font-size: 75px; cursor: pointer; font-weight: bold;" class=" m-2 fs-1">&times;</span>
        <span class="text-white cursor-pointer" @click="createImage(user_text.text_area_class)">Create Text Story.</span>
        <textarea v-model="user_text_type" id="text" :class="user_text.text_area_class">

        </textarea>
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
    background-color: rgba(1, 1, 1, 0.361);
    width: 100%;
    left: 0px;
    flex-direction: column;
    align-items: center;
    height: 100vh;
}
.story-img-preview{
    width:100%;
    border-radius: 10px;
    display: block;
    

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
    flex-direction: column;
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
    display: none;
    left: 0px;
    z-index: 1;
}
.user-info{
    display: flex;
}
.circle-thumbnail{
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin-right: auto;
    
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
    height: 600px;
    width: 80%;
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
    position: absolute; 
    right: 0px; 
    top: 0px;
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
    z-index: 1;
    display:none;
}
.user-info{
    display: flex;
}
.circle-thumbnail{
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin-right: auto;
    
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
    background-color: rgba(1, 1, 1, 0.361);
    width: 100%;
    left: 0px;
    flex-direction: column;
    align-items: center;
    height: 100vh;
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
    z-index: 1;
    display: block;
}
.user-info{
    display: flex;
}
.circle-thumbnail{
    border-radius: 50%;
    width: 50px;
    height: 50px;
    margin-right: auto;
    
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
    background-color: rgba(1, 1, 1, 0.216);
    width: 100%;
    left: 0px;
    flex-direction: column;
    align-items: center;
    height: 100vh;
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
    position: absolute; 
 
    right:0px;
    top: 100px;
}
}

</style>