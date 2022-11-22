import Base from "./base";

export default class Rules extends Base {
    async index() {
        return await this.get('/rule')
    }

    async save(data){
        return await this.post('/rule', data)
    }
}
