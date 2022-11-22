import Base from "./base";

export default class Services extends Base {
    async index() {
        return await this.get('/services')
    }
}
