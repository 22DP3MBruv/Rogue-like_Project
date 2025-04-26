<template>
  <div class="app-container">
    <!-- Use HeaderComponent -->
    <HeaderComponent 
      :isLoggedIn="isLoggedIn" 
      :username="username"
      @open-login="showLoginForm = true"
      @open-register="showRegisterForm = true"
      @open-manage-account="openManageAccountOptions"
      @logout="logout"
      @toggle-menu="toggleMenu"
    />

    <!-- Backdrop for menu -->
    <div v-if="isMenuOpen" class="menu-backdrop" @click="toggleMenu"></div>

    <nav class="nav-menu" :class="{ active: isMenuOpen }">
      <ul>
        <li><router-link to="/">Home</router-link></li>
        <router-link to="/#news-section">News</router-link>
        <router-link to="/#game-section">Game</router-link>
        <li v-if="isLoggedIn && userRole === 'Moderator'">
          <router-link to="/moderator">Moderator</router-link>
        </li>
      </ul>
    </nav>

    <main class="main-content">
      <!-- Login Modal -->
      <div class="modal" v-if="showLoginForm">
        <form @submit.prevent="login" class="auth-form">
          <h2>Login</h2>
          <input type="text" v-model="loginData.identity" placeholder="Email or Username" required>
          <input type="password" v-model="loginData.password" placeholder="Password" required>
          <div class="button-group">
            <button type="submit">Login</button>
            <button type="button" @click="showLoginForm = false">Close</button>
          </div>
        </form>
      </div>

      <!-- Register Modal -->
      <div class="modal" v-if="showRegisterForm">
        <form @submit.prevent="register" class="auth-form">
          <h2>Register</h2>
          <input type="text" v-model="registerData.username" placeholder="Username" required>
          <input type="email" v-model="registerData.email" placeholder="Email" required>
          <input v-if="isModeratorEmail" type="text" v-model="registerData.moderator_key" placeholder="Moderator Key (only for .mod emails)">
          <input type="password" v-model="registerData.password" placeholder="Password" required>
          <div class="button-group">
            <button type="submit">Register</button>
            <button type="button" @click="showRegisterForm = false">Close</button>
          </div>
        </form>
      </div>

      <!-- Manage Account Options Modal -->
      <div class="modal" v-if="showManageAccount">
        <div class="auth-form">
          <h2>Manage Your Account</h2>
          <div class="button-group" style="flex-direction: column;">
            <button type="button" @click="openUpdateAccountInfo">Update Account Info</button>
            <button type="button" @click="openUpdatePassword">Update Password</button>
            <button type="button" @click="openDeleteAccountModal">Delete Account</button>
            <button type="button" @click="closeManageAccount">Cancel</button>
          </div>
        </div>
      </div>

      <!-- Update Account Info Modal -->
      <div class="modal" v-if="showUpdateAccountInfo">
        <form @submit.prevent="updateAccount" class="auth-form">
          <h2>Update Account Info</h2>
          <input type="text" v-model="accountData.username" placeholder="New Username" required>
          <input type="email" v-model="accountData.email" placeholder="New Email" required>
          <input type="password" v-model="accountData.oldPassword" placeholder="Current Password" required>
          <div class="button-group">
            <button type="submit">Update Account</button>
            <button type="button" @click="closeUpdateAccountInfo">Cancel</button>
          </div>
        </form>
      </div>

      <!-- Update Password Modal -->
      <div class="modal" v-if="showUpdatePassword">
        <form @submit.prevent="updatePassword" class="auth-form">
          <h2>Update Password</h2>
          <input type="password" v-model="passwordData.oldPassword" placeholder="Current Password" required>
          <input type="password" v-model="passwordData.newPassword" placeholder="New Password" required>
          <input type="password" v-model="passwordData.confirmPassword" placeholder="Confirm New Password" required>
          <div class="button-group">
            <button type="submit">Update Password</button>
            <button type="button" @click="closeUpdatePassword">Cancel</button>
          </div>
        </form>
      </div>

      <!-- Delete Account Confirmation Modal -->
      <div class="modal" v-if="showDeleteAccountModal">
        <div class="auth-form">
          <h2>Confirm Account Deletion</h2>
          <input type="password" v-model="deleteData.oldPassword" placeholder="Current Password" required>
          <div class="button-group">
            <button type="button" @click="deleteAccountConfirmed">Delete Account</button>
            <button type="button" @click="closeDeleteAccountModal">Cancel</button>
          </div>
        </div>
      </div>

      <!-- Content Sections -->
      <section id="news-section" class="news-section">
        <h2>Latest News</h2>
        <div class="news-controls">
          <input type="text" v-model="newsSearch" placeholder="Search news..." />
          <select v-model="newsSortOrder">
            <option value="desc">Newest First</option>
            <option value="asc">Oldest First</option>
          </select>
          <button @click="fetchNews">Search</button>
        </div>
        <div class="news-container">
          <div v-for="news in newsPosts" :key="news.articleId" class="news-post">
            <h3>{{ news.title }}</h3>
            <small>{{ news.publicationDate }}</small>
            <p>{{ news.content }}</p>
          </div>
        </div>
      </section>

      <section id="game-section" class="game-section">
        <h2>Game</h2>
        <div class="game-container">
          <h1>No game access</h1>
        </div>  
      </section>
    </main>

    <FooterComponent :isLoggedIn="isLoggedIn" />
  </div>
