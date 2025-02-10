<script setup>
import Header from "../component/Header.vue";
import SideNav from "../component/SideNav.vue";
import store from "../store";
import { ref } from "vue";
import axiosClient from "../axios";
import { useRouter } from "vue-router";
const user_mail=localStorage.getItem('USER_MAIL');
const countryList = {
  AF: "Afghanistan", AL: "Albania", DZ: "Algeria", AD: "Andorra", AO: "Angola", AG: "Antigua and Barbuda", AR: "Argentina", 
  AM: "Armenia", AU: "Australia", AT: "Austria", AZ: "Azerbaijan", BS: "Bahamas", BH: "Bahrain", BD: "Bangladesh", 
  BB: "Barbados", BY: "Belarus", BE: "Belgium", BZ: "Belize", BJ: "Benin", BT: "Bhutan", BO: "Bolivia", BA: "Bosnia and Herzegovina", 
  BW: "Botswana", BR: "Brazil", BN: "Brunei", BG: "Bulgaria", BF: "Burkina Faso", BI: "Burundi", CV: "Cabo Verde", KH: "Cambodia", 
  CM: "Cameroon", CA: "Canada", CF: "Central African Republic", TD: "Chad", CL: "Chile", CN: "China", CO: "Colombia", KM: "Comoros", 
  CD: "Democratic Republic of the Congo", CR: "Costa Rica", HR: "Croatia", CU: "Cuba", CY: "Cyprus", CZ: "Czechia", DK: "Denmark", 
  DJ: "Djibouti", DM: "Dominica", DO: "Dominican Republic", EC: "Ecuador", EG: "Egypt", SV: "El Salvador", GQ: "Equatorial Guinea", 
  ER: "Eritrea", EE: "Estonia", SZ: "Eswatini", ET: "Ethiopia", FJ: "Fiji", FI: "Finland", FR: "France", GA: "Gabon", GM: "Gambia", 
  GE: "Georgia", DE: "Germany", GH: "Ghana", GR: "Greece", GD: "Grenada", GT: "Guatemala", GN: "Guinea", GW: "Guinea-Bissau", 
  GY: "Guyana", HT: "Haiti", HN: "Honduras", HU: "Hungary", IS: "Iceland", IN: "India", ID: "Indonesia", IR: "Iran", IQ: "Iraq", 
  IE: "Ireland", IL: "Israel", IT: "Italy", CI: "Ivory Coast", JM: "Jamaica", JP: "Japan", JO: "Jordan", KZ: "Kazakhstan", 
  KE: "Kenya", KI: "Kiribati", KW: "Kuwait", KG: "Kyrgyzstan", LA: "Laos", LV: "Latvia", LB: "Lebanon", LS: "Lesotho", 
  LR: "Liberia", LY: "Libya", LI: "Liechtenstein", LT: "Lithuania", LU: "Luxembourg", MG: "Madagascar", MW: "Malawi", MY: "Malaysia", 
  MV: "Maldives", ML: "Mali", MT: "Malta", MH: "Marshall Islands", MR: "Mauritania", MU: "Mauritius", MX: "Mexico", 
  FM: "Micronesia", MD: "Moldova", MC: "Monaco", MN: "Mongolia", ME: "Montenegro", MA: "Morocco", MZ: "Mozambique", 
  MM: "Myanmar (Burma)", NA: "Namibia", NR: "Nauru", NP: "Nepal", NL: "Netherlands", NZ: "New Zealand", NI: "Nicaragua", 
  NE: "Niger", NG: "Nigeria", KP: "North Korea", MK: "North Macedonia", NO: "Norway", OM: "Oman", PK: "Pakistan", PW: "Palau", 
  PA: "Panama", PG: "Papua New Guinea", PY: "Paraguay", PE: "Peru", PH: "Philippines", PL: "Poland", PT: "Portugal", QA: "Qatar", 
  RO: "Romania", RU: "Russia", RW: "Rwanda", KN: "Saint Kitts and Nevis", LC: "Saint Lucia", VC: "Saint Vincent and the Grenadines", 
  WS: "Samoa", SM: "San Marino", ST: "Sao Tome and Principe", SA: "Saudi Arabia", SN: "Senegal", RS: "Serbia", SC: "Seychelles", 
  SL: "Sierra Leone", SG: "Singapore", SK: "Slovakia", SI: "Slovenia", SB: "Solomon Islands", SO: "Somalia", ZA: "South Africa", 
  KR: "South Korea", SS: "South Sudan", ES: "Spain", LK: "Sri Lanka", SD: "Sudan", SR: "Suriname", SE: "Sweden", CH: "Switzerland", 
  SY: "Syria", TW: "Taiwan", TJ: "Tajikistan", TZ: "Tanzania", TH: "Thailand", TL: "Timor-Leste", TG: "Togo", TO: "Tonga", 
  TT: "Trinidad and Tobago", TN: "Tunisia", TR: "Turkey", TM: "Turkmenistan", TV: "Tuvalu", UG: "Uganda", UA: "Ukraine", 
  AE: "United Arab Emirates", GB: "United Kingdom", US: "United States of America", UY: "Uruguay", UZ: "Uzbekistan", VU: "Vanuatu", 
  VA: "Vatican City", VE: "Venezuela", VN: "Vietnam", YE: "Yemen", ZM: "Zambia", ZW: "Zimbabwe"
};

