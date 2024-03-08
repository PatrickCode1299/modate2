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

let current_user=sessionStorage.getItem('USER_MAIL');
let post_info=reactive({
    post_data:user_post_id.post_content,
})
let like_state=reactive({
    post_id:"",
    count:0,
    commentCount:0,
})

onMounted(()=>{
    let formData=new FormData();
    formData.append("post_id",post_id);
    axiosClient.post("/findPostLikeCounts",formData).then(response=>{
       
        if(response.data.reply > 1){
            like_state.count=response.data.reply;
        }else{
            like_state.count=response.data.reply;
        }
        
    }).catch(e=>{
        console.log(e);
    });
});
onMounted(()=>{
    let formData=new FormData();
    formData.append("post_id",post_id);
    axiosClient.post("/findPostCommentCounts",formData).then(response=>{
        like_state.commentCount=response.data.reply;
    }).catch(e=>{
        console.log(e);
    });
});
onMounted(() =>{
    let formData=new FormData();
    formData.append("post_id",post_id);
    formData.append("email",current_user);
    axiosClient.post("/findAllPostUserLiked",formData).then(response=>{
       if(response.data.reply === 'true'){
        document.getElementById(post_id).style.color="red";
       }else{
        document.getElementById(post_id).style.color="black";
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
            document.getElementById("unlike").play();
            document.getElementById(post_id).style.color="black";
        }).catch(err=>{
            console.log(err)
        });
      }else{
        like_state.count +=1;
        document.getElementById("like").play();
        document.getElementById(post_id).style.color="red";
      }
    }).catch(e=>{
        console.log(e);
    });
   
document.getElementById(post_id).style.color="red";
}
let router=useRouter();

function sharePost(){
    alert("User wants to share post");
}
</script>
<template>
  <ul class="inline-flex">
                        <li :id="post_id" @click="updateLike" class="m-2 list-unstyled"><i class="fa fa-regular fa-heart"></i><span v-if="like_state.count < 2">{{ like_state.count }} Like</span><span v-else>{{ like_state.count }} Likes</span></li>
                        <li  class="m-2 list-unstyled"><RouterLink :post_data="post_id" :to='`/status/${post_id}`'><i style="margin-right: 2px;" class="fa fa-regular fa-comment"></i>{{ like_state.commentCount }}</RouterLink></li>
                        <li @click="sharePost" class="m-2 list-unstyled"><i class="fa  fa-share"></i>Share</li>
</ul>
<audio style="display: none;"  id="like">
    <source type="audio/wav" src="../notifications/like bell.wav" />
    
</audio>
<audio style="display: none;"  id="unlike">
    <source type="audio/wav" src="../notifications/unlike bell.wav" />
    
</audio>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
}
@media screen and (min-width:620px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
}
@media screen and (min-width:1224px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
}
</style>
