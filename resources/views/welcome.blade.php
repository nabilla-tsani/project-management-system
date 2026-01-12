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
    transform: scale(1.035);
    box-shadow: 0 6px 18px rgba(108, 104, 255, 0.35);
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
  /* Item checked */
  .todo-list li.checked {
    opacity: 0.6;
  }

  /* Checkbox dasar */
  .todo-checkbox {
    cursor: pointer;
    width: 18px;
    height: 18px;
    border-radius: 6px; /* lebih rounded */
    border: 2px solid rgba(138, 167, 255, 0.9);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.25s ease;
    background: rgba(255, 255, 255, 0.1);
  }

  /* Hover effect */
  .todo-checkbox:hover {
    border-color: #5ca9ff;
    box-shadow: 0 0 8px rgba(92, 169, 255, 0.6);
  }

  /* Checked state */
  .todo-checkbox.checked {
    background: linear-gradient(135deg, #5ca9ff, #9c62ff);
    border-color: transparent;
    box-shadow: 0 0 10px rgba(156, 98, 255, 0.8);
    transform: scale(1.05);
  }

  /* Check icon */
  .todo-checkbox.checked::after {
    content: "✓";
    color: white;
    font-size: 13px;
    font-weight: 700;
    line-height: 1;
  }

  /* Animasi klik */
  .todo-checkbox:active {
    transform: scale(0.9);
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
    color: #ffffffff;
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
    <a href="#" class="logo" aria-label="Logo Onesoft">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
      Manajemen Proyek
    </a>
    <a href="{{ route('register') }}" class="btn-register">Daftar</a>
  </header>

  <main>
    <h1>
      Manajemen<br/>
      Proyek
    </h1>
    <br>
    <p>
      Solusi lengkap untuk merencanakan, mengontrol, dan mengevaluasi proyek Anda.
      Mudah digunakan namun tetap andal untuk kebutuhan profesional.
    </p>

    <div class="button-container">
        @auth
        <a href="{{ url('/dashboard') }}" class="btn-start">
            <span class="gradient-text">Mulai Sekarang!</span>
        </a>
        @else
        <a href="{{ url('/login') }}" class="btn-start">
            <span class="gradient-text">Mulai Sekarang!</span>
        </a>
        @endauth
    </div>

    <section class="features" aria-label="Fitur Produk">

      <!-- Card 1: Manajemen Proyek -->
      <article class="card" aria-labelledby="card1-title">
        <ul class="project-tasks" aria-label="Daftar tugas proyek">
          <li>Mengelola setiap detail dengan mudah</li>
          <li>Menetapkan anggota tim</li>
          <li>Melampirkan dokumen yang dibutuhkan</li>
        </ul>
        <h3 id="card1-title" class="card-title">Manajemen Proyek</h3>
        <p class="card-desc">
          Kelola setiap proyek dengan mudah melalui alat pintar dan otomatisasi terintegrasi.
        </p>
      </article>

      <!-- Card 2: Manajemen Customer -->
      <article class="card" aria-labelledby="card2-title">
        <div class="customer-list" aria-label="Daftar pelanggan">
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Pelanggan Maya" />
            <div class="info">Maya Putri</div>
          </div>
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/men/28.jpg" alt="Pelanggan Dito" />
            <div class="info">Dito Wijaya</div>
          </div>
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Pelanggan Sari" />
            <div class="info">Sari Dewi</div>
          </div>
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/men/68.jpg" alt="Pelanggan Ali" />
            <div class="info">Ali Putra</div>
          </div>
          <div class="customer-item">
            <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Pelanggan Siska" />
            <div class="info">Siska Ana</div>
          </div>
        </div>
        <h3 id="card2-title" class="card-title">Manajemen Klien</h3>
        <p class="card-desc">
          Tingkatkan efisiensi dengan mengelola data klien secara rapi dan terstruktur.
        </p>
      </article>

      <!-- Card 3: AI Assistant -->
      <article class="card" aria-labelledby="card4-title" style="position: relative;">
        <div class="chatbot-mini">
          <div class="chatbot-header">
            Asisten AI
          </div>
          <div class="chatbot-messages">
            <div class="message user-message">Halo AI</div>
            <div class="message bot-message">Halo! Ada yang bisa saya bantu?</div>
            <div class="message user-message">Ada berapa proyek yang masih berjalan?</div>
            <div class="message bot-message">Saat ini kamu memiliki 8 proyek dengan status masih berjalan</div>
          </div>
          <form class="chatbot-input" onsubmit="return false;">
            <input type="text" placeholder="Tanyakan sesuatu..." aria-label="Input chat" />
            <button type="submit" aria-label="Kirim pesan">&#x27A4;</button>
          </form>
        </div>
        <h3 class="card3-title">Asisten AI</h3>
        <p class="card-desc">
          Asisten AI untuk percakapan cerdas, pembuatan proposal, dan otomatisasi fitur.
        </p>
      </article>

      <!-- Card 4: Catatan Pekerjaan -->
      <article class="card" aria-labelledby="card4-title">
        <ul class="todo-list" aria-label="Daftar catatan pekerjaan">
          <li><span class="todo-checkbox checked"></span>Progres modul login – Dito Wijaya</li>
          <li><span class="todo-checkbox checked"></span>Hasil Pengujian register – Sari Dewi</li>
          <li><span class="todo-checkbox"></span>Kendala integrasi API – Ali Putra</li>
        </ul>

        <h3 class="card-title">Catatan Pekerjaan</h3>
        <p class="card-desc">
          Catatan singkat dari anggota tim untuk manajer proyek.
        </p>
      </article>


      <!-- Card 5: Role -->
      <article class="card" aria-labelledby="card5-title">
        <div class="role-badge">Manajer</div>
        <div class="role-list">
          <span>Programmer</span>
          <span>Penguji</span>
        </div>
        <h3 class="card-title">Peran</h3>
        <p class="card-desc">
          Pengaturan peran yang fleksibel dan aman untuk mengelola akses pengguna.
        </p>
      </article>

      <!-- Card 6: Dashboard -->
      <article class="card" aria-labelledby="card6-title">
        <div class="dashboard-summary">
          <div class="dashboard-section">
            <h4>Informasi Cepat</h4>
            <div class="dashboard-info-summary">
              <div class="dashboard-info-card">
                <strong>12</strong>
                <span>Proyek</span>
              </div>
              <div class="dashboard-info-card">
                <strong>28</strong>
                <span>Catatan</span>
              </div>
              <div class="dashboard-info-card">
                <strong>15</strong>
                <span>Klien</span>
              </div>
            </div>
          </div>
        </div>
        <h3 class="card-title">Dasbor</h3>
        <p class="card-desc">
          Dasbor terpadu untuk memantau proyek dan mendapatkan ringkasan informasi penting.
        </p>
      </article>

    </section>
  </main>
</div>
</body>
</html>
