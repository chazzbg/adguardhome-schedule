import Servers from "./servers";
import Rules from "./rules";
import Services from "./services";

export const api = {
    servers: new Servers(),
    rules: new Rules(),
    services: new Services()
}