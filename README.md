# PocketPHP - A Lightweight PHP Framework for Small-Scale Projects on Shared Hosting

# Note currently this framework is under development not for production use

> **Note:** This is a proprietary PHP framework created and maintained by [MadForTech](https://github.com/madfortech). All rights reserved.

PocketPHP is a lightweight, fast, and easy-to-use PHP framework designed specifically for small-scale projects on shared hosting. It helps you build web applications quickly and efficiently.

## ✨ Key Features

- 🚀 Fast and lightweight
- 🔐 Built-in Authentication System
- ✉️ Email Verification
- 🔄 MVC Architecture
- 🛣️ Simple Routing System
- 🔧 Easy Configuration
- 🔒 Secure by Default
- 📦 Composer Ready

## 📋 Requirements

- PHP 8.2 or higher
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Apache/Nginx Web Server

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/madfortech/PocketPHP.git
   cd PocketPHP
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up the `.env` file:
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your database and application settings.

## ⚙️ Configuration

### Database Configuration

```
config/database.php
```

### Email Configuration (using Mailtrap as example)
```env
MAIL_DRIVER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME="Your App Name"
MAIL_DEBUG=true
```

## 🛠️ Setup Database

1. Create a new MySQL database phpmyadmin

## 🚦 Running the Application

For development:
```bash
php -S localhost:8000 -t public
```

Or using the built-in server:
```bash
composer serve
```

Then open `http://localhost:8000` in your browser.

## 🛣️ Routing

Define your routes in `config/routes.php`:

```php
// Basic Route
$router->addRoute('GET', '/', 'PocketPHP\Controller\WelcomeController@index');
```

## 📜 License

Copyright © 2025 [MadForTech](https://github.com/madfortech)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 Documentation

For more detailed documentation, please visit [PocketPHP Docs](#) (Coming Soon)
