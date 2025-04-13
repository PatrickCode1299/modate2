<script setup>
import Header from "../component/Header.vue";
import store from "../store";
import { ref,reactive, watch, onMounted, onUpdated } from "vue";
import axiosClient from "../axios";
import moment from 'moment'
import Pusher from 'pusher-js';
import LoadJsVideoComponent from "../component/LoadJsVideoComponent.vue";
import VideoPlayerComponent from "../component/VideoPlayerComponent.vue";
import ChatSkeletonLoader from "../component/ChatSkeletonLoader.vue";
import ProgressBar from "../component/ProgressBar.vue";
import { useRouter, useRoute } from "vue-router";
const user_mail=localStorage.getItem('USER_MAIL');
const authToken=localStorage.getItem('TOKEN');
const goto=useRouter();
const route=useRoute();
let message_id=route.params.uid;
let formData=new FormData();
formData.append("unique_id",message_id);
let chat_info=reactive({
isLoadingChat:true,
name:"",
avatar:"",
last_activity:"",
sender_email:"",
reciever_email:"",
sender_message:[],
reciever_message:[],
});
let info=reactive({
    isBlocked:"",
});
function scrollToBottom() {
    window.scrollTo(0,document.body.scrollHeight);
}
onUpdated(()=>{
scrollToBottom();
});
onMounted(()=>{
    axiosClient.post("/findConvo",formData).then(response=>{
    if(response.data.sender!=user_mail){
        chat_info.sender_email=user_mail;
        chat_info.reciever_email=btoa(response.data.sender);
        let formData=new FormData();
        formData.append('user',response.data.sender);
        axiosClient.post("/findRecieverInfo",formData).then(res=>{
        chat_info.name=res.data.reply.first_name + res.data.reply.last_name;
        chat_info.avatar=res.data.reply.profile_picture;
        chat_info.last_activity=res.data.reply.updated_at;
        chat_info.isLoadingChat=false;
        }).catch(err=>{
            console.log(err);
        });
    }else{
        chat_info.sender_email=response.data.sender;
        chat_info.reciever_email=btoa(response.data.reciever);
        let formData=new FormData();
        formData.append('user',response.data.reciever);
        axiosClient.post("/findRecieverInfo",formData).then(res=>{
        chat_info.name=res.data.reply.first_name + "\t"+  res.data.reply.last_name;
        chat_info.avatar=res.data.reply.profile_picture;
        chat_info.last_activity=res.data.reply.updated_at;
        chat_info.isLoadingChat=false;
        }).catch(err=>{
            console.log(err);
        });
    }
let all_convo=response.data.reply;
conversation.convo=all_convo;
all_convo.forEach(elem => {

        conversation.message_chat.push({convo: elem.conversation, file:elem.file, file_status:elem.file_status, sender:elem.sender, reciever:elem.reciever, isRead:elem.isRead, date:elem.created_at});
       
 
});
let user_who_blocked=chat_info.reciever_email;
let current_user=user_mail;
let formData=new FormData();
formData.append("current_user",current_user);
formData.append("user_who_blocked",user_who_blocked);
axiosClient.post("/isUserBlocked",formData).then(response=>{
info.isBlocked=response.data.reply;
}).catch(error=>{
console.log(error);
});

}).catch(error=>{
    console.log(error);
})
});


let conversation=reactive({
    message_chat:[],
    chat_doc:"",
    doc_ext:"",
    uploadProgress:0,
})
let message_data=reactive({
    file:"",
    doc_uri:"",
    file_ext:"",
})
let server_reply_data_for_user=reactive({
    file:"",
    text:""
})
let user_message=ref('');

