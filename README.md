# DbpRelayGreenlightConnectorCampusonlineBundle

[GitHub](https://github.com/digital-blueprint/relay-greenlight-connector-campusonline-bundle) |
[Packagist](https://packagist.org/packages/dbp/relay-greenlight-connector-campusonline-bundle)

[![Test](https://github.com/digital-blueprint/relay-greenlight-connector-campusonline-bundle/actions/workflows/test.yml/badge.svg)](https://github.com/digital-blueprint/relay-greenlight-connector-campusonline-bundle/actions/workflows/test.yml)

**Note:** This project depends on the DCC infrastructure of the Austrian
Government. Since the DCC infrastructure is [no longer available since June
2023](https://github.com/Federal-Ministry-of-Health-AT/green-pass-overview/issues/11#issuecomment-1617997232),
this project is no longer actively maintained.

This Symfony bundle fetches images for [DbpRelayGreenlightBundle](https://packagist.org/packages/dbp/relay-greenlight-bundle)
from CampusOnline, while retrieving the `co-obfuscated-c-ident` from LDAP.

See the [documentation](./docs/README.md) for more information.

## Bundle installation

```bash
composer require dbp/relay-greenlight-connector-campusonline-bundle
```

## Development & Testing

* Install dependencies: `composer install`
* Run tests: `composer test`
* Run linters: `composer run lint`
* Run cs-fixer: `composer run cs-fix`
