<script setup>
import axios from "axios";
import store from "../store";
import axiosClient from "../axios.js";
import { useRouter } from "vue-router";
import { reactive } from "vue";
import { inject } from "vue";

import { defineAsyncComponent } from "vue";

const Header=defineAsyncComponent({
   loader:  () => import("../component/Header.vue"),
  
});
const SideNav=defineAsyncComponent({
    loader: () => import("../component/SideNav.vue")
});
const StoriesandPost=defineAsyncComponent({
    loader: () => import("../component/StoriesandPost.vue")
});
let user_mail;
const router=useRouter();

if(sessionStorage.getItem('USER_MAIL') === null){
    $cookies=inject('$cookies');
    let user_cookie=$cookies.get('User');
    sessionStorage.setItem('USER_MAIL',user_cookie);
}
user_mail=sessionStorage.getItem('USER_MAIL');



function deleteOldStories(){
    axiosClient.post("/deleteOldStory",{data:""}).then(response=>{

    }).catch(err=>{
        console.log(err)
    });
}
let info=reactive({
    info_value:"",
});
function checkIfUserHasCompleteProfile(){
    user_mail=sessionStorage.getItem('USER_MAIL');
    axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
       info.info_value="true";
       sessionStorage.setItem('INCOMPLETE',info.info_value);
       
    }

})).catch((error =>{
    console.log(error);
}))
}

deleteOldStories();
</script>
<template>
    {{ checkIfUserHasCompleteProfile() }}
    <Header style="background-color:white; padding:0px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav />
    <div v-if="info.info_value==='true'" style="margin-top: 100px;" class=" incomplete d-flex justify-content-center">
        <h2 class="fs-2 m-4">Click on Profile and Complete your profile to be Visible</h2>
    </div>
    <div v-else  class="story-and-post">
    <StoriesandPost  />
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .story-and-post{
    display: flex;
    justify-content: flex-start;
    width:97%;
    cursor: pointer;
   
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 100%; 
    margin: 0px auto;
}
}
@media screen and (min-width:620px) {
    .story-and-post{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 50%; 
    margin: 0px auto;
}
}
@media screen and (min-width:1224px) {
    .story-and-post{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 50%; 
    margin: 0px auto;
}
}

</style>