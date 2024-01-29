<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { ref } from "vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
const user_mail=sessionStorage.getItem('USER_MAIL');
const allStates=[
"Lagos","Abuja","Abia","Adamawa","Akwa Ibom","Anambra","Bauchi","Bayelsa","Benue",
"Borno","Cross River","Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa",
"Kaduna","Kano","Katstina","Kebbi","Kogi","Kwara","Lagos","Nasarawa","Niger","Ogun",
"Ondo","Osun","Oyo","Plateau","Rivers","Sokoto","Taraba","Yobe","Zamafara"

];
let state=ref('');
let first_name=ref('');
let last_name=ref('');
let user_gender=ref('');
let user_birthdate=ref('');
let phone=ref('');
let info;
const router=useRouter();
function updateProfile(e){
    console.log(user_mail);
    e.preventDefault();
   axiosClient.post("/updateProfile",{data:{
    email:user_mail,
    firstname:first_name.value,
    lastname:last_name.value,
    gender:user_gender.value,
    state:state.value,
    birthday:user_birthdate.value,
    phone:phone.value
   }}).then(response=>{
      info=false;
       sessionStorage.setItem('INCOMPLETE',info);
       alert(response.data.success);
        router.push({
            name:"Profile"
        });
       
   });
   
}


</script>
<template>
    <Header />
    <SideNav />
    <div class="container p-4 shadow-sm edit-container">
        <form @submit="updateProfile">
            <h2 class="text-danger fs-6 m-4 text-center">Please Fill All Details Correctly and Crosscheck Before Submitting This is your only chance...</h2>
            <div class="form-group m-2">
                <label class="green-text-bold" for="firstname">First Name</label>
                <input v-model="first_name" type="text" placeholder="Your First Name e.g Patrick" class="form-control rounded" required>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Last Name">Last Name</label>
                <input v-model="last_name" type="text" placeholder="Your Last Name e.g Emmanuel" class="form-control rounded" required>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Orientation">Gender Orientation</label>
                <select v-model="user_gender" class="form-control" name="orientation" required>
                    <option value="Male">Straight Male</option>
                    <option value="Female">Straight Female</option>
                    <option value="Gay">Gay</option>
                    <option value="Bisexual">Bisexual</option>
                    <option value="Lesbian">Lesbian</option>
                    <option value="Non-binary">Non Binary</option>
                </select>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Location">Your Residence</label>
                <select v-model="state" class="form-control" name="location" required>
                    <option v-for="state in allStates" :value=state>{{state}}</option>
                </select>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="birthday">Your Birthday</label>
                <input v-model="user_birthdate" type="date" class="form-control" name="birthday" required>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Phone Number">Phone Number</label>
                <input v-model="phone" type="text" placeholder="Phone Number e.g +2349031227654" class="form-control rounded" required>
            </div>
           <div class="d-flex submit"><button class="btn submit-btn btn-md btn-success">Complete Profile</button></div> 
        </form>
    </div>
</template>
<style scoped>
.edit-container{
    width: 50%;
    margin:0px auto;
    background-color: rgb(253, 253, 253);
    height: auto;
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
</style>