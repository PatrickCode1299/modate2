<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

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
        linkname:"Sign Out",
        location:"#",
        function:function logout(){
                store.commit('logout');
                router.push({
                    name:'welcome'
                })
                }
    }
   
]
   
}else{
    links=[
    {
     linkname:"Home",
     location:"home"
    },
    {
    linkname:"Features",
    location:"features"
    },
    {
    linkname:"Contact",
    location:"contact"
    },
    {
    linkname:"Log in",
    location:"login"
    },
    {
    linkname:"Signup Now",
    location:"signup"
    }
]
}


</script>
<template>
<nav class="nav navbar navbar-fixed-top">
    <div class="container-fluid p-2">
    <RouterLink class="navbar-brand p-2 fs-2" to="/">Modate2</RouterLink>
    <ul class="d-flex justify-content-space-around list-unstyled p-2 fs-5">
        <RouterLink :to="link.location" @click="link.function" active-class="active"  class="link" v-for="link in links">{{ link.linkname }}
         
        </RouterLink>
    </ul>
    </div>
</nav>
</template>
<style scoped>
.active{

 
}

.link{
    color:magenta;
    margin-right:20px;
}
.navbar-brand{
    color:rgb(0,0,0);
    font-family: tahoma;
    font-weight: bold;
}
</style>