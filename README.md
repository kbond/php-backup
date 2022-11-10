# zenstruck/backup

Create and archive backups. [A Symfony Bundle](https://github.com/kbond/ZenstruckBackupBundle) and
a [Laravel package](https://github.com/vinkla/backup) is available that wraps this library.

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
