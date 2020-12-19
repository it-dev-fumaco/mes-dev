Instructions using GUI and Commands

    Download GitHub Desktop
    Clone Project https://github.com/it-dev-fumaco/mes-dev.git
    Locate Project Folder on your local PC
    Open CMD and change directory to your Local project folder
    Type copy .env.example .env
    Create "sessions" folder in /storage/framework
    Setup database connections in config/database.php
        - ESSEX (for dev)
            - host = 10.0.0.82
            - database name = 'essex'
            - username = it2
            - password = 'fumaco'
        - ERP (for dev)
            - host = 10.0.1.85
            - database name = '3f2ec5a818bccb73'
            - username = erp
            - password = 'fumaco'
        - MES (for dev)
            - host 10.0.0.82
            - database name = 'mes'
            - username = it2
            - password = 'fumaco'
    Type php artisan key:generate
    Type php artisan optimize
    Type php artisan serve
    Access it via URL using your IP or localhost with default port = 8000
    Open VsCode

Note: Please specify the Summary and Description on your every commit﻿ Finalize and Review Your Code before Pushing to Dev Branch


        Release notes - Manufacturing Execution System - Version Version 7.5  (MES Phase 3)
    
<h2>        Story
</h2>
<ul>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-729'>MES-729</a>] -         Merging of redundant process name displaying with total number of good in production search modal
</li>
</ul>
    
<h2>        Bug
</h2>
<ul>
<li>- Production Order with status "Cancelled" in Production Order Search tab status.
</li>
<li>- No In Progress in timelogs but blinking in Production order search.
</li>
<li>- Production Order is cancelled but its shows in action needed (backlogs).
</li>
<li>- Error in editing production order in painting.
</li>
<li>- For scheduling date, please indicate the word "Today" to easily distinguished today schedule in Production Schedule Monitoring.
</li>
<li>- Over all status completed showed even the process is still in progress.
</li>
<li>- Error in fabrication inventory page. 
</li>
<li>- Not related fabrication items showed in item selection (Stock Adjustment form in fabrication inventory).
</li>
</ul>

