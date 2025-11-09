<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Project Management System</title>
<style>
  @import url('https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap');

  * {
    box-sizing: border-box;
  }
  body {
  margin: 0;
  font-family: 'Figtree', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
  color: white;
  min-height: 100vh;

  /* Gradasi 2 warna */
  background: linear-gradient(270deg, #ac7bffff, #77b6ffff);
  background-size: 200% 200%;
  
  /* Animasi geser */
  animation: gradientShift 3s ease infinite;
  }

  @keyframes gradientShift {
    0% {
      background-position: 0% 50%;
    }
    50% {
      background-position: 100% 50%;
    }
    100% {
      background-position: 0% 50%;
    }
  }

  a {
    text-decoration: none;
    color: inherit;
  }
  button {
    font-family: antialiased;
    cursor: pointer;
  }

  .container {
    max-width: 1150px;
    margin: 0 auto;
    padding: 40px 24px 80px 24px;
  }

  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 24px;
  }

  /* Logo only, no nav */
  .logo {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 20px;
  }
  .logo svg {
    width: 25px;
    height: 25px;
    fill: white;
  }
  .btn-register {
    background: transparent;
    border-radius: 30px;
    padding: 10px 28px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    color: white;
    box-shadow: 0 8px 26px rgba(108, 104, 255, 0.7);
    transition: background 0.3s ease;
  }
  .btn-register:hover {
    background: white;
    color: #77b6ffff;
    box-shadow: 0 8px 33px rgba(108, 104, 255, 1);
  }

  .button-container {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    }

  .btn-start {
    background: white;
    border-radius: 30px;
    padding: 10px 68px;
    font-weight: 600;
    font-size: 18px;
    border: none;
    box-shadow: 0 8px 26px rgba(108, 104, 255, 0.7);
    transition: background 0.3s ease;
    margin-bottom: 45px;
    text-decoration: none;
    display: inline-block;
  }

  .btn-start:hover {
      background: transparent;
      box-shadow: 0 8px 33px rgba(108, 104, 255, 1);
      color: white; 
  }

  .gradient-text {
    background: linear-gradient(90deg, #9c62ff 0%, #5ca9ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;    
  }

  main h1 {
    font-weight: 800;
    font-size: 64px;
    line-height: 1.15;
    max-width: 700px;
    margin: 24px auto 8px auto;
    text-align: center;
    letter-spacing: -0.015em;
  }
  main p {
    font-weight: 400;
    font-size: 16px;
    max-width: 620px;
    margin: 0 auto 36px auto;
    text-align: center;
    opacity: 0.85;
    line-height: 1.5;
  }

  .features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 32px;
    padding: 0 8px;
  }

  .card {
    background: rgba(255 255 255 / 0.12);
    border-radius: 18px;
    padding-bottom: 2px;
    padding: 28px 26px;
    padding-bottom: 0px;
    box-shadow: 0 14px 42px rgba(92, 169, 255, 0.2);
    color: white;
    user-select: none;
    display: flex;
    flex-direction: column;
    min-height: 260px;
  }

  /* Card title */
  .card-title {
    margin-top: auto;
    font-weight: 700;
    font-size: 20px;
    /* margin-top: 8px; */
    margin-bottom: 8px;
    color: white;
    text-align: center;
  }

  .card3-title {
    margin-top: auto;
    font-weight: 700;
    font-size: 20px;
    margin-bottom: 8px;
    color: white;
    text-align: center;
  }
  .card-desc {
    font-weight: 400;
    font-size: 14px;
    opacity: 0.7;
    line-height: 1.4;
  }

  /* -- Specific card contents -- */

  /* 1. Manajemen Proyek */
  .project-tasks {
    list-style: none;
    padding-left: 0;
    margin: 0 0 16px 0;
    flex-grow: 0;
  }
  .project-tasks li {
    background: rgba(255 255 255 / 0.15);
    margin-bottom: 10px;
    border-radius: 10px;
    padding: 10px 14px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 20px rgba(92, 169, 255, 0.3);
  }

  /* 2. Manajemen Customer */
  .customer-list {
    flex-grow: 1;
    overflow-y: auto;
    max-height: 150px;
    margin-bottom: 18px;
  }

  /* Scrollbar container */
  .customer-list::-webkit-scrollbar {
      width: 6px; /* ketebalan scrollbar */
  }

  /* Scrollbar track (background) */
  .customer-list::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.2); 
      border-radius: 10px;
  }

  /* Scrollbar thumb (bagian yang digeser) */
  .customer-list::-webkit-scrollbar-thumb {
      background: white;
      border-radius: 10px;
  }

  /* Scrollbar thumb saat hover */
  .customer-list::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, #5ca9ff, #9c62ff);
  }

  .customer-item {
    display: flex;
    gap: 14px;
    align-items: center;
    margin-bottom: 12px;
  }
  .customer-item img {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 0 8px rgba(93, 166, 255, 0.7);
  }
  .customer-item .info {
    flex-grow: 1;
    font-weight: 600;
    font-size: 14px;
  }

  /* 3. To Do List */
  .todo-list {
    list-style: none;
    padding-left: 0;
    margin: 0 0 16px 0;
    flex-grow: 0;
  }
  .todo-list li {
    background: rgba(255 255 255 / 0.12);
    margin-bottom: 10px;
    border-radius: 10px;
    padding: 10px 14px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 4px 20px rgba(92, 169, 255, 0.3);
  }
  .todo-list li.checked {
    text-decoration: line-through;
    opacity: 0.5;
  }
  .todo-checkbox {
    cursor: pointer;
    width: 18px;
    height: 18px;
    border-radius: 4px;
    border: 2px solid #8aa7ff;
    display: inline-block;
    position: relative;
  }
  .todo-checkbox.checked {
    background: #6274f9;
    border-color: #6274f9;
  }
  .todo-checkbox.checked::after {
    content: "✔";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -55%);
    color: white;
    font-size: 14px;
  }


  /* 4. Role */
  .role-badge {
    display: inline-block;
    font-weight: 700;
    font-size: 13px;
    padding: 17px 30px;
    border-radius: 50px;
    background: linear-gradient(90deg, #6274f9, #9c62ff);
    user-select: none;
    text-align: center;
    box-shadow: 0 6px 22px rgba(108, 104, 255, 0.75);
    max-width: 160px;
    margin: 0 auto; 
    margin-top: 11px; 
  }
  /* Role list */
  .role-list {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 0 auto; 
    margin-top: 12px;
    margin-bottom: 38px;
  }
  .role-list span {
    background: rgba(100 100 255 / 0.3);
    border-radius: 50px;
    padding: 17px 30px;
    font-weight: 600;
    font-size: 13px;
    color: #d0d5ff;
    cursor: default;
    box-shadow: 0 0 10px rgba(156, 98, 255, 0.6);
  }

  /* 5. Dashboard (card 5) */
  .dashboard-summary {
    flex-grow: 1;
  }
  .dashboard-section {
    margin: 0 auto;
  }
  .dashboard-section h4 {
    font-weight: 700;
    font-size: 15px;
    margin-bottom: 8px;
    border-bottom: 2px solid rgba(255 255 255 / 0.3);
    padding-bottom: 4px;
  }
  .dashboard-tasks {
    list-style: none;
    padding-left: 0;
    margin: 0;
  }
  .dashboard-tasks li {
    background: rgba(255 255 255 / 0.2);
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 10px;
    font-weight: 600;
    box-shadow: 0 3px 22px rgba(104, 92, 252, 0.35);
  }
  .dashboard-info-summary {
    display: flex;
    gap: 18px;
    justify-content: space-between;
  }
  .dashboard-info-card {
    background: rgba(255 255 255 / 0.18);
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(97, 80, 255, 0.3);
    padding: 18px 14px;
    flex: 1;
    text-align: center;
  }
  .dashboard-info-card strong {
    display: block;
    font-size: 20px;
    margin-bottom: 6px;
    font-weight: 700;
    color: #dcdcff;
  }
  .dashboard-info-card span {
    font-size: 13px;
    color: rgba(255 255 255 / 0.75);
  }


  /* Style untuk chatbot mini dalam card */
.chatbot-mini {
  position: absolute;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);
  width: 260px;
  height: 180px;
  background: transparent;
  border: 1px solid #ddd;
  border-radius: 12px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  font-family: Arial, sans-serif;
  font-size: 10px;
  color: #333;
  z-index: 10;
}

