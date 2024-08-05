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
import Status from "../views/Status.vue";
import Notifications from "../views/Notifications.vue";
import ShowMessage from "../views/ShowMessage.vue";
import Chat from "../views/Chat.vue";
import NotFound from "../views/NotFound.vue";
import Bookmark from "../views/Bookmarks.vue";
import Find from "../views/Find.vue";
import CreateLiveVideo from "../views/CreateLiveVideo.vue";
import WatchLiveStream from "../views/WatchLiveStream.vue";
import CommentDiscuss from "../views/CommentDiscuss.vue";
import ChannelPostComponent from "../component/ChannelPostComponent.vue";
import SharedPostComponent from "../component/SharedPostComponent.vue";
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
            {
                name:"ChannelPosts",
                path:"/home/:cat",
                component:ChannelPostComponent
            },
            {
                name:"SharedPosts",
                path:"/home/:cat",
                component:SharedPostComponent
            },
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
        path:"/notify",
        name:"Notifications",
        meta:{requiresAuth:true},
        component:Notifications
    },
    {
        path:"/find",
        name:"Find",
        meta:{requiresAuth:true},
        component:Find
    },
    {
        path:"/messages",
        name:"Messages",
        meta:{requiresAuth:true},
        component:ShowMessage
    },
    {
        path:"/live",
        name:"Live",
        meta:{requiresAuth:true},
        component:CreateLiveVideo,
    },
    {
        path: '/live/:streamId',
        name: 'WatchLiveStream',
        component:WatchLiveStream
    },
     {
        path:"/user/:info",
        name:"User",
        component:User,
        
    },
    {
        path:"/channel",
        name:"Channel",
        component:Channel,
        children:[
            {
                path:"/channel/:uid",
                component:Channel
            }
            
        ]
       
    },
    {
        path:"/status/:postid",
        name:"Status",
        component:Status,
    },
    {
        path:"/comment/:comment_id",
        component:CommentDiscuss
        
    },
    {
        path:"/chat/:uid",
        name:"Chat",
        meta:{requiresAuth:true},
        component:Chat,
       
    },
    {
        path:"/create_channel",
        name:"channel_create",
        meta:{requiresAuth:true},
        component:LaunchChannel,
        
    },
    {
        path:"/bookmarks",
        name:"bookmark",
        meta:{requiresAuth:true},
        component:Bookmark,
        
    },
    {
        path:"/:pathMatch(.*)*",
        name:"NotFound",
        component:NotFound
    }
]
const router=createRouter({
history:createWebHistory(import.meta.env.BASE_URL),
routes
})
router.beforeEach((to,from,next) =>{
if(to.meta.requiresAuth && !store.state.user.token && !store.state.user.cookieToken){
    next({name: 'Sign Up'});
}
else if(store.state.user.token && (to.name === 'Sign Up' || to.name === 'welcome' || to.name ==='Login')){
    next({name:'Home'});
}else if(store.state.user.cookieToken && (to.name === 'Sign Up' || to.name === 'welcome' || to.name ==='Login')){
    next({name:'Home'});
}
else{
    next();
}
})

export default router
