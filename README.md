Instructions using GUI and Commands

    Download GitHub Desktop
    Clone Project https://github.com/it-dev-fumaco/mes-dev.git
    Locate Project Folder on your local PC
    Open CMD and change directory to your Local project folder
    Type copy .env.example .env
    Create "sessions" folder in /storage/framework
    Setup database connections in config/database.php
        - ESSEX (for dev)
            - host = 10.0.48.82
            - database name = 'essex'
            - username = it2
            - password = 'fumaco'
        - ERP (for dev)
            - host = 10.0.48.85
            - database name = '3f2ec5a818bccb73'
            - username = erp
            - password = 'fumaco'
        - MES (for dev)
            - host 10.0.48.82
            - database name = 'mes'
            - username = it2
            - password = 'fumaco'
    Type php artisan key:generate
    Type php artisan optimize
    Type php artisan serve
    Access it via URL using your IP or localhost with default port = 8000
    Open VsCode

Note: Please specify the Summary and Description on your every commit﻿ Finalize and Review Your Code before Pushing to Dev Branch


        Release notes - Manufacturing Execution System - Version Version 7.6  (MES Phase 3)
    
<h2>        Story
</h2>
<ul>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-712'>MES-712</a>] -  Display the new code of FG in planning wizzard</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-710'>MES-710</a>] - Display original code with new code of FG in sales order tracking</li>
<li>Override completed qty and status of spotwelding workstation once painting is completed</li>
</ul>

Instructions after pulling master branch on live MES Server
    - run command "php artisan optimize"
    - go to bootstrap folder, then delete "config" file
    - go to storage/framework, then remove all files inside "sessions" folder
    
