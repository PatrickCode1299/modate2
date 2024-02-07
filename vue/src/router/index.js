import { createRouter,  createWebHistory } from "vue-router";
import Welcome from "../views/Welcome.vue";
import Header from "../component/Header.vue";
import Home from "../views/Home.vue";
import Signup from "../views/Signup.vue";
import Login from "../views/Login.vue";
import Terms from "../views/Terms.vue";
import Profile from "../views/Profile.vue";
import Payment from "../views/Payment.vue";
import Match from "../views/Match.vue"
import Edit from "../views/Edit.vue";
import ProfileHeader from "../views/ProfileHeader.vue";
import CreateStories from "../views/CreateStories.vue";
import User from "../views/User.vue";
import Channel from "../views/Channels.vue";
import LaunchChannel from "../views/LaunchChannel.vue";
import store from '../store/index.js';

const routes=[
    
    {
    path:"/",
    redirects:"welcome",
    name:"welcome",
    component:Welcome
    },
    {
        path:"/home",
        name:"Home",
        component:Home,
        meta:{requiresAuth:true},
        children:[
            {   path:"/home",
                name:"Home",
                component:Header
            }
            
        ]
    },
    {
        path:"/signup",
        name:"Sign Up",
        component:Signup
    },
    {
        path:"/login",
        name:"Login",
        component:Login
    },
    {
        path:"/terms",
        name:"Terms",
        component:Terms
    },
    {
        path:"/profile",
        name:"Profile",
        component:Profile,
        meta:{requiresAuth:true},
    },
    {
        path:"/match",
        name:"Match",
        meta:{requiresAuth:true},
        component:Match
    },
    {
        path:"/payment",
        name:"Payment",
        meta:{requiresAuth:true},
        component:Payment
    },
    {
        path:"/edit",
        name:"Edit",
        meta:{requiresAuth:true},
        component:Edit
    },
    {
        path:"/editheader",
        name:"EditHeader",
        meta:{requiresAuth:true},
        component:ProfileHeader
    },
    {
        path:"/create_story",
        name:"create_story",
        meta:{requiresAuth:true},
        component:CreateStories
    },
    {
        path:"/user/:info",
        name:"User",
        component:User
    },
    {
        path:"/channel",
        name:"Channel",
        component:Channel,
        children:{
            path:"/channel/:uid",
            name:"Channel",
        }
    },
    {
        path:"/create_channel",
        name:"channel_create",
        component:LaunchChannel,
        
    },
]
const router=createRouter({
history:createWebHistory(import.meta.env.BASE_URL),
routes
})
router.beforeEach((to,from,next) =>{
if(to.meta.requiresAuth && !store.state.user.token){
    next({name: 'Sign Up'});
}else if(store.state.user.token && (to.name === 'Sign Up' || to.name === 'welcome' || to.name ==='Login')){
    next({name:'Home'});
}
else{
    next();
}
})

export default router
