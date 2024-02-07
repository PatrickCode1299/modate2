<script setup>
import axios from "axios";
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import StoriesandPost from "../component/StoriesandPost.vue";
import store from "../store";
import axiosClient from "../axios.js";
import { useRouter } from "vue-router";
import { reactive } from "vue";

let user_mail;
const router=useRouter();


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
    user_mail=store.state.user.data;
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
    <Header />
    <SideNav />
    <div v-if="info.info_value==='true'" class="incomplete d-flex justify-content-center">
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