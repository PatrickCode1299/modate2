<script setup>
import { RouterLink, useRoute } from 'vue-router';
import {ref} from "vue";
import { useRouter } from 'vue-router';
const router=useRouter();
const route=useRoute();
const channel_details=defineProps([
    "channel_info", "post_data"
]);
let subscriber_amount=channel_details.channel_info.subscriber_count;
let number = subscriber_amount.match(/\d+/)[0];
const number_of_subscribers=ref();
number_of_subscribers.value=number;
const post_data=channel_details.post_data;
const post_id=channel_details.channel_info.postid;

</script>
<template>
 <RouterLink :to="{
      name: 'Advertise',
      params: { post_id: channel_info.postid },
      query: {
        post_data: encodeURIComponent(JSON.stringify(post_data)),
      },
    }" v-if="number_of_subscribers > 0 && !route.params.uid"  class="btn btn-md font-bold fs-5 btn-primary">Promote</RouterLink>
</template>
<style scoped>


</style>
