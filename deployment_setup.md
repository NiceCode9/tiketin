# Deployment Setup Guide

This guide details the server configurations required to run the background processes for the retry mechanism and scheduled tasks.

## 1. Cron Job (Scheduler)

The Laravel scheduler handles the `orders:cancel-expired` command, which runs every minute to clean up unpaid orders.

Add the following Cron entry to your server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Replace `/path-to-your-project` with the absolute path to your Laravel application.

## 2. Supervisor (Queue Worker)

Supervisor is a process monitor that keeps the queue worker running. This worker processes the [ProcessMidtransWebhook](file:///d:/Web%20Personal/tiketin/app/Jobs/ProcessMidtransWebhook.php#11-62) jobs.

### Configuration (`/etc/supervisor/conf.d/tiketin-worker.conf`)

Create a new configuration file:

```ini
[program:tiketin-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killsignal=SIGQUIT
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log
stopwaitsecs=3600
```

**Important:**
- Replace `/path-to-your-project` with your actual project path.
- Ensure the `user` matches your web server user (e.g., `www-data` or your user).

### Commands to Start Supervisor

1.  Reread configuration:
    ```bash
    sudo supervisorctl reread
    ```
2.  Update process group:
    ```bash
    sudo supervisorctl update
    ```
3.  Start the worker:
    ```bash
    sudo supervisorctl start tiketin-worker:*
    ```

## 3. Verify Operations

### Check Scheduler
Run manually to test:
```bash
php artisan schedule:run
```

### Check Queue Worker
Run manually to test (stop Supervisor first):
```bash
php artisan queue:work
```
