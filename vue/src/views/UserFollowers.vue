<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { onMounted, onUpdated, reactive, ref, watch } from "vue";
import axiosClient from "../axios";
import { useRouter, useRoute } from "vue-router";
const route=useRoute();
const user_mail=atob(route.params.uid);
let followers=reactive({
    all_followers:[],
    isLoading:"true",
})

onMounted(()=>{
let formData=new FormData();
formData.append('email',user_mail);
axiosClient.post("/findUserFollowers",formData).then(response=>{
followers.all_followers=response.data.reply;
followers.isLoading="false";
}).catch(e=>{
    console.log(e);
})
});
onMounted(()=>{
document.title='Followers';
});
function checkifInfoIsLong(text){
     if(text==null){
        return;
    }else if(text.length < 400){
        return text;
    }
    else if(text.length > 400){
        let caption_new=text.slice(0,400) + "....See More";
        return caption_new;
    }
}
</script>
<template>
    <Header style="position:fixed; z-index:1; top:0px; width:100%;" class="shadow-sm bg-white" />
    <div v-if="followers.isLoading==='true'" class="d-flex justify-content-center spinner align-items-center loading-icon">
    </div>
    <div class="container edit-container">
    <h2 class="fs-5 font-bold">Followers</h2>
    <ul style="margin-top:5px;">
      <li style="position:relative; margin-top:0px;"  v-for="x in followers.all_followers"><RouterLink class="d-flex"  :to='`/user/${x.follower}`'><img v-if="x.profile_picture === null" class="notify-img" src="../pictures/profile.png" ><img v-else class="notify-img" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`" /><span class="font-bold" style="display:inline; margin-left:5px;  margin-top:10px;">{{ x.first_name }}</span></RouterLink><span style="position:absolute; display:none; top:0px; right:0px;" class="follow-unfollow"><button style="border-radius:20px;" class="btn btn-sm btn-success fs-6 font-bold">Follow</button><button style="border-radius:20px;" class="btn btn-sm btn-danger fs-5 font-bold">Unfollow</button></span>
        <div style="position: relative; margin-bottom:40px;">
        <p  style="position:absolute; left:50px;">{{ x.cover_text }}</p>
      </div></li>  
    </ul>
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
    margin-top:80px;
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
    margin-top:50px;
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
}
@media screen and (min-width:1224px) {
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
    margin-top:80px;
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
}

</style>