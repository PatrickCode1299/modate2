<script setup>
import { useRouter } from "vue-router";
import Footer from "../component/Footer.vue";
import Header from "../component/Header.vue";
const features=[
    {
        "title":"10k+ Members",
        "info":"Biggest platform to support you, whether you are looking for love or friendship"
    },
    {
        "title":"Smart AI",
        "info":"Best match using the best pairing algorithm"
    },
    {
        "title":"Perfect Match",
        "info":"Lot of people, have found success using our App."
    }

]
const router=useRouter();
const signup=() =>{
   return router.push('/signup');
}
</script>
<template>
<main>
    <Header />
    <div class="contenido" style="flex:2;">
<ul class="backgroundslider">
<li></li>
<li></li>
<li></li> 
<li></li>	
</ul>
    <div class="container details-holder">
    <div class="d-flex holder justify-content-center align-items-center">
        <div for="text" class="p-4 text">
            <h5>We created Modate2</h5>
            <h3>to delete your</h3>
            <h2>Dating Apps</h2>
            <p class="fs-5">We designed a platform to get you out of comfort <br /> dating app zone and get back to real life.</p>
            <button @click="signup" class="btn btn-md btn-primary shadow-sm">I'm Ready</button>
            
        </div>
        <div for="pictures" class="picture p-2">
                <img class="black-lady model-pic" src="../landing/blackie.jpg" />
                <img class="white-lady model-pic" src="../landing/blondie.jpg" />
                <img class="asian-lady model-pic" src="../landing/asian.jpg" />
            
        </div>
    </div>
</div>
<div class="container m-2 d-flex justify-content-center align-items-center p-2">
                <div class="row">
                <div class="col-sm-4" v-for="f in features">
                <div height="200" class="card m-2 fs-6 card-default shadow-sm p-2">
                    <div class="card-header">
                        <h4>{{f.title}}</h4>
                    </div>
                    <p class="small p-2">{{ f.info }}</p>
                </div>
            </div>
        </div>
</div>
</div>
<Footer />
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
.picture > .black-lady{
    align-self: flex-start;
    flex: 1;
    /* transform: rotate(-15deg); **/
}
.picture > .white-lady{
    align-self: flex-start;
    flex: 1;
    /* transform: rotate(10deg); **/
}
.picture > .asian-lady{
    align-self: flex-end;
    flex: 1;
    /* transform: rotate(-15deg); **/
}
.model-pic{
    border-radius:5px;
    height:200px;
    width:auto;
    margin-top: 5px;
}
.model-pic > img{
    width: 100px;
    height:100px;
    object-fit: cover;
    border-radius: 10px;
}
.details-holder{
    margin-top: 20px;
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
.model-pic{
    border-radius:5px;
    height:200px;
    margin-top: 5px;
}
.model-pic > img{
    width: 100px;
    height:100px;
    object-fit: cover;
    border-radius: 10px;
}
.details-holder{
    margin-top: 20px;
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
.model-pic{
    border-radius:5px;
    height:200px;
    margin-top: 5px;
}
.model-pic > img{
    width: 100px;
    height:100px;
    object-fit: cover;
    border-radius: 10px;
}
.details-holder{
    margin-top: 20px;
}

}

</style>