<script setup>
import { useRouter } from "vue-router";
import Footer from "../component/Footer.vue";
import Header from "../component/Header.vue";
import { reactive, onMounted } from "vue";

const router = useRouter();
let user_mail = localStorage.getItem('USER_MAIL');

const randomPic = [
  {
    img_url: new URL('@/assets/spidey.jfif', import.meta.url).href,
    img_name: "A Picture of Spider Man",
  },
  {
    img_url: new URL('@/assets/milk.jfif', import.meta.url).href,
    img_name: "A Picture of Milk",
  },
  {
    img_url: new URL('@/assets/cat_new.jpg', import.meta.url).href,
    img_name: "A Picture of a Computer Geek",
  },
  {
    img_url: new URL('@/assets/404cat.jfif', import.meta.url).href,
    img_name: "A Picture of a Programming Cat",
  },
];

// Reactive object to store the selected image and title
let change_image = reactive({
  picture: "",
  title: "",
});

// Randomize the image and title when the component mounts
onMounted(() => {
  const random_seed = Math.floor(Math.random() * randomPic.length);
  const img = randomPic[random_seed];
  
  // Update reactive object
  change_image.picture = img.img_url;
  change_image.title = img.img_name;
  
  // Update document title
  document.title = "Not Found";
});
</script>

<template>
  <main id="main">
    <Header class="shadow-sm" />

    <div class="container error_page_container">
      <h1 class="fs-1 font-bold">Page Not Found, Nevermind</h1>
      
      <!-- Display the random picture -->
      <img
        id="random_picture"
        :src="change_image.picture"
        :alt="change_image.title"
        class="random-img"
      />
      
      <!-- Display the random title -->
      <h2 class="fs-2 font-bold">{{ change_image.title }}</h2>
      <p class="font-semibold"><RouterLink to="/">Go to Home</RouterLink></p>
    </div>

    <Footer style="position: fixed; bottom:0px;" v-if="user_mail === null" id="footer" />
  </main>
</template>

<style scoped>
/* Styles remain the same */
#random_picture {
  width: 200px;
  height: 200px;
  border-radius: 20px;
  object-fit: cover;
}
.error_page_container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  row-gap: 10px;
}
</style>
