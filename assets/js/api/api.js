import Services from "./services";
import Clients from "./clients";
import Rest from "./rest";

export const api = {
    servers: new Rest('server'),
    rules: new Rest('rule'),
    services: new Services(),
    clients: new Clients()
}