# NeuroScan MS — MRI-based Multiple Sclerosis Detection

A professional, bilingual (English / Arabic) web application for doctors to upload brain MRI scans and receive AI-assisted MS detection results.  
Built as a graduation project using **HTML · CSS · JavaScript · PHP · MySQL**.

---

## ✨ Features

- Secure doctor login (PHP sessions + bcrypt hashing)
- MRI image upload with live preview (drag-and-drop)
- AI-powered analysis via **OpenAI GPT-4o Vision** (with simulation fallback)
- Dashboard with scan statistics & recent history
- Result page: MS Positive/Negative verdict + confidence score + clinical notes
- Full **English ↔ Arabic** UI with automatic RTL layout
- Dedicated team page for graduation project members
- Clean, responsive medical-style UI (white & blue theme)

---

## 📁 Project Structure

```
ms-detection-system/
├── config/
│   ├── database.php         # MySQL connection settings
│   └── config.php           # OpenAI key & app settings
├── includes/
│   ├── auth.php             # Login / session helpers
│   ├── functions.php        # Upload + AI analysis logic
│   ├── header.php           # Shared top-bar + nav
│   └── footer.php           # Shared footer
├── lang/
│   ├── en.php               # English strings
│   └── ar.php               # Arabic strings
├── assets/
│   ├── css/style.css        # Full stylesheet (medical theme)
│   ├── js/main.js           # Dropzone + preview + UI
│   └── images/              # (empty - reserved)
├── uploads/                 # MRI images stored here
├── database/
│   └── schema.sql           # Users / Images / Results tables
├── api/                     # (reserved for future endpoints)
├── install.php              # One-click installer
├── index.php                # Auth-aware entry point
├── login.php                # Login page
├── dashboard.php            # Doctor dashboard
├── upload.php               # Upload MRI page
├── result.php               # Result page
├── team.php                 # Team page
├── logout.php               # Ends session
└── README.md                # This file
```

---

## 🚀 Installation (XAMPP / WAMP / MAMP)

1. **Copy the folder** `ms-detection-system/` into your web server root  
   e.g. `C:\xampp\htdocs\ms-detection-system\`

2. **Start Apache & MySQL** from the XAMPP control panel.

3. **Configure the database** — edit `config/database.php` if your MySQL credentials differ (defaults: user `root`, empty password).

4. **Run the installer** once in your browser:  
   `http://localhost/ms-detection-system/install.php`  
   This will:
   - Create the database `ms_detection_db`
   - Create tables `users`, `images`, `results`
   - Seed the default doctor account
   - Ensure the `uploads/` directory is writable

5. **(Optional) Enable real AI analysis** — edit `config/config.php`:
   ```php
   define('OPENAI_API_KEY', 'sk-...your-key-here...');
   ```
   Get a key at: https://platform.openai.com/api-keys  
   If left empty, the system runs in **simulation mode** (realistic random results).

6. **Log in** at `http://localhost/ms-detection-system/login.php`

---

## 🔐 Default Login

| Email                      | Password    |
|----------------------------|-------------|
| doctor@ms-detect.com       | doctor123   |

---

## 🌐 Language Switching

Click the **globe button** in the top-right (or top-left in Arabic mode) to toggle between English and Arabic. The UI automatically switches to RTL when Arabic is active.

---

## 🗄️ Database Schema

**users**
| Column      | Type             |
|-------------|------------------|
| id          | INT, PK, AI      |
| name        | VARCHAR(150)     |
| email       | VARCHAR(150), UQ |
| password    | VARCHAR(255)     |
| created_at  | TIMESTAMP        |

**images**
| Column         | Type             |
|----------------|------------------|
| id             | INT, PK, AI      |
| user_id        | INT, FK → users  |
| image_path     | VARCHAR(500)     |
| original_name  | VARCHAR(255)     |
| upload_date    | TIMESTAMP        |

**results**
| Column             | Type                           |
|--------------------|--------------------------------|
| id                 | INT, PK, AI                    |
| image_id           | INT, FK → images               |
| result             | ENUM('Positive','Negative')    |
| confidence_score   | DECIMAL(5,2)                   |
| notes              | TEXT                           |
| created_at         | TIMESTAMP                      |

---

## ⚠️ Disclaimer

This project is for **educational and research purposes only**. It is not a certified medical device and must not be used for actual patient diagnosis.

---

## 👥 Team

Edit the names in `team.php` to reflect your own graduation project members.

---

© 2026  MS · Graduation Project
