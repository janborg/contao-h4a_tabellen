# Changelog

## [5.0.3](https://github.com/janborg/contao-h4a_tabellen/compare/v5.0.2...v5.0.3) (2026-08-23)


### Bug Fixes

* 500er bei fehlgeschlagener handball.net API-Antwort verhindern ([#182](https://github.com/janborg/contao-h4a_tabellen/issues/182)) ([b85d168](https://github.com/janborg/contao-h4a_tabellen/commit/b85d168a7d79fa2038fbdda29f51a97ecbf76585))

## [5.0.2](https://github.com/janborg/contao-h4a_tabellen/compare/v5.0.1...v5.0.2) (2026-08-14)


### Bug Fixes

* Spaltenname im UpdateH4aEventsCron korrigieren ([#180](https://github.com/janborg/contao-h4a_tabellen/issues/180)) ([441f5a0](https://github.com/janborg/contao-h4a_tabellen/commit/441f5a0b5250a16df9403f4b825c4c660df60667))

## [5.0.1](https://github.com/janborg/contao-h4a_tabellen/compare/v5.0.0...v5.0.1) (2026-08-08)


### Bug Fixes

* readme.md überarbeiten ([c2c19cf](https://github.com/janborg/contao-h4a_tabellen/commit/c2c19cfe1b9482647414ea9d3e4ec5f1bc926f13)), closes [#177](https://github.com/janborg/contao-h4a_tabellen/issues/177)

## [5.0.0](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.22...v5.0.0) (2026-08-06)


### ⚠ BREAKING CHANGES

* refactor EventAutomator for handballnet ([#172](https://github.com/janborg/contao-h4a_tabellen/issues/172))
* remove deprecated functions in H4aEventAutomator ([#158](https://github.com/janborg/contao-h4a_tabellen/issues/158))
* remove CE from tl_content
* remove service H4aApiHelper
* remove obsolete Migrations
* remove H4a ContentElementController

### Features

* add backend administration for handballnet clubs, teams and seasons ([#147](https://github.com/janborg/contao-h4a_tabellen/issues/147)) ([2871bf4](https://github.com/janborg/contao-h4a_tabellen/commit/2871bf46a824317bddc848fa9629a4a3bd96c913))
* add handball.net widgets as content_element ([#164](https://github.com/janborg/contao-h4a_tabellen/issues/164)) ([8a02c20](https://github.com/janborg/contao-h4a_tabellen/commit/8a02c20a3fb6d13b21e997959744cefe1d025203))
* add Migrations from v4 to v5 ([#171](https://github.com/janborg/contao-h4a_tabellen/issues/171)) ([980c53f](https://github.com/janborg/contao-h4a_tabellen/commit/980c53fad284811ebe46886c11ba6c7b5bd79632))
* new calendar update logic ([#169](https://github.com/janborg/contao-h4a_tabellen/issues/169)) ([36d96d6](https://github.com/janborg/contao-h4a_tabellen/commit/36d96d61d8e2087cb60a2abce3be7eba0d1fb2b1))


### Bug Fixes

* ContentElements for Table and Games ([#156](https://github.com/janborg/contao-h4a_tabellen/issues/156)) ([5d5ec8a](https://github.com/janborg/contao-h4a_tabellen/commit/5d5ec8af1bed59d471fdb60f132ef8d1fa1e208d))
* delete duplicate handballnetwidget Template ([cb52c1e](https://github.com/janborg/contao-h4a_tabellen/commit/cb52c1e441c60d9559d079e032974e514bd36c90))
* nuliga needs longer liga_shortnames ([2c96de7](https://github.com/janborg/contao-h4a_tabellen/commit/2c96de76e07b1f2a51622378f7349902eeb64d20))
* remove redundant [data] ([af60b6e](https://github.com/janborg/contao-h4a_tabellen/commit/af60b6e5877dfdacdbc917d545ecdd820a03460d))
* remove unused dependencies ([76933eb](https://github.com/janborg/contao-h4a_tabellen/commit/76933eb49590fcb977da4d094a8e43e76fd6ad0c))
* replace h4a with handballnet in translations ([b219d46](https://github.com/janborg/contao-h4a_tabellen/commit/b219d46832b43ff5a4347cc7c935754e05181e4f))
* show wildcard info depending on widget type ([7fb2a67](https://github.com/janborg/contao-h4a_tabellen/commit/7fb2a675ff10ba2aaea1aa9aeab0aa9916da0193))


### Miscellaneous Chores

* refactor EventAutomator for handballnet ([#172](https://github.com/janborg/contao-h4a_tabellen/issues/172)) ([5d33c38](https://github.com/janborg/contao-h4a_tabellen/commit/5d33c3865a8b62ae3aa00a3ae656398f9e01dba7))


### Code Refactoring

* remove CE from tl_content ([28edd29](https://github.com/janborg/contao-h4a_tabellen/commit/28edd294b022b615b874956f0c3d2530ce954412))
* remove deprecated functions in H4aEventAutomator ([#158](https://github.com/janborg/contao-h4a_tabellen/issues/158)) ([a1079e8](https://github.com/janborg/contao-h4a_tabellen/commit/a1079e8ef70f45d522ec86ed5cb3ca51ea380c75))
* remove H4a ContentElementController ([ac35ae2](https://github.com/janborg/contao-h4a_tabellen/commit/ac35ae2d2efbba0729aa47c94a23c49e861e63b1))
* remove obsolete Migrations ([660e778](https://github.com/janborg/contao-h4a_tabellen/commit/660e77818de3f5c92242ca52844f6c37a989fa73))
* remove service H4aApiHelper ([63544b0](https://github.com/janborg/contao-h4a_tabellen/commit/63544b03a7a007c71b8137e46787ebad8baa8b0f))

## [4.0.22](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.21...v4.0.22) (2026-05-16)


### Bug Fixes

* only try to update events with handballnet_id ([3c87b92](https://github.com/janborg/contao-h4a_tabellen/commit/3c87b9225e1926542bf94bca877a745e703e2d38))

## [4.0.21](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.20...v4.0.21) (2026-05-16)


### Bug Fixes

* liga_name must not be mandatory ([13803f9](https://github.com/janborg/contao-h4a_tabellen/commit/13803f9b736dc0502961c2542925c2a91a8d0887))

## [4.0.19](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.18...v4.0.19) (2026-03-31)


### Bug Fixes

* dont show empty halftime results ([f03e997](https://github.com/janborg/contao-h4a_tabellen/commit/f03e997bb62bb27b0d548c1d0ca1a16760b5fd88))

## [4.0.18](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.17...v4.0.18) (2026-03-31)


### Features

* add deprecation info ([#133](https://github.com/janborg/contao-h4a_tabellen/issues/133)) ([a4c59d1](https://github.com/janborg/contao-h4a_tabellen/commit/a4c59d1f033e88dc884ace81e7efbb4e1df2d556))
* add release-please-manifest ([6e5d398](https://github.com/janborg/contao-h4a_tabellen/commit/6e5d398f217c6e1ab3def19e1394880d99f04a82))
* use CacheTagManager instead of EntityCacheTags ([c50c172](https://github.com/janborg/contao-h4a_tabellen/commit/c50c172725f15e7951b0a23e7ffa32a092165042))
* Use DI AsCallback instead of ServiceAnnotation ([68c2b18](https://github.com/janborg/contao-h4a_tabellen/commit/68c2b18fbeae96b3fbd35b6882fb49307910da48))

## [4.0.17](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.16...v4.0.17) (2026-03-28)


### Bug Fixes

* Backend Routing for global Operations in UpdateHandballNetTeamsController ([248eb8f](https://github.com/janborg/contao-h4a_tabellen/commit/248eb8f75cca0101160ced6e5a9cb813bd29a7a4))

## [4.0.16](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.15...v4.0.16) (2026-03-11)


### Bug Fixes

* check liga_name and shortname correctly ([ea21833](https://github.com/janborg/contao-h4a_tabellen/commit/ea21833bb36f94383327f3a38710251805cefbec))

## [4.0.15](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.14...v4.0.15) (2026-03-10)


### Bug Fixes

* fill and update handballnet_id of events ([c4ca67f](https://github.com/janborg/contao-h4a_tabellen/commit/c4ca67fe7c262917566598482701287f05a6d445))
* get Liga name and acronym from phase in json ([f0ca9a7](https://github.com/janborg/contao-h4a_tabellen/commit/f0ca9a7dafba7aa38c209aaefa0f47dbcbc5a75b))
* wrong aray key for game state ([a856137](https://github.com/janborg/contao-h4a_tabellen/commit/a856137733ad6d879ff5b928b8f98c29e1f9f2f7))

## [4.0.14](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.13...v4.0.14) (2026-03-09)


### Bug Fixes

* use 'state' to check, if game has ended ([#124](https://github.com/janborg/contao-h4a_tabellen/issues/124)) ([18de069](https://github.com/janborg/contao-h4a_tabellen/commit/18de069d09c663d71b5841242b85833200b14832))

## [4.0.13](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.12...v4.0.13) (2026-03-02)


### Bug Fixes

* Backend Routing for global Operations in 5.7 ([#122](https://github.com/janborg/contao-h4a_tabellen/issues/122)) ([14a772d](https://github.com/janborg/contao-h4a_tabellen/commit/14a772d67512f5a5e0bacabe2ccf4e697d07f000))

## [4.0.12](https://github.com/janborg/contao-h4a_tabellen/compare/v4.0.11...v4.0.12) (2026-02-08)


### Bug Fixes

* use ArrayInsert Global Operations for calendar_events ([2cd7afd](https://github.com/janborg/contao-h4a_tabellen/commit/2cd7afd7ab777d1d6e7c1e77562deebb30a9d189))
* use ArrayInsert Global Operations for tl_calendar ([1ffa1b9](https://github.com/janborg/contao-h4a_tabellen/commit/1ffa1b981a515a04a7ee2561aa8ae74a5199494e))

## [4.0.11](https://github.com/janborg/contao-h4a_tabellen/compare/4.0.10...v4.0.11) (2025-12-18)


### Miscellaneous Chores

* add assignee in release-please ([#117](https://github.com/janborg/contao-h4a_tabellen/issues/117)) ([2b7c5f7](https://github.com/janborg/contao-h4a_tabellen/commit/2b7c5f76f659306fe400c4b49e3a300ee5c874f8))
