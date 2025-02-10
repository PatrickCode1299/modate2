<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import {onMounted, ref} from "vue";
import { watch } from 'vue';
import { reactive,defineProps } from 'vue';
import CreateCommunityPostComponent from "./CreateCommunityPostComponent.vue";
import axios from "axios";
import axiosClient from "../axios";
const router=useRouter();
const route=useRoute();
let community_details=defineProps(['info']);
let user_mail=localStorage.getItem('USER_MAIL');
function shareCommunity(){
    alert("Trying to share community");
}
function hidebox(){
    document.getElementById("create_post_component").style.display="none";
}
function createPost(){
    document.getElementById("create_post_component").style.display="block";
}
function joinCommunity(){
    let formData=new FormData();
    formData.append("community_name",community_details.info.community_name);
    formData.append("current_user",user_mail);
    axiosClient.post("/joinCommunity",formData).then(response=>{
        alert("You Joined community");
    }).catch(error=>{
        console.log("error");
    })
}
</script>
<template>
<div class="community-header">
<div class="community-title shadow-md">
<h2 class="font-bold fs-4">{{ community_details.info.community_name }}</h2>
</div>
<div class="community-cover-bg">
<img src="../landing/asian.jpg" class="cover-photo"/>
</div>
<div class="community-details shadow-md">
    <h2 class="font-bold fs-3">{{ community_details.info.community_name }}
    <small class="category" style="display:block;">
        {{community_details.info.community_category}}
    </small>
    <p class="fs-6 m-2  font-normal">{{ community_details.info.community_bio }}</p>
    <small class="font-normal" style="display:block; padding-left:20px; margin-top:10px; font-size:16px;">
        {{community_details.info.community_member_count > 1 ? community_details.info.community_member_count +'\t'+'Members':community_details.info.community_member_count+'\t'+'Member'}}
    </small>
    <div class="d-flex" style="justify-content:flex-end;">
    <button @click="shareCommunity" style="margin-right:10px;"  class="btn btn-sm btn-success"><i class="fa fa-share"></i></button>
    <button @click="createPost"    class='btn btn-sm btn-success font-bold'>&plus;</button>
    <button @click="joinCommunity" v-if="user_mail != community_details.info.community_owner" style="margin-left:auto;" class="btn btn-sm btn-success">Join</button>
    </div>
</h2>
    
</div>
</div>
<div id='create_post_component' class='create_community_post shadow-sm'>
<span @click="hidebox" class='hide_community_post_component fs-2 font-bold'>&times;</span>
<CreateCommunityPostComponent :community_name='community_details.info.community_name' />
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
.create_community_post{
    display:none;
    position:fixed;
    width:100%;
    top:0px;
    height:100%;
    background-color:white;
    border-radius:5px;
    z-index:1;
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
.create_community_post{
    display:none;
    position:fixed;
    width:50%;
    top:100px;
    height:inherit;
    background-color:white;
    border-radius:5px;
    z-index:1;
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
    position:sticky;
    top:0px;
    width:auto;
    z-index:1;
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
.create_community_post{
    display:none;
    position:fixed;
    width:50%;
    top:100px;
    height:inherit;
    background-color:white;
    border-radius:5px;
    padding:40px 40px;
    z-index:1;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}

</style>