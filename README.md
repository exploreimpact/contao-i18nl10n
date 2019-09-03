
# Contao Multilingual Pages [i18nl10n]

[![Contao 4 compatible](https://img.shields.io/badge/Contao%204-compatible-orange.svg?style=flat-square&logo=data%3Aimage%2Fsvg%2Bxml%3Bbase64%2CPD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iRWJlbmVfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIxNHB4IiBoZWlnaHQ9IjEyLjIzcHgiIHZpZXdCb3g9IjI0LjExNiAyMS4wNDMgMTQgMTIuMjMiIGVuYWJsZS1iYWNrZ3JvdW5kPSJuZXcgMjQuMTE2IDIxLjA0MyAxNCAxMi4yMyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI%2BPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTI1LjA0MiwyMS4wNDNjLTAuNTExLDAtMC45MjUsMC40MTQtMC45MjYsMC45MjR2MTAuMzgxYzAuMDAxLDAuNTExLDAuNDE2LDAuOTI1LDAuOTI2LDAuOTI1SDM3LjE5YzAuNTA5LDAsMC45MjMtMC40MTEsMC45MjYtMC45MjFWMjEuOTY4YzAtMC41MTEtMC40MTUtMC45MjUtMC45MjYtMC45MjRIMjUuMDQyeiIvPjxwYXRoIGZpbGw9IiNGNDdDMDAiIGQ9Ik0yNS4zNjEsMjguNDZjMC4zOTIsMS44MjMsMC43MTgsMy41NiwxLjg2LDQuODFoLTIuMTc5Yy0wLjUwOSwwLTAuOTIzLTAuNDExLTAuOTI2LTAuOTIxVjIxLjk2OGMwLjAwMS0wLjUxMSwwLjQxNi0wLjkyNCwwLjkyNi0wLjkyNGgxLjU3NGMtMC4zODQsMC4zNS0wLjcyMSwwLjc0OC0xLjAwMiwxLjE4NEMyNC4zNjksMjQuMTM2LDI0Ljg4MSwyNi4yMTcsMjUuMzYxLDI4LjQ2eiBNMzcuMTksMjEuMDQzaC0yLjQ3YzAuNTg0LDAuNTg1LDEuMDc4LDEuMzQyLDEuNDUsMi4yODRsLTMuNzU1LDAuNzkyYy0wLjQxMi0wLjc4OS0xLjAzNy0xLjQ0NS0yLjI2MS0xLjE4N2MtMC42NzUsMC4xNDMtMS4xMjMsMC41MjEtMS4zMjEsMC45MzZjLTAuMjQ0LDAuNTExLTAuMzY0LDEuMDg0LDAuMjE5LDMuODA4YzAuNTgzLDIuNzI0LDAuOTI3LDMuMTk4LDEuMzU4LDMuNTY2YzAuMzUxLDAuMjk4LDAuOTE2LDAuNDY0LDEuNTkxLDAuMzIxYzEuMjI1LTAuMjU4LDEuNTI2LTEuMTEsMS41OC0xLjk5N2wzLjc1NS0wLjc5M2MwLjA4OCwxLjk0Mi0wLjUxMywzLjQ0OS0xLjU3NCw0LjQ5MWgxLjQyOGMwLjUwOSwwLDAuOTIzLTAuNDExLDAuOTI2LTAuOTIxVjIxLjk2OEMzOC4xMTYsMjEuNDU2LDM3LjcwMSwyMS4wNDMsMzcuMTksMjEuMDQzeiIvPjwvc3ZnPg%3D%3D)](https://contao.org/)
[![packagist](https://img.shields.io/packagist/vpre/exploreimpact/contao-i18nl10n.svg?style=flat-square)](https://packagist.org/packages/exploreimpact/contao-i18nl10n)
[![packagist](https://img.shields.io/packagist/v/exploreimpact/contao-i18nl10n.svg?style=flat-square)](https://packagist.org/packages/exploreimpact/contao-i18nl10n)
[![License](https://img.shields.io/badge/License-LGPL%20v3-blue.svg?style=flat-square)](http://www.gnu.org/licenses/lgpl-3.0)

**Warning: This Extension is in beta status. DO NOT USE IT ON ANY PRODUCTIVE ENVIRONMENT!**


![i18nl10n Logo](i18nl10n-logo_v2.png)


## About
A module to handle localizations for Contao 4.

By default Contao handles multiple languages by duplicating page trees for
each language (in Contao's Site Structure).
If you have many pages and languages it can become a lot of work to handle
your content and to keep them in sync. Especially, when the content is
similar across languages.
This extension is for you!


## System requirements
* [Contao 4](https://github.com/contao/core-bundle)


## Installation

### 1. Install package
```sh
$ composer require exploreimpact/contao-i18nl10n:2.0.2

# or for latest state of development (not recommended):

$ composer require exploreimpact/contao-i18nl10n:dev-master
```

### 2. Update database
Run a database update via the Installtool afterwards.

### 3. Set URL settings
Open the Contao backend and go to "Settings".
Under "Add language to URL*" select the way the language should be added to your URL.


## Documentation:
**Contao 4**: ... t.b.d. ...  
**Contao 3**: https://github.com/exploreimpact/contao-i18nl10n/wiki  
Note: Most configuration instructions still match for Contao 4 intallations.
  
For changes made in the past please look into [CHANGELOG.md](CHANGELOG.md).


## Support
Claudio De Facci  
<https://exploreimpact.de>  
<claudio@exploreimpact.de>  
