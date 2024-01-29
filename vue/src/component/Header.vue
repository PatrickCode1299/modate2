<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import {ref} from "vue";
import { watch } from 'vue';
import { reactive } from 'vue';
let links;
const router=useRouter();
const store=useStore();
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
        router.push({
            name:'Home'
        })
     }
    },
    {
     linkname:"Match",
     location:"match",
     property:"link",
     function:function gotoMatch(){
        router.push({
            name:'Match'
        })
     }
    },
    {
     linkname:"Profile",
     location:"profile",
     property:"link",
     function:function gotoHome(){
        router.push({
            name:'Profile'
        })
     }
    },
    {
     linkname:"Messages",
     location:"#",
     property:"link",
     function:function gotoMessages(){
        alert('Not created yet');
     }
    },
    {
        linkname:"Sign Out",
        location:"#",
        property:"link",
        function:function logout(){
                store.commit('logout');
                router.push({
                    name:'welcome'
                })
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

</script>
<template>
<nav class="nav navbar navbar-fixed-top">
    <div class="container-fluid p-2">
    <RouterLink class="navbar-brand p-2 fs-2" to="/">Modate2</RouterLink>
    <ul class="d-none-mobile justify-content-space-around list-unstyled p-2 fs-5">
        <RouterLink  :to="link.location" @click="link.function"   :class="link.property" v-for="link in links">{{ link.linkname }}
         
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
    width:20px;
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
    color:magenta;
    display: block;
    margin-right:20px;
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
}
}
@media screen and (min-width:1224px) {
.link{
    color:rgb(255, 0, 255);
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
}
}
</style>