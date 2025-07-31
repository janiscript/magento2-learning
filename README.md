# Magento 2 Learning Project

This repository contains custom Magento 2 development work including custom modules and themes created during the Magebit Academy course.

## Project Structure

### Custom Modules

* **Magebit_PageListWidget** - CMS Page List Widget module that provides configurable widget for displaying CMS pages

## Custom Module: Magebit_PageListWidget

A custom widget that allows displaying a configurable list of CMS pages.

### Features:

* ✅ Configurable title field
* ✅ Display mode options (All Pages / Specific Pages)
* ✅ Multi-select for specific page selection
* ✅ Store-aware functionality
* ✅ Professional styling with hover effects
* ✅ Responsive design and accessibility features
* ✅ PHPDoc documentation for all methods
* ✅ Service contracts usage
* ✅ Magento 2 best practices

### File Structure:

```
app/code/Magebit/PageListWidget/
├── etc/
│   ├── module.xml
│   └── widget.xml
├── Block/Widget/
│   └── PageList.php
├── Model/Config/Source/
│   └── CmsPage.php
├── view/frontend/
│   ├── templates/widget/
│   │   └── page_list.phtml
│   └── web/css/
│       └── page-list-widget.css
└── registration.php
```

### Usage:

1. Go to **Content > Elements > Widgets** in Magento Admin
2. Create a new widget and select **CMS Page List Widget**
3. Configure the widget:
   - **Title**: Optional title to display above the page list
   - **Display Mode**: Choose "All Pages" or "Specific Pages"
   - **Selected Pages**: Multi-select field (appears when "Specific Pages" is selected)
4. Assign to layout and save

## Installation

This is a clean Magento 2.4.x installation with custom development modules.

### Enable Custom Modules:

```bash
php bin/magento module:enable Magebit_PageListWidget
php bin/magento setup:upgrade
php bin/magento cache:flush
```

## Requirements

- PHP 8.1+
- MySQL/MariaDB
- Composer
- Elasticsearch/OpenSearch

## Development

This project follows Magento 2 coding standards and best practices:

* PHPDoc comments for all methods
* Proper file structure
* Service contracts usage
* Responsive design implementation
* Accessibility features

## Course Progress

* ✅ CMS Page List Widget Module
* 🔄 Additional modules and themes (in progress)

## Author

Created as part of Magebit Academy Magento 2 Development Course. 