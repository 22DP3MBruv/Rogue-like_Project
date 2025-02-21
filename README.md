# Rogue-like_Project

A 2D game developed using [Godot](https://godotengine.org/) inspired by "Vampire Survivors". In the game you battle simple AI enemies on a flat plane. As you progress, you gain experience, level up, and unlock power ups that change the way you play while enemy difficulty scales.
The homepage will contain all related information about the game, being the access point for the game and game content

## Controls
  The game will be controlled by both keyboard and mouse

  Keyboard:
  - The key W or the up arrow moving you up
  - The key A or the left arrow moving you left
  - The key S or the down arrow moving you down
  - The key D or the right arrow moving you right
 
  Mouse:
  - Using the cursor you will be able to orient your attack direction


## Project Structure
  
- **Backend (PHP API):**  
  Contains endpoints for user login, registration, and database setup.  
  - The database connection is implemented in `backend/database.php`.
  - Login: `backend/login.php`
  - Registration: `backend/register.php`
  - Setup script for creating the users table: `backend/setup.php`

- **Frontend (Vue 3 + Vite):**  
  A modern web client that includes login and register modals as well as various content sections.  
  - Main application: `frontend/src/App.vue`
  - Entry point: `frontend/src/main.js`
  - Configuration and build scripts are provided in `frontend/package.json` and `frontend/vite.config.js`

- **Game (Godot):**  
  The gameplay and scenes are developed in Godot.  
  - The primary game source is in `roguelikesource/project.godot`  
  - Sample scene: `roguelikesource/scenes/testscene.tscn`  
  - Player script: `roguelikesource/scenes/player.gd`

## Setup Instructions

### Prerequisites

- **PHP & MySQL:** Ensure you have PHP installed along with a MySQL server.  
- **Node.js & NPM:** Required for running the frontend.
- **Godot Engine:** For running and editing the game source in `roguelikesource`.

### Backend Setup

1. **Database Setup:**  
   - Create a database named as specified in `backend/database.php` (default: `data`).
   - Update database credentials in `backend/database.php` if needed.
  
2. **Create Tables:**  
   - Run the setup script by accessing `backend/setup.php` via your browser or command line from localhost.  
   - This script creates the `users` table used for authentication.

3. **API Endpoints:**  
   - The PHP API endpoints for login and registration are located at `backend/login.php` and `backend/register.php`.

### Frontend Setup

1. **Install Dependencies:**  
   Open the terminal in the `frontend` directory and run:

   ```sh
   npm install
   ```

2. **Development Server:**  
   To start development mode run:

   ```sh
   npm run dev
   ```

3. **Build for Production:**  
   For building the production version run:

   ```sh
   npm run build
   ```

   This configuration uses Vite as defined in `frontend/vite.config.js`.

### Godot Setup

1. **Open the Project:**  
   Open the `roguelikesource/project.godot` file with Godot Engine.
   
2. **Scene Development:**  
   - Edit or run scenes like `roguelikesource/scenes/testscene.tscn` to view the game.
   - Player controls and logic are implemented in `roguelikesource/scenes/player.gd`.

## General Details

- **Authentication:**  
  The frontend uses Vue 3 views to present login and registration forms. After successful authentication, users can log in to access additional game or shop content.

- **User Interface:**  
  Navigation is handled via Vue Router (`frontend/src/router/index.js`), and the main interface is built in `frontend/src/App.vue`.

- **Styling:**  
  Global styles are included via `frontend/src/assets/base.css` and `frontend/src/assets/main.css`.

- **Icons & Components:**  
  Custom icons for documentation, community, ecosystem, support, and tooling are available in `frontend/src/components/icons/`.

