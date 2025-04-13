import axios from "axios";
import store from "./store";

const axiosClient=axios.create({
   // baseURL:'https://api.hexarex.com/api'
   baseURL:'http://localhost:8000/api'
})
axiosClient.interceptors.request.use(config =>{
    config.headers.Authorization = `Bearer ${store.state.user.token}`;
    config.headers['x-api-key'] = 'hexarex2025$';
    return config;
})
export default axiosClient;