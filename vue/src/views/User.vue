<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { ref,reactive } from "vue";
import axiosClient from "../axios";
import {useRoute, useRouter } from "vue-router";
const route=useRoute();
const user_mail=route.params.info;
let info=reactive({
    info_value:"false",
    coverbgVal:"false",
    bgUrl:"",
    cover_text:""
});
let personal_info=reactive({
        u_first_name:"",
        u_last_name:"",
        u_location:"",
        u_phone_number:"",
        u_profile_pic:""
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
  
       info.info_value="false";
     
       
            info.coverbgVal="true";
            cover_bg=response.data.coverPhoto;
            cover_photo=`http://localhost:8000/storage/${cover_bg}`;
            info.bgUrl='url('+cover_photo+')';
            if(response.data.coverText==null){
               info.cover_text="I am a Uhangout member...";
               
            }else{
                
               info.cover_text=response.data.coverText;
            }
            
            
           
        
        personal_info.u_first_name=response.data.first_name;
        personal_info.u_last_name=response.data.last_name;
        personal_info.u_location=response.data.location;
        personal_info.u_profile_pic=response.data.profile_picture;
      
        
        
     

    
    
})).catch((error =>{
    alert('Network Error...');
}));
       
</script>
<template>
    <Header />
    <SideNav v-if="store.state.user.token != null" />
   <div class="container user-profile p-2">
    <div :style="{backgroundImage:info.bgUrl}" class="images p-4">
        <span style="word-wrap: break-word;" class="fs-5">{{ info.cover_text }}</span>
       
        <div class="user-pic-name">

            <img v-if="personal_info.u_incomplete === 'true'" src="../landing/blondie.jpg" class="user-profile-img" />
            <img v-else :src="`http://localhost:8000/storage/${personal_info.u_profile_pic}`" class="user-profile-img" />
            <span style="text-shadow: none;" class="fs-2 text-black text-bold bold">{{ personal_info.u_first_name }}</span>
        </div>
       
    </div>
    <div class="user-info-card p-2">
        <div class="bg-white shadow-sm user-info-card p-2">
            <div class="heading-edit-btn">
                <div class="heading p-2">
                <h2 class="fs-3 user-info-title">Personal information</h2>
                <small class="helper-text">Update your information about you and details here.</small>
                </div>
            </div>
            <div class="all-user-info p-4">
                <div    class="complete-profile">
                    <ul>
                    <li class="d-flex justify-content-flex-start"><span class="title">First Name</span><span>{{personal_info.u_first_name}}</span></li>
                    <li class="d-flex justify-content-flex-start"><span class="title">Last Name</span><span>{{personal_info.u_last_name}}</span></li>
                    <li class="d-flex justify-content-flex-start"><span class="title">Location</span><span>{{personal_info.u_location}}</span></li>
                    <li class="d-flex justify-content-flex-start"><span class="title">Phone Number</span><span>Message User to get this detail</span></li>
                </ul>
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
    height: 1000px;
    margin:0 auto;
}
.images{
    border-radius: 10px;
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
}
.user-profile-img{
    border-radius: 50%;
    height: 100px;
    width: 100px;
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
    margin-top: 110px;
    border-radius: 5px;
    

}
.heading-edit-btn{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.user-info-title{
    font-weight: bold;
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
}
@media screen and (min-width:620px) {
    .user-profile{
    width: 80%;
    background-color: rgb(252, 252, 252);
    height: 1000px;
    margin:0 auto;
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
    height: 200px;
    width: 200px;
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
    margin-top: 110px;
    border-radius: 5px;
    

}
.heading-edit-btn{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.user-info-title{
    font-weight: bold;
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
}
@media screen and (min-width:1224px) {
    .user-profile{
    width: 60%;
    background-color: rgb(252, 252, 252);
    height: 1000px;
    margin:0 auto;
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
    height: 200px;
    width: 200px;
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
    margin-top: 110px;
    border-radius: 5px;
    

}
.heading-edit-btn{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.user-info-title{
    font-weight: bold;
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
}

</style>