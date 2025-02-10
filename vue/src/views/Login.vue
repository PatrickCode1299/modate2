<script setup>
import { RouterLink } from "vue-router";
import {ref} from "vue";
import Header from "../component/Header.vue";
import Footer from "../component/Footer.vue";
import store from "../store";
import { useRouter } from "vue-router";
const email=ref('');
const password=ref('');
const router=useRouter();
let err=ref();
const user={
    email:ref(email.value),
    password:ref(password.value)
}
function login(e){
    e.preventDefault();
    user.email=email.value;
    user.password=password.value
    store.dispatch('login',user)
    .then(()=>{
        router.push({
            name:'Home'
        })
    }).catch(e=>{
        err.value="Incorrect Email or Password";
        
        
    })
}
function hiderr(){
   err.value=null;
}
document.title='Login';
</script>
<template>
<Header  class="shadow-sm" />

<main>
<div class="container signup-holder">
<h2 class="fs-2 sign-up-header font-weight-bold m-2">Login</h2>
<div v-if="err != null" class="d-flex justify-content-center align-items-center text-danger border-radius-5 text-bold bg-danger text-white p-2">
{{ err }}
<span @click="hiderr" class="fs-2 cancel">&times;</span>
</div>
<form class="" @submit="login">
<div class="form-group">
<label for="email" class="form-label">Your Email</label>
<input required v-model="email" placeholder="patrick@example.com" class="form-control" type="email">
</div>
<div class="form-group">
<label for="password" class="form-label">Your Password</label>
<input required v-model="password" class="form-control" placeholder="your password" type="password">
</div>
<button>Log In</button>
<p class="signup-info-tag">Don't have an accoun't yet <RouterLink class="signup" to="/signup">Sign up</RouterLink></p>
</form>
</div>
</main>

<Footer class="footer-fixed-bottom" />
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
.signup-info-tag{
    color: gray;
    font-weight: 400;
    font-size: 15px;
}

h2{
    font-weight: bold;
    margin-bottom: 25px;
}
form{
    margin-top: 20px;
}
.signup{
    color: black;
    font-weight: bold;
}
.border-radius-5{
    border-radius: 5px;
}
.cancel{
    margin-left:auto;
    font-size: 45px;
    font-weight: bold;
    cursor: pointer;
}
.footer-fixed-bottom{
position: static; 
bottom: 0px;
}
main{
background-image: none;
background-color: none;
position: none;
width: 100%;
height: 100%;
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
    width:50%;
    margin:0px auto;
    margin-top:10vh;
    background-color: none;
}
.signup-info-tag{
    color: gray;
    font-weight: 400;
    font-size: 15px;
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
.signup{
    color: black;
    font-weight: bold;
}
.border-radius-5{
    border-radius: 5px;
}
.cancel{
    margin-left:auto;
    font-size: 45px;
    font-weight: bold;
    cursor: pointer;
}
.footer-fixed-bottom{
position: fixed; 
bottom: 0px;
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
    margin-top:10vh;
    background-color: none;
}
.signup-info-tag{
    color: gray;
    font-weight: 400;
    font-size: 15px;
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
.signup{
    color: black;
    font-weight: bold;
}
.border-radius-5{
    border-radius: 5px;
}
.cancel{
    margin-left:auto;
    font-size: 45px;
    font-weight: bold;
    cursor: pointer;
}
.footer-fixed-bottom{
position: fixed; 
bottom: 0px;
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
}

</style>