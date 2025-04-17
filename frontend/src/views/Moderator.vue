<template>
  <div class="moderator-page">
    <!-- Dashboard header for moderator functions -->
    <h1>Moderator Dashboard</h1>
    <!-- Group of action buttons for moderator tasks -->
    <div class="button-group">
      <button @click="createNews">Create News</button>
      <button @click="editNews">Edit News</button>
      <button @click="resolveReports">Resolve Reports</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Moderator',
  // Lifecycle hook to restrict access to moderators
  created() {
    // Retrieve the stored user role (this example uses localStorage)
    const userRole = localStorage.getItem('userRole');
    // If the user is not a moderator, redirect to the homepage
    if (userRole !== 'Moderator') {
      this.$router.push('/');
    }
  },
  methods: {
    // Placeholder method to launch news creation functionality
    createNews() {
      alert("Create News functionality placeholder");
    },
    // Placeholder method to launch news editing interface
    editNews() {
      alert("Edit News functionality placeholder");
    },
    // Placeholder method to launch report resolution interface
    resolveReports() {
      alert("Resolve Reports functionality placeholder");
    }
  }
}
</script>

<style scoped>
/* Styling for the moderator page container */
.moderator-page {
  padding: 2rem;
  background: #1e1e1e;
  color: #e0e0e0;
}

/* Styling for the button group container */
.button-group {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

/* Consistent styling for action buttons */
.button-group button {
  background-color: #ff9a00;
  color: #fff;
  padding: 0.75rem 1.25rem;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

/* Hover effect for buttons */
.button-group button:hover {
  background-color: #ff8800;
}
</style>