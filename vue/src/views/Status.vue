<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import LikeCommentSharesCount from "../component/LikeCommentSharesCount.vue";
import VideoPlayerComponent from "../component/VideoPlayerComponent.vue";
import store from "../store";
import { ref,reactive, watch, onMounted, onBeforeUpdate, onUpdated } from "vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
import { useRoute } from "vue-router";
import moment from 'moment'
import { defineProps } from "vue";
const user_mail=localStorage.getItem('USER_MAIL');
const name_of_current_user=localStorage.getItem('FIRSTNAME');
const route=useRoute();
let loading_icon=ref('true');
let postid=route.params.postid;
let formData=new FormData();
formData.append("post_id",postid);
let all_post_info=reactive({
name:"",
avatar:"",
caption:"",
post_email:"",
post_date:"",
img_1:"",
img_2:"",
img_3:"",
img_4:"",
video:"",
sharer_name:"",
sharer_email:"",
sharer_quote:"",
sharer_avatar:"",
postid:"",
prev_id:"",
error:"",
comment_count:"",
shares_count:"",
likes_count:"",
bookmark_count:"",
isReply:""
});
onMounted(()=>{
axiosClient.post("/findStatus",formData).then(response=>{
if(response.data.reply==='error'){
all_post_info.error="This content has been removed by owner..";
}else{
all_post_info.name=response.data.reply.name;
all_post_info.avatar=response.data.reply.avatar;
all_post_info.caption=response.data.reply.caption;
all_post_info.post_date=response.data.reply.post_date;
all_post_info.post_email=response.data.reply.user_email;
all_post_info.img_1=response.data.reply.img_1;
all_post_info.img_2=response.data.reply.img_2;
all_post_info.img_3=response.data.reply.img_3;
all_post_info.img_4=response.data.reply.img_4;
all_post_info.video=response.data.reply.video;
all_post_info.sharer_name=response.data.reply.sharer_name;
all_post_info.sharer_email=response.data.reply.sharer_email;
all_post_info.sharer_quote=response.data.reply.sharer_quote;
all_post_info.sharer_avatar=response.data.reply.sharer_avatar;
all_post_info.postid=response.data.reply.prev_id;
all_post_info.isReply=response.data.reply.isReply;
response.data.no_of_comment.forEach(comment_count => {
   all_post_info.comment_count=comment_count.total_comments;
});
response.data.no_of_users_who_shared.forEach(shares_count => {
   all_post_info.shares_count=shares_count.total_shares;
});
response.data.no_of_users_who_liked.forEach(likes_count => {
   all_post_info.likes_count=likes_count.total_like;
});
response.data.no_of_users_who_bookmarked.forEach(bookmark_count => {
   all_post_info.bookmark_count=bookmark_count.total_bookmarks;
});
if(all_post_info.sharer_quote === '' || all_post_info.sharer_quote === undefined){
    let description=all_post_info.name+'\t on Hexarex'+all_post_info.caption;
    document.title=all_post_info.name+'\t on Hexarex'+all_post_info.caption;
    var metaDescription = document.querySelector('meta[name="og:description"]');
    metaDescription.setAttribute('content', description);
}else{
    let description=document.title=all_post_info.sharer_name+'\t on Hexarex'+all_post_info.sharer_quote;
    document.title=all_post_info.sharer_name+'\t on Hexarex'+all_post_info.sharer_quote;
    var metaDescription = document.querySelector('meta[name="og:description"]');
    metaDescription.setAttribute('content', description);
}
}
}).catch(e=>{
  console.log(e);
})

});
let comment=ref('');
let showComment=reactive({
reply:[],
all_comments:[],
});
let user_new_comment=reactive({
    status:"false",
})
function postComment(e){
    e.preventDefault();
    let formData=new FormData();
    if(all_post_info.sharer_email != undefined){
   axiosClient.post("/postComment",{
    post_id:postid,
    comment:comment.value,
    post_owner:all_post_info.sharer_email,
    user_who_comment_name:name_of_current_user,
    user:user_mail,
    tagged_users:all_tagged_users.info}).then(response=>{
    showComment.reply.push(response.data.reply);
    comment.value="";
    user_new_comment.status="true";
    document.getElementById("like").play();
   }).catch(e=>{
    console.log(e);
   })  
    }else{
   axiosClient.post("/postComment",{
    post_id:postid,
    comment:comment.value,
    post_owner:all_post_info.post_email,
    user_who_comment_name:name_of_current_user,
    user:user_mail,
    tagged_users:all_tagged_users.info}).then(response=>{
    showComment.reply.push(response.data.reply);
    comment.value="";
    user_new_comment.status="true";
    document.getElementById("like").play();
   }).catch(e=>{
    console.log(e);
   })
    }
 
}
onMounted(()=>{
let formData=new FormData();
formData.append("post_id",postid);
axiosClient.post("/findAllComments",formData).then(response=>{
showComment.all_comments=response.data.reply;
loading_icon.value='false';
}).catch(e=>{
    console.log(e);
})
});
watch(comment, ()=>{
if(comment.value.length > 0){
   let post_button=document.getElementById("post-button");
   post_button.removeAttribute("disabled");
}else{
    let post_button=document.getElementById("post-button");
   post_button.setAttribute("disabled","true");
}
});
function DeleteComment(comment_date, owner){
    var ask_if_user_wants_to_delete_comment=confirm("Do you want to Delete This comment?");
    if(ask_if_user_wants_to_delete_comment){
        let formData=new FormData();
        formData.append("date",comment_date);
        formData.append("email",owner);
        axiosClient.post('/DeleteComment',formData).then(response=>{
            document.getElementById("unlike").play();
        }).catch(e=>{
            console.log(e);
        })
    }else{
        return;
    }
    let current_div=comment_date+owner;
        document.getElementById(current_div).style.display="none";
}
function DeleteCommentReply(comment_date, owner){
    var ask_if_user_wants_to_delete_comment=confirm("Do you want to Delete This Reply?");
    if(ask_if_user_wants_to_delete_comment){
        let formData=new FormData();
        formData.append("date",comment_date);
        formData.append("email",owner);
        axiosClient.post('/DeleteCommentReply',formData).then(response=>{
            document.getElementById("unlike").play();
        }).catch(e=>{
            console.log(e);
        })
    }else{
        return;
    }
    let current_div=comment_date+owner;
        document.getElementById(current_div).style.display="none";
}
let checklink=reactive({
    status:false,
    current_item:"",
    count:0
});


