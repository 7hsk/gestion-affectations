<p align="center">
  <a href="https://streamable.com/3o9mjr" target="_blank">
    <img src="gestion-affectation/exodia-corp.jpg" alt="Watch Demo Video" width="640">
  </a>
</p>


<h1 align="center">Gestion Affectations</h1>

<p align="center">
  <b>Modern Laravel platform for managing academic assignments, schedules, and more.</b><br>
  <a href="#features">Features</a> •
  <a href="#quick-start">Quick Start</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#contributing">Contributing</a>
</p>

---

## 🚀 About Gestion Affectations

Gestion Affectations is a robust web application built with Laravel, designed to streamline the management of academic assignments, schedules, and user roles (Admin, Enseignant, Coordonnateur, Chef, Vacataire, etc.).

---

## ✨ Features

- User authentication & role management (Admin, Enseignant, Coordonnateur, Chef, Vacataire)
- Assignment & schedule management
- Notifications system
- Import/export (Excel, PDF)
- Activity logs & history tracking
- Responsive UI with TailwindCSS & Bootstrap
- Permission management (Spatie)
- SweetAlert notifications

---

## 🛠️ Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** TailwindCSS, Bootstrap 5, Alpine.js
- **PDF Export:** barryvdh/laravel-dompdf
- **Excel Import/Export:** maatwebsite/excel
- **Permissions:** spatie/laravel-permission
- **Notifications:** realrashid/sweet-alert

---

## ⚡ Quick Start

```bash
# Clone the repo
 git clone https://github.com/your-username/gestion-affectations.git
 cd gestion-affectations

# Install PHP dependencies
 composer install

# Install JS dependencies
 npm install && npm run dev

# Copy .env and set up your environment
 cp .env.example .env
 php artisan key:generate

# Run migrations & seeders
 php artisan migrate --seed

# Start the server
 php artisan serve
```

---

## 📂 Project Structure

- `app/Models/` — Eloquent models (User, Affectation, Note, etc.)
- `app/Http/Controllers/` — Controllers for each user role
- `resources/views/` — Blade templates for all user interfaces
- `routes/` — Route definitions (web, auth, console)

---

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you would like to change.

---

## 📄 License

This project is open-sourced under the MIT license.

---

<p align="center">
  <i>Made with ❤️ loztvayne</i>
</p>
