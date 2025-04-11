<script setup>
import { RouterLink } from 'vue-router';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';
import { useRouter } from 'vue-router';
import { onMounted } from 'vue';
import { defineProps } from 'vue';
import axiosClient from '../axios';
import {ref} from "vue";
import { watch } from 'vue';
import { reactive } from 'vue';
import ImageSliderForPost from './ImageSliderForPost.vue';
let user_post_id=defineProps(['post_id','post_content','post_owner']);
let post_id=user_post_id.post_id;
let post_content=user_post_id.post_content;
let comment_status=user_post_id.post_content.post_is_comment_status;
let isSound=localStorage.getItem('ISSOUND');
let current_user=localStorage.getItem('USER_MAIL');
let user_pic=localStorage.getItem('PICTURE');
let post_info=reactive({
    post_data:user_post_id.post_content,
});
let like_state=reactive({
    post_id:"",
    count:post_content.post_likes_count,
    commentCount:post_content.post_comments_count,
    shareCount:post_content.post_shares_count,
    quoteCount:post_content.post_shares_count,
});
let taglist=reactive({
tagged_users:[],
tagged_users_result:{}
});
let all_tagged_users=reactive({
info:[],
value:[]
}); 
onMounted(() =>{
    let formData=new FormData();
    formData.append("post_id",post_id);
    formData.append("email",current_user);
    axiosClient.post("/findAllPostUserLiked",formData).then(response=>{
       if(response.data.reply === 'true'){
        document.getElementById(post_id).style.color="red";
       }else{
        document.getElementById(post_id).style.color="black";
       }
     
    }).catch(e=>{
        console.log(e);
    });
});
function updateLike(){
    //go to database and check if user has liked post before.
    let formData=new FormData();
    formData.append("email",current_user);
    formData.append("post_id",post_id);
    axiosClient.post("/checkIfUserLiked",formData).then(response=>{
      if(response.data.reply==='false'){
        axiosClient.post("/DeleteLike",formData).then(response=>{
            like_state.count -=1;
            if(isSound != 'true'){
                document.getElementById("unlike").play();
            }
            document.getElementById(post_id).style.color="black";
        }).catch(err=>{
            console.log(err)
        });
      }else{
        like_state.count +=1;
        if(isSound != 'true'){
        document.getElementById("like").play();
        }
        document.getElementById(post_id).style.color="red";
      }
    }).catch(e=>{
        console.log(e);
    });
   
document.getElementById(post_id).style.color="red";
}
let router=useRouter();
let quote=ref('');
function sharePost(e){
    e.stopImmediatePropagation();
    if(quote.value ===''){
        let post_caption=post_content.post_caption;
   let post_image1=post_content.post_image_one;
   let post_image2=post_content.post_image_two;
   let post_image3=post_content.post_image_three;
   let post_image4=post_content.post_image_four;
   let post_video=post_content.post_video;
   let post_owner=post_content.post_owner_name;
   let postid=Math.random(10,100000) * 1000;
   let prev_id=post_id;
   let post_owner_email=atob(post_content.post_owner_email);  
   let current_user_first_name=localStorage.getItem('FIRSTNAME');
   let current_user_last_name=localStorage.getItem('LASTNAME');
   let current_user_email=localStorage.getItem('USER_MAIL');
   let formData=new FormData();
   formData.append("caption",post_caption);
   formData.append("name",post_owner);
   formData.append("email",post_owner_email);
   formData.append("post_img1",post_image1);
   formData.append("post_img2",post_image2);
   formData.append("post_img3",post_image3);
   formData.append("post_img4",post_image4);
   formData.append("video",post_video);
   formData.append("postid",postid);
   formData.append("prev_id",prev_id);
   formData.append("firstname",current_user_first_name);
   formData.append("lastname",current_user_last_name);
   formData.append("email_of_user_who_shared",current_user_email);
   formData.append("tagged_users","");

    axiosClient.post("/sharePost",formData).then(response=>{
       document.getElementById("quote"+prev_id).style.display="none";
    }).catch(error=>{
        console.log(error);
    });
    }else{
    let post_caption=post_content.post_caption;
   let post_image1=post_content.post_image_one;
   let post_image2=post_content.post_image_two;
   let post_image3=post_content.post_image_three;
   let post_image4=post_content.post_image_four;
   let post_video=post_content.post_video;
   let post_owner=post_content.post_owner_name;
   let postid=Math.random(10,100000) * 1000;
   let prev_id=post_id;
   let post_owner_email=atob(post_content.post_owner_email);  
   let current_user_first_name=localStorage.getItem('FIRSTNAME');
   let current_user_last_name=localStorage.getItem('LASTNAME');
   let current_user_email=localStorage.getItem('USER_MAIL');
    axiosClient.post("/sharePost",{
        caption:post_caption,
        name:post_owner,
        email:post_owner_email,
        post_img1:post_image1,
        post_img2:post_image2,
        post_img3:post_image3,
        post_img4:post_image4,
        video:post_video,
        postid:postid,
        prev_id:prev_id,
        firstname:current_user_first_name,
        lastname:current_user_last_name,
        email_of_user_who_shared:current_user_email,
        quote:quote.value,
        tagged_users:all_tagged_users.info

    }).then(response=>{
       if(response.data.reply==='true'){
        alert("You Successfuly Quote this post..");
        like_state.quoteCount+=1;
        document.getElementById("quote"+prev_id).style.display="none";
       }else{
        alert("You already responded to this post");
       }
    }).catch(error=>{
        console.log(error);
    });
    }
   

}
function quotePost(id){
    let quote_editor=document.getElementById(id);
    quote_editor.style.display='block';
}
function hidePost(boxid){
    let quote_editor=document.getElementById(boxid);
    quote_editor.style.display='none'; 
}
async function share(){
  let post_link='https://hexarex.com/status/'+post_id;
  const shareData = {
  title: "From Hexarex",
  text: "See this Post on Hexarex.com",
  url:post_link,
};

   
    try {
    await navigator.share(shareData);
  } catch (err) {
    console.log(err);
  }

}

