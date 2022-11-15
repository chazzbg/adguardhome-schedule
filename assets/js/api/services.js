import Base from "./base";

class Services extends Base {
    async index() {
        return await this.get('/services')
    }
}

export default Services