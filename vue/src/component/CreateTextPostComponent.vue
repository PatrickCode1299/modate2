
<script setup>
import store from '../store';
import axiosClient from '../axios';
import { reactive, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
const router=useRouter();
const user_mail=localStorage.getItem('USER_MAIL');
let caption=ref();
let hold_picture=reactive({
    picture:"",
    isLoadingStoryPic:"true",
});
watch(caption, ()=>{
if(caption.value.trim().length > 0){
   let post_button=document.getElementById("post-button");
   post_button.removeAttribute("disabled");
}else{
    let post_button=document.getElementById("post-button");
   post_button.setAttribute("disabled","true");
}
});
let taglist=reactive({
tagged_users:[],
tagged_users_result:{}
});
let all_tagged_users=reactive({
info:[],
value:[]
}); 
watch(caption, ()=>{
let str = caption.value;
let pattern = /\B@[a-z0-9_-]+/gi;
let user_match=str.match(pattern);
if(user_match === null)
return;
else{
    user_match.forEach(x => {
    taglist.tagged_users=x;
    
});
document.getElementById("tag_box").style.display="block";
let formData=new FormData();
let tagged_user=taglist.tagged_users.substring(1);
formData.append('search_info',tagged_user);
axiosClient.post("/Search",formData).then(response=>{
taglist.tagged_users_result=response.data.reply;
}).catch(e=>{
    console.log(e);
})

}

});
function sendUserPost(e){
    e.preventDefault();
    e.stopImmediatePropagation();
    if(all_tagged_users.info.length==0){
    axiosClient.post("/createUserPost",{email:user_mail, user_caption:caption.value, tagged_users:""}).then(response=>{
        findNewestPost();
    }).catch(e=>{
        alert("Network Error");
    });
    }else{
        axiosClient.post("/createUserPost",{email:user_mail, user_caption:caption.value, tagged_users:all_tagged_users.info}).then(response=>{
        findNewestPost();
    }).catch(e=>{
        alert("Network Error");
    });
    }
   
    caption.value="";
}
function findNewestPost(){
    axiosClient.post("/fetchNewPost",{email:user_mail}).then(response=>{
    from_user_sent.date=response.data.reply.created_at;
    from_user_sent.name=response.data.reply.name;
    from_user_sent.avatar=response.data.reply.avatar;
    from_user_sent.caption=response.data.reply.caption;
    from_user_sent.email=response.data.reply.email;
    from_user_sent.postid=response.data.reply.postid;


    }).catch(err=>{
        console.log(err);
    });
}
let from_user_sent=reactive({
    date:"",
    name:"",
    avatar:"",
    caption:"",
    email:"",
    postid:""
});





watch(from_user_sent, ()=>{
    sessionStorage.removeItem('NEW_POST_DATE');
    sessionStorage.removeItem('NEW_POSTER_NAME');
    sessionStorage.removeItem('NEW_POST_AVATAR');
    sessionStorage.removeItem('NEW_POST_CAPTION');
    sessionStorage.setItem('NEW_POST_DATE',from_user_sent.date);
    sessionStorage.setItem('NEW_POSTER_NAME',from_user_sent.name);
    sessionStorage.setItem('NEW_POST_AVATAR',from_user_sent.avatar);
    sessionStorage.setItem('NEW_POST_CAPTION',from_user_sent.caption);
    sessionStorage.setItem('NEW_POST_POSTID',from_user_sent.postid);
    sessionStorage.setItem('NEW_POST_EMAIL',from_user_sent.email);
    location.reload();
});





function setTaggedUser(email,first_name,last_name){
    let user_to_get_tagged_email=atob(email);
    all_tagged_users.info.push(user_to_get_tagged_email);
    let user_full_name=first_name +'\t'+ last_name;
    all_tagged_users.value.pop();
    all_tagged_users.value.push(user_full_name);
    let name_suggestion_from_user=caption.value;
    //let string_pattern='/'+name_suggestion_from_user.substring(1)+'/g';
   let user_occurence=all_tagged_users.info.filter(x => x==user_to_get_tagged_email).length;
   if(user_occurence > 1){
   for(let i=0; i<all_tagged_users.info.length; i++){
    if(all_tagged_users.info[i]==user_to_get_tagged_email){
        all_tagged_users.info.splice(i,1);
        
    }
   }
   for(let i=0; i<all_tagged_users.value.length; i++){
    if(all_tagged_users.value[i]==user_full_name){
        all_tagged_users.value.splice(i,1);
       
    }
   }
   }
   let current_caption=caption.value;
   caption.value=current_caption.replace(/@\w+/,"")+all_tagged_users.value.join(" ");
   document.getElementById("tag_box").style.display="none";
    
    
}
function hideTagBox(){
    let tag_box=document.getElementById("tag_box");
    tag_box.style.display="none";
}
function hideTextPost(){
    document.getElementById('text_post').style.display='none';
}
function createNewPost(){
    document.getElementById('text_post').style.display='block';
}
</script>
<template>
<div class="stories-and-div-container">

<div style="position: relative;" class="user-post">
    <button @click="createNewPost" id="create_new_post" class="btn create_new_post  p-2 m-2 fs-1 btn-md">&plus;</button>
    <div id="text_post" class='user-text-post shadow-md'>
    <span @click="hideTextPost" class="back-arrow font-bold fs-1 cursor-pointer">&times;</span>
    <form style="margin:0px auto;" @submit="sendUserPost" class="user-posting-form">
        <textarea style="outline: none; outline: 0; outline-style: none; margin:0px;  " v-model="caption" class="post-text form-control" placeholder="Feeling good write something.."></textarea>
        <span class="position-to-right"><button style="margin-top:5px;" id="post-button" disabled   class="btn border-20px btn-md btn-success">Post</button></span>
    </form>
    <div id="tag_box" class="card tag_users_box card-default cursor-pointer p-2" style="position: absolute; top:155px; z-index:1; left:10px; overflow-x: hidden;  overflow-y: scroll;  height:200px; border-radius:5px; width:auto;">
        <div style="display:block;"><span @click="hideTagBox" class="m-2"><i class="fa fa-arrow-left"></i></span></div>
            <li @click="setTaggedUser(u.email,u.first_name,u.last_name)" class="list-unstyled m-4" v-for="u in taglist.tagged_users_result"><img v-if="u.profile_picture === null || u.profile_picture === 'null'" loading="lazy" style="border-radius: 50px; width: 40px; height:40px; object-fit: cover; float:left;"  src="../pictures/profile.png"  ><img v-else loading="lazy" style="border-radius: 50px; width: 40px; height:40px; object-fit: cover; float:left;"  :src="`https://res.cloudinary.com/fishfollowers/image/upload/${u.profile_picture}`"   ><span class="m-2">{{ u.first_name + '\t' + u.last_name }}</span></li>
    </div>
    </div>
</div>
</div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .story-posts-container{
    position: fixed; 
    background-color: rgba(0, 0, 0, 0.979); 
    height: 100%; 
    left: 0px;
    top: 0px;
    display: none;
    width: 100%;
    z-index: 1;
}
.magenta{
    border:2px solid rgb(255, 110, 255);
}
.story-keeper{
    display:block;
    width: 100%;
    position: relative;
    height: 100%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 100%;
    height:auto;
    border-radius: 0px;
    margin-left: 0px;
    
}
.border-50px{
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    position: absolute;
    top:5px;
    font-weight: bold;
    background-color: rgb(255, 211, 211);
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

}
.user-post{
    width: 100%;
    margin-top:0px;
   
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 100%; 
    margin: 0px auto;
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-radius: 10px;
}
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    opacity: 0.8;
    padding: 0px;
    position: relative;
    
}
.stories-and-div-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 0px;
    width: 100%;
    padding:0px 0px;

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
textarea:focus{
    outline: none;
    outline-style: none;
}
.post-text:hover{
    outline: none;
    border: none;
}
.post-text:hover{
    outline: none;
  
}
.position-to-right{
    display: flex;
    justify-content: flex-end;
    
}
.border-20px{
    border-radius: 20px;
    font-weight: bold;
    width: 100px;
}
.btn-default{
background-color:rgba(0,0,0,0.8);
}
.story-count-indicator{
    position: fixed;
    max-width:100%;
    z-index:1;
}
.user-posting-form{
    width:100%;
    padding:0px 0px;
}
#left{
    position: absolute; 
   top: 40%; 
    z-index: 1; 
    color:white; 
    font-weight:bold; 
    font-size:35px; 
    left: 40px;
}
#right{
   position: absolute; 
   top: 40%; 
   z-index: 1; 
   color:white; 
   font-weight:bold; 
   font-size:35px; 
   right: 40px;
}
.tag_users_box{
    display:none;
}
.prev{
    position:absolute;
    top:100px;
    left:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:50px;
    width:50px;
}
.next{
    position:absolute;
    top:100px;
    right:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:50px;
    width:50px;
}
.spinner {
  position: absolute;
  top: 80%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid #f3f3f3; /* Light grey */
  border-top: 8px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.tag_box{
    display:none;
}
.user-text-post{
    background:rgb(255, 255, 255);
    position:fixed;
    width:100%;
    height:100vh;
    border-radius:10px;
    z-index:1;
    top:0px;
    padding-top:0px;
    padding-left:5px;
    padding-right:5px;
   display:none;
}
.create_new_post{
    position:fixed; 
    background-color:rgba(255, 22, 255, 0.607); 
    color:white; 
    right:0px; 
    border-radius:50px; 
    height:auto; 
    width:40px; 
    z-index:1;
    bottom:300px;
}
}
@media screen and (min-width:620px) {
    .story-posts-container{
    position: fixed; 
    background-color: rgba(0, 0, 0, 0.979); 
    height: 100%; 
    left: 0px;
    top: 0px;
    display: none;
    width: 100%;
    z-index: 1;
}
.magenta{
    border:2px solid rgb(255, 110, 255);
}
.story-keeper{
    display: flex;
    flex-direction:column;
    justify-content: center;
    align-items: center;
    width:400px;
    position: relative;
    height: 100%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 100%;
    height:auto;
    border-radius: 5px;
    margin-left: 5px;
    object-fit:cover;
}
.border-50px{
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    position: absolute;
    top:5px;
    font-weight: bold;
    background-color: rgb(255, 211, 211);
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

}
.user-post{
    width: 100%;
   
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 100%; 
    margin: 0px auto;
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-radius: 10px;
}
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    opacity: 0.8;
    padding: 0px;
    position: relative;
    
}
.stories-and-div-container{
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    margin-top: 0px;
    width: 50%;

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
textarea:focus{
    outline: none;
    outline-style: none;
}
.post-text:hover{
    outline: none;
    border: none;
}
.post-text:hover{
    outline: none;
  
}
.position-to-right{
    display: flex;
    justify-content: flex-end;
    
}
.border-20px{
    border-radius: 20px;
    font-weight: bold;
    width: 100px;
}
.btn-default{
background-color:rgba(0,0,0,0.8);
}
.story-count-indicator{
    position: fixed;
    max-width:100%;
    z-index:1;
}
.user-posting-form{
    width:100%;
}
#left{
    position: absolute; 
   top: 40%; 
    z-index: 1; 
    color:white; 
    font-weight:bold; 
    font-size:35px; 
    left: 0px;
}
#right{
   position: absolute; 
   top: 40%; 
   z-index: 1; 
   color:white; 
   font-weight:bold; 
   font-size:35px; 
   right: 0px;
}
.tag_users_box{
    display:none;
}
.prev{
    position:absolute;
    top:100px;
    left:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:80px;
    width:80px;
}
.next{
    position:absolute;
    top:100px;
    right:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:80px;
    width:80px;
}
.spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid #f3f3f3; /* Light grey */
  border-top: 8px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.tag_box{
    display:none;
}
.user-text-post{
    background:rgb(250, 249, 249);
    position:fixed;
    width:50%;
    height:400px;
    margin:0px auto;
    border-radius:10px;
    z-index:1;
    top:120px;
    padding-top:0px;
    display:none;
}
.create_new_post{
    position:fixed; 
    background-color:rgba(255, 22, 255, 0.65); 
    color:white; 
    right:0px; 
    border-radius:50px; 
    height:80px; 
    width:80px; 
    bottom:150px;
}
}
@media screen and (min-width:1224px) {
    .story-posts-container{
    position: fixed; 
    background-color: rgba(0, 0, 0, 0.979); 
    height: 100%; 
    left: 0px;
    top: 0px;
    display: none;
    width: 100%;
    z-index: 1;
}
.magenta{
    border:2px solid rgb(255, 110, 255);
}
.story-keeper{
    display: flex;
    flex-direction:column;
    justify-content: center;
    align-items: center;
    width:400px;
    position: relative;
    height: 100%;
    background-color:rgba(0, 0, 0, 0.144);
    margin-top: 0px;
    margin: 0 auto;
}
.story-img{
    width: 100%;
    height:auto;
    border-radius: 0px;
    margin-left: 0px;
    object-fit:cover;
}
.border-50px{
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    position: absolute;
    top:5px;
    font-weight: bold;
    background-color: rgb(255, 211, 211);
}
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom:0px;
}
.user-post{
    width: 100%;
    margin-top:0px;
   
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 100%; 
    margin: 0px auto;
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-top-left-radius: 10px;
border-top-right-radius:10px;
border-bottom-left-radius: 5px;
border-bottom-right-radius: 5px;
}
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    opacity: 0.8;
    padding: 0px;
    position: relative;
    
}
.stories-and-div-container{
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    margin-top: 0px;
    margin:0px auto;
    width: 50%;

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
textarea:focus{
    outline: none;
    outline-style: none;
}
.post-text:hover{
    outline: none;
    border: none;
}
.post-text:hover{
    outline: none;
  
}
.position-to-right{
    display: flex;
    justify-content: flex-end;
    
}
.border-20px{
    border-radius: 20px;
    font-weight: bold;
    width: 100px;
}
.btn-default{
background-color:rgba(0,0,0,0.8);
}
.story-count-indicator{
    position:fixed;
    max-width:100%;
    z-index:1;
}
.user-posting-form{
    width:80%;
}
#left{
    position: absolute; 
   top: 40%; 
    z-index: 1; 
    color:white; 
    font-weight:bold; 
    font-size:35px; 
    left: 0px;
}
#right{
   position: absolute; 
   top: 40%; 
   z-index: 1; 
   color:white; 
   font-weight:bold; 
   font-size:35px; 
   right: 0px;
}
.tag_users_box{
    display:none;
}
.prev{
    position:absolute;
    top:100px;
    left:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:80px;
    width:80px;
}
.next{
    position:absolute;
    top:100px;
    right:0px;
    background-color:rgba(255, 255, 255, 0.744);
    color:black;
    font-size:35px;
    font-weight:bold;
    border-radius:50px;
    height:80px;
    width:80px;
}
.spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid #f3f3f3; /* Light grey */
  border-top: 8px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}
.tag_box{
    display:none;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.user-text-post{
    background:rgb(255, 255, 255);
    position:fixed;
    width:50%;
    height:50vh;
    margin:0px auto;
    border-radius:10px;
    z-index:1;
    top:120px;
    padding-top:0px;
    display:none;
}
.create_new_post{
    position:fixed; 
    background-color:rgba(255, 22, 255, 0.631); 
    color:white; 
    right:0px; 
    border-radius:50px; 
    height:80px; 
    width:80px; 
    bottom:100px;
}
}
</style>