.chatbot-header {
  padding: 8px 12px;
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: transparent;
  border-radius: 12px 12px 0 0;
}

.chatbot-messages {
  flex-grow: 1;
  padding: 8px 12px;
  overflow-y: auto;
  background: transparent;
}
.chatbot-messages::-webkit-scrollbar {
      width: 6px; /* ketebalan scrollbar */
  }

  /* Scrollbar track (background) */
  .chatbot-messages::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.2); 
      border-radius: 10px;
  }

  /* Scrollbar thumb (bagian yang digeser) */
  .chatbot-messages::-webkit-scrollbar-thumb {
      background: white;
      border-radius: 10px;
  }

  /* Scrollbar thumb saat hover */
  .chatbot-messages::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(180deg, #5ca9ff, #9c62ff);
  }

.message {
  max-width: 80%;
  margin-bottom: 8px;
  padding: 6px 10px;
  border-radius: 16px;
  line-height: 1.3;
  word-wrap: break-word;
}

.user-message {
  background: #2979ff;
  color: white;
  margin-left: auto;
  border-bottom-right-radius: 0;
}

.bot-message {
  background: #e3e3e3;
  color: #333;
  margin-right: auto;
  border-bottom-left-radius: 0;
}

.chatbot-input {
  display: flex;
  padding: 6px 8px;
  align-items: center;
  font-size: 10px;
}

