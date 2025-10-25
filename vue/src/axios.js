import axios from "axios";
import store from "./store";

const axiosClient=axios.create({
   baseURL:'http://192.168.174.215:8000/api/'

})
axiosClient.interceptors.request.use(config =>{
    config.headers.Authorization = `Bearer ${store.state.user.token}`;
    config.headers['x-k'] = 'unrelated';
    return config;
})
export default axiosClient;