function bookMark(post_id){
    let formData=new FormData();
    formData.append("post_id",post_id);
    formData.append("email",current_user);
    axiosClient.post("/bookmarkPost",formData).then(response=>{
        if(response.data.reply==='duplicate'){
            let current_bookmarked_post="bookmark"+post_id;
            document.getElementById(current_bookmarked_post).style.color='black';
        }else{
            let current_bookmarked_post="bookmark"+post_id;
            document.getElementById(current_bookmarked_post).style.color='magenta';
        }
    }).catch(error=>{
        console.log(error);
    });
}
function checkIfFriendPostIsLong(text, key){
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
watch(quote, ()=>{
quote.value ==='' ? document.getElementById("repost").setAttribute("disabled","true") 
: document.getElementById("repost").removeAttribute("disabled");


let str = quote.value;
let pattern = /\B@[a-z0-9_-]+/gi;
let user_match=str.match(pattern);
if(user_match === null)
return;
else{
    user_match.forEach(x => {
    taglist.tagged_users=x;
    
});
document.getElementById("quote_tag_box"+post_id).style.display="block";
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
   let current_caption=quote.value;
   quote.value=current_caption+'\t'+all_tagged_users.value.join(" ");
  
    
    
}
function hideTagBox(postid){
    let tag_box=document.getElementById("quote_tag_box"+postid);
    tag_box.style.display="none";
}
</script>
<template>
    <div :id='`quote${post_id}`' class=" quote-editor shadow-sm">
        <span style='color:black; cursor:pointer;' @click="hidePost('quote'+post_id)" class="fs-2 m-4 font-bold">&times;</span>
        <button id='repost' disabled @click="sharePost" style="border-radius:50px; float: right; width: 80px;" class="btn p-2 m-2 btn-sm font-bold btn-success">Repost</button>
        <div class="user-opinion-div" style="position:relative;">
       <img v-if="user_pic === null || user_pic === 'null'" src="../pictures/profile.png" style="width:40px; height:40px; border-radius:50px; object-fit:cover;" /> <img v-else  style="width:40px; height:40px; border-radius:50px; object-fit:cover;" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${user_pic}`' />
        <textarea v-model="quote" placeholder="Add your thought.." style="resize: none;  border:none;"></textarea>
        <div :id="'quote_tag_box'+post_id" class="card tag_users_box card-default cursor-pointer p-2" style="position: absolute; top:50px; z-index:1; left:10px; overflow-x: none;  overflow-y: scroll;  height:200px; border-radius:5px; width:auto;">
        <div style="display:block;"><span @click="hideTagBox(post_id)" class="m-2"><i class="fa fa-arrow-left"></i></span></div>
            <li @click="setTaggedUser(u.email,u.first_name,u.last_name)" class="list-unstyled m-4" v-for="u in taglist.tagged_users_result"><img v-if="u.profile_picture === null || u.profile_picture === 'null'" loading="lazy" style="border-radius: 50px; width: 40px; height:40px; object-fit: cover; float:left;"  src="../pictures/profile.png"  ><img v-else loading="lazy" style="border-radius: 50px; width: 40px; height:40px; object-fit: cover; float:left;"  :src="`http://localhost:8000/storage/${u.profile_picture}`"   ><span class="m-2">{{ u.first_name + '\t' + u.last_name }}</span></li>
    </div>
        </div>
        <div class="previous-post card" style="height: auto; margin-top: 0px;">
            <div style=" position: relative; background-color: rgba(255, 255, 255, 0.634);" class="card-header">
               <img v-if="post_content.post_owner_avatar === null || post_content.post_owner_avatar === 'null'" src="../pictures/profile.png" class='img-circle small-thumbnail'/> <img v-else loading="lazy"  :src='`https://res.cloudinary.com/fishfollowers/image/upload/${post_content.post_owner_avatar}`' class='img-circle small-thumbnail'>
                <span class="m-2" style="position: absolute; left: 15%; top: 20%;">{{post_content.post_owner_name}}</span>
            </div>
            <p style="white-space: pre-wrap;" class="p-2">{{checkIfFriendPostIsLong(post_content.post_caption)}}</p>
            <ImageSliderForPost
                        style="margin-top:0px;"
                        v-if="post_content.post_video === null && post_content.post_image_one !== null"
                        :user_email="post_content.post_owner_email"
                        :postid="post_content.postid"
                        :images="[
                            post_content.post_image_one && `https://res.cloudinary.com/fishfollowers/image/upload/${post_content.post_image_one}`,
                            post_content.post_image_two && `https://res.cloudinary.com/fishfollowers/image/upload/${post_content.post_image_two}`,
                            post_content.post_image_three && `https://res.cloudinary.com/fishfollowers/image/upload/${post_content.post_image_three}`,
                            post_content.post_image_four && `https://res.cloudinary.com/fishfollowers/image/upload/${post_content.post_image_four}`
                        ].filter(Boolean)"
                    />
                    <div v-if="post_content.post_video != null" class="flex-video">
                        <video  controls>
                            <source :src='`https://res.cloudinary.com/fishfollowers/video/upload/${post_content.post_video}`' />
                        </video>
                    </div>
        </div>
    </div>
  <ul class="inline-flex">
                        <li :id="post_id" @click="updateLike" class="m-2 list-unstyled"><i class="fa-regular fa-heart"></i><span v-if="like_state.count < 2">{{ like_state.count }} Like</span><span v-else>{{ like_state.count }} Likes</span></li>
                        <li v-if="comment_status === '' || comment_status===null"  class="m-2 list-unstyled"><RouterLink :post_data="post_id" :to='`/status/${post_id}`'><i style="margin-right: 2px;" class="fa-regular fa-comment"></i>{{ like_state.commentCount }}</RouterLink></li>
                        <li @click="share" class="m-2 list-unstyled"><i class="fa-regular fa-share-from-square"></i></li>
                        <li @click="quotePost('quote'+post_id)" class="m-2 list-unstyled"><i class="far  fa-edit"></i>{{ like_state.quoteCount }}</li>
                        <li @click="bookMark(post_id)" :id="'bookmark'+post_id" class="m-2 list-unstyled"><i class="fa-regular fa-newspaper"></i></li>
                        
</ul>
<audio v-if="isSound != 'true'" style="display: none;"  id="like">
    <source type="audio/wav" src="../notifications/like bell.wav" />
    
</audio>
<audio v-if="isSound != 'true'" style="display: none;"  id="unlike">
    <source type="audio/wav" src="../notifications/unlike bell.wav" />
    
</audio>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    cursor: pointer;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
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
    height: 400px;
   
    
}
.quote-editor{
    display: none;
    position: fixed;
    height: 100%;
    width: 100%;
    background-color: rgba(255, 255, 255, 1);
    left: 0px;
    z-index: 1;
    top:0%;

}
.quote-editor > textarea{
    border: none;
    background-color: none;
}
.flex-video{
    padding: 0px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.flex-video > video{
    flex-basis: 30%;
    width:400px;
    height: 300px;
}
.card{
    border-radius: 0px;
    margin-top: 0px;
}
.user-opinion-div{
    display: flex;
    flex-direction: row;
}
.user-opinion-div > textarea{
    background:none;
    outline: none;
    width:100%;
    margin-left:10px;
    margin-bottom:5px;
    border-radius: 5px;

}
.tag_users_box{
    display:none;
}
}
@media screen and (min-width:620px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    cursor: pointer;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
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
    height: 400px;
   
    
}
.quote-editor{
    display: none;
    position: fixed;
    height: 50%;
    width: 50%;
    background-color: rgba(255, 255, 255, 1);
    left: 20px;
    right: 20px;
    z-index: 1;
    top:20%;
    margin:0px auto;

}
.quote-editor > textarea{
    border: none;
    background-color: none;
}
.flex-video{
    padding: 0px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.flex-video > video{
    flex-basis: 30%;
    width:400px;
    height: 300px;
}
.card{
    border-radius: 0px;
}
.user-opinion-div{
    display: flex;
    flex-direction: row;
}
.user-opinion-div > textarea{
    background:none;
    outline: none;
    width:100%;
    margin-left:10px;
    border-radius: 5px;
    background-color: white;
}
.tag_users_box{
    display:none;
}
}
@media screen and (min-width:1224px) {
    .inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    cursor: pointer;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
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
    height: 400px;
   
    
}
.quote-editor{
    display: none;
    position: fixed;
    height: 100%;
    width: 50%;
    background-color: rgba(255, 255, 255, 1);
    left: 20px;
    right: 20px;
    z-index: 1;
    top:8%;
    margin:0px auto;
    

}
.quote-editor > textarea{
    border: none;
    background-color: none;
}
.flex-video{
    padding: 0px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.flex-video > video{
    flex-basis: 30%;
    width:400px;
    height: 300px;
}
.card{
    border-radius: 0px;
}
.user-opinion-div{
    display: flex;
    flex-direction: row;
}
.user-opinion-div > textarea{
    background:none;
    outline: none;
    max-width:100%;
    margin-left:10px;
    border-radius: 5px;
    background-color: white;
}
.tag_users_box{
    display:none;
}
}
</style>
