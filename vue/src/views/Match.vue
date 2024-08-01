<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { useRouter } from "vue-router";
import {ref,reactive,onMounted,} from "vue";
import StoriesandPost from "../component/StoriesandPost.vue";
import axiosClient from "../axios";
const router=useRouter();
const user_mail=localStorage.getItem('USER_MAIL');
function gotoFind(){
router.push({
    name:"Find"
});
}
let hold_user_suggestion=reactive({
interest:"",
religion:"",
school:"",
});
onMounted(()=>{
document.title='Find People';
let formData=new FormData();
formData.append("email",user_mail);
axiosClient.post("/suggestUsers",formData).then(response=>{
   hold_user_suggestion.interest    =response.data.user_with_similar_interest;
   hold_user_suggestion.religion    =response.data.user_with_similar_religion;
   hold_user_suggestion.school      =response.data.user_with_similar_school;
}).catch(error=>{
    console.log(error);
});
});
function setUserMatch(email){
    document.getElementById(email).setAttribute("disabled",true);
    axiosClient.post("/choice",{
        user:user_mail,
        choice:email
    }).then(response=>{
        alert(response.data.success);
    }).catch(e=>{
       let error="You can't follow this user";
        alert(error);
    });
}
</script>
<template>
    <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
    <div  v-if="hold_user_suggestion.interest === ''" class="d-flex spinner justify-content-center align-items-center">
    </div>
    <div v-else class="container-fluid">
    <div style="margin-bottom:10px;"  class=" d-flex search-bar justify-content-center align-items-center">
    <input @click="gotoFind" style="border-radius:20px;" width="100%" type="text" placeholder="Search" />
    </div>
<h2 v-if="hold_user_suggestion.interest.length > 0" class="text-center font-bold fs-5">You might like</h2>
<div  class="story-and-post" >
    <div v-for="i in hold_user_suggestion.interest" class="d-flex p-2 religion-suggestions justify-content-center align-items-center">
        <div style="position:relative;" class="card p-4 card-default shadow-sm user-suggestion-card">
        <RouterLink :to="`/user/${i.email}`"><img v-if="i.profile_picture === '' || i.profile_picture===null" class="img-circle avatar"  src="../pictures/profile.png"/>
        <img v-else class="img-circle avatar"  :src='`http://localhost:8000/storage/${i.profile_picture}`' />
        <h6 class="fs-6">{{i.first_name + '\t' + i.last_name}}</h6></RouterLink>
        <button :id="i.email" @click="setUserMatch(i.email)" class="btn follow-suggestion-btn btn-sm btn-success font-bold">Follow</button>
        </div>
    </div>
    </div>
    <h2 v-if="hold_user_suggestion.religion.length > 0" class="text-center font-bold fs-5">People with similar religion</h2>
<div  class="story-and-post" >
    <div  v-for="i in hold_user_suggestion.religion" class="d-flex p-2 religion-suggestions justify-content-center align-items-center">
        <div style="position:relative;" class="card p-4 card-default shadow-sm user-suggestion-card">
        <RouterLink :to="`/user/${i.email}`"><img v-if="i.profile_picture === '' || i.profile_picture===null" class="img-circle avatar"  src="../pictures/profile.png"/>
        <img v-else class="img-circle avatar"  :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' />
        <h6 class="fs-6">{{i.first_name + '\t' + i.last_name}}</h6></RouterLink>
        <button :id="i.email" @click="setUserMatch(i.email)" class="btn follow-suggestion-btn btn-sm btn-success font-bold">Follow</button>
        </div>
    </div>
    </div>
    <h2 v-if="hold_user_suggestion.school.length > 0" class="text-center font-bold fs-5">You attended same school with them</h2>
<div  class="story-and-post" >
    <div v-for="i in hold_user_suggestion.school" class="d-flex p-2 religion-suggestions justify-content-center align-items-center">
        <div style="position:relative;" class="card p-4 card-default shadow-sm user-suggestion-card">
        <RouterLink :to="`/user/${i.email}`"><img v-if="i.profile_picture === '' || i.profile_picture===null" class="img-circle avatar"  src="../pictures/profile.png"/>
        <img v-else class="img-circle avatar"  :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' />
        <h6 class="fs-6">{{i.first_name + '\t' + i.last_name}}</h6></RouterLink>
        <button :id="i.email" @click="setUserMatch(i.email)" class="btn follow-suggestion-btn btn-sm btn-success font-bold">Follow</button>
        </div>
    </div>
    </div>
</div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .story-and-post{
    display: flex;
    flex-direction:row;
    flex-wrap:wrap;
    justify-content:space-between;
    cursor: pointer;
    width: 100%;
    
}
.user-match{
    flex: 1;
    height: 400px;

}
.user-match-img{
    width:200px;
    height:200px;
    object-fit: cover;
    object-position: center;
}
.user-match-info{
    flex: 1;
    height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    position: relative;
}
.next-cancel{
    position:absolute;
    margin-top:40px;
    top:20px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    width: 80px;
    border-radius:20px;
}
.match-error{
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin-top: 15vh;
    font-weight: bold;
}
.search-bar{
    margin-top:60px; 
    margin-bottom:20px;
}
.religion-suggestions{
    margin-top:0px;
    width:200px;
    flex:1;
}
.avatar{
    width:100px;
    height:100px;
    border-radius:50px;
    object-fit:cover;
}
.follow-suggestion-btn{
    bottom:0px;
    width:100%;
    border-radius:20px;
    margin-top:20px;
}
.spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid #f3f3f3; /* Light grey */
  border-top: 8px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}
@media screen and (min-width:620px) {
    .story-and-post{
    display: flex;
    flex-direction:row;
    flex-wrap:wrap;
    justify-content:space-between;
    cursor: pointer;
    width: 100%;
    margin:0px auto;
}
.search-bar{
    margin-top:200px; 
    margin-bottom:20px;
}
.follow-suggestion-btn{
    bottom:0px;
    width:100%;
    border-radius:20px;
    margin-top:20px;
}
.spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid #f3f3f3; /* Light grey */
  border-top: 8px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}
@media screen and (min-width:700px) {
    .story-and-post{
    display: flex;
    flex-direction:row;
    flex-wrap:wrap;
    cursor: pointer;
    width: 80%;
    
    
}
.search-bar{
    margin-top:100px; 
    margin-bottom:0px;
}
.avatar{
    width:100px;
    height:100px;
    border-radius:50px;
    object-fit:cover;
}
.user-suggestion-card{
   width:200px;
   display:flex;
   flex-direction:column;
   justify-content:center;
   align-items:center;
   margin:10px;
}
.follow-suggestion-btn{
    bottom:0px;
    width:100%;
    border-radius:20px;
    margin-top:20px;
}
.spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid #f3f3f3; /* Light grey */
  border-top: 8px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
}

</style>