# AI Internship & Skill Management Web Application

A production-ready, AI-enabled Web Application built with PHP 8, MySQL, and Bootstrap 5.

## 🚀 Key Features

- **Role-Based Access**: Admin, Mentor, and Intern roles with secure authentication.
- **AI Integration**:
  - **Skill Gap Analyzer**: Compares intern skills with task requirements.
  - **Resume Improver**: Analyzes resumes for ATS compatibility.
- **Task Management**: Mentors create tasks; Interns view and submit work.
- **3D SaaS UI**: Modern Glassmorphism design with GSAP animations.
- **Dashboards**: Customized views for each role with analytics.

## 🛠️ Tech Stack

- **Backend**: PHP 8.x
- **Database**: MySQL
- **Frontend**: HTML5, CSS3 (Bootstrap 5), JavaScript (Vanilla + Chart.js + GSAP)
- **Deployment**: Ready for Shared Hosting (iPage, GoDaddy, etc.)

## ⚙️ Installation (Local Development)

1.  **Prerequisites**:
    - Install XAMPP, WAMP, or Laragon.
    - Ensure PHP 8.0+ is enabled.

2.  **Database Setup**:
    - Open phpMyAdmin (`http://localhost/phpmyadmin`).
    - Create a database named `internship_db`.
    - Import `schema.sql` from the project root.

3.  **Configuration**:
    - Open `app/config/config.php`.
    - Ensure DB credentials match your local setup (Default: `root` / empty password).

4.  **Run**:
    - Place the project folder in `htdocs` (XAMPP) or `www` (WAMP).
    - Access via `http://localhost/YourProjectFolder/public`.

## ☁️ Deployment (iPage / Shared Hosting)

1.  **Upload Files**:
    - Upload the entire project folder to `public_html`.

2.  **Database**:
    - Create a MySQL database in your hosting control panel.
    - Import `database.sql` using phpMyAdmin.

3.  **Update Config**:
    - Edit `app/config/config.php`:
      - Set `$is_local = false;` (Automatic detection should work).
      - Update `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` with your hosting details.

4.  **AI Service**:
    - Edit `app/services/AIService.php` and add your OpenAI/Gemini API Key.

## 📂 Project Structure

```
/app
  /config         # Database & App Config
  /controllers    # MVC Controllers
  /models         # Database Models
  /views          # HTML Views (Layouts, Dashboards)
  /services       # AI Logic
  /core           # Router & Database Wrapper
/public
  /assets         # CSS, JS, Images
  index.php       # Entry Point
```
