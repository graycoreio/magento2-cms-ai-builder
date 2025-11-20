# Graycore AI CMS Builder

A Magento 2 module that extends the CMS page editor with AI-powered content generation and visual preview capabilities.

## Overview

This module adds an AI-powered visual editor to Magento 2's CMS pages, allowing content managers to:

- Generate page schemas from text prompts using OpenAI
- Preview content in real-time using an Angular-based renderer
- Store and serve dynamic content via GraphQL

## Features

- **AI Schema Generation**: Convert text prompts into structured component schemas using OpenAI GPT-4
- **Visual Editor**: Split-pane editor with prompt input and live preview
- **Component Registry**: Configurable component system for defining available UI components
- **GraphQL Integration**: Schemas automatically available via GraphQL for frontend consumption
- **Custom Element Support**: Preview uses Angular web components (custom elements)

## Installation

1. Download the package:
```bash
composer require graycore/magento2-cms-ai-builder
```

2. Configure the package:

3. Enable the module:
```bash
bin/magento module:enable Graycore_CmsAiBuilder
```

## Customizing the Editor

You can replace the default Angular editor with your own implementation by configuring custom script paths in your module's `etc/config.xml`:

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Store:etc/config.xsd">
    <default>
        <ai_cms_builder>
            <editor_customization>
                <custom_polyfills_script>Vendor_Module/js/custom-editor/polyfills.js</custom_polyfills_script>
                <custom_main_script>Vendor_Module/js/custom-editor/main.js</custom_main_script>
            </editor_customization>
        </ai_cms_builder>
    </default>
</config>
```

These scripts should be built as an Angular app following the documentation from the `@daffodil/content` admin editor guide.

## License

See [LICENSE](./LICENSE)