import Base from "./base";

export default class Traces extends Base {
    async index (page) {
        let params = {};
        if (page){
            params.page = page
        }

        return await this.get('/trace', params)
    }
    async total () {
        return await this.get('/trace/total')
    }
}