import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import AccountCreation from '../components/AcountCreation.vue';
import Panel from '../components/Panel.vue';

const routes = [
  { path: '/', redirect: '/login' }, // Redirect root path to login
  { path: '/login', component: Login },
  { path: '/signup', component: AccountCreation },
  { path: '/panel', component: Panel }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
