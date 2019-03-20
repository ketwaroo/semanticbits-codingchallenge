Current Modified Pages - Coding Challenge
=========================================

This is a coding excercise in creating a simple Drupal 8 module.

## Installation

The code currently here is for demo purposes only. But it is intended to be
installable as a composer package into a Drupal installation.

## Usage

When installed and enabled, this module will add a `Block` definition with
machine name `modifiedpageoftheday_currentmodfiedblock`.

The block, when added to a section will render a list of pages that were modified
on the current day. By current day, it is understood to be from 00:00:00 of the
morning of that day.

The block has a single configurable option to set the limit of how many pages to
display.


## Automated Testing

In a Drupal instance with dev packages installed and phpunit set up, the unit
tests can be run as follows:

```
> cd <web directory>

> phpunit -c core/phpunit.xml.dist modules/custom/modifiedpageoftheday/tests/

```
the `phpunit.xml.dist` can be copied to `phpunit.xml` and modified as necessary.
