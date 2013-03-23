Asebox Sybase Monitoring Tools
==============================


What is Asebox?
---------------

Asebox is a tool for monitoring Sybase servers (ASE , RS , IQ, RAO)   It is comprised of two components : A *logger*, which periodically collects metrics from a *monitored server*, and archives them in an *archive database*.   A *reporter*, the *query* and *reporting* part, installed in a Web Server.  With these pages, you can query the *archive database* in order to analyse the data captured by the *logger*.   

Asebox Modules
--------------

* Performance Monitoring –  based on historised MDA tables (extends asemon, by Jean-Paul Martin).
* Application Auditing – based on sybsecurity and native sybase auditing.
* Application Tracing  - based on application tracing and logging tools.
* Server Comparison - compare performance and configuration between servers or periods.
* Data Dictionary - based on Sybase system tables.
* Testing Package - extract production activity in real-time, and replay in a test environment.
* Application Supervision - application activity and availability monitoring (compatible with industry standard Nagios)
* Quality Tracking - summary defect reports and code quality indicators (requires SQLBrowser)

Asebox Audience
---------------

* Administrators - both system, and application tuning have never been easier.
* Developers - identify problem code, and contention issues.
* Testers - Replay tools, regression testing, integration testing, and stress testing. 
* Support - Proactively identify problems, and produce detailed analysis reports.
* Auditors - Reports giving overview, analysis and recommendations.
* Managers - keep your DBAs, and developers on their toes.

Support
-------
* Enterprise class support, training, and consulting available. 
* Documentation available at [Wiki](https://github.com/asebox/asebox/wiki "ASEBOX Wiki")

Installation
------------
First, you have to install asemon_logger on a server (Windows, Unix or Linux) See Asemon logger installation for details. Second, install asemon_report (PHP pages in a web server). See Asemon report installation for details.
