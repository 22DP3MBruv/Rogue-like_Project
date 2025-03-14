<template>
  <div class="app-container">
    <header class="header">
      <div class="logo">Logo</div>
      <div class="header-right">
        <div class="auth-section" v-if="!isLoggedIn">
          <button @click="showLoginForm = true">Login</button>
          <button @click="showRegisterForm = true">Register</button>
        </div>
        <div class="auth-section" v-else>
          <span>Welcome, {{ username }}</span>
          <button @click="logout">Logout</button>
        </div>
        <!-- Menu toggle is now part of the header-right group -->
        <button class="menu-toggle" @click="toggleMenu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </header>

    <nav class="nav-menu" :class="{ active: isMenuOpen }">
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
          <div class="button-group">
            <button type="submit">Login</button>
            <button type="button" @click="showLoginForm = false">Close</button>
          </div>
        </form>
      </div>

      <div class="modal" v-if="showRegisterForm">
        <form @submit.prevent="register" class="auth-form">
          <h2>Register</h2>
          <input type="text" v-model="registerData.username" placeholder="Username" required>
          <input type="email" v-model="registerData.email" placeholder="Email" required>
          <input type="password" v-model="registerData.password" placeholder="Password" required>
          <div class="button-group">
            <button type="submit">Register</button>
            <button type="button" @click="showRegisterForm = false">Close</button>
          </div>
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
           <h1>No items currently</h1>
        </div>
      </section>

      <section class="game-section">
        <h2>Game</h2>
        <div class="game-container">
          <!-- Game items will be populated here -->
          <h1>No game access</h1>
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
/* Container Layout */
.app-container {
  min-height: 100vh;
  background: #121212;
}

/* Header with dark background and orange gradients */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-sizing: border-box;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #ff9a00, #cc7000);
  color: #fff;
}

/* New container to group right-side header items */
.header-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
}

/* Auth section with spaced buttons */
.auth-section {
  display: flex;
  gap: 1rem;
}

/* Relocate hamburger menu from absolute positioning to a flex item */
.menu-toggle {
  display: flex;
  flex-direction: column;
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
}

.menu-toggle span {
  display: block;
  width: 25px;
  height: 3px;
  background: #fff;
}

/* Navigation menu */
.nav-menu {
  position: fixed;
  top: 0;
  right: -250px;
  width: 250px;
  height: 100vh;
  background: #1e1e1e;
  transition: transform 0.3s ease;
  padding: 2rem;
  z-index: 10;
}

.nav-menu.active {
  transform: translateX(-250px);
}

.nav-menu ul {
  list-style: none;
  padding: 0;
}

.nav-menu a {
  color: #fff;
  text-decoration: none;
  display: block;
  padding: 0.75rem 0;
  border-bottom: 1px solid #333;
}

/* Main content spacing */
.main-content {
  padding: 2rem;
}

/* Section styling */
.news-section,
.shop-section,
.game-section {
  margin-bottom: 2rem;
}

.news-section h2,
.shop-section h2,
.game-section h2 {
  margin-bottom: 1rem;
  color: #ff9a00;
}

/* Modal overlay */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 20;
}

/* Auth form styling */
.auth-form {
  background: #1e1e1e;
  padding: 2rem;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  min-width: 300px;
  color: #e0e0e0;
}

.auth-form h2 {
  margin: 0;
  color: #ff9a00;
}

.auth-form input {
  padding: 0.75rem;
  border: 1px solid #444;
  border-radius: 4px;
  background: #121212;
  color: #e0e0e0;
}

/* Button group for modal actions */
.button-group {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.auth-form button {
  background-color: #ff9a00;
  color: #fff;
  padding: 0.75rem 1.25rem;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.auth-form button:hover {
  background-color: #ff8800;
  transform: translateY(-2px);
}
</style>