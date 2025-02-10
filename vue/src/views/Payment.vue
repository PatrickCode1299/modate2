<script setup>
import axios from "axios";
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import StoriesandPost from "../component/StoriesandPost.vue";
import store from "../store";
import axiosClient from "../axios.js";
import { ref } from "vue";
import { useRouter } from "vue-router";
const user_mail=localStorage.getItem('USER_MAIL');

function toSupport(){
    alert('User is trying to contact support');
}
let user_email;
let link=ref('');
const router =useRouter();
function gotoMerchant(e){
    e.preventDefault();
    user_email = user_mail;
    axiosClient.post("/pay",{email:user_email}).then(response =>{
        if(response.data.url === undefined) {
            console.log(response.data.url);
          alert("Sorry please connect to an ISP network and try again");
        }else{
        let url_data=JSON.stringify(response.data.url);
        let new_url=JSON.parse(url_data);
       // window.location.href=response.data.url;
        window.location.href=new_url.url;
        }
    }).
    catch(err=>{
        console.error('Error',err);
        alert("Sorry please connect to an ISP network and try again");
    })
    
}
</script>
<template>
<main>
    <Header style="background-color: white;" class="shadow-sm" />
    <SideNav style="display: none;" />
    <div  class="container payment-container d-flex justify-content-center align-items-center">
        <img style="width:50%; margin-top:50px; object-fit:contain; height:inherit; margin-bottom:0px;" src="../pictures/payment.jpg" />
        <h4 class="fs-4 p-2">In other to use Channels  you are required to make a one time subscription fee of 500 Naira </h4>
        <h4 class="fs-4 p-2">Via bank transfer to the account number on the merchant site or using your Debit ATM card.</h4>
        <p class="d-flex justify-content-center align-items-center">Our payment is secured by Paystack and for transparency we do not store your bank details, however if you would like to contact our support channel for clear explanations you can reach out via: <span @click="toSupport" class="pointer text-success">support</span></p>
        <form @submit="gotoMerchant">
        <input type="hidden" name="email"  :value="user_mail">
        <input type="hidden" name="currency"  value="NGN">
        <button  class="btn  m-3 btn-success btn-lg btn-block">To Payment</button>
        </form>
    </div>
</main>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .payment-container{
   flex-direction: column;
   width:100%; 
   height:500px;
}
.pointer{
    cursor: pointer;
    font-weight: bold;
}

}
@media screen and (min-width:620px) {
    .payment-container{
   flex-direction: column;
   width:80%; 
   height:500px;
}
.pointer{
    cursor: pointer;
    font-weight: bold;
}
}
@media screen and (min-width:1224px) {
    .payment-container{
   flex-direction: column;
   width:50%; 
   height:500px;
}
.pointer{
    cursor: pointer;
    font-weight: bold;
}
}

</style>