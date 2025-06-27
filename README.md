# Content Security Policy Optimized Allowlist
Greatly reduce the list of default CSP allowed domains that come via (default) extensions via `csp_whitelist.xml`

These allowed domains are added both added on the frontend and backend and do not have any validation if they are even used.
This brings certain security risks as it can be used to trigger some XSS attacks.

Magento installations can have many third party extensions installed. These extensions all bring there own `csp_whitelist.xml`
file which are loaded in the frontend. This also greatly increases the size of the CSP header.

Basically meaning allowing many domains which are never used in the frontend to be included by default.

## Installation
Installation is plain and simple, in your Magento project.

The optimization will be activated by default, but can be enabled and disable per storeview.

```shell
composer require hyva-themes/magento2-optimized-csp-allowlist
bin/magento setup:upgrade
```

## Configuration
This module has one configuration option.

- Navigate to **Stores** -> **Configuration**
- Then **Security** -> **Content Security Policy (CSP)**
- Under **allowlist optimization** enable or disable the setting per store

## Research into XSS Risks when allowing many domains
On a vanilla Magento 2.4.8 installation without this optimization 25 of the allowed domains can be used to inject malicious script into
a store via callbacks. Enabling this module reduces this number to 0.

## TODO
- [ ] Fully disable module `csp_whitelist.xml` for modules (backend setting)
- [ ] Add a Viewmodel to add localized domains dynamically 

## Author
- [Jeroen Boersma](https://www.github.com/JeroenBoersma)