# Communibase Entities

[![Build Status](https://travis-ci.org/kingsquare/communibase-entity.svg?branch=master)](https://travis-ci.org/kingsquare/communibase-entity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kingsquare/communibase-entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kingsquare/communibase-entity/?branch=master)

Some basic reusable CB Entities:

- CommunibaseId
- CommunibaseIdCollection
- Address
- Email
- PhoneNumber

PhoneNumber uses a setter which parses the the given string to countryCode, areaCode en subscriberNumber internally.
