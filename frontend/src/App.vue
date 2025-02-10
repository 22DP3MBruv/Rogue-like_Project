<template>
    <div class="app-container">
      <header class="header">
        <div class="logo">Logo</div>
        <div class="auth-section" v-if="!isLoggedIn">
          <button @click="showLoginForm = true">Login</button>
          <button @click="showRegisterForm = true">Register</button>
        </div>
        <div class="auth-section" v-else>
          <span>Welcome, {{ username }}</span>
          <button @click="logout">Logout</button>
        </div>
        <button class="menu-toggle" @click="toggleMenu">
          <span></span><span></span><span></span>
        </button>
      </header>
  
      <nav class="nav-menu" :class="{ 'active': isMenuOpen }">
        <ul>
          <li><router-link to="/">Home</router-link></li>
          <li><router-link to="/game">Game</router-link></li>
          <li><router-link to="/shop">Shop</router-link></li>
          <li><router-link to="/news">News</router-link></li>
        </ul>
      </nav>
  
      <main class="main-content">
        <!-- Auth Forms -->
        <div class="modal" v-if="showLoginForm">
          <form @submit.prevent="login" class="auth-form">
            <h2>Login</h2>
            <input type="text" v-model="loginData.username" placeholder="Username" required>
            <input type="password" v-model="loginData.password" placeholder="Password" required>
            <button type="submit">Login</button>
            <button type="button" @click="showLoginForm = false">Close</button>
          </form>
        </div>
  
        <div class="modal" v-if="showRegisterForm">
          <form @submit.prevent="register" class="auth-form">
            <h2>Register</h2>
            <input type="text" v-model="registerData.username" placeholder="Username" required>
            <input type="email" v-model="registerData.email" placeholder="Email" required>
            <input type="password" v-model="registerData.password" placeholder="Password" required>
            <button type="submit">Register</button>
            <button type="button" @click="showRegisterForm = false">Close</button>
          </form>
        </div>
  
        <!-- Content Sections -->
        <section class="news-section">
          <h2>Latest News</h2>
          <div class="news-container">
            <!-- News items will be populated here -->
          </div>
        </section>
  
        <section class="shop-section">
          <h2>Shop</h2>
          <div class="shop-container">
            <!-- Shop items will be populated here -->
          </div>
        </section>
  
        <section class="game-section">
          <h2>Game</h2>
          <div class="game-container">
            <!-- Game items will be populated here -->
          </div>  
        </section>
      </main>
    </div>
  </template>
  
  <script>
  export default {
    name: 'App',
    data() {
      return {
        isMenuOpen: false,
        isLoggedIn: false,
        username: '',
        showLoginForm: false,
        showRegisterForm: false,
        loginData: {
          username: '',
          password: ''
        },
        registerData: {
          username: '',
          email: '',
          password: ''
        }
      }
    },
    methods: {
      toggleMenu() {
        this.isMenuOpen = !this.isMenuOpen
      },
      async login() {
        try {
          const response = await fetch('../backend/api/login.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(this.loginData)
          })
          const data = await response.json()
          if (data.success) {
            this.isLoggedIn = true
            this.username = this.loginData.username
            this.showLoginForm = false
          }
        } catch (error) {
          console.error('Login error:', error)
        }
      },
      async register() {
        try {
          const response = await fetch('../backend/api/login.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(this.registerData)
          })
          const data = await response.json()
          if (data.success) {
            this.showRegisterForm = false
          }
        } catch (error) {
          console.error('Registration error:', error)
        }
      },
      logout() {
        this.isLoggedIn = false
        this.username = ''
      }
    }
  }
  </script>
  
  <style scoped>
  .app-container {
    min-height: 100vh;
    margin: 0;
    padding: 0;
  }
  
  .header {
    display: flex;
    justify-content: space-between;
    box-sizing: border-box;
    align-items: center;
    padding: 1rem;
    background: #333;
    color: white;
  }
  
  .auth-section {
    display: flex;
    /* gap: 1rem; */
  }
  
  .auth-section button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .auth-form {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    min-width: 300px;
  }
  
  .auth-form input {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  .menu-toggle {
    display: flex;
    flex-direction: column;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
  }
  
  .menu-toggle span {
    display: block;
    width: 25px;
    height: 3px;
    background: white;
  }
  
  .nav-menu {
    position: fixed;
    top: 0;
    right: -250px;
    width: 250px;
    height: 100vh;
    background: #444;
    transition: 0.3s;
    padding: 2rem;
  }
  
  .nav-menu.active {
    right: 0;
  }
  
  .nav-menu ul {
    list-style: none;
    padding: 0;
  }
  
  .nav-menu a {
    color: white;
    text-decoration: none;
    display: block;
    padding: 0.5rem 0;
  }
  
  .main-content {
    padding: 1rem;
  }
  
  .news-section,
  .shop-section,
  .game-section {
    margin-bottom: 2rem;
  }
  </style>