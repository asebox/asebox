This bundle can be used if you don't already have Apache, PHP and eventually Sybase Open Client installed on your machine
When Apache is stated you can access to the ASEMON_REPORT PHP pages to view your historized
data recorded with ASEMON_LOGGER

Step to install :
---------------

1) unzip the distribution in "c:"
(all files will be copied under c:\AsemonReportSrv)
If you unzip elsewhere, you have to manually change all the references to c:\AsemonReportSrv 
in the two files :

C:\AsemonReportSRV\Apache2\conf\httpd.conf
C:\AsemonReportSRV\php-5.3.10\php.ini


2) If you don't have Open Client on your machine :
change the "C:\AsemonReportSRV\Sybase\ini\sql.ini" file according to your installation
 


Step to start Apache :
--------------------

1) open a cmd window 
2) cd c:\AsemonReportSrv
3) eventually, execute the "setup.bat" script if you don't have Open Client installed on your machine
4) startApache.bat
(this window must stay opened)

It is possible to start Apache as a service (go to "C:\AsemonReportSRV\Apache2\bin" and execute "httpd -?" to get help)


How to get ASEMON_REPORT pages :
------------------------------

1) start IE or FIREFOX
2) enter "http://localhost/asebox_report/compare_report.php"
