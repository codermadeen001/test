
<template>
  <div class="container-login-signup">
    <div v-if="isLoading" class="loading-spinner"></div>
    <h1>Wardrob App</h1>
    <form @submit.prevent="handleSubmit">
      <div>
        <label>Email</label>
        <input type="email" v-model="email" required />
      </div>
      <div>
        <label>Password</label>
        <input type="password" v-model="password" required />
      </div>
      <div>
        <label>Confirm Password</label>
        <input type="password" v-model="confirmPassword" required />
      </div>
      <p v-if="errorMessage" id="errorMessage">{{ errorMessage }}</p>
      <button type="submit" :disabled="isLoading">Signup</button>
      <router-link to="/login" class="link-style">Sign up</router-link>
    </form>
  </div>
</template>

<script>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default {
  setup() {
    const router = useRouter();
    const email = ref("");
    const password = ref("");
    const confirmPassword = ref("");
    const errorMessage = ref("");
    const isLoading = ref(false);

    const handleSubmit = async () => {
      if (password.value !== confirmPassword.value) {
        errorMessage.value = "Password mismatch!";
        return;
      }

      const minLength = 8;
      const hasUpperCase = /[A-Z]/.test(password.value);
      const hasLowerCase = /[a-z]/.test(password.value);
      const hasNumbers = /\d/.test(password.value);
      const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password.value);

      if (
        password.value.length < minLength ||
        !hasUpperCase ||
        !hasLowerCase ||
        !hasNumbers ||
        !hasSpecialChar
      ) {
        errorMessage.value =
          "Password must be strong with at least one uppercase, lowercase, number, and special character.";
        return;
      }

      isLoading.value = true;
      try {
       // const response = await axios.post("http://localhost:8000/api/well", {
        const response = await axios.post("http://localhost:8000/api/wardrob/signup", {
          email: email.value,
          password: password.value,
        });

        if (response.data.success) {
          alert(response.data.message);
          email.value = "";
          password.value = "";
          confirmPassword.value = "";
          errorMessage.value = "";
          router.push("/login");
        } else {
          errorMessage.value = response.data.message || "Server error";
        }
      } catch (error) {
        errorMessage.value = "Network Error! Unable to connect to backend";
      }
      isLoading.value = false;
      setTimeout(() => {
        errorMessage.value =''
      }, 3500);
    };

    return { email, password, confirmPassword, errorMessage, isLoading, handleSubmit };
  },
};
</script>

<style scoped>
body {
  font-family: Arial, sans-serif;
  background-color: #ffffff;
  margin: 0;
  padding: 0;
}

.container-login-signup {
  position: relative;
  max-width: 350px;
  margin: 80px auto;
  padding: 20px;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  color: #3b7bbf;
  font-size: 22px;
  margin-bottom: 15px;
}

form label {
  font-size: 16px;
  color: #333;
  margin-bottom: 5px;
}

form input {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  outline: none;
  font-size: 16px;
}

form input:focus {
  border-color: #3b7bbf;
}

button {
  width: 100%;
  padding: 12px;
  background-color: #3b7bbf;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  outline: none;
  font-size: 16px;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #3481bb;
}

button:disabled {
  background-color: #b0bec5;
  cursor: not-allowed;
}

#errorMessage {
  color: red;
  font-size: 14px;
  margin-top: 10px;
}

.link-style {
  color: #3b7bbf;
  text-decoration: none;
  margin-top: 15px;
  display: inline-block;
}

.link-style:hover {
  text-decoration: underline;
}

.loading-spinner {
  position: absolute;
  top: 40%;
  left: 35%;
  transform: translate(-50%, -50%);
  border: 8px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 8px solid #3b7bbf;
  width: 80px;
  height: 80px;
  animation: spin 1s linear infinite;
  z-index: 9999;
}
.link-style{
  text-align: center;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
