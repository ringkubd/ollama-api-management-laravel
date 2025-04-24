# Ollama API Management

A comprehensive management system for Ollama API that provides request handling, queueing, model management, and a complete role-based access control system.

![Ollama API Management](https://img.shields.io/badge/Ollama-API%20Management-blue)
![Laravel](https://img.shields.io/badge/Built%20with-Laravel-FF2D20?logo=laravel&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 🔍 Overview

Ollama API Management provides a web interface and API layer to manage and interact with [Ollama](https://ollama.ai/) AI models. It enables organizations to centrally manage API access, monitor usage, control permissions through a role-based system, and test models directly in the browser.

## ✨ Features

- **API Request Handling & Queuing**: Process and queue AI model requests for efficient handling
- **Comprehensive Dashboard**: Monitor API usage, performance metrics, and system status
- **Model Management**: Sync, activate/deactivate, and manage Ollama models
- **API Key Management**: Generate and manage API keys with granular permissions
- **User Authentication & RBAC**: Complete role-based access control system
- **Model Playground**: Test models directly in the browser with chat and text generation interfaces
- **Detailed API Documentation**: Complete API docs with Postman collection
- **React Native Integration**: Backend support for notes application using Socketi for real-time communication

## 🛠️ Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL or compatible database
- Ollama server running and accessible

### Steps

1. Clone the repository
   ```bash
   git clone https://github.com/yourusername/ollama-api-management.git
   cd ollama-api-management
   ```

2. Install dependencies
   ```bash
   composer install
   ```

3. Copy environment file and configure
   ```bash
   cp .env.example .env
   ```

   Update the following in your `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ollama_api_management
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_password
   
   OLLAMA_API_URL=http://your-ollama-server:11434
   ```

4. Generate application key
   ```bash
   php artisan key:generate
   ```

5. Run database migrations and seed initial data
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. Start the development server
   ```bash
   php artisan serve
   ```

7. Visit `http://localhost:8000` in your browser

## 👥 Default Users

After seeding, the system will be populated with:

- **Admin User**: admin@example.com / password
- **Editor User**: editor@example.com / password
- **Viewer User**: viewer@example.com / password

## 🚀 Usage

### API Endpoints

The system provides the following API endpoints:

- `POST /api/chat`: Send chat completions requests
- `POST /api/generate`: Generate text completions
- `GET /api/models`: List available models

All API endpoints require an API key provided in the header:

```
Authorization: Bearer YOUR_API_KEY
```

### Admin Interface

The admin interface provides several sections:

- **Dashboard**: View API usage statistics and system status
- **Models**: Manage available Ollama models
- **API Keys**: Generate and manage API keys
- **Playground**: Test models directly in the browser
- **API Docs**: View API documentation
- **Users & Roles**: Manage users and role permissions

### Model Playground

The Model Playground allows administrators to:

1. Test chat models through an interactive chat interface
2. Test text generation with prompt-based inputs
3. Adjust parameters like temperature and max tokens
4. View detailed error information for debugging

## 📊 System Architecture

The system is built with Laravel and includes:

- **Queue Workers**: For processing long-running AI requests
- **Role-Based Access Control**: Three default roles (Admin, Editor, Viewer)
- **Services Layer**: Handles communication with Ollama API
- **Events System**: Supports real-time notifications via Socketi

## 🔐 Role-Based Access Control

The system includes three default roles:

- **Admin**: Full access to all features
- **Editor**: Can manage models and API keys, but not users or roles
- **Viewer**: Read-only access to dashboard and API documentation

## 📝 API Documentation

Complete API documentation is available at `/admin/documentation` in the web interface. A Postman collection is also available for download.

## 🔄 React Native Integration

The system includes backend components to integrate with React Native applications:

- `ProcessNotesAssistanceRequest` job for handling note assistance requests
- `NoteAssistanceGenerated` event for real-time updates

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📧 Contact

If you have any questions or feedback, please open an issue on GitHub.

---

Built with ❤️ for Ollama users
