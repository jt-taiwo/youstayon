Recommended Daily Workflow

From now on, your development routine should be:

1. Start XAMPP

Start only:

✅ Apache

Leave:

❌ MySQL (MariaDB)

stopped.

2. Oracle MySQL

This should already be running automatically as the Windows service:

MySQL80

You can verify anytime with:

Get-Service MySQL80

Expected:

Status : Running
3. Open the project
cd C:\YouStayOn\backend
4. Start Laravel
php artisan serve

or later, when we add queues:

composer run dev
5. Database administration

Use MySQL Workbench for:

Schema design
Viewing tables
Running SQL
Import/export
Query analysis

No need to use phpMyAdmin anymore for this project.