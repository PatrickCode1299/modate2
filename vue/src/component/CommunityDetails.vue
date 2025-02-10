<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import {onMounted, ref} from "vue";
import moment from 'moment'
import { watch } from 'vue';
import { reactive} from 'vue';
import axiosClient from "../axios";
import PostSkeletonLoader from "./PostSkeletonLoader.vue";
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
<PostSkeletonLoader  v-if="community_details.isLoading==='true'" />
<div v-else class="channel-container">
    <h1 class="m-2 font-bold">Community Info</h1>
    <p class="m-2">{{ community_details.info.community_bio }}</p>
    <p class="m-2">This is a public community for now.</p>
    <h2 class="font-bold m-2">Date Created:</h2>
    <p class="m-2">{{ moment(community_details.info.created_at).format("MMMM D, YYYY") }}</p>
    <h2 class="m-2 font-bold">Rules</h2>
    <p class="m-2">Not available yet</p>
    <h2 class="m-2 font-bold">Owner</h2>
    <p class="m-2">{{community_details.info.community_owner}}</p>
    <h2 class="m-2 font-bold">Members Count</h2>
    <p class="m-2">{{community_details.info.member_count }}</p>
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