.chatbot-input input[type="text"] {
  flex-grow: 1;
  padding: 6px 10px;
  font-size: 10px;
  border: 1px solid #ccc;
  border-radius: 18px;
  outline: none;
}

.chatbot-input input[type="text"]:focus {
  border-color: #2979ff;
}

.chatbot-input button {
  background: #2979ff;
  border: none;
  color: white;
  font-size: 14px;
  margin-left: 8px;
  padding: 6px 6px;
  border-radius: 50%;
  cursor: pointer;
  line-height: 1;
}
</style>
</head>
<body>
<div class="container">
  <header>
    <a href="#" class="logo" aria-label="Onesoft logo">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" >
        <circle cx="12" cy="12" r="5"></circle>
        <line x1="12" y1="1" x2="12" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="23"></line>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
        <line x1="1" y1="12" x2="3" y2="12"></line>
        <line x1="21" y1="12" x2="23" y2="12"></line>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
      </svg>
      Project Management
    </a>
    <a href="{{ route('register') }}" class="btn-register">Register</a>
  </header>

  <main>
    <h1>
      Project<br/>
      Management
    </h1>
    <br>
    <p>
      An all-in-one solution for planning, controlling, and evaluating your projects. Simple to use, yet powerful for professional needs    </p>

    <div class="button-container">
        @auth
        <a href="{{ url('/dashboard') }}" class="btn-start">
            <span class="gradient-text">Start Now!</span>
        </a>
        @else
        <a href="{{ url('/login') }}" class="btn-start">
            <span class="gradient-text">Start Now!</span>
        </a>
        @endauth
    </div>


    

    <section class="features" aria-label="Product Features">

      <!-- Card 1: Manajemen Proyek -->
      <article class="card" aria-labelledby="card1-title">
        <ul class="project-tasks" aria-label="Project tasks list">
          <li>Organize Every Detail with Ease</li>
          <li>Assign Team Members</li>
          <li>Attach the Required Documents</li>
        </ul>
        <h3 id="card1-title" class="card-title">Project Management</h3>
        <p class="card-desc">Handle every project with ease through our smart, integrated tools and automation.</p>
      </article>

      <!-- Card 2: Manajemen Customer -->
      <article class="card" aria-labelledby="card2-title">
        <div class="customer-list" aria-label="List of customers">
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Customer Maya" />
            <div class="info">Maya Putri</div>
          </div>
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/men/28.jpg" alt="Customer Tono" />
            <div class="info">Dito Wijaya</div>
          </div>
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Customer Sari" />
            <div class="info">Sari Dewi</div>
          </div>
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/men/68.jpg" alt="Customer Tono" />
            <div class="info">Ali Putra</div>
          </div>
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Customer Sari" />
            <div class="info">Siska Ana</div>
          </div>
        </div>
        <h3 id="card2-title" class="card-title">Customer Management</h3>
        <p class="card-desc">Enhance efficiency by keeping customer information structured and easy to manage.</p>
      </article>

      <!-- Card 3: AI Assitant -->
      <article class="card" aria-labelledby="card4-title" style="position: relative;">
        <div class="chatbot-mini">
          <div class="chatbot-header">
            AI Assistant
          </div>
          <div class="chatbot-messages">
            <div class="message user-message">Hello AI</div>
            <div class="message bot-message">Hello! How can I help you today?</div>
            <div class="message user-message">I want to ask some things</div>
            <div class="message bot-message">Okay, I'm ready. Ask away! I'll do my best to answer.</div>
          </div>
          <form class="chatbot-input" onsubmit="return false;">
            <input type="text" placeholder="Ask something..." aria-label="Chat input" />
            <button type="submit" aria-label="Send message">&#x27A4;</button>
          </form>
        </div>
        <h3 id="card-title" class="card3-title">AI Assistant</h3>
        <p class="card-desc">AI Assistant for smart chats, instant proposals, and automated feature creation.</p>
      </article>

      <!-- Card 4: To Do List -->
      <article class="card" aria-labelledby="card4-title">
        <ul class="todo-list" aria-label="To do list">
          <li><span class="todo-checkbox checked"></span> Review Project Proposal</li>
          <li><span class="todo-checkbox"></span> Update Customer Database</li>
          <li><span class="todo-checkbox"></span> Follow up with Clients</li>
        </ul>
        <h3 id="card4-title" class="card-title">To Do List</h3>
        <p class="card-desc">Plan and manage tasks smoothly so your work stays monitored and delivered on time.</p>
      </article>

      <!-- Card 5: Role -->
      <article class="card" aria-labelledby="card5-title">
        <div class="role-badge" aria-label="User main role">Manager</div>
        <div class="role-list" aria-label="Additional user roles">
          <span>Programmer</span>
          <span>Tester</span>
        </div>
        <h3 id="card5-title" class="card-title">Role</h3>
        <p class="card-desc">Flexible and secure role-based configurations for managing user access more effectively.</p>
      </article>

      <!-- Card 6: Dashboard -->
      <article class="card" aria-labelledby="card6-title">
        <div class="dashboard-summary">
          <div class="dashboard-section">
            <h4>Quick Information</h4>
            <div class="dashboard-info-summary">
              <div class="dashboard-info-card" aria-label="Total Projects">
                <strong>12</strong>
                <span>Project</span>
              </div>
              <div class="dashboard-info-card" aria-label="Pending Tasks">
                <strong>28</strong>
                <span>Tasks</span>
              </div>
              <div class="dashboard-info-card" aria-label="Team Members">
                <strong>15</strong>
                <span>Customer</span>
              </div>
            </div>
          </div>
        </div>
        <h3 id="card6-title" class="card-title">Dashboard</h3>
        <p class="card-desc">A unified dashboard for monitoring tasks and capturing essential user insights.</p>
      </article>

    </section>

  </main>
</div>
</body>
</html>