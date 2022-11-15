import {createRouter, createWebHashHistory} from 'vue-router'
import Home from './pages/Home'
import ServerForm from './pages/ServerForm'
import {h} from "vue";


function prefixRoutes(prefix, routes) {
    return routes.map(route => route.path = prefix + '/' + route.path)
}

const routes = [
    {path: '/', name: 'home', component: Home},
    {path: '/server/add', name: 'server_add', component: ServerForm},
    {path: '/server/:id(\\d+)', name: 'server_edit', ServerForm},
    {path: '/rule/add', name: 'rule_add'},
    {path: '/rule/:id(\\d+)', name: 'rule_edit'},

]

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const router = createRouter({
    // 4. Provide the history implementation to use. We are using the hash history for simplicity here.
    history: createWebHashHistory(),
    routes, // short for `routes: routes`
})


export default router