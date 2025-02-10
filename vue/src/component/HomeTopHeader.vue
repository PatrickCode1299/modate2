<template>
    <nav style="height:auto;" class="nav navbar navbar-fixed-top">
      <div class="container-fluid" style='padding:0px;'>
        <RouterLink v-if="!store.state.user.token" class="navbar-brand p-2 fs-2" to="/">
          <img src="../landing/hexarex.png" style="height:30px; width:30px;">
        </RouterLink>
        <ul v-if="store.state.user.token" class="d-none-mobile list-unstyled fs-5">
          <RouterLink
            :to="link.location"
            @click="link.function ? link.function() : null"
            :class="[link.property, { 'magenta': link.location === $route.path }]"
            v-for="(link, index) in links"
            :key="index"
          >
            <small v-if="link.linkname === 'Messages'" class="fs-6" style="color:red; border-radius: 5px; background-color: whitesmoke; font-weight:bold;">{{ notify_msg_count.count > 10 ? '10+' : 
            notify_msg_count.count < 1 ? '': notify_msg_count.count }}</small>
            <small v-if="link.linkname === 'Notifications'" class="fs-6" style="color:red; border-radius: 5px; background-color: whitesmoke; font-weight:bold;">{{ notify_count.count > 10 ? '10+' : notify_count.count < 1 ? '' : notify_count.count }}</small>
            <i :class="link.itemicon"></i>
          </RouterLink>
        </ul>
        <ul v-else class="d-none-mobile-login list-unstyled p-2 fs-5">
          <RouterLink
            :to="link.location"
            @click="link.function ? link.function() : null"
            :class="[link.property]"
            v-for="(link, index) in links"
            :key="index"
          >
          {{link.linkname }}
          </RouterLink>
        </ul>
        <span style="position:absolute; top:0px; right:0px;" @click="displayLinks" v-if="!store.state.user.token" class="icon-bar">&#8801;</span>
      </div>
      <ul v-if="store.state.user.token"  class="mobile-nav list-unstyled">
        <RouterLink
          :to="link.location"
          @click="link.function ? link.function() : null"
          class="mobile-link"
          active-class="magenta"
          v-for="(link, index) in links"
          :key="index"
        >
          <i :class="link.itemicon"></i>
        </RouterLink>
    </ul>
      <div id="sidecontainer" class="container-fluid shadow-lg p-4 side-container">
        <RouterLink
          :to="link.location"
          @click="link.function ? link.function() : null"
          class="side-link"
          v-for="(link, index) in links"
          :key="index"
        >
          <i :class="link.itemicon"></i>
          {{ link.linkname }}
        </RouterLink>
      </div>
    </nav>
  </template>
  
  <script setup>
  import { RouterLink } from 'vue-router';
  import axiosClient from "../axios.js";
  import { useStore } from 'vuex';
  import { useRoute } from 'vue-router';
  import { useRouter } from 'vue-router';
  import { onMounted, ref, reactive, watch } from 'vue';
  
  const router = useRouter();
  const route = useRoute();
  const store = useStore();
  let user_mail;
  
  let current_route;
  
  onMounted(() => {
    current_route = route;
  });
  
  let links = ref([]);
  
  if (store.state.user.token !== null) {
    links.value = [
      {
        linkname: store.state.user.data.name,
        location: "login"
      },
      {
        linkname: "Home",
        location: "/home",
        property: "link",
        function: function gotoHome() {
          if (current_route.path === "/home") {
            location.reload();
          } else {
            router.push({ name: 'Home' });
          }
        },
        itemicon: "fa-light fa-play-pause"
       
      },
      {
        linkname: "Stories",
        location: "/home/stories",
        property: "link",
        function: function gotoStory() {
          router.push({ name: 'Stories' });
        },
        itemicon: "fa-light fa-message"
      },
      {
        linkname: "Conversation",
        location: "/home/sharedposts",
        property: "link",
        itemicon: "fa-light fa-messages"
      },
      {
        linkname: "ChannelPosts",
        location: "/home/channelposts",
        property: "link",
        itemicon: "fa-light fa-tv"
      },
      {
        linkname: "Status",
        location: "/home/status",
        property: "link",
        itemicon: "fa-light fa-circle-notch"
      },
    
    
    ];
  } else {
    links.value = [
      {
        linkname: "Home",
        location: "/home",
        property: "link"
      },
      {
        linkname: "Features",
        location: "/features",
        property: "link"
      },
      {
        linkname: "Contact",
        location: "/contact",
        property: "link"
      },
      {
        linkname: "Log in",
        location: "/login",
        property: "link magenta-rounded-bold"
      },
      {
        linkname: "Join Now",
        location: "/signup",
        property: "link purple-rounded-bold"
      }
    ];
  }
  
  let checklink=reactive({
    status:false,
    count:0
});

