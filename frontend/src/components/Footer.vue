<template>
  <footer class="footer">
    <div class="footer-links">
      <button @click="openAboutModal">About Us</button>
      <button @click="openContactModal">Contact Us</button>
      <button v-if="isLoggedIn" @click="openReportModal">Send Report</button>
      <button v-if="isLoggedIn" @click="openMyReportsModal">My Reports</button>
    </div>

    <!-- About Us Modal -->
    <div v-if="showAboutModal" class="modal-overlay" @click.self="closeAboutModal">
      <div class="modal-content">
        <h2>About Us</h2>
        <p>We are a team dedicated to providing the best gaming experience. Contact us for more info.</p>
        <button @click="closeAboutModal">Close</button>
      </div>
    </div>

    <!-- Contact Us Modal (updated: no inputs, only displays email and phone) -->
    <div v-if="showContactModal" class="modal-overlay" @click.self="closeContactModal">
      <div class="modal-content">
        <h2>Contact Us</h2>
        <!-- Temporary contact details as placeholder -->
        <p><strong>Email:</strong> support@rvt.lv</p> 
        <p><strong>Phone:</strong> 11 111 111</p>
        <div class="modal-buttons">
          <button type="button" @click="closeContactModal">Close</button>
        </div>
      </div>
    </div>

    <!-- Send Report modal-->
    <div v-if="showReportModal" class="modal-overlay" @click.self="closeReportModal">
      <div class="modal-content">
        <h2>Send a Report</h2>
        <form @submit.prevent="submitReport">
          <label for="reportType">Report Type:</label>
          <select id="reportType" v-model="reportData.type">
            <option value="GameIssue">Game Issue</option>
            <option value="WebsiteIssue">Website Issue</option>
          </select>
          <label for="reportContent">Content:</label>
          <textarea id="reportContent" v-model="reportData.content" placeholder="Describe the issue..." required></textarea>
          <div class="modal-buttons">
            <button type="submit">Submit Report</button>
            <button type="button" @click="closeReportModal">Close</button>
          </div>
        </form>
      </div>
    </div>

<!-- My Reports Modal -->
    <div v-if="showMyReportsModal" class="modal-overlay" @click.self="closeMyReportsModal">
      <div class="modal-content">
        <h2>My Reports</h2>
        <ul>
          <li v-for="report in myReports" :key="report.reportId">
            <strong>{{ report.type }}</strong> - {{ report.content }} ({{ report.status }})
          </li>
        </ul>
        <button @click="closeMyReportsModal">Close</button>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'FooterComponent',
  props: {
    isLoggedIn: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      showAboutModal: false,
      showContactModal: false,
      showReportModal: false,
      showMyReportsModal: false,
      reportData: {
        type: 'GameIssue',
        content: ''
      },
      myReports: []
    }
  },
  methods: {
    openAboutModal() {
      this.showAboutModal = true;
    },
    closeAboutModal() {
      this.showAboutModal = false;
    },
    openContactModal() {
      this.showContactModal = true;
    },
    closeContactModal() {
      this.showContactModal = false;
    },
    openReportModal() {
      this.showReportModal = true;
    },
    closeReportModal() {
      this.showReportModal = false;
      this.reportData = { type: 'GameIssue', content: '' };
    },
    async submitReport() {
      try {
        const response = await fetch('/apiPHP/backend/api/report.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.reportData)
        });
        const data = await response.json();
        if (data.success) {
          alert('Report submitted successfully');
          this.closeReportModal();
        } else {
          alert('Error: ' + data.error);
        }
      } catch (error) {
        console.error('Report submission error:', error);
      }
    },
    openMyReportsModal() {
      this.showMyReportsModal = true;
      this.fetchMyReports();
    },
    closeMyReportsModal() {
      this.showMyReportsModal = false;
      this.myReports = [];
    },
    async fetchMyReports() {
      try {
        const response = await fetch('/apiPHP/backend/api/getReports.php');
        const data = await response.json();
        if (data.success) {
          this.myReports = data.reports;
        } else {
          alert('Error fetching reports: ' + data.error);
        }
      } catch (error) {
        console.error('Fetch reports error:', error);
      }
    }
  }
}
</script>

<style scoped>
.footer {
  background: #1e1e1e;
  color: #e0e0e0;
  padding: 1rem;
  text-align: center;
  position: relative;
  bottom: 0;
  width: 100%;
}
.footer-links button {
  background: none;
  border: none;
  color: #ff9a00;
  margin: 0 1rem;
  cursor: pointer;
  font-size: 1rem;
}
.footer-links button:hover {
  text-decoration: underline;
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 50;
}
.modal-content {
  background: #121212;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  color: #e0e0e0;
}
.modal-content h2 {
  margin-top: 0;
  color: #ff9a00;
}
.modal-content textarea {
  width: 100%;
  height: 100px;
  margin: 0.5rem 0;
  padding: 0.5rem;
  border: 1px solid #444;
  border-radius: 4px;
  background: #1e1e1e;
  color: #e0e0e0;
}
.modal-buttons {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}
.modal-buttons button {
  background-color: #ff9a00;
  border: none;
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.modal-buttons button:hover {
  background-color: #ff8800;
}
</style>