import { RouterLink } from 'vue-router';
<template>
    <div class="min-h-screen bg-gray-100">
      <header class="bg-white shadow p-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-indigo-600"><RouterLink to="/home"><img src="../landing/hexarex.png" class='logo' style="height:30px; margin-left:20px; width:30px;"></RouterLink></h1>
        <nav class="space-x-4">
          <a href="#features" class="text-gray-600 hover:text-indigo-600">Features</a>
          <a href="#ads" class="text-gray-600 hover:text-indigo-600">Ads</a>
          <a href="#contact" class="text-gray-600 hover:text-indigo-600">Contact</a>
        </nav>
      </header>
  
      <section class="bg-red-600 text-white text-center py-20 px-4">
        <h2 class="text-4xl font-bold mb-4">Connect. Match. Monetize.</h2>
        <p class="text-lg max-w-xl mx-auto">Hexarex is your new favorite social platform to meet real people, build communities, and earn through your influence and content.</p>
      </section>
  
      <section id="features" class="py-16 px-6 bg-white">
  <h3 class="text-3xl font-bold text-center text-gray-800 mb-12">Features</h3>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    
    <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition text-center">
      <i class="fas fa-user-friends text-5xl text-red-600 mb-4"></i>
      <h3 class="text-xl font-semibold mb-2">User Matching</h3>
      <p class="text-gray-600">Find people by interests, location, or life goals with our smart algorithm.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition text-center">
      <i class="fas fa-heart text-5xl text-red-600 mb-4"></i>
      <h3 class="text-xl font-semibold mb-2">Likes, Comments & Shares</h3>
      <p class="text-gray-600">Engage with content and help it reach more people with ease.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition text-center">
      <i class="fas fa-bell text-5xl text-red-600 mb-4"></i>
      <h3 class="text-xl font-semibold mb-2">Instant Notifications</h3>
      <p class="text-gray-600">Never miss a moment with fast, reliable push notifications.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition text-center">
      <i class="fas fa-bullhorn text-5xl text-red-600 mb-4"></i>
      <h3 class="text-xl font-semibold mb-2">User Advertising</h3>
      <p class="text-gray-600">Let users create and promote their own ads to their followers.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-lg transition text-center">
      <i class="fas fa-search text-5xl text-red-600 mb-4"></i>
      <h3 class="text-xl font-semibold mb-2">Smart Discovery</h3>
      <p class="text-gray-600">Get personalized feed suggestions based on user behavior and preferences.</p>
    </div>

  </div>
</section>

  
      <section id="ads" class="bg-indigo-50 py-16 px-6">
        <h3 class="text-3xl font-bold text-center text-gray-800 mb-6">Monetize & Advertise</h3>
        <p class="text-center max-w-2xl mx-auto mb-12 text-gray-600">
          Whether you're an influencer, a business, or just starting out, Hexarex lets you advertise to targeted audiences directly on your channel.
        </p>
       
      </section>
  
      <section id="contact" class="bg-white py-20 px-6">
        <h3 class="text-3xl font-bold text-center text-gray-800 mb-4">Get In Touch</h3>
        <p class="text-center text-gray-600 mb-8">Want to partner with us or learn more? We’d love to hear from you.</p>
        <form @submit.prevent="sendEmail" class="max-w-xl mx-auto grid gap-4">
            <input v-model="name" type="text" placeholder="Your Name" class="p-3 border rounded" required />
            <input v-model="email" type="email" placeholder="Email Address" class="p-3 border rounded" required />
            <textarea v-model="message" rows="4" placeholder="Message" class="p-3 border rounded" required></textarea>
            <button type="submit" class="bg-red-600 text-white py-3 px-6 rounded hover:bg-red-700">
            Send Message
            </button>
        </form>
      </section>
  
      <footer class="bg-gray-800 text-white text-center p-6">
        &copy; 2025 Hexarex. All rights reserved.
      </footer>
    </div>
  </template>
  
  <script setup>
    import { ref } from 'vue'

    const name = ref('')
    const email = ref('')
    const message = ref('')

    const sendEmail = () => {
    const recipient = "patrick@hexarex.com" // or "akintolaoluwaseun73@gmail.com"
    const subject = encodeURIComponent("Message from " + name.value)
    const body = encodeURIComponent(`Name: ${name.value}\nEmail: ${email.value}\n\nMessage:\n${message.value}`)

    const mailtoLink = `mailto:${recipient}?subject=${subject}&body=${body}`
    window.location.href = mailtoLink
    }
  </script>
  

  
  <style scoped>
  body {
    font-family: 'Inter', sans-serif;
  }
  </style>
  