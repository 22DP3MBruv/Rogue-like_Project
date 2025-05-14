<template>
  <div>
    <!-- News Section -->
    <section id="news-section" class="news-section">
      <h2>Latest News</h2>
      <div class="news-controls">
        <input type="text" v-model="newsSearch" placeholder="Search news..." />
        <select v-model="newsSortOrder">
          <option value="desc">Newest First</option>
          <option value="asc">Oldest First</option>
        </select>
        <button @click="changePage(1)">Search</button>
      </div>
      <div class="news-container">
        <div 
          v-for="news in newsPosts" 
          :key="news.articleId" 
          class="news-post"
          @click="toggleExpand(news.articleId)">
          <div class="news-card" :class="{ expanded: isExpanded(news.articleId) }">
            <div class="news-card-header">
              <div>
                <h3 class="news-title">{{ news.title }}</h3>
                <small class="news-author">by {{ news.author }}</small>
              </div>
              <small class="news-date">{{ news.publicationDate }}</small>
            </div>
            <div class="news-card-body">
              <p class="news-content">
                {{ isExpanded(news.articleId) ? news.content : news.content.substring(0, 150) + (news.content.length > 150 ? '...' : '') }}
              </p>
            </div>
          </div>
        </div>
      </div>
      <!-- Pagination Control -->
      <div class="pagination">
        <button 
          v-for="page in totalPages" 
          :key="page" 
          @click="changePage(page)" 
          :class="{ active: currentPage === page }">
          {{ page }}
        </button>
      </div>
    </section>

    <!-- Game Section -->
    <section id="game-section" class="game-section">
      <div class="game-container">
        <h2>Game</h2>
        <iframe
        src="http://localhost/Rogue-like_Project\frontend\public\export\roguelikeSource.html"
        width="960"
        height="540"
        frameborder="0">
      </iframe>
      </div>  
    </section>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      newsPosts: [],
      newsSearch: '',
      newsSortOrder: 'desc',
      expandedCards: {},  // holds expansion state by articleId
      currentPage: 1,
      totalPages: 1  
    }
  },
  methods: {
    async fetchNews() {
      try {
        const params = new URLSearchParams({
          q: this.newsSearch,
          order: this.newsSortOrder,
          limit: 10,              // limit of 10 news articles per page
          page: this.currentPage  // current page number
        });
        const response = await fetch(`http://localhost/Rogue-like_Project/backend/api/news.php?${params.toString()}`);
        const data = await response.json();
        if (data.success) {
          this.newsPosts = data.articles;
          this.totalPages = data.totalPages;
          // Reset expansion when new news are fetched
          this.expandedCards = {};
        } else {
          console.error('Error fetching news:', data.error);
        }
      } catch (error) {
        console.error('Fetch news error:', error);
      }
    },
    toggleExpand(articleId) {
      this.expandedCards[articleId] = !this.expandedCards[articleId];
    },
    isExpanded(articleId) {
      return !!this.expandedCards[articleId];
    },
    changePage(page) {
      this.currentPage = page;
      this.fetchNews();
    }
  },
  mounted() {
    this.fetchNews();
  }
}
</script>

<style scoped>
.news-section,
.game-section {
  margin-bottom: 1rem;
}

.game-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.news-controls {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
}
.news-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* News Thumbnail Styles */
.news-post {
  cursor: pointer;
  transition: all 0.3s ease;
}
.news-card {
  border: 1px solid #444;
  border-radius: 8px;
  overflow: hidden;
  background: #181818;
  transition: max-height 0.3s ease-in-out;
  will-change: max-height;
  max-height: 80px; /* collapsed thumbnail height */
}
.news-card.expanded {
  max-height: 300px; /* expanded height */
}
.news-card-header {
  padding: 0.5rem 1rem;
  background: #ff9a00;
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.news-card-body {
  padding: 0.5rem 1rem;
  color: #ccc;
  transition: max-height 0.3s ease-in-out;
  will-change: max-height;
}
.news-card.expanded .news-card-body {
  max-height: 240px; /* fixed height for scrolling */
  overflow-y: auto;
  overflow-x: hidden;   /* Prevent horizontal scroll */
  word-break: break-word;  /* Break long words */
}
.news-title {
  margin: 0;
  font-size: 1.2rem;
}
.news-author {
  font-size: 0.9rem;
  color: #ccc;
}
.news-date {
  font-size: 0.9rem;
}
.news-content {
  margin: 0;
  line-height: 1.4;
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
</style>