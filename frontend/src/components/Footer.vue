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

    <!-- Contact Us Modal -->
    <div v-if="showContactModal" class="modal-overlay" @click.self="closeContactModal">
      <div class="modal-content">
        <h2>Contact Us</h2>
        <p><strong>Email:</strong> support@rvt.lv</p> 
        <p><strong>Phone:</strong> 11 111 111</p>
        <div class="modal-buttons">
          <button type="button" @click="closeContactModal">Close</button>
        </div>
      </div>
    </div>

    <!-- Send Report Modal -->
    <div v-if="showReportModal" class="modal-overlay" @click.self="closeReportModal">
      <div class="modal-content">
        <h2>Send a Report</h2>
        <form @submit.prevent="submitReport">
          <label for="reportTitle">Report Title:</label>
          <input id="reportTitle" v-model="reportData.title" type="text" placeholder="Short description" required />
          
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
        <div class="reports-container">
          <div 
            v-for="report in myReports" 
            :key="report.reportId" 
            class="report-card"
            @click="toggleExpandReport(report.reportId)"
            :class="{ expanded: isReportExpanded(report.reportId) }">
            <div class="report-card-header">
              <strong>{{ report.title }}</strong>
              <span class="report-type">({{ report.type }})</span>
              <div class="report-status">
                Status: {{ report.status }}
              </div>
            </div>
            <div class="report-card-body">
              <p>
                {{ isReportExpanded(report.reportId)
                  ? report.content
                  : report.content.substring(0, 100) + (report.content.length > 100 ? '...' : '') }}
              </p>
              <button @click.stop="deleteReport(report.reportId)">Delete Report</button>
            </div>
          </div>
        </div>
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
    },
    reporterId: {
      // Logged in user's id
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      showAboutModal: false,
      showContactModal: false,
      showReportModal: false,
      showMyReportsModal: false,
      reportData: {
        title: '',
        type: 'GameIssue',
        content: ''
      },
      myReports: [],
      expandedReports: {}
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
      this.reportData = { title: '', type: 'GameIssue', content: '' };
    },
    async submitReport() {
      try {
        const payload = { ...this.reportData, reporterId: this.reporterId };
        const response = await fetch('/apiPHP/backend/api/report.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
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
      this.expandedReports = {};
    },
    async fetchMyReports() {
      try {
        const response = await fetch(`/apiPHP/backend/api/getReports.php?reporterId=${this.reporterId}`);
        const data = await response.json();
        if (data.success) {
          this.myReports = data.reports;
        } else {
          alert('Error fetching reports: ' + data.error);
        }
      } catch (error) {
        console.error('Fetch reports error:', error);
      }
    },
    toggleExpandReport(reportId) {
      this.expandedReports[reportId] = !this.expandedReports[reportId];
    },
    isReportExpanded(reportId) {
      return !!this.expandedReports[reportId];
    },
    async deleteReport(reportId) {
      try {
        const confirmDelete = confirm("Are you sure you want to delete this report?");
        if (!confirmDelete) return;
        const response = await fetch('/apiPHP/backend/api/deleteReport.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ reportId })
        });
        const data = await response.json();
        if (data.success) {
          alert("Report deleted successfully");
          this.fetchMyReports();
        } else {
          alert("Error deleting report: " + data.error);
        }
      } catch (error) {
        console.error("Delete report error:", error);
      }
    }
  }
}
</script>

<style scoped>
.footer {
  background: #1e1e1e;
  color: #e0e0e0;
  padding: 0.5rem;
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
.modal-content textarea,
.modal-content input {
  width: 100%;
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

/* Reports Thumbnail Styles */
.reports-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin: 1rem 0;
}
.report-card {
  border: 1px solid #444;
  border-radius: 8px;
  overflow: hidden;
  background: #181818;
  cursor: pointer;
  transition: max-height 0.3s ease;
  will-change: max-height;
  transform: translateZ(0);
  backface-visibility: hidden;
  max-height: 80px; /* collapsed thumbnail height */
}
.report-card.expanded {
  max-height: 300px; /* overall expanded height */
}
.report-card-header {
  padding: 0.5rem 1rem;
  background: #ff9a00;
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  /* Ensure title always appears at the top */
}
.report-card-body {
  padding: 0.5rem 1rem;
  color: #ccc;
  transition: max-height 0.3s ease;
  will-change: max-height;
  transform: translateZ(0);
  backface-visibility: hidden;
}
.report-card.expanded .report-card-body {
  max-height: 240px; /* fixed height for scrolling */
  overflow-y: auto;
}
.report-card-body button {
  background-color: #ff4d4d;
  border: none;
  color: #fff;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 0.5rem;
  transition: background-color 0.3s ease;
}
.report-card-body button:hover {
  background-color: #ff3333;
}
.report-status {
  font-size: 0.9rem;
  color: #fff;
  margin-left: 1rem;
}
</style>