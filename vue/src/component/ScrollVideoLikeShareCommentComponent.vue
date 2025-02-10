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
let user_post_id=defineProps(['post_id','post_content','post_owner']);
let post_id=user_post_id.post_id;
let post_content=user_post_id.post_content;
let comment_status=user_post_id.post_content.post_is_comment_status;
let isSound=localStorage.getItem('ISSOUND');
let current_user=localStorage.getItem('USER_MAIL');
let user_pic=localStorage.getItem('PICTURE');
let post_info=reactive({
    post_data:user_post_id.post_content,
});
let like_state=reactive({
    post_id:"",
    count:post_content.post_likes_count,
    commentCount:post_content.post_comments_count,
    shareCount:post_content.post_shares_count,
    quoteCount:post_content.post_shares_count,
});
let taglist=reactive({
tagged_users:[],
tagged_users_result:{}
});
let all_tagged_users=reactive({
info:[],
value:[]
}); 
onMounted(() =>{
    let formData=new FormData();
    formData.append("post_id",post_id);
    formData.append("email",current_user);
    axiosClient.post("/findAllPostUserLiked",formData).then(response=>{
       if(response.data.reply === 'true'){
        document.getElementById(post_id).style.color="red";
       }else{
        document.getElementById(post_id).style.color="white";
       }
     
    }).catch(e=>{
        console.log(e);
    });
});
function updateLike(){
    //go to database and check if user has liked post before.
    let formData=new FormData();
    formData.append("email",current_user);
    formData.append("post_id",post_id);
    axiosClient.post("/checkIfUserLiked",formData).then(response=>{
      if(response.data.reply==='false'){
        axiosClient.post("/DeleteLike",formData).then(response=>{
            like_state.count -=1;
            document.getElementById(post_id).style.color="white";
        }).catch(err=>{
            console.log(err)
        });
      }else{
        like_state.count +=1;
        document.getElementById(post_id).setAttribute("class","liked");
        document.getElementById(post_id).style.color="red";
      }
    }).catch(e=>{
        console.log(e);
    });
   
document.getElementById(post_id).style.color="red";
}
let router=useRouter();


async function share(){
  let post_link='https://hexarex.com/status/'+post_id;
  const shareData = {
  title: "From Hexarex",
  text: "See this Post on Hexarex.com",
  url:post_link,
};

   
    try {
    await navigator.share(shareData);
  } catch (err) {
    console.log(err);
  }

}




</script>
<template>
            <div :id="post_id" class="action-item" @click="updateLike(post_id)">
              <i class="fa-solid fa-heart"></i>
              <span style="display:block;">{{ like_state.count }}</span>
            </div>
            <div class="action-item">
             <RouterLink :to="`/status/${post_id}`"><i class="fa-light fa-comment-dots"></i>
              <span>{{ like_state.commentCount }}</span></RouterLink>
            </div>
            <div @click="share()" class="action-item">
              <i class="fa-light fa-share-all"></i>
              <span>{{ like_state.shareCount }}</span>
            </div>
</template>
<style scoped>
.video-feed {
  display: flex;
  flex-direction: column;
  overflow-y: scroll;
  width:100%;
  height: 100vh;
  scroll-snap-type: y mandatory;
}

.video-container {
  position: relative;
  max-width: 100%;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: black;
  scroll-snap-align: start;
  overflow: hidden;
}

.video-container video {
  width: 100%;
  max-height: 100%;
  object-fit: contain;
  background-color: black;
}

.video-container video.portrait {
  width: 100%;
  height: auto;
  object-fit: cover;
}

.video-container video.landscape {
  width: auto;
  height: 100%;
  object-fit: contain;
}

.play-pause-controls {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.play-btn {
  background: rgba(0, 0, 0, 0.5);
  border: none;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 10px 10px;
  transition: background-color 0.3s;
  color: white;
  font-size: 20px;
}

.play-btn i {
  color: white;
  font-size: 36px;
}

.play-btn:hover {
  background-color: rgba(0, 0, 0, 0.7);
}

.overlay {
  background-color:rgba(0, 0, 0, 0.767);
  position: absolute;
  bottom: 0px;
  left: 0px;
  color: white;
  width:100%;
}

.profile {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.profile img {
  border-radius: 50%;
  width: 40px;
  height: 40px;
  margin-right: 10px;
}

.caption {
  margin-bottom: 20px;
}

.actions {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: absolute;
  right: 20px;
  bottom: 80px;
}

.action-item {
  margin: 10px 0;
  text-align: center;
  font-size:25px;
}

.action-item i {
  font-size: 24px;
  cursor: pointer;
  transition: color 0.3s;
}

.action-item span {
  display: block;
  margin-top: 5px;
}

.action-item .liked {
  color: red;
}

.no-video {
  color: white;
}

/* Comments Modal */
.comments-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
}

.comments-content {
  background: white;
  border-radius: 8px;
  padding: 20px;
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
}

.comment-item {
  margin-bottom: 10px;
}
.liked{
    animation: bloodFlow 0.6s ease-out;
    font-size:20px;
    
}
@keyframes bloodFlow {
  0% {
    transform: scale(1);
  }
  30% {
    transform: scale(1.2);
  }
  50% {
    transform: scale(1.4);
  }
  70% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
  }
}

input[type="text"] {
  width: 100%;
  padding: 10px;
  margin-top: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
</style>
