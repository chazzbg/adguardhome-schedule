import Base from "./base";

class Servers extends Base {
    async index() {
        return await this.get('/server');
    }

    async save(data){
        return await this.post('/server', data)
    }
}

export default Servers