<script setup>
import { RouterLink } from 'vue-router';
import axiosClient from "../axios.js";
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';
import {ref} from "vue";
import { watch } from 'vue';
import { reactive } from 'vue';
let links;
const router=useRouter();
const route=useRoute();
const store=useStore();
let user_mail;
let current_route;
onMounted(()=>{
    current_route=route;
});
if(store.state.user.token != null){
    links=[
    {
    linkname:store.state.user.data.name,
    location:"login"
    },
    {
     linkname:"Home",
     location:"home",
     property:"link",

     function:function gotoHome(){
        if(current_route.path === "/home"){
            location.reload();
        }else{
            router.push({
            name:'Home'
        })
        }
       
     },
     itemicon:"fa-solid fa-house"

    },
    {
     linkname:"Match",
     location:"match",
     property:"link",
     function:function gotoMatch(){
        router.push({
            name:'Match'
        })
     },
     itemicon:"fa-solid fa-user-group"
    },
    {
     linkname:"Profile",
     location:"profile",
     property:"link",
     function:function gotoProfile(){
        router.push({
            name:'Profile'
        })
     },
     itemicon:"fa-solid fa-user"
    },
    {
     linkname:"Messages",
     location:"#",
     property:"link",
     function:function gotoMessages(){
        alert('Not created yet');
     },
     itemicon:"fa-solid fa-envelope"
    },
    {
     linkname:"Channels",
     location:"channel",
     property:"link",
     itemicon:"fa-solid fa-tv"
    },
    {
     linkname:"Notifications",
     location:"notify",
     property:"link",
     itemicon:"fa-solid fa-bell",
     badge:6
    },
    {
        linkname:"Sign Out",
        location:"#",
        property:"link",
        itemicon:"fa-solid fa-right-from-bracket",
        function:function logout(){
                var ask_if_user_wants_to_logout=confirm("Do you want to logout");
                if(ask_if_user_wants_to_logout){
                    store.commit('logout');
                    router.push({
                    name:'welcome'
                    });
                }else{
                    var get_current_route=route.params.name;
                    router.push({
                    name:get_current_route
                    });
                }
                
    }

    },
    
   
]
}else{
    links=[
    {
     linkname:"Home",
     location:"home",
     property:"link"
    },
    {
    linkname:"Features",
    location:"features",
    property:"link"
    },
    {
    linkname:"Contact",
    location:"contact",
    property:"link"
    },
    {
    linkname:"Log in",
    location:"login",
    property:"link magenta-rounded-bold"
    },
    {
    linkname:"Signup Now",
    location:"signup",
    property:"link magenta-rounded-bold"
    
    }
]
}
let sideBar=ref("");
let checklink=reactive({
    status:false,
    count:0
});

function displayLinks(){
checklink.status=true;
checklink.count++;
if(checklink.count > 1){
    checklink.count=0;
}
} 
watch(checklink, ()=>{
let side_container=document.getElementById("sidecontainer");
side_container.style.display="flex";

});
watch(checklink, ()=>{
if(checklink.count == 0){
    let side_container=document.getElementById("sidecontainer");
   side_container.style.display="none";  
}


});
let notify_count=reactive({
    count:""
});
onMounted(()=>{
    user_mail=sessionStorage.getItem('USER_MAIL');
let formData=new FormData();
formData.append("owner",user_mail);
axiosClient.post("/findNotifyCount",formData).then(response=>{
notify_count.count=response.data.reply;
}).catch(e=>{
    console.log('error');
});
});
</script>
<template>
<nav class="nav navbar navbar-fixed-top">
    <div class="container-fluid p-2">
    <RouterLink v-if="store.state.user.token==null" class="navbar-brand p-2 fs-2" to="/">NearbyNess</RouterLink>
    <ul class="d-none-mobile justify-content-flex-end list-unstyled p-2 fs-5">
        <RouterLink   :to="link.location" @click="link.function"   :class="link.property" v-for="link in links"><small v-if="link.linkname==='Notifications'"  class='fs-6'  style='color:red; border-radius: 5px; background-color: whitesmoke; font-weight:bold;   '>{{notify_count.count }}</small><i :class="link.itemicon"></i>{{ link.linkname }}
         
        </RouterLink>
      
        
      
    </ul>
    <span @click="displayLinks" class="icon-bar">&#8801;</span>
    </div>
    <div id="sidecontainer" class="container-fluid  p-4 side-container">
        <RouterLink :to="link.location" @click="link.function"   class="side-link" v-for="link in links">{{ link.linkname }}
         
        </RouterLink>
    </div>
</nav>

</template>
<style scoped>
@media screen and (min-width:320px) {
    .link{
    color:magenta;
    display: none;
    margin-right:20px;
}
.navbar-brand{
    color:rgb(0,0,0);
    font-family: tahoma;
    font-weight: bold;
}
.icon-bar{
    margin-left: auto;
    font-size: 45px;
    display: inline;
    cursor: pointer;
    color: magenta;
    font-weight: bold;
    
}
.side-container{
    display: none;
    position: fixed;
    left: 0px;
    height: 100%;
    bottom: 0px;
    background-color:rgb(210, 35, 210);
    color:black;
    width:200px;
    transition: width 5s;
    z-index: 1;
    flex-direction: column;
    
    
}
.side-container:hover{
    width: 200px;
    border-radius: 2px;
    cursor: pointer;
    color:rgb(249, 255, 83);
}

.side-link{
    font-weight: bold;
}
.d-none-mobile{
    display: none;
}
}
@media screen and (min-width:620px) {
.link{
    color:rgb(0, 0, 0);
    display: block;
    margin-right:20px;
    font-weight: bold;
}   
.navbar-brand{
    color:rgb(0,0,0);
    font-family: tahoma;
    font-weight: bold;
}
.icon-bar{
    display: none;
    
}
.side-container{
    display: none;
}
.d-none-mobile{
    display: flex;
    justify-content: flex-end;
    margin-left: auto;
}
}
@media screen and (min-width:1224px) {
.link{
    color:rgb(0, 0, 0);
    display: block;
    margin-right:20px;
    font-weight: bold;
}
.navbar-brand{
    color:rgb(255, 0, 119);
    font-family: tahoma;
    font-weight: bold;
}
.icon-bar{
    display: none;
}
.side-container{
    display: none;
}
.magenta-rounded-bold{
    background-color: #ff0084;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    font-weight: bolder;
    padding-top: 5px;
    padding-bottom: 10px;
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 5px;
    color: white;
}
.d-none-mobile{
    display: flex;
    justify-content: flex-end;
    margin-left: auto;
}
}
</style>
