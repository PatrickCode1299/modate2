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
let user_post_id=defineProps(['post_id']);
let post_id=user_post_id.post_id;
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
function deletePost(postid){
    var ask_if_user_wants_to_delete_post=confirm("Are you sure, you want to Delete This Post?");
    if(ask_if_user_wants_to_delete_post){
    let formData=new FormData();
    formData.append("user",user_mail);
    formData.append("postid",postid);
    axiosClient.post("/deleteUserPost",formData).then(response=>{
        document.getElementById('post'+postid).style.display="none";
    }).catch(error=>{
        console.log("error");
    });
    }else{
       return;
    }
}
function disableComment(postid){
    var ask_if_user_wants_to_disable_comment=confirm("Are you sure, you want to Stop People from Commenting on this Post?");
    if(ask_if_user_wants_to_disable_comment){
    let formData=new FormData();
    formData.append("user",user_mail);
    formData.append("postid",postid);
    axiosClient.post("/disableUserComment",formData).then(response=>{
       
    }).catch(error=>{
        console.log("error");
    });
    }else{
       return;
    }
}
</script>
<template>
    <span style="position: relative;" @click="displayBlockReportList(post_id)" @dblclick="hideBlockReportList(post_id)"><i class="fas fa-ellipsis-h"></i>
    <ul :id='`report${post_id}`' class="p-2 block-report-list shadow-md list-unstyled">
      <li @click="deletePost(post_id)" style="color:red;" class="font-bold"><i class="fa-regular fa-trash-can"></i>Delete </li>
      <li @click="disableComment(post_id)" class="font-bold"><i class="fa-regular fa-comment"></i>Disable comment</li>
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

    


}
.block-report-list > li{
    margin-top:20px;
    font-size: 14px;
}
</style>
