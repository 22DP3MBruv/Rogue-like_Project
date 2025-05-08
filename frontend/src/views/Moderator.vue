<template>
  <div class="moderator-page">
    <h1>Moderator Dashboard</h1>
    <div class="button-group">
      <button @click="openCreateNewsModal">Create News</button>
      <button @click="openEditNewsModal">Edit News</button>
      <button @click="openReportsModal">View All Reports</button>
    </div>

    <!-- Create News Modal -->
    <div v-if="showCreateNewsModal" class="modal-overlay" @click.self="closeCreateNewsModal">
      <div class="modal-content">
        <h2>Create News</h2>
        <form @submit.prevent="submitCreateNews">
          <div class="form-group">
            <label for="news-title">Title:</label>
            <input id="news-title" v-model="createNewsData.title" type="text" required />
          </div>
          <div class="form-group">
            <label for="news-content">Content:</label>
            <textarea id="news-content" v-model="createNewsData.content" rows="5" required></textarea>
          </div>
          <div class="form-actions">
            <button type="submit">Submit News</button>
            <button type="button" @click="closeCreateNewsModal">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit News Modal with Thumbnails and Pagination -->
    <div v-if="showEditNewsModal" class="modal-overlay" @click.self="closeEditNewsModal">
      <div class="modal-content edit-news-content">
        <h2>Edit News</h2>
        <div class="news-thumbnails-container">
          <div 
            v-for="news in newsArticles" 
            :key="news.articleId" 
            class="news-card edit-card"
            @click="toggleExpandNews(news.articleId)"
            :class="{ expanded: isNewsExpanded(news.articleId) }">
            <div class="news-card-header">
              <h3 class="news-title">{{ news.title }}</h3>
              <small class="news-date">{{ news.publicationDate }}</small>
            </div>
            <div class="news-card-body">
              <p class="news-content">
                {{ isNewsExpanded(news.articleId)
                    ? news.content
                    : news.content.substring(0, 100) + (news.content.length > 100 ? '...' : '') }}
              </p>
              <div class="edit-options">
                <button @click.stop="openModifyNews(news)">Modify</button>
                <button @click.stop="deleteNews(news.articleId)">Delete</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Pagination Control -->
        <div class="pagination">
          <button 
            v-for="page in totalNewsPages" 
            :key="page" 
            @click="changeNewsPage(page)" 
            :class="{ active: currentNewsPage === page }">
            {{ page }}
          </button>
        </div>
        <button @click="closeEditNewsModal">Close</button>
      </div>
    </div>

    <!-- Modify News Modal -->
    <div v-if="showModifyNewsModal" class="modal-overlay" @click.self="closeModifyNewsModal">
      <div class="modal-content">
        <h2>Modify News</h2>
        <form @submit.prevent="submitModifyNews">
          <div class="form-group">
            <label for="modify-news-title">Title:</label>
            <input id="modify-news-title" v-model="modifyNewsData.title" type="text" required />
          </div>
          <div class="form-group">
            <label for="modify-news-content">Content:</label>
            <textarea id="modify-news-content" v-model="modifyNewsData.content" rows="5" required></textarea>
          </div>
          <div class="form-actions">
            <button type="submit">Save Changes</button>
            <button type="button" @click="closeModifyNewsModal">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Reports Modal with Thumbnails and Search -->
    <div v-if="showReportsModal" class="modal-overlay" @click.self="closeReportsModal">
      <div class="modal-content">
        <h2>All User Reports</h2>
        <!-- Search Input -->
        <div class="reports-search">
          <input 
            type="text" 
            v-model="reportsSearch" 
            @input="fetchAllReports" 
            placeholder="Search reports..." />
        </div>
        <div class="reports-container">
          <div 
            v-for="report in reports" 
            :key="report.reportId" 
            class="report-card"
            @click="toggleExpandReport(report.reportId)"
            :class="{ expanded: isReportExpanded(report.reportId) }">
            <div class="report-card-header">
              <strong>{{ report.title }}</strong>
              <span class="report-type">({{ report.type }})</span>
              <div class="report-user">
                Submitted by: {{ report.reporterName }}
              </div>
            </div>
            <div class="report-card-body">
              <p>
                {{ isReportExpanded(report.reportId)
                  ? report.content
                  : report.content.substring(0,100) + (report.content.length > 100 ? '...' : '') }}
              </p>
              <button 
                v-if="report.status === 'Open'" 
                @click.stop="updateReportStatus(report.reportId)">
                Mark as Resolved
              </button>
            </div>
          </div>
        </div>
        <button @click="closeReportsModal">Close</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Moderator',
  data() {
    return {
      showCreateNewsModal: false,
      showEditNewsModal: false,
      showModifyNewsModal: false,
      showReportsModal: false,
      reports: [],
      reportsSearch: '', // for searching reports
      newsArticles: [],
      createNewsData: { title: '', content: '' },
      modifyNewsData: { articleId: null, title: '', content: '' },
      expandedReports: {},
      expandedNews: {},
      currentNewsPage: 1,
      totalNewsPages: 1
    }
  },
  methods: {
    // Create News Methods
    openCreateNewsModal() {
      this.showCreateNewsModal = true;
    },
    closeCreateNewsModal() {
      this.showCreateNewsModal = false;
      this.createNewsData = { title: '', content: '' };
    },
    async submitCreateNews() {
      try {
        const authorId = localStorage.getItem('userId');
        const payload = {
          title: this.createNewsData.title,
          content: this.createNewsData.content,
          authorId: authorId
        };
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/createNews.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await response.json();
        if (data.success) {
          alert("News created successfully");
          this.closeCreateNewsModal();
        } else {
          alert("Error creating news: " + data.error);
        }
      } catch (error) {
        console.error("Create news error:", error);
      }
    },

    // Edit News Methods with Pagination and Scrolling
    openEditNewsModal() {
      this.showEditNewsModal = true;
      this.currentNewsPage = 1;
      this.fetchNewsArticles();
    },
    closeEditNewsModal() {
      this.showEditNewsModal = false;
      this.newsArticles = [];
      this.expandedNews = {};
    },
    async fetchNewsArticles() {
      try {
        const params = new URLSearchParams({
          limit: 10,
          page: this.currentNewsPage
        });
        const response = await fetch(`http://localhost/Rogue-like_Project/backend/api/news.php?${params.toString()}`);
        const data = await response.json();
        if (data.success) {
          this.newsArticles = data.articles;
          this.totalNewsPages = data.totalPages;
          this.expandedNews = {};
        } else {
          alert("Error fetching news articles: " + data.error);
        }
      } catch (error) {
        console.error("Fetch news articles error:", error);
      }
    },
    changeNewsPage(page) {
      this.currentNewsPage = page;
      this.fetchNewsArticles();
    },
    toggleExpandNews(articleId) {
      this.expandedNews[articleId] = !this.expandedNews[articleId];
    },
    isNewsExpanded(articleId) {
      return !!this.expandedNews[articleId];
    },
    openModifyNews(news) {
      this.modifyNewsData.articleId = news.articleId;
      this.modifyNewsData.title = news.title;
      this.modifyNewsData.content = news.content;
      this.showModifyNewsModal = true;
    },
    closeModifyNewsModal() {
      this.showModifyNewsModal = false;
      this.modifyNewsData = { articleId: null, title: '', content: '' };
    },
    async submitModifyNews() {
      try {
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/updateNews.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.modifyNewsData)
        });
        const data = await response.json();
        if (data.success) {
          alert("News updated successfully");
          this.closeModifyNewsModal();
          this.fetchNewsArticles();
        } else {
          alert("Error updating news: " + data.error);
        }
      } catch (error) {
        console.error("Update news error:", error);
      }
    },
    async deleteNews(articleId) {
      try {
        const confirmDelete = confirm("Are you sure you want to delete this news article?");
        if (!confirmDelete) return;
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/deleteNews.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ articleId })
        });
        const data = await response.json();
        if (data.success) {
          alert("News deleted successfully");
          this.fetchNewsArticles();
        } else {
          alert("Error deleting news: " + data.error);
        }
      } catch (error) {
        console.error("Delete news error:", error);
      }
    },

    // Reports Methods with Scrolling and Search
    openReportsModal() {
      this.showReportsModal = true;
      this.fetchAllReports();
    },
    closeReportsModal() {
      this.showReportsModal = false;
      this.reports = [];
      this.expandedReports = {};
    },
    async fetchAllReports() {
      try {
        // Append search query if present:
        let url = 'http://localhost/Rogue-like_Project/backend/api/getReports.php?all=1';
        if (this.reportsSearch.trim() !== '') {
          const encodedQuery = encodeURIComponent(this.reportsSearch.trim());
          url += `&q=${encodedQuery}`;
        }
        const response = await fetch(url);
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
        const response = await fetch('http://localhost/Rogue-like_Project/backend/api/report.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ action: "update", reportId, status: "Resolved" })
        });
        const data = await response.json();
        if (data.success) {
          alert("Report updated successfully");
          this.fetchAllReports();
        } else {
          alert("Error updating report: " + data.error);
        }
      } catch (error) {
        console.error("Update report error:", error);
      }
    },
    toggleExpandReport(reportId) {
      this.expandedReports[reportId] = !this.expandedReports[reportId];
    },
    isReportExpanded(reportId) {
      return !!this.expandedReports[reportId];
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
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: 2rem;
  z-index: 50;
}
/* With scrolling for modal contents */
.modal-content {
  background: #121212;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  color: #e0e0e0;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  max-height: 80vh;
  overflow-y: auto;
}
.modal-content h2 {
  margin-top: 0;
  color: #ff9a00;
  font-size: 1.5rem;
  margin-bottom: 1rem;
}
.modal-content .form-group {
  margin-bottom: 1rem;
}
.modal-content label {
  display: block;
  margin-bottom: 0.5rem;
}
.modal-content input,
.modal-content textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #333;
  border-radius: 4px;
  background: #1e1e1e;
  color: #fff;
  box-sizing: border-box;
}
.modal-content .form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
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
}
.modal-content button:hover {
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
  max-height: 80px;
}
.report-card.expanded {
  max-height: 300px;
}
.report-card-header {
  padding: 0.5rem 1rem;
  background: #ff9a00;
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.report-card-body {
  padding: 0.5rem 1rem;
  color: #ccc;
  transition: max-height 0.3s ease;
}
.report-card.expanded .report-card-body {
  max-height: 240px;
  overflow-y: auto;
}
.report-card-body button {
  margin-top: 0.5rem;
  padding: 0.5rem;
  background-color: #ff9a00;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
}
.report-card-body button:hover {
  background-color: #ff8800;
}

/* News Thumbnail Styles for Edit News */
.news-thumbnails-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin: 1rem 0;
}
.news-card.edit-card {
  border: 1px solid #444;
  border-radius: 8px;
  overflow: hidden;
  background: #181818;
  transition: max-height 0.3s ease;
  max-height: 80px;
}
.news-card.edit-card.expanded {
  max-height: 300px;
}
.news-card.edit-card .news-card-header {
  padding: 0.5rem 1rem;
  background: #ff9a00;
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.news-card.edit-card .news-card-body {
  padding: 0.5rem 1rem;
  color: #ccc;
  transition: max-height 0.3s ease;
}
.news-card.edit-card.expanded .news-card-body {
  max-height: 240px;
  overflow-y: auto;
  overflow-x: hidden;
  word-break: break-word;
}
.edit-options {
  margin-top: 0.5rem;
  display: flex;
  gap: 0.5rem;
}
.edit-options button {
  padding: 0.5rem;
  background-color: #ff9a00;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color 0.3s ease;
}
.edit-options button:hover {
  background-color: #ff8800;
}

/* Pagination Control Styles */
.pagination {
  display: flex;
  justify-content: center;
  margin-top: 1rem;
  gap: 0.5rem;
}
.pagination button {
  padding: 0.5rem 0.75rem;
  border: 1px solid #444;
  background: #181818;
  color: #e0e0e0;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.3s ease;
}
.pagination button:hover {
  background: #ff9a00;
}
.pagination button.active {
  background: #ff9a00;
  color: #fff;
  font-weight: bold;
  border-color: #ff9a00;
}

/* Additional style for search input */
.reports-search {
  margin-bottom: 1rem;
}
.reports-search input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #333;
  border-radius: 4px;
  background: #1e1e1e;
  color: #fff;
}
</style>