</template>

<script>
import HeaderComponent from './components/Header.vue'
import FooterComponent from './components/Footer.vue'

export default {
  name: 'App',
  components: { HeaderComponent, FooterComponent },
  data() {
    return {
      isMenuOpen: false,
      isLoggedIn: false,
      username: '',
      userRole: '',
      showLoginForm: false,
      showRegisterForm: false,
      showManageAccount: false,
      showUpdateAccountInfo: false,
      showUpdatePassword: false,
      showDeleteAccountModal: false,
      loginData: {
        identity: '',
        password: ''
      },
      registerData: {
        username: '',
        email: '',
        password: '',
        moderator_key: ''
      },
      accountData: {
        username: '',
        email: '',
        oldPassword: ''
      },
      passwordData: {
        oldPassword: '',
        newPassword: '',
        confirmPassword: ''
      },
      deleteData: { oldPassword: '' },
      newsPosts: [],
      newsSearch: '',
      newsSortOrder: 'desc',
      reportData: { type: 'GameIssue', content: '' }
    }
  },
  computed: {
    isModeratorEmail() {
      return this.registerData.email.includes('.mod@');
    }
  },
  created() {
    // Load authentication state from localStorage.
    const storedUsername = localStorage.getItem('username');
    const storedRole = localStorage.getItem('userRole');
    if (storedUsername && storedRole) {
      this.username = storedUsername;
      this.userRole = storedRole;
      this.isLoggedIn = true;
    }
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen;
    },
    async login() {
      try {
        const response = await fetch('/apiPHP/backend/api/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.loginData)
        });
        const data = await response.json();
        if (data.success) {
          this.isLoggedIn = true;
          this.username = data.username;
          this.userRole = data.role;
          localStorage.setItem('userId', data.user_id || data.username);
          localStorage.setItem('username', data.username);
          localStorage.setItem('userRole', data.role);
          this.showLoginForm = false;
        }
      } catch (error) {
        console.error('Login error:', error);
      }
    },
    logout() {
      this.isLoggedIn = false;
      this.username = '';
      this.userRole = '';
      localStorage.removeItem('username');
      localStorage.removeItem('userRole');
    },
    async fetchNews() {
      try {
        const params = new URLSearchParams({
          q: this.newsSearch,
          order: this.newsSortOrder
        });
        const response = await fetch(`/apiPHP/backend/api/news.php?${params.toString()}`);
        const data = await response.json();
        if (data.success) {
          this.newsPosts = data.articles;
        } else {
          console.error('Error fetching news:', data.error);
        }
      } catch (error) {
        console.error('Fetch news error:', error);
      }
    },
    async updateAccount() {
      try {
        const payload = {
          currentUsername: this.username,
          username: this.accountData.username,
          email: this.accountData.email,
          oldPassword: this.accountData.oldPassword
        };
        const response = await fetch('/apiPHP/backend/api/updateAccount.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (data.success) {
          alert(`Account updated for ${this.accountData.username}`);
          this.username = this.accountData.username;
          this.showUpdateAccountInfo = false;
        } else {
          alert('Update error: ' + data.error);
        }
      } catch (error) {
        console.error('Update account error:', error);
      }
    },
    async updatePassword() {
      if (this.passwordData.newPassword !== this.passwordData.confirmPassword) {
        alert("New passwords do not match!");
        return;
      }
      try {
        const payload = {
          currentUsername: this.username,
          oldPassword: this.passwordData.oldPassword,
          newPassword: this.passwordData.newPassword
        };
        const response = await fetch('/apiPHP/backend/api/updatePassword.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (data.success) {
          alert("Password updated successfully!");
          this.showUpdatePassword = false;
          this.passwordData.oldPassword = '';
          this.passwordData.newPassword = '';
          this.passwordData.confirmPassword = '';
        } else {
          alert("Error: " + data.error);
        }
      } catch (error) {
        console.error("Update password error:", error);
      }
    },
    async deleteAccountConfirmed() {
      try {
        const payload = {
          currentUsername: this.username,
          oldPassword: this.deleteData.oldPassword
        };
        const response = await fetch('/apiPHP/backend/api/deleteAccount.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (data.success) {
          alert("Account deleted successfully.");
          this.logout();
          this.closeDeleteAccountModal();
        } else {
          alert("Deletion error: " + data.error);
        }
      } catch (error) {
        console.error('Delete account error:', error);
      }
    },
    openManageAccountOptions() { this.showManageAccount = true; },
    closeManageAccount() { this.showManageAccount = false;},
    openUpdateAccountInfo() {
      this.showManageAccount = false;
      this.accountData.oldPassword = '';
      this.showUpdateAccountInfo = true;
    },
    closeUpdateAccountInfo() { this.showUpdateAccountInfo = false; },
    openUpdatePassword() {
      this.showManageAccount = false;
      this.passwordData.oldPassword = '';
      this.passwordData.newPassword = '';
      this.passwordData.confirmPassword = '';
      this.showUpdatePassword = true;
    },
    closeUpdatePassword() { this.showUpdatePassword = false; },
    openDeleteAccountModal() {
      this.showManageAccount = false;
      this.deleteData.oldPassword = '';
      this.showDeleteAccountModal = true;
    },
    closeDeleteAccountModal() { this.showDeleteAccountModal = false; }
  },
  mounted() {
    this.fetchNews();
  }
}
</script>

