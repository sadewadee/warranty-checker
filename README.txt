=== Warranty Checker ===
Contributors: yourname
Tags: warranty, checker, google sheets, customer service, woocommerce
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Mengelola dan menyediakan sistem pengecekan garansi secara online dengan import dari Google Sheets.

== Description ==

Warranty Checker adalah plugin WordPress yang memungkinkan bisnis untuk mengelola dan menyediakan sistem pengecekan garansi secara online. Plugin ini mengimport data garansi dari Google Sheets, menyimpannya ke database WordPress, dan menyediakan form pencarian garansi yang dapat ditempatkan di mana saja menggunakan shortcode.

= Key Features =

* Import data garansi dari Google Sheets
* Database management dengan custom table
* Shortcode untuk form pengecekan garansi
* AJAX-based search tanpa reload
* Standard template dengan 11 kolom
* Auto-update warranty status
* Admin dashboard untuk data management
* Multi-language support
* Rate limiting untuk prevent abuse

= How It Works =

1. Upload warranty data ke Google Sheets menggunakan template yang disediakan
2. Share Google Sheets dengan "Anyone with the link can view"
3. Paste link di plugin settings dan import
4. Tambahkan shortcode `[warranty_checker]` ke halaman/post
5. Customer dapat search warranty mereka menggunakan warranty number atau invoice number

= Requirements =

* PHP 7.4 atau lebih tinggi
* MySQL 5.6 atau MariaDB 10.1 atau lebih tinggi
* WordPress 5.8 atau lebih tinggi

== Installation ==

1. Upload plugin folder ke `/wp-content/plugins/` directory
2. Activate plugin melalui 'Plugins' menu di WordPress
3. Go to 'Warranty Checker' menu di admin panel
4. Configure settings dan import data dari Google Sheets
5. Use shortcode `[warranty_checker]` di page atau post

== Frequently Asked Questions ==

= Bagaimana cara import data? =

1. Download template CSV dari tab "Template & Help"
2. Buat Google Sheets baru dan copy template format
3. Tambahkan data warranty Anda
4. Share sheets dengan "Anyone with the link can view"
5. Copy link dan paste di tab "Import Data"
6. Click "Import Now"

= Apakah data warranty aman? =

Ya, semua data disimpan di database WordPress Anda. Plugin menggunakan prepared statements untuk prevent SQL injection dan semua input disanitize.

= Apakah bisa auto-import? =

Ya, Anda bisa enable auto-import di settings dan choose schedule (daily atau weekly).

= Bagaimana cara custom tampilan form? =

Anda bisa customize form title, placeholder, dan button text di tab "Form Settings". Untuk styling lebih lanjut, gunakan CSS custom.

== Screenshots ==

1. Admin dashboard dengan tabs
2. Import data dari Google Sheets
3. Data management dengan search dan filter
4. Form settings customization
5. Frontend warranty checker form
6. Search result display

== Changelog ==

= 1.0.0 =
* Initial release
* Import dari Google Sheets
* Custom database table
* AJAX warranty search
* Admin dashboard dengan 4 tabs
* Standard template dengan 11 kolom
* Auto-update warranty status via WP-Cron
* Rate limiting & security features
* Multi-language support
* Shortcode dengan customizable parameters

== Upgrade Notice ==

= 1.0.0 =
Initial release of Warranty Checker plugin.
