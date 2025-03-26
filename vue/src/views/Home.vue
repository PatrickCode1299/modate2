<script setup>
import axiosClient from "../axios.js";
import { useRouter,useRoute } from "vue-router";
import { ref, reactive } from "vue";
import { defineAsyncComponent } from "vue";
const Header=defineAsyncComponent({
   loader:  () => import("../component/Header.vue"),
  
});
const HomeTopHeader=defineAsyncComponent({
   loader:  () => import("../component/HomeTopHeader.vue"),
  
});
const route=useRoute();
const SideNav=defineAsyncComponent({
    loader: () => import("../component/SideNav.vue")
});
const ScrollingVideoComponent=defineAsyncComponent({
    loader: () => import("../component/ScrollingVideoComponent.vue")
});
const StoriesandPostComponent=defineAsyncComponent({
    loader: () => import("../component/StoriesandPost.vue")
});
const ChannelPostComponent=defineAsyncComponent({
    loader: () => import("../component/ChannelPostComponent.vue")
});
const SharedPostComponent=defineAsyncComponent({
    loader: () => import("../component/SharedPostComponent.vue")
});
const StatusComponent=defineAsyncComponent({
    loader: () => import("../component/Status.vue")
});
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
function updateUserLastActivity(){
    user_mail=localStorage.getItem('USER_MAIL');
    axiosClient.post("/updateLastActivity",{email:user_mail}).then((response=>{
        
})).catch((error =>{
    console.log(error);
}));
}
function checkIfUserHasCompleteProfile(){
    user_mail=localStorage.getItem('USER_MAIL');
    axiosClient.post("/profile",{email:user_mail}).then((response=>{
    if(response.data.info==="false"){
       info.info_value="true";
       localStorage.setItem('INCOMPLETE',info.info_value);
       
    }

})).catch((error =>{
    const interval = setInterval(() => {
        if (navigator.onLine) {
            clearInterval(interval);
            location.reload();
        }
    }, 5000);
    console.log(error);
}))
}

const userIsHover = ref(false);

const checkIfUserHover = (hoverState) => {
  userIsHover.value = hoverState;
  
};
document.title='Home';
deleteOldStories();
updateUserLastActivity();

</script>
<template>
    {{ checkIfUserHasCompleteProfile() }}
    <HomeTopHeader v-if="userIsHover" class="shadow-md" style="background-color:rgb(254,254,254); position: fixed; width: 100%; z-index: 1; bottom: 0px;" />
    <Header v-if="userIsHover" id="header" class="shadow-md" style="background-color:white; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
    <div v-if="info.info_value==='true'" style="margin-top: 50px; flex-direction:column;" class=" incomplete d-flex justify-content-center align-items-center">
        <h2 class="fs-5 font-bold m-4">Click on profile icon above and complete your profile to be visible</h2>

    <video
    style="width:50%; height:400px; border:1px solid grey; padding:0px; border-radius:10px;" 
    autoplay
    controls
    src="https://res.cloudinary.com/fishfollowers/video/upload/v1727547032/users_posts/vxlj09dtsbpqouu4ueku.mp4"
    />
    </div>
    <div v-else  class="story-and-post">
    <ChannelPostComponent v-if="route.params.cat=='channelposts'" />
    <SharedPostComponent v-else-if="route.params.cat=='sharedposts'" />
    <StoriesandPostComponent v-else-if="route.params.cat=='stories'" />
    <StatusComponent v-else-if="route.params.cat=='status'" />
    <ScrollingVideoComponent @handle_header="checkIfUserHover" v-else />
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .story-and-post{
    display: flex;
    justify-content: flex-start;
    width:100%;
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