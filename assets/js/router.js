import {createRouter, createWebHashHistory} from 'vue-router'
import Home from './pages/Home'

const routes = [
    {path: '/', name: 'home', component: Home},
    {path: '/server/add', name: 'server_add', component: () => import('./pages/ServerForm')},
    {path: '/server/:id(\\d+)', name: 'server_edit', component:  () => import('./pages/ServerForm')},
    {path: '/rule/add', name: 'rule_add', component:  () => import('./pages/RuleForm')},
    {path: '/rule/:id(\\d+)', name: 'rule_edit', component:  () => import('./pages/RuleForm')},
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