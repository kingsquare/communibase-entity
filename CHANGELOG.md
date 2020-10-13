# Change Log

## 5.1.0
- add CommunibaseIdCollection::fromValidStrings method

## 5.0.0
- **[BC break]** strict typing
- **[BC break]** add CommunibaseIdCollection
- add clone methods to Address, Email and PhoneNumber

## 4.0.0
- **[BC break]** make exceptions extend \Exception instead of \RuntimeException

## 2.3.1
- add some named static constructors

## 2.3.0
- added backwards compatible handling of geo point native stuff

## 2.2.0
- add dutch mobile with country code
- make entities extendable (composition is not working here)

## 2.1.0
- add streetNumberAddition

## 2.0.0
- **[BC break]** Geolocation MUST use/return valid float values
- **[BC break]** CommunibaseId array MUST contain only CommunibaseId objects

## 1.1.0
- added some badges
- stricter type checking
- Re-index array (JSON requires consecutive keys)
- The (private) constructors do not allow null parameters anymore

## 1.0.1
- detect/remove areacode parenthesis if areacode is empty
- handle '0' areacode as if empty
- added spaces to default phoneNumber format
- changed changelog layout

## 1.0.0

- initial version
