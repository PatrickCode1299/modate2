<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
import {ref, watch} from "vue";
const router=useRouter();
const  image=ref('');
const cover_photo=ref('');
let cover_text=ref('');
let img_model=ref({
        image_uri:null,
        cover_uri:null
    
});
function editProfilePic(e){
e.preventDefault();
const current_user=sessionStorage.getItem('USER_MAIL');
const formData=new FormData();
formData.append('owner',current_user);
formData.append('profile_pic',image.value);
axiosClient.post('/update',formData).then((response)=>{
alert(response.data.update);
router.push({
    name:"Profile"
});
});
}
function addCoverPhoto(e){
    e.preventDefault();
const current_user=sessionStorage.getItem('USER_MAIL');
const formData=new FormData();
formData.append('owner',current_user);
formData.append('coverPhoto',cover_photo.value);
axiosClient.post('/updateCoverPhoto',formData).then((response)=>{
alert(response.data.update);
router.push({
    name:"Profile"
});
});
}
function addCoverText(e){
    e.preventDefault();
    const current_user=sessionStorage.getItem('USER_MAIL');
    const formData=new FormData();
    formData.append('email',current_user);
    formData.append('cover_text',cover_text.value);
    axiosClient.post("/updateCoverText",formData).then(response=>{
        router.push({
            name:"Profile"
        });
    });
}

function user_profile_img(e){
  const img=e.target.files[0];
  image.value=img;
  const reader=new FileReader();
  reader.onload=() =>{
    img_model.value.image_uri=reader.result;
  };
  reader.readAsDataURL(img);
 
 

  
}
function user_cover_photo(e){
    const img=e.target.files[0];
  cover_photo.value=img;
  const reader=new FileReader();
  reader.onload=() =>{
    img_model.value.cover_uri=reader.result;
  };
  reader.readAsDataURL(img);
}
watch(cover_text, ()=>{
let cover_text_btn=document.getElementById("cover_text_button");
if(cover_text.value.length > 128){
cover_text_btn.setAttribute("disabled",true);
}else{
cover_text_btn.removeAttribute("disabled");
}
});
</script>
<template>
    <Header />
    <SideNav />
    <div class="profile-header-container shadow-sm container p-4">
        <form @submit="editProfilePic">
            <div class="form-group">
                <label class="m-2 fs-5 form-label" for="Display Picture">Edit Profile Picture</label>
                <h2 class="text-danger fs-6 m-2">We recommend a very clear headshot that is visible...</h2>
                <img :src=img_model.image_uri  class="img-responsive preview-img" alt="preview img">
                <input ref="file" v-on:change="user_profile_img" class="m-2 form-control md" type="file" name="file" />
            </div>
            <button class="btn m-2 edit-profile-btn btn-block btn-md btn-success">Edit</button>
        </form>
        <form @submit="addCoverPhoto">
            <div class="form-group">
                <label class="m-2 fs-5 form-label" for="Display Picture">Add Cover Photo</label>
                <h2 class="text-danger fs-6 m-2">Ensure your cover photo is set to a Landscape size with an height of 150px</h2>
                <img :src=img_model.cover_uri  class="img-responsive  cover-img" alt="preview img">
                <input v-on:change="user_cover_photo" class="m-2 form-control md" type="file" name="coverPhoto" />
            </div>
            <button class="btn m-2 edit-profile-btn btn-block btn-md btn-success">Add</button>
        </form>
        <form @submit="addCoverText">
            <div class="form-group">
                <label class="m-2 fs-5 form-label" for="Display Picture">Add Cover Text</label>
                <h2 class="text-danger fs-6 m-2">Your cover text cannot be greater than 128 characters...</h2>
                <textarea required v-model="cover_text" placeholder="I am a bored introvert currently working at Modate2..." class="form-control covertext" ></textarea>
            </div>
            <button id="cover_text_button" class="btn m-2 cover-text-btn btn-block btn-md btn-success">Add Cover Text</button>
        </form>
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .profile-header-container{
    background-color: red;
    width: 100%;
    margin:0 auto;
    background-color: rgb(253, 253, 253);
    height: 800px;
    border-radius: 10px;

}
.form-label{
    font-weight: bold;
}
.edit-profile-btn{
    width:100px;
    transition: width 2s;
}
.edit-profile-btn:hover{
    width: 150px;
}
.cover-text-btn{
    width: 150px;
}
.covertext{
    resize: none;
}
.preview-img{
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
}
}
@media screen and (min-width:620px) {
    .profile-header-container{
    background-color: red;
    width: 50%;
    margin:0 auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;

}
.form-label{
    font-weight: bold;
}
.edit-profile-btn{
    width:100px;
    transition: width 2s;
}
.edit-profile-btn:hover{
    width: 150px;
}
.cover-text-btn{
    width: 150px;
}
.covertext{
    resize: none;
}
.preview-img{
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
}
}
@media screen and (min-width:1224px) {
    .profile-header-container{
    background-color: red;
    width: 50%;
    margin:0 auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;

}
.form-label{
    font-weight: bold;
}
.edit-profile-btn{
    width:100px;
    transition: width 2s;
}
.edit-profile-btn:hover{
    width: 150px;
}
.cover-text-btn{
    width: 150px;
}
.covertext{
    resize: none;
}
.preview-img{
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
}
.cover-img{
    width: 100%;
    object-fit: cover;
    object-position: left center;
    height: 150px;
}
}

</style>