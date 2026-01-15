## DJ Website Management App

This application manages DJs and their time slots efficiently. It includes an admin panel for managing DJs and a frontend for showcasing schedules.

### Features
- **Admin Panel**:
  - Create DJs with video, name, and time slot.
  - Edit DJ information.
  - Modify, switch, and reschedule time slots.
- **Frontend**:
  - Display DJ schedules.

### Technologies
- **Laravel 10**: Backend framework.
- **Vite**: Asset bundler with Sass entrypoints.
- **Bootstrap 5**: Frontend styling.
- **Tabler**: Admin panel design.
- **PHP 8.2**: Programming language.
- **MySQL**: Database for storing DJ and schedule data.

### Installation
1. Clone the repository:
   ```bash
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```bash
   cd DJ-and-voter-web-app
   ```
3. Install PHP dependencies:
   ```bash
   composer install
   ```
4. Install Node.js dependencies:
   ```bash
   npm install
   ```
5. Compile assets using Vite:
   ```bash
   npm run dev
   ```
6. Set up the environment file:
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your database credentials.
7. Run database migrations:
   ```bash
   php artisan migrate
   ```
8. Start the development server:
   ```bash
   php artisan serve
   ```

### Documentation
Refer to the [Admin Documentation](docs/diagrams/admin-documentation.md) for detailed information.
