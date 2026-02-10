# Content Security Policy Optimized Allowlist
This extension optimizes the Content Security Policy (CSP) for Magento by allowing selective loading of `csp_whitelist.xml` files, reducing security risks associated with unnecessary domain allowances and potential XSS vulnerabilities.

## Description

The CSP header `Content-Security-Policy` contains a list of allowed domains which are to both your frontend and backend of your Magento installation. No validation is done if these domains are actually needed to run a page. This brings certain security risks as it can be used to trigger some clever XSS attacks (more below) on your store.

Even if CSP is fully enforced, `eval` and unsigned `inline` scripts are disabled, it still will be possible to inject non validated code to run on your store.

Your Magento installation will include many third party extensions and Magento also ships some `csp_whitelist.xml` files per default, all files include many domains.
These domains will automatically be merged into the CSP header. Besides for the additional risks this greatly increases the size of the CSP header.

![Configuration image](docs/configuration.png)

## Installation

Installation in your Magento project is done via composer.

```shell
composer require hyva-themes/magento2-optimized-csp-allowlist
bin/magento setup:upgrade
```

## Post installation

This extension can run in two modes:

1. **Fully disable all modules `csp_whitelist.xml`** - The safest option, where you can still include a `csp_whitelist.xml` file directly in your theme
2. **Check if a template file from a specific extension is used** - Only if a `.phtml` file from an installed extension is used, it will include in the CSP header

The first option is the safest, you can still include a `csp_whitelist.xml` file directly in your theme. After installation the second option is enabled per default.

Checking if an `.phtml` is used and only loading those allowlist XML files is enabled by default.

![Configuration image](docs/configuration.png)

## Configuration

You can configure how the extension should work per store:

- Navigate to **Stores** -> **Configuration**
- Then **Security** -> **Content Security Policy (CSP)**
  - **Fully disable module allowlists** to fully disable modules `csp_whitelist.xml`'s (default: **No**)
  - **Enable allowlist optimization** to enable or disable the setting per store (default: **Yes**)

## Research: XSS Risks when allowing domains

On a vanilla Magento (tested 2.4.8-p1) installation without this feature, 25 of the 46 items on the allowed domains list can be used to inject XSS.

An example as `*.google.com` is whitelisted in the `magento/module-payment-services-paypal/etc/csp_whitelist.xml` which allows at least the following possible injection.

```html
<script src="https://accounts.google.com/o/oauth2/revoke?callback=alert(1337)"></script>
```

This input is reflected on the endpoint and therefore it will trigger `alert(1337)`, this can become anything.

More information about this, take a look at [renniepak/CSPBypass](https://github.com/renniepak/CSPBypass) and test your own domains.

## Technique

The extension checks whether a `.phtml` file is loaded within an extension. If this is true, the extension will be allowed to load its whitelist.

## Adding domains and hashes without use of XML

You can use ViewModel  `\Hyva\OptimizedCspAllowlist\ViewModel\Hosts::add(string $policyId, ['host.ext', ...])` to add domains without the use of `csp_whitelist.xml`.

This gives more flexibility as you can be very specific on which domains or hashes you want to allow in the frontend. This also brings the possibility to have dynamic domains on multilingual stores or using a specific CDN on a specific store.

For instance, the Google Analytics `.phtml` file can include registration for the domain in the same file instead of XML, making sure that only that script can be loaded on that page.

### Dynamic domains

This will also bring the possibility to add custom domains when using multilingual domains, no more `*.host.ext` but allow `lang.host.ext` instead.

### Viewmodels for any theme

Viewmodels can be injected in `layout.xml` for any theme.

```xml
<!-- ... snap ... -->
<block ...>
  <arguments>
    <argument name="csp_view_model" xsi:type="object">\Hyva\OptimizedCspAllowlist\ViewModel\Hosts</argument>
  </arguments>
</block>
<!-- ... /snap ... -->
```

In your `.phtml` file you:

```php
<?php
// .. snap
$cspViewModel = $block->getCspViewModel();
$cspViewModel->add('script-src', 'https://lang.host.ext')
// .. /snap
```

### Viewmodel registry when using Hyvä Themes

When using Hyvä Themes, `$viewmodel->require(...)` can be used.

```php
<?php
// .. snap
$cspViewModel = $viewModels->require(\Hyva\OptimizedCspAllowlist\ViewModel\Hosts::class);
$cspViewModel->add('script-src', 'https://lang.host.ext')
// .. /snap
```

## Notes

During research I ran into some miss configurations in default Magento extensions, for instance. `google-analytics.com` is whitelisted in the adwords extension.
Enabling google analytics in the backend will not add the header for GA because the domain is registered in the google adwords extension. 🤷

### License

This package is licensed under the **Open Software License (OSL 3.0)**.

* **Copyright:** Copyright © 2020-present Hyvä Themes. All rights reserved.
* **License Text (OSL 3.0):** The full text of the OSL 3.0 license can be found in the `LICENSE.txt` file within this package, and is also available online at [http://opensource.org/licenses/osl-3.0.php](http://opensource.org/licenses/osl-3.0.php).

## Author

- [Jeroen Boersma](https://www.github.com/JeroenBoersma)

