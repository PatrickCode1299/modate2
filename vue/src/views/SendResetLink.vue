<script setup>
import Header from "../component/Header.vue";
import Footer from "../component/Footer.vue";
import {ref, watch} from "vue";
import store from "../store";
import { useRouter,useRoute } from "vue-router";
import axiosClient from "../axios";
const router=useRouter();
const route=useRoute();

const email=ref('');
const user={
    email:ref(email.value),
}
let err=ref();
let server_res=ref('Enter your email, if you have an account with us you would be sent your password reset link.')
function sendResetLink(e){
    const resetButton = document.getElementById('reset_link_button');
resetButton.setAttribute("disabled", "true");  // Disable button immediately

user.email = email.value;
let formData = new FormData();
formData.append("email", user.email);
e.preventDefault();

axiosClient.post("/sendResetLink", formData)
    .then(response => {
        alert(response.data.reply);
        server_res.value = 'Kindly check your inbox for your password reset link. If you do not find it, check your spam folder.';
    })
    .catch(error => {
        console.log(error);
    })
    .finally(() => {
        resetButton.removeAttribute("disabled");  // Re-enable the button regardless of the request outcome
    });
}
</script>
<template>
<Header  class="shadow-sm" />
<main>
<div class="container signup-holder">
<div>
<h2 class="fs-2 sign-up-header font-weight-bold m-2">Enter your email address to continue</h2>
<form class="" method="POST" @submit="sendResetLink">
<div v-if="err != null" class="d-flex justify-content-center align-items-center text-danger border-radius-5 text-bold bg-danger text-white p-2">
{{ err }}
</div>
<div class="form-group">
<label for="password" class="form-label">Your Email</label>
<input required v-model="email" placeholder="patrick@example.com" class="form-control" type="email">
</div>
<button id="reset_link_button" style="margin-top:10px;">Reset</button>
<p class="signup-info-tag m-2">{{ server_res }}</p>
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