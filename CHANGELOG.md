# Changelog
All notable changes to this project will be documented in this file.

## [1.12.2] - 2023-08-11
- Fixed: exception when using print function in some cases

## [1.12.1] - 2022-11-30
- Fixed: compatibility with FrontendModuleController

## [1.12.0] - 2022-11-14
- Changed: make ContentModule override less invasive

## [1.11.0] - 2022-04-05
- Changed: allow php 8
- Changed: updated license

## [1.10.0] - 2021-03-19
- added new dependency
- fixed filenames to be generated correctly, removed urlencoding of file names

## [1.9.1] - 2019-12-04
- fixed error in composer.json

## [1.9.0] - 2019-12-04
- replaced contao-legacy/ical_creator dependency with kigkonsult/icalcreator
- updated some namespaces

## [1.8.1] - 2019-05-09

### Changed
- replaced WhatsApp:// with `whatsapp click to chat url` (https://wa.me/?text=)

## [1.8.0] - 2019-05-09

### Added
- whatsapp and linkedin syndication

### Changed
- removed gplus syndication

## [1.7.3] - 2019-04-02

#### Removed
- print versions from search index

## [1.7.2] - 2019-02-21

#### Fixed
- ical generation not using corrent event id

## [1.7.1] - 2018-09-21

#### fixed
- fixed printing in safari. added delay of function call window.print cause otherwise the content is not loaded yet

## [1.7.0] - 2018-05-29

#### Added
- mpdf 7.0 compatibility

## [1.6.0] - 2018-03-21

#### Added
- option to add share urls to template
- updated translations

## [1.5.2] - 2018-02-19

#### Fixed
- bug introduced in 1.5.1

## [1.5.1] - 2018-02-19

#### Fixed
- no pdf rendering if called via compiler method

#### Changed
- code enhancements

## [1.5.0] - 2018-01-16

#### Added
- add share to articles
- force print without template (=print page) even if print template is set (added printWithoutTemplate option)

## [1.4.1] - 2017-10-10

### Fixed
- clear the buffer before Template if generated
- `<!-- print::stop-->` and `<!-- print::continue-->` typo

## [1.4.0] - 2017-10-10

### Fixed
- print with `tl_module.share_customPrintTpl` under contao 4 now works

### Changed
- set href for print link to `javascript:window.print();` if no `tl_module.share_customPrintTpl` is set

## [1.3.6] - 2017-09-25

### Changed
- replace `$this->strTemplate` with `$this->customTpl` removed from `share` class as modules, as customTpl should be something like `share_*` 

## [1.3.5] - 2017-08-21

### Fixed
- feedback integration

## [1.3.4] - 2017-08-08

### Fixed
- mailto

## [1.3.3] - 2017-08-08

### Fixed
- PDF generation with Contao 3
- PHP 5.6 compatiblity

## [1.3.2] - 2017-08-08

### Fixed
- mailto integration

## [1.3.1] - 2017-08-01

### Added
- optimized palette handling

## [1.3.0] - 2017-07-31

### Added
- mailto-syndication
- refactoring of palette handling
- fixed url encoding

## [1.2.6] - 2017-07-17

### Added
- started decoubling pdf renderer
- WkhtmltopdfModule class
- PdfModule class 
- added authentication option to wkhtmltopdf

### Fixed
- missing attribute in Share->generatePDF


## [1.2.5] - 2017-07-14

### Fixed
- wrong pdf print template with multiple share instances on same page

## [1.2.4] - 2017-07-12

### Added
- wkhtmltopdf for pdf generation
- option to output pdf inline or as download (for all pdf renderer)
- ModulePdfInterface for PDF naming

### Changed
- PDF-Options in the backend will only show up, if the pdf renderer support the options

## [1.2.3] - 2017-07-03

### Added
- generateSocialLink Method to `Share` with Contao-version check

### Fixed
- not working share links in contao 4

### Changed
- renamed some variables to make code more readable

## [1.2.2] - 2017-07-03

### Fixed
- print dialog not opening

## [1.2.1] - 2017-07-03

### Fixed
- call renderPrintModule instead of pdf
- composer typo

## [1.2.0] - 2017-06-29

### Added
- option to choose mpdf for pdf generation
- PDFPage class
- pdf and ical constant in `Share`
- generateHead-Method in `PrintPage`
- added mpdf to composer suggest

### Fixed
- workaround for tcpdf php7 incompatibility
- error in documentation-comment

## [1.1.1] - 2017-05-09

### Added
- php 7 support

## [1.1.0] - 2017-04-05

### Added
- if a custom print template is set within module configuration, a custom print page will be created, with better debug abilities
- ob_clean before pdf output

## [1.0.4] - 2017-01-24

### Fixed
- pdf print within heimrichhannot/contao-modal (check against addShare on module config)
- ob_clean before pdf output

## [1.0.3] - 2016-12-05

### Fixed
- remove $_GET parameters from share link urls
