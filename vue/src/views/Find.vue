<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { onMounted, onUpdated, reactive, ref, watch } from "vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
const user_mail=localStorage.getItem('USER_MAIL');
const currentDate=new Date();
const router=useRouter();
const currentYear=currentDate.getFullYear();
const user_email=localStorage.getItem('USER_MAIL');
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
if(response.data.reply ===null || response.data.reply==='User match not found'){
    server_response.server_info="true";
}else{
    server_response.server_info="false";
    localStorage.setItem('USER_MATCH_FIRST_NAME',response.data.reply.first_name);
    localStorage.setItem('USER_MATCH_LAST_NAME',response.data.reply.last_name);
    localStorage.setItem('USER_MATCH_IMG',response.data.reply.profile_picture);
    localStorage.setItem('USER_MATCH_AGE',response.data.reply.birthday);
    localStorage.setItem('USER_MATCH_EMAIL',response.data.reply.email);
    let user_birthday=localStorage.getItem('USER_MATCH_AGE');
    var user_match_birthday=user_birthday.slice(0,4);
    var user_match_birthdate=parseInt(user_match_birthday);
    var user_match_age=currentYear-user_match_birthdate;
    setNextChoice.next_profile_pic=localStorage.getItem('USER_MATCH_IMG');
    setNextChoice.next_first_name=localStorage.getItem('USER_MATCH_FIRST_NAME');
    setNextChoice.next_last_name=localStorage.getItem('USER_MATCH_LAST_NAME');
    setNextChoice.next_user_email=localStorage.getItem('USER_MATCH_EMAIL');
    setNextChoice.next_user_age=user_match_age;
    setNextChoice.next_user_age=user_match_age+"\tYrs";

}


});


function findNextMatch(email){
    localStorage.removeItem('USER_MATCH_IMG');
    localStorage.removeItem('USER_MATCH_FIRST_NAME');
    localStorage.removeItem('USER_MATCH_LAST_NAME');
    localStorage.removeItem('USER_MATCH_EMAIL');
    document.getElementById(email).removeAttribute("disabled");
    axiosClient.post('/find', {data:user_email}).then(response =>{
    if(response.data.reply===null){
            server_response.server_info='true';
    }else{
    localStorage.setItem('USER_MATCH_FIRST_NAME',response.data.reply.first_name);
    localStorage.setItem('USER_MATCH_LAST_NAME',response.data.reply.last_name);
    localStorage.setItem('USER_MATCH_IMG',response.data.reply.profile_picture);
    setNextChoice.next_profile_pic=response.data.reply.profile_picture;
    setNextChoice.next_first_name=response.data.reply.first_name;
    setNextChoice.next_last_name=response.data.reply.last_name;
    setNextChoice.next_user_email=response.data.reply.email;

    let birthday=response.data.reply.birthday;
    var user_match_birthday=birthday.slice(0,4);
    var user_match_birthdate=parseInt(user_match_birthday);
    var user_match_age=currentYear-user_match_birthdate;
        setNextChoice.next_user_age=user_match_age+"\tYrs";
    }
    
       



});
}
setNextChoice.next_profile_pic=localStorage.getItem('USER_MATCH_IMG');
setNextChoice.next_first_name=localStorage.getItem('USER_MATCH_FIRST_NAME');
setNextChoice.next_last_name=localStorage.getItem('USER_MATCH_LAST_NAME');
setNextChoice.next_user_email=localStorage.getItem('USER_MATCH_EMAIL');

let match_error=ref();
function setUserMatch(email){
    document.getElementById(email).setAttribute("disabled",true);
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
let user_search=ref('');
let live_search=reactive({
    result:""
})
watch(user_search,()=>{
let formData=new FormData();
formData.append("search_info",user_search.value);
axiosClient.post('/Search',formData).then(response=>{
   live_search.result=response.data.reply;
}).
catch(e=>{
        console.log(e);
});
   

});

</script>
<template>
    <Header />
    <SideNav style="display:none;" />
    <div class="container  p-2  edit-container">
    <input v-model="user_search" style="border-radius:5px;" class="search p-2  border-r-8" placeholder="Search Hexarex" />
    <div id="livesearch" class=" m-2 livesearch-bar">
        <li style="margin-top:20px;" class='list-unstyled d-flex' v-for="x in live_search.result" ><RouterLink class='d-flex' :to="`/user/${x.email}`"><img v-if="x.profile_picture === null" src="../pictures/profile.png" style="border-radius:50px; height:50px; width:50px;  object-fit:cover;"/><img v-else  style="border-radius:50px; height:50px; width:50px;  object-fit:cover;"  :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.profile_picture}`" /><span style='margin-top:10px; font-weight:500; margin-left:10px;'>{{x.first_name + '\t' + x.last_name}}</span></RouterLink></li>
    </div>
    </div>
    <div v-if="server_response.server_info==='true'" class="match-error d-flex justify-content-center align-item-center font-weight-bold">
        <h2 class="fs-3">We would show you users as they appear.</h2>
    </div>
    <div v-else class="story-and-post p-2">
    <div class="user-match" style="margin-top:100px;">
        <RouterLink :to='`/user/${setNextChoice.next_user_email}`'><img v-if="setNextChoice.next_profile_pic === null || setNextChoice.next_profile_pic === 'null'" class="user-match-img rounded" src="../pictures/profile.png"><img v-else class="user-match-img rounded" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${setNextChoice.next_profile_pic}`" /></RouterLink>
    </div>
    <div class="user-match-info">
    <h5 class="fs-3"><span class="m-2">{{ setNextChoice.next_first_name }}</span><span class="m-2">{{ setNextChoice.next_last_name}}</span></h5>
    <h6 class="fs-6">Around You</h6>
    <div class="d-flex next-cancel">
    <button class="btn btn-danger btn-sm font-bold" @click="findNextMatch(setNextChoice.next_user_email)">Next</button>
    <button :id="setNextChoice.next_user_email" class="btn btn-success btn-sm font-bold" @click="setUserMatch(setNextChoice.next_user_email)">Follow</button>
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
.story-and-post{
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
    
}
.user-match{
    flex: 1;
    height: 400px;

}
.user-match-img{
    width:200px;
    height:200px;
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
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    margin-top:10px;
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
@media screen and (min-width:620px) {
    .edit-container{
    width: 50%;
    margin:0px auto;
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
.user-match{
    flex: 1;
    height: 400px;

}
.user-match-img{
    width:200px;
    height: 200px;
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
    margin-top:40px;
    top:20px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    width: 80px;
    border-radius:20px;
    margin-top:10px;
}
.match-error{
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-top: 15vh;
    font-weight: bold;
}
.search-bar{
    margin-top:200px; 
    margin-bottom:20px;
}
.story-and-post{
    display: flex;
    flex-direction: row;
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
    width:200px;
    height: 200px;
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
    margin-top:0px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    margin-top:10px;
    width: 80px;
    border-radius:20px;
}
}
@media screen and (min-width:1224px) {
    .edit-container{
    width: 50%;
    margin:0px auto;
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
.user-match{
    flex: 1;
    height: 400px;

}
.user-match-img{
    width:200px;
    height: 200px;
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
    margin-top:40px;
    top:20px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    width: 80px;
    border-radius:20px;
    margin-top:10px;
}
.match-error{
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-top: 15vh;
    font-weight: bold;
}
.search-bar{
    margin-top:200px; 
    margin-bottom:20px;
}
.story-and-post{
    display: flex;
    flex-direction: row;
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
    width:200px;
    height: 200px;
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
    margin-top:0px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    margin-top:10px;
    width: 80px;
    border-radius:20px;
}
}


</style>