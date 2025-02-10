<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import {ref} from "vue";
import { watch } from 'vue';
import { reactive,defineProps } from 'vue';
import axios from "axios";
const router=useRouter();
let user_mail=localStorage.getItem('USER_MAIL');
function LaunchCommunity(){
    router.push({
        name:"community_create"
    });
}
</script>
<template>
<div class="community-header-container">
<span class="fs-2 user-community"><RouterLink :to='`/community/${user_mail}`'><i class="fa-light fa-users"></i></RouterLink></span>
<form>
    <textarea class="search" placeholder="Find a Community"></textarea>
</form>
<span @click="LaunchCommunity" class="fs-1 add_community font-bold">&plus;</span>

</div>
</template>
<style scoped>
.community-header-container{
    display:flex;
    flex-direction:row;
    justify-content:space-evenly;
    width:100%;
    padding-left:10px;
    padding-right:10px;
    
}
form{
    flex:2;
    margin-right:auto;
}
.user-community{
    margin-right:10px;
}
.add_community{
    margin-left:10px;
}
.search{
    resize:none;
    border:1px solid grey;
    width:100%;
    border-radius:40px;
    height:50px;
}
</style>