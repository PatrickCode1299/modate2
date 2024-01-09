<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import StoriesandPost from "../component/StoriesandPost.vue";
</script>
<template>
    <Header />
    <SideNav />
    <div class="story-and-post">
    <div class="user-match">
        <img class="user-match-img" src="../landing/blackie.jpg" />
    </div>
    <div class="user-match-info">
    <h5 class="fs-3">Akintola Patrick, 26</h5>
    <h6 class="fs-5">Around You</h6>
    <div class="d-flex next-cancel">
    <button class="btn btn-success btn-sm">Next</button>
    <button class="btn btn-danger btn-sm">Cancel</button>
    </div>
    </div>
    </div>
</template>
<style scoped>
@media screen and (min-width:400px) {
    .story-and-post{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 100%;
    margin:0px auto;
}
.user-match{
    flex: 1;
    height: 400px;

}
.user-match-img{
    width:100%;
    height: 400px;
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
    bottom: 0px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    width: 80px;
    border-radius:20px;
}
}
@media screen and (min-width:700px) {
    .story-and-post{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 50%;
    margin:0px auto;
}
.user-match{
    flex: 1;
    height: 400px;

}
.user-match-img{
    width:100%;
    height: 400px;
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
    bottom: 0px;
    justify-content: space-around;
}
.next-cancel > button{
    margin-right: 20px;
    width: 80px;
    border-radius:20px;
}
}

</style>