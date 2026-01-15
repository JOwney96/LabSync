# Project Prerequisites & Setup

Before you can run **LabSync**, you must have the following tools installed on your machine.

## 1. Core Software

### PHP

- **Version:** 8.2 or higher
- **Check installed version:** `php -v`
- *Windows Users:* We recommend installing **Laragon** or **XAMPP**.
- *Mac Users:* Use **Laravel Valet** or Homebrew.

### Composer (PHP Dependency Manager)

- **Required to install Laravel libraries.**
- **Check installed version:** `composer -V`
- [Download Composer](https://getcomposer.org/)

### Node.js & NPM (Asset Management)

- **Required for Tailwind CSS.**
- **Version:** Node 18+ recommended.
- **Check installed version:** `node -v` and `npm -v`
- [Download Node.js](https://nodejs.org/)

### Git (Version Control)

- **Check installed version:** `git --version`

---

## 2. IDE Setup: PhpStorm (Highly Recommended)

We have free access to JetBrains PhpStorm via our student licenses. It provides superior autocomplete for Laravel.

### Essential Plugins

Go to **Settings/Preferences** $\rightarrow$ **Plugins** $\rightarrow$ **Marketplace** and install these:

1. **Laravel Idea (The "Superpower"):**
    * *Note:* This is normally a paid plugin, but you can often get a free license with your .edu email or use the free
      **"Laravel"** plugin by Ryuta Hamasaki as an alternative.
    * **What it does:** It understands Laravel "magic." If you type `User::where(...)`, it knows exactly what database
      columns are available. It generates code for you.

2. **Alpine.js Support:**
    * **Why:** PhpStorm doesn't strictly recognize Alpine syntax (like `x-data` or `@click`) by default.
    * **What it does:** Adds autocomplete for Alpine directives so you don't get yellow warning squiggles in your HTML.

3. **Tailwind CSS (Built-in):**
    * *Note:* This is usually pre-installed. Ensure it is enabled in Plugins.
    * **What it does:** Gives you a color preview in the gutter and autocompletes classes like `bg-blue-500`.

4. **PlantUML Integration:**
    * **Why:** To view our `.puml` diagram files.
    * **Critical Setup:** You must install **Graphviz** on your computer for this to render diagrams locally.
        * *Windows:* `choco install graphviz` (or download installer).
        * *Mac:* `brew install graphviz`.
    * *Alternative:* In Plugin settings, set it to use a "Remote Renderer" (like `http://www.plantuml.com/plantuml`) if
      you don't want to install Graphviz.

5. **AWS Toolkit for JetBrains (Optional):**
    * **Why:** Since we are using AWS, this lets you explore our S3 buckets and RDS databases directly inside the IDE.

6. **.env files support:**
    * **What it does:** syntax highlighting for our environment variables.

### Recommended Settings

1. **Enable Blade Support:** PhpStorm handles `.blade.php` files natively, but ensure "PHP" language level is set to
   8.2 (matching our server) in **Settings $\rightarrow$ PHP**.
2. **Terminal:** Use the built-in terminal (`Alt+F12`) to run your `php artisan` commands.

### Code Style & Formatting (Crucial!)

To keep our code looking clean and consistent across everyone's computers, please enable **Actions on Save**. This makes
PhpStorm automatically fix your code every time you press `Ctrl + S`.

**How to Enable:**

1. Open **Settings** (Windows) or **Preferences** (Mac).
2. Navigate to **Tools** $\rightarrow$ **Actions on Save**.
3. Check the following boxes:
    * [ ] **Reformat code:** (Select "Whole file" or "Changed lines").
    * [ ] **Optimize imports:** (Removes unused `use` statements at the top of PHP files).
    * [ ] **Rearrange code:** (Keeps methods ordered efficiently).
    * [ ] **Run Code Cleanup:** (Applies fixes from code cleanup inspection).

**Why do this?**
It prevents merge conflicts caused by invisible whitespace changes and ensures we all follow standard PSR-12 coding
standards without thinking about it.

## 3. AWS Configuration (For S3/RDS Access)

*Note: You only need this if you are testing S3 uploads locally.*

1. **AWS CLI:** [Install AWS CLI](https://aws.amazon.com/cli/)
2. **Configure:**
   ```
   aws configure
   ```
    * Ask the Team Lead for the Access Key ID and Secret Access Key.
