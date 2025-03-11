<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import OldLikeShareComment from "../component/OldLikeShareComment.vue";
import UserPostSettings from "../component/UserPostSettings.vue";
import VideoPlayerComponent from "../component/VideoPlayerComponent.vue";
import moment from 'moment';
import { RouterLink } from 'vue-router';
  import axiosClient from "../axios.js";
  import { useStore } from 'vuex';
  import { useRoute } from 'vue-router';
  import { useRouter } from 'vue-router';
  import { onMounted, ref, reactive, watch } from 'vue';
  
  const router = useRouter();
  const route = useRoute();
  const store = useStore();
const user_mail=localStorage.getItem('USER_MAIL');
let info=reactive({
    info_value:"false",
    coverbgVal:"false",
    bgUrl:"",
    cover_text:""
});
let incomplete;
let option;
let personal_info=reactive({
        u_incomplete:incomplete,
     });
let isColor=ref(localStorage.getItem('ISCOLOR'));
let isSound=ref(localStorage.getItem('ISSOUND'));
let isPrivate=ref(localStorage.getItem('ISPRIVATE'));
axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
       info.info_value="true";
       localStorage.setItem('INCOMPLETE',info.info_value);
       incomplete=localStorage.getItem('INCOMPLETE');
       personal_info.u_incomplete=localStorage.getItem('INCOMPLETE');
    }else{
       info.info_value="false";
    }
}));
function logout() {
          var ask_if_user_wants_to_logout = confirm("Do you want to logout?");
          if (ask_if_user_wants_to_logout) {
            store.commit('logout');
            router.push({ name: 'welcome' });
          } else {
            var get_current_route = route.params.name;
            router.push({ name: get_current_route });
          }
}
function setIsPrivateTrue(){
option="isPrivateOn";
let formData=new FormData();
formData.append("option",option);
formData.append("email",user_mail);
axiosClient.post("/customiseProfile",formData).then(response=>{
    isPrivate.value='true';
    localStorage.removeItem("ISPRIVATE");
    localStorage.setItem("ISPRIVATE","true");

}).catch(error=>{
console.log(error);
});
alert("Private account on");
}
function setIsPrivateFalse(){
option="isPrivateOff";
let formData=new FormData();
formData.append("option",option);
formData.append("email",user_mail);
axiosClient.post("/customiseProfile",formData).then(response=>{
    isPrivate.value='false';
    localStorage.removeItem("ISPRIVATE");
    localStorage.setItem("ISPRIVATE","false");
}).catch(error=>{
console.log(error);
});
}
function setIsSoundTrue(){
option="isSoundOn";
let formData=new FormData();
formData.append("option",option);
formData.append("email",user_mail);
axiosClient.post("/customiseProfile",formData).then(response=>{
    isSound.value='true';
    localStorage.removeItem("ISSOUND");
    localStorage.setItem("ISSOUND","true");
}).catch(error=>{
console.log(error);
});
}
function setIsSoundFalse(){
option="isSoundOff";
let formData=new FormData();
formData.append("option",option);
formData.append("email",user_mail);
axiosClient.post("/customiseProfile",formData).then(response=>{
    isSound.value='false';
    localStorage.removeItem("ISSOUND");
    localStorage.setItem("ISSOUND","false");
}).catch(error=>{
console.log(error);
});
}
function setColorModeTrue(){
localStorage.removeItem("darkMode");
option="isDarkModeOff";
let formData=new FormData();
formData.append("option",option);
formData.append("email",user_mail);
axiosClient.post("/customiseProfile",formData).then(response=>{
    isColor.value='light';
    localStorage.removeItem("ISCOLOR");
    localStorage.setItem("ISCOLOR","light");
    location.reload();
}).catch(error=>{
console.log(error);
});

}
function setColorModeFalse(){
localStorage.setItem('darkMode',"enabled");
option="isDarkModeOn";
let formData=new FormData();
formData.append("option",option);
formData.append("email",user_mail);
axiosClient.post("/customiseProfile",formData).then(response=>{
    isColor.value='dark';
    localStorage.removeItem("ISCOLOR");
    localStorage.setItem("ISCOLOR","dark");
    location.reload();
}).catch(error=>{
console.log(error);
});

}
</script>
<template>
    <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
   <div  class="container settings-container">
    <ul>
        <li  class="fs-5 list-unstlyed cursor-pointer m-5"><span class="icon"><i class="fa-light fa-lock"></i></span>Privacy
            <div class="p-2">
               <span  class="m-2 fs-6"><span style="color:red;" @click="setIsPrivateTrue" v-if="isPrivate=='false'"><i class="fa-light fa-toggle-large-off"></i>Enable Private Profile</span><span style="color:forestgreen;" @click="setIsPrivateFalse" v-else > <i class="fa-light fa-toggle-large-on"></i>Disable Private Profile</span></span>
            </div>
        </li>
        <li     class="fs-5 list-unstlyed cursor-pointer m-5"><span class="icon"><i class="fa-light fa-shield-halved"></i></span>Security</li>
        <li     class="fs-5 list-unstlyed cursor-pointer m-5"><span class="icon"><i class="fa-light fa-microphone-slash"></i></span>Sound  <div class="p-2">
               <span  class="m-2 fs-6"><span style="color:red;" @click="setIsSoundTrue" v-if="isSound=='false'"><i class="fa-light fa-toggle-large-on"></i>Toggle Sound Off</span><span style="color:forestgreen;" @click="setIsSoundFalse" v-else ><i class="fa-light fa-toggle-large-off"></i>Toggle Sound On</span></span>
            </div></li>
        <li     class="fs-5 list-unstlyed cursor-pointer m-5"><span class="icon"><i class="fa-light fa-user-pen"></i></span>Appearance<div class="p-2">
               <span  class="m-2 fs-6"><span style="color:red;" @click="setColorModeTrue" v-if="isColor=='dark'"><i class="fa-light fa-toggle-large-on"></i>Disable Dark Mode</span><span style="color:forestgreen;" @click="setColorModeFalse" v-else ><i class="fa-light fa-toggle-large-off"></i>Enable Dark Mode</span></span>
            </div></li>
            <li     class="fs-5 list-unstlyed cursor-pointer m-5"><span class="icon"><i class="fa-light fa-user-pen"></i></span>History<div class="p-2">
               <RouterLink to="bookmarks"  class="m-2 fs-6"><span style="color:grey;">Bookmarks</span></RouterLink>
            </div></li>
           
        <li  @click="logout"   class="fs-5 list-unstlyed cursor-pointer m-5"><span class="icon"><i class="fa-light fa-right-from-bracket"></i></span>Log Out</li>
    </ul>
   </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .settings-container{
    display:block; 
    margin-top:80px; 
    max-width:100%;
}
.icon{
    margin-right:5px;
}
}
@media screen and (min-width:620px) {
    .settings-container{
    display:block; 
    margin-top:55px; 
    width:50%;
}
.icon{
    margin-right:20px;
}
}
@media screen and (min-width:1224px) {
.settings-container{
    display: block ; 
    margin-top:80px; 
    width:50%;
}
.icon{
    margin-right:20px;
}
}


</style>