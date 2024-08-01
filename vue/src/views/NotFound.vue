<script setup>
import { useRouter } from "vue-router";
import Footer from "../component/Footer.vue";
import Header from "../component/Header.vue";
import { reactive, onMounted } from "vue";

const router=useRouter();
let user_mail=localStorage.getItem('USER_MAIL');
const randomPic=[
{
    img_url:"spidey.jfif",
    img_name:"Spider Man"
},
{
    img_url:"milk.jfif",
    img_name:"A Picture of Milk"
},
{
    img_url:"cat_new.jpg",
    img_name:"A Computer Geek"
},
{
    img_url:"404cat.jfif",
    img_name:"A Programming Cat"
},
];
let change_image=reactive({
    picture:"",
    title:""
});
onMounted(()=>{
    var random_seed=Math.floor(Math.random() * randomPic.length);
let img=randomPic[random_seed];
change_image.picture=img.img_url;
change_image.title=img.img_name;
//let random_image=document.getElementById("random_picture");
//random_image.setAttribute("src",change_image.picture);
});
document.title="Not Found";
</script>
<template>
<main id="main">
    <Header class="shadow-sm" />
<div class="container error_page_container">
<h1 class="fs-1 font-bold">Oops!! 404 Page Not Found, Nevermind</h1>
<img id="random_picture"  src='./pictures/milk.jfif' alt="Not Found Image"  />
<h2 class="fs-2 font-bold" id="">Here's a Picture of Milk..</h2>
</div>
<Footer v-if="user_mail===null" id="footer"/>
</main>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .text > h5{
    color:rgba(22, 22, 22, 0.5);
    font-weight: 400;
    font-size: 35px;
    
}
.text > h3{
    color:rgb(0,0,0);
    font-weight: bold;
    font-size: 50px;
    
}
.text > h2{
    color:rgba(251, 28, 139, 0.5);
    font-weight: bolder;
    font-size: 75px;
  
    
}
.text > p{
    color:rgba(22, 22, 22, 0.5);
    margin-top: 25px;
}
.text > button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
.text > button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
.picture{
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    
}
.holder{
    flex-direction: column;
    position: relative;
}
#footer{
    position:static; 
}
#random_picture{
    width:200px;
    height: 200px;
    border-radius:20px;
    object-fit: cover;
}
.error_page_container{
    display:flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    row-gap: 10px;
}

}
@media screen and (min-width:620px) {
    .text > h5{
    color:rgba(22, 22, 22, 0.5);
    font-weight: 400;
    font-size: 35px;
    
}
.text > h3{
    color:rgb(0,0,0);
    font-weight: bold;
    font-size: 50px;
    
}
.text > h2{
    color:rgba(251, 28, 139, 0.5);
    font-weight: bolder;
    font-size: 75px;
  
    
}
.text > p{
    color:rgba(22, 22, 22, 0.5);
    margin-top: 25px;
}
.text > button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
.text > button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
.picture{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    
}
.holder{
    position: relative;
    flex-direction: row;
}

#footer{
    position: fixed; 
    bottom: 0px;
}
#random_picture{
    width:200px;
    height: 200px;
    border-radius:20px;
    object-fit: cover;
}
.error_page_container{
    display:flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    row-gap: 10px;
}
}
@media screen and (min-width:1224px) {
    .text > h5{
    color:rgb(245, 255, 55);
    font-weight: 400;
    font-size: 35px;
    
}
.text > h3{
    color:rgb(255,255,255);
    font-weight: bold;
    font-size: 50px;
    
}
.text > h2{
    color:rgb(255, 0, 128);
    font-weight: bolder;
    font-size: 75px;
  
    
}
.text > p{
    color:rgb(255, 255, 255);
    margin-top: 25px;
}
.text > button{
    text-transform: uppercase;
    border-radius: 50px;
    width: 150px;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
    padding-right: 5px;
    margin-top: 30px;
    transition: width 2s;
    background-image:linear-gradient(to right, orange,rgb(150, 29, 142),magenta);
    border:magenta;
}
.text > button:hover{
    width:300px;
    padding-top:10px;
    padding-bottom: 10px;
    padding-left:5px;
    padding-right: 5px;
    font-weight: bold;
}
.picture{
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    
}
.holder{
    position: relative;
    flex-direction: row;
}
.picture > .black-lady{
    align-self: flex-start;
    transform: rotate(-30deg);
}
.picture > .white-lady{
    align-self: flex-start;
    transform: rotate(45deg);
}
.picture > .asian-lady{
    align-self: flex-end;
    transform: rotate(-30deg);
}
#footer{
    position: fixed; 
    bottom: 0px;
}
#random_picture{
    width:350px;
    height: 350px;
    border-radius:20px;
    object-fit: cover;
}
.error_page_container{
    display:flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    row-gap: 10px;
}
}

</style>