function showFile(){
    document.getElementById("file").click();
}
let file=ref('');
function chat_document(e){
    const doc=e.target.files[0];
  message_data.file=doc;
  file.value=doc;
  let pattern=new RegExp('[^.]+$');
  
    let check_extension=file.value.name.match(pattern);
    let file_extension=check_extension[0];
    message_data.file_ext=file_extension.toLowerCase();
    let reader;
    switch(file_extension.toLowerCase()){
        case 'jpg':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        case 'jpeg':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        case 'jfif':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        case 'gif':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        case 'png':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        case 'mp4':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        case 'mp3':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        case 'wav':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        case 'pdf':
        reader=new FileReader();
        reader.onload=() =>{
        message_data.doc_uri=reader.result;
        };
        reader.readAsDataURL(doc);
        break;
        default:
        console.log("No file added");
        
    }
  
}
function sendMessage(e){
    e.preventDefault();
    document.getElementById("send_msg").setAttribute("disabled","true");
    let message_id=route.params.uid;
    let formData=new FormData();
    if(message_data.file ===''){
    formData.append("conversation",user_message.value);
    formData.append("unique_id",message_id);
    formData.append("sender",user_mail);
    formData.append("reciever",atob(chat_info.reciever_email));
    axiosClient.post("/sendMessage",formData).then(response=>{
        user_message.value='';
       // conversation.message_chat.push({convo: response.data.reply.conversation, file:'', sender:response.data.reply.sender, reciever:response.data.reply.reciever, date:response.data.reply.created_at});
    }).catch(error=>{
        console.log(error);
    });
 

    }else{
    conversation.uploadProgress=0;
    formData.append("conversation",user_message.value);
    formData.append("doc",file.value);
    formData.append("unique_id",message_id);
    formData.append("sender",user_mail);
    formData.append("reciever",atob(chat_info.reciever_email));
    axiosClient.post("/sendMessage",formData, {onUploadProgress:(event)=>{
            conversation.uploadProgress = Math.round((event.loaded * 100) / event.total);
        }}).then(response=>{
        user_message.value='';
        document.getElementById("file-preview").style.display="none";
        //conversation.message_chat.push({convo: response.data.reply.conversation, file:response.data.reply.file, sender:response.data.reply.sender, reciever:response.data.reply.reciever, date:response.data.reply.created_at});
    }).catch(error=>{
        console.log(error);
    }).finally(()=>{
        conversation.uploadProgress = 0;
    });


    }

    
    
} 
function showDocument(doc,doc_status){
    conversation.chat_doc="";
    conversation.chat_doc=doc;
    document.getElementById("doc-container").style.display="block";
    let pattern=new RegExp('[^.]+$');
  conversation.doc_ext=doc_status; 
    
}
function hideDoc(){
    document.getElementById("doc-container").style.display="none";
}
watch(user_message, ()=>{
if(user_message.value.trim().length > 0){
    console.log(user_message.value.length);
    document.getElementById("send_msg").removeAttribute("disabled");
}else{
    document.getElementById("send_msg").setAttribute("disabled","true");
}
});
function hide_file_preview(){
    let container=document.getElementById("file-preview");
    container.style.display="none";
    message_data.file_ext="";
    message_data.doc_uri="";
}
onMounted(()=>{
    function check_new_message(){
        const pusher = new Pusher('d81e8911650d34cdc928', {
  cluster: 'eu',
  encrypted: true, 
});
const channel = pusher.subscribe('chat');
channel.bind('send_message', function(data) {
    conversation.message_chat.push({convo: data[0].conversation, file:data[0].file, file_status:data[0].file_status, sender:data[0].sender, reciever:data[0].reciever,isRead:data[0].isRead, date:data[0].created_at});
    document.getElementById("unlike").play();
    scrollToBottom();
});

}
    check_new_message();
});
document.title="Chat";


