<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { ref,reactive,onMounted } from "vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
const user_mail=localStorage.getItem('USER_MAIL');
let all_messages=reactive({
    info:"",
    isLoading:"true"
});

onMounted(()=>{
let formData=new FormData();
formData.append('email',user_mail);
axiosClient.post("/findAllMessages",formData).then(response=>{
all_messages.isLoading="false";
all_messages.info=response.data.reply;
}).catch(e=>{
    console.log(e);
})
});
onMounted(()=>{
document.title='Messages';
});
function setMessageIsRead(date,chat_id,chat_sender){
    let day=date;
    let unique_id=chat_id;
    let sender=chat_sender;
    let formData=new FormData();
    formData.append("day",day);
    formData.append("unique_id",unique_id);
    formData.append("sender",sender);
    axiosClient.post("/updateMsgStatus",formData).catch(e=>{
        console.log("an error occured");
    });
}
function checkMsgLength(text){
  if(text===null){
    return "";
  }else if(text.length > 50){
        let caption_new=text.slice(0,100) + "......";
        return caption_new;
  }else{
        return text;
    }
     
}
</script>
<template>
     <Header class="shadow-sm" style="background-color:white; position:fixed; padding-bottom:10px;  width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
    <div class="container  p-2 edit-container">
        <div v-if="all_messages.isLoading==='true'" class="d-flex justify-content-center spinner align-items-center loading-icon">
        </div>
        <div v-else>
       <p @click="setMessageIsRead(x.created_at,x.unique_id,x.sender)" style="position: relative;" class="m-4" v-for="x in all_messages.info">
        <RouterLink  :to='`/chat/${x.unique_id}`'>
            <img v-if="x.profile_picture === null || x.profile_picture === 'null'" src="../pictures/profile.png" class="notify-img" />
            <img v-else class="notify-img" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`" />
            <span class="convo-name">{{x.first_name+'\t'+x.last_name}}</span>
            <p class="convo" style="color:grey;" v-if="x.isRead === 'false'"><b>{{checkMsgLength(x.conversation)}}</b></p>
            <p class="convo" style="color:grey;" v-else>{{checkMsgLength(x.conversation)}}</p>
        </RouterLink>
        </p>
        <h3 style="margin-top:50px; margin-bottom:50px;" class="fs-5 text-center" v-if="all_messages.info.length===0">You have no new messages yet..</h3>
        </div>

      
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .edit-container{
    width: 100%;
    margin:0px auto;
    height: auto;
    border-radius: 10px;
    margin-top: 50px;
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
    height: 50px;
    width: 50px;
    object-fit: cover;
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
.convo{
    margin-left: 20%; 
    margin-top:0px;
}
.convo-name{
    position: absolute; 
    margin-bottom:0px; 
    top:10%;
    left:20%;
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
    height: auto;
    border-radius: 10px;
    margin-top: 50px;
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
    height: 50px;
    width: 50px;
    object-fit: cover;
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
.convo{
    margin-left: 20%; 
    margin-top:0px;
}
.convo-name{
    position: absolute; 
    margin-bottom:0px; 
    top:10%;
    left:20%;
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
    height: auto;
    border-radius: 10px;
    margin-top: 50px;
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
    height: 50px;
    width: 50px;
    object-fit: cover;
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
.convo{
    margin-left: 10%; 
    margin-top:0px;
}
.convo-name{
    position: absolute;
     margin-bottom:0px;
      top:10%; 
      left:10%;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

}

</style>