import {createStore} from "vuex";
import axiosClient from "../axios.js";
import { inject } from "vue";
const store = createStore({
    state:{
        user:{
            data:{},
            token:localStorage.getItem("TOKEN"),
            cookieToken:'',
            hasStatus:true

        }
    },
    getters:{},
    actions:{
        registerUser({commit}, user){
            return axiosClient.post('/register', user)
            .then(({data}) => {
                commit('setUser',data);
                return data;
            })
        },
        login({commit}, user){
        return axiosClient.post('/login', user)
        .then(({data}) => {
            commit('setUser',data);
            return data;
            
        })
        
        
        },
        cookieisSet({commit},cookie){
            commit('setUserLoginWithCookie',cookie);
        }
    },
    mutations:{
        logout:state =>{
            state.user.data = {};
            state.user.token = null;
            state.user.cookieToken=null;
            localStorage.clear();
            sessionStorage.clear();
        },
        setUser:(state, userData) =>{
            state.user.token = userData.token;
            state.user.data  = userData.user;
            localStorage.setItem('TOKEN',userData.token);
            localStorage.setItem('USER_MAIL',state.user.data);
            
        },
        setUserLoginWithCookie:(state,cookieData) =>{
            state.user.cookieToken=cookieData;
            localStorage.setItem('USER_MAIL',cookieData);
            state.user.token="user_cookie_token";
        }
    },
    modules:{}

})
export default store;