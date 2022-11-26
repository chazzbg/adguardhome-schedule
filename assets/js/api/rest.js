import Base from "./base";

export default class Rest extends Base {
    constructor(object) {
        super();
        this.object = object
    }

    async index() {
        return await this.get(`/${this.object}`);
    }

    async save(data) {
        return await this.post(`/${this.object}`, data)
    }

    async show(id, params) {
        return await this.get(`/${this.object}/${id}`, params)
    }

    async update(id, data) {
        return await this.post(`/${this.object}/${id}`, data)
    }

    async delete(id) {
        return this.client.delete(`/${this.object}/${id}`)
    }

}