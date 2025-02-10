<script setup>
const footeritems=[
    {
        header:"Join Hexarex",
        firstinfo:new Date().toLocaleString(),
        url:"signup"
    },
    {
        header:"Company",
        firstinfo:"About",
        secondinfo:"Careers",
        thirdinfo:"Press",
        fourthinfo:"Ad choices"
        
    },
    {
        header:"Conditions",
        firstinfo:"Privacy",
        secondinfo:"Cookies - Manage Preferences",
        thirdinfo:"Terms",
        fourthinfo:"Community Guidelines"
    },
    {
        header:"Contact",
        firstinfo:"Support",
        secondinfo:"Security",
        thirdinfo:"Safety Tips"
    },
    {
        header:"Follow",
        firstinfo:"Blog",
        secondinfo:"Tech Blog",
        thirdinfo:"Facebook",
        fourthinfo:"Instagram"
    }
]
</script>
<template>
<div class="container-fluid app-footer">
<div class="d-flex footer-link ">
<ul class="list" v-for="f in footeritems">
<h2 class="f-5 list-unstyled bold-text"><RouterLink :to='`${f.url}`'>{{f.header }}</RouterLink></h2>
<li class="small-text">{{ f.firstinfo }}</li>
<li class="small-text">{{ f.secondinfo }}</li>
<li class="small-text">{{ f.thirdinfo }}</li>
<li class="small-text">{{ f.fourthinfo }}</li>
</ul>
</div>
</div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .app-footer{
    display: none;
    background-color: rgb(23, 23, 23);
    bottom: 0px;
    padding-top: 20px;
    padding-left: 10px;
    padding-right: 10px;
    padding-bottom: 20px;
    color: white;
    height: auto;
}
.list{
    margin-right: 50px;
}
.small-text{
    font-size: 12px;
    margin-top: 5px;
}
.bold-text{
    text-transform: uppercase;
    font-size:15px;
    font-weight: bold;
}
.footer-link{
flex-direction: column;
justify-content: flex-start;
align-items: flex-start;
}
}
@media screen and (min-width:620px) {
    .app-footer{
        display: none;
    background-color: rgb(23, 23, 23);
    bottom: 0px;
    padding-top: 20px;
    padding-left: 10px;
    padding-right: 10px;
    padding-bottom: 20px;
    color: white;
    height: auto;
}
.list{
    margin-right: 50px;
}
.small-text{
    font-size: 12px;
    margin-top: 5px;
}
.bold-text{
    text-transform: uppercase;
    font-size:15px;
    font-weight: bold;
}
.footer-link{
flex-direction: row;
justify-content:center;
align-items:center;
} 
}
@media screen and (min-width:1224px) {
    .app-footer{
    display: block;
    background-color: rgb(23, 23, 23);
    bottom: 0px;
    padding-top: 20px;
    padding-left: 10px;
    padding-right: 10px;
    padding-bottom: 20px;
    color: white;
    height: auto;
}
.list{
    margin-right: 50px;
}
.small-text{
    font-size: 12px;
    margin-top: 5px;
}
.bold-text{
    text-transform: uppercase;
    font-size:15px;
    font-weight: bold;
}
.footer-link{
flex-direction: row;
justify-content:center;
align-items:center;
} 
}
</style>