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
        choice:atob(email)
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
        <div style="position:relative;" class="card p-2 card-default shadow-sm user-suggestion-card">
        <RouterLink :to="`/user/${i.email}`"><img v-if="i.profile_picture === '' || i.profile_picture===null" class="img-circle avatar"  src="../pictures/profile.png"/>
        <img v-else class="img-circle avatar"  :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' />
        <h6>{{i.first_name + '\t' + i.last_name}}</h6></RouterLink>
        <button :id="i.email" @click="setUserMatch(i.email)" class="btn follow-suggestion-btn btn-sm btn-success font-bold">Follow</button>
        </div>
    </div>
    </div>
    <h2 v-if="hold_user_suggestion.religion.length > 0" class="text-center font-bold fs-5">People with similar religion</h2>
<div  class="story-and-post" >
    <div  v-for="i in hold_user_suggestion.religion" class="d-flex p-2 religion-suggestions justify-content-center align-items-center">
        <div style="position:relative;" class="card p-2 card-default shadow-sm user-suggestion-card">
        <RouterLink :to="`/user/${i.email}`"><img v-if="i.profile_picture === '' || i.profile_picture===null" class="img-circle avatar"  src="../pictures/profile.png"/>
        <img v-else class="img-circle avatar"  :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' />
        <h6>{{i.first_name + '\t' + i.last_name}}</h6></RouterLink>
        <button :id="i.email" @click="setUserMatch(i.email)" class="btn follow-suggestion-btn btn-sm btn-success font-bold">Follow</button>
        </div>
    </div>
    </div>
    <h2 v-if="hold_user_suggestion.school.length > 0" class="text-center font-bold fs-5">People with similar school</h2>
<div  class="story-and-post" >
    <div v-for="i in hold_user_suggestion.school" class="d-flex p-2 religion-suggestions justify-content-center align-items-center">
        <div style="position:relative;" class="card p-2 card-default shadow-sm user-suggestion-card">
        <RouterLink :to="`/user/${i.email}`"><img v-if="i.profile_picture === '' || i.profile_picture===null" class="img-circle avatar"  src="../pictures/profile.png"/>
        <img v-else class="img-circle avatar"  :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`' />
        <h6>{{i.first_name + '\t' + i.last_name}}</h6></RouterLink>
        <button :id="i.email" @click="setUserMatch(i.email)" class="btn follow-suggestion-btn btn-sm btn-success font-bold">Follow</button>
        </div>
    </div>
    </div>
</div>
</template>
<style scoped>
/* For mobile devices */
@media screen and (max-width: 599px) {
  .story-and-post {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    cursor: pointer;
  }
  .religion-suggestions {
  margin-top: 0px;
  display: grid;
  grid-template-columns: repeat(3, 1fr); /* 3 columns with equal width */
  grid-gap: 10px; 
}
  .user-suggestion-card {
    width: 100%; /* Take full width */
    margin:0; /* Add margin between items */
    display: flex;
    height:200px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size:12px;
    position:relative;
    padding-left:10px;
    padding-right:10px;
    text-align:center;
  }

  .avatar {
    width: 100px;
    height: 100px;
    border-radius: 50px;
    object-fit: cover;
  }

  .follow-suggestion-btn {
    bottom: 0px;
    width: 100%;
    border-radius: 20px;
    margin-top: 20px;
    position:absolute;
    bottom:0px;
  }

  .search-bar {
    margin-top: 80px;
    margin-bottom: 20px;
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
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
}

/* For tablets */
@media screen and (min-width: 600px) and (max-width: 899px) {
    .religion-suggestions {
  margin-top: 0px;
  display: grid;
  grid-template-columns: repeat(3, 1fr); /* 3 columns with equal width */
  grid-gap: 10px; 
}
  .story-and-post {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    cursor: pointer;
    width: 100%;
  }

  .user-suggestion-card {
    width: 100%; /* Take full width */
    margin: 0; /* Add margin between items */
    display: flex;
    height:200px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size:12px;
    position:relative;
    padding-left:10px;
    padding-right:10px;
  }


  .avatar {
    width: 100px;
    height: 100px;
    border-radius: 50px;
    object-fit: cover;
  }

  .follow-suggestion-btn {
    bottom: 0px;
    width: 100%;
    border-radius: 20px;
    margin-top: 20px;
    position:absolute;
  }

  .search-bar {
    margin-top: 80px;
    margin-bottom: 20px;
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
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
}

/* For desktops */
@media screen and (min-width: 900px) {
  .story-and-post {
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* Center the grid */
    width: 80%; /* Set a smaller width for desktop */
    margin: 0 auto;
  }
  .religion-suggestions {
  margin-top: 0px;
  display: grid;
  grid-template-columns: repeat(3, 1fr); /* 3 columns with equal width */
  grid-gap: 10px; 
}
  .user-suggestion-card {
    width: 100%; /* Take full width */
    margin: 10px 0; /* Add margin between items */
    display: flex;
    height:200px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size:12px;
    position:relative;
    padding-left:20px;
    padding-right:20px;
  }


  .avatar {
    width: 100px;
    height: 100px;
    border-radius: 50px;
    object-fit: cover;
  }

  .follow-suggestion-btn {
    bottom: 0px;
    width: 100%;
    border-radius: 20px;
    margin-top:20px;
    position:absolute;
  }

  .search-bar {
    margin-top: 100px;
    margin-bottom: 0px;
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
    0% {
      transform: rotate(0deg);
    }
    100% {
      transform: rotate(360deg);
    }
  }
}

</style>