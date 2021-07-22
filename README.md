# Renderer for audiobooks

This is test task for creating renderer for separation autiobook for chapters and subcharters using data from xml file

### Table of contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Links](#links)

# Requirements

* PHP >= 7.4:
* Git

## Installation

Clone this repository:
`git clone <repository_address>`.

Make sure that directory `result` has write permissions

Run command:
`php parse.php --f=silence1.xml --cs=3 --mct=1800 --lcs=2`

This script must retrieve next parameters. `!All parameters are required!`:
- `f` - (string) The path to an XML file with silence intervals. Example: silence1.xml
- `cs` - (seconds|float) The silence duration which reliably indicates a chapter transition
- `mct` - (seconds|float) The maximum duration of a segment, after which the chapter will be broken up into multiple segments
- `lcs` - (seconds|float) A silence duration which can be used to split a long chapter (always shorter than the silence duration used to split chapters)

## Links

Links below can help to understand the project better.

* [PHP](https://www.php.net)
* [DateInterval](https://www.php.net/manual/ru/class.dateinterval.php)
* [SimpleXMLElement](https://www.php.net/manual/ru/class.simplexmlelement.php)