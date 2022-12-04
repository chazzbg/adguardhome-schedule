import axios from "axios";

export default class Base {
    constructor() {
        this.client = axios.create({
            baseURL: '/api'
        })
    }

    get(path, params) {
        return this.client.get(path, {params}).then(response => {
            return response.data
        }).catch(error => {
            throw error
        });
    }

    post(path, data) {
        return this.client.post(path, data).then(response => {
            return response.data
        }).catch((e) => {
            throw  e
        })
    }
}
