# Tom's Performance Studio

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![AI Engine](https://img.shields.io/badge/AI-Laravel_AI_(GPT--4.1)-412991?style=flat-square&logo=openai&logoColor=white)](https://openai.com)
[![WebSockets](https://img.shields.io/badge/Realtime-Laravel_Reverb-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://reverb.laravel.com/)

---
<div style="margin-top: 16px; display: flex; justify-content: center; align-items: center; gap: 16px">
    <b style="font-size: 16px; text-align: center;"> Live Platforms :</b> 
    <span> 
        <a href="https://dashboard.tomsperformancestudio.cloud" style="font-size: 15px;">Admin Dashboard</a>
    </span> 
    <span>•</span>
    <span> 
        <a href="https://tomsperformancestudio.cloud" style="font-size: 15px;">Client Portal</a>
    </span> 
</div>

---

### ◼ About the Project

A class management system for a web-first music and performing arts studio platform integrating automated lesson scheduling, student practice notes with multimedia recordings, selective tuition dues billing, real-time messaging, and back-office administration.

---

### ◼ My Role

Responsible for end-to-end development: engineered the complete backend, built the client portal (React SPA), and designed the admin dashboard (Blade). This repository only contains a portion of the backend.

---

### ◼ Tech Stack

| Category | Technology |
| :--- | :--- |
| **Backend** | Laravel 13, PHP 8.5 |
| **Admin Dashboard** | Blade, Tailwind CSS, Alpine.js, Vite |
| **Client Frontend** | React, Tailwind CSS, Vite |
| **Architecture** | Modular Monolith |
| **Design Pattern** | Action-Domain Pattern (Domain-Oriented MVC) |
| **Database** | MySQL, Eloquent ORM |
| **Auth & Security** | `Sanctum`, `Breeze`, `Socialite` |
| **RBAC** | `Spatie Permission` |
| **Billing** | `Laravel Cashier` (Stripe)|
| **File Handling** | `Laravel FilePond` (Chunked uploads), `mPDF` |

<div style="margin:48px auto;  background-color: #fff8e1; border: 1px solid #ffe082; border-left: 5px solid #ffb300; padding: 14px 18px; border-radius: 8px; color: #5d4037;">
  <strong>⚠️ Note on Confidentiality</strong><br>
  Due to proprietary restrictions, sensitive credentials have been replaced with fake/personal test data, and files containing proprietary business architecture were removed. Modules, sub-modules, and the underlying structure have been retained for inspection & review.
</div>

### ◼ CORE MODULES

`🔴 Class Scheduling` `🟡 LMS`  `🟣 Billing` `🟢 Payment Generation Engine`

---

### ◼ FEATURES

- **Lesson Schedules:** Automated recurring schedules with calendar synchronization for private and group lessons.
- **Attendance & Rescheduling:** Attendance tracking, lesson history, and teacher schedule reassignment tools.
- **Dues & Cancellations:** Policy-based cancellation requests with admin approval workflows and auto-adjusted dues.
- **Lesson Notes:** Student practice assignments with embedded audio and video attachments via chunked uploads.
- **Billing & Dues:** Itemized tuition generation, selective dues checkout via Stripe, and transaction logs.
- **Disputes & Reports:** In-dashboard report logging, dispute resolution tools, and status notifications.
- **Portals & Roles:** Granular RBAC (Super Admin, Admin, Teacher, Student, Guest) with dedicated portals.


---

### ◼ Impact of this project

- Serving an active performing arts studio managing `40+ instructors`, `1400+ student enrollments`, and instrument disciplines.
- Streamlined studio scheduling, lesson history tracking, and student practice reviews across web and mobile views.
- Automated recurring billing and payment collection via Stripe.

---

### ◼ Architecture Highlights

```text
app/
├── Actions/                  # Reusable action files (create, update, delete, patch/toggle)
├── Enums/                    # Reusable enum files (e.g., NotifyChannelEnums)
├── Domains/                  # Core business domain folder 
│   ├── Billings/             # Core domain-1
│   ├── Users/                # Core domain-2
│   └── Lessons/              # Core domain-3
│   .    ├── Actions/         # Domain-based action files, e.g., LessonSaveAction.php
│   .    ├── Controllers/     # ApiLessonController, AdminLessonController
│   .    ├── DTOs/            # Immutable transferrable data formats, e.g., LessonScheduleDto.php
│   .    ├── Enums/           # Fixed type definitions, e.g., LessonPhaseEnums.php
│   .    ├── Events/          # Domain-level events, e.g., LessonNotifyEvent.php
│   .    ├── Listeners/       # Domain-level listeners, e.g., LessonPushListener.php
│   .    ├── Models/          # Domain-based models, e.g., Lesson.php, LessonHistory.php
│   .    ├── Requests/        # Domain-based Form validations, e.g., LessonRequest.php
│   .    ├── Services/        # Business logic orchestrators, e.g., HistoryGeneratorService.php
│   .    ├── Traits/          # Shared behaviors & scopes, e.g., HasLessonSchedules.php
│   .    └── ValueObjects/    # Immutable typed values with self-validation, e.g., ScheduledSlot.php
│  ... ... ...
├── Http/Controllers/         # For framework-centric modules (e.g., User)
├── Jobs/                     # Chunked background workers
└── Models/                   # For framework-centric modules (e.g., User)

routes/
├── _registry/
├── Auth/
├── Billings/
├── Communication/
└── Workflows/
... ... ...
```

---

### ◼ Setup

```bash
# 1. Install dependencies
composer install
npm install
npm run build

# 2. Environment & key
cp .env.example .env
php artisan key:generate

# 3. Database & storage
php artisan migrate --seed
php artisan storage:link

# 4. Run
composer run dev
```

---

### ◼ License

Proprietary. All rights reserved.
