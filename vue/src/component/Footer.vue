<script setup>
const footeritems = [
  {
    header: "Join Hexarex",
    firstinfo: new Date().toLocaleString(),
    url: "signup"
  },
  // All other sections are gone, but their links will be listed inline below
];

const quickLinks = [
  "About", "Careers", "Press", "Ad Choices",
  "Privacy", "Cookies", "Terms", "Community Guidelines",
  "Support", "Security", "Safety Tips",
  "Blog", "Tech Blog", "Facebook", "Instagram"
];
</script>

<template>
  <footer class="app-footer">
    <div class="footer-center">
      <!-- Main heading -->
      <h2 class="bold-text">
        <RouterLink :to="'/signup'">Join Hexarex</RouterLink>
      </h2>

      <!-- Inline links & info -->
      <div class="footer-inline">
        <span class="small-text">{{ footeritems[0].firstinfo }}</span>
        <span v-for="(item, index) in quickLinks" :key="index" class="small-text">
          • {{ item }}
        </span>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.app-footer {
  width: 100%;
  background-color: #171717;
  color: white;
  padding: 20px 0;
  display: flex;
  justify-content: center;
}

.footer-center {
  text-align: center;
}

.bold-text {
  font-size: 15px;
  text-transform: uppercase;
  font-weight: bold;
  margin-bottom: 8px;
}

.bold-text a {
  color: #fff;
  text-decoration: none;
}

.bold-text a:hover {
  color: #0d6efd;
}

.footer-inline {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 8px;
  font-size: 12px;
  opacity: 0.9;
}

.footer-inline span:hover {
  opacity: 1;
  cursor: pointer;
}

/* On Mobile - Still centered, but stack more neatly */
@media (max-width: 600px) {
  .footer-inline {
    gap: 5px;
    font-size: 11.5px;
  }
}
</style>
