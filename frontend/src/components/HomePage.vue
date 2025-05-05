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

    <!-- Game Section -->
    <section id="game-section" class="game-section">
      <h2>Game</h2>
      <div class="game-container">
        <h1>No game access</h1>
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
      newsSortOrder: 'desc'
    }
  },
  methods: {
    async fetchNews() {
      try {
        const params = new URLSearchParams({
          q: this.newsSearch,
          order: this.newsSortOrder
        });
        const response = await fetch(`http://localhost/Rogue-like_Project/backend/api/news.php?${params.toString()}`);
        const data = await response.json();
        if (data.success) {
          this.newsPosts = data.articles;
        } else {
          console.error('Error fetching news:', data.error);
        }
      } catch (error) {
        console.error('Fetch news error:', error);
      }
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
</style>