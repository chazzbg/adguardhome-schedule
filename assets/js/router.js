import {createRouter, createWebHashHistory} from 'vue-router'
import Home from './pages/Home'
// 2. Define some routes
// Each route should map to a component.
// We'll talk about nested routes later.
const routes = [
    {path: '/', name: 'home', component: Home},
    {
        path: '/server', name: 'server-parent', children: [
            {path: 'add', name: 'server_add'},
            {path: ':id(\\d+)', name: 'server_edit'},
        ]
    },
    {
        path: '/rule', name: 'rule-parent', children: [
            {path: 'add', name: 'rule_add'},
            {path: ':id(\\d+)', name: 'rule_edit'},
        ]
    }

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