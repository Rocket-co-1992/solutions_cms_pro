# Solutions CMS

**Solutions CMS** is a flexible and modular content management system designed for web agencies and developers. It allows for easy site development, offering a balance between simplicity for end-users and deep customization for developers. Solutions CMS is available on Codecanyon and is used both by Pandao Web Agency and external developers to create highly optimized and customized websites.

Documentation: https://cms.pandao.eu/doc/

## Key Features

- **Modular architecture**: Add, remove, or customize modules such as pages, articles, menus, media, etc.
- **Multilingual support**: Easily handle multi-language content with dynamic routing based on language settings.
- **Dynamic URL management**: URLs can include or exclude language prefixes, and handle routing to pages, articles, or even 404 pages dynamically.
- **Admin Panel**: Clean and intuitive interface with login, dashboard, settings, and management for each module.
- **AJAX integration**: Seamless handling of AJAX requests in the admin interface.
- **Responsive design**: Mobile-first approach with automatic handling of image sizes based on devices.
- **Composer Integration**: Easily manage external libraries and dependencies with Composer.

## Installation

### 1. Installation wizard

Extract the files on your project directory.
Go to https://www.yourwebsite.com and follow the instructions of the wizard for an automatic installation.

### 2. Manual installation

Edit the `/config/config.php` file to set up your database connection details.
Make sure your web server is properly configured, and edit `/public/.htaccess` to replace {DOCBASE} with the path to the root of your application (usually `/`).

### 3. Access Admin Panel

After installation, you can access the admin panel at:

```
https://www.yourwebsite.com/admin
```

## File Structure