const allReligion=[
"Christian","Muslim","Traditional","Deist","Atheist"

];
const allInterests=[
    "Technology","Sport","Education","Gaming","Engineering & Construction","Lotto","Fashion",
    "Religion","Politics","Military","Hunting","Health","Music","Comedy","Lifestyle","Internet Currency"
];
let state = ref(null);
const dropdownOpen = ref(false);


const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

const selectCountry = (code) => {
  state.value = code;
  dropdownOpen.value = false;
};
let first_name=ref('');
let last_name=ref('');
let user_gender=ref('');
let user_birthdate=ref('');
let phone=ref('');
let religion=ref('');
let school=ref('');
let interest=ref('');
let info;
const router=useRouter();
function updateProfile(e){
    console.log(user_mail);
    e.preventDefault();
   axiosClient.post("/updateProfile",{data:{
    email:user_mail,
    firstname:first_name.value,
    lastname:last_name.value,
    gender:user_gender.value,
    state:state.value,
    birthday:user_birthdate.value,
    phone:phone.value,
    school:school.value,
    religion:religion.value,
    interest:interest.value
   }}).then(response=>{
      info=false;
       localStorage.setItem('INCOMPLETE',info);
       alert(response.data.success);
        router.push({
            name:"Profile"
        });
       
   });
   
}


