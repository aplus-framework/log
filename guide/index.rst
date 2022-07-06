Log
===

.. image:: image.png
    :alt: Aplus Framework Log Library

Aplus Framework Log Library.

- `Installation`_
- `Getting Started`_
- `Log Levels`_
- `Loggers`_
- `Conclusion`_

Installation
------------

The installation of this library can be done with Composer:

.. code-block::

    composer require aplus/log

Getting Started
---------------

The Log Library allows you to save logs in several ways.

In the constructor of the Logger class it is possible to set the **destination**,
the **level** and configurations through the **config** parameter.

Let's see an example saving logs with CRITICAL level (5) or higher in the app.log
file:

.. code-block:: php

    use Framework\Log\Loggers\FileLogger;
    use Framework\Log\LogLevel;

    $destination = __DIR__ . '/app.log';
    $level = LogLevel::CRITICAL;
    $logger = new FileLogger($destination, $level);

    $logger->logDebug('Debug message'); // bool
    $logger->logCritical('Critical message'); // bool
    $logger->logAlert('Alert message'); // bool

Note that logs with a level lower than CRITICAL (5) are not saved in the
destination.

Log Levels
----------

Logs can be defined at eight different levels:

- `DEBUG`_
- `INFO`_
- `NOTICE`_
- `WARNING`_
- `ERROR`_
- `CRITICAL`_
- `ALERT`_
- `EMERGENCY`_

DEBUG
#####

Level 0: Detailed debug information.

INFO
####

Level 1: Interesting events.

Example: User logs in, SQL logs.

NOTICE
######

Level 2: Normal but significant events.

WARNING
#######

Level 3: Exceptional occurrences that are not errors.

Example: Use of deprecated APIs, poor use of an API, undesirable things
that are not necessarily wrong.

ERROR
#####

Level 4: Runtime errors that do not require immediate action but should
typically be logged and monitored.

CRITICAL
########

Level 5: Critical conditions.

Example: Application component unavailable, unexpected exception.

ALERT
#####

Level 6: Action must be taken immediately.

Example: Entire website down, database unavailable, etc. This should
trigger the SMS alerts and wake you up.

EMERGENCY
#########

Level 7: System is unusable.

Loggers
-------

The Log Library has five different loggers:

- `Email Logger`_
- `File Logger`_
- `Multi File Logger`_
- `SAPI Logger`_
- `Sys Logger`_

Email Logger
############

The message is sent by email to the address in the destination parameter.

.. code-block:: php

    use Framework\Log\Loggers\EmailLogger;

    $destination = 'sysadmin@domain.tld';
    $logger = new EmailLogger($destination);

In the third parameter of the constructor, config, you can set custom headers
for the message:

.. code-block:: php

    $logger = new EmailLogger($destination, config: [
        'headers' => [
            'From' => 'system@domain.tld',
            'Subject' => 'System Log',
        ],
    ]);

File Logger
###########

The message is appended to the file destination.

.. code-block:: php

    use Framework\Log\Loggers\FileLogger;

    $destination = __DIR__ . '/app.log';
    $logger = new FileLogger($destination);

Multi File Logger
#################

The message is appended to a file with the date in the filename with a directory
as destination.

.. code-block:: php

    use Framework\Log\Loggers\MultiFileLogger;

    $destination = __DIR__ . '/logs';
    $logger = new MultiFileLogger($destination);

The filename has the following format: ``Y-m-d.log``

SAPI Logger
###########

The message is sent directly to the SAPI logging handler.

.. code-block:: php

    use Framework\Log\Loggers\SAPILogger;

    $logger = new SAPILogger();

Sys Logger
##########

The message is sent to PHP's system logger, using the Operating System's system
logging mechanism or a file.

.. code-block:: php

    use Framework\Log\Loggers\SysLogger;

    $logger = new SysLogger();

Conclusion
----------

Aplus Log Library is an easy-to-use tool for, beginners and experienced, PHP developers. 
It is perfect for saving logs with different destinations. 
The more you use it, the more you will learn.

.. note::
    Did you find something wrong? 
    Be sure to let us know about it with an
    `issue <https://gitlab.com/aplus-framework/libraries/log/-/issues>`_. 
    Thank you!
