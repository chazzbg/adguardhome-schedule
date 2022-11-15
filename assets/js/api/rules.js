import Base from "./base";

class Rules extends Base {
    async index() {
        return await this.get('/rule')
    }
}

export default Rules