watch(checklink, ()=>{
let current_item=checklink.current_item;
let side_container=document.getElementById(current_item);
side_container.style.visibility="visible";

});
watch(checklink, ()=>{
if(checklink.count == 0){
    let current_item=checklink.current_item;
    let side_container=document.getElementById(current_item);
   side_container.style.visibility="hidden";  
   
}


});
function displayDeleteIcon(comment_id){
    checklink.status=true;
    checklink.current_item=comment_id;
    checklink.count++;
    if(checklink.count > 1){
    checklink.count=0;
    }
    let current_comment=document.getElementById(comment_id);
    current_comment.style.visibility="visible";  
}
let hold_certain_comment_replies=reactive({
    to_decieve_compiler:"random",
    reply:[],
    sorted_reply:[],
    status:"",
    id_to_track:"",
    

});
let current_reply_div=ref('');
function showReplyBox(reply_id,comment_id){
    let get_reply_box=document.getElementById(reply_id);
    get_reply_box.style.display="block";
    let formData=new FormData();
    formData.append("comment_id",comment_id);
    axiosClient.post("/findCommentReplies",formData).then(response=>{
        hold_certain_comment_replies.reply.push(response.data.reply);
        current_reply_div=reply_id;
        hold_certain_comment_replies.reply.forEach(elem=>{
        hold_certain_comment_replies.sorted_reply=elem;
        });
        hold_certain_comment_replies.id_to_track=reply_id;
       
    }).catch(error=>{
        alert("Unable to fetch Replies");
    });
    window.location.href="#"+comment_id;
}
let comment_reply=ref('');
function replyComment(comment_id,user_being_replied){
    let formData=new FormData();
    formData.append("initial_comment_id",comment_id);
    formData.append("comment_reply",comment_reply.value);
    formData.append("user_being_replied",user_being_replied);
    formData.append("user_who_replied",user_mail);
    axiosClient.post("/replyComment",formData).then(response=>{
        //user_comment_reply=response.data.reply;
        comment_reply.value='';
        document.getElementById("like").play();
        location.reload();
    }).catch(e=>{
        alert("Unable to reply comment...");
    })
}
let other_reply=reactive({
    user_who_wrote_prev_comment:"",
    comment_id:""
});
function replyOtherUsers(user_who_wrote_prev_comment,text_holder){
    let textbox=document.getElementById(text_holder);
    let new_val=textbox.placeholder="Replying to \t"+user_who_wrote_prev_comment;
    other_reply.user_who_wrote_prev_comment=user_who_wrote_prev_comment;
    other_reply.comment_id=text_holder;
    let reply_btn=text_holder+"reply-btn";
    let comment_btn=document.getElementById(reply_btn);
    comment_btn.style.display="none";
    let other_reply_btn=text_holder+'other-reply-btn';
    let comment_btn_new=document.getElementById(other_reply_btn);
    comment_btn_new.style.display="block";
    window.location.href="#"+text_holder;
}
function sendOtherReply(text_holder){
    let textbox=document.getElementById(text_holder);
    let formData=new FormData();
    formData.append("initial_comment_id",other_reply.comment_id);
    formData.append("comment_reply",textbox.value);
    formData.append("user_being_replied",other_reply.user_who_wrote_prev_comment);
    formData.append("user_who_replied",user_mail);
    axiosClient.post("/replyComment",formData).then(response=>{
        location.reload();
    }).catch(e=>{
        alert("Unable to reply comment...");
    })
}
function url_to_link(text) {
    const urlPattern = /(?:https?:\/\/|www\.)?(?:[\w-]+\.)+(?:[a-z]{2,})(\/\S*)?/gi;
    if (!urlPattern.test(text)) {
        return text; // No URLs found, return original text
      }else{
        return text.replace(urlPattern, match => {
        const href = match.match(/^https?:\/\//i) ? match : `http://${match}`;
        return `<a style='font-weight:bold; color:purple;' href="${href}" target="_blank">${match}</a>`;
      });
      }
}
let taglist=reactive({
tagged_users:[],
tagged_users_result:{}
});
let all_tagged_users=reactive({
info:[],
value:[]
}); 
watch(comment, ()=>{
let str = comment.value;
let pattern = /\B@[a-z0-9_-]+/gi;
let user_match=str.match(pattern);
if(user_match === null)
console.log(null);
else{
    user_match.forEach(x => {
    taglist.tagged_users=x;
    
});
document.getElementById("tag_box").style.display="block";
let formData=new FormData();
let tagged_user=taglist.tagged_users.substring(1);
formData.append('search_info',tagged_user);
axiosClient.post("/Search",formData).then(response=>{
taglist.tagged_users_result=response.data.reply;
}).catch(e=>{
    console.log(e);
})

}

});
function hideTagBox(){
    let tag_box=document.getElementById("tag_box");
    tag_box.style.display="none";
}
function setTaggedUser(email,first_name,last_name){
   all_tagged_users.value=[];
    all_tagged_users.info.push(email);
    let user_full_name=first_name +'\t'+ last_name;
    all_tagged_users.value.push(user_full_name);
   let user_occurence=all_tagged_users.info.filter(x => x==email).length;
   if(user_occurence > 1){
   for(let i=0; i<all_tagged_users.info.length; i++){
    if(all_tagged_users.info[i]==email){
        all_tagged_users.info.splice(i,1);
        
    }
   }
   for(let i=0; i<all_tagged_users.value.length; i++){
    if(all_tagged_users.value[i]==user_full_name){
        all_tagged_users.value.splice(i,1);
       
    }
   }
   }
   let current_caption=comment.value;
   comment.value=current_caption+'\t'+all_tagged_users.value.join(" ");
    
    
}
function checkIfCommentIsLong(text){
     if(text==null){
        return;
    }else if(text.length < 400){
        return text;
    }
    else if(text.length > 400){
        let caption_new=text.slice(0,400) + "....See More";
        return caption_new;
    }
}
</script>
<template>
    <Header class="shadow-sm" style="background-color:white; position:fixed; padding-bottom:10px;  width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
<div v-if="loading_icon==='true'" class="spinner" style="display: flex; margin-top:100px; justify-content: center; align-items: center;">
        
    </div>
<div style="margin-top:200px;" v-else-if="all_post_info.error != ''">
<h2 class="fs-2 text-center m-2">{{all_post_info.error }}</h2>
</div>
 <div style="position:relative;" v-else class="all-post-info   container-fluid">
<div class="m-2" v-if="all_post_info.sharer_name != 'null' || all_post_info.sharer_name != undefined">
<h6  class="m-2 font-bold" style="position:relative;"><RouterLink :to='`/user/${all_post_info.sharer_email}`'><img v-if="all_post_info.sharer_avatar === null" src="../pictures/profile.png" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"  /><img v-else v-if="all_post_info.sharer_name != undefined" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${all_post_info.sharer_avatar}`' /><span style="position:absolute; margin-top:10px; font-weight: bold; top:0px; margin-left:55px; display:inline;">{{all_post_info.sharer_name}}</span></RouterLink></h6>
<p class="m-2" style="white-space:pre-wrap;" v-html="url_to_link(all_post_info.sharer_quote)"></p>
</div>
    <div  style="border:1px solid lightgray; cursor:pointer; position:relative;">
  <div  class="user-or-channel-info p-2">
    <h6 style="position:relative;"><RouterLink :to="`/user/${all_post_info.post_email}`"><img v-if="all_post_info.avatar === null" src="../pictures/profile.png" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"  /><img v-else  style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${all_post_info.avatar}`' /><span style="position:absolute; margin-top:10px; font-weight: bold; top:0px; margin-left:50px; display:inline;">{{all_post_info.name}}</span></RouterLink></h6>
  </div>
  
  <a v-if="all_post_info.sharer_name !=undefined " :href="`/status/${all_post_info.postid}`"><p  style="margin-bottom: 5px; white-space:pre-wrap;" class="m-2 post-caption" v-html="url_to_link(all_post_info.caption)"></p></a>
  <p v-else style="margin-bottom: 5px; white-space:pre-wrap;" v-html="url_to_link(all_post_info.caption)" class="m-2 post-caption"></p>
                <div class="flex-img">
                        <img style="border-top-left-radius:10px;" v-if=" all_post_info.img_1 !=null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${all_post_info.img_1}`' />
                        <img style="border-top-right-radius:10px;" v-if="all_post_info.img_2 !=null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${all_post_info.img_2}`' />
                        <img style="border-bottom-left-radius:10px;" v-if="all_post_info.img_3 !=null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${all_post_info.img_3}`' />
                        <img style="border-bottom-right-radius:10px;" v-if="all_post_info.img_4 !=null" loading="lazy" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${all_post_info.img_4}`' />
                    </div>
                    <div v-if=" all_post_info.video != null" class="flex-video">
                      <VideoPlayerComponent style="max-width:100%;" :video_info="{
                        source:all_post_info.video
                      }" />
                    </div>

  <p class="date m-2">{{moment(all_post_info.post_date).fromNow()}}</p>
    </div>
  <LikeCommentSharesCount :post_data="{
    likes_count:all_post_info.likes_count,
    comment_count:all_post_info.comment_count,
    shares_count:all_post_info.shares_count,
    bookmark_count:all_post_info.bookmark_count,
    status_id:postid,

   }" />
  <form class="comment-form" style="margin-top:0px;" v-if="user_mail!=null"  @submit="postComment">
  <textarea v-if="all_post_info.isReply === ''"  v-model="comment" class="outline-none fs-5" placeholder="Post your comment" style="resize: none; border-radius: 50px; z-index:2; font-weight:600; margin-top: 0px; border:none;   width: 100%;"></textarea>
  <span     v-if="all_post_info.isReply === ''"  class="position-to-right"><button id="post-button" disabled   class="btn border-20px btn-md btn-success">Post</button></span>
  <div id="tag_box" class="card tag_users_box card-default shadow-sm cursor-pointer p-2">
        <div style="display:block;"><span @click="hideTagBox" class="m-2"><i class="fa fa-arrow-left"></i></span></div>
            <li @click="setTaggedUser(u.email,u.first_name,u.last_name)" class="list-unstyled m-4" v-for="u in taglist.tagged_users_result"><img v-if="u.profile_picture === null || u.profile_picture === 'null'" loading="lazy" style="border-radius: 50px; width: 40px; height:40px; object-fit: cover; float:left;"  src="../pictures/profile.png"  ><img v-else loading="lazy" style="border-radius: 50px; width: 40px; height:40px; object-fit: cover; float:left;"  :src="`https://res.cloudinary.com/fishfollowers/image/upload/${u.profile_picture}`"   ><span class="m-2">{{ u.first_name + '\t' + u.last_name }}</span></li>
    </div>
    </form>
    <audio style="display: none;"  id="like">
    <source type="audio/wav" src="../notifications/like bell.wav" />
    
    </audio>
    <audio style="display: none;"  id="unlike">
    <source type="audio/wav" src="../notifications/unlike bell.wav" />
    </audio>
    <div  class="container-fluid all-comments">
        <div v-for="x in showComment.reply" :id="x.comment_date+x.user_who_comment" style=" width: 100%;" v-if="user_new_comment.status === 'true'" class="card  card-default">
            <div  style="position:relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header d-flex">
                <div style="margin-right: auto;"><img v-if="x.avatar === null" src="../pictures/profile.png" style="border-radius: 50%; object-fit: cover; width: 40px; height: 40px;"> <img v-else style="border-radius: 50%; object-fit: cover; width: 40px; height: 40px; " :src='`https://res.cloudinary.com/fishfollowers/image/upload/${x.avatar}`'/><span style="position:absolute; top:0px; margin-left:50px; margin-top:10px;">{{x.first_name}}</span></div>
                <span style="margin-left: auto;" v-if="x.user_who_comment === user_mail"  @click="displayDeleteIcon(x.comment_date)" class="d-flex cursor-pointer comment-info"><i  class="fas fa-ellipsis-h" style="position:relative; margin-right: 5px;"></i>
                    <ul    :id="x.comment_date" class="list-unstyled comment-info-holder p-2 d-flex justify-content-center">
                    <li class="list-unstyled p-2" @click="DeleteComment(x.comment_date,x.user_who_comment)">Delete      <i class="fa fa-trash"></i></li>
                </ul></span>
            </div>
            <RouterLink :to='`/comment/${x.comment_id}`'><p style="white-space:pre-wrap;" v-html="checkIfCommentIsLong(url_to_link(x.comment))" class="p-2"></p></RouterLink>
            <p class="p-2">{{moment(x.comment_date).fromNow() }}</p>
        </div>
        <div v-for="i in showComment.all_comments" :id="i.created_at+i.user_who_comment" style=" width: 100%;"  class="card p-2 card-default">
            
            <div  style="position:relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header d-flex">
                <div style="margin-right: auto;"><RouterLink :to='`/user/${i.user_who_comment}`'><img v-if="i.profile_picture === null" src="../pictures/profile.png" style="border-radius: 50%; object-fit: cover; width: 40px; height: 40px;" /><img v-else style="border-radius: 50%; object-fit: cover; width: 40px; height: 40px; " :src='`https://res.cloudinary.com/fishfollowers/image/upload/${i.profile_picture}`'/><span style="position:absolute; top:0px; margin-left:50px; margin-top:10px;">{{i.first_name}}</span></RouterLink></div>
                <span  v-if="i.user_who_comment === user_mail"  @click="displayDeleteIcon(i.created_at)" class="d-flex cursor-pointer comment-info"><i  class="fas fa-ellipsis-h" style="position:relative; margin-right: 5px;"></i>
                    <ul :id="i.created_at" class="list-unstyled comment-info-holder p-2 d-flex justify-content-center">
                    <li class="list-unstyled p-2" @click="DeleteComment(i.created_at,i.user_who_comment)">Delete      <i class="fa fa-trash"></i></li>
                </ul></span>
            </div>
            <RouterLink :to='`/comment/${i.comment_id}`'><p style="white-space:pre-wrap;" v-html="checkIfCommentIsLong(url_to_link(i.comment))" class="p-2"></p></RouterLink>
            <p class="p-2">{{moment(i.created_at).fromNow() }}</p>
            <small @click="showReplyBox(i.created_at+i.comment_id,i.comment_id)" class="m-2 cursor-pointer"><b><i class="fs-6 fa fa-regular fa-comment"></i>{{ i.reply_count }}</b></small>
            <h6 v-if="i.reply_count > 0" class="m-2"> <b>Replies</b></h6>
            <div  :id="i.created_at+i.comment_id" class="comment-replies">
                <span v-if="hold_certain_comment_replies.id_to_track === ''"><img width="100" height="100" src="../landing/loading-loader.gif" /></span>
                <div v-else v-for="y in hold_certain_comment_replies.sorted_reply" :id="y.created_at+y.user_who_replied" style=" width: 100%;"  class="card p-2 card-default">
                    <div style="position:relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header d-flex">
                <div style="margin-right: auto;"><img v-if="y.profile_picture === null" src="../pictures/profile.png" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;"  /><img v-else style="border-radius: 50%; object-fit: cover; width: 40px; height: 40px; " :src='`https://res.cloudinary.com/fishfollowers/image/upload/${y.profile_picture}`'/><span style="position:absolute; top:0px; margin-left:50px; margin-top:10px;"><RouterLink :to='`/user/${y.user_who_replied}`'>{{y.first_name}}</RouterLink></span></div>
                <span  v-if="y.user_who_replied === user_mail"  @click="displayDeleteIcon(y.created_at)" class="d-flex cursor-pointer comment-info"><i  class="fas fa-ellipsis-h" style="position:relative; margin-right: 5px;"></i>
                    <ul :id="y.created_at" class="list-unstyled comment-info-holder p-2 d-flex justify-content-center">
                    <li class="list-unstyled p-2" @click="DeleteCommentReply(y.created_at,y.user_who_replied)">Delete      <i class="fa fa-trash"></i></li>
                </ul></span>
            </div>
            <p v-html="url_to_link(y.comment_reply)" style="white-space:pre-wrap;" class="p-2"></p>
            <p class="p-2">{{moment(y.created_at).fromNow() }}</p>
            <small @click="replyOtherUsers(y.user_who_replied,i.comment_id)" class="m-2 cursor-pointer"><b><i class="fs-6 fa fa-regular fa-comment"></i>{{ y.other_reply_count }}</b></small>
          
                </div>
              
                <textarea v-if="user_mail !=null " :id="i.comment_id"   class="comment_reply" :placeholder='`Replying to ${i.first_name}`' v-model="comment_reply"></textarea>
                <button @click="replyComment(i.comment_id,i.user_who_comment)" :id="i.comment_id+'reply-btn'"  class="btn btn-sm reply-btn btn-success"><i class="fa fa-paper-plane"></i></button>
                <button @click="sendOtherReply(i.comment_id)" :id="i.comment_id+'other-reply-btn'"  class="btn btn-sm btn-success send_other_reply"><i class="fa fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

 </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
  .all-post-info{
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  width: 100%;
  margin-top:50px;
 }
 
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    
}
.flex-img > img{
    margin-left: 2px;
    object-fit: cover;
    width: 100px;
    height: 100px;
    
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    justify-content: center;
    align-items: center;
    padding: 0px;
    
}
.flex-img > img{
    margin-left: 2px;
    margin-top: 2px;
    object-fit: cover;
    flex: 1;
    flex-basis: 40%;
    height: 100%;
   
    
}
.position-to-right{
    display: flex;
    justify-content: flex-end;
    
}
.border-20px{
    border-radius: 20px;
    font-weight: bold;
    width: 100px;
}
.card{
    border: none;
}
.comment-info-holder{
   flex-direction: column;
   font-size: 13px;
   background-color: whitesmoke;
   position: absolute;
   top: 70%;
  right: 5%;
   width: 100px;
  visibility: hidden;
}
.comment-replies{
    display: none;
    position: relative;
}
.other-comment-replies{
    display: none;
    position: relative;
}
.comment_reply{
    border-radius: 40px;
    resize: none;
    width: 100%;
    height: auto;
    border:none;
    border:2px solid rgb(142, 0, 142);

}
.reply-btn{
    position: absolute;
    right: 5%;
    bottom: 0%;
    margin-bottom: 20px;
}
.send_other_reply{
    position: absolute;
    right: 5%;
    bottom: 0%;
    margin-bottom: 20px;
    display:none;
}
.all-comments{
    padding:0px;
    margin:0px;
}
.tag_users_box{
    display:none;
    position: absolute; 
    bottom:120px;
    z-index:1; 
    left:20px; 
    overflow-x: hidden;  
    overflow-y: scroll; 
    height:200px; 
    border-radius:5px; 
    width:auto;
}
.spinner {
  position: absolute;
  top: 30%;
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
  .all-post-info{
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  width: 50%;
  margin: 0px auto;
  margin-top:60px;
 }
 
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    
}
.flex-img > img{
    margin-left: 2px;
    object-fit: cover;
    width: 100px;
    height: 100px;
    
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    justify-content: center;
    align-items: center;
    padding: 0px;
    
}
.flex-img > img{
    margin-left: 2px;
    margin-top: 2px;
    object-fit: cover;
    flex: 1;
    flex-basis: 40%;
    height: 100%;
   
    
}
.card{
    border: none;
}
.comment-info-holder{
   flex-direction: column;
   font-size: 13px;
   background-color: whitesmoke;
   position: absolute;
   top: 70%;
  right: 5%;
   width: 100px;
    visibility: hidden;
   
}
.comment-replies{
    display: none;
    position: relative;
}
.other-comment-replies{
    display: none;
    position: relative;
}
.comment_reply{
    border-radius: 40px;
    resize: none;
    width: 100%;
    height: auto;
    border:none;
    border:2px solid rgb(142, 0, 142);

}
.reply-btn{
    position: absolute;
    right: 5%;
    bottom: 0%;
    margin-bottom: 20px;
}
.send_other_reply{
    position: absolute;
    right: 5%;
    bottom: 0%;
    margin-bottom: 20px;
    display:none;
}
.tag_users_box{
    display:none;
    position: absolute; 
    bottom:120px;
    z-index:1; 
    left:20px; 
    overflow-x: hidden;  
    overflow-y: scroll; 
    height:200px; 
    border-radius:5px; 
    width:auto;
}
.spinner {
  position: absolute;
  top: 30%;
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
@media screen and (min-width:1224px) {
 .all-post-info{
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  width: 50%;
  margin: 0px auto;
  margin-top:60px;
  border:1px solid rgba(233, 233, 233, 0.442);

 }
 
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    padding:0px;
    
}
.flex-img > img{
    margin-left: 2px;
    object-fit: cover;
    width: 100px;
    height: 100px;
    
}
.flex-img{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    border-radius: 20px;
    justify-content: center;
    align-items: center;
    padding: 0px;
    
}
.flex-img > img{
    margin-left: 2px;
    margin-top: 2px;
    object-fit: cover;
    flex: 1;
    flex-basis: 40%;
    height:100%;
   
    
}
.position-to-right{
    display: flex;
    justify-content: flex-end;
    
}
.border-20px{
    border-radius: 20px;
    font-weight: bold;
    width: 100px;
}
.card{
    border: none;
}
.comment-info-holder{
   flex-direction: column;
   font-size: 13px;
   background-color: whitesmoke;
   position: absolute;
   top: 70%;
  right: 5%;
   width: 100px;
   visibility: hidden;
}
.comment-replies{
    display: none;
    position: relative;
}
.other-comment-replies{
    display: none;
    position: relative;
}
.comment_reply{
    border-radius: 40px;
    resize: none;
    width: 100%;
    height: auto;
    border:none;
    border:2px solid rgb(142, 0, 142);

}
.reply-btn{
    position: absolute;
    right: 5%;
    bottom: 0%;
    margin-bottom: 20px;
}
.send_other_reply{
    position: absolute;
    right: 5%;
    bottom: 0%;
    margin-bottom: 20px;
    display:none;
}
.tag_users_box{
    display:none;
    position: absolute; 
    top:300px; 
    z-index:1; 
    left:20px; 
    overflow-x: hidden;  
    overflow-y: scroll; 
    height:200px; 
    border-radius:5px; width:auto;
}
.spinner {
  position: absolute;
  top: 30%;
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