# zenstruck/backup

[![Build Status](http://img.shields.io/travis/kbond/php-backup.svg?style=flat-square)](https://travis-ci.org/kbond/php-backup)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/kbond/php-backup.svg?style=flat-square)](https://scrutinizer-ci.com/g/kbond/php-backup/)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/kbond/php-backup.svg?style=flat-square)](https://scrutinizer-ci.com/g/kbond/php-backup/)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/c4035c63-56a5-4498-99be-d143938384af.svg?style=flat-square)](https://insight.sensiolabs.com/projects/c4035c63-56a5-4498-99be-d143938384af)
[![StyleCI](https://styleci.io/repos/45110395/shield)](https://styleci.io/repos/45110395)
[![Latest Stable Version](http://img.shields.io/packagist/v/zenstruck/backup.svg?style=flat-square)](https://packagist.org/packages/zenstruck/backup)
[![License](http://img.shields.io/packagist/l/zenstruck/backup.svg?style=flat-square)](https://packagist.org/packages/zenstruck/backup)

Create and archive backups. [A Symfony Bundle is available](https://github.com/kbond/ZenstruckBackupBundle)
that wraps this library.

An "executor" takes a backup profile and processes it. A backup "profile" consists of 4 parts:

1. **Source(s)**: What to backup (ie database/files). The source fetches files and copies them to a "scratch"
   directory. These files are typically persisted between backups (improves rsync performance) but can be
   cleared by the "executor".
2. **Processor**: Convert to a single file (ie zip/tar.gz). This step uses a **Namer** to name the file.
3. **Namer**: Generates a filename to be used by the above processor.
4. **Destination**: Where to send the backup (ie filesystem/S3).

## Installation

1. Install this library:

        composer require zenstruck/backup

2. (Optional) Install process (used by some Destinations, Sources and Processors):

        composer require symfony/process

3. (Optional) Install console (for the console command):

        composer require symfony/console
