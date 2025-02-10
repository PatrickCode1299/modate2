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
let user_post_id=defineProps(['post_id','post_owner']);
let post_id=user_post_id.post_id;
let post_owner=atob(user_post_id.post_owner);
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
function blockUser(){
    var ask_if_user_wants_to_block_poster=confirm("Are you sure, you want to Block This User?");
    if(ask_if_user_wants_to_block_poster){
    let user_getting_blocked=post_owner;
    let formData=new FormData();
    formData.append("user_who_block",user_mail);
    formData.append("user_getting_blocked",user_getting_blocked);
    axiosClient.post("/blockUser",formData).then(response=>{
    }).catch(err=>{
        console.log("Some error occured");
    });
    }else{
        console.log("No");
    }
}
</script>
<template>
    <span v-if="post_owner != user_mail" style="position: relative;" @click="displayBlockReportList(post_id)" @dblclick="hideBlockReportList(post_id)"><i class="fas fa-ellipsis-h"></i>
    <ul v-if="post_owner !=user_mail" :id='`report${post_id}`' class="p-2 block-report-list shadow-md list-unstyled">
      <li @click="blockUser" class="font-bold">Block User</li>
      <li @click="reportUser" class="font-bold">Report</li>
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
