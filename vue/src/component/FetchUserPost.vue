<script setup>
import store from '../store';
import axiosClient from '../axios';
import { ref } from 'vue';
import { reactive } from 'vue';
import { defineProps } from 'vue';
const latest_post=defineProps(['latest']);
const x=1;
const user_mail=sessionStorage.getItem('USER_MAIL');
var post_date;
var poster_name;
var post_avatar;
var post_caption;
let store_all_post=reactive({
    post_date:"",
    poster_name:"",
    post_avatar:"",
    post_caption:"",
    isLoading:"true",

});
let newest_post=reactive({
    date:"",
    name:"",
    avatar:"",
    caption:"",

});
function showlatest(){
    newest_post.date=latest_post.latest.date;
    newest_post.name=latest_post.latest.name;
    newest_post.avatar=latest_post.latest.avatar;
    newest_post.caption=latest_post.latest.caption;
}
function fetchUserPost(e){
    axiosClient.post("/fetchUserPost",{email:user_mail}).then(response=>{
    post_date=response.data.reply.created_at;
    poster_name=response.data.reply.name;
    post_avatar=response.data.reply.avatar;
    post_caption=response.data.reply.caption;
    sessionStorage.setItem('POST_DATE',post_date);
    sessionStorage.setItem('POSTER_NAME',poster_name);
    sessionStorage.setItem('POST_AVATAR',post_avatar);
    sessionStorage.setItem('POST_CAPTION',post_caption);
    }).catch(err=>{
        console.log(err);
    });
   
}
let all_post=reactive({
    first_five_post:"",
    first_post_name:"",

    every_one_post:""
})
function fetchAllPost(){
    axiosClient.post('/fetchAllPost',{email:user_mail}).then(response=>{
        store_all_post.isLoading="false";
        all_post.first_five_post=response.data.reply;
    }).catch(e=>{
        console.log(e);
    });
    window.onscroll=function (){
        //alert('User tried to scroll...');
    }
    
}

store_all_post.post_date=sessionStorage.getItem('POST_DATE');
store_all_post.poster_name=sessionStorage.getItem('POSTER_NAME');
store_all_post.post_avatar=sessionStorage.getItem('POST_AVATAR');
store_all_post.post_caption=sessionStorage.getItem('POST_CAPTION');
fetchAllPost();
</script>
<template>
{{ fetchUserPost() }}
<div class="stories-and-div-container m-2 ">
<div class="user-post-holder">
    {{showlatest()}}
<div class='m-2' style="width: 100%;" v-for="i in x">
    <div style='border: none; border-radius: 0px;' class='card p-4 post-container card-default'>
    <div class='card-header inline-flex p-2 panel-header'>
        <span style="margin-right: auto;"><img :src='`http://localhost:8000/storage/${newest_post.avatar}`' class='img-circle small-thumbnail'></span><span class='m-2'>{{store_all_post.poster_name}}</span>
    </div>
    <p class='p-4 fs-6'>{{ newest_post.caption }}</p>
    <ul class='inline-flex'>
        <li class='list-unstyled'>{{newest_post.date}}</li>
    </ul>
   </div>
   <span v-if="store_all_post.isLoading==='true'" class="text-bold cursor-pointer fs-4">Loading......</span>
   <div v-else class='m-2' style="width: 100%;" v-for="x in all_post.first_five_post">
    <div style='border: none; border-radius: 0px;' class='card p-4 post-container card-default'>
    <div class='card-header inline-flex p-2 panel-header'>
        <span style="margin-right: auto;"><img :src='`http://localhost:8000/storage/${x.profile_picture}`' class='img-circle small-thumbnail'></span><span class='m-2'>{{x.name}}</span>
    </div>
    <p class='p-4 fs-6'>{{ x.caption }}</p>
    <ul class='inline-flex'>
        <li class='list-unstyled'>{{x.created_at}}</li>
    </ul>
   </div>
   </div>
</div>
</div>
</div>
</template>
<style scoped>
.user-post-holder{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

}
.inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
}
.user-post{
    width: 80%;
   
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-radius: 5px;
}
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    border:2px solid magenta;
    opacity: 0.8;
    padding: 0px;
    
}
.stories-and-div-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 0px;
    width: 100%;

}
.stories-and-div-container > div{
    margin-top: 10px;
}
.post-text{
    border-radius: 5px;
    resize: none;
    padding: 10px 10px;
    color: rgb(17, 17, 17);
    font-weight: 400;
    width:100%;
    height:auto;
    border: none;
    outline: none;
}
.post-text:hover{
    outline: none;
  
}
.post-container{
    display: flex;
    flex-direction: column;
    justify-content: space-around;
}
</style>