import axios from "axios";

class Base {
    constructor() {
        this.client = axios.create({
            baseURL: '/api'
        })
    }

    get(path) {
        return this.client.get(path).then(response => {
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

export default Base