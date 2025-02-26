<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
import Cropper from "cropperjs";
import "cropperjs/dist/cropper.css";
import { ref, reactive, watch, nextTick } from "vue";

const router = useRouter();
const image = ref('');
const imageElement = ref(null);
const cover_photo = ref('');
const cover_text = ref('');
const croppedImage = ref("");
const cropper = ref(null);
let img_model = ref({
  image_uri: "",
  cover_uri: ""
});

const showCropper = ref(false);
let user_mail = localStorage.getItem('USER_MAIL');

function editProfilePic(e) {
  e.preventDefault();
  const current_user = localStorage.getItem('USER_MAIL');
  const formData = new FormData();
  formData.append('owner', current_user);
  formData.append('profile_pic', image.value);
  axiosClient.post('/update', formData).then((response) => {
    alert(response.data.update);
    router.push({
      name: "Profile"
    });
  });
}

function addCoverPhoto(e) {
  e.preventDefault();
  cropImage();
  
}

function addCoverText(e) {
  e.preventDefault();
  const current_user = localStorage.getItem('USER_MAIL');
  const formData = new FormData();
  formData.append('email', current_user);
  formData.append('cover_text', cover_text.value);
  axiosClient.post("/updateCoverText", formData).then(response => {
    router.push({
      name: "Profile"
    });
  });
}

function user_profile_img(e) {
  const img = e.target.files[0];
  image.value = img;
  const reader = new FileReader();
  reader.onload = () => {
    img_model.value.image_uri = reader.result;
  };
  reader.readAsDataURL(img);
  document.getElementById("plus-icon").style.color = "grey";
}

function user_cover_photo(e) {
  const img = e.target.files[0];
  cover_photo.value = img;
  showCropper.value = true;

  // Destroy previous cropper instance if it exists
  if (cropper.value) {
    cropper.value.destroy();
  }

  const reader = new FileReader();
  reader.onload = () => {
    img_model.value.cover_uri = reader.result;

    nextTick(() => {
      if (imageElement.value) {
        // Create a new cropper instance
        cropper.value = new Cropper(imageElement.value, {
          aspectRatio: 1.91, // Aspect ratio for cover photo (ideal for landscape)
          viewMode: 2, // To allow full control over cropping
          ready() {
            console.log('Cropper ready');
          }
        });
      }
    });
  };

  reader.readAsDataURL(img);
}

function cropImage() {
  if (cropper.value) {

    const croppedCanvas = cropper.value.getCroppedCanvas();
    croppedCanvas.toBlob((blob) => {
     
      const current_user = localStorage.getItem('USER_MAIL');
      const formData = new FormData();
      formData.append('owner', current_user);
      formData.append('coverPhoto', blob, 'cover_photo.jpg'); 
      const croppedImageUrl = URL.createObjectURL(blob);
      img_model.value.cover_uri = croppedImageUrl;
    
      axiosClient.post('/updateCoverPhoto', formData).then((response) => {
        alert(response.data.update);
        router.push({
          name: "Profile"
        });
      }).catch(error => {
        console.error('Error uploading cover photo:', error);
      });
    }, 'image/jpeg'); 
    
    showCropper.value = false;
    cropper.value.destroy();
  }
}


watch(cover_text, () => {
  let cover_text_btn = document.getElementById("cover_text_button");
  if (cover_text.value.length > 65) {
    cover_text_btn.setAttribute("disabled", true);
  } else {
    cover_text_btn.removeAttribute("disabled");
  }
});

const goto = useRouter();
function gotoEdit() {
  goto.push({
    name: "Edit"
  });
}

function findPicture() {
  document.getElementById("profile-picture").click();
}

function findCoverPhoto() {
  document.getElementById("cover-photo").click();
}

let personal_info = reactive({
  u_incomplete: "",
});

personal_info.u_incomplete = localStorage.getItem('INCOMPLETE');

function updateProfile() {
  let formData = new FormData();
  formData.append("email", user_mail);
  axiosClient.post("/sendUpdateProfileLink", formData).then(response => {
    alert(response.data.reply);
  }).catch(error => {
    console.log(error);
  });
}
</script>

