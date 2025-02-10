<script setup>
import Header from "../component/Header.vue";
import Footer from "../component/Footer.vue";
import {ref, watch} from "vue";
import store from "../store";
import { useRouter,useRoute } from "vue-router";
import axiosClient from "../axios";
const router=useRouter();
const route=useRoute();
const link_date=atob(route.params.current_day);
if (link_date) {
    // Parse the link_date into a Date object
    const parsedLinkDate = new Date(link_date);

    // Get the current date without time (to only compare the day, month, and year)
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); // Reset time to midnight for accurate comparison

    // Also reset the time for parsedLinkDate (assuming link_date has no time or you want to compare only the date)
    parsedLinkDate.setHours(0, 0, 0, 0);

    // Compare the dates
    if (parsedLinkDate.getTime() === currentDate.getTime()) {
       
    }else {
       router.push({
        name:"Home"
       });
    }
}
const email=ref('');
const password=ref('');
const user={
    email:ref(email.value),
    password:ref(password.value)
}
let err=ref();

if(localStorage.getItem('USER_COOKIE')){
    let user_cookie=localStorage.getItem('USER_COOKIE');
   store.dispatch('cookieisSet',user_cookie);
   //router.push('/home');
}else{
    console.log('');
}
function updateNewPassword(e){
    e.preventDefault();
    let user_who_reset_password=atob(route.params.user_mail);
    if(password.value.length < 6){
     alert("Your password needs to be greater than six characters, ensure you use a strong password");
    }else{
    let formData=new FormData();
    formData.append("email",user_who_reset_password);
    formData.append("password",password.value);
    axiosClient.post("/updateDetails",formData).
    then(response=>{
     alert(response);
    }).catch(error=>{
     console.log(error);
    });
    }
    
}
</script>
<template>
<Header  class="shadow-sm" />
<main>
<div class="container signup-holder">
<div>
    <form @submit="updateNewPassword">
    <label for="password" class="form-label">Enter Your New Password</label>
    <input required v-model="password" placeholder="Enter your new password" class="form-control" type="password">   
    <button style="margin-top:10px;">Update</button>
    </form>
</div>
</div>
</main>
<Footer  class="footer-fixed-bottom"/>
</template>
<style scoped>
@media screen and (min-width:320px) {
    button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    color: white;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
input{
    border-radius: 5px;
}
.signup-holder{
    width:100%;
    margin:0px auto;
    margin-top:10vh;
    background-color: none;
}
main{
    background-color: none;
}
h2{
    font-weight: bold;
    margin-bottom: 25px;
}
form{
    margin-top: 20px;
}
.terms{
    color: black;
    font-weight: bold;
}
.footer-fixed-bottom{
position: static; 
bottom: 0px;
}
}
@media screen and (min-width:620px) {
    button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    color: white;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
input{
    border-radius: 5px;
}
.signup-holder{
    width:80%;
    margin:0px auto;
    margin-top:10vh;
    background-color: none;
}
main{
    background-color: none;
}
h2{
    font-weight: bold;
    margin-bottom: 25px;
}
form{
    margin-top: 20px;
}
.terms{
    color: black;
    font-weight: bold;
}
.footer-fixed-bottom{
position: fixed; 
bottom: 0px;
}
}
@media screen and (min-width:1224px) {
    button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    color: white;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
input{
    border-radius: 5px;
}
.signup-holder{
    width:50%;
    margin:0px auto;
    margin-top:2vh;
    padding-top: 20px;
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 20px;
    border-radius: 20px;
    
}
.sign-up-header{
    color: black;
}
main{
        background-image:none;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        background-position: center center;
    background-color: none;
    position: fixed;
    width: 100%;
    height: 100%;
}
h2{
    font-weight: bold;
    margin-bottom: 25px;
}
form{
    margin-top: 20px;
}
.terms{
    color: magenta;
    font-weight: bold;
}
.footer-fixed-bottom{
position: fixed; 
bottom: 0px;
}
.form-label{
    color: black;
    font-weight: bold;
}
.signup-info-tag{
    color: black;
}
}


</style>