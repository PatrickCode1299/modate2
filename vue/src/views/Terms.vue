<script setup>
import Header from "../component/Header.vue";
import Footer from "../component/Footer.vue";
document.title="Terms";
</script>
<template>
<main>
<Header class="shadow-sm" />
<div class="container-fluid d-flex justify-content-center align-items-center">
<h2 class="fs-3 text-bold font-weight-bold">Terms of Service</h2>
<p class="m-4">The following page outlines the terms of use of this software Hexarex. Before using any of the services, you are require to read and go through this terms thoroughly.<br />
Read through the next paragraph below to understand the terms of use.
<div class="d-flex justify-content-center align-items-center">
<img class="" src="../assets/icon-foreground.jpg" width="200px" height="auto" />
</div>
</p>
<div class="grey-out-essential-site-info d-flex p-2">
<div class="p-2 essential">
<h2 class="fs-4">Acceptance of Terms</h2>
<p class="user-paragraph">By accessing and using each and every pages on this software, you agree to not make use of the software for malicious intent. On registering as a user of this software we assume you are interested in meeting new people and possibly a friend to hangout with.
You agree to not making use of this software as an avenue for illegal meetups, orgies, or engaging in trafficking in any form.
</p>
<p class="user-paragraph">Hexarex is a platform that gives you the freedom to express yourself, we set up the best means to make you meet real people, our platform is designed for everyone and we do not discriminate based on age, religion or gender. 
    However users need to understand using a social media app might be sometimes restricted to a certain age group, and we wouldn't allow you signup if you fall under the age of 16 years in your respective country.</p>
<p class="user-paragraph">Any user who choose to signup illegally through by passing the system age restriction do so at their own risk, and we are not liable for any matter that arises, always remember our team of engineers are working everyday to secure this software with the best means possible..
</p>
<h3 class="m-2 fs-5"><u>Our Business Terms</u></h3>
<p>Read the following terms thoroughly and ensure you agree to them before completing your registration..</p>
<ul class="list-group">
<li class="list-unstyled m-2 red">I agree that the company can remove my account at any point in time if my mode of using this software is malicious to some users..</li>
<li class="list-unstyled m-2 red">I agree to paying a one time fee when required as i become an active user of this software..</li>
<li class="list-unstyled m-2 red">I agree that the company can make use of my data for advertisement, which includes target social media adverts or promotions.</li>
<li class="list-unstyled m-2 red">I agree to any advert shown to me via the software..</li>
</ul>
<small class="m-2">Please ensure you read the following terms thoroughly thanks..</small>
</div>
</div>
</div>
</main>
<Footer   />
</template>
<style scoped>
@media screen and (min-width:320px) {
    .container-fluid{
    flex-direction: column;
}
.grey-out-essential-site-info{
    color: black;
    font-weight: bold;
    flex-direction: column;
    
}
.red{
    color: darkgreen;
}
.essential{
    flex: 4;
}
.not-bold{
    font-weight: 400;
    flex: 1;
    
}
.user-paragraph{
    line-height: 30px;
    font-weight:400;
    margin-top:20px;
}
.font-weight-bold{
    font-weight: bold;
}
}
@media screen and (min-width:620px) {
    .container-fluid{
    flex-direction: column;
}
.grey-out-essential-site-info{
    color: black;
    font-weight: bold;
    flex-wrap: wrap;
    
    
}
.red{
    color: darkgreen;
}
.essential{
    flex: 4;
}
.not-bold{
    font-weight: 400;
    flex: 1;
    
}
.user-paragraph{
    line-height: 30px;
    font-weight:400;
    margin-top:20px;
}
.font-weight-bold{
    font-weight: bold;
}
}
@media screen and (min-width:1224px) {
    .container-fluid{
    flex-direction: column;
}
.grey-out-essential-site-info{
    color: black;
    font-weight: bold;
    flex-wrap: wrap;
    flex-direction: row;
    
}
.red{
    color: darkgreen;
}
.essential{
    flex: 4;
}
.not-bold{
    font-weight: 400;
    flex: 1;
    
}
.user-paragraph{
    line-height: 30px;
    font-weight:400;
    margin-top:20px;
}
.font-weight-bold{
    font-weight: bold;
}
}

</style>