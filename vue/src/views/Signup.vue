<script setup>
import Header from "../component/Header.vue";
import {ref, watch} from "vue";
import store from "../store";
import { useRouter } from "vue-router";
const router=useRouter();
const email=ref('');
const password=ref('');
const user={
    email:ref(email.value),
    password:ref(password.value)
}
let err=ref();
function registerUser(e){
    user.email=email.value;
    user.password=password.value;
    e.preventDefault();
    store.dispatch('registerUser',user).
    then((res) => {
        router.push({
            name:'Home'
        }).catch(error=>{
        alert("Invalid Registration Details");
        
        
    })
    })
}
function hiderr(){
   err.value=null;
}
/** 
watch(email, () => {
    console.log(user.email.value);
}); */
</script>
<template>
<Header />
<main>
<div class="container signup-holder">
<h2 class="fs-2 font-weight-bold m-2">Signup Today We are Waiting for you.</h2>
<form class="" method="POST" @submit="registerUser">
<div v-if="err != null" class="d-flex justify-content-center align-items-center text-danger border-radius-5 text-bold bg-danger text-white p-2">
{{ err }}
<span @click="hiderr" class="fs-2 cancel">&times;</span>
</div>
<div class="form-group">
<label for="email" class="form-label">Your Email</label>
<input v-model="email" placeholder="patrick@example.com" class="form-control" type="email">
</div>
<div class="form-group">
<label for="password" class="form-label">Your Password</label>
<input v-model="password" class="form-control" placeholder="your password" type="password">
</div>
<button>Sign Up</button>
<p class="signup-info-tag m-2">By Signing up we ensure you have agree to our <RouterLink class="terms" to="/terms">Terms and Conditions</RouterLink></p>
</form>
</div>
</main>
</template>
<style scoped>
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
    margin-top:15vh;
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
</style>