</script>
<template>
    <Header class="shadow-sm" style="background-color:white; padding-bottom:10px; position: fixed; width: 100%; z-index: 1; top: 0px;" />
    <SideNav style="display:none;" />
    <div class="container p-4 shadow-sm edit-container">
        <form @submit="updateProfile">
            <h2 class="text-danger fs-6 m-4 text-center">Please Fill All Details Correctly and Crosscheck Before Submitting</h2>
            <div class="form-group m-2">
                <label class="green-text-bold" for="firstname">First Name</label>
                <input v-model="first_name" type="text" placeholder="Your First Name e.g Patrick" class="form-control rounded" required>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Last Name">Last Name</label>
                <input v-model="last_name" type="text" placeholder="Your Last Name e.g Emmanuel" class="form-control rounded" required>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Orientation">Gender Orientation</label>
                <select v-model="user_gender" class="form-control" name="orientation" required>
                    <option value="Male">Straight Male</option>
                    <option value="Female">Straight Female</option>
                    <option value="Gay">Gay</option>
                    <option value="Bisexual">Bisexual</option>
                    <option value="Lesbian">Lesbian</option>
                    <option value="Non-binary">Non Binary</option>
                </select>
            </div>
            <div class="custom-select form-group m-2">
    <label class="green-text-bold" for="Location">Your Residence</label>
    <div class="select-box" @click="toggleDropdown">
      <div class="selected">
        <span v-if="state" :class="`flag-icon flag-icon-${state.toLowerCase()}`"></span>
        {{ countryList[state] || 'Select your country' }}
      </div>
      <div v-if="dropdownOpen === false" class="dropdown" id="dropdown">
        <div
          v-for="(country, code) in countryList"
          :key="code"
          @click="selectCountry(code)"
          class="dropdown-item"
        >
          <span :class="`flag-icon flag-icon-${code.toLowerCase()}`"></span> {{ country }}
        </div>
      </div>
    </div>
  </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="birthday">Your Birthday</label>
                <input v-model="user_birthdate" type="date" class="form-control" name="birthday" required>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Interest">Your Interest</label>
                <select v-model="interest" class="form-control" name="interest" required>
                    <option v-for="i in allInterests" :value=i>{{i}}</option>
                </select>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Interest">Your Religion</label>
                <select v-model="religion" class="form-control" name="religion" required>
                    <option v-for="r in allReligion" :value=r>{{r}}</option>
                </select>
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Interest">College Attended (can be empty)</label>
                <input v-model="school" type="text" placeholder="Example: University of Lagos" class="form-control rounded">
            </div>
            <div class="form-group m-2">
                <label class="green-text-bold"  for="Phone Number">Phone Number</label>
                <input v-model="phone" type="text" placeholder="Phone Number e.g +2349031227654" class="form-control rounded" required>
            </div>
           <div class="d-flex submit"><button class="btn submit-btn btn-md btn-success">Complete Profile</button></div> 
        </form>
    </div>
</template>
<style scoped>
@media screen and (min-width:320px) {
    .edit-container{
    width: 100%;
    margin-top:50px;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
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
.custom-select {
  position: relative;
  width: 100%;
}
.select-box {
  border: 1px solid #ccc;
  padding: 10px;
  cursor: pointer;
}
.selected {
  display: flex;
  align-items: center;
}
.dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  border: 1px solid #ccc;
  background-color: white;
  z-index: 1;
  overflow-y:scroll;
  height:400px;
}
.dropdown-item {
  padding: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
}
.dropdown-item:hover {
  background-color: #f0f0f0;
}
.flag-icon {
  margin-right: 8px;
}
}
@media screen and (min-width:620px) {
    .edit-container{
    width: 50%;
    margin:0px auto;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
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
.custom-select {
  position: relative;
  width: 100%;
}
.select-box {
  border: 1px solid #ccc;
  padding: 10px;
  cursor: pointer;
}
.selected {
  display: flex;
  align-items: center;
}
.dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  border: 1px solid #ccc;
  background-color: white;
  z-index: 1;
  overflow-y: scroll;
  height:400px;
}
.dropdown-item {
  padding: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
}
.dropdown-item:hover {
  background-color: #f0f0f0;
}
.flag-icon {
  margin-right: 8px;
}
}
@media screen and (min-width:1224px) {
    .edit-container{
    width: 50%;
    margin:0px auto;
    margin-top:50px;
    background-color: rgb(253, 253, 253);
    height: auto;
    border-radius: 10px;
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
.custom-select {
  position: relative;
  width:100%;
}
.select-box {
  border: 1px solid #ccc;
  padding: 10px;
  cursor: pointer;
  background-color:white;
  border-radius:5px;
}
.selected {
  display: flex;
  align-items: center;
}
.dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  border: 1px solid #ccc;
  background-color: white;
  z-index: 1;
  overflow-y:scroll;
  height:400px;
}
.dropdown-item {
  padding: 10px;
  cursor: pointer;
  display: flex;
  align-items: center;
}
.dropdown-item:hover {
  background-color: #f0f0f0;
}
.flag-icon {
  margin-right: 8px;
}
}

</style>