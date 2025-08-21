<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

# Reltroner HRM (Human Resource Manager)

**Reltroner HRM** is a Laravel 12-based human resource management application with **AI-powered employee wellbeing analytics**, built with the traditional stack of Laravel + Blade + Tailwind + Vite. This project is part of the digital infrastructure of *Reltroner Studio*.

## ✨ Key Features

### Core HR Management
* Employee CRUD
* Department & Role Management
* Task Management (CRUD + Status: Complete/Pending)
* Attendance Tracking
* Payroll Processing
* Leave Request Handling
* Form Validation & Flash Messages
* Dashboard Statistics
* Soft Deletes + Eloquent Relationships

### 🤖 AI-Powered Wellbeing Analytics (NEW!)
* **OpenAI GPT-4o Integration** - Intelligent pattern analysis
* **Anomaly Detection** - Automatic detection of work pattern irregularities
* **Risk Assessment** - Low/Medium/High risk classification with numerical scoring
* **Slack Notifications** - Automated HR alerts for employee wellbeing concerns
* **Pattern Analysis** - Late check-ins, long work days, weekend work detection
* **Historical Tracking** - Complete analysis history with trend visualization
* **Interactive Dashboard** - Real-time wellbeing monitoring interface

## 📆 Tech Stack

* Laravel 12
* Blade Templating Engine
* Tailwind CSS + Mazer Template
* MySQL / PostgreSQL
* Laravel Breeze (Auth)
* **OpenAI GPT-4o API** 🧠
* **Slack Webhooks** 💬
* Flatpickr, DataTables

## 🧠 Design Philosophy

Reltroner HRM is not just a practice project, but a technical pillar of the world-building studio. It integrates principles like:

* Meritocratic systems
* SDI Structure (Sentient Development Index)
* **AI-driven employee wellbeing monitoring**
* **Proactive mental health support**
* Clean and intuitive UI

## 🤖 AI Wellbeing Features

### Anomaly Detection
- Late check-in patterns
- Extended work hours
- Weekend work frequency
- Inconsistent schedule patterns
- Consecutive long work days

### Risk Assessment
- **Low Risk (25-40 points)**: Healthy work patterns
- **Medium Risk (41-70 points)**: Some concerns detected
- **High Risk (71-100 points)**: Immediate intervention needed

### AI Analysis
- Contextual insights for creative work environment
- Personalized recommendations for HR teams
- Industry-specific pattern recognition
- Burnout prevention strategies

## 🚿 Bug & Issue Notes

### 1. Sidebar Active Highlight Not Dynamic

* Sidebar `active` class does not automatically follow the current page.
* **Solution:** use `request()->routeIs()` helper on sidebar-item.

### 2. Date Fields (Birth Date & Hire Date) Empty on Edit

* Although the data is stored, the date input fields do not display it.
* **Solution:** ensure `birth_date` & `hire_date` are cast to `date` in the model and formatted with `format('Y-m-d')`.

## 🚀 Local Installation

```bash
git clone https://github.com/jiajun489/hr-app-laravel.git
cd hr-app-laravel
composer install
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Install frontend dependencies
npm install && npm run dev

# Start the server
php artisan serve
```

## ⚙️ AI Configuration

### OpenAI Setup
1. Get API key from https://platform.openai.com/api-keys
2. Add to `.env`:
```bash
OPENAI_API_KEY=your_openai_api_key_here
OPENAI_MODEL=gpt-4o
```

### Slack Notifications (Optional)
1. Create Slack app at https://api.slack.com/apps
2. Add Incoming Webhook
3. Add to `.env`:
```bash
SLACK_WEBHOOK_URL=your_slack_webhook_url_here
```

## 🎯 AI Commands

```bash
# Run wellbeing analysis for all employees
php artisan employees:analyze-patterns

# Analyze specific employee with HR notification
php artisan employees:analyze-patterns --employee_id=1 --notify-hr

# Generate sample presence data for testing
php artisan generate:sample-presence 1 --days=30

# Test Slack notifications
php artisan test:slack-notification

# Debug OpenAI prompts
php artisan debug:openai-prompt 1
```

## 📁 Module Structure

```
- app/Models
  - Employee.php
  - EmployeeAIAnalysis.php (NEW!)
  - Task.php, Department.php, Role.php
  - Payroll.php, Presence.php, LeaveRequest.php
  
- app/Http/Controllers
  - EmployeeWellbeingController.php (NEW!)
  - EmployeeController.php, TaskController.php
  - DepartmentController.php, RoleController.php
  - PayrollController.php, PresenceController.php
  
- app/Services (NEW!)
  - OpenAIService.php
  - AnomalyDetectionService.php
  - SlackNotificationService.php
  
- app/Console/Commands (NEW!)
  - AnalyzeEmployeePatterns.php
  - GenerateSamplePresenceData.php
  - TestSlackNotification.php
```

## 🔐 Access & Authentication

* Laravel Breeze is enabled
* All routes are protected with `auth` middleware (except login/register)  
* Role-based authorization (Admin/HR Manager for wellbeing features)

## 🔐 Access for Recruiters / HRD – Demo Accounts

For testing and demo purposes, you can log in as:

- **Admin:**  
  user: `admin@example.com`  
  password: `password`

- **HR Manager:**  
  user: `hr@example.com`  
  password: `password`

- **Employee:**  
  user: `developer@example.com`  
  password: `password`

---

🌍 **Live Demo:** [hrm.reltroner.com](https://hrm.reltroner.com)  
💻 **Source:** [github.com/jiajun489/hr-app-laravel.git](https://github.com/jiajun489/hr-app-laravel.git)

---

## 👨‍💻 Developer

* **Rei Reltroner** – Founder & Developer
* [Reltroner Studio](https://reltroner.com) – Digital Worldbuilding & Product Ecosystem

---

## License

This project is built with Laravel and follows the [MIT License](https://opensource.org/licenses/MIT).
