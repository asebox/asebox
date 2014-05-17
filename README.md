Asebox Sybase Monitoring Tools
==============================


What is Asebox?
---------------
 
Asebox is a tool for monitoring Sybase servers (ASE , RS , IQ, RAO)   It is comprised of two components : A *logger*, which periodically collects metrics from a *monitored server*, and archives them in an *archive database*.   A *reporter*, the *query* and *reporting* part, installed in a Web Server.  With these pages, you can query the *archive database* in order to analyse the data captured by the *logger*.   

Asebox Modules
--------------

* Performance Monitoring –  based on historised MDA tables (extends asemon, by Jean-Paul Martin).
* Server Comparison - compare performance and configuration between servers or periods.
* Application Tracing  - based on application tracing and logging tools.
* Sybase Auditing – based on sybsecurity and native sybase auditing.
* Data Dictionary - based on Sybase system tables.
* Testing Package - extract production activity in real-time, and replay in a test environment.
* Supervision - application activity and availability monitoring (compatible with industry standard Nagios)
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
* Documentation available at [Wiki](https://github.com/asebox/asebox/wiki "ASEBOX Wiki")
* Enterprise class support, training, and consulting available. 

Contributing 
------------

Anyone and everyone is welcome to contribute. Please take a moment to
review the [guidelines for contributing](CONTRIBUTING.md).

* [Bug reports](CONTRIBUTING.md#bugs)
* [Feature requests](CONTRIBUTING.md#features)
* [Pull requests](CONTRIBUTING.md#pull-requests)

Installation
------------
First, you have to [install asemon_logger](https://github.com/asebox/asebox/wiki/Installing-Logger "install asemon logger")  on a server (Windows, Unix or Linux).  
Second, you have to [install asemon_report](https://github.com/asebox/asebox/wiki/Installing-Reporter "install asemon report") on a web server (for PHP pages). 

