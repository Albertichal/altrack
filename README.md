# ALtrack

A gym workout tracking web app built for athletes who want to log sessions fast — without the friction.

🔗 **Live:** [www.altrack.my.id](https://www.altrack.my.id)

---

## What it does

ALtrack lets users log their workouts with minimal input by remembering what they've done before.

- **Custom splits** — Users start with Push / Pull / Legs defaults, and can create their own splits on the fly
- **Smart exercise history** — When you log the same split again, ALtrack pre-fills your last session's sets, reps, and weight — so you can see your previous numbers without digging through history
- **Custom exercises** — Add any exercise not in the default list; it gets saved and becomes available as a dropdown option on future sessions
- **localStorage draft** — Your current session is buffered locally, so refreshing or switching tabs won't wipe your progress
- **Cardio logging** — Optional cardio section per session
- **Session notes** — Add a short note to each workout
- **Admin panel** — Admin can view all users' progress and workout history
- **Progress page** — Users can track their own history over time

---

## Tech Stack

- **Backend:** Laravel 13 (PHP)
- **Frontend:** Blade + Vanilla JS
- **Database:** MySQL
- **Deployment:** Railway (nixpacks)

---

## Key Technical Details

- Auth: Session-based (admin-managed accounts)
- Draft persistence: localStorage before DB commit
- On session save: splits and exercises are persisted to DB and auto-populated on next login
- Last session pre-fill: queries most recent workout log per split to surface previous performance

---

## Screenshots
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/5bfb74a8-b77f-4bf2-805a-67c7c7a02310" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/9d3364d9-811e-438d-b486-c8cec62f7024" />
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/7e016d8a-39c3-4997-8e98-2cd8b1ff7509" />


---

## Local Setup

```bash
git clone https://github.com/Albertichal/altrack.git
cd altrack
composer install
cp .env.example .env
php artisan key:generate
# Configure DB in .env
php artisan migrate
php artisan serve
```

---

Built by [@Albertichal](https://github.com/Albertichal)
