# Rogue-like_Project

A prospective 2D game inspired by "Vampire Survivors" developed with Godot, PHP, and Vue 3. In this project you would battle simple AI enemies on a flat plane. As you progress, you gain experience, level up, and unlock power-ups. In addition, the project features a web interface for user authentication, news updates, and report submissions. These features currently aren't implemented fully and the game is in a bare bones state.

---

## System Launching Via The Command Line

### Backend (PHP API)
- **Server Environment:**  
  This project uses [XAMPP](https://www.apachefriends.org/) for PHP and administrative tasks.
  
- **Setup & Execution:**  
  1. Start XAMPP and ensure Apache and MySQL are running.
  2. Place the project in your XAMPP `htdocs` folder (e.g., `c:\xampp\htdocs\Rogue-like_Project`).
  3. Configure the database connection in `backend/config/database.php` (default database name: `data`).
  4. Run the setup script by accessing `http://localhost/Rogue-like_Project/backend/config/setup.php` via your browser to create the necessary tables.
  5. API endpoints such as login (`backend/api/login.php`), registration (`backend/api/register.php`), reports (`backend/api/report.php`), etc. will be accessible at `http://localhost/Rogue-like_Project/backend/api/`.

### Frontend (Vue 3 + Vite)
- **Installation:**  
  Open a terminal in the `frontend` folder and run:
  ```sh
  npm install
  ```
- **Development Server:**  
  Start the development server using:
  ```sh
  npm run dev
  ```
- **Production Build:**  
  Build for production with:
  ```sh
  npm run build
  ```
- **Access:**  
  The application will run on the port specified by Vite (typically http://localhost:3000).

---

## System Development Tools

- **Backend:**  
  - PHP & MySQL (via XAMPP)
  - PDO for database interactions
- **Frontend:**  
  - Vue 3
  - Vite (build and development tool)
  - JavaScript (ES6+)
- **Game Engine:**  
  - Godot Engine (for gameplay and scenes)
- **Version Control:**  
  - Git
- **Other Tools (optional):**  
  - VSCode (recommended IDE with Vue support via Volar)
  - Git Bash / Terminal

---

## Project Structure

### Backend
- **API Endpoints:**  
  Located in `backend/api/` (e.g., `login.php`, `register.php`, `getReports.php`, etc.)
- **Configuration:**  
  Database and application configuration files can be found in `backend/config/`.

### Frontend
- **Vue Components:**  
  Main UI components are under `frontend/src/components/` (e.g., `Header.vue`, `Footer.vue`, `HomePage.vue`).
- **Routing:**  
  Handled by Vue Router (`frontend/src/router/`).
- **Assets & Styling:**  
  Global and component-specific styles reside in `frontend/src/assets/`.

### Game (Godot)
- **Source Code:**  
  Located in the `roguelikesource/` folder.
- **Scenes & Scripts:**  
  Edit gameplay and scenes (e.g., `project.godot`, `scenes/player.gd`) using Godot Engine.