```bash
|   .htaccess                  # Main configuration file for Apache
|   index.php                  # Main entry point for the front-end
|   web.config                 # Configuration file for IIS servers
|
+---admin                      # Admin interface
|   |   index.php              # Main entry point for the admin panel
|   |
|   +---assets                 # Static assets for the admin panel (css, js, plugins)
|   |
|   +---controllers            # Admin controllers for handling module actions
|   |   Controller.php         # Base controller for admin modules
|   |   DashboardController.php # Handles the admin dashboard
|   |   FormController.php     # Handles form submissions
|   |   ListController.php     # Handles listing pages for admin modules
|   |   LoginController.php    # Handles login/logout for admin users
|   |   ModuleController.php   # Handles dynamic loading of admin modules
|   |   SettingsController.php # Handles admin settings
|   |
|   +---core                   # Core functionalities for the admin
|   |   AdminBootstrap.php      # Initializes the admin environment
|   |   Helpers.php             # Helper functions specific to the admin
|   |   Router.php              # Admin-specific routing
|   |
|   +---models                 # Admin-specific models
|   |   AdminContext.php        # Admin context management (session, auth)
|   |   Column.php              # Model for handling table columns in listings
|   |   Field.php               # Model for form fields
|   |   Filter.php              # Model for filtering data in listings
|   |   FormModel.php           # Handles form data processing
|   |   ListModel.php           # Handles list data processing
|   |   MediaModel.php          # Handles media uploads and management
|   |   Module.php              # Base class for all admin modules
|   |   ModuleModel.php         # Handles module-specific data
|   |   SettingsModel.php       # Manages admin settings
|   |   UsersModel.php          # Manages admin users
|   |
|   +---services               # Handles specific admin services
|   |   clear_tmp.php           # Clears temporary files
|   |   order_item.php          # Orders items in admin listings
|   |   order_medias.php        # Orders media items
|   |   remover.php             # Handles removal of items in admin
|   |   uploader.php            # Handles file uploads
|   |   uploadifive.php         # File upload integration with Uploadify
|   |
|   \---views                  # Admin views
|       |   dashboard.php       # Admin dashboard view
|       |   login.php           # Admin login page view
|       |   settings.php        # Admin settings view
|       \---partials           # Partial views like headers, footers
|               footer.php
|               head.php
|               header.php
|
+---common                     # Shared resources between admin and front-end
|   +---core                   # Core functionalities used across the project
|   |   Autoloader.php         # Autoload classes
|   |   Bootstrap.php           # Initializes the entire system
|   |   Database.php            # Database connection and management
|   |
|   +---models                 # Shared models between admin and front-end
|   |   LangManager.php         # Manages multi-language support
|   |
|   +---services               # Services used across the project
|   |   AuthHandler.php         # Authentication handler (login, logout)
|   |   Csrf.php                # CSRF protection handler
|   |
|   \---utils                  # Utility functions used across the project
|           AuthUtils.php       # Helper functions for authentication
|           DateUtils.php       # Helper functions for date management
|           DbUtils.php         # Helper functions for database operations
|           FileUtils.php       # Helper functions for file operations
|           GeoUtils.php        # Helper functions for geolocation
|           MailUtils.php       # Helper functions for email management
|           StrUtils.php        # String manipulation utilities
|           UrlUtils.php        # URL manipulation utilities
|
+---config                     # Configuration files for the project
|       config.php              # Main project configuration
|       routes.json             # Defines the routes for the front-end and admin
|
+---controllers                # Front-end controllers
|       ContactController.php   # Handles the contact page
|       Controller.php          # Base controller for front-end
|       HomeController.php      # Handles the home page
|       PageController.php      # Handles static pages
|
+---core                       # Core functionality for the front-end
|       Router.php              # Handles front-end routing
|
+---handlers                   # Request handlers for specific actions
|       send_comment.php        # Handles the submission of comments
|
+---libs                       # External libraries
|   \---phpmailer               # PHPMailer library for sending emails
|
+---models                     # Front-end models
|       Article.php             # Manages articles
|       Content.php             # Manages content items
|       Location.php            # Manages location-based data
|       NavItem.php             # Manages navigation items
|       Page.php                # Manages static pages
|       View.php                # Manages view-specific data
|       Widget.php              # Manages widgets on the front-end
|   \---dto                    # Data transfer objects
|           Contact.php         # DTO for contact form submissions
|
+---public                     # Public files accessible by users
|   |   .htaccess               # Public directory configuration for Apache
|   |   index.php               # Front-end entry point
|   |   robots.txt              # Robots.txt file for SEO
|   |   sitemap.xml             # Sitemap for search engines
|   |
|   +---common                 # Shared assets for the front-end (css, images, js)
|   |
|   +---medias                 # Uploaded media files
|   |   +---article             # Media related to articles
|   |   +---page                # Media related to pages
|   |   +---slide               # Media related to slides
|   |   +---widget              # Media related to widgets
|   |
+---services                   # Front-end services
|       ArticleService.php      # Service for managing articles
|       CommentService.php      # Service for managing comments
|       MenuService.php         # Service for managing menus
|       PageService.php         # Service for managing pages
|       SiteContext.php         # Manages the current site context (language, etc.)
|       WidgetService.php       # Service for managing widgets
|
+---setup                      # Setup scripts for installation
|   |   config-tmp.php          # Temporary config file used during setup
|   |   db.sql                  # SQL file to create the initial database structure
|   |
|   +---controllers            # Controllers used during setup
|   |       SetupController.php # Handles the setup process
|   |
|   \---views                  # Views used during the setup process
|           setup.php           # Setup page view
|
\---templates                  # Front-end templates
    \---default                # Default front-end template
        +---assets             # Static assets for the front-end (css, js, images)
        +---views              # Template views
        |   |   404.php         # 404 error page view
        |   |   article.php     # Article page template
        |   |   home.php        # Home page template
        |   |   page.php        # Static page template
        |
        \---partials           # Reusable partial views
                header.php      # Header for the front-end
                footer.php      # Footer for the front-end
                comments.php    # Comments section for articles
```

## Usage

- **Front-office**: Managed through dynamic routing that handles both language and content. Simply create pages and articles in the admin, and the URLs will be generated accordingly.
- **Back-office**: Each module in the admin follows a standardized structure (list, add/edit, settings), and new modules can be added easily by creating a directory in `/admin/modules/`.

## Customization

- **Adding Modules**: Simply create a new directory under `/admin/modules/` with your `config.xml`, controller, and views. The system will dynamically pick up the new module.
- **Templating**: Front-end templates are located in `/templates/` with all static assets like logo, favicon...
- **Multilingual Support**: Define your content per language by enabling the multi-language feature in `config/` or from the admin panel > settings page.

## Requirements

- PHP >= 7.4 with PDO/MySQL
- Composer
- MySQL or MariaDB
- Apache or Nginx with mod_rewrite enabled (for .htaccess routing)

## License

Solutions CMS is licensed under the MIT License. See [LICENSE](LICENSE) for more details.

## Contact

For any questions or inquiries, feel free to contact us at [support@pandao.eu](mailto:support@pandao.eu).
Author: Pandao