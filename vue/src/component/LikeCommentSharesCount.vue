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
let user_post_id=defineProps(['post_data']);
</script>
<template>
<ul class="inline-flex">
                        <li  class="m-2 list-unstyled"><i class="fa fa-regular fa-heart"></i><span>{{user_post_id.post_data.likes_count  }}</span><!--<span v-if="like_state.count < 2">{{ like_state.count }} Like</span><span v-else>{{ like_state.count }} Likes</span>--></li>
                        <li  class="m-2 list-unstyled"><i style="margin-right: 2px;" class="fa fa-regular fa-comment"></i><span>{{user_post_id.post_data.comment_count }}</span></li>
                        <li v-if="user_post_id.post_data.shares_count  > 0"   class="m-2 list-unstyled"><RouterLink :to='`/status/quotes/${user_post_id.post_data.status_id}`'><i class="far  fa-edit"></i><span>{{user_post_id.post_data.shares_count  }}</span></RouterLink></li>
                        <li   class="m-2 list-unstyled"><i class="fa-regular fa-newspaper"></i><span>{{user_post_id.post_data.bookmark_count  }}</span></li>
                        
  </ul>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content:space-between;
    cursor: pointer;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    
}
.flex-img > img{
    margin-left: 2px;
    object-fit: cover;
    width: 100px;
    height: 100px;
    
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    justify-content: center;
    align-items: center;
    padding: 0px;
    
}
.flex-img > img{
    margin-left: 2px;
    margin-top: 2px;
    object-fit: cover;
    flex: 1;
    flex-basis: 40%;
    height: 400px;
   
    
}
.quote-editor{
    display: none;
    position: fixed;
    height: 100%;
    width: 100%;
    background-color: rgba(255, 255, 255, 1);
    left: 0px;
    z-index: 1;
    top:0%;

}
.quote-editor > textarea{
    border: none;
    background-color: none;
}
.flex-video{
    padding: 0px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.flex-video > video{
    flex-basis: 30%;
    width:400px;
    height: 300px;
}
.card{
    border-radius: 0px;
    margin-top: 0px;
}
.user-opinion-div{
    display: flex;
    flex-direction: row;
}
.user-opinion-div > textarea{
    background:none;
    outline: none;
    width:100%;
    margin-left:10px;
    margin-bottom:5px;
    border-radius: 5px;

}
}
@media screen and (min-width:620px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    cursor: pointer;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    
}
.flex-img > img{
    margin-left: 2px;
    object-fit: cover;
    width: 100px;
    height: 100px;
    
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    justify-content: center;
    align-items: center;
    padding: 0px;
    
}
.flex-img > img{
    margin-left: 2px;
    margin-top: 2px;
    object-fit: cover;
    flex: 1;
    flex-basis: 40%;
    height: 400px;
   
    
}
.quote-editor{
    display: none;
    position: fixed;
    height: 50%;
    width: 50%;
    background-color: rgba(255, 255, 255, 1);
    left: 20px;
    right: 20px;
    z-index: 1;
    top:20%;
    margin:0px auto;

}
.quote-editor > textarea{
    border: none;
    background-color: none;
}
.flex-video{
    padding: 0px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.flex-video > video{
    flex-basis: 30%;
    width:400px;
    height: 300px;
}
.card{
    border-radius: 0px;
}
.user-opinion-div{
    display: flex;
    flex-direction: row;
}
.user-opinion-div > textarea{
    background:none;
    outline: none;
    width:100%;
    margin-left:10px;
    border-radius: 5px;
    background-color: white;
}
}
@media screen and (min-width:1224px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    cursor: pointer;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    
}
.flex-img > img{
    margin-left: 2px;
    object-fit: cover;
    width: 100px;
    height: 100px;
    
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    justify-content: center;
    align-items: center;
    padding: 0px;
    
}
.flex-img > img{
    margin-left: 2px;
    margin-top: 2px;
    object-fit: cover;
    flex: 1;
    flex-basis: 40%;
    height: 400px;
   
    
}
.quote-editor{
    display: none;
    position: fixed;
    height: 100%;
    width: 50%;
    background-color: rgba(255, 255, 255, 1);
    left: 20px;
    right: 20px;
    z-index: 1;
    top:8%;
    margin:0px auto;
    

}
.quote-editor > textarea{
    border: none;
    background-color: none;
}
.flex-video{
    padding: 0px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.flex-video > video{
    flex-basis: 30%;
    width:400px;
    height: 300px;
}
.card{
    border-radius: 0px;
}
.user-opinion-div{
    display: flex;
    flex-direction: row;
}
.user-opinion-div > textarea{
    background:none;
    outline: none;
    width:100%;
    margin-left:10px;
    border-radius: 5px;
    background-color: white;
}
}
</style>
