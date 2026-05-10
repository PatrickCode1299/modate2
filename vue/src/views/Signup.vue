<script setup>
import { useRouter } from "vue-router";
import Footer from "../component/Footer.vue";
import Header from "../component/Header.vue";
import store from "../store";
import { ref } from "vue";

const router = useRouter();
const email = ref("");
const password = ref("");
const err = ref(null);

async function registerUser(e) {
  e.preventDefault();
  const user = { email: email.value, password: password.value };

  try {
    await store.dispatch("registerUser", user);
    router.push({ name: "Login" });
  } catch (e) {
    err.value = "Invalid registration details";
  }
}

function hiderr() {
  err.value = null;
}

document.title = "Signup | Hexarex";
</script>

<template>
  <main class="min-h-screen flex flex-col">
    <Header />

    <section
      class="flex flex-col-reverse md:flex-row flex-grow items-center justify-center p-4 bg-gradient-to-r bg-pink-100"
    >
      <!-- Left: Signup Form -->
      <div
        class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 mt-10 md:mt-0 p-8"
      >
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">
          Create Your Account
        </h2>

        <form @submit="registerUser" class="space-y-5">
          <div>
            <label
              for="email"
              class="block text-sm font-semibold text-gray-600 mb-1"
              >Email</label
            >
            <input
              v-model="email"
              type="email"
              placeholder="you@example.com"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:outline-none"
            />
          </div>

          <div>
            <label
              for="password"
              class="block text-sm font-semibold text-gray-600 mb-1"
              >Password</label
            >
            <input
              v-model="password"
              type="password"
              placeholder="Create a strong password"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:outline-none"
            />
          </div>

          <div
            v-if="err"
            class="text-center text-sm text-red-600 bg-red-50 py-2 rounded-md"
          >
            {{ err }}
            <span
              @click="hiderr"
              class="ml-2 cursor-pointer text-lg font-bold"
              >&times;</span
            >
          </div>

          <button
            type="submit"
            class="w-full bg-gradient-to-r from-pink-500 to-orange-500 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition"
          >
            Sign Up
          </button>

          <p class="text-center text-gray-500 text-sm mt-3">
            Already have an account?
            <RouterLink
              to="/login"
              class="text-pink-600 font-medium hover:underline"
              >Log in</RouterLink
            >
          </p>
          <p class="text-center text-gray-500 text-sm">
            By signing up, you agree to our
            <RouterLink
              to="/terms"
              class="text-pink-600 font-medium hover:underline"
              >Terms and Conditions</RouterLink
            >.
          </p>
        </form>
      </div>

      <!-- Right: Intro Text -->
      <!-- Right: Intro Text -->
<div
  class="text-rose-600 md:text-left px-6 md:px-12 max-w-lg mb-10 md:mb-0 hidden md:block"
>
  <h1 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight">
    Start your<br />journey today.
  </h1>
  <p class="text-lg opacity-90 mb-8">
    Join <b>Hexarex</b> — the platform built to connect, grow, and thrive
    with real people who share your energy.
  </p>
  <ul class="space-y-2 text-sm opacity-90">
    <li>✔ 100% free to start</li>
    <li>✔ Community-driven experience</li>
    <li>✔ Discover your best self</li>
  </ul>
</div>

    </section>

    <Footer />
  </main>
</template>

<style scoped>
main {
  font-family: "Poppins", "Raleway", sans-serif;
}

input::placeholder {
  color: #aaa;
}

button {
  transition: all 0.3s ease-in-out;
}

button:hover {
  transform: translateY(-2px);
}
</style>
