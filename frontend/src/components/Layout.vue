<template>
  <div class="layout-container">
    <!-- Header -->
    <HeaderComponent
      ref="header"
      :isLoggedIn="isLoggedIn"
      :username="username"
      @open-login="openLoginForm"
      @open-register="openRegisterForm"
      @open-manage-account="openManageAccountModal"
      @logout="logout"
      @toggle-menu="toggleMenu"
    />

    <!-- Nav Menu -->
    <nav class="nav-menu" :class="{ active: isMenuOpen }" ref="navMenu">
      <ul>
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/#news-section">News</router-link></li>
        <li><router-link to="/#game-section">Game</router-link></li>
        <li v-if="isLoggedIn && userRole==='Moderator'">
          <router-link to="/moderator">Moderator</router-link>
        </li>
      </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      <router-view></router-view>
    </main>

    <!-- Footer: Pass the reporterId from computed -->
    <FooterComponent :isLoggedIn="isLoggedIn" :reporterId="reporterId" />

    <!-- Login Modal -->
    <div v-if="showLoginForm" class="modal-overlay" @click.self="closeLoginForm">
      <div class="modal-content">
        <h2>Login</h2>
        <form @submit.prevent="login">
          <div class="form-group">
            <label for="login-identity">Username/Email:</label>
            <input id="login-identity" v-model="loginData.identity" type="text" required />
          </div>
          <div class="form-group">
            <label for="login-password">Password:</label>
            <input id="login-password" v-model="loginData.password" type="password" required />
          </div>
          <div class="form-actions">
            <button type="submit">Login</button>
            <button type="button" @click="closeLoginForm">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Register Modal -->
    <div v-if="showRegisterForm" class="modal-overlay" @click.self="closeRegisterForm">
      <div class="modal-content">
        <h2>Register</h2>
        <form @submit.prevent="register">
          <div class="form-group">
            <label for="register-username">Username:</label>
            <input id="register-username" v-model="registerData.username" type="text" required />
          </div>
          <div class="form-group">
            <label for="register-email">Email:</label>
            <input id="register-email" v-model="registerData.email" type="email" required />
          </div>
          <div class="form-group">
            <label for="register-password">Password:</label>
            <input id="register-password" v-model="registerData.password" type="password" required />
          </div>
          <div class="form-group" v-if="isModeratorEmail">
            <label for="register-moderator-key">Moderator Key (optional):</label>
            <input id="register-moderator-key" v-model="registerData.moderator_key" type="text" />
          </div>
          <div class="form-actions">
            <button type="submit">Register</button>
            <button type="button" @click="closeRegisterForm">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Manage Account Modal -->
    <div v-if="showManageAccountModal" class="modal-overlay" @click.self="closeManageAccountModal">
      <div class="modal-content">
        <h2>Manage Account</h2>
        <form @submit.prevent="updateAccount">
          <div class="form-group">
            <label for="account-username">New Username:</label>
            <input id="account-username" v-model="manageAccountData.username" type="text" required />
          </div>
          <div class="form-group">
            <label for="account-old-password">Current Password (required for changes):</label>
            <input id="account-old-password" v-model="manageAccountData.oldPassword" type="password" required />
          </div>
          <div class="form-actions">
            <button type="submit">Update Account</button>
            <button type="button" @click="closeManageAccountModal">Cancel</button>
          </div>
        </form>
        <div class="form-actions" style="margin-top: 1rem;">
          <button type="button" @click="openUpdatePasswordModal" style="background-color: #ff8800;">
            Change Password
          </button>
          <button type="button" @click="openDeleteAccountModal" style="background-color: #cc0000;">
            Delete Account
          </button>
        </div>
      </div>
    </div>

    <!-- Update Password Modal -->
    <div v-if="showUpdatePasswordModal" class="modal-overlay" @click.self="closeUpdatePasswordModal">
      <div class="modal-content">
        <h2>Update Password</h2>
        <form @submit.prevent="updatePassword">
          <div class="form-group">
            <label for="old-password">Current Password:</label>
            <input id="old-password" v-model="updatePasswordData.oldPassword" type="password" required />
          </div>
          <div class="form-group">
            <label for="new-password">New Password:</label>
            <input id="new-password" v-model="updatePasswordData.newPassword" type="password" required />
          </div>
          <div class="form-actions">
            <button type="submit">Update Password</button>
            <button type="button" @click="closeUpdatePasswordModal">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Account Modal -->
    <div v-if="showDeleteAccountModal" class="modal-overlay" @click.self="closeDeleteAccountModal">
      <div class="modal-content">
        <h2>Confirm Account Deletion</h2>
        <p>This action cannot be undone. Please enter your current password to confirm.</p>
        <form @submit.prevent="deleteAccount">
          <div class="form-group">
            <label for="delete-password">Current Password:</label>
            <input id="delete-password" v-model="deleteAccountData.oldPassword" type="password" required />
          </div>
          <div class="form-actions">
            <button type="submit" style="background-color: #cc0000;">Delete Account</button>
            <button type="button" @click="closeDeleteAccountModal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import HeaderComponent from './Header.vue'
