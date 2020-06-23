/**
 * Import modules
 */
import axios from "axios";

const allAvatars = (ctx => {
    let keys = ctx.keys();
    return keys.map(ctx);
})(require.context('./images/avatars', true, /.*/));

export function randomArray(arr) {
    const index = Math.round(Math.random() * (arr.length - 1));
    return arr[index];
}

export function randomAvatar() {
    return randomArray(allAvatars);
}


let axiosInstance = axios.create({
    baseURL: "http://localhost:8888/api/",
    responseType: "json"
});

export const API = axiosInstance;