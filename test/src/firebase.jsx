import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider } from "firebase/auth";

const firebaseConfig = {
  apiKey: "AIzaSyB_Iigyq2UBUiJBA7-1Z9m2GFtCMHhj26M",
  authDomain: "icents.firebaseapp.com",
  projectId: "icents",
  storageBucket: "icents.firebasestorage.app",
  messagingSenderId: "1042290287974",
  appId: "1:1042290287974:web:d3e680416891b17e6f54a7",
  measurementId: "G-WC4EE3KDFT"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const googleProvider = new GoogleAuthProvider();

export { auth, googleProvider }; 








