<script setup>
import { RouterLink } from 'vue-router';
import {ref} from "vue";

const sidenavitems=[
    {
        itemname:"Home",
        itemicon:"fa-solid fa-house"
        
    },
    {
        itemname:"Profile",
        itemicon:"fa-solid fa-user"
        
    },
    {
        itemname:"Match",
        itemicon:"fa-solid fa-user-group"
    },
    {
        itemname:"Messages",
        itemicon:"fa-solid fa-envelope"
    },
    {
        itemname:"Channel",
        itemicon:"fa-solid fa-tv"
    },
    {
        itemname:"Bookmarks",
        itemicon:"fa-regular fa-newspaper"
    },


  
]
let  badge=ref(5);

</script>
<template>
<div class="side-nav-container">
<ul class="list-group"><li v-for="item in sidenavitems" class="fs-5 font-bold hsover:bg-sky-700 bg-transparent list-group-item list-unstyled"><RouterLink v-if="item.itemname==='Notifications'" active-class="magenta" :to="item.itemname"><i style="margin-right: 10px;" :class="item.itemicon"></i>{{item.itemname}}</RouterLink><RouterLink v-else active-class="magenta" :to="item.itemname"><i style="margin-right: 10px;" :class="item.itemicon"></i>{{item.itemname}}</RouterLink></li>
</ul>
</div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .side-nav-container{
        display: none;
        
    }
}
@media screen and (min-width:620px) {
    .side-nav-container{
    display: none;
    width:250px;
    height: 800px;
    border-radius:5px;
    padding-left: 10px;
    padding-top: 10px;
    padding-right: 10px;
    color: green;
    font-family: helvetica;
    float: left;
    background-color: rgb(249, 163, 249);
}
.list-group{
    background-color: none;
    border:none;
}
.list-group-item{
    background-color: none;
    border: none;
    margin-top: 20px;
}
}
@media screen and (min-width:1224px) {
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@700&display=swap');
    .side-nav-container{
    display: block;
    width:250px;
    height: 800px;
    border-radius:5px;
    padding-left: 10px;
    padding-top: 10px;
    padding-right: 10px;
    color: green;
    font-family: helvetica;
    position: fixed;
    left: 0px;
    z-index: 1;
    top: 0px;
    float: left;
    background-color: rgb(255, 255, 255);
}
.list-group{
    background-color: none;
    border:none;
}
.list-group-item{
    background-color: none;
    border: none;
    margin-top: 40px;
    font-family: 'Manrope', helvetica, arial, sans-serif;
}
.magenta{
    font-weight: bold;
    color: magenta;
}
}

</style>