<style scoped>
/* Container Layout */
.app-container {
  min-height: 100vh;
  background: #121212;
  position: relative;
}

/* Navigation menu */
.nav-menu {
  position: fixed;
  top: 0;
  right: 0;
  width: 250px;
  height: 100vh;
  background: #1e1e1e;
  transition: transform 0.3s ease;
  padding: 2rem;
  z-index: 10;
  transform: translateX(100%);
}
.nav-menu.active {
  transform: translateX(0);
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

/* Backdrop */
.menu-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background: transparent;
  z-index: 5;
}

/* Main content spacing */
.main-content {
  padding: 2rem;
  margin-top: 1rem;
}
.news-section,
.game-section {
  margin-bottom: 2rem;
}
.news-section h2,
.game-section h2 {
  margin-bottom: 1rem;
  color: #ff9a00;
}

/* News controls */
.news-controls {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}
.news-controls input,
.news-controls select,
.news-controls button {
  padding: 0 1rem;
  border: 1px solid #444;
  border-radius: 5px;
  background: #1e1e1e;
  color: #e0e0e0;
  font-size: 1rem;
  height: 2.75rem;
  line-height: 2.75rem;
  box-sizing: border-box;
}
.news-controls input:focus,
.news-controls select:focus {
  outline: none;
  border-color: #ff9a00;
}
.news-controls button {
  background-color: #ff9a00;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.news-controls button:hover {
  background-color: #ff8800;
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
.auth-form button.delete {
  background-color: #cc0000;
}
.auth-form button.delete:hover {
  background-color: #a30000;
}

/* News post styling */
.news-post {
  background: #1e1e1e;
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 4px;
}
</style>