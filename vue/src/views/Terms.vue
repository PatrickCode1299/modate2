<script setup>
import Header from "../component/Header.vue";
import Footer from "../component/Footer.vue";
</script>
<template>
<main>
<Header />
<div class="container-fluid d-flex justify-content-center align-items-center">
<h2 class="fs-3 text-bold font-weight-bold">Terms of Service</h2>
<p class="m-4">The following page outlines the terms of use of this website Modate2. Before using any of the services, you are require to read and go through this terms thoroughly.<br />
Read through the next paragraph below to understand the terms of use.
</p>
<div class="grey-out-essential-site-info d-flex p-2">
<div class="p-2 essential">
<h2 class="fs-4">Acceptance of Terms</h2>
<p class="user-paragraph">By accessing and using each and every web pages on this website, you agree to not visit this website for malicious intent. On registering as a member of this website we assume you are interested in meeting new people and possibly a future partner.
You agree to not making use of this website as an avenue for illegal meetups, orgies, or engaging in trafficking in any form.
</p>
<h3 class="m-2 fs-5"><u>Our Essential Terms</u></h3>
<p>Read the following terms thoroughly and ensure you agree to them before completing your registration..</p>
<ul class="list-group">
<li class="list-unstyled m-2 red">I agree to paying a one time registration fee on Sign Up and becoming an active member of this website..</li>
<li class="list-unstyled m-2 red">I agree that the company can make use of my data for advertisement, which includes social media adverts or promotions.</li>
<li class="list-unstyled m-2 red">I agree that the company can give out my information to law enforcement agency to aid in any investigation if i am caught wanting of criminal behavior..</li>
<li class="list-unstyled m-2 red">I agree that the company can remove my account at any point in time if my mode of using this website is malicious to some users..</li>
</ul>
<small class="m-2">Please ensure you read the following terms thoroughly thanks..</small>
</div>
<div class="not-bold">
    <h2 class="fs-4">Basically</h2>
    <p>Having read the terms written in bold fonts, you agree to all the terms of this website and you have deemed yourself fit for registration..</p>
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
    background-color: rgb(237, 237, 237);
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
    background-color: rgb(237, 237, 237);
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
    background-color: rgb(237, 237, 237);
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
}
.font-weight-bold{
    font-weight: bold;
}
}

</style>