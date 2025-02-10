<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import {onMounted, ref} from "vue";
import { watch } from 'vue';
import { reactive,defineProps } from 'vue';
import axios from "axios";
import CommunityPageHeader from "../component/CommunityPageHeader.vue";
import FetchCommunityPostComponent from "../component/FetchCommunityPostComponent.vue";
import ShowCommunitySkeletonLoader from "../component/ShowCommunitySkeletonLoader.vue"
import axiosClient from "../axios";
const router=useRouter();
const route=useRoute();
let user_mail=localStorage.getItem('USER_MAIL');
document.title=route.params.c_id;
let community_details=reactive({
info:"",
isLoading:"true",
});
onMounted(()=>{
let formData=new FormData();
formData.append("community_name",route.params.c_id);
axiosClient.post("/findCommunityDetails",formData).then(response=>{
community_details.info=response.data.reply;
community_details.isLoading="false";
}).catch(error=>{
console.log(error);
});
});

</script>
<template>
<ShowCommunitySkeletonLoader  v-if="community_details.isLoading==='true'" />
<div v-else class="channel-container">
    <CommunityPageHeader :info="{
        created_at:community_details.info.created_at,
        community_name:community_details.info.community_name,
        community_owner:community_details.info.community_owner,
        community_bio:community_details.info.community_bio,
        community_avatar:community_details.info.community_avatar,
        community_cover:community_details.info.community_cover,
        community_category:community_details.info.community_category,
        community_rules:community_details.info.community_rules,
        community_member_count:community_details.info.member_count,
    }" />
    <div class="navigation">
        <li class="list-unstyled fs-5"><RouterLink :to='`/showcommunity/${route.params.c_id}/top`'>Top</RouterLink></li>
        <li class="list-unstyled fs-5"><RouterLink :to='`/showcommunity/${route.params.c_id}/latest`'>Latest</RouterLink></li>
        <li class="list-unstyled fs-5"><RouterLink :to='`/showcommunity/${route.params.c_id}/about`'>About</RouterLink></li>
    </div>
    <router-view></router-view>
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .channel-container{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    width:100%;
    cursor: pointer;
   
}
.community-cover-bg{
    border-radius:5px;
    height:300px;
    width:100%;
}
.cover-photo{
   width:100%;
   object-fit:cover; 
   height:inherit;
}
.community-title{
    background-color:rgb(253, 253, 253);
    padding-top:10px;
    padding-left:20px;
    padding-bottom:10px;
}
.community-details{
    background-color:rgb(253, 253, 253);
    padding:10px 10px;
}
.category{
    border:2px solid grey;
    border-radius:20px;
    padding:5px 5px;
    text-align:center;
    width:100px;
    font-size:14px;
}
.navigation{
    display:flex; 
    flex-direction:row; 
    width:inherit; 
    background:grey;
    justify-content:space-around;
    margin-top:10px;
    width:100%;
    color:white;
}


}
@media screen and (min-width:620px) {
    .channel-container{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
}

.community-cover-bg{
    border-radius:5px;
    height:300px;
    width:100%;
}
.cover-photo{
   width:100%;
   object-fit:cover; 
   height:inherit;
}
.community-title{
    background-color:rgb(253, 253, 253);
    padding-top:10px;
    padding-left:20px;
    padding-bottom:10px;
}
.community-details{
    background-color:rgb(253, 253, 253);
    padding:10px 10px;
}
.category{
    border:2px solid grey;
    border-radius:20px;
    padding:5px 5px;
    text-align:center;
    width:100px;
    font-size:14px;
}
.navigation{
    display:flex; 
    flex-direction:row; 
    width:inherit; 
    justify-content:space-around;
    margin-top:10px;
    width:50%;
    color:white;
}
}
@media screen and (min-width:1224px) {
    .channel-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
    margin:0 auto;
    
}
.community-cover-bg{
    border-radius:5px;
    height:300px;
    width:100%;
}
.cover-photo{
   width:100%;
   object-fit:cover; 
   height:inherit;
}
.community-title{
    background-color:rgb(253, 253, 253);
    padding-top:10px;
    padding-left:20px;
    padding-bottom:10px;
}
.community-details{
    background-color:rgb(253, 253, 253);
    padding:10px 10px;
}
.category{
    border:2px solid grey;
    border-radius:20px;
    padding:5px 5px;
    text-align:center;
    width:100px;
    font-size:14px;
}
.navigation{
    display:flex; 
    flex-direction:row; 
    width:inherit; 
    justify-content:space-around;
    margin-top:10px;
    width:40%;
    color:white;
}
.navigation > li:hover{
    color:green;
    font-weight:bold;
    border-radius:5px;
    
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}

</style>