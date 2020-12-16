Instructions using GUI and Commands

    Download GitHub Desktop
    Clone Project https://github.com/it-dev-fumaco/mes-dev.git
    Locate Project Folder on your local PC
    Open CMD and change directory to your Local project folder
    Type copy .env.example .env
    Create "sessions" folder in /storage/framework
    Setup database connections in app/config.app
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
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-622'>MES-622</a>] -         Email alert to sales order owner to notify of new delivery date
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-657'>MES-657</a>] -         Registration of bundled items in manual production order form
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-658'>MES-658</a>] -         Display message info if the selected items is a product bundle.
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-660'>MES-660</a>] -         Create stock entry in ERP for feedbacking as &quot;Material Transfer&quot; for delivery
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-717'>MES-717</a>] -         Link Reschedule Delivery Date modal upon drag in Production Scheduling
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-724'>MES-724</a>] -         Notification to update Item Quantity based on updated UOM in BOM
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-725'>MES-725</a>] -         Display Bundle components with item image in manual production order
</li>
</ul>
    
<h2>        Bug
</h2>
<ul>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-694'>MES-694</a>] -         Error saving delivery date logs in manual production order
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-695'>MES-695</a>] -         Duplicate production orders displaying in list
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-696'>MES-696</a>] -         Duplicate production order displaying in scheduling
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-697'>MES-697</a>] -         Duplicate production order displaying in withdrawal slip
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-699'>MES-699</a>] -         Unable to load ready for feedback list
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-700'>MES-700</a>] -         Unable to view manual production order in list
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-701'>MES-701</a>] -         Blank page in printing withdrawal slip for manual production order in scheduling
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-714'>MES-714</a>] -         Inaccurate transferred/issued qty display in stock withdrawal
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-715'>MES-715</a>] -         Unable to load ready for feedback in Main Dashboard
</li>
<li>[<a href='https://fumacoinc.atlassian.net/browse/MES-718'>MES-718</a>] -         Production order with changed code in sales order item is not displaying in list
</li>
</ul>
