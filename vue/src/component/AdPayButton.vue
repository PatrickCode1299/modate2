<template>
  <div class="ad-setup-info">
    <div class="card shadow-md p-2 m-2 card-default">
      <h5 class="font-bold">Audience</h5>
      <div style="border:none; background-color:lightgray;" class="card p-2">
        <h6 class="font-bold">Audience Details</h6>
        <small>Location: Nigeria</small>
        <small>Target Age Range</small>
        <div class="d-flex">
      <input placeholder="18" type="number" v-model="minimum_target_age" min="18" max="65" />
      <input disabled placeholder="65" type="number" v-model="maximum_target_age" min="65" max="65" />
    </div>
      </div>
    </div>
    <div class="card shadow-md p-2 m-2 card-default">
      <h5 class="font-bold">Duration</h5>
      <div style="border:none; background-color:lightslategray;" class="card">
        <p class="p-2">Duration: <strong>{{ store_current_ad_info.day_count }} days</strong></p>
      </div>
      <div class="ad-duration">
        <div class="slider-container">
          <h2>Estimated {{ store_current_ad_info.minReach }} - {{ store_current_ad_info.maxReach }} accounts reached per day</h2>
          <div class="budget-display">
            <span>$</span>
            <input
              type="number"
              v-model="budget"
              step="0.01"
              class="budget-input"
              :min="0.2"
            />
          </div>
          <input
            type="range"
            v-model="budget"
            :min="0.2"
            :max="100"
            :step="0.01"
            class="slider"
          />
        </div>
        <div class="d-flex justify-center align-items-center">
          <paystack
            buttonClass="'button-class btn btn-primary'"
            buttonText="Pay Online"
            :publicKey="publicKey"
            :email="this.store_current_ad_info.payer_email"
            :amount="this.store_current_ad_info.ad_amount"
            :reference="transactionReference"
            :onSuccess="onSuccessfulPayment"
            :onCancel="onCancelledPayment"
          ></paystack>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, onMounted, onUpdated } from 'vue';
import paystack from "vue3-paystack";
import { nanoid } from "nanoid"; 
import axiosClient from '../axios';
import { min } from 'lodash';
// if using nanoid

export default {
  components: {
    paystack,
  },
  props:{
    postid:String,
  },
  data() {
    const budget = ref(1); // Default budget
    const current_rate = 1496.00;
    const current_user_email = localStorage.getItem('USER_MAIL');
    const transactionReference = ref(""); // Store the transaction reference
    const first_name=localStorage.getItem("FIRSTNAME");
    const last_name=localStorage.getItem("LASTNAME");
    let formData=new FormData();
    // Reactive store for ad info
    const store_current_ad_info = reactive({
      ad_duration: computed(() => Math.min(Math.floor(budget.value * 5), 365)),
      ad_amount: computed(() => budget.value * current_rate * 100), // Computed based on budget
      minReach: computed(() => Math.floor(budget.value * 10)),
      maxReach: computed(() => Math.floor(budget.value * 50)),
      day_count: computed(() => Math.min(Math.floor(budget.value * 5), 365)),
      payer_email: current_user_email,
    });

    // Watcher to enforce minimum budget
    watch(budget, (newVal) => {
      if (newVal < 0.2) budget.value = 0.2; // Ensure min budget is 0.2
    });
    watch(transactionReference,()=>{
      transactionReference.value=nanoid(15);
    });
    // Generate reference once when component is mounted
   

    return {
      budget,
      minimum_target_age:18,
      maximum_target_age:65,
      store_current_ad_info,
      formData,
      post_id:this.postid,
      ad_duration:store_current_ad_info.day_count,
      target_location:'Nigeria',
     // publicKey: 'pk_live_955f2af234dd685e18de8779b4e7d9eba6b65953',
      publicKey:'pk_test_6b40038aef38a37fc71d010a07528fbe86576d7e',
      email: current_user_email,
      firstname:first_name,
      lastname:last_name,
      transactionReference, // Binding the reference to the component's data
    };
  },
  methods: {
    onSuccessfulPayment(response) {
  console.log("Payment successful!");// Add this
  this.formData.append("email", this.email);
  this.formData.append("maximum_target_age", this.maximum_target_age);
  this.formData.append("minimum_target_age", this.minimum_target_age);
  this.formData.append("postid", this.post_id);
  this.formData.append("ad_duration", this.store_current_ad_info.day_count);
  this.formData.append("target_location", this.target_location);
  this.formData.append("amount_paid", this.store_current_ad_info.ad_amount);
  axiosClient.post("/createAdvertisement", this.formData)
    .then(response => {
      console.log("Ad created:", response);
    })
    .catch(err => {
      console.error("Error creating ad:", err);
    });
},
    onCancelledPayment() {
      console.log('Payment cancelled by user');
    },
  },
};
</script>
<style scoped>
.slider-container {
  max-width: 500px;
  margin: auto;
  text-align: center;
  font-family: Arial, sans-serif;
}

h2 {
  margin-bottom: 10px;
}

.budget-display {
  margin-bottom: 20px;
  font-size: 24px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.budget-input {
  width: 80px;
  margin-left: 5px;
  font-size: 18px;
  text-align: right;
}

.slider {
  width: 100%;
}
.channel-container{
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    width: 50%;
    margin:0px auto;
    margin-top:40px;
}
.channel-info{
    margin: 0px auto; 
    margin-top:50px;
    width: 80%;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
}
.channel-info-holder{
    margin-top:40px;
}
.fetch-user-post-container{
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center; 
    width: 50%; 
    margin: 0px auto;
}
.channel-img{
    border-radius: 50%;
    width: 150px;
    height: 150px;
    object-fit: cover;
}
.create_text{
    resize: none;
    color: black;
    font-weight: 400;
}
.display-content{
    padding-top: 0px;
    padding-bottom: 20px;
    border-radius: 10px;
    margin-top:0px;
    background-color: rgba(255,255,255,0.8);
}
.border-20px{
    border-radius: 20px;
    padding-left: 10px;
    padding-right: 10px;
}
.inline-flex{
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.small-thumbnail{
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.user-post{
    width: 100%;
   
}
.story-preview-img{
object-fit: cover;
object-position: center center;
border-radius: 5px;
}
.card{
    border: none;
    width:100%;
}
.text-grey{
    color: rgb(158, 156, 156);
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
.channel_photo_gallery {
  display: none;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px; /* Adjust the gap between the grid items as needed */
}

.channel_photo_gallery img {
  width: 100%; /* Make sure images take up the full width of the grid cell */
  height: auto; /* Maintain the aspect ratio of the images */
}
.channel_photo_gallery > div > img{
   object-fit: cover;
   
}
.spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid #f3f3f3; /* Light grey */
  border-top: 8px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 2s linear infinite;
}
.ad-preview-container{
   flex-wrap:wrap;
   justify-content:center;
   align-items:center;
   width:80%;
   margin-top:50px;
}
.ad-setup-info{
    flex:2;
    margin-right:50px;

}
.ad-post-preview{
    flex:1;
    height:400px;
    margin-top:0px;
}
</style>