<template>
  <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
  <SideNav style="display:none;" />
  <div class="profile-header-container shadow-sm container p-4">
    <button style="width:100%;" v-if="personal_info.u_incomplete === 'true'" class="btn btn-success btn-block font-bold fs-6 edit btn-sm" @click="gotoEdit">Click Here and Update Details</button>
    <button v-else style="width:100%;" class="btn btn-default btn-success edit btn-sm font-bold" @click="updateProfile">Modify</button>
    <form style="position:relative;" @submit="editProfilePic">
      <div class="form-group">
        <label class="m-2 fs-5 form-label" for="Display Picture">Edit Profile Picture</label>
        <h2 class="text-danger fs-6 m-2">We recommend a very clear headshot that is visible...</h2>
        <img @click="findPicture" v-if="img_model.image_uri == ''" src="../pictures/profile.png" class="img-responsive preview-img" alt="preview img">
        <img @click="findPicture" v-else :src="img_model.image_uri" class="img-responsive preview-img" alt="preview img">
        <input ref="file" v-on:change="user_profile_img" id="profile-picture" class="m-2 form-control md profile-picture" type="file" name="file" />
        <span @click="findPicture" id="plus-icon" class="plus-icon"><i class="fa-solid fa-circle-plus"></i></span>
      </div>
      <button class="btn m-2 edit-profile-btn btn-block btn-md btn-success">Edit</button>
    </form>
    <form style="position:relative;" @submit="addCoverPhoto">
      <div class="form-group">
        <label class="m-2 fs-5 form-label" for="Display Picture">Add Cover Photo</label>
        <h2 class="text-danger fs-6 m-2">Ensure your cover photo is set to a Landscape size with a width of 933px and height of 150px</h2>
        <img @click="findCoverPhoto" v-if="img_model.cover_uri === ''" src="../pictures/cover_photo.jpg" class="img-responsive cover-img" alt="preview img">
        <img @click="findCoverPhoto" v-else :src="img_model.cover_uri" class="img-responsive cover-img" alt="preview img">
        <input id="cover-photo" v-on:change="user_cover_photo" class="m-2 form-control cover-photo md" type="file" name="coverPhoto" />
        <div @click="findCoverPhoto" class="cover-icon"><i class="fa-solid fa-circle-plus"></i></div>
      </div>
      <div v-if="showCropper" class="cropper-container">
        <img ref="imageElement" :src="img_model.cover_uri" alt="Crop Image" />
      </div>
      <button class="btn m-2 edit-profile-btn btn-block btn-md btn-success">Add</button>
    </form>
    <form @submit="addCoverText">
      <div class="form-group">
        <label class="m-2 fs-5 form-label" for="Display Picture">Add Cover Text</label>
        <h2 class="text-danger fs-6 m-2">Your cover text cannot be greater than 65 characters...</h2>
        <textarea required v-model="cover_text" placeholder="I am a bored introvert currently working at Hexarex..." class="form-control covertext" ></textarea>
      </div>
      <button id="cover_text_button" class="btn m-2 cover-text-btn btn-block btn-md btn-success">Add Cover Text</button>
    </form>

  </div>
</template>

<style scoped>
@media screen and (min-width:320px) {
  .profile-header-container {
    background-color: rgb(253, 253, 253);
    height: 800px;
    border-radius: 10px;
  }

  .form-label {
    font-weight: bold;
  }

  .preview-img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    object-position: center;
  }

  .plus-icon {
    position: absolute;
    bottom: 100px;
    left: 85px;
    color: white;
    font-size: 35px;
    cursor: pointer;
  }

  .profile-picture {
    display: none;
  }

  .cover-photo {
    display: none;
  }

  .cover-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    color: black;
    font-size: 35px;
    cursor: pointer;
    position: absolute;
    top: 150px;
    width: 100%;
  }
}

@media screen and (min-width:1224px) {
  .profile-header-container {
    background-color: rgb(253, 253, 253);
    width: 50%;
    margin: 0 auto;
    margin-top: 50px;
    height: auto;
    border-radius: 10px;
  }

  .cover-img {
    width: 100%;
    object-fit: cover;
    object-position: left center;
    height: 150px;
  }

  .cover-icon {
    color: black;
    font-size: 35px;
    cursor: pointer;
    width: 100%;
    position: absolute;
    top: 150px;
  }
}

.cropper-container {
  margin-top: 20px;
  max-width: 100%;
}

.cover-text-btn {
  width: 150px;
}

.covertext {
  resize: none;
}

</style>
