import Servers from "./servers";
import Rules from "./rules";
import Services from "./services";
import Clients from "./clients";

export const api = {
    servers: new Servers(),
    rules: new Rules(),
    services: new Services(),
    clients: new Clients()
}