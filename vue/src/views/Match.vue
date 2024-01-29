<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import {ref,reactive} from "vue";
import StoriesandPost from "../component/StoriesandPost.vue";
import axiosClient from "../axios";
const currentDate=new Date();
const currentYear=currentDate.getFullYear();
const user_email=sessionStorage.getItem('USER_MAIL');
var setNextChoice=reactive({
    next_profile_pic:null,
    next_first_name:null,
    next_last_name:null,
    next_user_age:null,
    next_user_email:null,
});
var server_response=reactive({
    server_info:""
});
axiosClient.post('/find', {data:user_email}).then(response =>{
if(response.data.reply ===null){
    server_response.server_info="true";
}else{
    server_response.server_info="false";
    sessionStorage.setItem('USER_MATCH_FIRST_NAME',response.data.reply.first_name);
    sessionStorage.setItem('USER_MATCH_LAST_NAME',response.data.reply.last_name);
    sessionStorage.setItem('USER_MATCH_IMG',response.data.reply.profile_picture);
    sessionStorage.setItem('USER_MATCH_AGE',response.data.reply.birthday);
    sessionStorage.setItem('USER_MATCH_EMAIL',response.data.reply.email);
    let user_birthday=sessionStorage.getItem('USER_MATCH_AGE');
    var user_match_birthday=user_birthday.slice(0,4);
    var user_match_birthdate=parseInt(user_match_birthday);
    var user_match_age=currentYear-user_match_birthdate;
    setNextChoice.next_profile_pic=sessionStorage.getItem('USER_MATCH_IMG');
    setNextChoice.next_first_name=sessionStorage.getItem('USER_MATCH_FIRST_NAME');
    setNextChoice.next_last_name=sessionStorage.getItem('USER_MATCH_LAST_NAME');
    setNextChoice.next_user_email=sessionStorage.getItem('USER_MATCH_EMAIL');
    setNextChoice.next_user_age=user_match_age;
    setNextChoice.next_user_age=user_match_age+"\tYrs";

}


});


function findNextMatch(){
    sessionStorage.removeItem('USER_MATCH_IMG');
    sessionStorage.removeItem('USER_MATCH_FIRST_NAME');
    sessionStorage.removeItem('USER_MATCH_LAST_NAME');
    sessionStorage.removeItem('USER_MATCH_EMAIL');
    axiosClient.post('/find', {data:user_email}).then(response =>{
sessionStorage.setItem('USER_MATCH_FIRST_NAME',response.data.reply.first_name);
sessionStorage.setItem('USER_MATCH_LAST_NAME',response.data.reply.last_name);
sessionStorage.setItem('USER_MATCH_IMG',response.data.reply.profile_picture);
setNextChoice.next_profile_pic=response.data.reply.profile_picture;
setNextChoice.next_first_name=response.data.reply.first_name;
setNextChoice.next_last_name=response.data.reply.last_name;
setNextChoice.next_user_email=response.data.reply.email;

let birthday=response.data.reply.birthday;


    var user_match_birthday=birthday.slice(0,4);
   var user_match_birthdate=parseInt(user_match_birthday);
   var user_match_age=currentYear-user_match_birthdate;
    setNextChoice.next_user_age=user_match_age+"\tYrs";
   



});
}
setNextChoice.next_profile_pic=sessionStorage.getItem('USER_MATCH_IMG');
setNextChoice.next_first_name=sessionStorage.getItem('USER_MATCH_FIRST_NAME');
setNextChoice.next_last_name=sessionStorage.getItem('USER_MATCH_LAST_NAME');
setNextChoice.next_user_email=sessionStorage.getItem('USER_MATCH_EMAIL');

let match_error=ref();
function setUserMatch(e){
    e.stopImmediatePropagation();
    axiosClient.post("/choice",{
        user:user_email,
        choice:setNextChoice.next_user_email
    }).then(response=>{
        alert(response.data.success);
    }).catch(e=>{
        match_error.value="You already tried matching with this user, we have notified them";
        alert(match_error.value);
    });
}
</script>
<template>
    <Header />
    <SideNav />
    <div v-if="server_response.server_info==='true'" class="match-error d-flex justify-content-center align-item-center font-weight-bold">
        <h2 class="fs-3">We would show you matches as they appear</h2>
    </div>
    <div v-else class="story-and-post">
    <div class="user-match">
        <img class="user-match-img rounded" :src="`http://localhost:8000/storage/${setNextChoice.next_profile_pic}`" />
    </div>
    <div class="user-match-info">
    <h5 class="fs-3"><span class="m-2">{{ setNextChoice.next_first_name }}</span><span class="m-2">{{ setNextChoice.next_last_name}}</span></h5>
    <h5 class="fs-5">{{setNextChoice.next_user_age}}</h5>
    <h6 class="fs-5">Around You</h6>
    <div class="d-flex next-cancel">
    <button class="btn btn-success btn-sm" @click="findNextMatch">Next</button>
    <button class="btn btn-danger btn-sm" @click="setUserMatch">Choose</button>
    </div>
    </div>
    </div>
    

</template>
<style scoped>
@media screen and (min-width:400px) {
    .story-and-post{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
    margin:0px auto;
}
.user-match{
    flex: 1;
    height: 400px;

}
.user-match-img{
    width:100%;
    height: 400px;
    object-fit: cover;
    object-position: center;
}
.user-match-info{
    flex: 1;
    height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    position: relative;
}
.next-cancel{
    position:absolute;
    bottom: 0px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    width: 80px;
    border-radius:20px;
}
.match-error{
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-top: 15vh;
    font-weight: bold;
}
}
@media screen and (min-width:700px) {
    .story-and-post{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 50%;
    margin:0px auto;
}
.user-match{
    flex: 1;
    height: 400px;

}
.user-match-img{
    width:100%;
    height: 400px;
    object-fit: cover;
    object-position: center;
}
.user-match-info{
    flex: 1;
    height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    position: relative;
}
.next-cancel{
    position:absolute;
    bottom: 0px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    width: 80px;
    border-radius:20px;
}
}

</style>