import FooterComponent from './Footer.vue'

export default {
  name: 'Layout',
  components: { HeaderComponent, FooterComponent },
  data() {
    return {
      isMenuOpen: false,
      isLoggedIn: false,
      username: '',
      userRole: '',
      showLoginForm: false,
      showRegisterForm: false,
      showManageAccountModal: false,
      showUpdatePasswordModal: false,
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
      manageAccountData: {
        currentUsername: '',
        username: '',
        email: '',
        oldPassword: ''
      },
      updatePasswordData: {
        currentUsername: '',
        oldPassword: '',
        newPassword: ''
      },
      deleteAccountData: {
        currentUsername: '',
        oldPassword: ''
      }
    }
  },
  computed: {
    // Returns the reporterId (logged in user's id), or 0 if not set
    reporterId() {
      return Number(localStorage.getItem('userId')) || 0;
    },
    isModeratorEmail() {
      return this.registerData.email && this.registerData.email.includes('.mod@')
    }
  },
  created() {
    const storedUsername = localStorage.getItem('username')
    const storedRole = localStorage.getItem('userRole')
    const storedEmail = localStorage.getItem('userEmail') || ''
    if (storedUsername && storedRole) {
      this.username = storedUsername
      this.userRole = storedRole
      this.manageAccountData.email = storedEmail
      this.isLoggedIn = true
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  },
  methods: {
    toggleMenu() {
      this.isMenuOpen = !this.isMenuOpen
    },
    openLoginForm() {
      this.showLoginForm = true
    },
    closeLoginForm() {
      this.showLoginForm = false
      this.loginData.identity = ''
      this.loginData.password = ''
    },
    openRegisterForm() {
      this.showRegisterForm = true
    },
    closeRegisterForm() {
      this.showRegisterForm = false
      this.registerData.username = ''
      this.registerData.email = ''
      this.registerData.password = ''
      this.registerData.moderator_key = ''
    },
    async login() {
      try {
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.loginData)
        })
        const data = await response.json()
        if (data.success) {
          this.isLoggedIn = true
          this.username = data.username
          this.userRole = data.role
          localStorage.setItem('userId', data.userId)  // Store the reporterId
          localStorage.setItem('username', data.username)
          localStorage.setItem('userRole', data.role)
          if (data.email) {
            localStorage.setItem('userEmail', data.email)
            this.manageAccountData.email = data.email
          }
          this.closeLoginForm()
        } else {
          alert('Login failed: ' + data.error)
        }
      } catch (error) {
        console.error('Login error:', error)
      }
    },
    async register() {
      try {
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/register.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.registerData)
        })
        const data = await response.json()
        if (data.success) {
          this.isLoggedIn = true
          this.username = this.registerData.username
          this.userRole = data.role
          localStorage.setItem('userId', data.userId)  // Ensure your API returns userId upon registration
          localStorage.setItem('username', this.registerData.username)
          localStorage.setItem('userRole', data.role)
          localStorage.setItem('userEmail', this.registerData.email)
          this.manageAccountData.email = this.registerData.email
          this.closeRegisterForm()
        } else {
          alert('Registration failed: ' + data.error)
        }
      } catch (error) {
        console.error('Registration error:', error)
      }
    },
    openManageAccountModal() {
      this.manageAccountData.currentUsername = this.username
      this.manageAccountData.username = this.username
      this.manageAccountData.oldPassword = ''
      this.showManageAccountModal = true
    },
    closeManageAccountModal() {
      this.showManageAccountModal = false
      this.manageAccountData = { currentUsername: '', username: '', email: this.manageAccountData.email, oldPassword: '' }
    },
    async updateAccount() {
      try {
        const payload = {
          currentUsername: this.manageAccountData.currentUsername,
          username: this.manageAccountData.username,
          oldPassword: this.manageAccountData.oldPassword
        }
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/updateAccount.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        })
        const data = await response.json()
        if (data.success) {
          alert('Account updated successfully.')
          this.username = this.manageAccountData.username
          localStorage.setItem('username', this.manageAccountData.username)
          this.closeManageAccountModal()
        } else {
          alert('Update failed: ' + data.error)
        }
      } catch (error) {
        console.error('Update account error:', error)
      }
    },
    openUpdatePasswordModal() {
      this.updatePasswordData.currentUsername = this.username
      this.updatePasswordData.oldPassword = ''
      this.updatePasswordData.newPassword = ''
      this.showUpdatePasswordModal = true
    },
    closeUpdatePasswordModal() {
      this.showUpdatePasswordModal = false
      this.updatePasswordData = { currentUsername: '', oldPassword: '', newPassword: '' }
    },
    async updatePassword() {
      try {
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/updatePassword.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.updatePasswordData)
        })
        const data = await response.json()
        if (data.success) {
          alert('Password updated successfully.')
          this.closeUpdatePasswordModal()
        } else {
          alert('Password update failed: ' + data.error)
        }
      } catch (error) {
        console.error('Update password error:', error)
      }
    },
    openDeleteAccountModal() {
      this.deleteAccountData.currentUsername = this.username
      this.deleteAccountData.oldPassword = ''
      this.showDeleteAccountModal = true
    },
    closeDeleteAccountModal() {
      this.showDeleteAccountModal = false
      this.deleteAccountData = { currentUsername: '', oldPassword: '' }
    },
    async deleteAccount() {
      try {
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/deleteAccount.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.deleteAccountData)
        })
        const data = await response.json()
        if (data.success) {
          alert('Account deleted successfully.')
          this.logout()
          this.closeDeleteAccountModal()
        } else {
          alert('Deletion failed: ' + data.error)
        }
      } catch (error) {
        console.error('Delete account error:', error)
      }
    },
    logout() {
      this.isLoggedIn = false
      this.username = ''
      this.userRole = ''
      localStorage.removeItem('userId')
      localStorage.removeItem('username')
      localStorage.removeItem('userRole')
      localStorage.removeItem('userEmail')
    },
    handleClickOutside(e) {
      const navMenu = this.$refs.navMenu
      const header = this.$refs.header ? this.$refs.header.$el : null
      if (this.isMenuOpen && navMenu && !navMenu.contains(e.target) && header && !header.contains(e.target)) {
        this.isMenuOpen = false
      }
    }
  }
}
</script>

