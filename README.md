# Content Security Policy Optimized Allowlist
Greatly reduce or disable the allowlists of default CSP allowed domains that come via (default) extensions in `csp_whitelist.xml`

## Introduction
These allowed domains are added both in the frontend and backend of Magento and don't have any validation if they are used.
This brings certain security risks as it can be used to trigger some interesting XSS attacks.

Magento installations can have many third party extensions installed. These extensions all bring there own `csp_whitelist.xml`
file which are loaded in the frontend. This also greatly increases the size of the CSP header.

Basically meaning allowing many domains which will never be used will be included by default.

## Installation
Installation is plain and simple, in your Magento project.

The optimization will be activated by default, but can be enabled and disable per storeview.

```shell
composer require hyva-themes/magento2-optimized-csp-allowlist
bin/magento setup:upgrade
```

## Configuration
This module has two configuration options.

- Navigate to **Stores** -> **Configuration**
- Then **Security** -> **Content Security Policy (CSP)**
  - **Fully disable module allowlists** to fully disable modules `csp_whitelist.xml`'s (default: **No**)
  - **Enable allowlist optimization** to enable or disable the setting per store (default: **Yes**)

![Configuration image](docs/configuration.png)

## Research into XSS Risks when allowing many domains
On a vanilla Magento 2.4.8 installation without this optimization 25 of the allowed domains can be used to inject malicious script into
a store via callbacks. Enabling this module reduces this number to 0.

### Default 


## Author
- [Jeroen Boersma](https://www.github.com/JeroenBoersma)