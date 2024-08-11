# Email Template and Subscription Management System

## Overview

This system provides functionality for managing email templates and subscriptions. It includes the ability to:

- Manage email templates linked to servers.
- Create, view, and edit email templates using an HTML editor.
- Preview email templates to see how they will appear when sent.
- Manage subscription details, including the selection of servers and connections.

## Features

- **Servers Management**: Create and manage servers with optional URLs.
- **Email Templates Management**: Create, edit, and preview email templates linked to servers.
- **Subscription Management**: Handle subscription details, including connections and duration.

## Installation

### Prerequisites

- PHP 8.0 or higher
- Composer
- Laravel 10.x
- Filament Admin Panel

### Installation Steps

1. **Clone the Repository**

```bash
git clone https://github.com/your-repository.git
cd your-repository
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan make:filament-user
```

## Usage
### Accessing the Admin Panel
After installation, you can access the Filament admin panel at http://example.com/admin. Log in using your admin credentials.

### Managing Servers
Navigate to the "Servers" section in the admin panel.
Create a new server with a name and an optional URL.

### Managing Email Templates
Navigate to the "Email Templates" section in the admin panel.
Create or edit email templates. Use the HTML editor to define your template and add {{var}} placeholders for dynamic content.
Preview templates to see how they will appear when sent.

### Preview Functionality
**Listing Page**: Each email template listing includes a "Preview" button that directs to a page displaying the rendered template.

### Sending Subscription Emails
Navigate to the "Subscription Emails" section in the admin panel.
Send a new subscription mail by selecting the server, connections, subscription type, and duration.
Enter client details and username and password for service and click Submit to send the subscription email.

