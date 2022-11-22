import Base from "./base";

export default class Clients extends Base {
    async index() {
        return await this.get('/clients')
    }
}
