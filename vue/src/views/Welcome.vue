<script setup>
import { useRouter } from "vue-router";
import Footer from "../component/Footer.vue";
import Header from "../component/Header.vue";
import store from "../store";
import {ref} from "vue";
const email=ref('');
const password=ref('');
const screen_width=window.innerWidth;
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
const features=[
    {
        "title":"10k+ Members",
        "info":"Biggest platform to support you, whether you are looking for love or friendship"
    },
    {
        "title":"Smart AI",
        "info":"Best match using the best pairing algorithm"
    },
    {
        "title":"Perfect Match",
        "info":"Lot of people, have found success using our App."
    }

]
const router=useRouter();

const signup=() =>{
   return router.push('/signup');
}
document.title='Welcome | Get back to real life.';
</script>
<template>
<main>
    <Header style="font-family:Raleway;"  class="shadow-sm" />
    <div class="contenido" style="flex:2;">
<ul class="backgroundslider">
<li></li>
<li></li>
<li></li> 
<li></li>	
</ul>
    <div class="container details-holder" style="margin-bottom: 100px;">
    <div class="d-flex holder justify-content-center align-items-center">
        <div for="text" class="text">
            <h5>We created Hexarex</h5>
            <h3>to forget your</h3>
            <h2>Friendship Apps</h2>
            <p   class="fs-5">We designed a platform to get you out of toxic social media apps <br />  and get back to real life.</p>
<div class=" signup-holder">
<div v-if="err != null" class="d-flex justify-content-center align-items-center text-danger border-radius-5 text-bold bg-danger text-white p-2">
{{ err }}
<span @click="hiderr" class="fs-2 cancel">&times;</span>
</div>
<form style="margin-top:0px;"  @submit="login">
<div class="form-group">
<label for="email" class="form-label">Your Email</label>
<input required v-model="email" placeholder="patrick@example.com" class="form-control" type="email">
</div>
<div class="form-group">
<label for="password" class="form-label">Your Password</label>
<input required v-model="password" class="form-control" placeholder="your password" type="password">
</div>
<button style="margin-top:10px;">Log In</button>
<p class="signup-info-tag">Don't have an accoun't yet? <RouterLink class="signup" to="/signup">Sign up</RouterLink></p>
<p class="signup-info-tag m-2">Forgot Password? <RouterLink class="signup" to="/reset">Reset</RouterLink></p>

</form>
</div>

            
        </div>
        <!--
        <div for="pictures" class="picture p-4">
                <img class="black-lady model-pic" src="../landing/blackie.jpg" />
                <img class="white-lady model-pic" src="../landing/blondie.jpg" />
                <img class="asian-lady model-pic" src="../landing/asian.jpg" />
            
        </div>-->
    </div>
    
</div>
</div>
<Footer  />
</main>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .text > h5{
    color:rgba(22, 22, 22, 0.5);
    font-weight: 400;
    font-size: 30px;
    margin-top:50px;
    
}
.text > h3{
    color:rgb(0,0,0);
    font-weight: bold;
    font-size: 35px;
    
}
.text > h2{
    color:rgba(251, 28, 139, 0.5);
    font-weight: bolder;
    font-size: 40px;
  
    
}
.text > p{
    color:rgba(22, 22, 22, 0.5);
    margin-top: 0px;
}
.text > button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
.text > button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
.picture{
    display: none;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    
}
.holder{
    flex-direction: column;
    position: relative;
}
.picture > .black-lady{
    align-self: flex-start;
    flex: 1;
    display: none;
     transform: rotate(-15deg); 
}
.picture > .white-lady{
    align-self: flex-start;
    flex: 1;
     transform: rotate(0deg); 
}
.picture > .asian-lady{
    display: none;
    align-self: flex-end;
    flex: 1;
    transform: rotate(-15deg); 
}
.model-pic{
    border-radius:5px;
    height:300px;
    width:auto;
    margin-top: 5px;
}
.model-pic > img{
    width: 100px;
    height:100px;
    object-fit: cover;
    border-radius: 10px;
}
.details-holder{
    margin-top: 0px;
}
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
    margin-top:0px;
    background-color: none;
}
.signup-info-tag{
    color: gray;
    font-weight: 400;
    font-size: 12px;
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
    .text > h5{
    color:rgba(22, 22, 22, 0.5);
    font-weight: 400;
    font-size: 35px;
    
}
.text > h3{
    color:rgb(0,0,0);
    font-weight: bold;
    font-size: 50px;
    
}
.text > h2{
    color:rgba(251, 28, 139, 0.5);
    font-weight: bolder;
    font-size: 75px;
  
    
}
.text > p{
    color:rgba(22, 22, 22, 0.5);
    margin-top: 25px;
}
.text > button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
.text > button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
.picture{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    
}
.holder{
    position: relative;
    flex-direction: row;
}
.picture > .black-lady{
    display: block;
    align-self: flex-start;
    transform: rotate(-30deg);
}
.picture > .white-lady{
    align-self: flex-start;
    transform: rotate(45deg);
}
.picture > .asian-lady{
    display: block;
    align-self: flex-end;
    transform: rotate(-30deg);
}
.model-pic{
    border-radius:5px;
    height:200px;
    margin-top: 5px;
}
.model-pic > img{
    width: 100px;
    height:100px;
    object-fit: cover;
    border-radius: 10px;
}
.details-holder{
    margin-top: 20px;
}
.signup-info-tag{
    color: gray;
    font-weight: 400;
    font-size: 16px;
}
}
@media screen and (min-width:1224px) {
    .text > h5{
    color:rgba(22, 22, 22, 0.5);
    font-weight: 400;
    font-size: 35px;
    
}
.text > h3{
    color:rgb(0,0,0);
    font-weight: bold;
    font-size: 50px;
    
}
.text > h2{
    color:rgba(251, 28, 139, 0.5);
    font-weight: bolder;
    font-size: 75px;
  
    
}
.text > p{
    color:rgba(22, 22, 22, 0.5);
    margin-top: 25px;
}
.text > button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
.text > button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
.picture{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    
}
.holder{
    position: relative;
    flex-direction: row;
}
.picture > .black-lady{
    display: block;
    align-self: flex-start;
    transform: rotate(-30deg);
}
.picture > .white-lady{
    align-self: flex-start;
    transform: rotate(45deg);
}
.picture > .asian-lady{
    display: block;
    align-self: flex-end;
    transform: rotate(-30deg);
}
.model-pic{
    border-radius:5px;
    height:200px;
    margin-top: 5px;
}
.model-pic > img{
    width: 100px;
    height:100px;
    object-fit: cover;
    border-radius: 10px;
}
.details-holder{
    margin-top: 20px;
}
.signup-info-tag{
    color: gray;
    font-weight: 400;
    font-size: 16px;
}

}

</style>