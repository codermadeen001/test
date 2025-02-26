/*import { createApp } from 'vue'
import './style.css'
import App from './App.vue'

createApp(App).mount('#app')






<template>
  <div>
    <h1>Main App</h1>
    <HelloComponent />
  </div>
</template>

<script>
import HelloComponent from "./components/HelloComponent.vue";

export default {
  name: "App",
  components: {
    HelloComponent,
  },
};
</script>

<style>
h1 {
  text-align: center;
  color: #4CAF50;
}
</style>












// Vue Language Features
//ventur --disable



*/


import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';


import 'bootstrap/dist/js/bootstrap.bundle.min.js';


const app = createApp(App);
app.use(router);
app.mount("#app");