<style scoped>
.layout-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background: #121212;
}

/* Nav Menu */
.nav-menu {
  position: fixed;
  top: 0;
  right: 0;
  width: 250px;
  height: 100vh;
  background: #1e1e1e;
  padding: 2rem;
  transform: translateX(100%);
  transition: transform 0.3s ease;
  z-index: 100;
}
.nav-menu.active {
  transform: translateX(0);
}
.nav-menu ul {
  list-style: none;
  padding: 0;
}
.nav-menu li {
  margin-bottom: 1rem;
}
.nav-menu a {
  color: #fff;
  text-decoration: none;
}

/* Main content */
.main-content {
  flex: 1;
  padding: 2rem;
  margin-top: 1rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 200;
}
.modal-content {
  background: #121212;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  color: #e0e0e0;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}
.modal-content h2 {
  margin-top: 0;
  margin-bottom: 1rem;
  color: #ff9a00;
}
.form-group {
  margin-bottom: 1rem;
}
.form-group label {
  display: block;
  margin-bottom: 0.5rem;
}
.form-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #333;
  border-radius: 4px;
  background: #1e1e1e;
  color: #fff;
  box-sizing: border-box;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}
.form-actions button {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  background: #ff9a00;
  color: #fff;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.form-actions button:hover {
  background: #ff8800;
}
</style>