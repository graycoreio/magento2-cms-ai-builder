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

## License

See [LICENSE](./LICENSE)