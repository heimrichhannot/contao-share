# Changelog
All notable changes to this project will be documented in this file.

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
