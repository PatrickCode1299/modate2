<script setup>
const x=4;

</script>
<template>
<div class="stories-and-div-container">
<div class="user-story">
<div v-for="i in x" class="stories-preview">
    <img src="../landing/asian.jpg" class="story-preview-img" width="150px" height="100%">
</div>
</div>
<div class="user-post">
    <form class="user-posting-form">
        <textarea class="post-text" placeholder="Feeling good write something.."></textarea>
        <button class="btn btn-sm btn-success">Post</button>
    </form>
</div>
</div>
</template>
<style scoped>
.user-story{
    display: flex;
    justify-content: center;
    align-items: center;

}
.user-post{
    width: 80%;
   
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-radius: 5px;
}
.stories-preview{
    flex: 1;
    margin-left: 5px;
    border-radius:10px;
    border:2px solid magenta;
    opacity: 0.8;
    padding: 0px;
    
}
.stories-and-div-container{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-top: 0px;

}
.stories-and-div-container > div{
    margin-top: 10px;
}
.post-text{
    border-radius: 5px;
    resize: none;
    padding: 10px 10px;
    color: rgb(17, 17, 17);
    font-weight: 400;
    width:100%;
    height:auto;
    border: none;
    outline: none;
}
.post-text:hover{
    outline: none;
  
}
</style>