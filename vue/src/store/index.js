import {createStore} from "vuex";
import axiosClient from "../axios.js";
const store = createStore({
    state:{
        user:{
            data:{},
            token:sessionStorage.getItem("TOKEN"),
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
    },
    mutations:{
        logout:state =>{
            state.user.data = {};
            state.user.token = null;
            sessionStorage.clear();
        },
        setUser:(state, userData) =>{
            state.user.token = userData.token;
            state.user.data  = userData.user;
            sessionStorage.setItem('TOKEN',userData.token);
            sessionStorage.setItem('USER_MAIL',state.user.data);
        }
    },
    modules:{}

})
export default store;