function displayLinks(){
checklink.status=true;
checklink.count++;
if(checklink.count > 1){
    checklink.count=0;
}
} 
watch(checklink, ()=>{
let side_container=document.getElementById("sidecontainer");
side_container.style.display="flex";

});
watch(checklink, ()=>{
if(checklink.count == 0){
    let side_container=document.getElementById("sidecontainer");
   side_container.style.display="none";  
}


});
  
  let notify_count = reactive({
    count: ""
  });
  
  let notify_msg_count = reactive({
    count: ""
  });
  
  onMounted(() => {
    user_mail = localStorage.getItem('USER_MAIL');
    let formData = new FormData();
    formData.append("owner", user_mail);
  
    axiosClient.post("/findNotifyCount", formData).then(response => {
      notify_count.count = response.data.reply;
    }).catch(e => {
      console.log('error');
    });
  
    axiosClient.post("/findMsgCount", formData).then(response => {
      notify_msg_count.count = response.data.reply;
    }).catch(e => {
      console.log('error');
    });
  });
  </script>
  
  <style scoped>
  @media screen and (min-width: 320px) {
    .mobile-nav {
      display:flex;
      justify-content:center;
      align-items:center;
      z-index: 1;
      width: 100%;
      padding-top:0px;
    }
  
    .mobile-nav > .mobile-link {
      margin-right: 30px;
      font-size: 24px;
      font-weight: bold;
      padding-top:0px;
    }
  
  
    .link {
      color: magenta;
      display: none;
      
    }
  
    .navbar-brand {
      color: rgb(0, 0, 0);
      font-family: tahoma;
      font-weight: bold;
    }
  
    .icon-bar {
      margin-left: auto;
      font-size: 45px;
      display: inline;
      cursor: pointer;
      color: magenta;
      font-weight: bold;
      margin-right:20px;
      margin-top:0px;
      position:absolute;
      top:0px;
    }
  
    .side-container {
      display: none;
      position: fixed;
      left: 0px;
      height: 100%;
      bottom: 0px;
      background-color: rgb(255, 255, 255);
      color: black;
      width: 200px;
      transition: width 5s;
      z-index: 1;
      flex-direction: column;
      justify-content: flex-start;
      align-items: flex-start;
      margin-top: 0px;
    }
  
    .side-container > .side-link {
      flex-basis: 5%;
      flex-grow: 1;
      flex-shrink: -1;
    }
  
    .side-link {
      font-weight: bold;
    }
  
    .d-none-mobile {
      display: flex;
    }
  }
  
  @media screen and (min-width: 620px) {
    .mobile-nav {
      display: none;
    }
  
    .link {
      color: rgb(0, 0, 0);
      display: block;
      margin-right: 20px;
      font-weight: bold;
    }
  
    .navbar-brand {
      color: rgb(0, 0, 0);
      font-family: tahoma;
      font-weight: bold;
    }
  
    .icon-bar {
      display: none;
    }
  
    .side-container {
      display: none;
    }
  
    .d-none-mobile {
      display: flex;
      flex-direction: row;
      margin: 0px auto;
    }
  
    .d-none-mobile > a {
      margin-left: 20px;
      font-size: 25px;
    }
  
    .d-none-mobile-login {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      margin-left: auto;
    }
  }
  
  @media screen and (min-width: 1224px) {
    .mobile-nav {
      display: none;
    }
  
    .link {
      color: rgb(0, 0, 0);
      display: block;
      margin-right: 20px;
      font-weight: bold;
    }
  
    .navbar-brand {
      color: rgb(255, 0, 119);
      font-family: tahoma;
      font-weight: bold;
    }
  
    .icon-bar {
      display: none;
    }
  
    .side-container {
      display: none;
    }
  
    .magenta-rounded-bold {
      background-color: #ff0084;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      font-weight: bolder;
      padding-top: 5px;
      padding-bottom: 10px;
      padding-left: 20px;
      padding-right: 20px;
      padding-bottom: 5px;
      color: white;
    }
    .purple-rounded-bold{
      background-color: #8a0478;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      font-weight: bolder;
      padding-top: 5px;
      padding-bottom: 10px;
      padding-left: 20px;
      padding-right: 20px;
      padding-bottom: 5px;
      color: white;
    }
    .d-none-mobile {
      display: flex;
      flex-direction: row;
      margin: 0px auto;
    }
  
    .d-none-mobile > a {
      margin-left: 20px;
      font-size: 25px;
    }
  
    .d-none-mobile-login {
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      margin-left: auto;
    }
  
    .magenta {
      font-weight: bold;
      border-bottom: 2px solid magenta;
    }
  }
  </style>