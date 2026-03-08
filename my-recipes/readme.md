# My Recipes Plugin

A custom WordPress plugin that fetches recipes from the Spoonacular API and registers a custom post type (`recipe`) with block editor support.

## ✅ Features

- Custom post type: `recipe`
- Automatically fetches recipes from Spoonacular API
- Saves recipe data, ingredients, cooking time, and images
- Registers a Gutenberg block for editing
- Admin fetch endpoint (`/wp-admin/admin-post.php?action=fetch_spoonacular`)
- Auto-fetches on plugin activation

## 🔧 Installation

1. Clone/download this plugin into your WordPress `wp-content/plugins/` directory.
2. Activate the plugin via the WP admin dashboard.
3. In your `wp-config.php`, add this line with your Spoonacular API key:

   ```php
   define( 'SPOONACULAR_API_KEY', 'your_api_key_here' );
