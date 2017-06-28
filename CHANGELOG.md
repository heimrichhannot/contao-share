# Changelog
All notable changes to this project will be documented in this file.

## [1.1.2] - 2017-06-28

### Fixed
- workaround for tcpdf php7 incompatibility

### Added
- pdf constant in `Share`

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
