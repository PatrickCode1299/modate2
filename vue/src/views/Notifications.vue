<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import NotificationsSkeletonLoader from "../component/NotificationsSkeletonLoader.vue";
import store from "../store";
import { onMounted, onUpdated, reactive, ref, watch } from "vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
const user_mail=localStorage.getItem('USER_MAIL');
let all_notifications=reactive({
    info:"",
    isLoading:"true",
});

onMounted(()=>{
let formData=new FormData();
formData.append('email',user_mail);
axiosClient.post("/findNotifications",formData).then(response=>{
all_notifications.info=response.data.reply;
all_notifications.isLoading="false";
}).catch(e=>{
    console.log(e);
})
});
onMounted(()=>{
document.title='Notifications';
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
    <Header style="position:fixed; top:0px; width:100%;" class="shadow-sm bg-white" />
    <SideNav style="display:none;" />
    <div class="container  p-2  edit-container">
        <NotificationsSkeletonLoader v-if="all_notifications.isLoading==='true'"  />
        <div v-else>
       <p class="m-4" v-for="x in all_notifications.info"><RouterLink v-if="x.source==='match'" :to='`/user/${x.from}`'><span class='not-info' v-if="x.owner_has_read === 'false'"><img v-if="x.profile_picture === null" class="notify-img" src="../pictures/profile.png"><img v-else class="notify-img" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`" /><strong>{{checkifInfoIsLong(x.info)}}</strong></span><span class='not-info' v-else><img v-if="x.profile_picture === null" class="notify-img" src="../pictures/profile.png" /><img v-else class="notify-img" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`" />{{ checkifInfoIsLong(x.info)}}</span></RouterLink>
        <RouterLink v-else :to='`/status/${x.source}`'><span class='not-info' v-if="x.owner_has_read === 'false'"><img v-if="x.profile_picture === null" class="notify-img" src="../pictures/profile.png" ><img v-else class="notify-img" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`" /><strong>{{checkifInfoIsLong(x.info)}}</strong></span><span class="not-info" v-else><img v-if="x.profile_picture === null" class="notify-img" src="../pictures/profile.png"><img v-else class="notify-img" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`" />{{ checkifInfoIsLong(x.info) }}</span></RouterLink></p>
        <h3 style="margin-top:50px; margin-bottom:50px;" class="fs-5 text-center" v-if="all_notifications.info.length===0">There are no new notifications yet</h3>
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
    margin-top:50px;
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
    margin-top:50px;
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