# Database Setup Guide (phpMyAdmin)

This guide explains how to set up the Event Ticketing System database on your own computer using the SQL files in the `database` folder.

## What you need

- XAMPP installed on your computer
- MySQL and Apache running in the XAMPP Control Panel
- The project downloaded or cloned from this GitHub repository

## Step 1: Start XAMPP

1. Open **XAMPP Control Panel**.
2. Click **Start** beside **Apache**.
3. Click **Start** beside **MySQL**.
4. Make sure both services are running.

If MySQL will not start, fix that issue first before continuing.

## Step 2: Open phpMyAdmin

1. Open your browser.
2. Go to: http://localhost/phpmyadmin
3. phpMyAdmin should open. You usually do not need to log in when using the default local XAMPP setup.

## Step 3: Create the database

1. In phpMyAdmin, select **New** in the left sidebar.
2. Enter the database name `event_ticketing`
   - Check the project's README or database connection/configuration file for the exact name.
   - **Important:** Every team member must use the same database name in their local configuration.
3. Click **Create**.

If the project already specifies a database name in its configuration, use that name rather than inventing a new one.

## Step 4: Import `schema.sql`

The schema file creates the database structure, such as tables and relationships.

1. Click the database `event_ticketing` you just created in the left sidebar.
2. Open the **Import** tab.
3. Click **Choose File** and select `database/schema.sql` from the project folder.
4. Keep the default format as **SQL**.
5. Click **Import** or **Go** at the bottom of the page.
6. Wait for phpMyAdmin to show a success message.
7. Check that the tables appear under your database in the left sidebar.

## Step 5: Import `seed.sql`

The seed file inserts initial or sample data into the tables.

1. Make sure you are still inside the same database.
2. Open the **Import** tab again.
3. Select `database/seed.sql`.
4. Keep the format as **SQL**.
5. Click **Import** or **Go**.
6. Check the **Browse** tab for relevant tables to confirm that data was added.

**Import order matters:** import `schema.sql` first, then `seed.sql`, because the seed data usually depends on the tables created by the schema.

## Step 6: Configure the project connection

The application must connect to the database you created.

1. Find the database connection settings in the project (for example, a `.env`, config, or database connection file).
2. Set the host to `localhost` for a standard local XAMPP setup.
3. Set the database name to the exact name you created.
4. Use the username and password configured for your local MySQL.
   - In a default XAMPP setup, the username is often `root` and the password is blank, but your computer may be configured differently.
5. Save the configuration and restart the relevant development server if needed.

Do not commit personal passwords or other secret credentials to GitHub. If the project uses environment variables, share an example configuration file with placeholder values instead of real credentials.

## Step 7: Verify the setup

- The database appears in phpMyAdmin.
- The tables created by `schema.sql` are visible.
- The initial data from `seed.sql` is present, if the seed file contains sample data.
- The project uses the same database name and can connect without an error.

## Common problems

- **`#1049 - Unknown database`:** The database name in the project configuration does not match the database you created.
- **`#1045 - Access denied`:** Check your MySQL username and password.
- **`#1064 - SQL syntax error`:** Check that you selected the correct SQL file and that it is compatible with your MySQL/MariaDB version.
- **Tables already exist:** You may have imported the schema before. Avoid repeatedly importing it into a database that already contains data. Back up anything important before resetting or recreating a database.
- **MySQL will not start:** Resolve the XAMPP/MySQL startup issue first.

## Important note for the team

These steps create a **local copy** of the database on each team member's computer. Changes made to your local database will not automatically appear on your teammates' computers.

When the team changes the database structure, update `database/schema.sql`. When the team changes the initial/sample data, update `database/seed.sql`, then commit and push the updated files so everyone can pull the changes and import them locally.

If you already have important local data, back it up before importing or resetting anything.
