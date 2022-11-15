import Base from "./base";

class Servers extends Base {
    async index() {
        return await this.get('/server');
    }
}

export default Servers