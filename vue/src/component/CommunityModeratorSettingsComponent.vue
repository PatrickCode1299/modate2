<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';
import { defineProps } from 'vue';
import axiosClient from '../axios';
import {ref} from "vue";
import { watch } from 'vue';
import { reactive } from 'vue';
const user_mail=localStorage.getItem('USER_MAIL');
let user_post_id=defineProps(['post_id','post_owner','post_admin','community_name']);
let post_id=user_post_id.post_id;
let post_owner=atob(user_post_id.post_owner);
let community_admin=user_post_id.post_admin;
function displayBlockReportList(post){
    let post_name="report"+post;
    let find_post=document.getElementById(post_name);
    find_post.style.display="block";
 
}
function hideBlockReportList(post){
    let post_name="report"+post;
    let find_post=document.getElementById(post_name);
    find_post.style.display="none";
 
}
function DeleteMemberPost(postid){
    var ask_if_user_wants_to_delete_post=confirm("Are you sure, you want to Delete This Post?");
    if(ask_if_user_wants_to_delete_post){
    let formData=new FormData();
    formData.append("user",user_mail);
    formData.append("postid",postid);
    axiosClient.post("/deleteCommunityPost",formData).then(response=>{
        document.getElementById('post'+postid).style.display="none";
    }).catch(error=>{
        console.log("error");
    });
    }else{
       return;
    } 
}
function reportUser(){
    alert("Trying to report User");
}
function removeMember(){
    var ask_if_user_wants_to_block_poster=confirm("Are you sure, you want to Remove this Member?");
    if(ask_if_user_wants_to_block_poster){
    let community_name=user_post_id.community_name;
    let evicted_member=post_owner;
    let formData=new FormData();
    formData.append("community_name",community_name);
    formData.append("evicted_member",evicted_member);
    axiosClient.post("/removeMember",formData).then(response=>{
    }).catch(err=>{
        console.log("Some error occured");
    });
    }else{
        console.log("No");
    }
}
</script>
<template>
    <span v-if="user_mail===community_admin || user_mail===post_owner" style="position: relative;" @click="displayBlockReportList(post_id)" @dblclick="hideBlockReportList(post_id)"><i class="fas fa-ellipsis-h"></i>
    <ul   :id='`report${post_id}`' class="p-2 block-report-list shadow-md list-unstyled">
    <li v-if="user_mail===community_admin || user_mail===post_owner" @click="DeleteMemberPost(post_id)" class="font-bold">Delete Post</li>
      <li v-if="user_mail===community_admin && user_mail !=post_owner"  @click="removeMember" class="font-bold">Remove Member</li>
      <li v-if="user_mail===community_admin" @click="reportUser" class="font-bold">Ban User</li>
    </ul>

    </span>
    
</template>
<style scoped>
.block-report-list{
    list-style: none;
    margin-top: 30px;
    background-color: rgb(255, 255, 255);
    position: absolute;
    right: 20%;
    top: 10%;
    z-index: 1;
    display: none;
    border-radius: 5px;
    width: 200px;
    border:1px solid lightgrey;

    


}
.block-report-list > li{
    margin-top:20px;
    font-size: 14px;
}
</style>
