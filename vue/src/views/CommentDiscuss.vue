<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { onMounted, onUpdated, reactive, ref, watch } from "vue";
import axiosClient from "../axios";
import moment from 'moment'
import { useRouter,useRoute } from "vue-router";
let route=useRoute();
const user_mail=localStorage.getItem('USER_MAIL');
let comment_data=reactive({
comment:"",
responses:""
});
let comment_id=route.params.comment_id;
let formData=new FormData();
formData.append("comment_id",comment_id);
axiosClient.post("/findComment",formData).then(response=>{
comment_data.comment=response.data.reply;
comment_data.responses=response.data.comment_replies;

}).catch(error=>{

});
function url_to_link(text) {
    const urlPattern = /(?:https?:\/\/|www\.)?(?:[\w-]+\.)+(?:[a-z]{2,})(\/\S*)?/gi;
    if (!urlPattern.test(text)) {
        return text; // No URLs found, return original text
      }else{
        return text.replace(urlPattern, match => {
        const href = match.match(/^https?:\/\//i) ? match : `http://${match}`;
        return `<a style='font-weight:bold; color:purple;' href="${href}" target="_blank">${match}</a>`;
      });
      }
}
</script>
<template>
    <Header class="shadow-sm" />
   
     <img style="margin:0px auto;" v-if="comment_data.comment === ''" width="100"  height="100" src="../landing/loading-loader.gif" />
    <div v-else class="container" style="border:1px solid lightgray; cursor:pointer; position:relative;">
  <div  class="user-or-channel-info p-2">
    <h6 style="position:relative;"><RouterLink :to="`/user/${comment_data.comment.user_who_comment}`"><img v-if="comment_data.comment.profile_picture === null" src="../pictures/profile.png" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"  /><img v-else  style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" :src='`http://localhost:8000/storage/${comment_data.comment.profile_picture}`' /><span style="position:absolute; margin-top:10px; font-weight: bold; top:0px; margin-left:50px; display:inline;">{{comment_data.comment.first_name + '\t' +comment_data.comment.last_name}}</span></RouterLink><p class="date m-2">{{moment(comment_data.comment.created_at).fromNow()}}</p></h6>
  </div>
  <p  style="margin-bottom: 5px; white-space:pre-wrap;" v-html="url_to_link(comment_data.comment.comment)" class="m-2 post-caption"></p>
       
    </div>
    <div class="container">
        <div class="replies">
            <div v-for="x in comment_data.responses"  style="border:none; width: 100%; margin-top:10px;" class="card  card-default">
            <div  style="position:relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header d-flex">
                <div style="margin-right: auto;"><img v-if="x.profile_picture === null" src="../pictures/profile.png" style="border-radius: 50%; object-fit: cover; width: 40px; height: 40px;"> <img v-else style="border-radius: 50%; object-fit: cover; width: 40px; height: 40px; " :src='`http://localhost:8000/storage/${x.profile_picture}`'/><span style="position:absolute; top:0px; margin-left:50px; margin-top:10px;">{{x.first_name}}</span></div>
            </div>
            <p class="p-2" v-html="url_to_link(x.comment_reply)"></p>
        </div>
    </div>
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {

.green-text-bold{
    color: green;
    font-weight: bold;
    margin-bottom: 5px;
}
.submit{
    justify-content: flex-end;
}
.submit-btn{
    margin-left: auto;
    font-weight: bold;
}
.success{
    display: none;
}
.error{
    display: none;
}
.edit-container{
    width: 100%;
    margin:0px auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
}
.notify-img{
    border-radius: 50%;
    height: 40px;
    width: 40px;
    object-fit: cover;
}
.not-info{
    word-wrap: break-word;
}
.date{
    color:lightslategray;
    position:absolute;
    top:22px;
    left:40px;
    font-size:14px;
}
}
@media screen and (min-width:620px) {
    .edit-container{
    width: 50%;
    margin:0px auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
}
.green-text-bold{
    color: green;
    font-weight: bold;
    margin-bottom: 5px;
}
.submit{
    justify-content: flex-end;
}
.submit-btn{
    margin-left: auto;
    font-weight: bold;
}
.success{
    display: none;
}
.error{
    display: none;
}
.notify-img{
    border-radius: 50%;
    height: 40px;
    width: 40px;
    object-fit: cover;
}
.not-info{
    word-wrap: break-word;
}
.date{
    color:lightslategray;
    position:absolute;
    top:22px;
    left:40px;
    font-size:14px;
}
}
@media screen and (min-width:1224px) {
    .edit-container{
    width: 50%;
    margin:0px auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
}
.green-text-bold{
    color: green;
    font-weight: bold;
    margin-bottom: 5px;
}
.submit{
    justify-content: flex-end;
}
.submit-btn{
    margin-left: auto;
    font-weight: bold;
}
.success{
    display: none;
}
.error{
    display: none;
}
.edit-container{
    width: 50%;
    margin:0px auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
    display: flex;
    justify-content: flex-start;
    flex-direction: column;
}
.notify-img{
    border-radius: 50%;
    height: 40px;
    width: 40px;
    object-fit: cover;
    margin-right: 5px;
    display: inline;
}
.not-info{
    word-wrap: break-word;
}
.container{
    width:50%;
}
.date{
    color:lightslategray;
    position:absolute;
    top:22px;
    left:40px;
    font-size:14px;
}
}

</style>