<script setup>
import { RouterLink } from 'vue-router';

const sidenavitems=[
    {
        itemname:"Profile",
        itemicon:""
    },
    {
        itemname:"Match",
        itemicon:""
    },
    {
        itemname:"Messages",
        itemicon:""
    },
  
]
</script>
<template>
<div class="side-nav-container">
<ul class="list-group">
<li v-for="item in sidenavitems" class="list-group-item list-unstyled"><RouterLink :to="item.itemname">{{ item.itemname }}</RouterLink></li>
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

</style>