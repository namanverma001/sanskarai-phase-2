# Sanskar AI

> Your comprehensive guide to Hindu rituals and traditions

Sanskar AI is a web application that helps users discover, learn, and perform Hindu rituals with the assistance of verified Pandits and AI-powered recommendations.

## 🚀 Features

### For Users
- **Explore Rituals**: Browse a comprehensive database of Hindu rituals with detailed steps and required items
- **Family Management**: Store family details including Gotra, Nakshatra, and Kul Devta for personalized suggestions
- **AI Suggestions**: Get personalized ritual recommendations based on occasion and family context
- **Book a Pandit**: Find and book verified Pandits for performing rituals at your home
- **Shopping List**: Generate and manage shopping lists for ritual items
- **Custom Rituals**: Create custom rituals for special occasions, validated by expert Pandits
- **Cultural Insights**: Learn about Hindu culture, traditions, and spiritual practices

### For Pandits
- **Profile Management**: Showcase your expertise, specialization, and experience
- **Assignment Management**: View, confirm, and complete ritual bookings
- **Q&A System**: Answer user questions about rituals and practices
- **Custom Ritual Validation**: Review and validate user-created custom rituals

### For Administrators
- **User Management**: Manage all users, block/unblock accounts
- **Pandit Approval**: Review and approve Pandit applications
- **Ritual Management**: Create, edit, and manage rituals in the database
- **AI Logs**: Monitor AI interactions and flag inappropriate content

## 🛠️ Technology Stack

- **Backend**: Pure PHP (No frameworks)
- **Database**: MySQL Server with PDO
- **Architecture**: MVC-style with custom routing
- **Authentication**: Session-based with bcrypt password hashing
- **Security**: CSRF protection, prepared statements

## 📁 Project Structure

```
sanskar-ai/
├── app/
│   ├── config/          # Configuration files
│   │   ├── app.php      # Application configuration
│   │   └── database.php # Database connection
│   ├── controllers/     # Application controllers
│   ├── core/            # Framework core classes
│   │   ├── Auth.php     # Authentication helper
│   │   ├── Controller.php # Base controller
│   │   ├── Model.php    # Base model
│   │   └── Router.php   # Routing system
│   ├── database/        # Migration and seeding scripts
│   │   ├── migrate.php  # Database migration
│   │   └── seed_admin.php # Sample data seeding
│   ├── middleware/      # Route middleware
│   ├── models/          # Data models
│   ├── services/        # Service classes (AI, etc.)
│   ├── views/           # View templates
│   └── routes.php       # Route definitions
├── public/
│   ├── index.php        # Application entry point
│   └── .htaccess        # URL rewriting
├── storage/             # File uploads and logs
├── .env                 # Environment configuration
└── README.md
```

## 🔧 Installation

### Prerequisites
- PHP 8.0+
- MySQL 8.0+
- Apache with mod_rewrite (or PHP built-in server for development)

### Setup Steps

1. **Clone the repository**
   ```bash
   cd "E:\SANSKAR AI\Phase 2"
   ```

2. **Configure environment**
   Copy `.env.example` to `.env` and update database credentials:
   ```
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=SAI
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

3. **Run database migration**
   ```bash
   php app/database/migrate.php
   ```

4. **Seed sample data** (optional)
   ```bash
   php app/database/seed_admin.php
   ```

5. **Start the server**
   ```bash
   cd public
   php -S localhost:8000
   ```

6. **Access the application**
   Open `http://localhost:8000` in your browser

## 👤 Test Accounts

After running the seeder, you can login with:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@sanskarai.com | admin123 |
| Pandit | pandit@sanskarai.com | pandit123 |
| User | user@sanskarai.com | user123 |

## 📊 Database Schema

The application uses 17 tables with the `SAI_` prefix:

- `SAI_users` - User accounts
- `SAI_pandit_profiles` - Pandit professional details
- `SAI_families` - User family information
- `SAI_family_members` - Family member details
- `SAI_rituals` - Ritual database
- `SAI_ritual_steps` - Step-by-step ritual instructions
- `SAI_ritual_items` - Required items for rituals
- `SAI_custom_rituals` - User-created custom rituals
- `SAI_custom_ritual_steps` - Steps for custom rituals
- `SAI_assignments` - Pandit booking assignments
- `SAI_shopping_lists` - User shopping lists
- `SAI_ai_requests` - AI interaction logs
- `SAI_ai_logs` - AI system logs
- `SAI_pandit_qna` - Questions and answers
- `SAI_cultural_insights` - Knowledge base articles
- `SAI_notifications` - User notifications

## 🔒 Security Features

- **Password Hashing**: bcrypt with cost factor 12
- **CSRF Protection**: Token-based protection on all forms
- **SQL Injection Prevention**: Prepared statements with PDO
- **Session Security**: Session regeneration on login
- **Role-Based Access**: Middleware-protected routes

## 🎨 UI Themes

Each panel has a unique color theme:
- **Auth**: Gradient with glassmorphism effect
- **Admin**: Dark purple professional theme
- **Pandit**: Purple spiritual theme
- **User**: Warm orange welcoming theme

## 📝 License

This project is proprietary software developed for Sanskar AI.

## 🤝 Support

For support, please contact the development team.
