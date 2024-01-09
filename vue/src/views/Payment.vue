<script setup>
import axios from "axios";
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import StoriesandPost from "../component/StoriesandPost.vue";
import store from "../store";
import axiosClient from "../axios.js";
import { useRouter } from "vue-router";
const user_mail=store.state.user.data;

function toSupport(){
    alert('User is trying to contact support');
}
let user_email;
function gotoMerchant(e){
    e.preventDefault();
    user_email = user_mail;
    axiosClient.post("/pay",{email:user_email}).then(response =>{
       window.location.href=response.data.url
    }).
    catch(err=>{
        console.error('Error',err);
    })
    
}
</script>
<template>
<main>
    <Header />
    <div class="container payment-container d-flex justify-content-center align-items-center">
        <h4 class="fs-4 p-2">In other to use the features of this website you are required to make a one time subscription fee of 500 Naira </h4>
        <h4 class="fs-4 p-2">Via bank transfer to the account number on the merchant site or using your debit card.</h4>
        <p>Our payment is secured by Paystack and for transparency we do not store your bank details, however if you would like to contact our support channel for clear explanations you can reach out via: <span @click="toSupport" class="pointer text-success">support</span></p>
        <form @submit="gotoMerchant">
        <input type="hidden" name="email"  :value="user_mail">
        <input type="hidden" name="currency"  value="NGN">
        <button  class="btn  m-3 btn-success btn-lg btn-block">To Payment</button>
        </form>
    </div>
</main>
</template>
<style scoped>
.payment-container{
   flex-direction: column;
}
.pointer{
    cursor: pointer;
    font-weight: bold;
}
</style>