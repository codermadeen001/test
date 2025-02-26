<template>
  <div v-if="isLoading">
    <div class="loading-overlay"></div>
    <div class="loading-spinner"></div>
  </div>

  <div class="container-login-signup">
    
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

      <div class="text-center">
        <button type="submit" class="btn btn-success w-100" :disabled="isLoading">
          {{ isLoading ? "Submitting..." : "Login" }}
        </button>
      </div>

      <p v-if="errorMessage" class="text-danger text-center mt-3">{{ errorMessage }}</p>

      <div class="redirection-link">
        <div class="inner">
          <RouterLink to="/signup" class="link-style">Create Account</RouterLink>
          <a href="#" class="link-style" @click.prevent="forgetPassword">Forget Password?</a>
        </div>
        <div class="google-signup">
          <FaGoogle class="google" />
          <button class="button" @click="handleGoogleLogin">Google Login</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { auth, googleProvider } from "../firebase";
import { signInWithPopup } from "firebase/auth";
//import { FaGoogle } from "react-icons/fa";
import { FaGoogle } from "oh-vue-icons/icons";

export default {
  setup() {
    const router = useRouter();
    const email = ref("");
    const password = ref("");
    const errorMessage = ref("");
    const isLoading = ref(false);

    const forgetPassword = async () => {
      if (!email.value) {
        errorMessage.value = "Email Needed";
        return;
      }

      try {
        const response = await axios.post("http://localhost:8000/api/wardrob/password_reset", { email: email.value });
        alert(response.data.message);
      } catch (err) {
        errorMessage.value = "Login Error: " + err.message;
      }

      isLoading.value = false;
      setTimeout(() => (errorMessage.value = ""), 3000);
    };

    const handleGoogleLogin = async () => {
      try {
        const result = await signInWithPopup(auth, googleProvider);
        const userData = result.user;
        const data = {
          userName: userData.displayName,
          userEmail: userData.email,
          userImgUrl: userData.photoURL,
        };
        //alert(userData.displayName)

        const response = await axios.post("http://localhost:8000/api/wardrob/google_login", data);
        if (response.data.success) {
          localStorage.setItem("token", response.data.token);
          router.push(response.data.isAdmin ? "/AppAdmn" : "/panel/");
        } else {
          errorMessage.value = response.data.message;
        }
      } catch (err) {
        errorMessage.value = "Google authentication failed!";
      }
      setTimeout(() => (errorMessage.value = ""), 3000);
    };

    const handleSubmit = async () => {
      isLoading.value = true;
      try {
        const response = await axios.post("http://localhost:8000/api/wardrob/login", { email: email.value, password: password.value }, { withCredentials: true });

        if (response.data.success) {
          localStorage.setItem("token", response.data.token);
          router.push(response.data.isAdmin ? "/AppAdmn" : "/panel/");
          email.value = "";
          password.value = "";
          //alert( response.data.token)
        } else {
          errorMessage.value = response.data.message;
        }
      } catch (error) {
        errorMessage.value = "Network Error! Unable to connect to backend";
      }
      isLoading.value = false;
      setTimeout(() => (errorMessage.value = ""), 3000);
    };

    return {
      email,
      password,
      errorMessage,
      isLoading,
      forgetPassword,
      handleGoogleLogin,
      handleSubmit,
    };
  },
};
</script>

<style scoped>
body {
  font-family: Arial, sans-serif;
  background-color: #f0f0f0;
  margin: 0;
  padding: 0;
}

.container-login-signup {
  position: relative;
  max-width: 340px;
  margin: 100px auto;
  padding: 20px;
  background-color: #ffffff;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  
}

.container-login-signup h1 {
  color: #3f51b5;
  font-size: 24px;
  margin-bottom: 20px;
  text-align: center;
}

.container-login-signup form label {
  font-size: 16px;
  color: #555;
  margin-bottom: 5px;
  margin-left: 0px;
  text-align: left;
}

.container-login-signup form input {
  width: 95%;
  padding: 12px;
  margin-bottom: 20px;
  border: 1px solid #cccccc;
  border-radius: 5px;
  outline: none;
  font-size: 16px;
}

.container-login-signup form input:focus {
  border-color: #3f51b5;
}

.loading-spinner {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 8px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 8px solid #3f51b5; 
  width: 75px;
  height: 75px;
  animation: spin 1s linear infinite;
  z-index: 9999;
}

@keyframes spin {
  0% { transform: translate(-50%, -50%) rotate(0deg); }
  100% { transform: translate(-50%, -50%) rotate(360deg); }
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.7);
  z-index: 9998;
}


.btn {
  background-color: #3f51b5; 
  color: white;
  padding: 12px;
  border-radius: 5px;
  border: none;
  width: 100%;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn:hover{
  background-color: #303f9f;
}

.btn:disabled {
  background-color: #b0bec5;
  cursor: not-allowed;
}


.redirection-link {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
}

.redirection-link .inner {
  display: flex;
  justify-content: center;
  gap: 20px;
  width: 100%;
}

.redirection-link .link-style, .google-signup .button {
  color: #3f51b5;
  font-size: 14px;
  text-decoration: none;
}

.redirection-link .link-style:hover {
  text-decoration: underline;
}

.google-signup {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
}

.google-signup .google {
  color: #4285f4;
  font-size: 24px;
  margin-right: 10px;
}


.google-signup .button {
  /*background-color: #4285f4;*/
  color: #3f51b5;
  border: none;
  padding: 6px 12px;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

/**.google-signup .button:hover {
  background-color: #3367d6;
  color: white;
} 
*/
.google-signup .button:disabled {
  background-color: #b0bec5;
  cursor: not-allowed;
}


</style>




