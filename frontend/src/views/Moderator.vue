<template>
  <div class="moderator-page">
    <!-- Dashboard header for moderator functions -->
    <h1>Moderator Dashboard</h1>
    <!-- Group of action buttons for moderator tasks -->
    <div class="button-group">
      <button @click="createNews">Create News</button>
      <button @click="editNews">Edit News</button>
      <button @click="openResolveReportsModal">Resolve Reports</button>
    </div>

    <!-- Modal for resolving reports -->
    <div v-if="showResolveReportsModal" class="modal-overlay" @click.self="closeResolveReportsModal">
      <div class="modal-content">
        <h2>Pending Reports</h2>
        <ul>
          <li v-for="report in reports" :key="report.reportId">
            <p>
              <strong>Type:</strong> {{ report.type }}<br>
              <strong>Reporter:</strong> {{ report.reporterName }}<br>
              <strong>Content:</strong> {{ report.content }}<br>
              <strong>Status:</strong> {{ report.status }}<br>
              <strong>Date:</strong> {{ report.date }}
            </p>
            <button v-if="report.status === 'Open'" @click="updateReportStatus(report.reportId)">
              Mark as Resolved
            </button>
          </li>
        </ul>
        <button @click="closeResolveReportsModal">Close</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Moderator',
  data() {
    return {
      showResolveReportsModal: false,
      reports: []
    }
  },

  methods: {
    createNews() {
      alert("Create News functionality placeholder");
    },
    editNews() {
      alert("Edit News functionality placeholder");
    },
    openResolveReportsModal() {
      this.showResolveReportsModal = true;
      this.fetchPendingReports();
    },
    closeResolveReportsModal() {
      this.showResolveReportsModal = false;
      this.reports = [];
    },
    async fetchPendingReports() {
      try {
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/getReports.php?pending=1');
        const data = await response.json();
        if (data.success) {
          this.reports = data.reports;
        } else {
          alert("Error fetching reports: " + data.error);
        }
      } catch (error) {
        console.error("Fetch reports error:", error);
      }
    },
    async updateReportStatus(reportId) {
      try {
        const confirmUpdate = confirm("Mark report #" + reportId + " as resolved?");
        if (!confirmUpdate) return;
        // POST to the report endpoint to update report status
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/report.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: "update", reportId, status: "Resolved" })
        });
        const data = await response.json();
        if (data.success) {
          alert("Report updated successfully");
          this.fetchPendingReports();
        } else {
          alert("Error updating report: " + data.error);
        }
      } catch (error) {
        console.error("Update report error:", error);
      }
    }
  }
}
</script>

<style scoped>
.moderator-page {
  padding: 2rem;
  background: #1e1e1e;
  color: #e0e0e0;
}
.button-group {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}
.button-group button {
  padding: 0.75rem 1.25rem;
  background-color: #ff9a00;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
}
.button-group button:hover {
  background-color: #ff8800;
}
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 2rem;
  z-index: 50;
}
.modal-content {
  background: #121212;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  color: #e0e0e0;
  box-shadow: 0 0 10px rgba(0,0,0,0.5);
}
.modal-content h2 {
  margin-top: 0;
  color: #ff9a00;
  font-size: 1.5rem;
  margin-bottom: 1rem;
}
.modal-content ul {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 400px;
  overflow-y: auto;
}
.modal-content li {
  background: #1e1e1e;
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 5px;
  font-size: 0.95rem;
}
.modal-content li p {
  margin: 0.5rem 0;
  line-height: 1.3;
}
.modal-content button {
  padding: 0.75rem 1rem;
  background-color: #ff9a00;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 1rem;
  transition: background-color 0.3s ease;
  margin-top: 0.5rem;
}
.modal-content button:hover {
  background-color: #ff8800;
}
</style>