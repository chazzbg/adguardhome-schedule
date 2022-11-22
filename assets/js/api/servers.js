import Base from "./base";

export default class Servers extends Base {
    async index() {
        return await this.get('/server');
    }

    async save(data){
        return await this.post('/server', data)
    }

    async show(id, full = false) {
        let params = {
            params: {}
        };

        if( full) {
            params.params['full'] = 1
        }
        return await this.get(`/server/${id}`, params)
    }
    async update(id, data){
        return await this.post(`/server/${id}`, data)
    }

    async delete(id){
        return this.client.delete(`/server/${id}`)
    }

}