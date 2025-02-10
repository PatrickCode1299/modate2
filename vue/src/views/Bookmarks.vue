<script setup>
import axios from "axios";
import store from "../store";
import axiosClient from "../axios.js";
import { useRouter } from "vue-router";
import { reactive } from "vue";
import BookmarkPosts from "../component/BookmarkPosts.vue";
import { defineAsyncComponent } from "vue";
let user_mail;
const Header=defineAsyncComponent({
   loader:  () => import("../component/Header.vue"),
  
});
const SideNav=defineAsyncComponent({
    loader: () => import("../component/SideNav.vue")
});

let info=reactive({
    info_value:"",
});
function checkIfUserHasCompleteProfile(){
    user_mail=store.state.user.data;
    axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
       info.info_value="true";
       localStorage.setItem('INCOMPLETE',info.info_value);
       
    }

})).catch((error =>{
    console.log(error);
}))
}
document.title='Bookmarks';


</script>
<template>
    {{ checkIfUserHasCompleteProfile() }}
    <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;"/>
    <div v-if="info.info_value==='true'" style="margin-top: 100px;" class=" incomplete d-flex justify-content-center">
        <h2 class="fs-5 m-4 font-bold">Click on profile icon and complete your profile to be Visible</h2>
    </div>
    <div v-else  class="story-and-post">
    <BookmarkPosts  />
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