<template>
  <header class="header">
    <div class="logo">
      <router-link to="/">
        <img :src="logoImg" alt="MyApp Logo" />
      </router-link>
    </div>
    <div class="header-right">
      <div class="auth-section" v-if="!isLoggedIn">
        <button @click="$emit('open-login')">Login</button>
        <button @click="$emit('open-register')">Register</button>
      </div>
      <div class="auth-section" v-else>
        <span>Welcome, {{ username }}</span>
        <button @click="$emit('open-manage-account')">Manage Account</button>
        <button @click="$emit('logout')">Logout</button>
      </div>
      <!-- Hamburger menu toggle remains styled separately -->
      <button class="menu-toggle" @click="$emit('toggle-menu')">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>
</template>

<script>
import logoImg from '@/assets/logo.png'

export default {
  name: 'HeaderComponent',
  props: {
    isLoggedIn: { type: Boolean, default: false },
    username: { type: String, default: '' }
  },
  data() {
    return {
      logoImg
    }
  }
}
</script>

<style>
.auth-section {
  gap: 1rem;
  display: flex;
  align-items: center;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #ff9a00, #cc7000);
  color: #fff;
  margin-bottom: 1.5rem;
}
.logo {
  font-size: 1.5rem;
  font-weight: bold;
}
.logo img {
  max-height: 50px;
}

.header-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.header-right > *:not(:last-child) {
  margin-right: 1rem;
}

.menu-toggle {
  background: none;
  border: none;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.menu-toggle span {
  width: 25px;
  height: 3px;
  background: #fff;
}
</style>