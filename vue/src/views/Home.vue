<script setup>
import axios from "axios";
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import StoriesandPost from "../component/StoriesandPost.vue";
import store from "../store";
import axiosClient from "../axios.js";
import { useRouter } from "vue-router";
//const user_mail=store.state.user.data;
let user_mail;
const router=useRouter();
function checkIfUserHasPaid(user_mail){
    user_mail=store.state.user.data;
   axiosClient.post('/checkIfUserHasPaid', {data:user_mail})
        .then(response => {
         const payment_status=response.data.user_status;
         if(payment_status=='true'){
            alert('User has Paid');
         }else{
           router.push({
            name:'Payment'
           })
         }
        }).catch(error => {
            console.error('Error',error);
        });
}
</script>
<template>
    {{ checkIfUserHasPaid() }}
    <Header />
    <SideNav />
    <div class="story-and-post">
    <StoriesandPost />
    </div>
</template>
<style scoped>
.story-and-post{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
}
</style>