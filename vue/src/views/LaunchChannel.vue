<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { onUpdated,onMounted, reactive, ref, watch } from "vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
const user_mail=localStorage.getItem('USER_MAIL');
let channel_name=ref('');
onMounted(() =>{
   let user_mail=store.state.user.data;
   axiosClient.post('/checkIfUserHasPaid', {data:user_mail})
        .then(response => {
         const payment_status=response.data.user_status;
         if(payment_status=='true'){
            return;
         }else{
    
       router.push({
            name:'Payment'
           });  
       } 
        }).catch(error => {
            console.error('Error',error);
        });
});
let channel_bio=ref('');
let channel_category=ref('');
let message=reactive({
    error:"",
    success:""
});
const router=useRouter();
watch(channel_bio, ()=>{
if(channel_bio.value.length > 150){
    let create_channel_button=document.getElementById("create_channel");
    create_channel_button.setAttribute("disabled",true);
}else{
    let create_channel_button=document.getElementById("create_channel");
    create_channel_button.removeAttribute("disabled");
}
});

function createChannel(e){
    
    e.preventDefault();
    var formData=new FormData();
    formData.append('channel_owner',user_mail);
    formData.append('channel_name',channel_name.value.split(" ").join(""));
    formData.append('channel_bio',channel_bio.value);
    formData.append('channel_category',channel_category.value);
   axiosClient.post("/createChannel",formData).then(response=>{
        message.success=response.data.reply;
        router.push({
            name:"Channel"
        });
       
   }).catch(e=>{
    message.error=e.response.data.errors;
   });
   
}
function cancelError(){
    let error=document.getElementById("error-message");
         error.style.display="none";
}
function cancelMessage(){
    let success=document.getElementById("success-message");
         success.style.display="none";
}
onUpdated(()=>{
    if(message.error === ''){
        return;
    }else{
        let error=document.getElementById("error-message");
         error.style.display="block";
    }
});
onUpdated(()=>{
    if(message.success === ''){
        return;
    }else{
        let success=document.getElementById("success-message");
         success.style.display="block";
        
    }
});

</script>
<template>
    <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
    <div class="container p-4 shadow-sm edit-container">
        <div id="success-message" class="p-2 success alert alert-success font-bold">
            <p>{{ message.success }}</p>
            <div class="d-flex"><button  style="margin-left: auto; margin-top: 10px;"  class="btn btn-success btn-sm" @click="cancelMessage">Close</button></div>
        </div>
        <div id="error-message" class="p-4 text-center error alert alert-danger font-bold">
            <p>{{ message.error }}</p>
            <div class="d-flex"><button  style="margin-left: auto; margin-top: 10px;"  class="btn btn-success btn-sm" @click="cancelError">Close</button></div>
        </div>
        <form @submit="createChannel">
            <h2 class="text-danger fs-6 m-4 text-center">Please Fill All Details Correctly and Confirm.</h2>
            <div class="form-group m-2">
                <label class="green-text-bold" for="firstname">Channel Name</label>
                <input v-model="channel_name" type="text" placeholder="Your Channel Name example: Pew-Die-Pie" class="form-control rounded" required>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Last Name">Channel Category</label>
                <select class="form-control" v-model="channel_category" name="category">
                    <option value="Photography">Photography</option>
                    <option value="Artist">Artist (Drawing, Cartoonist)</option>
                    <option value="Video Editor">Video Editor</option>
                    <option value="Graphics Design">Graphics Design</option>
                    <option value="Software Engineer">Software Engineer</option>
                    <option value="Relationship">Relationship Coach</option>
                    <option value="Life Coach">Life Coach</option>
                    <option value="Therapist">Therapist</option>
                    <option value="Medical">Wellness and Lifestyle.</option>
                    <option value="Sport Betting">Sport Betting</option>
                    <option value="Content Creator">Content Creator</option>
                    <option value="Government Organization">Government Organization</option>
                </select>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Last Name">Channel Bio</label>
                <input style="height: 100px;" v-model="channel_bio" type="text" placeholder="Example: I dedicate this channel to showcase my religious beliefs and i hope it doesn't affect you." class="form-control rounded" required>
            </div>
          
          
           <div class="d-flex submit"><button id="create_channel" class="btn submit-btn btn-md btn-success">Create Channel</button></div> 
        </form>
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .edit-container{
    width: 100%;
    background-color: rgb(253, 253, 253);
    height:100vh;
    margin-top:50px;
    border-radius: 10px;
}
.green-text-bold{
    color: green;
    font-weight: bold;
    margin-bottom: 5px;
}
.submit{
    justify-content: flex-end;
}
.submit-btn{
    margin-left: auto;
    font-weight: bold;
}
.success{
    display: none;
}
.error{
    display: none;
}
}
@media screen and (min-width:620px) {
    .edit-container{
    width: 50%;
    margin:0px auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
    margin-top:50px;
}
.green-text-bold{
    color: green;
    font-weight: bold;
    margin-bottom: 5px;
}
.submit{
    justify-content: flex-end;
}
.submit-btn{
    margin-left: auto;
    font-weight: bold;
}
.success{
    display: none;
}
.error{
    display: none;
}
}
@media screen and (min-width:1224px) {
    .edit-container{
    width: 50%;
    margin:0px auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
    margin-top:50px;
}
.green-text-bold{
    color: green;
    font-weight: bold;
    margin-bottom: 5px;
}
.submit{
    justify-content: flex-end;
}
.submit-btn{
    margin-left: auto;
    font-weight: bold;
}
.success{
    display: none;
}
.error{
    display: none;
}
}

</style>