</script>
<template>
   <Header class="shadow-sm" style="background-color:white; display:none; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
   <div style="display:block;" v-if="chat_info.isLoadingChat===true">
   <ChatSkeletonLoader  />
   </div>
    <div v-else id="container" class="container  edit-container">
     <div class="reciever-details-header shadow-sm p-2">
        <img  v-if="chat_info.avatar === null" style="object-fit: cover; margin-right:auto;" src="../pictures/profile.png"  class="reciever-img"/>
        <img v-else style="object-fit: cover; margin-right:auto;" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${chat_info.avatar}`'  class="reciever-img"/><span  class="fs-6 cursor-pointer"><RouterLink :to='`/user/${chat_info.reciever_email}`'>{{chat_info.name}}</RouterLink><small class="user-last-activity">Last Seen: {{moment(chat_info.last_activity).fromNow()}}</small></span>
     </div>
    <div class="conversation p-4" style="margin-bottom:200px;">
        <div v-for="x in conversation.message_chat">
            <div v-if="x.sender != user_mail && x.convo !=''  " class="message-recieved"><p>{{ x.convo }}</p><p class="m-4" style="cursor: pointer;" v-if="x.file != ''"><span  
                @click="showDocument(x.file,x.file_status)"><i class="fs-2 fa fa-file"></i></span></p><br /><small style="color: lightslategray;">{{moment(x.date).fromNow()}}</small></div>
            
            <div v-else-if="x.sender == user_mail && x.convo !=''" class="message-sent"><p>{{x.convo }}</p><p class="m-4" style="cursor: pointer;" v-if="x.file != ''"><span   
                @click="showDocument(x.file,x.file_status)"><i class="fs-2 fa fa-file"></i></span></p><br /><small v-if="x.isRead != 'false'" style="color:magenta;"><i class="fas fa-check-double"></i></small><small v-else style="color:grey;"><i class="fas fa-check-double"></i></small><br /><small style="color: lightslategray;">{{moment(x.date).fromNow()}}</small></div>
        </div>
    </div>
    <div id="file-preview" style="position:fixed; z-index:1;  background-color:rgba(0,0,0,0.8); width:100%; height:100%; left:0px; top:0%;"  v-if="message_data.doc_uri !=''"  class="file-preview">
        <span @click="hide_file_preview" class="cancel cursor-pointer m-2 fs-2 font-bold" style="color:white;">&times;</span>
        <div  style=" padding:0px; margin-top:0px;" class="d-flex  justify-content-center align-items-center">
        <img v-if="message_data.file_ext ==='jpg' || message_data.file_ext ==='png' || message_data.file_ext ==='gif' || message_data.file_ext ==='jfif'"  class="doc-img" :src="message_data.doc_uri" />
        <div style="color:white; display:flex; flex-direction:column; cursor: pointer; justify-content:center;  align-items:center; font-size:45px;" v-else-if="message_data.file_ext ==='pdf'"><i class="fa fa-file"></i><h4>Pdf File Added</h4></div>
        <LoadJsVideoComponent class="doc-video" v-else-if="message_data.file_ext==='mp4'" style="width:100%;" :video_info="{
                            source:message_data.doc_uri
                        }"/>
       
        <audio  class="doc-video" controls autoplay v-else-if="message_data.file_ext==='mp3' || message_data.file_ext==='wav' ">
            <source :src="message_data.doc_uri" />
        </audio>
        </div>
        <ProgressBar :progress="conversation.uploadProgress"/>
        </div>
    <div id="doc-container" class="conversation-doc">
        <div class="chat_doc_content" style="display:flex; justify-content:center; flex-direction:column; align-items:center;">
        <div style="margin-top:0px;"><span @click="hideDoc" class="fs-1 cancel  text-white font-bold">&times;</span></div>
        <div class="content_div">
        <img class="chat-image" v-if="conversation.doc_ext === 'image' "  :src='`https://res.cloudinary.com/fishfollowers/image/upload/${conversation.chat_doc}`'>
        <VideoPlayerComponent :video_info="{
                            source:conversation.chat_doc
                        }"  class="doc-video" v-else-if="conversation.doc_ext ==='video'"/>
        <audio autoplay controls v-else-if="conversation.doc_ext === 'audio'">
            <source :src='`https://res.cloudinary.com/fishfollowers/video/upload/${conversation.chat_doc}`' />
        </audio>
        <iframe v-else  style="width:100%; position:fixed; left:0px; height:100%; border:none;" :src='`https://res.cloudinary.com/fishfollowers/image/upload/${conversation.chat_doc}`'>

        </iframe>
        </div>
        </div>  
    </div>
    <div>
        <form v-if="info.isBlocked !='true'" class="message-box-holder"  @submit="sendMessage">
        <input accept="audio/*, image/*, application/pdf, video/*" style="display: none;" v-on:change="chat_document" id="file" class="m-2 form-control md" type="file" name="coverPhoto" />
        <textarea  v-model="user_message" placeholder="Type your message..." class="message-box shadow-lg"></textarea>
        <div class="send-message-box">
        <button  id="send_msg" disabled class="btn btn-success btn-lg send-msg-btn"><i class="fa fa-paper-plane"></i></button>
        <span @click="showFile"  class="btn btn-sm btn-default fs-2 font-bold show-file"><i class="fa-light fa-paperclip"></i></span>
        </div>
        </form>
        <h2 v-else class="text-center font-bold fs-5">You can no longer send messages to this user</h2>
    </div>
    <audio style="display: none;"  id="unlike">
    <source type="audio/wav" src="../notifications/unlike bell.wav" />
    </audio>
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .edit-container{
    width: 100%;
    display: flex;
    flex-direction: column;
    background-color: rgb(248, 248, 248);
    height: 100%;
    border-radius: 10px;
    margin-top: 0px;
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
.reciever-details-header{
position: relative;
padding: 0px;
width:100%;
left:0px;
background-color:white;
z-index:1;
display:flex;
flex-direction:row;
justify-content:flex-start;
position:fixed;
}
.reciever-img{
    width: 50px;
    height: 50px;
    border-radius: 50%;
}
.reciever-name{
    position: absolute;
    top: 20%;
    left: 20%;
    font-weight: bold;
}
.message-recieved{
    background-color: rgb(212, 255, 216,0.711);
    color: black;
    font-size: 13px;
    width:auto;
    border-radius:10px;
    float: left;
    clear: both;
    margin-top: 20px;
    word-wrap:break-word;
    border-top-left-radius: 5px;
    padding: 10px 10px;
    white-space:pre-wrap;

}
.message-sent{
    background-color: rgb(234, 234, 234);
    color: black;
    font-size: 13px;
    width:auto;
    border-radius:10px;
    margin-top: 20px;
    word-wrap: break-word;
    float: right;
    clear: both;
    border-top-right-radius: 5px;
    padding: 10px 10px;
    white-space:pre-wrap;
}
.conversation{
    flex:1;
    overflow-y: auto;
    padding:10px;
    margin-top:15px;
}
.message-box{
    width: 100%;
    resize: none;
    border-radius: 50px;
    background-color: rgb(250, 250, 250);
    border:none;
    
}
.message-box-holder{
    position: fixed;
    bottom:0px;
    left:0px;
    right:0px;
    margin-bottom:0px;
    box-sizing:border-box; 
    z-index:2;
}
.send-message-box{
position: relative;
z-index: 0;
}
.send-msg-btn{
    position: absolute;
    right: 5%;
    bottom: 0%;
    margin-bottom:15px;
    border-radius: 50%;
}
.conversation-doc{
    display: none;
    position: fixed;
    background-color: rgba(0, 0, 0, 0.513);
    width: 100%;
    height:100%;
    left:0px;
    color: white;
    cursor: pointer;
    z-index:1;
    top:0px;
    
}
.show-file{
    position:absolute; 
    bottom: 2%; 
    right: 35%;
}
.doc-img{
    width:100%; 
    height:100%; 
    object-fit:cover;
}
.doc-video{
    width:100%; 
    height:80vh;
}
.user-last-activity{
    margin-top: 0px;
    display:block;
    font-weight:400;
    color:grey;
}
.chat-image{
    width:100%; 
    margin:0px auto;  
    height:100%;
}
}
@media screen and (min-width:620px) {
    .edit-container{
    max-width: 100%;
    margin:0px auto;
    background-color: rgb(248, 248, 248);
    height: 100%;
    border-radius: 10px;
    margin-top: 0px;
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
.reciever-details-header{
position: relative;
padding: 0px;
width:100%;
left:0px;
background-color:white;
z-index:1;
display:flex;
flex-direction:row;
justify-content:flex-start;
position:fixed;
}
.reciever-img{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-top: 0px;
}
.reciever-name{
    position: absolute;
    top: 20%;
    left: 6%;
    font-weight: bold;
}
.message-recieved{
    background-color: rgb(212, 255, 216,0.711);
    color: black;
    font-size: 13px;
    width:200px;
    float: left;
    clear: both;
    margin-top: 20px;
    word-wrap:break-word;
    border-top-left-radius: 5px;
    padding: 10px 10px;
    white-space:pre-wrap;

}
.message-sent{
    background-color: rgb(234, 234, 234);
    color: black;
    font-size: 13px;
    width:200px;
    word-wrap: break-word;
    float: right;
    clear: both;
    margin-top: 20px;
    border-top-right-radius: 5px;
    padding: 10px 10px;
    white-space:pre-wrap;
}
.conversation{
    margin-top:20px;
}
.message-box{
    width: 100%;
    resize: none;
    margin-top: 0px;
    border-radius: 50px;
    background-color: rgb(250, 250, 250);
    border:none;
    
}
.send-message-box{
position: relative;
z-index: 0;
}
.send-msg-btn{
    position: absolute;
    right: 0%;
    bottom: 2%;
    border-radius: 50%;
}
.show-file{
    position:absolute; 
    bottom: 2%; 
    right: 15%;
}
.doc-img{
    width:300px; 
    height:300px; 
    
}
.doc-video{
    width:100%; 
    height:80vh;
}
.user-last-activity{
    margin-top: 0px;
    display:block;
    font-weight:400;
}
.chat-image{
    width:100%; 
    margin:0px auto;  
    height:80vh;
    border-radius:20px;
}
}
@media screen and (min-width:1224px) {
    .edit-container{
    max-width: 50%;
    margin:0px auto;
    background-color: rgb(248, 248, 248);
    height: 100%;
    border-radius: 10px;
    margin-top: 0px;
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
.reciever-details-header{
position: relative;
padding: 0px;
width:50%;
left:20px;
right:20px;
margin:0px auto;
background-color:white;
z-index:1;
display:flex;
flex-direction:row;
justify-content:flex-start;
position:fixed;
}
.reciever-img{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-top: 0px;
}
.reciever-name{
    position: absolute;
    top: 20%;
    left: 4%;
    font-weight: bold;
}
.message-box{
    width: 50%;
    resize: none;
    border-radius: 50px;
    background-color: rgb(250, 250, 250);
    border:none;
}
.message-box-holder{
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
}
.message-recieved{
    background-color: rgb(212, 255, 216,0.711);
    color: black;
    font-size: 13px;
    width:200px;
    float: left;
    clear: both;
    word-wrap:break-word;
    border-top-left-radius: 5px;
    padding: 10px 10px;
    white-space:pre-wrap;

}
.message-sent{
    background-color: rgb(234, 234, 234);
    color: black;
    font-size: 13px;
    width:200px;
    word-wrap: break-word;
    float: right;
    clear: both;
    border-top-right-radius: 5px;
    padding: 10px 10px;
    white-space:pre-wrap;
}
.conversation{
   margin-top:20px;
}
.send-message-box{
position: relative;
z-index:0;
width:50%;
}
.send-msg-btn{
    position: absolute;
    right: 0%;
    bottom: 20%;
    border-radius: 50%;
}
#file{
    display: none;
}
.conversation-doc{
    display: none;
    position: fixed;
    background-color: rgba(0, 0, 0, 0.513);
    width: 100%;
    height:100%;
    border-radius: 5px;
    left:0px;
    color: white;
    cursor: pointer;
    z-index:1;
    top:0px;
    
}
.show-file{
    position:absolute; 
    bottom: 2%; 
    right: 15%;
}
.doc-img{
    width:500px; 
    height:500px; 
    
}
.doc-video{
    width:100%; 
    height:80vh;
}
.user-last-activity{ 
    margin-top: 0px;
    display:block;
    font-weight:400;
}
.chat-image{
    width:100%; 
    margin:0px auto;  
    height:80vh;
    border-radius:20px;
}
}

</style>