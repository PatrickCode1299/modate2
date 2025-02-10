<script setup>
import UserPostSettings from "../component/UserPostSettings.vue";
import moment from 'moment';
let x=defineProps(['post_header']);
function reduceNameLength(name){
    if(name.length > 14){
        let reduced_name=name.slice(0,14) + "..";
        return reduced_name;
    }else{
        return name;
    }
}
</script>
<template>

<div  style="background-color: rgba(255, 255, 255, 0.634);" class='card-header inline-flex  panel-header'>
<span class="d-flex" style="margin-right: auto; ">
<img v-if="x.post_header.profile_picture === null" loading="lazy" src="../pictures/profile.png" class='img-circle small-thumbnail'>
<img v-else loading="lazy" :src="`https://res.cloudinary.com/fishfollowers/image/upload/${x.post_header.profile_picture}`" class='img-circle small-thumbnail'>
<span class='m-2'>{{reduceNameLength(x.post_header.name)}}
<ul class='inline-flex' style="display:block;">
<li style="font-size:12px; color:grey;" class='list-unstyled'>{{moment(x.post_header.created_at).fromNow()}}</li>
</ul>
</span></span>
<UserPostSettings :post_id="x.post_header.postid"  class="m-2 cursor-pointer"  />
</div>

</template>
<style scoped>
@media screen and (min-width:320px) {
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}

}
@media screen and (min-width:620px) {
   
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}

}
@media screen and (min-width:1224px) {
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.img-circle{
    border-radius: 50px;
}

}

</style>